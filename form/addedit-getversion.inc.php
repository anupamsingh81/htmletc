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

// =======================================================================================================
// === reference: http://www.php-mysql-tutorial.com/php-tutorial/php-read-remote-file.php
// =======================================================================================================
if (ini_get('allow_url_fopen') == '1') {
	// --- use fopen() or file_get_contents()
	if ($fp = fopen('includes/version.inc.php', 'r')) {
		$content = '';
		while ($line = fread($fp, 1024)) {
			$content .= $line;
		}
		// --- determine if we have a regular version (e.g., 1.3) or a minor version (e.g., 1.3.1)
		$numdots = substr_count($content,".");
		if ($numdots==2) {
			$thisversion = substr($content,strrpos($content,".")-1,3);
		} else {
			$thisversion = substr($content,strrpos($content,".")-3,5);
		}
		//echo "this version: " . $thisversion . "<br />";
	} else {
		$versionchkerror = "An error occured when trying to check for an updated version of phpAddEdit";
	}

	if ($fp = @fopen('http://www.phpaddedit.com/addedit/includes/version.inc.php', 'r')) {
		$content = '';
		while ($line = fread($fp, 1024)) {
			$content .= $line;
		}
		$numdots = substr_count($content,".");
		if ($numdots==2) {
			$latestversion = substr($content,strrpos($content,".")-1,3);
		} else {
			$latestversion = substr($content,strrpos($content,".")-3,5);
		}
		//echo "official version: " . $latestversion . "<br />";
	} else {
		$versionchkerror = "An error occured when trying to check for an updated version of phpAddEdit";
	}
} else {
	// --- use curl or your custom function
	// make sure curl is installed
	if (!function_exists('curl_init')) {
		// --- initialize a new curl resource
		$ch = curl_init();
	
		// --- set the url to fetch
		curl_setopt($ch, CURLOPT_URL, 'http://www.phpaddedit.com/addedit/includes/version.inc.php');
	
		// --- don't give me the headers just the content
		curl_setopt($ch, CURLOPT_HEADER, 0);
	
		// --- return the value instead of printing the response to browser
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		// --- use a user agent to mimic a browser
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
	
		$content = curl_exec($ch);
		$latestversion = substr($content,strrpos($content,".")-1,3);
	
		// --- remember to always close the session and free all resources
		curl_close($ch);
	} else {
		// --- curl library is not installed so we better use something else
		$versionchkerror = "Your server configuration will not allow this script to check for a newer version.";
	}
}
?>