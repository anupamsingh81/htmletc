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

// --------------------------------------------------------------------------------------------------
// --- create the actual form and elements...
// --------------------------------------------------------------------------------------------------

// --------------------------------------------------------------------------------------------------
// --- secure the demo by not allowing any eval() statements on phpaddedit.com
// --------------------------------------------------------------------------------------------------
if ( (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") ) {
	$formaction = $_SERVER['REQUEST_URI']; 	// used to use $_SERVER['PHP_SELF'] but that doesn't account for GET variables...
} else {
	if (substr($form_action,-1)!=";") $form_action .= ";";
	if (strstr($form_action,"=") || strstr($form_action,"$")) {
		if (substr($form_action,0,1)=="=") $form_action = substr($form_action,1);
		eval ("\$formaction = $form_action");
	} else {
		$formaction = $form_action;
	}
	if (substr($formaction,-1)==";") $formaction = substr($formaction,0,strlen($formaction)-1);
	foreach($_GET as $index=>$value) {
	  if (stristr($form_action,"PHP_SELF")) $formaction_append .= "$index=$value&";
	  $$index = $value; 
	}
	if ($formaction_append) {
		$formaction_append = str_replace("&","&amp;",$formaction_append);
		$formaction .= "?" . substr($formaction_append,0,strlen($formaction_append)-1);
	}
}
// --------------------------------------------------------------------------------------------------

if ($form_width) $formwidth = " style=\"width:".$form_width."\"";
printf("<form id=\"addedit\" name=\"%s\" enctype=\"%s\" action=\"%s\" method=\"%s\" class=\"formclass\"%s onsubmit=\"LockSubmit();\">\n",$form_name,$form_enctype,$formaction,strtolower($form_method),$formwidth);
echo "<table class=\"form-table\">\n";
if ($form_title) echo "<tr><td class=\"form-title\">$form_title</td></tr>\n";
echo "<tr><td>\n";

// --------------------------------------------------------------------------------------------------
// --- first, loop through all rows and columns...
// --------------------------------------------------------------------------------------------------
for($i=1; $i<=$numsections; $i++) {
	$rownum = "section".$i."numrows";
	$colnum = "section".$i."numcols";
	$sectiontitle = "section".$i."title";
	$sectionclass = "section".$i."class";
	$sectiontitleclass = "section".$i."titleclass";
	//echo "rownum is " . $rownum . " - " . $$rownum . "<br>";
	//echo "colnum is " . $colnum . " - " . $$colnum . "<br>";
	echo "<table class=\"".$sectionclass."\">\n";
	if ($$sectiontitle) echo "<tr><td colspan=\"".$$colnum."\">\n<div class=\"$sectiontitleclass\">".$$sectiontitle . "<br /></div>\n</td></tr>\n";

	for($x=1; $x<=$$rownum; $x++) {
	  echo "<tr>\n";
	  $sectionrownum = "section".$i."row".$x;
	  if (!$$sectionrownum) $temp = get_variables($sectionrownum." "," || ");

	  // --------------------------------------------------------------------------------------------------
	  // --- now loop through all columns for that row
	  // --------------------------------------------------------------------------------------------------
	  for($n=1; $n<=$$colnum; $n++) {

		$$sectionrownum = $temp[$n-1];
		if ( line_exists(FORM_VARIABLES,$$sectionrownum) ) {
			$tablefield = $$sectionrownum;
			$variable = "element";
		} else {
			$tablefield = substr($$sectionrownum,0,strrpos($$sectionrownum,"_"));
			$variable = substr($$sectionrownum,strrpos($$sectionrownum,"_")+1);
		}
		//echo $tablefield . " - " . $variable . "<br>";

		$variable_array = get_variables($tablefield." "," || ");
		//print_r($variable_array); 
		//echo "<br>\n";
		$value = "";
		if ($variable_array) {
		  foreach($variable_array as $index=>$field_array) {
			//print_r($field_array); 
			//echo "<br>";
			if ( substr($field_array,0,strlen($variable))==$variable ) $value = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,4)=="size" ) $size = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,6)=="maxlen" ) $maxlen = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,4)=="cols" ) $cols = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,10)=="errorcheck" ) $errorcheck = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,10)=="constraint" ) $constraint = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,7)=="english" ) $english = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,5)=="event" ) $event = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,7)=="default" ) $default = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,5)=="relID" ) $relID = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,11)=="populatestr" ) {
				$populatestr = substr($field_array,strpos($field_array,"=>")+2);
				eval ("\$populatestr = \"$populatestr\";");
			}
			if ( substr($field_array,0,17)=="populatevariables" ) $populatevariables = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,10)=="other_name" ) $other_name = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,11)=="other_value" ) $other_value = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,9)=="separator" ) $separator = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,8)=="selected" ) {
				$selected = substr($field_array,strpos($field_array,"=>")+2);
			} else {
				if ($_GET["selected"]) $selected = ""; 		// --- just in case someone tries to do an SQL injection...
			}
			if ( substr($field_array,0,7)=="checked" ) $checked = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,9)=="pwconfirm" ) $pwconfirm = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,6)=="pwhelp" ) $pwhelp = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,4)=="list" ) $list = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,5)=="align" ) $align = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,7)=="filedir" ) $filedir = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,11)=="displayfile" ) $displayfile = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,7)=="fckedit" ) $fckedit_toolbar = substr($field_array,strpos($field_array,"=>")+2);
			if ( substr($field_array,0,7)=="filedir" && !$files_directory ) $files_directory = substr($field_array,strpos($field_array,"=>")+2);
			//if ( substr($field_array,0,6)=="" ) $ = substr($field_array,strpos($field_array,"=>")+2);
		  }
		}


		// --------------------------------------------------------------------------------------------------
		// --- If you want to skip or change the type of a field based on some criteria use the 
		// --- addedit-customize.php file to set a skip variable; it should be of the form $tablefield."_skip". 
		// --- For example:
		// ---    $skip = "content_html_skip";
		// ---    $$skip = "skip";				--- use this to completely skip inclusion of the field
		// ---    $$skip = "textbox_noedit";	--- use this to create a non-editable text box instead of skipping
		// ---    $$skip = "hidden";			--- use this to create a hidden form field instead of skipping
		// --------------------------------------------------------------------------------------------------
		$tablefield_skip = $tablefield."_skip";
		if ($$tablefield_skip) {
			//echo "$tablefield_skip is ".$$tablefield_skip;
			if ($$tablefield_skip=="textbox_noedit") {
				$value = "textbox_noedit";
			} elseif ($$tablefield_skip=="hidden") {
				$value = "hidden";
			} elseif ($$tablefield_skip=="skip") {
				echo "<td></td>";		// --- do this so we have valid HTML
				continue;
			}
		}

		// --------------------------------------------------------------------------------------------------
		// --- evaluate default as a PHP expression if appropriate
		// --------------------------------------------------------------------------------------------------
		if ($default && substr($default,0,1)=="=") {
			// ----------------------------------------------------------------------------------------------
			// --- secure the demo by not allowing any eval() statements on phpaddedit.com
			// ----------------------------------------------------------------------------------------------
			if ( (getenv('HTTP_HOST')=="www.phpaddedit.com" || getenv('HTTP_HOST')=="phpaddedit.com") && ($formname!=$addeditdir."/forms/wordpress_content") ) {
				$default = "";
				printMessage("You specified a php statement as the default value for <code>$tablefield</code>. That feature is disabled in this demo.","red");
			} else {
				// --- make sure default specified is properly formatted (no "=" at beginning and a ";" at the end)
				if (substr($default,0,1) == "=") $default = substr($default,1);		// --- get rid of starting = if exists...
				if (substr($default,-1)!=";") $default .= ";";
				eval ("\$default = $default");
			}
			//echo "default - " . $default . "<br />";
		}
		// --------------------------------------------------------------------------------------------------
		
		if ($populatevariables) {
			$option_value = substr($populatevariables,0,strpos($populatevariables,"=>"));
			$option_display = substr($populatevariables,strpos($populatevariables,"=>")+2);
		}

		echo "<td align=\"left\" valign=\"top\">\n"; 
		//echo $value . "<br>";
		$$tablefield = stripslashes($$tablefield);
		$desc1 = get_variable(FORM_VARIABLES,$tablefield." ","desc1","=>"," ||");
		// --------------------------------------------------------------------------------------------------
		// --- eval the desc1 if it contains a variable indicator...
		// --------------------------------------------------------------------------------------------------
		if (stristr($desc1,"$")) {
			$desc1 = stripslashes($desc1);
			$desc1 = str_replace('"','\"',$desc1);
			eval ("\$desc1 = \"$desc1\";");
		}

		$desc2 = get_variable(FORM_VARIABLES,$tablefield." ","desc2","=>"," ||");
		// --------------------------------------------------------------------------------------------------
		// --- eval the desc2 if it contains a variable indicator...
		// --------------------------------------------------------------------------------------------------
		if (stristr($desc2,"$")) {
			$desc2 = stripslashes($desc2);
			$desc2 = str_replace('"','\"',$desc2);
			eval ("\$desc2 = \"$desc2\";");
		}

		if ($$tablefield_skip=="hidden") $desc1 = $desc2 = "";
		if ($desc1) {
			if ($value!="hidden" && ($desc1_location=="fieldset" || $desc2_location=="fieldset") ) echo "<div><fieldset id=\"". $tablefield . "\"><legend>\n";
			if (stristr($errorcheck,"required")) {
				$text_class = "form-required-text";
			} else {
				$text_class = "form-text";
			}
			if ($desc1_location=="fieldset" || $desc1_location=="left" || $desc1_location=="top") echo "<span class=\"$text_class\">" . $desc1 . "</span>\n";

			// --------------------------------------------------------------------------------------------------
			// --- add the password help message if appropriate...
			// --------------------------------------------------------------------------------------------------
			if ($pwhelp && ($value=="password" || $value=="passwordmd5")) printf("<a href=\"javascript:alert('%s')\"><img src=\"%s\" alt=\"Need to Know More?\" height=\"12\" width=\"12\" align=\"top\" hspace=\"2\" border=\"0\" /></a>",$pwhelp,$addeditdir."/images/info.gif");
			if ($value=="passwordmd5" && $editing) {
				$md5help = "This site encrypts your password to provide extra security. Since this password cannot be decrypted the fields below cannot be pre-populated. You must re-enter your password.";
				printf(" &nbsp; <a href=\"javascript:alert('%s')\">why is this blank?</a><br />",$md5help);
			}
			
			if ($desc2 && $desc2_location=="fieldset") echo "<span class=\"$text_class\">" . $desc2 . "</span>\n";
			if ($value!="hidden" && ($desc1_location=="fieldset" || $desc2_location=="fieldset") ) echo "</legend>\n";
		}
		if ($desc2 && $desc2_location=="left") echo " <span class=\"$text_class\">" . $desc2 . "</span>\n";
		if ($desc2 && $desc2_location=="top") echo "<span class=\"$text_class\">" . $desc2 . "</span><br />\n";

		if ($variable=="element" && $$sectionrownum && $tablefield) {
			//echo "tablefield is " . $tablefield . " - " . $$tablefield . " - " . $_POST[$tablefield] . "<br>";

			if ( substr($selected,0,1)=="=") {
				$selected = substr($selected,1);	// --- get rid of starting = if exists...
				if (substr($selected,-1)!=";") $selected .= ";";
				if (stristr($desc2,"$")) {
					eval ("\$selected = \"$selected\";");
				} else {
					eval ("\$selected = $selected");
				}
			}

			// ----------------------------------------------------------------------------------------------------------------------------
			if ( substr(strtolower($selected),0,6)=="select " && $editing ) {
				$$tablefield = "";
				// --------------------------------------------------------------------------------------------------
				// --- evaluate any selected field statements...
				// --- secure the demo by not allowing any eval() statements on phpaddedit.com
				// --------------------------------------------------------------------------------------------------
				if ( (getenv('HTTP_HOST')=="www.phpaddedit.com" || getenv('HTTP_HOST')=="phpaddedit.com") && ($formname!=$addeditdir."/forms/wordpress_content") ) {
					$default = "";
					printMessage("You specified a php statement as the default value for <code>$tablefield</code>. That feature is disabled in this demo.","red");
				} else {
					$default=substr($default,1);
					$selected = stripslashes($selected);
					$selected = str_replace('"','\"',$selected);
					if (substr($selected,-1)!=";") $selected .= ";";
					if (strstr($selected,"=")) {
						eval ("\$selected = \"$selected\"; ");
					} else {
						eval ("\$selected = $selected");
					}
				}
				//echo "selected is - " . $selected . "<br>";
				$selected_rows = $db->get_results($selected);
				//print_r($selected_rows); echo "<br />\n";
				for ($srcnt=1; $srcnt<=count($selected_rows); $srcnt++) {		
					$$tablefield .= $selected_rows[$srcnt-1]->$option_value . ",";
				}
				$$tablefield = substr($$tablefield,0,strlen($$tablefield)-1);
				//echo "tablefield value is " . $$tablefield . "<br>";
				if ($$tablefield) $$tablefield = explode(",",$$tablefield);
			} else {
				if (!$editing) $$tablefield = $selected;
			}

			// --- of course, if we are submitting, let's just use the POSTed values...
			if ($_POST[$tablefield]) $$tablefield = $_POST[$tablefield];
			//print_r($$tablefield);
			// ----------------------------------------------------------------------------------------------------------------------------


			// --------------------------------------------------------------------------------------------------
			// --- now go through the different types of form elements & implement...
			// --------------------------------------------------------------------------------------------------
			switch ($value) {
			  case "checkbox":
				checkbox($tablefield, $populatestr, $option_value, $option_display, $$tablefield, $event, $align);
				break;
			  case "datefield":
				datefield($tablefield);
				break;
			  case "file_upload":
			  	$temp = $$tablefield;
			  	if (is_array($$tablefield)) {
					$default = $temp[0];
				} else {
					$default = $temp;
				}
			  	file_upload($tablefield, $default, $size, $maxlen, $displayfile);
				break;
			  case "file_upload_ajax_single":
				$file_upload_ajax_type = "single";
			  	$default = $$tablefield;
				file_upload_ajax($tablefield, $default, $files_directory, $size, $displayfile, $align);
				break;
			  case "file_upload_ajax":
			  	$default = $$tablefield;
				file_upload_ajax($tablefield, $default, $files_directory, $size, $displayfile, $align);
				break;
			  case "hidden":
				if (!isset($default)) $default = $$tablefield;
			  	hidden($tablefield, $default);
				break;
			  case "password":
			  	if ($default && !$editing) $$tablefield = $default;
			  	password($tablefield, $$tablefield, $pwconfirm, $size, $maxlen);
				break;
			  case "passwordmd5":
			  	$md5 = true;
			  	if ($default && !$editing) $$tablefield = $default;
			  	password($tablefield, $$tablefield, $pwconfirm, $size, $maxlen);
				break;
			  case "radio":
				radio($tablefield, $populatestr, $option_value, $option_display, $$tablefield, $event, $align);
				break;
			  case "selectbox":
				selectbox($tablefield, $populatestr, $size, $option_value, $option_display, $$tablefield, $event);
				break;
			  case "selectbox_other":
				selectbox_other($tablefield, $populatestr, $size, $option_value, $option_display, $$tablefield, $align, $event);
				break;
			  case "selectbox_multiple":
			  case "selectbox_multirow":
				selectbox_multiple($tablefield, $populatestr, $size, $option_value, $option_display, $$tablefield, $event);
				break;
			  case "selectbox_multiple_other":
			  case "selectbox_multirow_other":
				selectbox_multiple_other($tablefield, $populatestr, $size, $option_value, $option_display, $$tablefield, $align, $event);
				break;
			  case "textarea":
			  	if ($default && !$editing) $$tablefield = $default;
				textarea($tablefield, $$tablefield, $size, $cols);
				break;
			  case "textarea_FCKedit":
			  	if ($default && !$editing) $$tablefield = $default;
				textarea_FCKedit($tablefield, $$tablefield, $size, $cols, $fckedit_toolbar);
				break;
			  case "url":
			  case "textbox":
			  	if ($default && !$editing) $$tablefield = $default;
				textbox($tablefield, $$tablefield, $size, $maxlen, $event);
				break;
			  case "textbox_noedit":
			  	if ($default && !$editing) $$tablefield = $default;
				textbox_noedit($tablefield, $$tablefield, $size, $maxlen, $event);
				break;
			}
		}
		if ($desc1 && $desc1_location=="right") echo " <span class=\"$text_class\">" . $desc1 . "</span>\n";
		if ($desc2 && $desc2_location=="right") echo " <span class=\"$text_class\">" . $desc2 . "</span>\n";
		if ($desc1 && $desc1_location=="bottom") echo "<br /><span class=\"$text_class\">" . $desc1 . "</span><br />\n";
		if ($desc2 && $desc2_location=="bottom") echo "<br /><span class=\"$text_class\">" . $desc2 . "</span><br />\n";
		if ($value!="hidden") echo "</fieldset></div>\n";
		echo "</td>\n";
	  }
	
	  //echo $sectionrownum . " - " . $$sectionrownum . "<br />";

	  echo "</tr>\n";
	}
	echo "</table>\n";
}

