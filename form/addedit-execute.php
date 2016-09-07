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

// --------------------------------------------------------------------------------------
// ----- perform error check
// ----- note - we must check for presence of submitval (a hidden variable set when submit button is generated)
// ----- b/c for some reason submit button doesn't register as a posted variable when we use our js function 
// ----- to disable it on submission...
// --------------------------------------------------------------------------------------
$error_message = array();
if ($_POST["submitval"] && $error_check_fields) {
	$error = error_check($error_check_fields,$error_check_english);
	//print_r($error_check_fields); echo "<br>";
	//echo "error_check is " . $error . "<br>";
}

// --------------------------------------------------------------------------------------
// ----- Loop through table(s) and field(s) and get field data, update, or add as approp.
// --------------------------------------------------------------------------------------
// echo "tables: "; print_r($tables); echo "<br />";
for ($i=1; $i<=count($tables); $i++) {
  $update_sql = "";
  $insert_sql_fields = "";
  $insert_sql_values = "";
  $tablename = trim($tables[$i-1]);

  $temp = $tablename."_primarykey";
  $temp2 = get_variables($temp," || ");
  $primarykey = $temp2[0];
  $tablename_primarykey = $tablename."_".$primarykey;
  if ($$tablename_primarykey) $$primarykey = $$tablename_primarykey;

  // ------------------------------------------------------------------------------------------------------------
  // --- for x2x type tables where primary key is relID we'll use the relative ID as the primary key instead....
  // ------------------------------------------------------------------------------------------------------------
  $temp = $tablename."_fields";
  $temp2 = get_variables($temp," ");
  $fields_temp = $temp2[0];
  $tablename_fields = $tablename."_".$fields_temp;
  $fields_temp = get_variables($tablename_fields." "," || ");
  if ($fields_temp) {
	  foreach($fields_temp as $index=>$field_array) {
		if ( substr($field_array,0,5)=="relID" ) $relID = substr($field_array,strpos($field_array,"=>")+2);
	  }
  }
  if (!$$primarykey && $$relID) {
  	$primarykey = $relID;
  	$$primarykey = $$relID;
  }
  if ($detail_debug) echo "primarykey is " . $primarykey . " - " . $$primarykey . "; relID is " . $relID . " - " . $$relID . "<br/>";
  // echo "editing is " . $editing . "<br />";

  // ------------------------------------------
  // ----- populate fields for editing entry...
  // ------------------------------------------
  if ($editing && !$_POST["submitval"] && !$error && $$primarykey) {

	// OK, we have to see if there is more than 1 relevant row (b/c of selectbox_multirow)
	$sql = "select count(*) as num from $tablename where $primarykey='".mysql_real_escape_string($$primarykey)."'";
	$getnumrows = $db->get_row($sql);
	$num = $getnumrows->num;
	//echo "num is " . $num;

	$sql = "select * from $tablename where $primarykey='".mysql_real_escape_string($$primarykey)."'";
	//echo $sql . "<br>";

	$var = "";
	$fields[$tablename] = get_variables($tablename."_fields");
	foreach($fields[$tablename] as $key=>$field) {
		if ($num>1) {
			$rows = $db->get_results($sql);
			//print_r($rows);
			foreach($rows as $a=>$b) {
				//echo $a . " - " . $b->$field . "<br>";
				$var .= $b->$field . ",";
			}
			$var = substr($var,0,strlen($var)-1);

		} else {
			$row = $db->get_row($sql);
			$var = $row->$field;
		}
		$tablefield = $tablename."_".$field;
		if ($_POST[$tablefield]) {
			$$tablefield = $_POST[$tablefield];
		} else {
			$$tablefield = $var;
		}
	}
  }

  // ---------------------
  // ----- update entry...
  // ---------------------
  //echo "i is " . $i . "; primarykey is ". $primarykey . ": ". $$primarykey . "<br>";
  if ($editing && $_POST["submitval"] && !$error && $$primarykey) {
	$sql = "select * from $tablename where $primarykey='".mysql_real_escape_string($$primarykey)."'";
	//echo $sql . "<br>";
	$row = $db->get_row($sql);
	$fields[$tablename] = get_variables($tablename."_fields");
	$update_sql = generate_sql("edit", $tablename, $fields[$tablename], $primarykey, $$primarykey, $row);
	if ($update_sql!="N/A") { 
		// now handle case where a selectbox_multirow generated multiple update sql statements
		if (stristr($update_sql,";;")) {
			$update_sql = substr($update_sql,0,strlen($update_sql)-2);
			$tok = strtok($update_sql, ";;");
			while ($tok) {
				if ($sql_debug) echo $tok . "<br />";
				if (!$execute_debug) $execute = $db->query("$tok");
				$tok = strtok(";;");
			}
		} else {
			if ($sql_debug) echo $update_sql . "<br />";
			if (!$execute_debug) $execute = $db->query($update_sql);
		}

		//echo "result is " . $db->result . "<br />";
		if ($db->result==1) {
			$temp = get_variables("form_edit_text"," || "); $message = $temp[0];
			if (!$message) $message .= "Entry Has Been Updated for table <code>" . $tablename . "</code> - Thank You";	
		} else {
			$error_message[] = "An error occurred editing an entry in table <code>" . $tablename . "</code>";
		}
	}
  }
  // ---------------------
  // ----- add entry...
  // ---------------------
  if (!$editing && $_POST["submitval"] && !$error) {
	$insert_sql = generate_sql("add", $tablename, $fields[$tablename], $primarykey, $$primarykey);
	if ($insert_sql!="N/A") { 
		// remove final ; if appropriate (i.e. if only one insert sql statement)
		//if (substr($insert_sql,-4,2)==";;") $insert_sql = substr($insert_sql,0,strlen($insert_sql)-3);
		if (substr_count($insert_sql,";;")==1) $insert_sql = substr($insert_sql,0,strlen($insert_sql)-3);
		// now handle case where a selectbox_multirow generated multiple insert sql statements
		if (stristr($insert_sql,";;")) {
			$insert_sql = substr($insert_sql,0,strlen($insert_sql)-2);
			$tok = strtok($insert_sql, ";;");
			while ($tok) {
				if ($sql_debug) echo $tok . "<br>";
				if (!$execute_debug) $execute = $db->query("$tok");
				$tok = strtok(";;");
			}
		} else {
			if ($sql_debug) echo $insert_sql . "<br />";
			if (!$execute_debug) $execute = $db->query($insert_sql);
			// --- first assign primary key to last insert so we have something to use in <link> section of RSS feed...
			//$mysql_insert_id = mysql_insert_id();
			$mysql_insert_id = $db->insert_id;
			//if (!$$primarykey) $$primarykey = mysql_insert_id();
			if (!$$primarykey) $$primarykey = $db->insert_id;
			if (!$first_insert_id) $first_insert_id = $$primarykey;
			if (!$$relID) $$relID = $_POST[$relID] = $$primarykey;
			if ($mysql_insert_id) {
				$insert_id[$tablename][] = $mysql_insert_id;
				// ------------------------------------------------------------------------------------------
				// --- if primary key for $tablename isn't already set let's set from the last insert id...
				$temp = $tablename."_primarykey";
				$temp2 = get_variables($temp," || ");
				$primarykey = $temp2[0];
				if (!$$primarykey) $$primarykey = $mysql_insert_id;
				// ------------------------------------------------------------------------------------------
			} else {
				$insert_id[$tablename][] = $$primarykey;
			}
			//echo $primarykey . " - " . $$primarykey . "<br><br>";
		}

		//echo "result is " . $db->result . "<br />";
		if ($db->result==1) {
			$temp = get_variables("form_insert_text"," || "); $message = $temp[0];
			if (!$message) $message .= "Entry Has Been Added - Thank You";
		} else {
			$error_message[] = "An error occurred adding an entry to table <code>" . $tablename . "</code>";
		}
	} else {
		if ($sql_debug) echo "sql: " . $insert_sql;
	}
  }
}

