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
// --- define basic path & page info...
// -------------------------------------------------------------------------------------------
define('ABSPATH', dirname(__FILE__).'/');
$basepath = substr(dirname(__FILE__),0,strrpos(dirname(__FILE__),"addedit")-1);
$thispath = $addeditdir = $addeditcwd = realpath(dirname(__FILE__));
//$addeditdir = "/".substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1)."/";
// --- added 09-07-2009 - JB - use the following code when working with XAMPP on windows systems to get the path right...
if (strpos($addeditdir,"\\")) {
	$part = explode('\\', $addeditdir);
	$temp = count($part);
	$addeditdir = $part[$temp-1];
}
$thispage = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'],'/')+1);
$host = $_SERVER['HTTP_HOST']; 
if (!$website) {
	$website = $host;
	if (substr($website,0,4)=="www.") $website = substr($website,4);
}


/*
echo "base path: " . $basepath . "<br />\n";
echo "this path: " . $thispath . "<br />\n";
echo "request_uri: " . $_SERVER["REQUEST_URI"] . "<br />\n";
echo "dir: " . $addeditdir . "<br />\n";
echo "cwd: " . $addeditcwd . "<br />\n";
echo "thispage: " . $thispage . "<br />\n";
*/

// -------------------------------------------------------------------------------------------
// --- include functions file
// -------------------------------------------------------------------------------------------
include_once ($addeditcwd."/addedit-functions.php"); 

// -------------------------------------------------------------------------------------------
// --- Get and define global form name...
// -------------------------------------------------------------------------------------------
if (!$formname && $_GET["editform"]) {
	$formname = urlencode($_GET["editform"]);
	if (stristr($formname,"..") || stristr($formname,"%")) {
		printMessage("Hack Error Detected","red");
		exit;
	}
}
if (!$formname) $formname = substr($thispage,0,strlen($thispage)-4);
$formname = $thispath."/forms/". $formname;
//echo "formname is " . $formname . " and editing is " . $editing . "<br>";

$formname_variables = $formname . "_variables.php";
if (is_readable($formname_variables)) {
	define('FORM_VARIABLES', $formname_variables);
	define('FORM_NAME', $formname.".php");
} else {
	printMessage("Can't find or read the specified form name <code>$formname_variables</code>","red");
	exit;
}


// -------------------------------------------------------------------------------------------
// --- See if this page is the form page being called directly
// -------------------------------------------------------------------------------------------
$directcall = false;
if ($thispath."/forms/".$thispage==FORM_NAME) {
	$directcall = true;
}

// -------------------------------------------------------------------------------------------
// --- define header if not viewing form in program...
// -------------------------------------------------------------------------------------------
$css = get_variables("css"," || ");
$temp = get_variables("form_title "," || "); if (!$form_title) $form_title = $temp[0];
if (!$form_title) {
	$page_title = "AddEdit Form Generator Script - Create PHP Forms Easily";
} else {
	$page_title = $form_title;
}

if ($directcall) {
	$temp = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	$temp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	//$temp = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	//$temp .= "<html>\n";
	$temp .= "<head>\n";
	$temp .= "<title>".$form_title."</title>\n";
	$temp .= "<link rel=\"stylesheet\" href=\"../includes/style.css\" type=\"text/css\" />\n";
	if ($css[0]) {
		foreach ($css as $css_file) {
			$temp .= "<link rel=\"stylesheet\" href=\"" .$css_file . "\" type=\"text/css\" />\n";
		}
	}
	$temp .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
	$temp .= "<script type=\"text/javascript\" src=\"../includes/javascripts.js\"></script>\n";
	$temp .= "</head>\n";
	$temp .= "<body>\n";
	echo $temp;
} else {
	$temp = "";
	if ($css[0]) {
		foreach ($css as $css_file) {
			//echo "file: " . $css_file . "<br>";
			$temp .= "<link rel=\"stylesheet\" href=\"" . $css_file . "\" type=\"text/css\" />\n";
		}
		echo $temp;
	}
}


