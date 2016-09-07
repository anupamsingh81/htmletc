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
// === Some administrative stuff - mostly stolen from wordpress project...
define('ABSPATH', dirname(__FILE__).'/');
$thispage = substr($_SERVER["PHP_SELF"], strrpos($_SERVER["PHP_SELF"],"/")+1);
$page = $_GET["page"];
$root_url = "http://" . getenv('HTTP_HOST'); 

// === Take care of case where register globals is OFF; also use to add or strip slashes as neeeded...
foreach($_POST as $index=>$value) {
  $value = @stripslashes($value);
  $$index = $value; 
  //echo $index . "-" . $value . "<br />";
}

foreach($_GET as $index=>$value) {
  $value = stripslashes($value);
  $$index = urlencode($value); 
  //echo $index . "-" . $value . "<br />";
}

// === Fix for IIS, which doesn't set REQUEST_URI
if ( empty( $_SERVER['REQUEST_URI'] ) ) {
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME']; // Does this work under CGI?
	
	// === Append the query string if it exists and isn't null
	if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

// === Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
if ( strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7 )
	$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];

// === Fix for Dreamhost and other PHP as CGI hosts
if ( strstr( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) )
	unset($_SERVER['PATH_INFO']);
// =======================================================================================================

// =======================================================================================================
// === define the file/form names - if both formname and editform are specified editform gets precedence...
if ($formname) { 
	if (stristr($formname,"..") || stristr($formname,"%")) {
		$editform_error = "Bad Form Name Detected";
	} else {
		define('FORM_VARIABLES', "forms/".$formname."_variables.php");
		define('FORM_NAME', "forms/".$formname.".php");
		define('FORM_EMAIL1', "forms/".$formname."_email1.php");
		define('FORM_EMAIL2', "forms/".$formname."_email2.php");
		define('STATUS',"new");
	}
}
if ($editform) { 
	if (stristr($editform,"..") || stristr($editform,"%")) {
		$editform_error = "Bad Form Name Detected";
	} else {
		define('FORM_VARIABLES', "forms/".$editform."_variables.php");
		define('FORM_NAME', "forms/".$editform.".php");
		define('FORM_EMAIL1', "forms/".$editform."_email1.php");
		define('FORM_EMAIL2', "forms/".$editform."_email2.php");
		define('STATUS',"edit");
	}
}
?>

<!-- Header -->
<div id="head">
<h1><a href="http://www.phpaddedit.com/">phpAddEdit</a> Form Generation Script <?php include "version.inc.php"; if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") echo " &nbsp; [<font color='red'>DEMO</font>]"; ?></h1>
</div>

<ul id="adminmenu">
	<li><a href="index.php" <?php if (!$page) echo "class=\"current\""; ?>>Generate Form Code</a></li>
	<li><a href="http://www.phpaddedit.com/" target="_blank">Documentation</a></li>
	<?php if ($editform) { ?>
		<li><a href="index.php?page=email&amp;editform=<?php echo $formname; echo $editform; ?>"<?php if ($page=="email") echo " class=\"current\""; ?>>Test Email</a></li>
		<li><a href="index.php?page=test&amp;editform=<?php echo $formname; echo $editform; ?>"<?php if ($page=="test") echo " class=\"current\""; ?>>Test Form</a></li>
		<li><a href="<?php echo FORM_NAME ?>">View <?php echo substr(FORM_NAME,6) ?></a></li>
		<li><a href="index.php?page=toc&amp;editform=<?php echo $formname; echo $editform; ?>"<?php if ($page=="toc") echo " class=\"current\""; ?>>List</a> &nbsp; || &nbsp; </li>
		<li><span style="font-size:8px;">Debug:</span><a href="index.php?page=debug&amp;editform=<?php echo $formname; echo $editform; ?>&amp;what=sql"<?php if ($what=="sql") echo " class=\"current\""; ?>>SQL</a>|
		<a href="index.php?page=debug&amp;editform=<?php echo $formname; echo $editform; ?>&amp;what=execute"<?php if ($what=="execute") echo " class=\"current\""; ?>>Execute</a>|
		<a href="index.php?page=debug&amp;editform=<?php echo $formname; echo $editform; ?>&amp;what=verbose"<?php if ($what=="verbose") echo " class=\"current\""; ?>>Verbose</a>|
		<a href="index.php?page=debug&amp;editform=<?php echo $formname; echo $editform; ?>&amp;what=none"<?php if ($what=="none") echo " class=\"current\""; ?>>None</a></li>
	<?php } ?>
</ul>

<ul id="submenu">
	<?php
	$total_steps = 11;
	if (!$_POST["continue"] && !$_GET["continue"]) $first_page = true;
	if ($_POST["continue"]) $continue = $_POST["continue"];
	if ($_GET["continue"]) $continue = $_GET["continue"];
	if (!$continue && !$page) $continue = "Next - Step 1";
	$continue = urldecode($continue);
	$step = substr($continue,-2);
	if ($step=="0") $step = substr($continue,-2);
	if ($step!="1") {
		$prev = "Next - Step " . ($step -1);
	} else {
		$prev = "Next - Step 1";
	}
	$heading = strtoupper(substr($continue,-6));

	for ($i=1; $i<=$total_steps; $i++) {
		$class = "";
		if ($i==$step) $class = " class=\"current\"";
		$cont = "Next - Step " . $i;
		$cont = urlencode($cont);
		switch ($i) {
		  case 1:
		  	$step_desc = "Start";
			break;
		  case 2:
		  	$step_desc = "Security";
			break;
		  case 3:
		  	$step_desc = "Tables";
			break;
		  case 4:
		  	$step_desc = "Fields";
			break;
		  case 5:
		  	$step_desc = "Options (1)";
			break;
		  case 6:
		  	$step_desc = "Options (2)";
			break;
		  case 7:
		  	$step_desc = "Setup Form (1)";
			break;
		  case 8:
		  	$step_desc = "Setup Form (2)";
			break;
		  case 9:
		  	$step_desc = "Email Options";
			break;
		  case 10:
		  	$step_desc = "RSS/Trackback";
			break;
		  case 11:
		  	$step_desc = "Finish";
			break;
		  default:
		  	$step_desc = "";
			break;
		}
		$tab = "<li><a href=\"index.php?continue=$cont&amp;editform=$formname$editform\"$class>$step_desc</a> &nbsp;</li>\n";
		if ($step_desc == "Create/Edit") $tab = "<li><a href=\"index.php\" $class>$i. $step_desc</a> &nbsp;</li>\n";
		echo $tab;
	}
	?>
</ul>