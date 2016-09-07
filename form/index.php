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
// --- set error reporting
// ----------------------------------------------------------------------------------------------------
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors","1");


// ----------------------------------------------------------------------------------------------------
// --- core includes
// ----------------------------------------------------------------------------------------------------
include_once ("config.php");
include_once ("includes/dbconnect.inc.php");
include_once ("addedit-functions.php");


// ----------------------------------------------------------------------------------------------------
// --- define basic variables 
// ----------------------------------------------------------------------------------------------------
//$addeditdir = "/".substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1)."/";
$addeditdir = realpath(dirname(__FILE__));
//echo "addeditdir is ".$addeditdir;
$host = $_SERVER['HTTP_HOST']; 
if (!$website) {
	$website = $host;
	if (substr($website,0,4)=="www.") $website = substr($website,4);
}
if ($_GET["page"]) $page = urlencode($_GET["page"]);
if ($_GET["type"]) $type = urlencode($_GET["type"]);
if ($_GET["what"]) $what = urlencode($_GET["what"]);
if ($_GET["ID"]) $ID = $_GET["ID"];
$temp_date = mktime(0,0,0,date("m"),date("d"),date("Y"));
$today = date( "Y-m-d", $temp_date );
$temp_date = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
$yesterday = date( "Y-m-d", $temp_date );
$temp_date = mktime(0,0,0,date("m"),date("d")-7,date("Y"));
$last_week = date( "Y-m-d", $temp_date );
$temp_date = mktime(0,0,0,date("m"),1,date("Y"));
$this_month = date( "Y-m-d", $temp_date );
$temp_date = mktime(0,0,0,date("m")-1,date("d"),date("Y"));
$last_month = date( "Y-m-d", $temp_date );