// --------------------------------------------------------------------------------------
// --- Get GET & POST variables & set key useful variables
// --- 
// --- Also handle date form field since it is 3 selectboxes that should function as one 
// --- date, we need to consolidate those fields into one before doing any processing
// --------------------------------------------------------------------------------------
foreach($_GET as $index=>$value) {
	//$value = stripslashes($value);
	$$index = $value; 
	//echo $index . " - " . $value . "<br>";
}
foreach($_POST as $index=>$value) {
	//$value = stripslashes($value);
	$$index = $value; 
	if (stristr($index,"_datemo")) {
		$index = $datename = substr($index,0,strlen($index)-2);
		$_POST[$index] .= $value;
	}
	if (stristr($index,"_datedd")) {
		$index = $datename = substr($index,0,strlen($index)-2);
		$_POST[$index] .= "-" . $value;
	}
	if (stristr($index,"_dateyy")) {
		$index = $datename = substr($index,0,strlen($index)-2);
		$_POST[$index] = $value . "-" . $_POST[$index];
	}
	//echo $index . " - " . $_POST[$index] . "<br />";
}

$today_date = date( "Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")) );
$today_datetime = date("Y-m-d H:i:s", mktime());


// -------------------------------------------------------------------------------------------
// --- include required files 
// -------------------------------------------------------------------------------------------
include_once ($thispath."/config.php");
include_once ($thispath."/includes/dbconnect.inc.php");


// -------------------------------------------------------------------------------------------
// --- Get all the relevant info from the variables files
// -------------------------------------------------------------------------------------------
$temp = get_variables("addenable"," || "); $addenable = $temp[0];
$temp = get_variables("editenable"," || "); $editenable = $temp[0];
$temp = get_variables("directenable"," || "); $directenable = $temp[0];
$temp = get_variables("addcookie "," || "); $addcookie = $temp[0];
$temp = get_variables("addcookie_value"," || "); $addcookie_value = $temp[0];
$temp = get_variables("editcookie "," || "); $editcookie = $temp[0];
$temp = get_variables("editcookie_value"," || "); $editcookie_value = $temp[0];
$temp = get_variables("pwhelp"," || "); if (!$pwhelp) $pwhelp = $temp[0];
$temp = get_variables("form_success_redirect"," || "); $form_success_redirect = $temp[0];
$temp = get_variables("form_failure_redirect"," || "); $form_failure_redirect = $temp[0];
$temp = get_variables("email_engine"," || "); $email_engine = $temp[0];
$temp = get_variables("smtp_host"," || "); $smtp_host = $temp[0];
$temp = get_variables("smtp_auth"," || "); $smtp_auth = $temp[0];
$temp = get_variables("smtp_user"," || "); $smtp_user = $temp[0];
$temp = get_variables("smtp_pass"," || "); $smtp_pass = $temp[0];
$temp = get_variables("email_from "," || "); $email_from = $temp[0];
$temp = get_variables("email_from_name"," || "); $email_from_name = $temp[0];
$temp = get_variables("email_reply"," || "); $email_reply = $temp[0];
$temp = get_variables("email_bounce"," || "); $email_bounce = $temp[0];
$temp = get_variables("send_email1"," || "); $send_email1 = $temp[0];
$temp = get_variables("email1_to"," || "); $email1_to = $temp[0];
$temp = get_variables("email1_cc"," || "); $email1_cc = $temp[0];
$temp = get_variables("email1_subject"," || "); $email1_subject = $temp[0];
$temp = get_variables("email1_body"," || "); $email1_body = $temp[0];
$temp = get_variables("email1_body_default"," || "); $email1_body_default = $temp[0];
$temp = get_variables("email1_include"," || "); $email1_include = $temp[0];
$temp = get_variables("send_email2"," || "); $send_email2 = $temp[0];
$temp = get_variables("email2_to"," || "); $email2_to = $temp[0];
$temp = get_variables("email2_cc"," || "); $email2_cc = $temp[0];
$temp = get_variables("email2_subject"," || "); $email2_subject = $temp[0];
$temp = get_variables("email2_body"," || "); $email2_body = $temp[0];
$temp = get_variables("email2_body_default"," || "); $email2_body_default = $temp[0];
$temp = get_variables("email2_include"," || "); $email2_include = $temp[0];
$temp = get_variables("email_format"," || "); $email_format = $temp[0];
$temp = get_variables("attachment1 "," || "); $attachment1 = $temp[0];
$temp = get_variables("attachment1_name"," || "); $attachment1_name = $temp[0];
$temp = get_variables("attachment2 "," || "); $attachment2 = $temp[0];
$temp = get_variables("attachment2_name"," || "); $attachment2_name = $temp[0];
$temp = get_variables("create_RSS"," || "); if (!$create_RSS) $create_RSS = $temp[0]; 
$temp = get_variables("rss_display"," || "); if (!$rss_display) $rss_display = $temp[0]; 
$temp = get_variables("rss_ping"," || "); if (!$rss_ping) $rss_ping = $temp[0]; 
$temp = get_variables("rss_file"," || "); if (!$rss_file) $rss_file = $temp[0]; 
$temp = get_variables("rss_title "," || "); if (!$rss_title) $rss_title = $temp[0]; 
$temp = get_variables("rss_description "," || "); if (!$rss_description) $rss_description = $temp[0]; 
$temp = get_variables("rss_link"," || "); if (!$rss_link) $rss_link = $temp[0]; 
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
$temp = get_variables("form_title "," || "); if (!$form_title) $form_title = $temp[0];
$temp = get_variables("form_name"," || "); if (!$form_name) $form_name = $temp[0];
$temp = get_variables("form_action"," || "); if (!$form_action) $form_action = $temp[0];
$temp = get_variables("form_method"," || "); if (!$form_method) $form_method = $temp[0];
$temp = get_variables("form_enctype"," || "); if (!$form_enctype) $form_enctype = $temp[0];
$temp = get_variables("onsubmit_action"," || "); if (!$onsubmit_action) $onsubmit_action = $temp[0];
$temp = get_variables("form_submit_text"," || "); if (!$form_submit_text) $form_submit_text = $temp[0];
$temp = get_variables("desc1_location"," || "); if (!$desc1_location) $desc1_location = $temp[0];
$temp = get_variables("desc2_location"," || "); if (!$desc2_location) $desc2_location = $temp[0];
$temp = get_variables("form_width"," || "); if (!$form_width) $form_width = $temp[0];
$temp = get_variables("numsections"," || "); if (!$numsections) $numsections = $temp[0];
$temp = get_variables("section1numrows"," || "); if (!$section1numrows) $section1numrows = $temp[0];
$temp = get_variables("section1numcols"," || "); if (!$section1numcols) $section1numcols = $temp[0];
$temp = get_variables("section1title"," || "); if (!$section1title) $section1title = $temp[0];
$temp = get_variables("section2numrows"," || "); if (!$section2numrows) $section2numrows = $temp[0];
$temp = get_variables("section2numcols"," || "); if (!$section2numcols) $section2numcols = $temp[0];
$temp = get_variables("section2title"," || "); if (!$section2title) $section2title = $temp[0];
$temp = get_variables("section3numrows"," || "); if (!$section3numrows) $section3numrows = $temp[0];
$temp = get_variables("section3numcols"," || "); if (!$section3numcols) $section3numcols = $temp[0];
$temp = get_variables("section3title"," || "); if (!$section3title) $section3title = $temp[0];
$temp = get_variables("encoding "," || "); if (!$encoding) $encoding = $temp[0];
	if (!$encoding) $encoding = "ISO-8859-1";
