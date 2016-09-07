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

// --------------------------------------------------------
// --- generate trackback if appropriate...
// --------------------------------------------------------
function trackback() {
	global $trackback_message, $id, $ID, $trackback_edit, $trackback_author, $trackback_title_field, $trackback_excerpt, $trackback_url, $trackback_encoding, $trackback_field1, $trackback_field2, $trackback_field3;

	$host = $_SERVER['HTTP_HOST'];
	if (substr($host,0,4)=="www.") $host = substr($host,4);

	$trackback_title = $_POST[$trackback_title_field];
	// --- Instantiate the class
	include("includes/trackback.class.php");
	$trackback = new Trackback($trackback_title, $trackback_author, $trackback_encoding);
	global $response;
	$text = $_POST[$trackback_field1] . " " . $_POST[$trackback_field2] . " " . $_POST[$trackback_field3];
	$text = stripslashes($text);
	//echo "text: ".$text."<br><br>";
	if ($tb_array = $trackback->auto_discovery($text)) {
		/* echo "tb_array is "; print_r($tb_array); echo "<br>"; */
	    foreach($tb_array as $tb_key => $tb_url) {
	    	// --- Attempt to ping each url...
			if (!$ID) $ID = mysql_insert_id();
			if (!$_GET["trackback_url"]) eval (" \$trackback_url = \"$trackback_url\"; ");
			// echo $trackback_url."<br>";
			$pingit = false;
			// --- try to send a trackback ping...UNLESS the URL is to our own domain, no point then...
			if (!stristr($tb_url,$host)) $pingit = $trackback->ping($tb_url, $trackback_url, stripslashes($_POST[$trackback_title_field]), stripslashes($_POST[$trackback_excerpt]), $trackback_author);
	        if ($pingit) {
				// --- Successful ping...
				if ($trackback_message) $trackback_message .= "<br />";
				$trackback_message .= "<span style=\"font-weight:normal;\">Trackback to <em>$tb_url</em> ...</span>Succeeded";
	        } else {
				// --- Error pinging...
				preg_match_all('/(<message.*?<\/message>)/sm', $response, $err_message, PREG_SET_ORDER);
				if ($trackback_message) $trackback_message .= "<br />";
				$trackback_message .= "<span style=\"font-weight:normal;\">Trackback to <em>$tb_url</em> ...<strong>failed</strong>\n";
				if ($err_message[0][1]) $trackback_message .= "<br /><span style=\"color:red; font-weight:bold\"> &nbsp; Error Message:</span> ".$err_message[0][1]."</span>\n";
	        }
		}
	} else {
	    // --- No trackbacks in TEXT...
	    $trackback_message .= "No trackbacks were auto-discovered...\n";
	}
}

if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
	printMessage("<br />"."If this wasn't a demo a trackback ping would have been sent...<br />","");
} else {
	trackback();
	if ($trackback_display=="Yes") {
		printMessage($trackback_message,"");
	}
}
?>