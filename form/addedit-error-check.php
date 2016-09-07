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

Function error_check($error_check_fields,$error_check_english) {
	global $editing, $db, $tables, $tablename, $fields, $humanverify, $humanverify_answer, $skip, $$skip, $akismet_use, $akismet_key, $akismet_fields, $encoding, $website;

	//print_r($_FILES);
	//print_r($error_check_fields); echo "<br><br>";
	//print_r($error_check_english); echo "<br>";
	
	if (is_readable(dirname(__FILE__)."/addedit-error-check-custom.php")) {
		include(dirname(__FILE__)."/addedit-error-check-custom.php");
	} else {
		printMessage("The Custom Error Check file (addedit-error-check-custom.php) cannot be read. It is probably a path issue or possibly a permission issue.","red");
	}

	foreach($error_check_fields as $key=>$fieldarray) {
		$$key = $_POST[$key];
		//echo "key is " . $key ." - " . $$key . "<br>";
		
		// --- let's unset any error check requirements for a skipped variable...
		//echo "skip - " . $skip . " - " . $$skip . "<br>";
		if (stristr($skip,$key)) $fieldarray = array();
		
		$english = strtoupper($error_check_english[$key]);
		if (!$english || $english==" ") $english = strtoupper($key);
		//echo "english is " . $english . "<br>";

		foreach($fieldarray as $fieldtype=>$constraint) {
			$fieldtype = substr($constraint,0,strpos($constraint,"=>"));
			$constraint = trim(substr($constraint,strpos($constraint,"=>")+2));
			//echo $key . " - " . $$key . "; type is " . $fieldtype . "; constraint is " . $constraint . "<BR>";
			switch ($fieldtype) {
			  case "date":
				if (substr($constraint,0,1)=="=") {
					$math = "equal";
					$constraint = substr($constraint,1);
				}
				if (substr($constraint,0,1)=="<") {
					$math = "lt";
					$constraint = substr($constraint,1);
				}
				if (substr($constraint,0,1)=="<=") {
					$math = "ltequal";
					$constraint = substr($constraint,2);
				}
				if (substr($constraint,0,1)==">") {
					$math = "gt";
					$constraint = substr($constraint,1);
				}
				if (substr($constraint,0,1)==">=") {
					$math = "gtequal";
					$constraint = substr($constraint,2);
				}
				if (substr($constraint,-1)!=";") $constraint .= ";";
				if (strstr($constraint,"=") && !$_GET["constraint"]) {
					eval("\$constraint = \"$constraint\"; ");
				} elseif ($constraint==";") {
					$constraint = "";
				} else {
					eval("\$constraint = $constraint");
				}
				// --- convert $$key and $constraint to timestamps so we can do math comparisons...
				$keyunix = strtotime($$key);
				$constraintunix = strtotime($constraint);
				//echo "<br>math - " . $math . "; key - " . $$key . "; constraint: " . $constraint;
				if ($$key) {
				  switch ($math) {
					case "equal":
						if ($keyunix==$constraintunix) $error .= "\\n The field \'" . $english . "\' must be = " . $constraint . "\\n";
						break;
					case "lt":
						if ($keyunix>=$constraintunix) $error .= "\\n The field \'" . $english . "\' must be < " . $constraint . "\\n";
						break;
					case "ltequal":
						if ($keyunix>$constraintunix) $error .= "\\n The field \'" . $english . "\' must be <= " . $constraint . "\\n";
						break;
					case "gt":
						if ($keyunix<=$constraintunix) $error .= "\\n The field \'" . $english . "\' must be > " . $constraint . "\\n";
						break;
					case "gtequal":
						if ($keyunix<$constraintunix) $error .= "\\n The field \'" . $english . "\' must be >= " . $constraint . "\\n";
						break;
				  }
				}
				break;
			  case "required":
			  	if ($_FILES[$key]) {
					if (!$_FILES[$key]['name'] && !$editing) $error .= "\\n The field \'" . $english . "\' cannot be empty\\n";
				} else {
					if ($$key=="") $error .= "\\n The field \'" . $english . "\' cannot be empty\\n";
				}
				break;
			  case "minchars":
			  case "minimum chars":
				if ($$key && strlen($$key)<$constraint) $error .= "\\n The field \'" . $english . "\' must be at least " . $constraint . " characters\\n";
				break;
			  case "maxchars":
			  case "maximum chars":
				if ($$key && strlen($$key)>$constraint) $error .= "\\n The field \'" . $english . "\' must be less than " . $constraint . " characters\\n";
				break;
			  case "minvalue":
			  case "minimum value":
				if ( $$key && (is_integer($$key) && $$key<=$constraint) ) $error .= "\\n The field \'" . $english . "\' cannot be less than " . $constraint . "\\n";
				break;
			  case "maxvalue":
			  case "maximum value":
				if ( $$key && (is_integer($$key) && $$key>=$constraint) ) $error .= "\\n The field \'" . $english . "\' cannot be more than " . $constraint . "\\n";
				break;
			  case "equal":
				if ($$key!=$constraint) $error .= "\\n The field \'" . $english . "\' must equal " . $constraint . "\\n";
				break;
			  case "notequal":
			  case "not equal":
				if ($$key==$constraint) $error .= "\\n The field \'" . $english . "\' cannot equal \'" . $constraint . "\'\\n";
				break;
			  case "email":
			  	if ($$key) {
					include_once(dirname(__FILE__)."/includes/verifyemail-lib.php");
				  	if ($constraint=="lax" || $constraint=="medium" || $constraint=="strict") $email_verify = verifyemail_validateemail($$key); 
					if ($constraint=="medium" || $constraint=="strict") $email_verify = verifyemail_validatehost($$key); 
					if ($constraint=="strict") $email_verify = verifyemail_validateexists($$key); 
					if ($email_verify==false) $error .= "\\n Email address \'" . strtoupper($$key) . "\' appears to be invalid. Please double-check your entry.\\n";
				}
				break;
			  case "filesize":
				//echo "FILES is "; print_r($_FILES);
				if ($_FILES[$key]['size']>$constraint) $error .= "\\n The file \'" .strtoupper($_FILES[$key]['name']) . "\' exceeds size limit ( " . ($constraint/1000) . " Kb)";
				break;
			  case "password":
				if ($$key!=$_POST["passwordconfirm"]) $error .= "\\nYour Password and Password Confirmation Don\'t Match.\\n";
				break;
			  case "unique":
			  	// only check unique if adding a new entry (if editing, obviously the entry could already exist)
				if (!$editing && $$key) {
					for ($i=1; $i<=count($tables); $i++) {
					  $tablename = trim($tables[$i-1]);
					  if (stristr($key,$tablename."_")) {
						$table = $tablename;
						$field = substr($key,strlen($tablename)+1,strlen($key));
					  }
					}
					$sql = "SELECT * FROM $table WHERE $field='" . addslashes(stripslashes($$key)) . "'";
					$row = $db->get_row($sql);
					if ($row) $error .= "\\n The field \'" . $english . "\' already exists and must be unique\\n";
				}
				break;
			  case "url":
				//echo "directory is " . dirname(__FILE__);
				include(dirname(__FILE__)."/includes/validateurl.php");
				if ($$key) {
					// Check to make sure URL has http:// else append it
					$begin = substr($$key,0,7);
					if ($begin!="http://" && $begin!="https:/") {
						$$key = "http://" . $$key;
					}
					if (!validateUrlSyntax($$key,'')) $error .= "\\n The field \'" . $english . "\' is not a properly formatted URL\\n";

					// ---------------------------------------------------------------------------------
					// --- Disabling the actual test of a live or dead link for now b/c unreliable...
					// ---------------------------------------------------------------------------------
					//$checkurl = get_http_headers($$key);
					//if ($checkurl[result]!=502 && $checkurl[result]!=200 && $checkurl[result]!="OK")  $error .= "\\n The field \'" . $english . "\' is not a valid URL [Code " . $checkurl[result] . "]\\n";
					//echo "Checking - ".stripslashes($$key)." :: Status - ".$checkurl[result]." - ".$text[$checkurl[result]]." - ".$checkurl[message];
				}
				break;
			}
			
		}

	}

	// ----------------------------------------------------------------------------------------------------
	// --- now check human verification question
	// ----------------------------------------------------------------------------------------------------
	if ($humanverify=="Y" && $_POST["humanverify_response"]!=$humanverify_answer) $error .= "\\nYou didn\'t answer the human verification question properly\\n";

	// ----------------------------------------------------------------------------------------------------
	// --- now check Akismet if setup
	// ----------------------------------------------------------------------------------------------------
	if ($akismet_use=="Y") {
		// --- need to declare all the following global variable b/c they are ussed in the addedit-kismet.php file and o/w don't have scope
		global $akismet_api_key, $akismet_api_host, $akismet_api_port, $charset, $blog, $latestversion, $akismet_result;
		$akismet_api_key = $akismet_key;
		include("addedit-akismet.php");
		$validkey = akismet_verify_key($akismet_api_key);
		if ($validkey!="valid") {
			printMessage("Invalid Akismet API Key","red");
		} else {
			$content = "viagra-test-123";	// --- use this as a sure-fire test string when debugging/developing...
			$content = "";
			//echo "fields: " . $akismet_fields . "<br />";
			$akismet_fields_array = explode(",",$akismet_fields);
			//print_r($akismet_fields_array); echo "<br />";
			foreach ($akismet_fields_array as $index=>$field) {
				$content = $_POST[$field] . " ";
				//echo $content; echo "<br />\n";
				$testit = akismet_auto_check_comment($content);
				// echo "akismet test result: " . $testit . "<br />";
				if ($testit == 'true' || $akismet_result == "Failed Spam Test") {
					$akismet_result = "Failed Spam Test";
				} else {
					$akismet_result = "passed";
				}
			}
		}

		if ($akismet_result!="passed") $error .= "\\n" . $akismet_result . "\\n";
	}

	if ($error) $error .= "\\n";

	if ($error) {
		echo "<script type=\"text/javascript\"> alert('$error'); </script>";
		echo "<noscript>";
		$error = "<font color=red>" . $error . "</font>";
		$error = str_replace("\\n", "<br />", $error);
		$error = stripslashes($error);
		printMessage($error,"");
		echo "</noscript>";
		return true;
	} else {
		return false;
	}
}
?>