$temp = get_variables("humanverify "," || "); if (!$humanverify) $humanverify = $temp[0];
$temp = get_variables("humanverify_question"," || "); if (!$humanverify_question) $humanverify_question = $temp[0];
$temp = get_variables("humanverify_answer"," || "); if (!$humanverify_answer) $humanverify_answer = $temp[0];
$temp = get_variables("displayfile"," || "); if (!$displayfile) $displayfile = $temp[0];
$temp = get_variables("fckedit_toolbar"," || "); if (!$fckedit_toolbar) $fckedit_toolbar = $temp[0];
$temp = get_variables("akismet_use"," || "); if (!$akismet_use) $akismet_use = $temp[0];
$temp = get_variables("akismet_key"," || "); if (!$akismet_key) $akismet_key = $temp[0];
$temp = get_variables("akismet_fields"," || "); if (!$akismet_fields) $akismet_fields = $temp[0];
//$temp = get_variables(""," || "); if (!$) $ = $temp[0];
// ---------------------------------------------------------

// -------------------------------------------------------------------------------------------
// --- if someone bothered to set an encoding scheme, let's tell the DB to use it...
// --- right now only supporting default and UTF8 - need to investigate other common choices
// -------------------------------------------------------------------------------------------
if ($encoding!="ISO-8859-1") {
	if ($encoding=="UTF-8" || $encoding=="UTF8") $db->query("SET NAMES 'UTF8'");
}


// -------------------------------------------------------------------------------------------
// --- include customize file so users can add code to suit their specific needs. 
// -------------------------------------------------------------------------------------------
include_once ($thispath."/addedit-customize.php"); 


