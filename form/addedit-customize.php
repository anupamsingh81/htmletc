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

// ---------------------------------------------------------------------------------------------
// --- Use this file to do non-intuitve things with phpAddEdit. 
// --- For example, if you were doing a complicated multiple table form you might want to 
// --- map primary key fields, etc..
// --- 
// --- A useful way to use this file is if you want to skip or change the type of a field based 
// --- on some criteria. To do so, just set a skip variable; it should be of the form $tablefield."_skip". 
// --- For example:
// ---    $skip = "content_html_skip";
// ---    $$skip = "skip";				--- use this to completely skip inclusion of the field
// ---    $$skip = "textbox_noedit";	--- use this to create a non-editable text box instead of skipping
// ---    $$skip = "hidden";			--- use this to create a hidden form field instead of skipping
// --- 
// --- Another potentially useful thing you can do is to manipulate the information written to the 
// --- the database for file upload fields. Say for example that your upload directory for the form
// --- is /directory/files/ but the script that pulls from it is found in directory and uses a 
// --- relative path files/ then you can specify that the uploaded file information be changed using
// --- the following two variables: 
// ---    $files_find_string = "/directory/";
// ---    $files_replace_string = "";
// --------------------------------------------------------------------------------------------------

// --------------------------------------------------------------------------------------------------
// --- these two variables are used if you want to use the datefield form field
// --- the $yearminus is how many years before this year to show
// --- and the $yearplus is how many years after this year to show. 
// --------------------------------------------------------------------------------------------------
$yearminus = 50;
$yearplus = 10;


// --------------------------------------------------------------------------------------------------
// --- if uncommented, the variable below will cause all selectboxes to include 
// --- a blank option at the beginning of any list populated from an SQL query...
// --------------------------------------------------------------------------------------------------
//$selectboxblank = true;

// --------------------------------------------------------------------------------------------------
// --- you can ignore everything below...I use it for one of my sites but am too lazy to remove it 
// --- for each public release - JB

if (!$type && $_GET["type"]) $type = $_GET["type"];
if ($_POST["content_type"]) $type = $_POST["content_type"];

// ---    $skip = "content_html_skip";
// ---    $$skip = true;
$htmlskip = "content_html_skip";
$$htmlskip = "skip";

if ($page=="content" || $thispage=="content.php") {
	global $content_id, $source_id; 
	// --- first I need to associate the relationship between the passed main table primary 
	// --- key (table: content; primary key: ID) and secondary related tables' primary keys...
	if ($content_ID) {
		$content_id = $content_ID;
	} else {
		$content_id = $ID;
	}
}

// --------------------------------------------------------------------------------------------------
// --- un-comment the two variables below if you have a selectbox_other or 
// --- selectbox_multiple_other form field and you need to do a more complex sql 
// --- statement (for either selecting or for inserting) than is standard (one 
// --- variable). Note that you need to add a slash to any variables you will use 
// --- as the code will get evaled later on by PHP.
// --------------------------------------------------------------------------------------------------
//$addother_select_sql = "SELECT * FROM \$table WHERE \$field = '\$othervalue' AND country='El Salvador'";
//$addother_insert_sql = "INSERT INTO \$table (\$field,country) VALUES ('\$othervalue','$_POST[country]')";
?>