// ----------------------------------------------------------------------------------------------------
// --- Check for permission or show login
// ----------------------------------------------------------------------------------------------------
$cookievalue = substr(ADMIN_USERNAME,0,4) . "-" . substr(ADMIN_PASSWORD,-4);
if (!$_COOKIE["addedit"] || $_COOKIE["addedit"]!=$cookievalue) {
	include ("addedit-login.php");
	exit;
} else {
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	echo "<head>\n";
	echo "<title>phpAddEdit Form Generator Script - Create PHP Forms Easily</title>\n";
	echo "<link rel=\"stylesheet\" href=\"includes/admin.css\" type=\"text/css\" />\n";
	echo "<link rel=\"stylesheet\" href=\"includes/cluetip.css\" type=\"text/css\" />\n";
	if ($page=="test") echo "<link rel=\"stylesheet\" href=\"includes/style.css\" type=\"text/css\" />\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
	echo "<script type=\"text/javascript\" src=\"includes/javascripts.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"includes/jquery-1.2.3.pack.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"includes/jquery.cluetip.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"includes/jquery.dimensions.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"includes/dynamic.js\"></script>\n";
	echo "</head>\n";
	echo "<body>\n";

	// ------------------------------------------------------
	// --- use a table because some of the steps have fairly 
	// --- wide pages and this will keep the header properly 
	// --- sized (since I use tables in the steps file). 
	// ------------------------------------------------------
	echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" style=\"background: #eaf3fa; border: 1px solid #999999;\"><tr><td>\n";

	// ------------------------------------------------------
	// --- Header
	// ------------------------------------------------------
	include ("includes/header.php");

	// ------------------------------------------------------
	// --- include relevant phpAddEdit files
	// ------------------------------------------------------
	include ("addedit-error-check.php");
	include ("addedit-form-fields.php");
	if ($editform_error) {
		printMessage($editform_error,"red");
		exit;
	} else {
		include ("addedit-steps.php");
	}
	
	// ------------------------------------------------------
	// --- Main page content
	// ------------------------------------------------------
	$error_check_fields = "";

	//echo "form name: " . $formname . "; editform: " . $editform . "; FORM_VARIABLES: " . FORM_VARIABLES . "<br />";

	// ------------------------------------------------------
	// --- get current version and notify if out of date...
	// ------------------------------------------------------
	include ("addedit-getversion.php");

	// ---------------------------------------------------------
	// --- lockout the wordpress demo on the phpAddEdit site...
	// ---------------------------------------------------------
	//echo $addeditdir;
	if ($addeditdir=="/demo" && (FORM_NAME=="forms/wordpress_content.php" || FORM_NAME=="forms/content.php" || FORM_NAME=="forms/faq.php" || FORM_NAME=="forms/test.php") ) {
		printMessage("this file cannot be edited in the DEMO","red");
		exit;
	}
	
	// ---------------------------------------------------------
	// --- do things depending on what page is
	// ---------------------------------------------------------
	switch ($page) {
	  // ----------------------------------------------------------------------------------------------------
	  // --- set debug levels from admin menu 
	  // ----------------------------------------------------------------------------------------------------
	  case "debug":
		if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
			printMessage("This feature is disabled for the demo","red");
		} else {
			echo "<div class=\"padded\">\n";	
			switch ($what) {
			  case "sql":
				edit_file ("config.php","sql_debug","sql_debug",";","sql_debug = true");
				printMessage("turned on SQL debugging - use the None option on the menu above to cancel");
				break;
			  case "execute":
				edit_file ("config.php","execute_debug","execute_debug",";","execute_debug = true");
				printMessage("turned on execute debugging (no SQL statements will be executed)- use the None option on the menu above to cancel");
				break;
			  case "verbose":
				edit_file ("config.php","detail_debug","detail_debug",";","detail_debug = true");
				printMessage("turned on verbose debugging - use the None option on the menu above to cancel");
				break;
			  case "none":
				edit_file ("config.php","sql_debug","sql_debug",";","sql_debug = false");
				edit_file ("config.php","execute_debug","execute_debug",";","execute_debug = false");
				edit_file ("config.php","detail_debug","detail_debug",";","detail_debug = false");
				printMessage("turned off all debugging");
				break;
			}
			echo "</div>\n";
		}
		break;
	  case "delete":
		echo "<div class=\"padded\">\n";	
	  	// --- it's very hard to write a generic routine here b/c how the form is named and how many tables are involved can make a huge difference
		// --- the first section is an attempt at a generic routine but use the second section to customize to your own needs...
		define('FORM_VARIABLES', "../addedit/forms/".$what."_variables.php");
		define('FORM_NAME', "../addedit/forms/".$what.".php");
		$tables = get_variables("tables");
		//print_r($tables);
		foreach ($tables as $tablename) {
			$primarykey = $tablename."_primarykey";
			$temp = get_variables($primarykey," || ");
			$$primarykey = $temp[0];
			//echo $$primarykey . "<br />";
			if (stristr($tablename,$what)) {
				$delete_sql = "DELETE FROM $tablename WHERE ".$$primarykey."=".$ID;
				//echo $delete_sql . "<br />";
				$db->query($delete_sql);
				printMessage("Deleted ID=$ID from table <code>$tablename</code>");	
			}
		}
		
		switch ($what) {
		  case "wordpress_content":
			$tables = array("wp_posts"=>"ID","wp_post2cat"=>"post_id");
			foreach ($tables as $tablename=>$primarykey) {
				$delete_sql = "DELETE FROM $tablename WHERE ".$primarykey."=".$ID;
				//echo $delete_sql . "<br />";
				$db->query($delete_sql);
				printMessage("Deleted ID=$ID from table <code>$tablename</code>");	
			}
			break;
		  case "":
			$tables = array();
			break;
		}

		echo "</div>\n";
		break;
	  case "email": 
	  	if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
			echo "<br />\n";
			printMessage("This feature is disabled for the demo","red");
		} else {
			// --------------------------------------
			// --- Test Email on finished page
			$temp = get_variables("send_email1"," || "); $send_email1 = $temp[0];
			$temp = get_variables("send_email2"," || "); $send_email2 = $temp[0];

			if ($send_email1=="No" && $send_email2=="No") {
				printMessage("You didn't specify any email send options for this form so there is nothing to test","red");
				break;
			}
			
			if ($_POST["test_email1"] || $_POST["test_email2"]) {
				$temp = get_variables("email_engine"," || "); $email_engine = $temp[0];
				$temp = get_variables("smtp_host"," || "); $smtp_host = $temp[0];
				$temp = get_variables("smtp_auth"," || "); $smtp_auth = $temp[0];
				$temp = get_variables("smtp_user"," || "); $smtp_user = $temp[0];
				$temp = get_variables("smtp_pass"," || "); $smtp_pass = $temp[0];
				$temp = get_variables("email_from "," || "); $email_from = $temp[0];
				$temp = get_variables("email_from_name"," || "); $email_from_name = $temp[0];
				$temp = get_variables("email_reply"," || "); $email_reply = $temp[0];
				$temp = get_variables("email_bounce"," || "); $email_bounce = $temp[0];
				$temp = get_variables("email1_to"," || "); $email1_to = $temp[0];
				$temp = get_variables("email1_cc"," || "); $email1_cc = $temp[0];
				$temp = get_variables("email1_subject"," || "); $email1_subject = $temp[0];
				$temp = get_variables("email1_body"," || "); $email1_body = $temp[0];
				$temp = get_variables("email1_body_default"," || "); $email1_body_default = $temp[0];
				$temp = get_variables("email2_to"," || "); $email2_to = $temp[0];
				$temp = get_variables("email2_cc"," || "); $email2_cc = $temp[0];
				$temp = get_variables("email2_subject"," || "); $email2_subject = $temp[0];
				$temp = get_variables("email2_body"," || "); $email2_body = $temp[0];
				$temp = get_variables("email2_body_default"," || "); $email2_body_default = $temp[0];
				$temp = get_variables("email_format"," || "); $email_format = $temp[0];
				$temp = get_variables("attachment1 "," || "); $attachment1 = $temp[0];
				$temp = get_variables("attachment1_name"," || "); $attachment1_name = $temp[0];
				$temp = get_variables("attachment2 "," || "); $attachment2 = $temp[0];
				$temp = get_variables("attachment2_name"," || "); $attachment2_name = $temp[0];
				if ($_POST["test_email1"]) send_email("1", $email1_to, $email1_cc, $email1_subject, $email1_body, $email1_body_default, $attachment1, $attachment1_name);
				if ($_POST["test_email2"]) send_email("2", $email2_to, $email2_cc, $email2_subject, $email2_body, $email2_body_default, $attachment2, $attachment2_name);
				//echo $email1_to . "; " . $email1_cc . "; " . $email1_subject . "; " . $email1_body . "; " . $email_body1_default . "; " . $attachment1 . "; " . $attachment1_name;
				printMessage ("Test Email Sent");
				break;
			}
	
			foreach($_GET as $index=>$value) {
			  $formaction_append .= "$index=$value&";
			}
			if ($formaction_append) $formaction = $_SERVER['PHP_SELF'] . "?" . substr($formaction_append,0,strlen($formaction_append)-1);
			printMessage(" Test Email Settings","","highlight");
			echo "<br />";
			$message .= "<form name=\"generatecode\" enctype=\"multipart/form-data\" action=\"" . $formaction . "\" method=\"post\">\n";
			$message .= "<strong>Test to make sure email settings are correct...</strong><br />\n";
			$message .= "In Step 9 you specified quite a few email related settings. Below you can test them (though note that the <code>email to</code> setting needs to be one of your own addresses)<br /><br />\n";
			if ($send_email1=="Yes") $message .= "<span class='submit' style=\"margin-left: auto; margin-right: auto;\"><input type=\"submit\" name=\"test_email1\" value=\"Test Email 1\"></span>\n";
			if ($send_email2=="Yes") $message .= "<span class='submit' style=\"margin-left: auto; margin-right: auto;\"> &nbsp; <input type=\"submit\" name=\"test_email2\" value=\"Test Email 2\"></span>\n";
			$message .= "<br />\n";
			$message .= "</form>\n";
			printMessage($message);
			// --------------------------------------
		}
		break;
	  case "test":
		if (!$formname && !$editform) { 
			echo "<br />\n";
			printMessage("You must specify a form to test - see Step1","red");
		} else {
			include ("addedit-render.php");
		}
		break;
	  case "toc":
		printMessage("<strong>Note:</strong> This feature is not perfect - it should work fine for simple, one-table forms but may not work properly for complicated, multi-table forms...");
		echo "<br />\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" style=\"margin-left:10px\">\n";
		$tables = get_variables("tables");
		foreach($tables as $index=>$tablename) {
			$fields[$tablename] = get_variables($tablename."_fields ");
			$index_fields = get_variables($tablename."_index_fields ");
			//print_r($index_fields);
			if (count($index_fields)) {
				$rows = $db->get_results("select * from $tablename");
				//print_r($rows);
				for ($n=0; $n<=count($rows); $n++) {
					echo "<tr>";
					foreach ($index_fields as $x=>$field) {
						if ($n==0) {
							if ($x==0) {
								echo "<th align=\"center\" valign=\"top\" style=\"background:inherit;\"></th>\n";
								echo "<th align=\"center\" valign=\"top\" style=\"background:inherit;\"></th>\n";
							}
							echo "<th align=\"center\">";
							echo $field;
							echo "</th>\n";
						} else {
							if ($x==0) {
								$primarykey = $tablename."_primarykey";
								$temp = get_variables($primarykey," || ");
								$key = $tablename."_".$temp[0] . "=" . $rows[$n-1]->$field;
								$edit_link = "index.php?page=test&amp;editform=".$editform."&amp;".$key;
								$delete_link = "index.php?page=delete&amp;editform=".$editform."&amp;ID=".$rows[$n-1]->$field;
								//echo $edit_link . "<br>";
								echo "<td align=\"center\" valign=\"top\" bgcolor=\"#E5E5E5\"><a href=\"".$edit_link."\"><img src=\"images/edit.png\" /></a> </td>\n";
								echo "<td align=\"center\" valign=\"top\" bgcolor=\"#E5E5E5\"><a href=\"".$delete_link."\" onclick=\"return confirmDelete('$key');\"><img src=\"images/delete.png\" /></a> </td>\n";
							}
							if ($n%2==0) {
								echo "<td valign=\"top\" bgcolor=\"#E5E5E5\">";
							} else {
								echo "<td valign=\"top\" bgcolor=\"#D5D5D5\">";
							}
							echo $rows[$n-1]->$field;	
							echo "</td>\n";
						}
					}
					echo "</tr>\n";
				}
			}
		}
		echo "</table>\n";
		break;
	  default:
		printf("<form name=\"generatecode\" id=\"generatecode\" enctype=\"multipart/form-data\" action=\"%s\" method=\"post\">\n",$_SERVER['PHP_SELF']);
	
		// -----------------------------------------------------------------------------------------------------------
		// --- Do our error check - if it fails we set $continue back a step to repeat until we get it right...
		if ($_POST["continue"] && $error_check_fields) $error_check = error_check($error_check_fields,$error_check_english);
		if ($error_check) $continue = $prev;
		// -----------------------------------------------------------------------------------------------------------
	
		if ( !$first_page ) {
			if (!$formname && !$editform && $_GET["continue"]!="Next - Step 1" && $step!="1") { 
				echo "<br />\n";
				printMessage("You must specify a form to test - see Step1","red");
				exit;
			}
		}
		// -----------------------------------------------------------------------------------------------------------
	
		//echo "continue is " . $continue;
		if (!$formname && !$editform && !$continue && $step>1) { 
			echo "<br />\n";
			printMessage("You must specify a form to test - see Step1","red");
			break;
		} 
		switch ($continue) {
		  case "Next - Step 1":
	
			if ($versionchkerror) {
				printMessage($versionchkerror,"red");
			} else {
				if ($thisversion < $latestversion) printMessage("A newer version of phpAddEdit (".$latestversion.") is available. <a href=\"http://www.phpaddedit.com/page/download/\">Download latest version</a>.","red");
			}
	
			// ------------------------------------------------------------------------------------------------
			// set error checking...
			$error_check_fields["addenable"] = array("required"=>"");
			$error_check_fields["editenable"] = array("required"=>"");
			//$error_check_fields["numtables"] = array("minimum chars"=>"2");
			$error_check_english = array("Enable Add", "Enable Edit", "numtables"=>"Number of Tables");
			// ------------------------------------------------------------------------------------------------
			if ( !$connect_error && !$select_error ) {
				step1HTML();
				break;
			} else {
				if (!$first_page && $connect_error) printMessage ($connect_error,"red");
				if (!$first_page && !$connect_error && $select_error) printMessage ($select_error,"red");
				step0HTML($dbhost);
				break;
			}
		  case "Next - Step 2":
			step2HTML();
			break;
		  case "Next - Step 3":
			step3HTML();
			break;
		  case "Next - Step 4":
			step4HTML();
			break;
		  case "Next - Step 5":
			step5HTML();
			break;
		  case "Next - Step 6":
			step6HTML();
			break;
		  case "Next - Step 7":
			step7HTML();
			break;
		  case "Next - Step 8":
			step8HTML();
			break;
		  case "Next - Step 9":
			step9HTML();
			break;
		  case "Next - Step 10":
			step10HTML();
			break;
		  case "Next - Step 11":
			step11HTML();
			break;
		  default:
			if ( !$connect_error && !$select_error ) {
				$intro = "This script will help you generate the PHP file necessary to run the addedit script.";
				printMessage($intro);
				step1HTML();
			} else {
				$intro = "This script will help you generate the PHP file necessary to run the addedit script.";
				printMessage($intro);
				step0HTML();
			}
			break;
		}
	
		// ---------------------------------------------------------------------------------
		// Let's keep track of previous step variables as we progress through the process...
		// ---------------------------------------------------------------------------------
		if ($connect_error) echo "<input type='hidden' name='connect_error' value='1' />\n";
		if ($select_error) echo "<input type='hidden' name='select_error' value='1' />\n";
		if ($editform && $step!="1") echo "<input type='hidden' name='editform' value='$editform' />\n";
		if ($formname && $step!="1") echo "<input type='hidden' name='formname' value='$formname' />\n";
	
		if ($error_check_fields && is_array($error_check_fields)) {
			foreach($error_check_fields as $elementname=>$elementarray) {
				foreach($elementarray as $index=>$value) {
					echo "<input type='hidden' name='error_check_fields[$elementname][$index]' value='$value' />\n";
				}
			}
		}
		if ($error_check_english && is_array($error_check_english)) {
			foreach($error_check_english as $index=>$value) {
				echo "<input type='hidden' name='error_check_english[$index]' value='$value' />\n";
			}
		}
		// ---------------------------------------------------------------------------------
	
	
		echo "</form>\n";
		echo "<br />\n";
	}
	
	echo "</td></tr></table>\n";
}
?>

</body>
</html>