// -------------------------------------------------------------------------------------------
// --- Get primary key info and check if those primary key(s) are unique 
// --- warn if not unique AND if we are on the test page...ignore o/w
// -------------------------------------------------------------------------------------------
$tables = get_variables("tables");
//print_r($tables);
//echo "<br />";
foreach($tables as $index=>$tablename) {
	$primarykey_previous = $primarykey;
	$$primarykey_previous = $$primarykey;
	//echo "prev primarykey is . " . $primarykey_previous . " - " . $$primarykey_previous . "<br>";
	$fields[$tablename] = get_variables($tablename."_fields");
	// -------------------------------------------------------------------------------------------
	// --- Catch cases where someone has mis-built a form or a form isn't finished yet...
	// -------------------------------------------------------------------------------------------
	if (count($fields[$tablename])==0) {
		echo "<br />";
		printMessage("This form appears to be unfinished","red");
		exit;
	}
	$temp = $tablename."_primarykey";
	$temp2 = get_variables($temp," || ");
	$primarykey = $temp2[0];
	$tablename_primarykey = $tablename."_".$primarykey;
	if ($$tablename_primarykey && !$_POST[$tablename_primarykey]) $editing=true;
	if ($_GET[$tablename_primarykey]) {
		$editing=true;
		$direct_editing=true;
		$$tablename_primarykey = $_GET[$tablename_primarykey];
	}
	//echo "<br>" . $tablename . " primary key is " . $primarykey . " - " . $$primarykey . " - " . $_GET[$tablename_primarykey] . "<br>";

	// --- if editing, let's check to see if using a unique ID or if update will affect multiple rows...
	if ($editing && $$primarykey) {
		$primary_check_sql = "select count(*) as num from $tablename where $primarykey='".mysql_real_escape_string($$primarykey)."'";
		//echo $primary_check_sql;

		$getnumrows = $db->get_row($primary_check_sql);
		$num = $getnumrows->num;
		//echo "num is " . $num . "<br />";
		if ($num>1 && $page=="test") printMessage ("<br />WARNING: The primary key you are using for table <code>$tablename</code> is not unique - this can cause problems when editing as more than one entry will possibly get updated.<br />","red");
	}
}
//echo "editing is " . $editing;
//echo "direct editing is " . $direct_editing;
//echo "<br /><br />\n";


// -------------------------------------------------------------------------------------------
// --- Check security settings for this form...
// -------------------------------------------------------------------------------------------
if ($directenable=="No" && $directcall) $error_message .= "This form cannot be run directly.<br />";
if (!$direct_editing && $addenable=="No" && $directcall) $error_message .= "This form is not setup to add new content via direct access.<br />";
if ($direct_editing && $editenable=="No" && $directcall) $error_message .= "This form is not setup to edit content via direct access.<br />";
if ($addcookie && !$editing) {
	if (!$_COOKIE[$addcookie]) $error_message .= "You are not authorized to use this form to add content.<br />";
	if ($addcookie_value && !stristr($addcookie_value,$_COOKIE[$addcookie])) $error_message .= "You are not authorized to use this form to add content.<br />";
}
if ($editcookie && $editing) {
	if (!$_COOKIE[$editcookie]) $error_message .= "You are not authorized to use this form to edit content.<br />";
	if ($editcookie_value && !stristr($editcookie_value,$_COOKIE[$editcookie])) $error_message .= "You are not authorized to use this form to edit content.<br />";
}

if ($error_message) {
	echo "<div style='margin:100px;'>\n";
	printMessage(substr($error_message,0,strlen($error_message)-6),"red");
	echo "</div>\n";
	echo "</body>\n";
	echo "</html>\n";
	exit;
}


// -------------------------------------------------------------------------------------------
// --- include required files 
// -------------------------------------------------------------------------------------------
include_once ($thispath."/addedit-form-fields.php"); 
include_once ($thispath."/addedit-error-check.php"); 
include_once ($thispath."/addedit-execute.php");

// -------------------------------------------------------------------------------------------
// --- Now execute if form submitted and render if appropriate (no submit or error)
// ---     include form header and footer files as well
// -------------------------------------------------------------------------------------------
if ( $error_message || $error || !$_POST["submitval"] ) {
	include_once ($formname."-header.inc.php");
	include_once ($thispath."/addedit-create-form.php");
	include_once ($formname."-footer.inc.php");
}

if ($directcall) {
	echo "</body>\n";
	echo "</html>\n";
}
?>