// ---------------------------------------------------------------------------------------
// --- include code that should only be included if we had success submitting the form...
// ---------------------------------------------------------------------------------------
if ($_POST["submitval"] && $db->result && !$error && count($error_message)==0) {
	// --- now include custom execute code...
	include_once ("addedit-execute-custom.php");

	// -------------------------------------------------------
	// --- generate RSS and Trackback if appropriate...
	// -------------------------------------------------------
	if ($create_RSS=="Yes") include_once ("addedit-rss.php");
	if (($editing && $trackback_edit=="Yes") || (!$editing && $create_trackback=="Yes")) include_once ("addedit-trackback.php");
	
	// -------------------------------------------------------
	// --- send email notifications 
	// -------------------------------------------------------
	if (!$editing && getenv('HTTP_HOST')!="www.phpaddedit.com" && getenv('HTTP_HOST')!="phpaddedit.com") {
		if ($_GET["email1_to"]) $email1_to = "";		// --- just in case someone tries to do an SQL injection...
		if ($_GET["email2_to"]) $email2_to = "";		// --- just in case someone tries to do an SQL injection...
		if (substr($email1_to,0,1)=="=") $email1_to = substr($email1_to,1);
		if (substr($email1_to,0,1)=="$") eval ("\$email1_to = \"$email1_to\"; ");
		if ($send_email1=="Yes" && $email1_to) send_email("1",$email1_to, $email1_cc, $email1_subject, $email1_body, $email1_body_default, $attachment1, $attachment1_name);
		if (substr($email2_to,0,1)=="=") $email2_to = substr($email2_to,1);
		if (substr($email2_to,0,1)=="$") eval ("\$email2_to = \"$email2_to\"; ");
		if ($send_email2=="Yes" && $email2_to) send_email("2",$email2_to, $email2_cc, $email2_subject, $email2_body, $email2_body_default, $attachment2, $attachment2_name);
	}

	// -------------------------------------------------------
	// --- redirect if appropriate
	// -------------------------------------------------------
	if ($form_success_redirect) refresh_page($form_success_redirect,5000);
}

// ---------------------------------------------------------------------------------------
// --- if form submission failed then redirect to form failure page if specified...
// ---------------------------------------------------------------------------------------
if ($_POST["submitval"] && !$db->result && $form_failure_redirect) refresh_page($form_failure_redirect,5000);


// ---------------------------------------------------------------------------------------
// --- Now show any succes/failure messages, send emails, and redirect if appropriate...
// ---------------------------------------------------------------------------------------
if ($message && !$_GET["message"]) {
	eval ("\$message = \"$message\"; ");
	printMessage($message,"");
}
if (count($error_message)>0) {
	for ($i=0; $i<count($error_message); $i++) {
		printMessage($error_message[$i],"red");
	}
}
?>