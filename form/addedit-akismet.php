<?php
/*
 ****************************************************************************
 * phpAddEdit Form Generator - http://www.phpaddedit.com
 * Copyright © Jeff M. Blum
 *
 * Licensed under the terms of the following license:
 *  - GNU Lesser General Public License Version 3 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 * == BEGIN LICENSE ==
 *
 * This file is part of phpAddEdit.
 *
 * phpAddEdit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpAddEdit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with phpAddEdit.  If not, see <http://www.gnu.org/licenses/>.
 *
 * == END LICENSE ==
 *
 ****************************************************************************
*/

// ----------------------------------------------------------------------------------------------------
// --- verify valid API Key
// ----------------------------------------------------------------------------------------------------
function akismet_verify_key($key, $ip=null) {
	global $akismet_api_host, $akismet_api_port, $blog;

	$response = akismet_http_post("key=$key&blog=$blog", 'rest.akismet.com', '/1.1/verify-key', $akismet_api_port, $ip);
	if ( !is_array($response) || !isset($response[1]) || $response[1] != 'valid' && $response[1] != 'invalid' )
		return 'failed';
	return $response[1];
}

// ----------------------------------------------------------------------------------------------------
// --- Returns true if server connectivity was OK at the last check, false if there was a problem that needs to be fixed.
// ----------------------------------------------------------------------------------------------------
function akismet_server_connectivity_ok() {
	$servers = akismet_get_server_connectivity();
	return !( empty($servers) || !count($servers) || count( array_filter($servers) ) < count($servers) );
}

// ----------------------------------------------------------------------------------------------------
// --- Check connectivity between the WordPress blog and Akismet's servers.
// --- Returns an associative array of server IP addresses, where the key is the 
// --- IP address, and value is true (available) or false (unable to connect).
// ----------------------------------------------------------------------------------------------------
function akismet_check_server_connectivity() {
	global $akismet_api_host, $akismet_api_port, $akismet_api_key;
	
	$test_host = 'rest.akismet.com';
	
	// --- Some web hosts may disable one or both functions
	if ( !is_callable('fsockopen') || !is_callable('gethostbynamel') )
		return array();
	
	$ips = gethostbynamel($test_host);
	if ( !$ips || !is_array($ips) || !count($ips) )
		return array();
		
	$servers = array();
	foreach ( $ips as $ip ) {
		$response = akismet_verify_key($akismet_api_key, $ip);
		// --- even if the key is invalid, at least we know we have connectivity
		if ( $response == 'valid' || $response == 'invalid' )
			$servers[$ip] = true;
		else
			$servers[$ip] = false;
	}

	return $servers;
}

// ----------------------------------------------------------------------------------------------------
// --- Check the server connectivity and store the results in an option.
// --- Returns the same associative array as akismet_check_server_connectivity()
// ----------------------------------------------------------------------------------------------------
function akismet_get_server_connectivity( $cache_timeout = 86400 ) {
	$servers = akismet_check_server_connectivity();
	return $servers;
}

// ----------------------------------------------------------------------------------------------------
// --- get Akismet host information
// --- if all servers are accessible, just return the host name.
// --- if not, return an IP that was known to be accessible at the last check.
// ----------------------------------------------------------------------------------------------------
function akismet_get_host($host) {
	if ( akismet_server_connectivity_ok() ) {
		return $host;
	} else {
		$ips = akismet_get_server_connectivity();

		// --- a firewall may be blocking access to some Akismet IPs
		if ( count($ips) > 0 && count(array_filter($ips)) < count($ips) ) {
			// --- use DNS to get current IPs, but exclude any known to be unreachable
			$dns = (array)gethostbynamel( rtrim($host, '.') . '.' );
			$dns = array_filter($dns);
			foreach ( $dns as $ip ) {
				if ( array_key_exists( $ip, $ips ) && empty( $ips[$ip] ) )
					unset($dns[$ip]);
			}
			// --- return a random IP from those available
			if ( count($dns) )
				return $dns[ array_rand($dns) ];
			
		}
	}
	// --- if all else fails try the host name
	return $host;
}

