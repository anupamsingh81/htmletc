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

// ====================================================================================================
// Edit this file to add any error checking that isn't covered by built-in options in the 
// AddEdit form generation script. Two concepts may help you: 
//   1. do your checking using the $_POST global variable
//   2. make sure whatever failure message you want is assigned the $error variable
//   3. since the error will appear in a javascript alert box, keep in mind that a new line = \n 
//      (= \\n in PHP) and all apostrophes or quotation marks need to be escaped (\' or \").
//
// Below is an example to use as a guideline:
//
// if ($_POST["formname"]!="hello") $error .= "\\n oh no! \\n";
// if ($_POST["numtables"]==42) $error .= "\\n That's the right answer! \\n";
// ====================================================================================================

$phpfile = $_POST["formname"] . ".php";
$jsfile = $_POST["formname"] . ".js";
$cssfile = $_POST["formname"] . ".css";
if (is_readable($phpfile) || is_readable($jsfile) || is_readable($cssfile)) {
	$error .= "The form name you specified - " . $_POST["formname"] . " already exists or is a system file. Plesae select another\\n";
}

// ------------------------------------------------------------------------------------
// --- Here is an example where you could make sure nobody uploads a file that already 
// --- exists...just change the filename details to suit your form...
// ------------------------------------------------------------------------------------
//echo "file is " . $_FILES['member_profile_photo']['name'] . "<br>";
if (stristr(FORM_NAME,"profile") && $_FILES['member_profile_photo']['name']) {
	$filename = getenv('DOCUMENT_ROOT')."/images/profile/" . $_FILES['member_profile_photo']['name'];
	if (file_exists($filename)) $error .= "A photo with the filename \"" . $_FILES['member_profile_photo']['name']  . "\" already exists. \\n\\nPlease rename your file and try again or if you are editing your profile and don\'t actually need to update your photo just leave the photo field empty.\\n";
}
?>