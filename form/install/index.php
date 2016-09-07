<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
	<title> &rsaquo; Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel='stylesheet' href='../includes/login.css' type='text/css' />
</head>
<body class="login">

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
//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors","1");

// ----------------------------------------------------------------------------------------------------
// --- core includes
// ----------------------------------------------------------------------------------------------------
include_once("../addedit-functions.php");
include_once("../config.php");
include_once("../includes/db.php");


// ----------------------------------------------------------------------------------------------------
// --- Wrapper div and form...
// ----------------------------------------------------------------------------------------------------
echo "<div id='login'>\n";
echo "<form name=\"install\" id=\"install\" action=\"\" method=\"post\">\n";


// ----------------------------------------------------------------------------------------------------
// --- Check if forms directory is writeable...
// ----------------------------------------------------------------------------------------------------
$file = "../forms/readme.txt";
if (!$file_handle = @fopen($file,"w+")) { 
	echo "<br />\n";
	printMessage("Your forms directory is not writeable - Please chmod to 0777","red");
	echo "</form>\n";
	echo "</div>\n";
	echo "<div id=\"back\"><a href=\"/\">&laquo; Back to Site</a> &nbsp; || &nbsp; <a href=\"../\">phpAddEdit Panel &raquo;</a></div>\n";
	exit;
} else {
	chmod($file,0777);
	if (!fwrite($file_handle, "hello")) { 
	  echo "Error Writing to file $file"; 
	}
}


