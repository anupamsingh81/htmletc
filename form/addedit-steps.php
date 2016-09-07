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
// === Define any variables that we might share across multiple functions...
$form_elements = array(""=>"","checkbox"=>"checkbox","datefield"=>"date field","hidden"=>"hidden",
	"file_upload"=>"File Upload","file_upload_ajax"=>"File Upload (AJAX)","file_upload_ajax_single"=>"File Upload (AJAX Single)",
	"password"=>"password","passwordmd5"=>"password (MD5)","radio"=>"radio button","selectbox"=>"selectbox",
	"selectbox_other"=>"selectbox other","selectbox_multiple"=>"selectbox (multiple)",
	"selectbox_multiple_other"=>"selectbox (multiple) other","selectbox_multirow"=>"selectbox (multiple row)",
	"selectbox_multirow_other"=>"selectbox (multiple row) other",
	"textbox"=>"textbox","textbox_noedit"=>"textbox (not editable)",
	"textarea"=>"textarea","textarea_FCKedit"=>"textarea (FCKedit)");
	//$select_event = sprintf("onchange=\"javascript:alert('hello')\"");


// =======================================================================================================
// === STEP 0 - Setup the config files (DB info)...
// =======================================================================================================
function step0HTML() {
	global $dbhost, $dbname, $dbuser, $dbpass, $first_page;
	if (!$dbhost) $dbhost = "localhost";
	if ($first_page) printMessage("Enter Your Database Connection Details Below. Do this once and you won't have to again...","","","");
	echo "<table>\n";
	echo "<tr>\n";
	echo "<td>Database host (sometimes 'localhost') </td>\n";
	echo "<td>"; textbox("dbhost", $dbhost); echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>Database name (e.g., 'mydb') </td>\n";
	echo "<td>"; textbox("dbname", $dbname); echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>Database user (e.g., 'root') </td>\n";
	echo "<td>"; textbox("dbuser", $dbuser); echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td valign=top>Database password </td>\n";
	echo "<td>"; password("dbpass","", "N"); echo "</td>\n";
	echo "</tr>\n";

	echo "<tr><td colspan=\"2\"><br /></td></tr>\n";

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td></td><td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 1\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 1 - Either choose an existing form to edit or enter a new form (and # of tables) to build...
// =======================================================================================================
function step1HTML() {
	global $numtables, $error_check_fields, $error_check, $error_check_english;

	printMessage("STEP 1: Create <strong>or</strong> edit a form and specify # of tables to use","","bold");
	echo "<br />";

	echo "<table>\n";
	$list = array(""=>"");
	$start_dir = dirname(__FILE__)."/forms"; 
	$filelist = array(""=>"");
	$dir = opendir($start_dir); 
	while ($f = readdir($dir)) { 
		//echo "file:$f<br>\n"; 
		//if (eregi("\_variables",$f)){ #if filename matches .txt in the name	- fixed eregi deprecation 09-29-2011 - JB 
		if (preg_match("/\_variables/",$f)){ #if filename matches .txt in the name
			$form = substr($f, 0, strrpos($f,"_variables"));
			$filelist = $filelist + array($form=>$form);
		}
	}
	asort($filelist);

	if (count($filelist)>1) {
		echo "<tr>\n";
		echo "<td>Form to edit: </td><td>"; selectbox("editform", $filelist, "1", "", "", $_GET["editform"]); echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><strong>OR</strong></td><td></td>\n";
		echo "</tr>\n";
	} else {
		echo "<tr>\n";
		echo "<td>Form to edit: </td><td>"; textbox("editform","","12","50",""); echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><strong>OR</strong></td><td></td>\n";
		echo "</tr>\n";
	}
	echo "<tr>\n";
	echo "<td>create a new form: </td><td>"; textbox("formname","","12","50",$event); echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td align=\"center\" colspan=\"2\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 2\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 2 - Set security options...
// =======================================================================================================
function step2HTML() {
	global $numtables, $formname, $editform, $error_check_fields, $error_check, $error_check_english;

	// ---------------------------------------------------------------------------------
	// --- Create the variables and form files we'll rely on through the steps
	// --- OR get info from variables file if editing...
	// ---------------------------------------------------------------------------------

	if (STATUS=="edit") {
		$temp = get_variables("primarykey "," || "); if (!$primarykey) $primarykey = $temp[0];
		$temp = get_variables("numtables "," || "); if (!$numtables) $numtables = $temp[0];
		$temp = get_variables("directenable "," || "); if (!$directenable) $directenable = $temp[0];
		$temp = get_variables("addenable "," || "); if (!$addenable) $addenable = $temp[0];
		$temp = get_variables("editenable "," || "); if (!$editenable) $editenable = $temp[0];
		$temp = get_variables("addcookie "," || "); if (!$addcookie) $addcookie = $temp[0];
		$temp = get_variables("addcookie_value"," || "); if (!$addcookie_value) $addcookie_value = $temp[0];
		$temp = get_variables("editcookie "," || "); if (!$editcookie) $editcookie = $temp[0];
		$temp = get_variables("editcookie_value"," || "); if (!$editcookie_value) $editcookie_value = $temp[0];
	} else {
		if (is_readable(FORM_NAME)) {
			printMessage("You have specified a form name that is not allowed or already exists","red");
			exit;
		}

		$header_name = "forms/".$formname."-header.inc.php";
		$footer_name = "forms/".$formname."-footer.inc.php";

		write_file(FORM_VARIABLES,"");
		chmod(FORM_VARIABLES,0755);
		write_file(FORM_NAME,"");
		chmod(FORM_NAME,0755);
		write_file(FORM_EMAIL1,"");
		chmod(FORM_EMAIL1,0755);
		write_file(FORM_EMAIL2,"");
		chmod(FORM_EMAIL2,0755);
		write_file($header_name,"");
		chmod($header_name,0755);
		write_file($footer_name,"");
		chmod($footer_name,0755);

		$temp = "<?php
";
		$temp .= "include (\"../addedit-render.php\");
";
		$temp .= "?>
";
		append_file(FORM_NAME,$temp);
	}

	printMessage("STEP 2: Select # of Tables and Define Your Form's Security Settings","","bold");
	echo "<br />";

	$message = "How many tables do you want this form to manipulate?";
	printMessage($message,"","");
	echo "<table>\n";
		echo "<tr>\n";
		echo "<td> # of tables to use: </td><td>"; textbox("numtables",$numtables,"2","2"); echo "</td>\n";
		echo "</tr>\n";
	echo "</table>\n";

	echo "<br />";
	$message = "You will be able to access your form in two ways: calling the form script directly (<code>http://".getenv('HTTP_HOST')."/".FORM_NAME."</code>) or including the form in another PHP page. Doing the former could be a security problem - if you don't add extra security measures anyone could use the form. You can fix this yourself by password-protecting the file or directory. Alternatively, you can use the following options. The first option is to disallow direct use of the form. The other option is to validate users by specifying cookie names; the script will then check that the cookie exists and, if a value or values are specified, it will check to make sure that value exists in the cookie value before allowing use of the form (in other words, if all you want to do is make sure a cookie is set, leave the value field empty).";
	printMessage($message,"","");
	echo "<table>\n";
	echo "<tr>\n";
	echo "<td>Enable Direct Calling of Script: </td>\n";
		if (!$directenable) $directenable="Yes";
	echo "<td>"; radio("directenable", "Yes=>Yes,No=>No", "", "", $directenable, "", "horizontal"); echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>Add Cookie Name: </td>\n";
	echo "<td>"; textbox("addcookie",$addcookie,"12","50",$event); 
	echo " &nbsp; Value(s): "; textbox("addcookie_value",$addcookie_value,"12","50",$event); 
	echo " &nbsp; (to specify multiple cookie values, separate by a comma)</td></tr>\n";
	echo "<tr>\n";
	echo "<td>Edit Cookie Name: </td>\n";
	echo "<td>"; textbox("editcookie",$editcookie,"12","50",$event); 
	echo " &nbsp; Value(s): "; textbox("editcookie_value",$editcookie_value,"12","50",$event); 
	echo " &nbsp; (to specify multiple cookie values, separate by a comma)</td></tr>\n";
	echo "</table>\n";

	echo "<br />";
	$message = "Your form can both add and edit content (thus the name). Above you had the option to not allow direct access to the form script, but you may want to allow direct access for only adding or only editing. If so, specify that below.";
	printMessage($message,"","");
	echo "<table>\n";
	echo "<tr>\n";
	echo "<td>Enable Add: </td>\n";
		if (!$addenable) $addenable="Yes";
		if (!$editenable) $editenable="Yes";
	echo "<td>"; radio("addenable", "Yes=>Yes,No=>No", "", "", $addenable, "", "horizontal"); echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>Enable Edit: </td>\n";
	echo "<td>"; radio("editenable", "Yes=>Yes,No=>No", "", "", $editenable, "", "horizontal"); echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td align=\"center\" colspan=\"2\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 3\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 3 - Select the tables to build the form around...
// =======================================================================================================
function step3HTML() {
	global $db, $formname, $numtables, $tables, $error_check_fields, $error_check, $error_check_english, $addeditdir;

	// ---------------------------------------------------------------------------------
	// --- Create the variables and form files we'll rely on through the steps
	// --- OR get info from variables file if editing...
	// ---------------------------------------------------------------------------------
	if (STATUS=="edit") {
		$selectedtables = get_variables("tables");
		//echo "tables are: "; print_r($selectedtables); echo "<br>";
		if ($selectedtables) {
			$numtables = count($selectedtables);
			if ($numtables>0) {
				foreach($selectedtables as $index=>$tablename) {
					$temp = $tablename."_primarykey";
					$temp2 = get_variables($temp," || ");
					$primarykey[$index+1] = $temp2[0];
				}
			}
		}
	}

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {
		$numtables = "numtables || " . stripslashes($_POST["numtables"]) . "
";
		$addenable = "addenable || " . stripslashes($_POST["addenable"]) . "
";
		$addcookie = "addcookie || " . stripslashes($_POST["addcookie"]) . "
";
		$addcookie_value = "addcookie_value || " . stripslashes($_POST["addcookie_value"]) . "
";
		$editenable = "editenable || " . stripslashes($_POST["editenable"]) . "
";
		$directenable = "directenable || " . stripslashes($_POST["directenable"]) . "
";
		$editcookie = "editcookie || " . stripslashes($_POST["editcookie"]) . "
";
		$editcookie_value = "editcookie_value || " . stripslashes($_POST["editcookie_value"]) . "
";
		if (!replace_line(FORM_VARIABLES,"numtables",$numtables)) append_file(FORM_VARIABLES,$numtables);
		if (!replace_line(FORM_VARIABLES,"addenable",$addenable)) append_file(FORM_VARIABLES,$addenable);
		if (!replace_line(FORM_VARIABLES,"editenable",$editenable)) append_file(FORM_VARIABLES,$editenable);
		if (!replace_line(FORM_VARIABLES,"directenable",$directenable)) append_file(FORM_VARIABLES,$directenable);
		if (!replace_line(FORM_VARIABLES,"addcookie ",$addcookie)) append_file(FORM_VARIABLES,$addcookie);
		if (!replace_line(FORM_VARIABLES,"addcookie_value",$addcookie_value)) append_file(FORM_VARIABLES,$addcookie_value);
		if (!replace_line(FORM_VARIABLES,"editcookie ",$editcookie)) append_file(FORM_VARIABLES,$editcookie);
		if (!replace_line(FORM_VARIABLES,"editcookie_value",$editcookie_value)) append_file(FORM_VARIABLES,$editcookie_value);
	}
	// need to lookup numtables again since when we wrote it to the variables file above it was a string "numtables || x"
	$temp = get_variables("numtables "," || "); $numtables = $temp[0];
	//echo $numtables;


	printMessage("STEP 3: Next select the table(s) to use and specify the variable that will be used to select a record when using the form to edit entries","","bold");
	echo "<table>\n";

	$primarykey_help_html = "This is typically the main database table field name (e.g., <code>id</code>) that will be used to select the relevant row to edit.<br /><strong>NOTE</strong>: Typically you will be choosing a unique field but if you are using mapping tables (e.g., to indicate a relationship between two tables) then you will likely be selecting the variable that connects the two tables being mapped.";
		echo "<div id=\"primarykey_help\" class=\"hide\">" . $primarykey_help_html . "</div>\n";

	$table = array();
	for ($i=1; $i<=$numtables; $i++) {
		echo "<tr>";
		echo "<td>Table $i: </td>";
		echo "<td><select name=\"table[$i]\" size=\"1\">\n";

		$tables = $db->get_tables(DB_NAME);
		foreach($tables as $index=>$tablename) {
			//echo $tablename . "<br />";
			$db->$tablename = $tablename;
			($tablename==$selectedtables[$i-1]) ? $selected = "selected=\"selected\"" : $selected="";
			echo "<option value = \"$tablename\" $selected>$tablename</option>\n";
		}

		echo "</select></td>";

		echo "<td> &nbsp; &nbsp; <a class=\"jt_sticky\" href=\"#\" rel=\"#primarykey_help\" title=\"Helpful Info\">Edit Field <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a>: \n";
		textbox("primarykey[$i]",$primarykey[$i],"12","50"); echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td></td><td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 4\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 4 - Choose which fields in each tables will be used...
// =======================================================================================================
function step4HTML() {
	global $primarykey, $error_check_fields, $error_check, $error_check_english, $addeditdir;
	$table = $_POST["table"];
	$primarykey = $_POST["primarykey"];

	// ---------------------------------------------------------------------------------
	// --- First, update the variables file with the variables selected in the prior step...
	// --- unless variables from prior step don't exist which means a tab was selected...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $table && $_POST["continue"]) {
		$tables = "tables ";
		foreach($table as $index=>$tablename) {
			$tables .= $tablename . " ";
		}
		foreach($primarykey as $index=>$variable) {
			$primarykey[$index] = $table[$index] . "_primarykey || " . $primarykey[$index] . "
";
		}
		$tables = trim($tables) . "
";
		// update or write the variables file 
		if (!replace_line(FORM_VARIABLES,"tables ",$tables)) append_file(FORM_VARIABLES,$tables);
		foreach($primarykey as $index=>$variable) {
			if (!replace_line(FORM_VARIABLES,$table[$index]."_primarykey",$primarykey[$index])) append_file(FORM_VARIABLES,$primarykey[$index]);
		}
	}
	// ---------------------------------------------------------------------------------

	$fieldname_help_html = "This is the name of the field (column) of the table you have selected.";
		echo "<div id=\"fieldname_help\" class=\"hide\">" . $fieldname_help_html . "</div>\n";
	$fieldtype_help_html = "This is just provided to remind you of the type of field you are dealing with which may help you decide which form element is appropriate.";
		echo "<div id=\"fieldtype_help\" class=\"hide\">" . $fieldtype_help_html . "</div>\n";
	$include_help_html = "Do you want a form element for this field or not? If you don't select this option any other options you fill out on the same row will just be ignored.";
		echo "<div id=\"include_help\" class=\"hide\">" . $include_help_html . "</div>\n";
	$index_help_html = "On the top menu is an item titled 'index' which displays all the current entries in the database along with a quick link to edit that entry using the current form. Use this checkbox to specify whether or not to include this field in that index display.";
		echo "<div id=\"index_help\" class=\"hide\">" . $index_help_html . "</div>\n";

	/* Here are the field info types that can be found using the - ref: http://us2.php.net/mysql_fetch_field
		blob; max_length; multiple_key; name; not_null; numeric; primary_key; table; type; default; unique_key; unsigned; zerofill
	*/

	printMessage("STEP 4: Next select the field(s) to use.","","bold");
	if (!$table) $table = get_variables("tables");		// Need this in case a tab was selected rather than a form submit...
	foreach($table as $index=>$tablename) {
		if (STATUS=="edit") {
			// get previously selected fields from variables file...
			$selectedfields = get_variables($tablename."_fields");
			//print_r($selectedfields);
		}

		printMessage("Table: <code>$tablename</code>  [click on any heading item to get a description]","","highlight");

		echo "<table style='width:60%'>\n";
		echo "<tr class='headingrow'>";
		echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#fieldname_help\" title=\"Helpful Info\">Field Name <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
		echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#fieldtype_help\" title=\"Helpful Info\">Field Type <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
		echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#include_help\" title=\"Helpful Info\">Include? <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
		echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#index_help\" title=\"Helpful Info\">Index? <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
		echo "</tr>\n";
		
		$query = "select * from $tablename";
		$result = mysql_query($query);
		if (!$result) die('Query failed: ' . mysql_error());
		/* get column metadata */
		$i = 0;
		while ($i < mysql_num_fields($result)) {
		  $meta = mysql_fetch_field($result, $i);
		  (@in_array($meta->name,$selectedfields)) ? $include_checked="1" : $include_checked="0";

		  $temp = get_variables($tablename."_index_fields");
		  (@in_array($meta->name,$temp)) ? $index_checked="1" : $index_checked="0";

		  if (!$meta) echo "No field information available<br />\n";
			echo "<tr>\n";
			echo "<td>"; textbox_noedit($tablename."_".$meta->name, $meta->name, 15, 50); echo "</td>\n";
			echo "<td>"; text($meta->type); echo "</td>\n";
			echo "<td>"; checkbox($tablename."_".$meta->name."_"."include","1=>","","",$include_checked,"","horizontal"); echo "</td>\n";
			echo "<td>"; checkbox($tablename."_".$meta->name."_"."index","1=>","","",$index_checked,"horizontal"); echo "</td>\n";
			echo "</tr>\n";
		  $i++;
		}
		mysql_free_result($result);

		echo "</table>\n";
	}

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 5\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 5 - Assign form elements to selected fields for each table being used...
// =======================================================================================================
function step5HTML() {
	global $form_elements, $error_check_fields, $error_check, $error_check_english, $addeditdir;
	$temp = array();
	$error_check_fields = array();
	$error_check_english = array();

	// --- Get tables info from variables file...
	$tables = get_variables("tables");

	// ---------------------------------------------------------------------------------
	// --- First, get fields from the variables file (IF we aren't updating)...
	// ---------------------------------------------------------------------------------
	if (($error_check || STATUS=="edit") && !$_POST["continue"]) {
		foreach($tables as $index=>$tablename) {
			$fields[$tablename] = get_variables($tablename."_fields");
		}
		/* echo "fields are: "; print_r($fields); echo "<br>"; */
	} 

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {
		$newlines = array();
		// if fields didn't exist already in variables file...
		// need to loop through tables for each $_POST (inefficient) b/c a table might have an underline which screws things up...
		foreach($_POST as $varname=>$varvalue) {
			//echo $varname . " - " . $varvalue . "<br />";
			if ( (substr($varname,-8)=="_include" && $varvalue==1) || $error_check ) {
				foreach($tables as $index=>$tablename) {
					if ( substr($varname,0,strlen($tablename)+1)==$tablename."_" ) {
						$tablefield = substr($varname, 0, strrpos($varname,"_"));
						$fieldname = substr( $tablefield, strlen($tablename)+1 );
						$fields[$tablename][] = $fieldname;
						//echo $tablename . " - " . $fieldname . "<br>";
					}
				}
			}
			if ( (substr($varname,-6)=="_index" && $varvalue==1) || $error_check ) {
				foreach($tables as $index=>$tablename) {
					if ( substr($varname,0,strlen($tablename)+1)==$tablename."_" ) {
						$tablefield = substr($varname, 0, strrpos($varname,"_"));
						$fieldname = substr( $tablefield, strlen($tablename)+1 );
						$index_fields[$tablename][] = $fieldname;
						//echo $tablename . " - " . $fieldname . "<br>";
					}
				}
			}
		}
		/* echo "fields are: "; print_r($fields); echo "<br>"; */

		for ($i=1; $i<=count($tables); $i++) {
			$tablename = trim($tables[$i-1]);
			//echo "tablename: $tablename <br>";
			$include_fields = $tablename . "_fields ";
			//print_r($fields[$tablename]); 
			//echo "<br>";
			foreach($fields[$tablename] as $index=>$value) {
				$include_fields .= $value . " ";
			}
			$include_fields = trim($include_fields) . "
";
			//echo "include_fields are " . $include_fields . "<br>";
			// update or write the variables file 
			if (!replace_line(FORM_VARIABLES,$tablename."_fields",$include_fields)) append_file(FORM_VARIABLES,$include_fields);

			//print_r($index_fields[$tablename]); 
			//echo "<br>";
			if (count($index_fields[$tablename])>0) {
				$index_field = $tablename . "_index_fields ";
				foreach($index_fields[$tablename] as $index=>$value) {
					$index_field .= $value . " ";
				}
				$index_field = trim($index_field) . "
";
				//echo "index fields - " . $index_field . "<br>";
				// update or write the variables file 
				if (!replace_line(FORM_VARIABLES,$tablename."_index_fields",$index_field)) append_file(FORM_VARIABLES,$index_field);
			}
		}

	}


	// ---------------------------------------------------------------------------------
	// --- Now get default values for this page if exist in variables file...
	// ---------------------------------------------------------------------------------
	foreach($tables as $index=>$tablename) {
		// echo "fields are: "; print_r($fields); echo "<br>"; /* */
		foreach($fields[$tablename] as $key=>$field) {
			$tablefield = $tablename."_".$field;
			$$tablefield = get_variables($tablefield." "," || ");
			/* echo "$tablefield is "; print_r($$tablefield); echo "<br /><br />"; */

			$errorcheck = get_variable(FORM_VARIABLES,$tablefield." ","errorcheck","=>"," ||");
			$errorcheck_array = explode(";",$errorcheck);
			//print_r($errorcheck_array);
			$minchars = ""; 
			for ($i=0; $i<count($errorcheck_array); $i++) {
				$temp = substr($errorcheck_array[$i], strpos($errorcheck_array[$i],"=>")+2);
				//echo "temp is " . $temp . "<br>";
				if (stristr($errorcheck_array[$i],"required")) {
					$_POST[$tablefield."_errorcheck_required"] = "1";
					$_POST[$tablefield."_required_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"unique")) {
					$_POST[$tablefield."_errorcheck_unique"] = "1";
					$_POST[$tablefield."_unique_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"minchars")) {
					$_POST[$tablefield."_errorcheck_minchars"] = "1";
					$_POST[$tablefield."_minchars_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"maxchars")) {
					$_POST[$tablefield."_errorcheck_maxchars"] = "1";
					$_POST[$tablefield."_maxchars_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"minvalue")) {
					$_POST[$tablefield."_errorcheck_minvalue"] = "1";
					$_POST[$tablefield."_minvalue_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"maxvalue")) {
					$_POST[$tablefield."_errorcheck_maxvalue"] = "1";
					$_POST[$tablefield."_maxvalue_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"equal")) {
					$_POST[$tablefield."_errorcheck_equal"] = "1";
					$_POST[$tablefield."_equal)constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"notequal")) {
					$_POST[$tablefield."_errorcheck_notequal"] = "1";
					$_POST[$tablefield."_notequal_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"date")) {
					$_POST[$tablefield."_errorcheck_date"] = "1";
					$_POST[$tablefield."_date_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"password")) {
					$_POST[$tablefield."_errorcheck_password"] = "1";
					$_POST[$tablefield."_password_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"url")) {
					$_POST[$tablefield."_errorcheck_url"] = "1";
					$_POST[$tablefield."_url_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"filesize")) {
					$_POST[$tablefield."_errorcheck_filesize"] = "1";
					$_POST[$tablefield."_filesize_constraint"] = $temp;
				}
				if (stristr($errorcheck_array[$i],"email")) {
					$_POST[$tablefield."_errorcheck_email"] = "1";
					$_POST[$tablefield."_email_constraint"] = $temp;
					//echo $temp . " - " . $_POST[$tablefield."_email_constraint"] . "<br>";
				}
				//if (stristr($errorcheck_array[$i],"")) {
				//	$_POST[$tablefield."_errorcheck_"] = "1";
				//	$_POST[$tablefield."_constraint"] = $temp;
				//}
			}
			

			if ($$tablefield) {
				foreach($$tablefield as $num=>$elementvalue) {
					//echo $num . " - " . $elementvalue . "<br>";
					if (stristr($elementvalue,"element=>")) $_POST[$tablefield."_element"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
					if (stristr($elementvalue,"desc1=>")) $_POST[$tablefield."_desc1"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
					if (stristr($elementvalue,"desc2=>")) $_POST[$tablefield."_desc2"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
					if (stristr($elementvalue,"english=>")) $_POST[$tablefield."_english"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
					if (stristr($elementvalue,"event=>")) $_POST[$tablefield."_event"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				} 
			}
		}
	}
	// ---------------------------------------------------------------------------------

	printMessage("STEP 5: Now let's define some options that apply to any kind of element form...","","bold");
	echo "<br />";
	
	$elementname_help_html = "This is the name of the form element that will be used in the final form.";
		echo "<div id=\"elementname_help\" class=\"hide\">" . $elementname_help_html . "</div>\n";
	$element_help_html = "Use the drop-down select box to specify what type of form element you want to use. Most of the choices are obvious but some are unique and require explanation: <br /><br /><strong>selectbox (other)</strong> - a regular select box but with a textbox beside it to add an option if it doesn't exist; If the select box is populated with values from a database table, the added option value will automatically be added to that table; Since a basic selectbox only allows one selection, the textbox value will replace anything that may be otherwise selected<br /><br /><strong>selectbox (multiple) vs. selectbox (multiple row)</strong> - these are essentially the same field types, the difference being that when the form is processed a selectbox (multiple) will produce a single string value that consists of all the selected items separated by a comma whereas a selectbox (multiple row) will actually add a new database entry for each selected item; the 'other' versions of these fields acts like the other for a basic selectbox, adding to a database table if appropriate.<br /><br /><strong>textarea (FCKedit)</strong> - a textarea that uses the open source FCKedit program<br />";
		echo "<div id=\"element_help\" class=\"hide\">" . $element_help_html . "</div>\n";
	$fieldname_help_html = "This is the name of the field (column) of the table you have selected.";
		echo "<div id=\"fieldname_help\" class=\"hide\">" . $fieldname_help_html . "</div>\n";
	$desc_help_html = "This is not required but you can specify up to two different bits of descriptive text to accompany each of your form elements. In a later step we will do a table layout so don't worry about positioning location right now (though typically one might go on a row itself above the form element and another might go to the left of the form element). <br /><br /> You can include HTML code in this field. For example: <br /><br /> <code>&lt;span class='formdescription'&gt;Some Descriptive Text&lt;/span&gt;</code><br />";
		echo "<div id=\"desc_help\" class=\"hide\">" . $desc_help_html . "</div>\n";
	$errorcheck_help_html = "This is not required but it is good practice to do error checking on your form submissions. Choose from the following errror check routines:<br /><br /> ";
	$errorcheck_help_html .= "<strong>required</strong> - form element cannot be empty<br />";
	$errorcheck_help_html .= "<strong>unique</strong> - if adding (not applicable if editing) this error check will make sure the value entered doesn't already exist for that field in the database (good for usernames, emails, etc.)<br />";
	$errorcheck_help_html .= "<strong>minimum chars</strong> - form element must be at least X chars long<br />";
	$errorcheck_help_html .= "<strong>maximum chars</strong> - form element must be X chars long<br />";
	$errorcheck_help_html .= "<strong>minimum value</strong> - must be a number >= X <br />";
	$errorcheck_help_html .= "<strong>maximum value</strong> - must be a number <= X <br />"; 
	$errorcheck_help_html .= "<strong>equal</strong> - must equal X <br />"; 
	$errorcheck_help_html .= "<strong>not equal</strong> - must not equal X <br />"; 
	$errorcheck_help_html .= "<strong>date</strong> - use a basic math expression to ensure a date entered is either equal, greater than or less than a specified date (e.g., <code><=2025-01-01</code>)<br />";
	$errorcheck_help_html .= "<strong>password</strong> - a password field includes a password confirmation box; choosing this error check will ensure both fields are the same <br />"; 
	$errorcheck_help_html .= "<strong>URL</strong> - checks that the entered URL is properly formatted; there is a routine to validate the URL is working, but that is currently disabled b/c of reliability <br />"; 
	$errorcheck_help_html .= "<strong>filesize</strong> - file upload cannot exceed X <br />"; 
	$errorcheck_help_html .= "<strong>email lax</strong> - check that the formatting of an email address is correct <br />"; 
	$errorcheck_help_html .= "<strong>email medium</strong> - check formatting AND check if host exists <br />"; 
	$errorcheck_help_html .= "<strong>email strict</strong> - check formatting, host exists AND user exists (warning: sometimes slow and occasionally produces false failures) <br />"; 
	$errorcheck_help_html .= "<strong>filesize</strong> - file upload cannot exceed X <br /><br />"; 
	$errorcheck_help_html .= "Specify the relevant constraint in the text field next to the errorcheck choice.";
		echo "<div id=\"errorcheck_help\" class=\"hide\">" . $errorcheck_help_html . "</div>\n";
	$errorcheck_constraint_help_html = "Enter the constraint that makes sense for the error check option you selected. <br /><br /> So, for example, if you want to make sure a text field is limited to 100 characters, select the maximum chars option and enter 100. <br /><br /> For filesize, enter the value in bytes with no commas (1000000=1MB) <br /><br /> If you select more than one error check option for a form element, enter the relevant criteria separated by a semicoln (e.g., 10;200;50).";
		echo "<div id=\"errorcheck_constraint_help\" class=\"hide\">" . $errorcheck_constraint_help_html . "</div>\n";
	$english_help_html = "If an error check fails, the general message will be something like 'The field FIELDNAME must...'. You can specify what FIELDNAME will read so it makes sense to the user.";
		echo "<div id=\"english_help\" class=\"hide\">" . $elementname_help_html . "</div>\n";
	$event_help_html = "Use this textbox to enter a javascript event to add to your form element. Events are things like <code>onselect</code>, <code>onclick</code>, <code>onchange</code>, etc. For example:<br /><br /> <code>onclick=javascript:('alert');</code> <br /><br /> Just enter the entire code (dont't surround with quotation marks)<br /><br />";
	$event_help_html .= "For reference, here are the events available: <br /> <ul><li>button: <code>onclick()</code></li> <li>checkbox: <code>onclick()</code></li> <li>select: <code>onblur</code>, <code>onfocus</code>, <code>onchange</code></li> <li>submit: <code>onclick()</code></li> <li>textbox: <code>onblur</code>, <code>onfocus</code>, <code>onchange</code>, <code>onselect</code></li> <li>textarea: <code>onblur</code>, <code>onfocus</code>, <code>onchange</code>, <code>onselect</code></li></ul>";
		echo "<div id=\"event_help\" class=\"hide\">" . $event_help_html . "</div>\n";

	foreach($tables as $index=>$tablename) {
		printMessage("Table: <code>$tablename</code>  [click on any heading item to get a description]","","highlight");

		echo "<table style='width:100%'>\n";
		echo "<tr class='headingrow'>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#elementname_help\" title=\"Helpful Info\">Element Name <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#element_help\" title=\"Helpful Info\">Form Element <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#desc_help\" title=\"Helpful Info\">Descriptive Text (1) <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#desc_help\" title=\"Helpful Info\">Descriptive Text (2) <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#errorcheck_help\" title=\"Helpful Info\">Error Check(s) <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#english_help\" title=\"Helpful Info\">English <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
			echo "<td><a class=\"jt_sticky\" href=\"#\" rel=\"#event_help\" title=\"Helpful Info\">JS event <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td>\n";
		echo "</tr>\n";

		//print_r($fields);
		$tablename = trim($tablename);
		foreach($fields[$tablename] as $fieldindex=>$field) {
			//echo $tablename . " - " . $fieldindex . " - " . $field . "<br>";
			$elementname = $tablename."_".$field;
			$error_check_fields[$elementname."_element"] = array("required"=>"required");
			$error_check_english = array_merge($error_check_english, array($elementname."_element"=>$field." Form Element"));
			echo "<tr>\n";
			echo "<td>"; textbox_noedit($elementname, $elementname, 12, 50); echo "</td>\n";
			echo "<td>"; selectbox($elementname."_element", $form_elements); echo "</td>\n";
			echo "<td>"; textbox($elementname."_desc1", "", 15, 250); echo "</td>\n";
			echo "<td>"; textbox($elementname."_desc2", "", 15, 250); echo "</td>\n";
			echo "<td>"; 
				checkbox($elementname."_errorcheck_required","1=>Required","","","","","horizontal"); echo "<br />\n";
				checkbox($elementname."_errorcheck_unique","1=>Unique","","","","","horizontal"); echo "<br />\n";
				checkbox($elementname."_errorcheck_minchars","1=>Min. Chars ","","","","","horizontal"); textbox($elementname."_minchars_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_maxchars","1=>Max. Chars ","","","","","horizontal"); textbox($elementname."_maxchars_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_minvalue","1=>Min. Value ","","","","","horizontal"); textbox($elementname."_minvalue_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_maxvalue","1=>Max. Value ","","","","","horizontal"); textbox($elementname."_maxvalue_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_equal","1=>Equal ","","","","","horizontal"); textbox($elementname."_equal_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_notequal","1=>Not Equal ","","","","","horizontal"); textbox($elementname."_notequal_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_date","1=>Date ","","","","","horizontal"); textbox($elementname."_date_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				checkbox($elementname."_errorcheck_password","1=>Password ","","","","","horizontal"); echo "<br />\n";
				checkbox($elementname."_errorcheck_url","1=>URL","","","","","horizontal"); echo "<br />\n";
				checkbox($elementname."_errorcheck_filesize","1=>File Size ","","","","","horizontal"); textbox($elementname."_filesize_constraint", "", 8, 50); echo "<a href=\"#\" onclick=\"javascript:alert('$errorcheck_constraint_help')\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a><br />\n";
				echo "Email: "; radio($elementname."_errorcheck_email", "lax=>lax,medium=>std,strict=>strict", "", "", $_POST[$elementname."_email_constraint"], "", "horizontal"); echo "<br />\n";
				echo "<br />\n";
			echo "</td>\n";
			echo "<td>"; textbox($elementname."_english", "", 10, 50); echo "</td>\n";
			echo "<td>"; textbox($elementname."_event", "", 10, 50); echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";

	}

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 6\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 6 - Specify options specific to the form elements chosen in previous step...
// =======================================================================================================
function step6HTML() {
	global $error_check_fields, $error_check, $error_check_english, $addeditdir;
	$error_check_fields = array();

	// ---------------------------------------------------------------------------------
	// --- Get tables and fields info from variables file...
	// ---------------------------------------------------------------------------------
	$tables = get_variables("tables");
	/* echo "tables are: "; print_r($tables); echo "<br>"; */

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	$newlines = array();
	foreach($tables as $index=>$tablename) {
		$fields[$tablename] = get_variables($tablename."_fields");
		foreach($fields[$tablename] as $key=>$field) {
			$line_id_str = $tablename."_".$field;
			$tablefield = $tablename."_".$field;
			$newlines = $tablefield . " || element=>".$_POST[$tablefield."_element"] . " || desc1=>" . $_POST[$tablefield."_desc1"] . " || desc2=>" . $_POST[$tablefield."_desc2"] . " || errorcheck=>";
			// handle all the errorcheck options...
			if ($_POST[$tablefield."_errorcheck_required"]) $newlines .= "required=>".$_POST[$tablefield."_errorcheck_required"];
			if ($_POST[$tablefield."_errorcheck_unique"]) $newlines .= ";unique=>".$_POST[$tablefield."_errorcheck_unique"];
			if ($_POST[$tablefield."_errorcheck_minchars"]) $newlines .= ";minchars=>".$_POST[$tablefield."_minchars_constraint"];
			if ($_POST[$tablefield."_errorcheck_maxchars"]) $newlines .= ";maxchars=>".$_POST[$tablefield."_maxchars_constraint"];
			if ($_POST[$tablefield."_errorcheck_minvalue"]) $newlines .= ";minvalue=>".$_POST[$tablefield."_minvalue_constraint"];
			if ($_POST[$tablefield."_errorcheck_maxvalue"]) $newlines .= ";maxvalue=>".$_POST[$tablefield."_maxvalue_constraint"];
			if ($_POST[$tablefield."_errorcheck_equal"]) $newlines .= ";equal=>".$_POST[$tablefield."_equal_constraint"];
			if ($_POST[$tablefield."_errorcheck_notequal"]) $newlines .= ";notequal=>".$_POST[$tablefield."_notequal_constraint"];
			if ($_POST[$tablefield."_errorcheck_date"]) $newlines .= ";date=>".$_POST[$tablefield."_date_constraint"];
			if ($_POST[$tablefield."_errorcheck_password"]) $newlines .= ";password=>".$_POST[$tablefield."_password_constraint"];
			if ($_POST[$tablefield."_errorcheck_url"]) $newlines .= "url=>".$_POST[$tablefield."_errorcheck_url"];
			if ($_POST[$tablefield."_errorcheck_filesize"]) $newlines .= ";filesize=>".$_POST[$tablefield."_filesize_constraint"];
			if ($_POST[$tablefield."_errorcheck_email"]) $newlines .= ";email=>".$_POST[$tablefield."_errorcheck_email"];
			$newlines .= " || english=>" . $_POST[$tablefield."_english"] . " || event=>" . $_POST[$tablefield."_event"];
			$newlines = stripslashes($newlines);
			//echo "newlines is " . $newlines . "<br><br>";

			if (!$error_check && $_POST["continue"]) {
				$text_exists = line_exists(FORM_VARIABLES,$tablefield." ");
				if ($text_exists) {
					// do a str_replace in case where we are editing a file
					//echo $newlines . "<br>";
					$temp = explode(" || ",$newlines);
					foreach($temp as $x=>$replace_str) {
						$search_str_begin = " " . substr($replace_str,0,strpos($replace_str,"=>"));
						$replace_str = " " . stripslashes($replace_str);
						//echo "<br>$replace_str - $search_str_begin <br>";
						if ($search_str_begin) edit_file(FORM_VARIABLES,$tablefield." ",$search_str_begin," ||",$replace_str);
					}
				} else {
					// append info to the variables file if creating a new form or edit didn't work 
					// append fields for next step (blank of course) - this will help if we need to edit them...
					$newlines .= " || size=> || cols=> || maxlen=> || default=> || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=> ";
					$newlines = trim($newlines) . "
";
					//echo "newlines is " . $newlines . "<br>";
					append_file(FORM_VARIABLES,$newlines);
				}
			}
		}
	}
	// ---------------------------------------------------------------------------------


	printMessage("STEP 6: For each table and field you selected you must now enter certain options specific to that form element","","bold");
	echo "<br />";
	
	$elementname_help_html = "This is the name of the form element that will be used in the final form.";
		echo "<div id=\"elementname_help\" class=\"hide\">" . $elementname_help_html . "</div>\n";
	$size_help_html = "Not all form elements have a size option, but if the one you selected does, use this option. For textarea and selectbox this = rows.";
		echo "<div id=\"size_help\" class=\"hide\">" . $size_help_html . "</div>\n";
	$cols_help_html = "Only a textarea really needs this option. For an FCKedit textarea rows and cols must be specified in pixels. Rows must be at least 200 and Cols must be at least 400.";
		echo "<div id=\"cols_help\" class=\"hide\">" . $cols_help_html . "</div>\n";
	$maxlen_help_html = "Not all form elements have a size option, but if the one you selected does, use this option.";
		echo "<div id=\"maxlen_help\" class=\"hide\">" . $maxlen_help_html . "</div>\n";
	$default_help_html = "If you wish to provide a default value for a field, use this option<br /><br /><strong>Note</strong>: if you want to specify a PHP function make the first character an equals sign, e.g.,<br /><code>=\$_SERVER['REMOTE_ADDR']</code>;";
		echo "<div id=\"default_help\" class=\"hide\">" . $default_help_html . "</div>\n";
	$relID_help_html = "This is the field that is associated with a selectbox_multirow form element - basically in order to add more than one entry to the database we need to know which table field identifies all the entries as common.";
		echo "<div id=\"relID_help\" class=\"hide\">" . $relID_help_html . "</div>\n";
	$selected_help_html = "If you want a checkbox or selectbox selected by default, enter the choice(s) that should be selected here. Separate multiple choices by a semicolon.<br /><br /><strong>Note</strong>: this option is case-sensitive";
		echo "<div id=\"selected_help\" class=\"hide\">" . $selected_help_html . "</div>\n";
	$populate_help_html = "Use this field to specify what content fills a selectbox. There are 2 choices:<br /><ol><li>An array of choices. Format: <code>choice1_value=>choice1_display, choice2_value=>choice2_display</code><br /><strong>Note:</strong> don't surround choices with quotation marks</li><li>An sql query. Format:<br /><code>select * from subjects</code><br /><strong>Note:</strong> if you need a WHERE clause, escape any quote marks (e.g., <code>WHERE user=\&quot;\$user\&quot;</code>)";
		echo "<div id=\"populate_help\" class=\"hide\">" . $populate_help_html . "</div>\n";
	$align_help_html = "This is only used for radio and selectbox_other elements - use it to specify whether to display the radio buttons (or select box and associated 'other' textbox) horizontally or vertically";
		echo "<div id=\"align_help\" class=\"hide\">" . $align_help_html . "</div>\n";
	$filedir_help_html = "For any file upload fields you must specify what directory you want the files to be placed in. This should be a path relative to the base directory (e.g., files/)<br /><br /><strong>NOTE</strong>: DO NOT use a URL (e.g., <code>http://www.domain.com/files/</code>)<br /><strong>NOTE</strong>: DO NOT use an absolute path (e.g., <code>/var/www/vhosts/domain/httpdocs/files/</code>) since the script adds the relative path before the file directory you specify)<br /><br /><strong>Note</strong> also that it does not matter if you use an initial slash or not (e.g., <code>/files/</code> and <code>files/</code> are both acceptable)";
		echo "<div id=\"filedir_help\" class=\"hide\">" . $filedir_help_html . "</div>\n";
	
	$cnt=0;
	foreach($tables as $index=>$tablename) {
		$cnt++;
		printMessage("Table: <code>$tablename</code>  [click on any heading item to get a description]","","highlight");

		$titles_to_show ="";
		echo "<table style='width: 100%'>\n";
		echo "<tr class='headingrow'>\n";
			echo "<td><div id='nameheading".$cnt."'><a class=\"jt_sticky\" href=\"#\" rel=\"#elementname_help\" title=\"Helpful Info\">Element Name <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='typeheading".$cnt."'>Element Type</div></td>\n";
			echo "<td><div id='".$tablename."_sizeheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#size_help\" title=\"Helpful Info\">Size/Rows <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_colsheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#cols_help\" title=\"Helpful Info\">Cols <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_maxlenheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#maxlen_help\" title=\"Helpful Info\">Maxlen <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_defaultheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#default_help\" title=\"Helpful Info\">Default <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_relIDheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#relID_help\" title=\"Helpful Info\">Relative ID <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_selectedheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#selected_help\" title=\"Helpful Info\">Selected <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_populateheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#populate_help\" title=\"Helpful Info\">Populate With... <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_alignheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#align_help\" title=\"Helpful Info\">Alignment <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
			echo "<td><div id='".$tablename."_filedirheading' style='display:none'><a class=\"jt_sticky\" href=\"#\" rel=\"#filedir_help\" title=\"Helpful Info\">File Directory <img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></div></td>\n";
		echo "</tr>\n";

		$i=0;
		foreach($fields[$tablename] as $key=>$field) {
			//print_r($fields); echo "<br>";
			//echo $key . " - " . $field . "<br />";
			$_POST[$tablefield] = $tablefield = $tablename."_".$field;
			//echo "tablefield is " . $tablefield . " - " . $_POST[$tablefield] . "<br>"; 
			$$tablefield = get_variables($tablefield." "," || ");
			//echo "$tablefield is "; print_r($$tablefield); echo "<hr size=1>"; 

			foreach($$tablefield as $num=>$elementvalue) {
				//echo $num . " - " . $elementvalue . "<br>";
				if (stristr($elementvalue,"element=>")) $_POST[$tablefield."_element"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"size=>")) $_POST[$tablefield."_size"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"cols=>")) $_POST[$tablefield."_cols"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"maxlen=>")) $_POST[$tablefield."_maxlen"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"default=>")) $_POST[$tablefield."_default"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"relID=>")) $_POST[$tablefield."_relID"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"selected=>")) $_POST[$tablefield."_selected"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"populatestr=>")) $_POST[$tablefield."_populatestr"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"populatevariables=>")) $_POST[$tablefield."_populatevariables"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"align=>")) $_POST[$tablefield."_align"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				if (stristr($elementvalue,"filedir=>")) $_POST[$tablefield."_filedir"] = substr($elementvalue, strpos($elementvalue,"=>")+2);
				//echo $_POST[$tablefield."_"] . "<br>";
			}

			echo "<tr>\n";
			echo "<td>"; textbox_noedit($tablefield, $tablefield, 20); echo "</td>\n";
			echo "<td>" . $_POST[$tablefield."_element"] . "</td>\n";
			echo "<td><div id='$tablefield"."_size'>"; textbox($tablefield."_size", "", 2, 3); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_cols'>"; textbox($tablefield."_cols", "", 2, 3); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_maxlen'>"; textbox($tablefield."_maxlen", "", 4, 10); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_default' style='display:none;'>"; textbox($tablefield."_default", "", 12, 255); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_relID' style='display:none;'>"; textbox($tablefield."_relID", "", 12, 255); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_selected' style='display:none;'>"; textbox($tablefield."_selected", "", 12, 255); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_populatestr' style='display:none;'>"; textbox($tablefield."_populatestr", "", 20, 500); 
				echo "<br />If SQL, option=>desc<br />";
				textbox($tablefield."_populatevariables", "", 20, 255);
				echo " </div></td>\n";
			$alignbox_list = array(""=>"","horizontal"=>"horizontal","vertical"=>"vertical");
			echo "<td><div id='$tablefield"."_align' style='display:none;'>"; selectbox($tablefield."_align", $alignbox_list, "1", "", "", $_POST[$tablefield."_align"]); echo " </div></td>\n";
			echo "<td><div id='$tablefield"."_filedir' style='display:none;'>"; textbox($tablefield."_filedir", "", 20, 255); echo " </div></td>\n";
			echo "</tr>\n";

			$i++;
			if ($i<count($fields[$tablename])) {
				echo "<tr><td colspan=\"11\"><hr size=\"1\" /></td></tr>\n";
			} else {
				echo "<tr><td colspan=\"11\"><br /></td></tr>\n";
			}

			//echo $_POST[$tablefield."_element"] . "; field is " . $field . "<br>";
			echo "<tr><td colspan=\"11\">\n";
			echo "<script type=\"text/javascript\">\n";
			switch ($_POST[$tablefield."_element"]) {
			  case "datefield":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) hidediv('".$tablefield."_size'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen');  } ); \n";
				break;
			  case "file_upload":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); hidediv('".$tablefield."_populatestr'); showdiv('".$tablefield."_filedir');  } ); \n";
				$titles_to_show .= "sizeheading; filedirheading; ";
				break;
			  case "file_upload_ajax":
			  case "file_upload_ajax_single":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_relID'); showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); showdiv('".$tablefield."_filedir'); } ); \n";
				$titles_to_show .= "sizeheading; relIDheading; selectedheading; populateheading; filedirheading; ";
				break;
			  case "password":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) hidediv('".$tablefield."_cols'); } ); \n";
				break;
			  case "checkbox":
			  case "radio":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_align');  showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_size'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "selectedheading; relIDheading; populateheading; alignheading; ";
				break;
			  case "selectbox":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); showdiv('".$tablefield."_relID'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "selectedheading; relIDheading; populateheading; ";
				break;
			  case "selectbox_other":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_align'); showdiv('".$tablefield."_relID'); showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "alignheading; relIDheading; selectedheading; populateheading; ";
				break;
			  case "selectbox_multiple":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_relID'); showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "sizeheading; relIDheading; selectedheading; populateheading; ";
				break;
			  case "selectbox_multiple_other":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_align'); showdiv('".$tablefield."_relID'); showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "alignheading; relIDheading; sizeheading; selectedheading; populateheading; ";
				break;
			  case "selectbox_multirow":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_relID'); showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "sizeheading; relIDheading; selectedheading; populateheading; ";
				break;
			  case "selectbox_multirow_other":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_align'); showdiv('".$tablefield."_relID'); showdiv('".$tablefield."_selected'); showdiv('".$tablefield."_populatestr'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); } ); \n";
				$titles_to_show .= "alignheading; sizeheading; relIDheading; selectedheading; populateheading; ";
				break;
			  case "textarea":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) hidediv('".$tablefield."_populatestr'); hidediv('".$tablefield."_selected');  } ); \n";
				$titles_to_show .= "sizeheading; colsheading; maxlenheading;";
				break;
			  case "textarea_FCKedit":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) hidediv('".$tablefield."_populatestr'); hidediv('".$tablefield."_selected');  } ); \n";
				$titles_to_show .= "sizeheading; colsheading; ";
				break;
			  case "textbox":
			  case "textbox_noedit":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablefield."_default'); hidediv('".$tablefield."_cols'); } ); \n";
				$titles_to_show .= "sizeheading; maxlenheading; defaultheading; ";
				break;
			  case "hidden":
				echo "addEvent(window, 'load',  function() { if (document.getElementById) hidediv('".$tablefield."_size'); hidediv('".$tablefield."_cols'); hidediv('".$tablefield."_maxlen'); showdiv('".$tablefield."_default'); hidediv('".$tablefield."_selected');  } ); \n";
				$titles_to_show .= "defaultheading; ";
				break;
			}

			// now show the headings if appropriate
			if (stristr($titles_to_show,"sizeheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_sizeheading');  } ); \n";
			if (stristr($titles_to_show,"colsheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_colsheading');  } ); \n";
			if (stristr($titles_to_show,"maxlenheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_maxlenheading');  } ); \n";
			if (stristr($titles_to_show,"defaultheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_defaultheading');  } ); \n";
			if (stristr($titles_to_show,"relIDheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_relIDheading');  } ); \n";
			if (stristr($titles_to_show,"selectedheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_selectedheading');  } ); \n";
			if (stristr($titles_to_show,"populateheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_populateheading');  } ); \n";
			if (stristr($titles_to_show,"alignheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_alignheading');  } ); \n";
			if (stristr($titles_to_show,"filedirheading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_filedirheading');  } ); \n";
			//if (stristr($titles_to_show,"heading")) echo "addEvent(window, 'load',  function() { if (document.getElementById) showdiv('".$tablename."_heading');  } ); \n";

			echo "</script>\n";
			echo "</td></tr>\n";
			//echo $titles_to_show;

		}
		echo "</table>\n";
	}

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 7\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 7 - Specify the number of rows and columns for the table...
// =======================================================================================================
function step7HTML() {
	global $error_check_fields, $error_check, $error_check_english, $addeditdir;
	$error_check_fields = array();

	// ---------------------------------------------------------------------------------
	// --- Get tables and fields info from variables file...
	// ---------------------------------------------------------------------------------
	$tables = get_variables("tables");
	/* echo "tables are: "; print_r($tables); echo "<br>"; */
 	$temp = get_variables("desc1_location"," || "); if (!$desc1_location) $desc1_location = $temp[0];
 	$temp = get_variables("desc2_location"," || "); if (!$desc2_location) $desc2_location = $temp[0];
 	$temp = get_variables("numsections"," || "); if (!$numsections) $numsections = $temp[0];
 	$temp = get_variables("form_width"," || "); if (!$form_width) $form_width = $temp[0];
 	$temp = get_variables("pwhelp"," || "); if (!$pwhelp) $pwhelp = $temp[0];
 	$temp = get_variables("section1numrows"," || "); if (!$section1numrows) $section1numrows = $temp[0];
 	$temp = get_variables("section1numcols"," || "); if (!$section1numcols) $section1numcols = $temp[0];
 	$temp = get_variables("section1title"," || "); if (!$section1title) $section1title = $temp[0];
 	$temp = get_variables("section2numrows"," || "); if (!$section2numrows) $section2numrows = $temp[0];
 	$temp = get_variables("section2numcols"," || "); if (!$section2numcols) $section2numcols = $temp[0];
 	$temp = get_variables("section2title"," || "); if (!$section2title) $section2title = $temp[0];
 	$temp = get_variables("section3numrows"," || "); if (!$section3numrows) $section3numrows = $temp[0];
 	$temp = get_variables("section3numcols"," || "); if (!$section3numcols) $section3numcols = $temp[0];
 	$temp = get_variables("section3title"," || "); if (!$section3title) $section3title = $temp[0];
 	$temp = get_variables("section4numrows"," || "); if (!$section4numrows) $section4numrows = $temp[0];
 	$temp = get_variables("section4numcols"," || "); if (!$section4numcols) $section4numcols = $temp[0];
 	$temp = get_variables("section4title"," || "); if (!$section4title) $section4title = $temp[0];
 	$temp = get_variables("encoding"," || "); if (!$encoding) $encoding = $temp[0];
		if (!$encoding) $encoding = "UTF-8";
 	$temp = get_variables("displayfile"," || "); if (!$displayfile) $displayfile = $temp[0];
 	$temp = get_variables("fckedit_toolbar"," || "); if (!$fckedit_toolbar) $fckedit_toolbar = $temp[0];
 	$temp = get_variables("humanverify "," || "); if (!$humanverify) $humanverify = $temp[0];
 	$temp = get_variables("humanverify_question"," || "); if (!$humanverify_question) $humanverify_question = $temp[0];
 	$temp = get_variables("humanverify_answer"," || "); if (!$humanverify_answer) $humanverify_answer = $temp[0];
		if (!$humanverify) $humanverify = "Y";
		if ($humanverify && !$humanverify_question) $humanverify_question = "What is 2+3?";
		if ($humanverify && !$humanverify_answer) $humanverify_answer = "5";
 	$temp = get_variables("akismet_use "," || "); if (!$akismet_use) $akismet_use = $temp[0];
		if (!$akismet_use) $akismet_use = "N";
 	$temp = get_variables("akismet_key "," || "); if (!$akismet_key) $akismet_key = $temp[0];
 	$temp = get_variables("akismet_fields "," || "); if (!$akismet_fields) $akismet_fields = $temp[0];
	$temp = get_variables("css"," || "); 
	if (!$css && $temp) {
		foreach ($temp as $value) {
			$css .= $value . ";";
		}
		$css = substr($css,0,strlen($css)-1);
	}

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {
		$section1numrows = 0;
		$newlines = array();
		foreach($tables as $index=>$tablename) {
			$fields[$tablename] = get_variables($tablename."_fields");
			//print_r($fields[$tablename]);
			//echo "<br><br>";
			$section1numrows = $section1numrows + count($fields[$tablename]);
			if (!$section1numcols) $section1numcols = 1;
			foreach($fields[$tablename] as $key=>$field) {
				$line_id_str = $tablename."_".$field;
				$tablefield = $tablename."_".$field;
				$tablefield_array = get_variables($tablefield." "," || ");
				/* echo "tablefield array is: "; print_r($tablefield_array); echo "<br>"; */
				$newlines = $tablefield . " || ";
				foreach($tablefield_array as $num=>$value) {
					$newlines .= $value . " || ";
				}
				//$newlines .= "";
				$newlines = substr($newlines,0,strlen($newlines)-4);
				//echo "$newlines <br>";
				
				// do a str_replace in case where we are editing a file
				//echo $newlines . "<br>";
				//echo "<br>";
				$temp = explode(" || ",$newlines);
				//print_r($temp);
				//echo "<br>";
				foreach($temp as $x=>$replace_str) {
					//echo $replace_str . "<br />";
					$search_str_begin = substr($replace_str,0,strpos($replace_str,"=>"));
					$postvar = $tablefield . "_" . $search_str_begin;
					//echo $postvar . "<br />";
					// for variables from previous step, use POST variables, for variables from earlier steps, re-use from variables file
					if (isset($_POST[$postvar])) {
						$replace_str = $search_str_begin . "=>" . stripslashes($_POST[$postvar]);
					} else {
						$replace_str = stripslashes($replace_str);
					}
					// now do the str replacement - but have to handle special case where last variable has no ending ||
					if ($search_str_begin) {
						//echo "$tablefield $search_str_begin - $replace_str <br>";
						edit_file(FORM_VARIABLES,$tablefield." "," ".$search_str_begin."=>"," ||"," ".$replace_str);
					}
				}
			}
		}
	}
	// ---------------------------------------------------------------------------------


	?>
	<script type="text/javascript">
	function showallsections (divname) {
		showdiv(divname+'a'); showdiv(divname+'b'); showdiv(divname+'c'); showdiv(divname+'d'); showdiv(divname+'e'); showdiv(divname+'f'); 
		return true;
	}
	function hideallsections (divname) {
		hidediv(divname+'a'); hidediv(divname+'b'); hidediv(divname+'c'); hidediv(divname+'d'); hidediv(divname+'e'); hidediv(divname+'f'); 
		return true;
	}	
	function sectionsClick (formname,divname) {
		var checked1 = document.generatecode[formname][0].checked;
		var checked2 = document.generatecode[formname][1].checked;
		var checked3 = document.generatecode[formname][2].checked;
		var checked4 = document.generatecode[formname][3].checked;
		if (checked1) {
			hideallsections(divname+'2'); 
			hideallsections(divname+'3'); 
			hideallsections(divname+'4'); 
		}
		if (checked2) {
			showallsections(divname+'2'); 
			hideallsections(divname+'3'); 
			hideallsections(divname+'4'); 
		}
		if (checked3) {
			showallsections(divname+'2'); 
			showallsections(divname+'3'); 
			hideallsections(divname+'4'); 
		}
		if (checked4) {
			showallsections(divname+'2'); 
			showallsections(divname+'3'); 
			showallsections(divname+'4'); 
		}
		return true;
	}
	<?php 
	if ($numsections=="2") echo "addEvent(window, 'load',  function() { showallsections('section2'); } ); \n";
	if ($numsections=="3") echo "addEvent(window, 'load',  function() { showallsections('section2'); showallsections('section3'); } ); \n";
	if ($numsections=="4") echo "addEvent(window, 'load',  function() { showallsections('section2'); showallsections('section3'); showallsections('section4'); } ); \n";
	if (!$numsections) $numsections = 1;
	echo "</script>\n";

	printMessage("STEP 7: The form will use a table layout so let's choose our table options...","","bold");
	echo "<br />";

	printMessage("Earlier you specified two descriptions for each field. Below choose where to place those descriptions (choices: <strong>fieldset</strong>: above the field using fieldset and legend HTML; <strong>top</strong>: above the field; <strong>left</strong>: to the left with all descriptions in one column and all fields in a second column for nice spacing; <strong>right</strong>: to the right.","","");
	$desc_location_list = array("fieldset"=>"fieldset","top"=>"top","bottom"=>"bottom","left"=>"left","right"=>"right");
	if (!$desc1_location) $desc1_location = "fieldset";
	if (!$desc2_location) $desc2_location = "right";
	echo "<table>\n";
	echo "<tr><td>Description 1 Location:</td><td align=\"left\">"; selectbox("desc1_location", $desc_location_list, "1", "", "", $desc1_location); echo "</td></tr>\n";
	echo "<tr><td>Description 2 Location:</td><td align=\"left\">"; selectbox("desc2_location", $desc_location_list, "1", "", "", $desc2_location); echo "</td></tr>\n";
	echo "</table>\n";
	echo "<br />";

	printMessage("If your form includes a password field, you can offer users a help message to describe password guidelines.","","");
	if (!$pwhelp) $pwhelp = "passwords must be a minimum of 6 characters and cannot include any special characters";
	echo "<table>\n";
	echo "<tr><td>Password Help Message:</td><td align=\"left\">"; textbox("pwhelp", $pwhelp, 75, 255); echo "</td></tr>\n";
	echo "</table>\n";
	echo "<br />";

	printMessage("Below you can specify some specialized settings for your form display.","","");
	echo "<table>\n";
	echo "<tr><td>Form Encoding:</td><td align=\"left\">"; textbox("encoding", $encoding, 20, 255); echo "</td></tr>\n";
	echo "<tr><td>Width of form:</td><td align=\"left\">"; textbox("form_width", $form_width, 5, 5); echo " (specify in % or pixels - e.g., 100%, 250px; can leave blank and set width in .formclass in <code>style.css</code>)</td></tr>\n";
	echo "<tr><td>Display image beside file upload field:</td><td>"; radio("displayfile", "Y=> Yes ,N=> No", "", "", $displayfile, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>FCKeditor ToolBarSet name:</td><td align=\"left\">"; textbox("fckedit_toolbar", $fckedit_toolbar, 20, 255); echo " (use this to choose the FCKeditor toolbar items to display. <a href=\"http://wiki.fckeditor.net/Developer%27s_Guide/Configuration/Toolbar\" target=\"_blank\">Click Here</a> for more info.)</td></tr>\n";
	echo "</table>\n";
	echo "<br />";

	printMessage("Spam Prevention Options.","","");
	echo "<table>\n";
	echo "<tr><td>Include human verification?</td><td>"; radio("humanverify", "Y=> Yes ,N=> No", "", "", $humanverify, "", "horizontal"); echo " (use this to prevent spam bots from using your form)</td></tr>\n";
	echo "<tr><td>Question to ask:</td><td align=\"left\">"; textbox("humanverify_question", $humanverify_question, 20, 255); echo "</td></tr>\n";
	echo "<tr><td>Answer expected:</td><td align=\"left\">"; textbox("humanverify_answer", $humanverify_answer, 20, 255); echo "</td></tr>\n";
	echo "<tr><td><br /></td></tr>\n";
	echo "<tr><td>Use Akismet Anti-spam Filter?</td><td>"; radio("akismet_use", "Y=> Yes ,N=> No", "", "", $akismet_use, "", "horizontal"); echo " &nbsp; &nbsp; (for more information, visit the <a href='http://akismet.com/' target='_blank'>Akismet site</a>)</td></tr>\n";
	echo "<tr><td>Your Akismet API Key:</td><td align=\"left\">"; textbox("akismet_key", $akismet_key, 20, 255,"onchange='akismetApiCheck(this.form);'"); echo " &nbsp; (for more information, see <a href='http://en.support.wordpress.com/api-keys/' target='_blank'>how to get your API key</a>\n";

	echo "<br />\n";
	echo "<div style='margin:5px; 0 5px 0;'>status:\n";
	echo '<span id="apicheck" style="color:#999; border:1px dotted #999; padding:2px 5px 2px 5px;">API Key Not Tested Yet</span>';
	printf(" \n<input type=\"button\" value=\"Verify Key\" onclick=\"akismetApiCheck(this.form)\" />\n");
	echo "</div>\n";
	echo "</td></tr>\n";


	
	echo "<tr><td>Fields to Submit to Akismet:</td><td align=\"left\">"; 
		$selectbox_list = array(""=>"");
		foreach($tables as $index=>$tablename) {
			$fields[$tablename] = get_variables($tablename."_fields");
			//print_r($fields[$tablename]); echo "<br />"; 
			foreach($fields[$tablename] as $key=>$field) {
				$tablefield = $tablename."_".$field;
				$selectbox_list = array_merge((array)$selectbox_list, array($tablefield=>$tablefield));
			}
		}
		selectbox_multiple("akismet_fields", $selectbox_list,5,"","",$akismet_fields);
	echo "</td></tr>\n";

	echo "</table>\n";
	echo "<br />";

	printMessage("phpAddEdit offers flexible layout options - namely up to 3 different sections or tables. Please specify the number of sections you want and the number of rows and columns for each below. A default for one section has been specified based on the number of fields specified in previous steps.","");
	echo "<table>\n";

	if (!$form_width) $form_width = "100%";
	$numsections_event = "onclick=\"return sectionsClick ('numsections','section');\"";
	echo "<tr><td>Number of Sections:</td><td>"; radio("numsections", "1=> 1 ,2=> 2 ,3=> 3 ,4=> 4 ", "", "", $numsections, $numsections_event, "horizontal"); echo "</td></tr>\n";

	echo "<tr><td>Number of Rows for Section 1:</td><td align=\"left\">"; textbox("section1numrows", $section1numrows, 2, 3); echo "</td></tr>\n";
	echo "<tr><td>Number of Columns for Section 1:</td><td align=\"left\"><div id='section1d'>"; textbox("section1numcols", $section1numcols, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td>Title for Section 1:</td><td align=\"left\">"; textbox("section1title", $section1title, 50, 255); echo "</td></tr>\n";
	echo "<tr><td><br /></td></tr>";

	echo "<tr><td><div id='section2a' style='display:none;'>Number of Rows for Section 2:</div></td><td align=\"left\"><div id='section2b' style='display:none;'>"; textbox("section2numrows", $section2numrows, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td><div id='section2c' style='display:none;'>Number of Columns for Section 2:</div></td><td align=\"left\"><div id='section2d' style='display:none;'>"; textbox("section2numcols", $section2numcols, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td><div id='section2e' style='display:none;'>Title for Section 2:</div></td><td align=\"left\"><div id='section2f' style='display:none;'>"; textbox("section2title", $section2title, 50, 255); echo "</div></td></tr>\n";
	echo "<tr><td><br /></td></tr>";

	echo "<tr><td><div id='section3a' style='display:none;'>Number of Rows for Section 3:</div></td><td align=\"left\"><div id='section3b' style='display:none;'>"; textbox("section3numrows", $section3numrows, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td><div id='section3c' style='display:none;'>Number of Columns for Section 3:</div></td><td align=\"left\"><div id='section3d' style='display:none;'>"; textbox("section3numcols", $section3numcols, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td><div id='section3e' style='display:none;'>Title for Section 3:</div></td><td align=\"left\"><div id='section3f' style='display:none;'>"; textbox("section3title", $section3title, 50, 255); echo "</div></td></tr>\n";
	echo "<tr><td><br /></td></tr>";

	echo "<tr><td><div id='section4a' style='display:none;'>Number of Rows for Section 4:</div></td><td align=\"left\"><div id='section4b' style='display:none;'>"; textbox("section4numrows", $section4numrows, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td><div id='section4c' style='display:none;'>Number of Columns for Section 4:</div></td><td align=\"left\"><div id='section4d' style='display:none;'>"; textbox("section4numcols", $section4numcols, 2, 3); echo "</div></td></tr>\n";
	echo "<tr><td><div id='section4e' style='display:none;'>Title for Section 4:</div></td><td align=\"left\"><div id='section4f' style='display:none;'>"; textbox("section4title", $section4title, 50, 255); echo "</div></td></tr>\n";
	echo "<tr><td><br /></td></tr>";

	echo "</table>\n";

	echo "<br />";
	printMessage("In the next step you will have the chance to specify specific CSS classes, but first you must specify which .css file(s) to use. By default, the <code>includes/style.css</code> file will be included and it will be easiest to just modify that file, but if you prefer you can specify external file(s) instead (including path(s) relative to the home directory). To specify more than one file, separate by a semicolon. <br /> &nbsp; &nbsp; &nbsp; example: <code>style1.css;style2.css</code> <br /> &nbsp; &nbsp; &nbsp; example: <code>/includes/style1.css;/includes/style2.css</code>","");
	echo "<table>\n";
	echo "<tr>\n";
		echo "<td>CSS File(s):</td><td align=\"left\">"; textbox("css", $css, 25, 50); echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table class='center'>\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 8\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

// =======================================================================================================
// === STEP 8 -  Setup form options and layout
// =======================================================================================================
function step8HTML() {
	global $filename, $error_check_fields, $error_check, $error_check_english, $form_name, $form_title, $form_action, $form_method, $form_enctype, $addeditdir;
	$error_check_fields = array();
	$desc1_location = $_POST["desc1_location"];
	$desc2_location = $_POST["desc2_location"];
	$form_width = $_POST["form_width"]; if (!$form_width) $form_width = " ";
	$pwhelp = $_POST["pwhelp"];
	$numsections = $_POST["numsections"];
	$section1numrows = $_POST["section1numrows"];
	$section1numcols = $_POST["section1numcols"];
	$section1title = $_POST["section1title"];
	$section2numrows = $_POST["section2numrows"];
	$section2numcols = $_POST["section2numcols"];
	$section2title = $_POST["section2title"];
	$section3numrows = $_POST["section3numrows"];
	$section3numcols = $_POST["section3numcols"];
	$section3title = $_POST["section3title"];
	$section4numrows = $_POST["section4numrows"];
	$section4numcols = $_POST["section4numcols"];
	$section4title = $_POST["section4title"];
	$encoding = $_POST["encoding"];
	$humanverify = $_POST["humanverify"];
	$humanverify_question = $_POST["humanverify_question"];
	$humanverify_answer = $_POST["humanverify_answer"];
	$displayfile = $_POST["displayfile"];
	$fckedit_toolbar = $_POST["fckedit_toolbar"];
	$akismet_use = $_POST["akismet_use"];
	$akismet_key = $_POST["akismet_key"];
	$akismet_fields = $_POST["akismet_fields"];
		if ($_POST["akismet_fields"]) $akismet_fields = implode(",",$akismet_fields);
	
	// ---------------------------------------------------------------------------------
	// --- Get relevant info from variables file...
	// ---------------------------------------------------------------------------------
	$tables = get_variables("tables");
	/* echo "tables are: "; print_r($tables); echo "<br>"; */
 	$temp = get_variables("pwhelp"," || "); if (!$pwhelp) $pwhelp = $temp[0];
 	$temp = get_variables("form_width"," || "); if (!$form_width) $form_width = $temp[0];
	$temp = get_variables("numsections"," || "); if (!$numsections) $numsections = $temp[0];
	$temp = get_variables("section1numrows"," || "); if (!$section1numrows) $section1numrows = $temp[0];
	$temp = get_variables("section1numcols"," || "); if (!$section1numcols) $section1numcols = $temp[0];
	$temp = get_variables("section2numrows"," || "); if (!$section2numrows) $section2numrows = $temp[0];
	$temp = get_variables("section2numcols"," || "); if (!$section2numcols) $section2numcols = $temp[0];
	$temp = get_variables("section3numrows"," || "); if (!$section3numrows) $section3numrows = $temp[0];
	$temp = get_variables("section3numcols"," || "); if (!$section3numcols) $section3numcols = $temp[0];
	$temp = get_variables("section4numrows"," || "); if (!$section4numrows) $section4numrows = $temp[0];
	$temp = get_variables("section4numcols"," || "); if (!$section4numcols) $section4numcols = $temp[0];
	$temp = get_variables("form_name"," || "); if (!$form_name) $form_name = $temp[0]; if (!$form_name) $form_name = "addedit";
	$temp = get_variables("form_title "," || "); if (!$form_title) $form_title = $temp[0];
	$temp = get_variables("form_action"," || "); if (!$form_action) $form_action = $temp[0]; //if (!$form_action) $form_action = "\$_SERVER['REQUEST_URI']";
	$temp = get_variables("form_method"," || "); if (!$form_method) $form_method = $temp[0]; if (!$form_method) $form_method = "POST";
	$temp = get_variables("form_enctype"," || "); if (!$form_enctype) $form_enctype = $temp[0]; if (!$form_enctype) $form_enctype = "multipart/form-data";
	$temp = get_variables("onsubmit_action"," || "); if (!$onsubmit_action) $onsubmit_action = $temp[0]; 
	$temp = get_variables("form_success_redirect"," || "); if (!$form_success_redirect) $form_success_redirect = $temp[0]; if (!$form_success_redirect) $form_success_redirect = "";
	$temp = get_variables("form_failure_redirect"," || "); if (!$form_failure_redirect) $form_failure_redirect = $temp[0]; if (!$form_failure_redirect) $form_failure_redirect = "";
	$temp = get_variables("form_submit_text"," || "); if (!$form_submit_text) $form_submit_text = $temp[0];
	$temp = get_variables("form_insert_text"," || "); if (!$form_insert_text) $form_insert_text = $temp[0];
	$temp = get_variables("form_edit_text"," || "); if (!$form_edit_text) $form_edit_text = $temp[0];
		if (!$form_submit_text) $form_submit_text = "Submit";

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {
		$newlines = array();
		$desc1_location = "desc1_location || " . $desc1_location . "
";
		$desc2_location = "desc2_location || " . $desc2_location . "
";
		$pwhelp = "pwhelp || " . $pwhelp . "
";
		$form_width = "form_width || " . $form_width . "
";
		$num_sections = "numsections || " . $numsections . "
";
		$section1_num_rows = "section1numrows || " . $section1numrows . "
";
		$section1_num_cols = "section1numcols || " . $section1numcols . "
";
		$section1_title = "section1title || " . $section1title . "
";
		$section2_num_rows = "section2numrows || " . $section2numrows . "
";
		$section2_num_cols = "section2numcols || " . $section2numcols . "
";
		$section2_title = "section2title || " . $section2title . "
";
		$section3_num_rows = "section3numrows || " . $section3numrows . "
";
		$section3_num_cols = "section3numcols || " . $section3numcols . "
";
		$section3_title = "section3title || " . $section3title . "
";
		$section4_num_rows = "section4numrows || " . $section4numrows . "
";
		$section4_num_cols = "section4numcols || " . $section4numcols . "
";
		$section4_title = "section4title || " . $section4title . "
";
		$encoding = "encoding || " . $encoding . "
";
		$displayfile = "displayfile || " . $displayfile . "
";
		$fckedit_toolbar = "fckedit_toolbar || " . $fckedit_toolbar . "
";
		$humanverify = "humanverify || " . $humanverify . "
";
		$humanverify_question = "humanverify_question || " . $humanverify_question . "
";
		$humanverify_answer = "humanverify_answer || " . $humanverify_answer . "
";
		$akismet_use = "akismet_use || " . $akismet_use . "
";
		$akismet_key = "akismet_key || " . $akismet_key . "
";
		$akismet_fields = "akismet_fields || " . $akismet_fields . "
";
		$css = str_replace(";"," || ",$_POST["css"]);
		if (substr($css,-4)==" || ") $css = substr($css,0,strlen($css)-4);
		$css = "css || " . $css . "
";


		// update or write the variables file 
		if (!replace_line(FORM_VARIABLES,"desc1_location",$desc1_location)) append_file(FORM_VARIABLES,$desc1_location);
		if (!replace_line(FORM_VARIABLES,"desc2_location",$desc2_location)) append_file(FORM_VARIABLES,$desc2_location);
		if (!replace_line(FORM_VARIABLES,"pwhelp",$pwhelp)) append_file(FORM_VARIABLES,$pwhelp);
		if (!replace_line(FORM_VARIABLES,"form_width",$form_width)) append_file(FORM_VARIABLES,$form_width);
		if (!replace_line(FORM_VARIABLES,"numsections",$num_sections)) append_file(FORM_VARIABLES,$num_sections);
		if (!replace_line(FORM_VARIABLES,"section1numrows",$section1_num_rows)) append_file(FORM_VARIABLES,$section1_num_rows);
		if (!replace_line(FORM_VARIABLES,"section1numcols",$section1_num_cols)) append_file(FORM_VARIABLES,$section1_num_cols);
		if (!replace_line(FORM_VARIABLES,"section1title",$section1_title)) append_file(FORM_VARIABLES,$section1_title);
		if (!replace_line(FORM_VARIABLES,"section2numrows",$section2_num_rows)) append_file(FORM_VARIABLES,$section2_num_rows);
		if (!replace_line(FORM_VARIABLES,"section2numcols",$section2_num_cols)) append_file(FORM_VARIABLES,$section2_num_cols);
		if (!replace_line(FORM_VARIABLES,"section2title",$section2_title)) append_file(FORM_VARIABLES,$section2_title);
		if (!replace_line(FORM_VARIABLES,"section3numrows",$section3_num_rows)) append_file(FORM_VARIABLES,$section3_num_rows);
		if (!replace_line(FORM_VARIABLES,"section3numcols",$section3_num_cols)) append_file(FORM_VARIABLES,$section3_num_cols);
		if (!replace_line(FORM_VARIABLES,"section3title",$section3_title)) append_file(FORM_VARIABLES,$section3_title);
		if (!replace_line(FORM_VARIABLES,"section4numrows",$section4_num_rows)) append_file(FORM_VARIABLES,$section4_num_rows);
		if (!replace_line(FORM_VARIABLES,"section4numcols",$section4_num_cols)) append_file(FORM_VARIABLES,$section4_num_cols);
		if (!replace_line(FORM_VARIABLES,"section4title",$section4_title)) append_file(FORM_VARIABLES,$section4_title);
		if (!replace_line(FORM_VARIABLES,"encoding",$encoding)) append_file(FORM_VARIABLES,$encoding);
		if (!replace_line(FORM_VARIABLES,"displayfile",$displayfile)) append_file(FORM_VARIABLES,$displayfile);
		if (!replace_line(FORM_VARIABLES,"fckedit_toolbar",$fckedit_toolbar)) append_file(FORM_VARIABLES,$fckedit_toolbar);
		if (!replace_line(FORM_VARIABLES,"humanverify",$humanverify)) append_file(FORM_VARIABLES,$humanverify);
		if (!replace_line(FORM_VARIABLES,"humanverify_question",$humanverify_question)) append_file(FORM_VARIABLES,$humanverify_question);
		if (!replace_line(FORM_VARIABLES,"humanverify_answer",$humanverify_answer)) append_file(FORM_VARIABLES,$humanverify_answer);
		if (!replace_line(FORM_VARIABLES,"akismet_use",$akismet_use)) append_file(FORM_VARIABLES,$akismet_use);
		if (!replace_line(FORM_VARIABLES,"akismet_key",$akismet_key)) append_file(FORM_VARIABLES,$akismet_key);
		if (!replace_line(FORM_VARIABLES,"akismet_fields",$akismet_fields)) append_file(FORM_VARIABLES,$akismet_fields);
		if (!replace_line(FORM_VARIABLES,"css",$css)) append_file(FORM_VARIABLES,$css);
	}


	printMessage("STEP 8: Now let's take a look at the proposed layout and add some descriptive text if so desired...","","bold");
	echo "<br />";

	printMessage("Specify your form definition. If you don't know what this is, just use the defaults.","");
	$onsubmit_help_html = "This will usually be left empty but if you want the form to perform some javascript action on submit then specify it here<br />e.g., <code>alert('hi there');</code>";
		echo "<div id=\"onsubmit_help\" class=\"hide\">" . $onsubmit_help_html . "</div>\n";
	echo "<table>";
	echo "<tr><td> form name</td><td>"; textbox("form_name",$form_name,30,255); echo "</td></tr>\n";
	echo "<tr><td> form action</td><td>"; textbox("form_action",$form_action,30,255); echo "</td></tr>\n";
	echo "<tr><td> form method</td><td>"; textbox("form_method",$form_method,30,255); echo "</td></tr>\n";
	echo "<tr><td> form enctype</td><td>"; textbox("form_enctype",$form_enctype,30,255); echo "</td></tr>\n";
	echo "<tr><td> form onsubmit action</td><td>"; textbox("onsubmit_action",$onsubmit_action,30,255); echo " <a class=\"jt_sticky\" href=\"#\" rel=\"#onsubmit_help\" title=\"Helpful Info\"><img src=\"".$addeditdir."/images/info.gif\" alt=\"more info\"></a></td></tr>\n";
	echo "</table><br />";

	printMessage("You can specify where to send the user when a form is submitted. If left blank, the same page will be specified (which is fine since there will be status messages displayed)","");
	echo "<table>";
	echo "<tr><td> Redirect on Success</td><td>"; textbox("form_success_redirect",$form_success_redirect,30,255); echo "</td></tr>\n";
	echo "<tr><td> Redirect on Failure</td><td>"; textbox("form_failure_redirect",$form_failure_redirect,30,255); echo "</td></tr>\n";
	echo "</table><br />";

	printMessage("Specify relevant text...","");
	echo "<table>";
	echo "<tr><td> form title/heading</td><td>"; textbox("form_title",$form_title,30,255); echo "</td></tr>\n";
	echo "<tr><td> form submit text</td><td>"; textbox("form_submit_text",$form_submit_text,30,255); echo "</td></tr>\n";
	echo "<tr><td> form completion text (for new form submission)</td><td>"; textbox("form_insert_text",$form_insert_text,30,255); echo "</td></tr>\n";
	echo "<tr><td> form completion text (for edit form submission)</td><td>"; textbox("form_edit_text",$form_edit_text,30,255); echo "</td></tr>\n";
	echo "</table><br />";

	printMessage("Below are the table cells for each section - assign a form element to each. To have an element take up more than one column, just leave column(s) to the right blank.","");
	echo "<br />";
	for($i=1; $i<=$numsections; $i++) {
		$rownum = "section".$i."numrows";
		$colnum = "section".$i."numcols";
		$sectiontitle = "section".$i."title";
		//echo "rownum is " . $rownum . " - " . $$rownum . "<br>";
		//echo "colnum is " . $colnum . " - " . $$colnum . "<br>";
		
		echo "<strong>Section ".$i." - ".$$sectiontitle."</strong>\n";
		echo "<table border=\"1\">\n";
		for($x=1; $x<=$$rownum; $x++) {
			echo "<tr>\n";
			$sectionrow = "section".$i."row".$x;
			if (!$$sectionrow) $temp = get_variables($sectionrow." "," || ");
			for($n=1; $n<=$$colnum; $n++) {
				echo "<td>"; 
				$col = "col".$n;
				$sectionrowcol = $sectionrow.$col;
				if (!$$sectionrowcol) $$sectionrowcol = $temp[$n-1]; $_POST[$sectionrowcol] = $$sectionrowcol;
				//echo "sectionrowcol is " . $sectionrowcol . " - " . $$sectionrowcol . "; " . $_POST[$sectionrowcol] . "<br>";
				$selectbox_list = array(""=>"");
				foreach($tables as $index=>$tablename) {
					$fields[$tablename] = get_variables($tablename."_fields");
					//print_r($fields[$tablename]); echo "<br />"; 
					foreach($fields[$tablename] as $key=>$field) {
						$tablefield = $tablename."_".$field;
						$selectbox_list = array_merge((array)$selectbox_list, array($tablefield=>$tablefield));
					}
				}
				selectbox($sectionrowcol, $selectbox_list);
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<br /><br />";
	}


	echo "<table class=\"center\">\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 9\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 9 - Setup email options
// =======================================================================================================
function step9HTML() {
	global $error_check_fields, $error_check, $error_check_english, $form_name, $form_title, $form_action, $form_method, $form_enctype, $form_success_redirect, $form_failure_redirect, $addeditdir;
	$error_check_fields = array();

	// ---------------------------------------------------------------------------------
	// --- Get relevant info from variables file...
	// ---------------------------------------------------------------------------------
	$temp = get_variables("numsections"," || "); if (!$numsections) $numsections = $temp[0];
	$temp = get_variables("section1numrows"," || "); if (!$section1numrows) $section1numrows = $temp[0];
	$temp = get_variables("section1numcols"," || "); if (!$section1numcols) $section1numcols = $temp[0];
	$temp = get_variables("section2numrows"," || "); if (!$section2numrows) $section2numrows = $temp[0];
	$temp = get_variables("section2numcols"," || "); if (!$section2numcols) $section2numcols = $temp[0];
	$temp = get_variables("section3numrows"," || "); if (!$section3numrows) $section3numrows = $temp[0];
	$temp = get_variables("section3numcols"," || "); if (!$section3numcols) $section3numcols = $temp[0];
	$temp = get_variables("section4numrows"," || "); if (!$section4numrows) $section4numrows = $temp[0];
	$temp = get_variables("section4numcols"," || "); if (!$section4numcols) $section4numcols = $temp[0];
	$temp = get_variables("email_format"," || "); if (!$email_format) $email_format = $temp[0]; 
	$temp = get_variables("email_engine"," || "); if (!$email_engine) $email_engine = $temp[0]; 
	$temp = get_variables("smtp_host"," || "); if (!$smtp_host) $smtp_host = $temp[0];
	$temp = get_variables("smtp_auth"," || "); if (!$smtp_auth) $smtp_auth = $temp[0];
	$temp = get_variables("smtp_user"," || "); if (!$smtp_user) $smtp_user = $temp[0];
	$temp = get_variables("smtp_pass"," || "); if (!$smtp_pass) $smtp_pass = $temp[0];
	$temp = get_variables("email_from "," || "); if (!$email_from) $email_from = $temp[0];
	$temp = get_variables("email_from_name"," || "); if (!$email_from_name) $email_from_name = $temp[0];
	$temp = get_variables("email_reply"," || "); if (!$email_reply) $email_reply = $temp[0];
	$temp = get_variables("email_bounce"," || "); if (!$email_bounce) $email_bounce = $temp[0];
	$temp = get_variables("send_email1"," || "); if (!$send_email1) $send_email1 = $temp[0];
	$temp = get_variables("email1_to"," || "); if (!$email1_to) $email1_to = $temp[0];
	$temp = get_variables("email1_cc"," || "); if (!$email1_cc) $email1_cc = $temp[0];
	$temp = get_variables("email1_subject"," || "); if (!$email1_subject) $email1_subject = $temp[0];
	$temp = get_file_contents(FORM_EMAIL1); if (!$email1_body) $email1_body = $temp;
	$temp = get_variables("email1_include"," || "); if (!$email1_include) $email1_include = $temp[0];
	$temp = get_variables("email1_body_default"," || "); if (!$email1_body_default) $email1_body_default = $temp[0];
	$temp = get_variables("send_email2"," || "); if (!$send_email2) $send_email2 = $temp[0];
	$temp = get_variables("email2_to"," || "); if (!$email2_to) $email2_to = $temp[0];
	$temp = get_variables("email2_cc"," || "); if (!$email2_cc) $email2_cc = $temp[0];
	$temp = get_variables("email2_subject"," || "); if (!$email2_subject) $email2_subject = $temp[0];
	$temp = get_file_contents(FORM_EMAIL2); if (!$email2_body) $email2_body = $temp;
	$temp = get_variables("email2_include"," || "); if (!$email2_include) $email2_include = $temp[0];
	$temp = get_variables("email2_body_default"," || "); if (!$email2_body_default) $email2_body_default = $temp[0];
	$temp = get_variables("attachment1 "," || "); if (!$attachment1) $attachment1 = $temp[0];
	$temp = get_variables("attachment1_name"," || "); if (!$attachment1_name) $attachment1_name = $temp[0];
	$temp = get_variables("attachment2 "," || "); if (!$attachment2) $attachment2 = $temp[0];
	$temp = get_variables("attachment2_name"," || "); if (!$attachment2_name) $attachment2_name = $temp[0];


	$default_body_help_html = "If you select Yes for this, all posted form variables will be listed with their associated variables. This will be instead of anything you may specify in the email_body.";
		echo "<div id=\"default_body_help\" class=\"hide\">" . $default_body_help_html . "</div>\n";

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {

		for($i=1; $i<=$numsections; $i++) {
			$rownum = "section".$i."numrows";
			$colnum = "section".$i."numcols";
			//echo "rownum is " . $rownum . " - " . $$rownum . "<br>";
			//echo "colnum is " . $colnum . " - " . $$colnum . "<br>";
		
			for($x=1; $x<=$$rownum; $x++) {
				$newlines = array();
				$sectionrownum = "section".$i."row".$x;
				$sectionrownum_value = $sectionrownum;
				for($n=1; $n<=$$colnum; $n++) {
					$sectionrownum_value .= " || " . $_POST["section".$i."row".$x."col".$n];
				}
				$sectionrownum_value .= "
";

				//echo $sectionrownum_value;
				// update or write the variables file 
				if (!replace_line(FORM_VARIABLES,$sectionrownum." ",$sectionrownum_value)) append_file(FORM_VARIABLES,$sectionrownum_value);
			}
		}

		
		$newlines = array();
		$form_title = "form_title || " . stripslashes($_POST["form_title"]) . "
";
		$form_name = "form_name || " . stripslashes($_POST["form_name"]) . "
";
		$form_action = "form_action || " . stripslashes($_POST["form_action"]) . "
";
		$form_method = "form_method || " . stripslashes($_POST["form_method"]) . "
";
		$form_enctype = "form_enctype || " . stripslashes($_POST["form_enctype"]) . "
";
		$onsubmit_action = "onsubmit_action || " . stripslashes($_POST["onsubmit_action"]) . "
";
		$form_success_redirect = "form_success_redirect || " . stripslashes($_POST["form_success_redirect"]) . "
";
		$form_failure_redirect = "form_failure_redirect || " . stripslashes($_POST["form_failure_redirect"]) . "
";
		$form_submit_text = "form_submit_text || " . stripslashes($_POST["form_submit_text"]) . "
";
		$form_insert_text = "form_insert_text || " . stripslashes($_POST["form_insert_text"]) . "
";
		$form_edit_text = "form_edit_text || " . stripslashes($_POST["form_edit_text"]) . "
";
		// update or write the variables file 
		if (!replace_line(FORM_VARIABLES,"form_title ",$form_title)) append_file(FORM_VARIABLES,$form_title);
		if (!replace_line(FORM_VARIABLES,"form_name",$form_name)) append_file(FORM_VARIABLES,$form_name);
		if (!replace_line(FORM_VARIABLES,"form_action",$form_action)) append_file(FORM_VARIABLES,$form_action);
		if (!replace_line(FORM_VARIABLES,"form_method",$form_method)) append_file(FORM_VARIABLES,$form_method);
		if (!replace_line(FORM_VARIABLES,"form_enctype",$form_enctype)) append_file(FORM_VARIABLES,$form_enctype);
		if (!replace_line(FORM_VARIABLES,"onsubmit_action",$onsubmit_action)) append_file(FORM_VARIABLES,$onsubmit_action);
		if (!replace_line(FORM_VARIABLES,"form_success_redirect",$form_success_redirect)) append_file(FORM_VARIABLES,$form_success_redirect);
		if (!replace_line(FORM_VARIABLES,"form_failure_redirect",$form_failure_redirect)) append_file(FORM_VARIABLES,$form_failure_redirect);
		if (!replace_line(FORM_VARIABLES,"form_submit_text",$form_submit_text)) append_file(FORM_VARIABLES,$form_submit_text);
		if (!replace_line(FORM_VARIABLES,"form_insert_text",$form_insert_text)) append_file(FORM_VARIABLES,$form_insert_text);
		if (!replace_line(FORM_VARIABLES,"form_edit_text",$form_edit_text)) append_file(FORM_VARIABLES,$form_edit_text);
	}
	// ---------------------------------------------------------------------------------

	?>
	<script type="text/javascript">
	function showall (divname) {
		showdiv(divname+'1'); showdiv(divname+'1a');
		showdiv(divname+'2'); showdiv(divname+'2a');
		showdiv(divname+'3'); showdiv(divname+'3a');
		showdiv(divname+'4'); showdiv(divname+'4a');
		showdiv(divname+'5'); showdiv(divname+'5a');
		showdiv(divname+'6'); showdiv(divname+'6a');
		showdiv(divname+'7'); showdiv(divname+'7a');
		showdiv(divname+'8'); showdiv(divname+'8a');
		return true;
	}
	function hideall (divname) {
		hidediv(divname+'1'); hidediv(divname+'1a');
		hidediv(divname+'2'); hidediv(divname+'2a');
		hidediv(divname+'3'); hidediv(divname+'3a');
		hidediv(divname+'4'); hidediv(divname+'4a');
		hidediv(divname+'5'); hidediv(divname+'5a');
		hidediv(divname+'6'); hidediv(divname+'6a');
		hidediv(divname+'7'); hidediv(divname+'7a');
		hidediv(divname+'8'); hidediv(divname+'8a');
		return true;
	}
	function radioClick (formname,divname) {
		var checked1 = document.generatecode[formname][0].checked;
		var checked2 = document.generatecode[formname][1].checked;
		if (checked1) {
			hideall (divname);
		}
		if (checked2) {
			showall (divname);
		}
		return true;
	}
	
	<?php 
	if ($email_engine=="smtp") echo "addEvent(window, 'load',  function() { showall('smtp'); } ); \n";
	if ($send_email1=="Yes") echo "addEvent(window, 'load',  function() { showall('email1'); } ); \n";
	if ($send_email2=="Yes") echo "addEvent(window, 'load',  function() { showall('email2'); } ); \n";

	echo "</script>\n";

	printMessage("STEP 9: Email Options!","","bold");
	echo "<br />";

	printMessage("You can choose to send up to two different emails (or none). This could be useful for sending a different email to an administrator and to the person who submits the form. To specify a variable in one of the options, enclose it in double brackets - e.g. [[\$variable]]","");
	echo "<table>";
	echo "<tr><td> email format?</td><td>"; radio("email_format", "text=>Text,html=>HTML", "", "", $email_format, "", "horizontal"); echo "</td></tr>\n";
	$mail_engine_event = "onclick=\"return radioClick ('email_engine','smtp');\"";
	echo "<tr><td> which send engine?</td><td>"; radio("email_engine", "mail=>PHP mail(),smtp=>SMTP", "", "", $email_engine, $mail_engine_event, "horizontal"); echo "</td></tr>\n";
	echo "<tr><td><div id='smtp1' style='display:none;'> SMTP host:</div></td><td><div id='smtp1a' style='display:none;'>"; textbox("smtp_host",$smtp_host,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='smtp2' style='display:none;'> SMTP authorization required?:</div></td><td><div id='smtp2a' style='display:none;'>"; radio("smtp_auth", "true=>Yes,false=>No", "", "", $smtp_auth, "", "horizontal"); echo "</div></td></tr>\n";
	echo "<tr><td><div id='smtp3' style='display:none;'> SMTP username:</div></td><td><div id='smtp3a' style='display:none;'>"; textbox("smtp_user",$smtp_user,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='smtp4' style='display:none;'> SMTP password:</div></td><td><div id='smtp4a' style='display:none;'>"; textbox("smtp_pass",$smtp_pass,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><br /></td></tr>";
	echo "<tr><td> email from address:</td><td>"; textbox("email_from",$email_from,30,255); echo "</td></tr>\n";
	echo "<tr><td> email from name:</td><td>"; textbox("email_from_name",$email_from_name,30,255); echo " (default will be \"Addedit Mailer\")</td></tr>\n";
	echo "<tr><td> email reply-to address:</td><td>"; textbox("email_reply",$email_reply,30,255); echo "</td></tr>\n";
	echo "<tr><td> email bounce address:</td><td>"; textbox("email_bounce",$email_bounce,30,255); echo "</td></tr>\n";
	echo "<tr><td><br /></td></tr>";
	$send_email1_event = "onclick=\"return radioClick ('send_email1','email1');\"";
	echo "<tr><td> send email (1):</td><td>"; radio("send_email1", "No=>No,Yes=>Yes", "", "", $send_email1, $send_email1_event, "horizontal"); echo "</td></tr>\n";
	echo "<tr><td><div id='email11' style='display:none;'> email to address:</div></td><td><div id='email11a' style='display:none;'>"; textbox("email1_to",$email1_to,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email12' style='display:none;'> email cc address:</div></td><td><div id='email12a' style='display:none;'>"; textbox("email1_cc",$email1_cc,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email13' style='display:none;'> email subject:</div></td><td><div id='email13a' style='display:none;'>"; textbox("email1_subject",$email1_subject,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email14' style='display:none;'> email body: <br /><span style='font-size:10px'>HTML is OK, specify <br />variables by enclosing in double <br />brackes - e.g. [[\$email]]</span></div></td><td><div id='email14a' style='display:none;'>"; textarea("email1_body", $email1_body); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email18' style='display:none;'> email body file: <br /><span style='font-size:10px'>If you prefer, specify <br />an include file with <br />your email body content</span></div></td><td><div id='email18a' style='display:none;'>"; textbox("email1_include", $email1_include,30,255); echo " (use relative path - e.g., \"/include/emailtemplate.php\")</div></td></tr>\n";
	echo "<tr><td><div id='email15' style='display:none;'> <a href=\"#\" onclick=\"javascript:alert('$default_body_help')\">use default body?</a></div></td><td><div id='email15a' style='display:none;'>"; radio("email1_body_default", "No=>No,Yes=>Yes", "", "", $email1_body_default, "", "horizontal"); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email16' style='display:none;'> attachment:</div></td><td><div id='email16a' style='display:none;'>"; textbox("attachment1",$attachment1,30,255); echo " (use relative path - e.g., \"/files/attachment.pdf\")</div></td></tr>\n";
	echo "<tr><td><div id='email17' style='display:none;'> attachment name:</div></td><td><div id='email17a' style='display:none;'>"; textbox("attachment1_name",$attachment1_name,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><br /></td></tr>";
	$send_email2_event = "onclick=\"return radioClick ('send_email2','email2');\"";
	echo "<tr><td> send email (2):</td><td>"; radio("send_email2", "No=>No,Yes=>Yes", "", "", $send_email2, $send_email2_event, "horizontal"); echo "</td></tr>\n";
	echo "<tr><td><div id='email21' style='display:none;'> email to address:</div></td><td><div id='email21a' style='display:none;'>"; textbox("email2_to",$email2_to,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email22' style='display:none;'> email cc address:</div></td><td><div id='email22a' style='display:none;'>"; textbox("email2_cc",$email2_cc,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email23' style='display:none;'> email subject:</div></td><td><div id='email23a' style='display:none;'>"; textbox("email2_subject",$email2_subject,30,255); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email24' style='display:none;'> email body: <br /><span style='font-size:10px'>HTML is OK, specify <br />variables by enclosing in double <br />brackes - e.g. [[\$email]]</span></div></td><td><div id='email24a' style='display:none;'>"; textarea("email2_body", $email2_body); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email28' style='display:none;'> email body file: <br /><span style='font-size:10px'>If you prefer, specify <br />an include file with <br />your email body content</span></div></td><td><div id='email28a' style='display:none;'>"; textbox("email2_include", $email2_include,30,255); echo " (use relative path - e.g., \"/include/emailtemplate.php\")</div></td></tr>\n";
	echo "<tr><td><div id='email25' style='display:none;'> <a href=\"#\" onclick=\"javascript:alert('$default_body_help')\">use default body?</a></div></td><td><div id='email25a' style='display:none;'>"; radio("email2_body_default", "No=>No,Yes=>Yes", "", "", $email2_body_default, "", "horizontal"); echo "</div></td></tr>\n";
	echo "<tr><td><div id='email26' style='display:none;'> attachment:</div></td><td><div id='email26a' style='display:none;'>"; textbox("attachment2",$attachment2,30,255); echo " (use relative path - e.g., \"/files/attachment.pdf\")</div></td></tr>\n";
	echo "<tr><td><div id='email27' style='display:none;'> attachment name:</div></td><td><div id='email27a' style='display:none;'>"; textbox("attachment2_name",$attachment2_name,30,255); echo "</div></td></tr>\n";
	echo "</table><br />\n";

	echo "<table class=\"center\">\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 10\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}


// =======================================================================================================
// === STEP 10 - RSS / Trackback
// =======================================================================================================
function step10HTML() {
	global $error_check_fields, $error_check, $error_check_english, $addeditdir;
	global $email_engine, $smtp_host, $smtp_auth, $smtp_user, $smtp_user, $smtp_pass, $email_from, $email_reply, $email_bounce;
	global $send_email1, $email1_to, $email1_cc, $email1_subject, $email1_body, $send_email2, $email2_to, $email2_cc, $email2_subject, $email2_body;
	$error_check_fields = array();

	// ---------------------------------------------------------------------------------
	// --- Get relevant info from variables file...
	// ---------------------------------------------------------------------------------
	$tables = get_variables("tables");
	$temp = get_variables("create_RSS"," || "); if (!$create_RSS) $create_RSS = $temp[0]; 
	$temp = get_variables("rss_ping"," || "); if (!$rss_ping) $rss_ping = $temp[0]; 
	$temp = get_variables("rss_display"," || "); if (!$rss_display) $rss_display = $temp[0]; 
	$temp = get_variables("rss_file"," || "); if (!$rss_file) $rss_file = $temp[0]; 
	$temp = get_variables("rss_title "," || "); if (!$rss_title) $rss_title = $temp[0]; 
	$temp = get_variables("rss_description "," || "); if (!$rss_description) $rss_description = $temp[0]; 
	$temp = get_variables("rss_link "," || "); if (!$rss_link) $rss_link = $temp[0]; 
	$temp = get_variables("rss_title_field"," || "); if (!$rss_title_field) $rss_title_field = $temp[0]; 
	$temp = get_variables("rss_description_field"," || "); if (!$rss_description_field) $rss_description_field = $temp[0]; 
	$temp = get_variables("rss_description_chars"," || "); if (!$rss_description_chars) $rss_description_chars = $temp[0]; 
	$temp = get_variables("rss_item_link"," || "); if (!$rss_item_link) $rss_item_link = $temp[0]; 
	$temp = get_variables("create_trackback"," || "); if (!$create_trackback) $create_trackback = $temp[0];
	$temp = get_variables("trackback_edit"," || "); if (!$trackback_edit) $trackback_edit = $temp[0];
	$temp = get_variables("trackback_display"," || "); if (!$trackback_display) $trackback_display = $temp[0];
	$temp = get_variables("trackback_author"," || "); if (!$trackback_author) $trackback_author = $temp[0];
	$temp = get_variables("trackback_title_field"," || "); if (!$trackback_title_field) $trackback_title_field = $temp[0];
	$temp = get_variables("trackback_excerpt"," || "); if (!$trackback_excerpt) $trackback_excerpt = $temp[0];
	$temp = get_variables("trackback_url"," || "); if (!$trackback_url) $trackback_url = $temp[0];
	$temp = get_variables("trackback_encoding"," || "); if (!$trackback_encoding) $trackback_encoding = $temp[0];
	$temp = get_variables("trackback_field1"," || "); if (!$trackback_field1) $trackback_field1 = $temp[0];
	$temp = get_variables("trackback_field2"," || "); if (!$trackback_field2) $trackback_field2 = $temp[0];
	$temp = get_variables("trackback_field3"," || "); if (!$trackback_field3) $trackback_field3 = $temp[0];
	//$temp = get_variables(""," || "); if (!$) $ = $temp[0];


	if (!$create_RSS) $create_RSS = "No";
	if (!$rss_ping) $rss_ping = "No";
	if (!$rss_display) $rss_display = "No";
	if (!$create_trackback) $create_trackback = "No";
	if (!$trackback_display) $trackback_display = "No";
	if (!$trackback_edit) $trackback_edit = "No";
	if (!$trackback_encoding) $trackback_encoding = "UTF-8";

	$list = "=> ,";
	foreach($tables as $index=>$tablename) {
		$fields[$tablename] = get_variables($tablename."_fields");
		// echo "fields are: "; print_r($fields); echo "<br>"; /* */
		foreach($fields[$tablename] as $key=>$field) {
			$tablefield = $tablename."_".$field;
			$list .= $tablefield . "=>" . $tablefield . ",";
		}
	}
	$list = substr($list,0,strlen($list)-1);

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {
		$email_format = "email_format || " . stripslashes($_POST["email_format"]) . "
";
		$email_engine = "email_engine || " . stripslashes($_POST["email_engine"]) . "
";
		$smtp_host = "smtp_host || " . stripslashes($_POST["smtp_host"]) . "
";
		$smtp_auth = "smtp_auth || " . stripslashes($_POST["smtp_auth"]) . "
"; 
		$smtp_user = "smtp_user || " . stripslashes($_POST["smtp_user"]) . "
"; 
		$smtp_pass = "smtp_pass || " . stripslashes($_POST["smtp_pass"]) . "
"; 
		$email_from = "email_from || " . stripslashes($_POST["email_from"]) . "
"; 
		$email_from_name = "email_from_name || " . stripslashes($_POST["email_from_name"]) . "
"; 
		$email_reply = "email_reply || " . stripslashes($_POST["email_reply"]) . "
";
		$email_bounce = "email_bounce || " . stripslashes($_POST["email_bounce"]) . "
";
		$send_email1 = "send_email1 || " . stripslashes($_POST["send_email1"]) . "
";
		$email1_to = "email1_to || " . stripslashes($_POST["email1_to"]) . "
";
		$email1_cc = "email1_cc || " . stripslashes($_POST["email1_cc"]) . "
";
		$email1_subject = "email1_subject || " . stripslashes($_POST["email1_subject"]) . "
";
		$email1_body = stripslashes($_POST["email1_body"]);
		$email1_body_default = "email1_body_default || " . stripslashes($_POST["email1_body_default"]) . "
";
		$email1_include = "email1_include || " . stripslashes($_POST["email1_include"]) . "
"; 
		$send_email2 = "send_email2 || " . stripslashes($_POST["send_email2"]) . "
";
		$email2_to = "email2_to || " . stripslashes($_POST["email2_to"]) . "
";
		$email2_cc = "email2_cc || " . stripslashes($_POST["email2_cc"]) . "
";
		$email2_subject = "email2_subject || " . stripslashes($_POST["email2_subject"]) . "
";
		$email2_body = stripslashes($_POST["email2_body"]);
		$email2_body_default = "email2_body_default || " . stripslashes($_POST["email2_body_default"]) . "
";
		$email2_include = "email2_include || " . stripslashes($_POST["email2_include"]) . "
"; 
		$attachment1 = "attachment1 || " . stripslashes($_POST["attachment1"]) . "
"; 
		$attachment1_name = "attachment1_name || " . stripslashes($_POST["attachment1_name"]) . "
"; 
		$attachment2 = "attachment2 || " . stripslashes($_POST["attachment2"]) . "
"; 
		$attachment2_name = "attachment2_name || " . stripslashes($_POST["attachment2_name"]) . "
"; 
		// update or write the variables file 
		if (!replace_line(FORM_VARIABLES,"email_format",$email_format)) append_file(FORM_VARIABLES,$email_format);
		if (!replace_line(FORM_VARIABLES,"email_engine",$email_engine)) append_file(FORM_VARIABLES,$email_engine);
		if (!replace_line(FORM_VARIABLES,"smtp_host",$smtp_host)) append_file(FORM_VARIABLES,$smtp_host);
		if (!replace_line(FORM_VARIABLES,"smtp_auth",$smtp_auth)) append_file(FORM_VARIABLES,$smtp_auth);
		if (!replace_line(FORM_VARIABLES,"smtp_user",$smtp_user)) append_file(FORM_VARIABLES,$smtp_user);
		if (!replace_line(FORM_VARIABLES,"smtp_pass",$smtp_pass)) append_file(FORM_VARIABLES,$smtp_pass);
		if (!replace_line(FORM_VARIABLES,"email_from",$email_from)) append_file(FORM_VARIABLES,$email_from);
		if (!replace_line(FORM_VARIABLES,"email_from_name",$email_from_name)) append_file(FORM_VARIABLES,$email_from_name);
		if (!replace_line(FORM_VARIABLES,"email_reply",$email_reply)) append_file(FORM_VARIABLES,$email_reply);
		if (!replace_line(FORM_VARIABLES,"email_bounce",$email_bounce)) append_file(FORM_VARIABLES,$email_bounce);
		if (!replace_line(FORM_VARIABLES,"send_email1",$send_email1)) append_file(FORM_VARIABLES,$send_email1);
		if (!replace_line(FORM_VARIABLES,"email1_to",$email1_to)) append_file(FORM_VARIABLES,$email1_to);
		if (!replace_line(FORM_VARIABLES,"email1_cc",$email1_cc)) append_file(FORM_VARIABLES,$email1_cc);
		if (!replace_line(FORM_VARIABLES,"email1_subject",$email1_subject)) append_file(FORM_VARIABLES,$email1_subject);
		write_file(FORM_EMAIL1,$email1_body);
		if (!replace_line(FORM_VARIABLES,"email1_body_default",$email1_body_default)) append_file(FORM_VARIABLES,$email1_body_default);
		if (!replace_line(FORM_VARIABLES,"email1_include",$email1_include)) append_file(FORM_VARIABLES,$email1_include);
		if (!replace_line(FORM_VARIABLES,"send_email2",$send_email2)) append_file(FORM_VARIABLES,$send_email2);
		if (!replace_line(FORM_VARIABLES,"email2_to",$email2_to)) append_file(FORM_VARIABLES,$email2_to);
		if (!replace_line(FORM_VARIABLES,"email2_cc",$email2_cc)) append_file(FORM_VARIABLES,$email2_cc);
		if (!replace_line(FORM_VARIABLES,"email2_subject",$email2_subject)) append_file(FORM_VARIABLES,$email2_subject);
		write_file(FORM_EMAIL2,$email2_body);
		if (!replace_line(FORM_VARIABLES,"email2_body_default",$email2_body_default)) append_file(FORM_VARIABLES,$email2_body_default);
		if (!replace_line(FORM_VARIABLES,"email2_include",$email2_include)) append_file(FORM_VARIABLES,$email2_include);
		if (!replace_line(FORM_VARIABLES,"attachment1",$attachment1)) append_file(FORM_VARIABLES,$attachment1);
		if (!replace_line(FORM_VARIABLES,"attachment1_name",$attachment1_name)) append_file(FORM_VARIABLES,$attachment1_name);
		if (!replace_line(FORM_VARIABLES,"attachment2",$attachment2)) append_file(FORM_VARIABLES,$attachment2);
		if (!replace_line(FORM_VARIABLES,"attachment2_name",$attachment2_name)) append_file(FORM_VARIABLES,$attachment2_name);
	}
	// ---------------------------------------------------------------------------------


	printMessage("STEP 10: Set RSS / Trackback Options","","bold");

	printMessage("You can choose to create an RSS XML file whenever you create/edit using this form.","","","");
	echo "<table>";
	echo "<tr><td>Create RSS?</td><td>"; radio("create_RSS", "Yes=>Yes,No=>No", "", "", $create_RSS, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>Ping RSS Directories?</td><td>"; radio("rss_ping", "Yes=>Yes,No=>No", "", "", $rss_ping, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>Display RSS Success/Failure Message?</td><td>"; radio("rss_display", "Yes=>Yes,No=>No", "", "", $rss_display, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>RSS Filename:</td><td>"; textbox("rss_file",$rss_file,50,255); echo " (use path as well if appropriate - e.g., ../../rss/feed.xml)</td></tr>\n";
	echo "<tr><td>Feed Title:</td><td>"; textbox("rss_title",$rss_title,50,255); echo "</td></tr>\n";
	echo "<tr><td>Feed Description:</td><td>"; textbox("rss_description",$rss_description,50,255); echo "</td></tr>\n";
	echo "<tr><td>Feed Link:</td><td>"; textbox("rss_link",$rss_link,50,255); echo "</td></tr>\n";

	echo "<tr><td>Item Title Field:</td><td>";  selectbox("rss_title_field", $list, "1", "", "", $rss_title_field); echo "</td></tr>\n";
	echo "<tr><td>Item Description Field:</td><td>";  selectbox("rss_description_field", $list, "1", "", "", $rss_description_field); echo "</td></tr>\n";
	echo "<tr><td>Item Description Truncation:</td><td>"; textbox("rss_description_chars",$rss_description_chars,4,255); echo " (IF you want to limit the # of characters displayed, enter the number of characters to show here)</td></tr>\n";
	echo "<tr><td>Item Link:</td><td>"; textbox("rss_item_link",$rss_item_link,50,255); echo "</td></tr>\n";
	echo "</table><br />\n";

	$trackback_help1_html = "Trackback is a system where you let other site(s) know that you have linked to them in some way.";
		echo "<div id=\"trackback_help1\" class=\"hide\">" . $trackback_help1_html . "</div>\n";
	$trackback_info1 = "<a class=\"jt_sticky\" href=\"#\" rel=\"#trackback_help1\" title=\"Helpful Info\"><img src=\"images/info.gif\" width=\"12\" height=\"12\" /></a>\n";
	$trackback_help2_html = "Trackback provides an excerpt of your content to the recipient to display on his/her site. Which field from your form should phpAddEdit use to generate this excerpt?";
		echo "<div id=\"trackback_help2\" class=\"hide\">" . $trackback_help2_html . "</div>\n";
	$trackback_info2 = "<a class=\"jt_sticky\" href=\"#\" rel=\"#trackback_help2\" title=\"Helpful Info\"><img src=\"images/info.gif\" width=\"12\" height=\"12\" /></a>\n";
	$trackback_help3_html = "phpAddEdit uses an automatic detection scheme to identify URLs within text that can accept trackbacks. You can specify up to three of your form fields to be included in the auto detection routine.";
		echo "<div id=\"trackback_help3\" class=\"hide\">" . $trackback_help3_html . "</div>\n";
	$trackback_info3 = "<a class=\"jt_sticky\" href=\"#\" rel=\"#trackback_help3\" title=\"Helpful Info\"><img src=\"images/info.gif\" width=\"12\" height=\"12\" /></a>\n";
	printMessage("You can choose to send a trackback to any relevant links contained in your post. ".$trackback_info1,"","","");
	echo "<table>";
	echo "<tr><td>Create Trackback?</td><td>"; radio("create_trackback", "Yes=>Yes,No=>No", "", "", $create_trackback, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>Trackback on Edit?</td><td>"; radio("trackback_edit", "Yes=>Yes,No=>No", "", "", $trackback_edit, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>Display Trackback Success/Failure Message?</td><td>"; radio("trackback_display", "Yes=>Yes,No=>No", "", "", $trackback_display, "", "horizontal"); echo "</td></tr>\n";
	echo "<tr><td>Trackback Author:</td><td>"; textbox("trackback_author",$trackback_author,50,255); echo "</td></tr>\n";
	echo "<tr><td>Trackback URL (yours not theirs):</td><td>"; textbox("trackback_url",$trackback_url,50,255); echo "</td></tr>\n";
	echo "<tr><td>Trackback Title Field:</td><td>";  selectbox("trackback_title_field", $list, "1", "", "", $trackback_title_field); echo "</td></tr>\n";
	echo "<tr><td>Trackback Excerpt Field:</td><td>";  selectbox("trackback_excerpt", $list, "1", "", "", $trackback_excerpt); echo $trackback_info2."</td></tr>\n"; 
	echo "<tr><td>Trackback Encoding:</td><td>"; textbox("trackback_encoding",$trackback_encoding,20,255); echo "</td></tr>\n";
	echo "<tr><td>Trackback Auto-Detect Field (1):</td><td>";  selectbox("trackback_field1", $list, "1", "", "", $trackback_field1); echo $trackback_info3."</td></tr>\n"; 
	echo "<tr><td>Trackback Auto-Detect Field (2):</td><td>";  selectbox("trackback_field2", $list, "1", "", "", $trackback_field2); echo $trackback_info3."</td></tr>\n"; 
	echo "<tr><td>Trackback Auto-Detect Field (3):</td><td>";  selectbox("trackback_field3", $list, "1", "", "", $trackback_field3); echo $trackback_info3."</td></tr>\n";
	echo "</table><br />\n";

	echo "<table class=\"center\">\n";
	echo "<tr>\n";
	echo "<td align=\"center\"><br /><p class='submit'><input type=\"submit\" name=\"continue\" value=\"Next - Step 11\" /></p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

}

// =======================================================================================================
// === STEP 11 - Finished, instructions for invocation code
// =======================================================================================================
function step11HTML() {
	global $addeditdir, $error_check_fields, $error_check, $error_check_english, $addeditdir;
	$error_check_fields = array();

	// ---------------------------------------------------------------------------------
	// --- Get relevant info from variables file...
	// ---------------------------------------------------------------------------------
	$tables = get_variables("tables");

	// ---------------------------------------------------------------------------------
	// --- Update the variables file with the variables selected in the prior step...
	// ---------------------------------------------------------------------------------
	if (!$error_check && $_POST["continue"]) {
		$create_RSS = "create_RSS || " . stripslashes($_POST["create_RSS"]) . "
";
		$rss_ping = "rss_ping || " . stripslashes($_POST["rss_ping"]) . "
";
		$rss_file = "rss_file || " . stripslashes($_POST["rss_file"]) . "
";
		$rss_display = "rss_display || " . stripslashes($_POST["rss_display"]) . "
";
		$rss_title = "rss_title || " . stripslashes($_POST["rss_title"]) . "
";
		$rss_description = "rss_description || " . stripslashes($_POST["rss_description"]) . "
";
		$rss_link = "rss_link || " . stripslashes($_POST["rss_link"]) . "
";
		$rss_title_field = "rss_title_field || " . stripslashes($_POST["rss_title_field"]) . "
";
		$rss_description_field = "rss_description_field || " . stripslashes($_POST["rss_description_field"]) . "
";
		$rss_description_chars = "rss_description_chars || " . stripslashes($_POST["rss_description_chars"]) . "
";
		$rss_item_link = "rss_item_link || " . stripslashes($_POST["rss_item_link"]) . "
";

		$create_trackback = "create_trackback || " . stripslashes($_POST["create_trackback"]) . "
";
		$trackback_edit = "trackback_edit || " . stripslashes($_POST["trackback_edit"]) . "
";
		$trackback_display = "trackback_display || " . stripslashes($_POST["trackback_display"]) . "
";
		$trackback_author = "trackback_author || " . stripslashes($_POST["trackback_author"]) . "
";
		$trackback_title_field = "trackback_title_field || " . stripslashes($_POST["trackback_title_field"]) . "
";
		$trackback_excerpt = "trackback_excerpt || " . stripslashes($_POST["trackback_excerpt"]) . "
";
		$trackback_url = "trackback_url || " . stripslashes($_POST["trackback_url"]) . "
";
		$trackback_encoding = "trackback_encoding || " . stripslashes($_POST["trackback_encoding"]) . "
";
		$trackback_field1 = "trackback_field1 || " . stripslashes($_POST["trackback_field1"]) . "
";
		$trackback_field2 = "trackback_field2 || " . stripslashes($_POST["trackback_field2"]) . "
";
		$trackback_field3 = "trackback_field3 || " . stripslashes($_POST["trackback_field3"]) . "
";
	}

	// update or write the variables file 
	if (!replace_line(FORM_VARIABLES,"create_RSS",$create_RSS)) append_file(FORM_VARIABLES,$create_RSS);
	if (!replace_line(FORM_VARIABLES,"rss_ping",$rss_ping)) append_file(FORM_VARIABLES,$rss_ping);
	if (!replace_line(FORM_VARIABLES,"rss_display",$rss_display)) append_file(FORM_VARIABLES,$rss_display);
	if (!replace_line(FORM_VARIABLES,"rss_file",$rss_file)) append_file(FORM_VARIABLES,$rss_file);
	if (!replace_line(FORM_VARIABLES,"rss_title",$rss_title)) append_file(FORM_VARIABLES,$rss_title);
	if (!replace_line(FORM_VARIABLES,"rss_description",$rss_description)) append_file(FORM_VARIABLES,$rss_description);
	if (!replace_line(FORM_VARIABLES,"rss_link",$rss_link)) append_file(FORM_VARIABLES,$rss_link);
	if (!replace_line(FORM_VARIABLES,"rss_title_field",$rss_title_field)) append_file(FORM_VARIABLES,$rss_title_field);
	if (!replace_line(FORM_VARIABLES,"rss_description_field",$rss_description_field)) append_file(FORM_VARIABLES,$rss_description_field);
	if (!replace_line(FORM_VARIABLES,"rss_description_chars",$rss_description_chars)) append_file(FORM_VARIABLES,$rss_description_chars);
	if (!replace_line(FORM_VARIABLES,"rss_item_link",$rss_item_link)) append_file(FORM_VARIABLES,$rss_item_link);

	if (!replace_line(FORM_VARIABLES,"create_trackback",$create_trackback)) append_file(FORM_VARIABLES,$create_trackback);
	if (!replace_line(FORM_VARIABLES,"trackback_edit",$trackback_edit)) append_file(FORM_VARIABLES,$trackback_edit);
	if (!replace_line(FORM_VARIABLES,"trackback_display",$trackback_display)) append_file(FORM_VARIABLES,$trackback_display);
	if (!replace_line(FORM_VARIABLES,"trackback_author",$trackback_author)) append_file(FORM_VARIABLES,$trackback_author);
	if (!replace_line(FORM_VARIABLES,"trackback_title_field",$trackback_title_field)) append_file(FORM_VARIABLES,$trackback_title_field);
	if (!replace_line(FORM_VARIABLES,"trackback_excerpt",$trackback_excerpt)) append_file(FORM_VARIABLES,$trackback_excerpt);
	if (!replace_line(FORM_VARIABLES,"trackback_url",$trackback_url)) append_file(FORM_VARIABLES,$trackback_url);
	if (!replace_line(FORM_VARIABLES,"trackback_encoding",$trackback_encoding)) append_file(FORM_VARIABLES,$trackback_encoding);
	if (!replace_line(FORM_VARIABLES,"trackback_field1",$trackback_field1)) append_file(FORM_VARIABLES,$trackback_field1);
	if (!replace_line(FORM_VARIABLES,"trackback_field2",$trackback_field2)) append_file(FORM_VARIABLES,$trackback_field2);
	if (!replace_line(FORM_VARIABLES,"trackback_field3",$trackback_field3)) append_file(FORM_VARIABLES,$trackback_field3);
	// ---------------------------------------------------------------------------------

	printMessage("STEP 11: FINISHED!","","bold");
	echo "<br />";

	for ($i=0; $i<=count($tables)-1; $i++) {
	  $tablename = trim($tables[$i]);
	  $primarykey = $tablename."_primarykey";
	  $temp = get_variables($primarykey," || ");
	  $key .= $tablename."_".$temp[0] . "=&lt;value&gt;&amp;";
	  $key2 .= "$" . $tablename."_".$temp[0] . "=&lt;value&gt;;<br />";
	}
	$key = substr($key,0,strlen($key)-5);
	$key2 = substr($key2,0,strlen($key2)-6);

	$formname = substr(FORM_NAME,6,strlen(FORM_NAME)-10);	// --- strip out forms/ piece of form name...
	//echo "form: ".$addeditdir."/forms/".$formname ."<br />";
	$message = "<strong>ADD CONTENT</strong><br /> You have finished creating your form! You can access your form to add content directly at:<br /><code>http://" .getenv('HTTP_HOST').$addeditdir.FORM_NAME . "</code><br /><br />or you can include it in any page by using the following code:<br /><code>\$formname=\"" . $formname . "\";<br />include(\"" . $addeditdir . "addedit-render.php\");</code><br /><br />";
	//$message .= "<strong>Note</strong>: replace the <code>&lt;formname&gt;</code> with the name of the form you are using (NO .php extension)";
	$message .= "<hr size=\"1\" /><strong>EDIT CONTENT</strong><br /> To use the same form to edit an entry, use the following URL format: <br /><code>http://" .getenv('HTTP_HOST').$addeditdir.FORM_NAME . "?$key</code><br /><br />or you can include it any page by using the following code:<br /><code>\$formname=\"" . $formname . "\";<br />\$editing=true;<br />$key2<br />include(\"" . $addeditdir . "addedit-render.php\");</code><br /><br />";
	$message .= "<strong>Note</strong>: replace the <code>&lt;value&gt;</code> instances above with the relevant value(s) for the primary edit variable(s) which will identify the database row to retrieve<br /><br />";
	$message .= "<strong>Security Warning</strong>: In step 1 we added a security measure to limit <u>who</u> can edit with this form using a cookie. A further potential security issue is that you may want to limit <u>what</u> can be edited even by authorized individuals. To do that you must use the include option above (using the URL directly will allow someone to modify the edit parameter)";
	printMessage($message,"","","");
}
?>