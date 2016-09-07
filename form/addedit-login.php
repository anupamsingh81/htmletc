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

$header = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
$header .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\" lang=\"en-US\">\n";
$header .= "<head>\n";
$header .= "<title> &rsaquo; Login</title>\n";
$header .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
$header .= "<link rel=\"stylesheet\" href=\"includes/login.css\" type=\"text/css\" />\n";
$header .= "</head>\n";
$header .= "<body class=\"login\">\n";

// ---------------------------------------------------------------------------
// --- check if installed already - if not don't allow use of admin panel
// ---------------------------------------------------------------------------
if (DB_USER=="x") {
	echo $header;
	echo "<br />\n";
	echo "<div id=\"login\">\n";
	echo "<form name=\"loginform\" id=\"loginform\" action=\"\" method=\"post\">\n";
	printMessage("It seems you have not yet run the <a href=\"install/\">installation routine</a>. Please do so before using the admin panel","red");
	echo "</form>\n";
	echo "</div>\n";
} else {
	if ($_POST["submit"]=="Log In") {
		if ($_POST["adminuser"]!=ADMIN_USERNAME) {
			$login_error = true;
			$usererror = "Invalid Admin Username"; 
		}
		if ($_POST["adminpass"]!=ADMIN_PASSWORD) {
			$login_error = true;
			$pwerror .= "Invalid Admin Password";
		}
		if (!$login_error) {
			// --- Set admin cookie so favorite form field will show up when I use the site...
			$cookievalue = substr($_POST["adminuser"],0,4) . "-" . substr($_POST["adminpass"],-4);
			if ($_POST["rememberme"]) {
				$expire = mktime(0,0,0,date("m"),date("d")+120,date("Y"));
				setcookie("addedit", $cookievalue, $expire, "/", "", 0);
			} else {
				setcookie("addedit", $cookievalue);
			}
			Header("Location:  ./");
		}
	}
	
	if (!$_POST["submit"] || $login_error) {
		echo $header;
		echo "<div id=\"login\">\n";
		echo "<form name=\"loginform\" id=\"loginform\" action=\"\" method=\"post\">\n";

		// ---------------------------------------------------------------------------
		// --- show any error message...
		// ---------------------------------------------------------------------------
		if ($usererror) printMessage($usererror,"red");
		if ($pwerror) printMessage($pwerror,"red");

		// ---------------------------------------------------------------------------
		// --- show demo info on phpAddEdit site...
		// ---------------------------------------------------------------------------
		if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
			printMessage("To use this demo, enter <code>demo</code> for the username and password");
		}
		?>
		<h1>phpAddEdit Administrator Login</h1>
		<p>
			<label>Username<br />
			<input type="text" name="adminuser" class="input" value="<?php echo $_POST[adminuser] ?>" size="20" tabindex="10" /></label>
		</p>
		<p>
			<label>Password<br />
			<input type="password" name="adminpass" class="input" value="<?php echo $_POST[adminpass] ?>" size="20" tabindex="20" /></label>
		</p>
		<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php echo $checked ?> tabindex="90" /> Remember Me</label></p>
		<p class="submit">
			<input type="submit" name="submit" id="submit" value="Log In" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
		</p>
		</form>
		</div>
		<p id="back"><a href="/" title="back to main page">&laquo; Back to Site</a></p>
		<?php
	}
}
?>
</body>
</html>