// --- Now add the submit button and human verification question if appropriate...
echo "</td></tr>\n";
	if ($humanverify=="Y") {
		echo "<tr><td class=\"humanverify-desc\">Please answer the question below to help us prevent spam</td></tr>\n";
		echo "<tr><td class=\"humanverify\">".stripslashes($humanverify_question)." &nbsp; "; textbox("humanverify_response", $humanverify_response, 3, 255); echo "</td></tr>\n";
	}
echo "<tr><td align=\"center\">\n";
if ($pre_submit) echo $pre_submit . "\n";
submit("go",$form_submit_text,"form-submit",$onsubmit_action);
echo "</td></tr>\n";
echo "</table>\n";

// --- Finally, add a powered by phpAddEdit...please don't remove this unless you make a donation
echo "<table>\n";
echo "<tr><td>\n";
echo "<span style=\"font-size:10px; color:green\">powered by <a href=\"http://www.phpAddEdit.com\">phpAddEdit</a> ";
//include "includes/version.inc.php";
echo "</span>\n";
echo "</td></tr>\n";
echo "</table>\n";

// -------------------------------------------------------------------------------------------
// ----- Now handle hidden fields for error checking...
// -------------------------------------------------------------------------------------------
$error_check_fields = array();
$error_check_english = array();
foreach($tables as $index=>$tablename) {
	foreach($fields[$tablename] as $key=>$field) {
		$tablefield = $tablename."_".$field;
		$temp = $$tablefield = get_variables($tablefield." "," || ");
		//print_r($temp);

		$errorcheck = get_variable(FORM_VARIABLES,$tablefield." ","errorcheck","=>","||");
		if (substr($errorcheck,0,1)==";") $errorcheck = substr($errorcheck,1);		// --- get rid of initial ; which is caused if required not selected...
		//echo "errorcheck is " . $errorcheck . "<br>";
		if (stristr($errorcheck,";")) {
			$errorcheck_array = explode(";",$errorcheck);
		} else {
			$errorcheck_array = array($errorcheck);
		}
		$error_check_fields[$tablefield] = $errorcheck_array;
		
		$english = get_variable(FORM_VARIABLES,$tablefield." ","english","=>","||");
		//echo "english is " . $english . "<br>";
		$error_check_english = $error_check_english + array($tablefield=>$english);
	}
}

if ($error_check_fields && is_array($error_check_fields)) {
	//print_r($error_check_fields);
	foreach($error_check_fields as $elementname=>$elementarray) {
		foreach($elementarray as $index=>$value) {
			printf("<input type='hidden' name='error_check_fields[$elementname][$index]' value='%s' />\n",$value);
		}
	}
}
if ($error_check_english && is_array($error_check_english)) {
	//print_r($error_check_english);
	foreach($error_check_english as $index=>$value) {
		printf("<input type='hidden' name='error_check_english[$index]' value='%s' />\n",$value);
	}
}

echo "</form>\n";
?>