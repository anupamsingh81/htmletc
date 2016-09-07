<!-- Reference: http://www.webproworld.com/viewtopic.php?t=60245 -->
<!-- Reference: http://www.phpied.com/javascript-include/ -->
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


function myTruncate($string, $limit, $pad="...") {
	# return with no change if string is shorter than $limit
	if(strlen($string) <= $limit) return $string;
	# truncate string and pad
	$string = substr($string, 0, $limit) . $pad; 
	return $string;
}

$display_date = date("D, d M Y h:i:s T", time());

if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
	$rss_title = "phpAddEdit";
	$rss_link = "http://www.phpaddedit.com";
	$rss_description = "phpAddEdit Demo Additions RSS Feed";
} else {
	if (!$_GET["rss_item_link"]) eval (" \$rss_item_link = \"$rss_item_link\"; ");
	//echo $rss_item_link . "<br>";
}

$rss = "<" . "?" . "xml version=\"1.0\" encoding=\"UTF-8\"" . "?" . ">";
//$rss .= "<rss version=\"2.0\">\n";
$rss .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
$rss .= "<channel>\n";
$rss .= "<title>" . $rss_title . "</title>\n";
$rss .= "<link>" . $rss_link . "</link>\n";
$rss .= "<description>" . $rss_description . "</description>\n";

$rss .= "<item>\n";
$rss .= "   <title>" . $_POST[$rss_title_field] . "</title>\n";
$rss .= "   <link>" . $rss_item_link . "</link>\n";
$rss .= "   <guid>" . $rss_item_link . "</guid>\n";
$rss .= "   <description><![CDATA[ \n";

//echo $rss_description_field . "<br>" . $_POST[$rss_description_field];
//echo "rss_description_chars is " . $rss_description_chars . "<br>";
$rss_description = $_POST[$rss_description_field];
if ($rss_description_chars) $rss_description = myTruncate($rss_description, $rss_description_chars);
if ($rss_description_chars=="0") $rss_description = "...";
$rss .= nl2br(stripslashes($rss_description));

$rss .= "   ]]></description>\n";
$rss .= "   <pubDate>" . $display_date . "</pubDate>\n";
//$rss .= "   <lastBuildDate>" . $display_date . "</lastBuildDate>\n";
$rss .= "</item>\n";
$rss .= "</channel>\n";
$rss .= "</rss>\n";

// --- replace & with &amp;
//$rss = str_replace("&","&amp;",$rss);

// --------------------------------------------------------------------------------------
// Now write the RSS to a static file to speed up email creation/delivery...
// But, only write if no file already exists
// --------------------------------------------------------------------------------------
if (!$file_handle = fopen($rss_file,"w")) { 
  printMessage("Cannot open file '$rss_file' - please set the permission - chmod 777","red");
  exit;
}   
if (!fwrite($file_handle, $rss)) { 
  printMessage("Cannot write to file '$rss_file' - please set the permission - chmod 777","red");
  exit;
}
//echo "You have successfully written data to $file";    
fclose($file_handle);   

// --------------------------------------------------------------------------------------
// --- Finally, write to a second file - this is for FeedBurner...
// --------------------------------------------------------------------------------------
$ext = substr($rss_file,strrpos($rss_file,"."));
$fd_file = substr($rss_file,0,strrpos($rss_file,".")) . "-feedburner" . $ext;
//echo "feedburner file is " . $fd_file;
if (!$file_handle = fopen($fd_file,"w")) { 
  //echo "Cannot open file"; 
}   
if (!fwrite($file_handle, $rss)) { 
  //echo "Cannot write to file"; 
}
//echo "You have successfully written data to $file";    
fclose($file_handle); 


// -----------------------------------------------------------------------------------------
// Now ping relevant blog directories...
// For info about the locations to ping and which require first-time manual submission
// visit http://www.masternewmedia.org/news/2004/11/10/increase_visibility_in_blog_and.htm
// -----------------------------------------------------------------------------------------
function weblog_ping($server = '', $path = '') {
	global $rss_title, $rss_link;
	$blogname = $rss_title;
	$blogurl = $rss_link;
	$wp_version = "0.9b";
	include_once ('includes/class-IXR.php');

	// using a timeout of 3 seconds should be enough to cover slow servers
	$client = new IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- phpAddEdit/'.$wp_version;

	// when set to true, this outputs debug messages by itself
	$client->debug = false;
	$client->query('weblogUpdates.ping', $blogname, $blogurl);
}


echo "<div style=\"margin:5px;\">\n";
if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
	if ($rss_display=="Yes") printMessage("<br />"."RSS Entry Has Been Created.  If this wasn't a demo a ping would have been sent to Technorati and Ping-o-matic as well...<br />","");
} else {
	if (!$rss_ping || $rss_ping=="Yes") {
		weblog_ping("rpc.pingomatic.com","/rpc");
		weblog_ping("rpc.technorati.com","/rpc/ping");
		weblog_ping("api.my.yahoo.com","/rss/ping");
		weblog_ping("blogsearch.google.com","/ping/RPC2");
		/*
		weblog_ping("rpc.weblogs.com","/RPC2");
		weblog_ping("ping.blo.gs","/");
		weblog_ping("xping.pubsub.com","/ping");
		weblog_ping("www.blogdigger.com","/RPC2");
		weblog_ping("api.feedster.com","/ping.php");
		weblog_ping("rpc.blogrolling.com","/pinger");
		weblog_ping("www.blogstreet.com","/xrbin/xmlrpc.cgi");
		weblog_ping("api.moreover.com","/ping");
		weblog_ping("ping.weblogalot.com","/rpc.php");
		weblog_ping("rpc.icerocket.com:10080","/");
		weblog_ping("topicexchange.com","/RPC2");
		weblog_ping("www.newsisfree.com","/xmlrpctest.php3");
		weblog_ping("rpc.blogbuzzmachine.com","/RPC2");
		weblog_ping("ping.syndic8.com","/xmlrpc.php");
		weblog_ping("ping.rootblog.com","/rpc.php");
		weblog_ping("www.blogsnow.com","/ping");
		*/
		//weblog_ping("","/");
		if ($rss_display=="Yes") printMessage("RSS Entry Has Been Created and a Ping Sent to Technorati and Ping-o-matic","");
	} else {
		if ($rss_display=="Yes") printMessage("RSS Entry Has Been Created - No Ping Was Sent","");
	}
}
echo "</div>\n";

?>