// ----------------------------------------------------------------------------------------------------
// --- Check if already installed...
// ----------------------------------------------------------------------------------------------------
$error = $execute = false;
if (DB_USER!="x") {
	$already_installed = true;
	echo "<br />\n";
	printMessage("It seems you have already run the installation routine. To re-install, transfer the original <code>config.php</code> file (found in the <code>includes</code> folder) from your setup files to your server.","red");
} else {
	if ($_POST["submit"]) {
		// --- Need the following code to update the config.php file so we can login to the DB...
		if (is_writable("../config.php")) {
			if ($_POST[dbhost]) {
				edit_file ("../config.php","DB_HOST","define",";","define('DB_HOST', '$_POST[dbhost]')");
			} else {
				$error .= "You must specify a database host<br />";
			}
			if ($_POST[dbname]) {
				edit_file ("../config.php","DB_NAME","define",";","define('DB_NAME', '$_POST[dbname]')");
			} else {
				$error .= "You must specify a database name<br />";
			}
			if (!$_POST[dbuser]) $error .= "You must specify a database user<br />";
			if ($_POST[dbpass]) {
				edit_file ("../config.php","DB_PASSWORD","define",";","define('DB_PASSWORD', '$_POST[dbpass]')");
			} else {
				$error .= "You must specify a database password<br />";
			}
			if ($_POST[dbcharset]) {
				edit_file ("../config.php","DB_CHARSET","define",";","define('DB_CHARSET', '$_POST[dbcharset]')");
			} else {
				$error .= "You must specify a database character set<br />";
			}
			if ($_POST[adminuser]) {
				edit_file ("../config.php","ADMIN_USERNAME","define",";","define('ADMIN_USERNAME', '$_POST[adminuser]')");
			} else {
				$error .= "You must specify an admin username<br />";
			}
			if ($_POST[adminpass]) {
				edit_file ("../config.php","ADMIN_PASSWORD","define",";","define('ADMIN_PASSWORD', '$_POST[adminpass]')");
			} else {
				$error .= "You must specify an admin password<br />";
			}
			if ($_POST[fckpath]) edit_file ("../config.php","FCK_PATH","define",";","define('FCK_PATH', '$_POST[fckpath]')");
			if ($_POST[cleanit]=="Y" || $_POST[cleanit]=="N") {
				edit_file ("../config.php","CLEANIT","define",";","define('CLEANIT', '$_POST[cleanit]')");
			} else {
				$error .= "cleanit value must be Y or N";
			}
			if ($error) {
				printMessage($error,"red");
			} else {
				// --- make the final config edit to the username since this is what I use to see if the installation has been installed...
				edit_file ("../config.php","DB_USER","define",";","define('DB_USER', '$_POST[dbuser]')");
				// -----------------------------------------------------------------------------
				// --- connect to DB with newly supplied info...
				// -----------------------------------------------------------------------------
				$aedb = new aedb($_POST[dbuser], $_POST[dbpass], $_POST[dbname], $_POST[dbhost]);
				$aedb->query("SET NAMES '$_POST[dbcharset]'");
				//print_r($aedb);
				if (!$aedb->dbh) {
					$error = "Some problem occurred connecting to the database";
					printMessage($error,"red");
				} else {
					echo "<br /><h1>Install complete - <a href=\"../\">Visit phpAddEdit Page</a></h1>\n";
				}
			}
		} else {
			echo "<br />";
			printMessage("'config.php' file is not writeable - please set the permission - chmod 777","red");
			exit;
		}
	}
	if (!$_POST["submit"] || $error) {
		if (!$_POST[dbhost]) $_POST[dbhost] = "localhost";
		if (!$_POST[dbcharset]) $_POST[dbcharset] = "UTF8";
		if (!$_POST[cleanit]) $_POST[cleanit] = "Y";
		?>
		<h1>Enter Your DB Connection Details</h1>
		<p>
			<label>Database host (sometimes 'localhost')<br />
			<input type="text" name="dbhost" class="input" value="<?php echo $_POST[dbhost] ?>" size="20" tabindex="10" /></label>
		</p>
		<p>
			<label>Database name (e.g., 'aedb')<br />
			<input type="text" name="dbname" class="input" value="<?php echo $_POST[dbname] ?>" size="20" tabindex="20" /></label>
		</p>
		<p>
			<label>Database username (e.g., 'root')<br />
			<input type="text" name="dbuser" class="input" value="<?php echo $_POST[dbuser] ?>" size="20" tabindex="30" /></label>
		</p>
		<p>
			<label>Database password<br />
			<input type="password" name="dbpass" class="input" value="<?php echo $_POST[dbpass] ?>" size="20" tabindex="40" /></label>
		</p>
		<p>
			<label>Database character set (e.g., 'UTF8')<br />
			<input type="text" name="dbcharset" class="input" value="<?php echo $_POST[dbcharset] ?>" size="20" tabindex="50" /></label>
		</p>

		<h1>...And Set Your Admin Login Info</h1>
		<p>
			<label>Administrator username<br />
			<input type="text" name="adminuser" class="input" value="<?php echo $_POST[adminuser] ?>" size="20" tabindex="60" /></label>
		</p>
		<p>
			<label>Administrator password<br />
			<input type="password" name="adminpass" class="input" value="<?php echo $_POST[adminpass] ?>" size="20" tabindex="70" /></label>
		</p>

		<h1>...And a few other settings</h1>
		<p>
			<label>FCK Editor Path (if you want to use an existing FCKeditor installation)<br />
			<input type="text" name="fckpath" class="input" value="<?php echo $_POST[fckpath] ?>" size="20" tabindex="80" /></label>
		</p>
		<p>
			<label>Use cleanit routine (Y/N)? [<a href='http://www.phpaddedit.com/' target='_blank'>more info</a>]<br />
			<input type="text" name="cleanit" class="input" value="<?php echo $_POST[cleanit] ?>" size="1" maxlength="1" tabindex="80" /></label>
		</p>

		<p class="submit">
			<input type="submit" name="submit" id="submit" value="Install" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
		</p>
		</form>

		</div>

		<?php
	}
}

echo "</form>\n";
echo "</div>\n";
echo "<div id=\"back\"><a href=\"/\">&laquo; Back to Site</a> &nbsp; || &nbsp; <a href=\"../\">phpAddEdit Panel &raquo;</a></div>\n";
?>

</body>
</html>