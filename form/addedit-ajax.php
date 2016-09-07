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

// -------------------------------------------------------------------------------------------
// --- include required files 
// -------------------------------------------------------------------------------------------
include_once ("config.php");
include_once ("includes/dbconnect.inc.php");
include_once ("addedit-functions.php");

// -------------------------------------------------------------------------------------------
// --- Get the passed GET variables
// -------------------------------------------------------------------------------------------
$apikey = $_GET["apikey"];
$function = $_GET["function"];
$table = $_GET["table"];
$field = $_GET["field"];
$othervalue = $_GET["othervalue"];
$encoding = $_GET["encoding"];
$addother_select_sql = stripslashes(urldecode($_GET[addother_select_sql]));
eval("\$addother_select_sql = \"$addother_select_sql\";");
$addother_insert_sql = stripslashes(urldecode($_GET[addother_insert_sql]));
eval("\$addother_insert_sql = \"$addother_insert_sql\";");

// -------------------------------------------------------------------------------------------
// --- if someone bothered to set an encoding scheme, let's tell the DB to use it...
// --- right now only supporting default and UTF8 - need to investigate other common choices
// -------------------------------------------------------------------------------------------
if ($encoding=="UTF-8" || $encoding=="UTF8") $db->query("SET NAMES 'UTF8'");

// -------------------------------------------------------------------------------------------
// --- let's do some basic SQL injection protection by limiting use of special chars...
// -------------------------------------------------------------------------------------------
$othervalue = str_replace('&quot;', '"', $othervalue);
$othervalue = preg_replace('/, +/', ' ', $othervalue);
$othervalue = str_replace(',', ' ', $othervalue);
$othervalue = str_replace(";", "\;", $othervalue);
$othervalue = str_replace("+", "\+", $othervalue);
$othervalue = str_replace("$", "\$", $othervalue);
$othervalue = str_replace("?", "\?", $othervalue);
$othervalue = str_replace("^", "\^", $othervalue);
$othervalue = str_replace("*", " ", $othervalue);
$othervalue = str_replace("=", " ", $othervalue);

switch ($function) {
  case "addother":
  	global $result;
	// --- only add if doesn't already exist, so check first...
	$sql = "SELECT * FROM $table WHERE $field = '$othervalue'";
	if ($addother_select_sql) $sql = $addother_select_sql;
	//echo $sql;
	$get = $db->get_row($sql);
	//print_r($db); echo "<br />";
	if (!$db->col_info) {
		$result = ' <span style="color:red;">SQL ERROR</span> - ';
		$result .= $sql;
	} else {
		if ($get->author_id) {
			$result = $othervalue . ' <span style="color:red;"> already exists</span>';
		} else {
			$sql = "INSERT INTO $table ($field) VALUES ('$othervalue')";
			if ($addother_insert_sql) $sql = $addother_insert_sql;
			// ------------------------------------------------------------------------------------------------------------------------------------------------
			// --- the following lines are customization for the wordpress sample form since adding a category needs to add two 
			// --- fields, not just one. If your DB has a similar need follow the example here...
			if ($table=="wp_terms") {
				$slug = slug($othervalue);
				$sql = "INSERT INTO $table ($field,slug) VALUES ('$othervalue','$slug')";			
			}
			// ------------------------------------------------------------------------------------------------------------------------------------------------
			$insert = $db->query($sql);
			$insert_id = mysql_insert_id();

			// ------------------------------------------------------------------------------------------------------------------------------------------------
			// --- the following lines are customization for the wordpress sample form since a category needs to be added to 
			// --- the table wp_term_taxonomy as well as the table wp_terms
			if ($table=="wp_terms") {
				$slug = slug($othervalue);
				$sql = "INSERT INTO wp_term_taxonomy (term_id) VALUES ('$insert_id')";			
				$insert = $db->query($sql);
			}
			// ------------------------------------------------------------------------------------------------------------------------------------------------

			$result = "Added " . stripslashes($othervalue);
			//$result = implode($_GET);
		}
		$result = $insert_id . ' <span style="color:green;">'.$result.'</span>';
	}
	
	echo $result;
	break;
  case "akismetapicheck":
	global $akismet_api_key;
	$akismet_api_key = $apikey;
	include("addedit-akismet.php");
	$result = akismet_verify_key($apikey);
	if ($result=="valid") {
		$result = ' <span style="color:green;">VALID</span>';
	} else {
		$result = ' <span style="color:red;">INVALID</span>';
	}
	echo $result;
	break;
}
?>