// ----------------------------------------------------------------------------------------------------
// --- Returns array with headers in $response[0] and body in $response[1]
// ----------------------------------------------------------------------------------------------------
function akismet_http_post($request, $host, $path, $port = 80, $ip=null) {	
	global $latestversion, $charset;

	$akismet_version = constant('AKISMET_VERSION');

	$http_request  = "POST $path HTTP/1.0\r\n";
	$http_request .= "Host: $host\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . $charset . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= "User-Agent: phpAddEdit/$latestversion | Akismet/$akismet_version\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;
	//echo $http_request."<br />";
	
	$http_host = $host;
	// --- use a specific IP if provided - needed by akismet_check_server_connectivity()
	if ( $ip && long2ip(ip2long($ip)) ) {
		$http_host = $ip;
	} else {
		$http_host = akismet_get_host($host);
	}

	$response = '';
	if( false != ( $fs = @fsockopen($http_host, $port, $errno, $errstr, 10) ) ) {
		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}
	return $response;
}

// ----------------------------------------------------------------------------------------------------
// --- check the content for spam
// ----------------------------------------------------------------------------------------------------
function akismet_auto_check_comment( $content ) {
	global $akismet_api_host, $akismet_api_port, $blog, $charset;

	$comment = array();
	$comment['user_ip']    = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
	$comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$comment['referrer']   = $_SERVER['HTTP_REFERER'];
	$comment['blog']       = $blog;
	$comment['blog_charset'] = $charset;
	$comment['comment_content'] = $content;

	$ignore = array( 'HTTP_COOKIE' );

	foreach ( $_SERVER as $key => $value )
		if ( !in_array( $key, $ignore ) && is_string($value) )
			$comment["$key"] = $value;

	//print_r($comment); echo "<br /><br />\n";
	$query_string = '';
	foreach ( $comment as $key => $data )
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';

	$response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
	//print_r($response);
	return $response[1];
}

// ----------------------------------------------------------------------------------------------------
// --- submit spam to Akismet
// ----------------------------------------------------------------------------------------------------
function akismet_submit_spam_comment( $content ) {
	global $akismet_api_host, $akismet_api_port, $blog, $charset, $user_ip, $user_agent, $user_referrer, $url, $title, $email;

	$comment = array();
	$comment['user_ip']    = preg_replace( '/[^0-9., ]/', '', $user_ip );
	$comment['user_agent'] = $user_agent;
	$comment['referrer']   = $user_referrer;
	$comment['blog']       = $blog;
	$comment['blog_charset'] = $charset;
	$comment['comment_content'] = $content;
	$comment['comment_author'] = $title;
	$comment['comment_author_url'] = $url;
	$comment['comment_author_email'] = $email;
	//print_r($comment);
	//exit;

	$ignore = array( 'HTTP_COOKIE' );

	foreach ( $_SERVER as $key => $value )
		if ( !in_array( $key, $ignore ) && is_string($value) )
			$comment["$key"] = $value;

	$query_string = '';
	foreach ( $comment as $key => $data )
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';

	$response = akismet_http_post($query_string, $akismet_api_host, "/1.1/submit-spam", $akismet_api_port);
	return $response[1];
}

// ----------------------------------------------------------------------------------------------------
// --- NOTE: include this file, specify a global variable ($akistmet_api_key) and 
// ---		set it to appropriate value then call whichever function you want 
// ----------------------------------------------------------------------------------------------------

// ----------------------------------------------------------------------------------------------------
// --- Define Global Variables
// ----------------------------------------------------------------------------------------------------
define('AKISMET_VERSION', '1.0');

// ----------------------------------------------------------------------------------------------------
// --- Useful variable definitions needed by Akismet
// ----------------------------------------------------------------------------------------------------
include ("addedit-getversion.php");
$akismet_api_host = $akismet_api_key . '.rest.akismet.com';
$akismet_api_port = 80;
$charset = $encoding;
if (!$encoding) $charset = "UTF-8";
$website = $_SERVER['HTTP_HOST']; 
if (substr($website,0,4)=="www.") $website = substr($website,4);
$blog = $website;
if (substr($blog,0,4)!="http") $blog = "http://" . $blog;
//echo "blog: " . $blog . "<br />";
//echo "key: " . $akismet_key . "<br />";
?>