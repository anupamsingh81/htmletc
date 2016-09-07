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

// =======================================================================================================================
// === File manipulation functions
// =======================================================================================================================

// --------------------------------------------------------------------------------------
// --- Read File 
// --------------------------------------------------------------------------------------
function read_file($file) {
	if (is_readable($file)) {
		//echo "is readable <br />";
		$lines = file($file);
		// Loop through our array, show HTML source as HTML source; and line numbers too.
		foreach ($lines as $line_num => $line) {
			//echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";
		}
	} else {
		printMessage("$file is not readable - please set the permission - chmod 0777","red");
		exit;
	} 
	return ($lines);
}

// --------------------------------------------------------------------------------------
// --- See if Text Exists in File 
// --------------------------------------------------------------------------------------
function text_exists($file,$str) {
	global $error;

	$found = false;
	$lines = read_file($file);
	//print_r($lines);

	foreach ($lines as $line_num => $line) {
		if (strstr($line,$str))	{
			//echo "found it " . $newline . "<br>";
			$found = true;
		}
	}
	clearstatcache();
	return ($found);
}

// --------------------------------------------------------------------------------------
// --- See if Line Exists in File 
// --------------------------------------------------------------------------------------
function line_exists($file,$str) {
	global $error;

	$found = false;
	$lines = read_file($file);
	//print_r($lines);

	foreach ($lines as $line_num => $line) {
		if ( substr($line,0,strlen($str))==$str ) {
			//echo "found it " . $str . "<br>";
			$found = true;
		}
	}
	clearstatcache();
	return ($found);
}

// --------------------------------------------------------------------------------------
// --- Get Variable from File 
// --------------------------------------------------------------------------------------
function get_variable($file,$tablefield,$search_str_begin,$search_str_separator,$search_str_end) {
	global $error;

	$found = false;
	$lines = read_file($file);
	$numchars = strlen($tablefield);
	//print_r($lines);

	foreach ($lines as $line_num => $line) {
		if ( substr($line,0,$numchars)==$tablefield ) {
			$truncated_line = substr($line,strpos($line,$search_str_begin));
			$startpos = strpos($truncated_line,$search_str_separator)+2;
			$stoppos = strpos($truncated_line,$search_str_end);
			$stoppos = $stoppos-$startpos;
			$variable = substr($truncated_line,$startpos,$stoppos);
			//echo $truncated_line . "<br>";
			//echo "found it " . $variable . "; $startpos; $stoppos<br>";
			$found = true;
		}
	}
	clearstatcache();
	return ($variable);
}

// --------------------------------------------------------------------------------------
// --- Get Variables from File 
// --- Basically, look for initial string, explode that line into an array and return ---
// --------------------------------------------------------------------------------------
function get_variables($match,$explodekey=" ") {
	//echo "match is " . $match . "<br />";
	//echo "form variables file is " . FORM_VARIABLES . "<br>";
	$numchars = strlen($match);
	$lines = read_file(FORM_VARIABLES);
	foreach($lines as $index=>$value) {
		//echo $index . " - " . $value . "<br />";
		if ( substr($value,0,$numchars)==$match ) {
			$temp = explode($explodekey,$value);
			//echo $value . "<br>";
			//print_r($temp);
			//echo "<br>";
			for ($i=1; $i<count($temp); $i++) {
				if ($temp[$i]) $variables[$i-1] = trim($temp[$i]);
			}
		}
	}
	//print_r($variables); echo "<br />";
	return ($variables);
}


// --------------------------------------------------------------------------------------
// --- Get Entire File Content 
// --------------------------------------------------------------------------------------
function get_file_contents($file) {
	//echo "match is " . $match . "<br>";
	$numchars = strlen($match);
	$lines = read_file($file);
	foreach($lines as $index=>$value) {
		$contents .= $value;
	}
	return ($contents);
}


// --------------------------------------------------------------------------------------
// --- Edit File 
// --------------------------------------------------------------------------------------
function edit_file($file,$line_id_str,$search_str_begin,$search_str_end="",$replace_str="") {
	global $error;

	$found = false;
	$last = false;
	$lines = read_file($file);
	//print_r($lines);

	if (is_writable($file)) {
		//echo "is writeable <br />";
		if (!$file_handle = fopen($file,"wb")) { 
		  echo "Cannot open file"; 
		} 
		foreach ($lines as $line_num => $line) {
			if (strstr($line,$line_id_str)) {
				if (strstr($line,$search_str_begin)) {
					//echo "<strong>found it </strong>" . $line . "<br>";
					//echo $search_str_begin . " - " . $replace_str . "<br>";
					$startpos = strpos($line,$search_str_begin);
					$truncated_line = substr($line,$startpos);
					if ( stristr($truncated_line,$search_str_end) ) {
						$search_str = substr($truncated_line,0,strpos($truncated_line,$search_str_end));
					} else {
						$last = true;
						$search_str = $truncated_line;
					}
					$line = str_replace($search_str,$replace_str,$line);
					if ($last) $line .= "
";
					//echo "<br>search_str_begin is " . $search_str_begin . " ($startpos)<br>";
					//echo "truncated line is " . $truncated_line . "<br>";
					//echo "search string is " . $search_str . "<br>";
					//echo "new line is " . $line . "<br><br>";
					$found = true;
				}
			}
			fwrite($file_handle, $line);
		}
		fclose($file_handle);   
	} else {
		$error = "$file file is not writeable - please set the permission - chmod 777";
		printMessage("$file file is not writeable - please set the permission - chmod 777","red");
	}
	clearstatcache();
	return ($found);
}

// --------------------------------------------------------------------------------------
// --- Replace Line in File 
// --------------------------------------------------------------------------------------
function replace_line($file,$str,$newline) {
	global $error;

	$found = false;
	$lines = read_file($file);
	//print_r($lines);

	if (is_writable($file)) {
		//echo "is writeable <br />";
		if (!$file_handle = fopen($file,"wb")) { 
		  echo "Cannot open file"; 
		} 
		foreach ($lines as $line_num => $line) {
			//echo "<strong>line $line_num</strong>: " . $line . "<br>";
			//if ($line=="") echo "empty";
			//if (strstr($line,$str))	{
			if ( substr($line,0,strlen($str))==$str ) {
				//echo "found it " . $newline . " (str = $str) <br>";
				fwrite($file_handle, $newline);
				$found = true;
			} else {
				//if (!empty($line) && $line!="&#13" && $line!="" && $line!=chr(13) && $line!=chr(10) && $line!="\n" && $line!="\r" && $line!="\n\r") echo "line is " . $line;
				if ($line) fwrite($file_handle, $line);
			}
		}
		fclose($file_handle);   
	} else {
		$error = "$file file is not writeable - please set the permission - chmod 777";
		printMessage("$file file is not writeable - please set the permission - chmod 777","red");
	}
	clearstatcache();
	return ($found);
}

// --------------------------------------------------------------------------------------
// --- Append File 
// --------------------------------------------------------------------------------------
// Must pass the content to append as an array of lines...
function append_file($file,$newlines="") {
	$lines = read_file($file);
	//print_r($lines);
	//echo $newlines."<br />";

	// Now append the newlines to any existing already...
	$lines = array_merge ($lines, (array)$newlines);	// --- modified to accomodate PHP5.x - 11/11/07 - JB

	if (is_writable($file)) {
		//echo "is writeable <br />";
		if (!$file_handle = fopen($file,"wb")) { 
		  echo "Cannot open $file"; 
		} 
		foreach ($lines as $line_num => $line) {
			fwrite($file_handle, $line);
		}
		fclose($file_handle);   
	} else {
		printMessage("$file file is not writeable - please set the permission - chmod 0777","red");
	}

	clearstatcache();
}

// --------------------------------------------------------------------------------------
// --- Write to a File 
// --------------------------------------------------------------------------------------
function write_file($file,$content) {
	if (!$file_handle = @fopen($file,"w+")) { 
	  printMessage("$file file could not be created - please set the directory permission - chmod 777","red");
	  exit;
	}   
	if ($content && !fwrite($file_handle, $content)) { 
	  echo "Error Writing to file $file"; 
	}
	fclose($file_handle);   
	clearstatcache();
}


// --------------------------------------------------------------------------------------
// --- Get Directory Files 
// --------------------------------------------------------------------------------------
function get_dirlist($start_dir) { 
 exec("ls -R $start_dir",$f_list); 
 $dir_str = $start_dir; 
 $filelist[0] = $start_dir; $i = 1; 
 for ($count=0; $count<count($f_list); $count++) { 
   if ($f_list[$count] == "") { continue; } 
   if (substr($f_list[$count],strlen($f_list[$count])-1,1) == ":") { 
     $dir_str = substr($f_list[$count],0,strlen($f_list[$count])-1); 
     $filelist[$i] = $dir_str; 
     $i++; 
   } else { 
     $file_str = "$dir_str/$f_list[$count]"; 
     if (is_file($file_str)) { 
       $filelist[$i] = $file_str;  
       $i++; 
     } 
   } 
 } 
 return $filelist; 
}



// =======================================================================================================================
// === Basic functions
// =======================================================================================================================

// --------------------------------------------------------------------------------------
// --- printMessage 
// --------------------------------------------------------------------------------------
// Use printMessage to display any error or other relevant messages...
// you can pass your own class for the div if you have one setup in CSS already o/w 
// use the default style (1 px grey border, red text, beige background).
//
if(!function_exists('printMessage')) {
  function printMessage($message,$color="#000000",$format="",$fontsize="14px",$weight="normal",$bgcolor="#FFFFF0") {
	global $website;
	$thisdir = "/".substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1);
	$alert = "http://" . $website . $thisdir . "/images/info2.gif";
	if ($format=="bold") {
		$weight = "bold";
		$format = "";
	}
	if ($color=="red" || $bgcolor=="red") {
		$fontsize = "16px";
		$alert = "http://" . $website . $thisdir . "/images/alert.gif";
		$style = "style=\"font-size: $fontsize; text-align: left; color: $color; font-weight: $weight; background-color: $bgcolor; background-image: url('$alert'); background-repeat: no-repeat; background-position: 5px 12px; margin: 0px 0px 5px 0px; border: 1px solid #ccc; padding: 8px; padding-left: 28px;\"";
	} else {
		$style = "style=\"font-size: $fontsize; text-align: left; color: $color; font-weight: $weight; background-color: $bgcolor; background-image: url('$alert'); background-repeat: no-repeat; background-position: 5px 8px; margin: 0px 0px 5px 0px; border: 1px solid #ccc; padding: 8px; padding-left: 28px;\"";
	}
	if ($format) {
		$format = "class=\"".$format."\"";
	} else {
		$format = $style;
	}
	echo "<div $format>$message<br/></div>\n";
  }
}

// --------------------------------------------------------------------------------------
// --- cleanup 
// --- Usage: use to fix various special html entities in a string 
// --------------------------------------------------------------------------------------
if(!function_exists('cleanup')) {
  function cleanup($str) {
  	global $encoding;
	if (!$encoding) $encoding = "ISO-8859-1";
	$str = htmlentities ($str, ENT_QUOTES, $encoding);
	$str = html_entity_decode($str, ENT_QUOTES, $encoding);

	$str = str_replace("&hellip;","...",$str);
	$str = str_replace("&#9675;","&middot;",$str);
	$str = str_replace("&#61607;","&middot;",$str);

	// --- the following lines *should* be unnecessary...
	$str = str_replace("&#8217;","'",$str);
	$str = str_replace("&rsquo;","'",$str);
	$str = str_replace("&lsquo;","'",$str);
	$str = str_replace('&quot;','"',$str);
	$str = str_replace('&ldquo;','"',$str);
	$str = str_replace('&rdquo;','"',$str);
	$str = str_replace('&amp;#','&#',$str);
	return $str;
  }
}

// --------------------------------------------------------------------------------------
// --- refresh_page
// --------------------------------------------------------------------------------------
if(!function_exists('refresh_page')) {
  Function refresh_page($page,$wait=6000) {
	echo "<script language=\"javascript\">";
	echo "checkit = self.location.href;";
	echo "if(!checkit.match('xx'))  {";
	if ($page) {
		echo "  setTimeout(\"location.href='" . $page . "'\", $wait);";
	} else {
		echo "  setTimeout(\"location.href='" . $PHP_SELF . "?xx'\", $wait);";
	}
	echo "}";
	echo "</script>";
  }
}

// --------------------------------------------------------------------------------------
// --- createthumb 
// --------------------------------------------------------------------------------------
function createthumb($name,$filename,$new_w,$new_h){
	$system=explode('.',$name);
	if (preg_match('/jpg|jpeg/',$system[1])) {
		$src_img=imagecreatefromjpeg($name);
	}
	if (preg_match('/png/',$system[1])) {
		$src_img=imagecreatefrompng($name);
	}

	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);
	if ($old_x > $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$old_y*($new_h/$old_x);
	}
	if ($old_x < $old_y) {
		$thumb_w=$old_x*($new_w/$old_y);
		$thumb_h=$new_h;
	}
	if ($old_x == $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$new_h;
	}

	$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 

	if (preg_match("/png/",$system[1])) {
		imagepng($dst_img,$filename); 
	} else {
		imagejpeg($dst_img,$filename); 
	}
	imagedestroy($dst_img); 
	imagedestroy($src_img); 
}

// =======================================================================================================================
// === Email functions
// =======================================================================================================================

// --------------------------------------------------------------------------------------
// --- php mailer 
// --------------------------------------------------------------------------------------
function phpaddeditmailer ($mailTo, $mailCC="", $mailSubject="Addedit Notification", $mailBody="", $attachment="", $attachment_name="") {
  global $email_engine, $smtp_host, $smtp_auth, $smtp_user, $smtp_pass, $email_from, $email_from_name, $email_reply, $email_bounce, $email_format, $email_include;
  require_once (dirname(__FILE__)."/phpmailer/class.phpmailer.php");

  $host = $_SERVER['HTTP_HOST'];
  if (substr($host,0,4)=="www.") $host = substr($host,4);
  $mailer = new phpmailer();

  // --- include body include file if specified...
  if ($email_include) require (getenv('DOCUMENT_ROOT').$email_include);

  $mailer->SetLanguage("en",dirname(__FILE__)."/phpmailer/");

  if (!$email_engine) { $temp = get_variables("email_engine "," || "); $email_engine = $temp[0]; }
  if ($email_engine=="smtp") {
	$mailer->Mailer = "smtp";
	$mailer->Host = $smtp_host;	
	$mailer->SMTPAuth = $smtp_auth;
	$mailer->Username = $smtp_user;
	$mailer->Password = $smtp_pass;
  }

  if ($mailCC) $mailer->AddCC($mailCC);

  if (!$email_from) { $temp = get_variables("email_from "," || "); $email_from = $temp[0]; }
  if (!$email_from) $email_from = "addedit_mailer@".$host;
  $mailer->From = $email_from;

  if (!$email_from_name) { $temp = get_variables("email_from_name"," || "); $email_from_name = $temp[0]; }
  if ($email_from_name) {
  	$mailer->FromName = $email_from_name;
  } else {
  	$mailer->FromName = "Mailer";
  }
  $mailer->AddAddress($mailTo);
  if (!$email_reply) { $temp = get_variables("email_reply"," || "); $email_reply = $temp[0]; }
  if (!$email_reply) $email_reply = "phpaddedit@".$host;
  $mailer->AddReplyTo($email_reply);
  if (!$email_bounce) $email_bounce = "bounce@".$host;
  $mailer->Sender = $email_bounce;

  $mailer->WordWrap = 50;
  $mailer->Subject = $mailSubject;

  if (!$body) $body = $mailBody;
  if ($email_format=="text") {
	$mailer->isHTML(false);
	$body = strip_tags($body);
  	$mailer->isHTML(false);
  } else {
	$mailer->isHTML(true);
  	require (dirname(__FILE__)."/includes/email_template.php");
	// ------- Use the code below if you want to have plain-text option for non-HTML clients ----------
	// Plain text body (for mail clients that cannot read HTML)
	$text_body = "If you are reading this, your email program doesn't handle HTML. Please contact us for more details.";
	$mailer->AltBody = $text_body;
  }
  $mailer->Body = $body;

  if ($attachment) {
	$attachment = getenv('DOCUMENT_ROOT').$attachment;
  	$mailer->AddAttachment($attachment,$attachment_name); 
  }

  $mailer->HeaderLine("X-Mailer", "phpAddEdit [ http://www.phpaddedit.com/ ]");

  if(!$mailer->Send()) {
	echo "Email Message could not be sent.";
	echo "Mailer Error: " . $mailer->ErrorInfo;
  }

  // Clear all addresses and attachments for next loop
  $mailer->ClearAddresses();
  $mailer->ClearAttachments();
}

// --------------------------------------------------------------------------------------
// --- send email 
// --------------------------------------------------------------------------------------
Function send_email($email_number, $email_to, $email_cc, $email_subject, $email_body, $email_body_default, $attachment, $attachment_name) {
	global $editing, $tablename, $formname, $email_format, $email_include, $email1_include, $email2_include;
	
	if (!$email_format) { $temp = get_variables("email_format"," || "); $email_format = $temp[0]; }
	if (!$email_body_default) $email_body_default = "No";
	if (!$tablename) { $temp = get_variables("tables"); $tablename = $temp[0]; }
	if (!$formname) $formname = substr(FORM_NAME,0,strlen($formname)-4);
	//echo "tablename is " . $tablename . "; email_format is " . $email_format . "; email_body_default is " . $email_body_default."; formname is " . $formname . "<br>";

	// --- include a body include file if specified...
	if ($email_number=="1" && $email1_include) $email_include = $email1_include;
	if ($email_number=="2" && $email2_include) $email_include = $email2_include;
	
	$status = "submitted";
	if ($editing) $status = "edited";
	if (!$email_subject) $email_subject = ucfirst($tablename) . " Has Been " . ucfirst($status) . " :)";
	if ($email_body_default=="Yes") {
		$email_body_html = "An entry with the following info was " . $status . ":<br /><br />\n";
		$email_body_text = "An entry with the following info was " . $status . "\n\n";
		foreach($_POST as $index=>$value) {
			if (is_array($value)) {
				$email_body_html .= "<strong>" . str_replace($tablename."_","",$index) . "</strong>: ";
				$email_body_html .= print_r($value, true) . "<br />\n";
			} else {
				$email_body_html .= "<strong>" . str_replace($tablename."_","",$index) . "</strong>: " . nl2br(stripslashes($value)) . "<br />\n";
			}
			$email_body_text .= str_replace($tablename."_","",$index) . ": " . strip_tags($value) . "\n\n";
		}
		foreach($_FILES as $index=>$value) {
			$email_body_html .= "<strong>File</strong>: " . $_FILES[$index]['name'] . " (" . $_FILES[$index]['size'] . ")<br />\n";
			$email_body_text .= "File: " . $_FILES[$index]['name'] . " (" . $_FILES[$index]['size'] . ")\n\n";
		}
		$email_body = $email_body_html;
		if ($email_format=="text") $email_body = $email_body_text;
	} else {
		$email_body = "";
		$email_body_file = $formname."_email".$email_number.".php";
		$email_body_array = read_file($email_body_file);
		foreach ($email_body_array as $index=>$line) {
			if (strstr($line,"[[")) {
				$start = strpos($line,"[[")+3;
				$stop = strpos($line,"]]");
				$temp = substr($line,$start,$stop-$start);
				$replace = "[[$".$temp."]]";
				$temp = $tablename . "_" . substr($line,$start,$stop-$start);
				$line = str_replace($replace,strip_tags($_POST[$temp]),$line);
			}
			$email_body_html .= $line . "<br />";
			$email_body_text .= strip_tags($line) . "";
		}
		$email_body = $email_body_html;
		if ($email_format=="text") $email_body = $email_body_text;
	}

	phpaddeditmailer ($email_to, $email_cc, $email_subject, $email_body, $attachment, $attachment_name);
}


// =======================================================================================================================
// === Database functions
// =======================================================================================================================

// --------------------------------------------------------------------------------------
// --- Generate SQL 
// --------------------------------------------------------------------------------------
function generate_sql ($addedit_type, $tablename, $fields, $primarykey, $primarykey_value, $row="") {
	global $db, $relID, $$relID, $insert_id, $first_insert_id, $error_message, $detail_debug, $files_find_string, $files_replace_string, $addeditdir, $formname;

	$$primarykey = $primarykey_value;

	// --------------------------------------------------------------------------------------
	// --- Get POST variables & set key useful variables (since we're in a function...)
	// --------------------------------------------------------------------------------------
	foreach($_POST as $index=>$value) {
		$value = @stripslashes($value);		// added @ 7-13-2011 for warnings in new version of PHP but someday should investigage instead
		$$index = $value; 
		if ($detail_debug) echo $index . " - " . $value . "<br />";
	}

	// ---------------------------------
	// --- just stuff for debugging...
	// ---------------------------------
	// echo "editing is " . $editing . "<br>";
	// print_r($_FILES); echo "<br><br>";
	// print_r($fields); echo "<br><br>";
	// echo "" . $tablename . " primary key is " . $primarykey . " - " . $$primarykey ."<br>";
	// echo "relID is " . $relID . " - " . $$relID . " - " . $_POST[$tablename."_".$relID] . "<br><br>";
	// print_r($fields) . "<br />";
	// ---------------------------------
	
	// -----------------------------------------------------------------------------
	// --- Get next increment for the table $tablename...could be useful...
	// -----------------------------------------------------------------------------
	$next_increment 	= 0;
	$qShowStatus 		= "SHOW TABLE STATUS LIKE '$tablename'";
	$qShowStatusResult 	= mysql_query($qShowStatus) or die ( "Query failed: " . mysql_error() . "<br/>" . $qShowStatus );
	$row = mysql_fetch_assoc($qShowStatusResult);
	$next_increment = $row['Auto_increment'];
	//echo "next increment number: [$next_increment]<br>";

	$valid_insert = false;	// --- use this variable for cases where no values exist so we should not do an insert...
	$valid_update = false;	// --- use this variable for cases where no values exist so we should not do an update...
	foreach($fields as $key=>$field) {
		$var = $row->$field;
		$tablefield = $tablename."_".$field;
		$tablefield_other = $tablename."_".$field."_other";
		$element = get_variable(FORM_VARIABLES,$tablefield." ","element","=>"," ||");
		$populatestr = trim(get_variable(FORM_VARIABLES,$tablefield." ","populatestr","=>"," ||"));
			eval("\$populatestr = \"$populatestr\";");
		$populatevariables = get_variable(FORM_VARIABLES,$tablefield." ","populatevariables","=>"," ||");
		$popvar1 = substr($populatevariables,0,strpos($populatevariables,"=>"));
		$popvar2 = substr($populatevariables,strpos($populatevariables,"=>")+2);
		if ($_POST[$tablefield]) {
			$$tablefield = $_POST[$tablefield];
		} else {
			$$tablefield = $var;
		}
		// --- handle case where value is a zero (PHP converts it to boolean false automatically)
		if ($_POST[$tablefield]=="0") $$tablefield = "0";

		if (!$$tablefield) {
			if ($_GET[$tablefield]) $$tablefield = $_GET[$tablefield];
			if ($tablefield == $primarykey) $tablefield = $primarykey;
		}

		// -----------------------------------------------------------------------------
		// --- just used during debugging...
		if ($detail_debug) echo "key - " . $key . "; tablefield - " . $tablefield . " - " . $$tablefield . "; element - " . $element . "; var - " . $var . "<br />";
		// -----------------------------------------------------------------------------

		// -----------------------------------------------------------------------------
		// --- cleanup textbox or textarea field
		// -----------------------------------------------------------------------------
		if ($element=="textbox" || $element=="textarea") {
			$$tablefield = cleanup($$tablefield);
			$$tablefield = addslashes(stripslashes($$tablefield));
		}

		// -----------------------------------------------------------------------------
		// --- Handle special elements (hidden, selectbox_other, selectbox_multiple, etc)
		// -----------------------------------------------------------------------------

		// ------------------------------------
		// --- Handle hidden fields
		// ------------------------------------
		if ($element=="hidden") {
			$default = stripslashes(get_variable(FORM_VARIABLES,$tablefield." ","default","=>"," ||"));
			// -----------------------------------------------------------------------------
			// --- secure the demo by not allowing any eval() statements on phpaddedit.com
			// -----------------------------------------------------------------------------
			//echo $formname."<br />";
			if ( substr($default,0,1)=="=" && (getenv('HTTP_HOST')=="www.phpaddedit.com" || getenv('HTTP_HOST')=="phpaddedit.com") && ($formname!=$addeditdir."/forms/wordpress_content") ) {
				$default = "";
				printMessage("You specified a php statement as the default value for <code>$field</code>. That feature is disabled in this demo.","red");
			} else {
				if (substr($default,0,1)=="=") $default = substr($default,1);
				if (substr($default,-1)!=";") $default .= ";";
				//echo $default . " - ";
				if (strstr($default,"=")) {
					eval("\$default = \"$default\"; ");
				} elseif ($default==";") {
					$default = "";
				} else {
					eval("\$default = $default");
				}
			}
			//echo "default is " . $default . "<br>";
			// -----------------------------------------------------------------------------
			$$tablefield = $default;
			if (!$$tablefield) $$tablefield = $_POST[$tablefield];
		}

		// ------------------------------------
		// --- Handle MD5 password fields
		// ------------------------------------
		if ($element=="passwordmd5") $_POST[$tablefield] = $$tablefield = md5($_POST[$tablefield]);
		
		// ------------------------------------------------------------------
		// --- Get relative ID (relID) for the table $tablename...
		// ------------------------------------------------------------------
		$relID = get_variable(FORM_VARIABLES,$tablefield." ","relID","=>"," ||");

		// --------------------------------------------------------------------------------------------------
		// --- Handle selectbox fields for relID - if a relID exists, create special insert_sql o/w ignore...
		// --------------------------------------------------------------------------------------------------
		if ($element=="selectbox") {
			if ($relID) {
				if ($_POST[$tablename."_".$relID]) {
					$$relID = $_POST[$tablename."_".$relID];
				} else {
					if (!$$relID) $$relID = $first_insert_id;
				}
				//echo $tablefield . " - relID is " . $relID . " - " . $$relID . " - " . $_POST[$tablename."_".$relID] . "<br>";
				$insert_sql .= "INSERT INTO $tablename (" . $relID . "," . $field . ") VALUES (\"" . $$relID . "\",\"" . $_POST[$tablefield] . "\");;\n ";
			}
		}

		// --------------------------------------------------------------------------------------------------
		// --- FCKedit is a good editor, even converting from MS Word - but still some cleanup is helpful...
		// --- NOTE: make sure your directory is writeable b/c we need to use temp file
		// --------------------------------------------------------------------------------------------------
		$cleanit = "N";
		if (defined('CLEANIT')) $cleanit = CLEANIT;
		//echo "cleanit is " . $cleanit;
		if ($element=="textarea_FCKedit" && $$tablefield && $cleanit=="Y" ) {
			include_once("includes/cleanit-functions.php");
			$temp_file = "temp.htm";
			write_file($temp_file,stripslashes($$tablefield));
			$lines = read_file($temp_file);
			//echo "lines: "; print_r($lines); echo "<br />";
			$converted_lines = cleanit($lines);
			//echo "converted lines: " . $converted_lines . "<br>";
			$$tablefield = addslashes(stripslashes($converted_lines));
		}

		// ------------------------------------
		// --- Handle selectbox_multiple fields
		// ------------------------------------
		if ($element=="selectbox_multiple" || $element=="selectbox_multiple_other") {
			if (count($_POST[$tablefield])>1) {
				$$tablefield = implode(",", $_POST[$tablefield]);
			} else {
				$$tablefield = $_POST[$tablefield][0];
			}
		}

		// ------------------------------------
		// --- Handle selectbox_multirow fields
		// ------------------------------------
		if ($element=="selectbox_multirow" || $element=="selectbox_multirow_other" || $element=="file_upload_ajax") {
			if (!$$relID) {
				if ($_POST[$tablename."_".$relID]) {
					$$relID = $_POST[$tablename."_".$relID];
				} else {
					$$relID = $first_insert_id;
				}
			}
			if ($_POST[$tablefield]) {
				// --- if updating - first delete all existing entries and then add all current selections - that's the easiest I think...
				if ($$primarykey) {
					$sql = "DELETE FROM $tablename WHERE $primarykey='".mysql_real_escape_string($$primarykey)."';;\n ";
					$valid_update = true;
				}
				foreach($_POST[$tablefield] as $x=>$y) {
					//$insert_sql .= "INSERT INTO $tablename (" . $relID . "," . $field . ") VALUES (\"" . $first_insert_id . "\",\"" . $y . "\");;\n ";
					if ($$relID && $y) {
						$sql .= "INSERT INTO $tablename (" . $relID . "," . $field . ") VALUES (\"" . $$relID . "\",\"" . $y . "\");;\n ";
						//$sql .= "INSERT INTO $tablename (" . $primarykey . "," . $field . ") VALUES (\"" . $$primarykey . "\",\"" . $y . "\");;\n ";
						$valid_insert = true;
					}
				}
				$insert_sql .= $sql;
				$update_sql .= $sql;
			} else {
				$insert_sql = "N/A";
				$update_sql = "N/A";
			}

			// -----------------------------------------------------------------------------
			// --- just used during debugging - should remove someday...
			// -----------------------------------------------------------------------------
			// echo $tablefield . " - relID is " . $relID . " - " . $$relID . " - " . $_POST[$tablename."_".$relID] . "<br>";
			// print_r($_POST[$tablefield]); echo "<br>";
			// -----------------------------------------------------------------------------

		}
		// -----------------------------------------------------------------------------

		// -----------------------------------------------------------------------------
		// --- modify any textarea entries that have character limits 
		// -----------------------------------------------------------------------------
		$temp = get_variables($tablefield." "," || ");
		if (stristr($temp[0],"textarea")) {
			$maxlen = get_variable(FORM_VARIABLES,$tablefield." ","maxlen","=>"," ||");
			if ($maxlen) $$tablefield = substr($$tablefield,0,$maxlen);
			$maxlen = 0;
		}
		// -----------------------------------------------------------------------------


		// -----------------------------------------------------------------------------
		// --- Upload any files to the directory specified in the variables file
		// -----------------------------------------------------------------------------
		if ($_FILES[$tablefield]['name']) {
			for ($i=0; $i<count($temp); $i++) {
				if ( substr($temp[$i],0,7)=="filedir" ) $filedir = substr($temp[$i],strpos($temp[$i],"=>")+2);
			}
			if (substr($filedir,0,1)!="/") $filedir = "/" . $filedir;
			if (substr($filedir,-1)!="/") $filedir = $filedir . "/";
			$$tablefield = $filedir.$_FILES[$tablefield]['name'];
			if ($detail_debug) echo "filename is: " . $_FILES[$tablefield]['name'] . "tmp filename is: " . $_FILES[$tablefield]['tmp_name'] . "; file location is: " . $$tablefield . "<br />";
			copy ($_FILES[$tablefield]['tmp_name'], getenv('DOCUMENT_ROOT').$$tablefield) or die ("Could not copy file $_FILES[$key]['name']"); 
			
			// --- check if user has specified a string replace action 
			if ($files_find_string && files_replace_string) $$tablefield = str_replace($files_find_string,$files_replace_string,$$tablefield);

		}
		// -----------------------------------------------------------------------------

		// -----------------------------------------------------------------------------
		// --- handle file_upload_ajax_single - if we don't set the 
		// --- filename it will just read 'Array'
		// -----------------------------------------------------------------------------
		if ($element=="file_upload_ajax_single") {
			$$tablefield = $_POST[$tablefield] = $_POST[$tablefield][0];
		}
		
		$insert_sql_fields .= $field . ", ";
		$insert_sql_values .= "\"" . @addslashes(stripslashes($$tablefield)) . "\", ";	// added @ 7-13-2011 for warnings but should investigate
		if ($$tablefield) $valid_insert = true;
		
		//echo $tablename . " field " . $field . " - " . $$tablefield . "<br>";
		if ($element=="file_upload" && !$_FILES[$tablefield]['name']) {		
			// --- let's not update a file upload field if we didn't actually update it...
		} else {
			$update_sql_fieldvalue .= $field . "=\"" . $$tablefield . "\", ";
		}
		if ($$tablefield) $valid_update = true;
	} 

	if (!$update_sql) $update_sql = "UPDATE $tablename SET " . substr($update_sql_fieldvalue,0,strlen($update_sql_fieldvalue)-2) . " WHERE $primarykey='".mysql_real_escape_string($$primarykey)."'";
	if (!$insert_sql) $insert_sql = "INSERT INTO $tablename (" . substr($insert_sql_fields,0,strlen($insert_sql_fields)-2) . ") VALUES (" . substr($insert_sql_values,0,strlen($insert_sql_values)-2) . ")";
	if ($insert_sql=="N/A" || $valid_insert==false) $insert_sql = "N/A";
	if ($update_sql=="N/A" || $valid_update==false) $update_sql = "N/A";
	//echo $update_sql;
	//echo $insert_sql;

	if ($addedit_type=="edit") return($update_sql);
	if ($addedit_type=="add") return($insert_sql);
}


// =======================================================================================================================
// === Other functions
// =======================================================================================================================

// --------------------------------------------------------------------------------------
// --- chmod
// --- Set correct file permissions - found in a wordpress distribution - not using now
// --------------------------------------------------------------------------------------
if(!function_exists('chmod')) {
  function chmod($file) {
	$stat = stat(dirname($file));
	$perms = $stat['mode'] & 0000777;
	@ chmod($file, $perms);
  }
}


// --------------------------------------------------------------------------------------
// --- Check URL 
// --------------------------------------------------------------------------------------
function checkurl($url) {
	global $errorno, $errstr;
	//$connect = fsockopen($url, 80, &$errno, &$errstr, 30);
	$connect = fsockopen($url, 80, $errno, $errstr, 30);	// changed 8/8/07 to get rid of Call-time pass-by-reference error; also added global variables above - JB
	if(!$connect) {
		echo "<b>$url is a <font color=\"red\">dead link!</font></b>\n"; 
	} else {
		echo("<a href=\"http://".$url."\">".$url."</a>");
	}
}


#       get_http_headers()
#       v1.3 - 1999/06/08
#
#       Copyright (c) 1998,1999 easyDNS Technologies Inc.
#       http://www.easyDNS.com
#       info@easyDNS.com
#       All rights reserved.
#
#       This code provided "As Is" with no warrantees express or implied.
#       The author and contributors are not liable for anything good or
#       bad that results from your use of this code.
#
#       You are free to distribute this for free provided this notice is
#       included. Please forward fixes/enhancements to the author for
#       inclusion in the next revision.
#
#       USAGE:
#               array get_http_headers( str url, str [proto], int [timeout]);
#
#               url is in the form "http://www.somewhere.com" or "www.somewhere.com"
#               proto is optional, default is "HTTP/1.0"
#               timeout is optional, default is 10 (seconds)
#
#               array is an associative array with the keys set to the header name
#               (lowercase) with the first line of headers (the result line) 
#               split up into:
#                       $array[protocol] = protocol server used to answer
#                       $array[result] = i.e. 200 or 301 or 403 or 404, etc
#                       $array[message] = i.e. "OK" or "FORBIDDEN" etc.
#
#               $array[time_used] will be the approximate number of seconds the 
#               that the request took
#
#               If the request times out, $array[result] will be set to 502.
#
#               If the URL is invalid, false is returned
#
#
#       HISTORY:
#               
#               v1.3
#               1999/06/08
#               from Mark Jeftovic, markjr@easyDNS.com
#                       - reduced buffer size in polling loop to 80 from 128                    
#                       - setsockblocking = 0 by default but 1 if proto
#                         request is HTTP/1.1 (in which case our own 
#                         timeout is ignored for some reason)
#                       - only send Host header for HTTP/1.1 requests
#                       - stripped extra \n before Host header
#                       - moved parsing to parse_output() function so
#                         we can still return partial headers on a timeout
#
#               v1.2
#               1999/02/24
#               from Colin Viebrock, cmv@easyDNS.com
#                       Speed enhancements and rewrite:
#                       - check that host exists before trying to open socket
#                       - socket blocking for a timeout
#                       - returns $array[time_used] ... just for fun.
#
#               v1.1
#               1998/11/24
#               from Gary E. Bickford, garyb@slb.com
#                       Fix for when $path turns out to be null
#
#               v1.0
#               from Mark Jeftovic, markjr@easyDNS.com
#                       Original code
#
#

function get_http_headers($url, $proto="HTTP/1.0", $timeout=10) {
        $return = false;
        if (substr($url,0,7)=="http://") {
                $url = substr($url,7);
        }

        $parts = parse_url("http://".$url);
        
        $ips = gethostbynamel($parts["host"]);

        if ($ips[0]) {
                $ip = $ips[0];
                $host = $parts["host"];
                $path = ($parts["path"]) ? $parts["path"] : "/";
                $port = ($parts["port"]) ? $parts["port"] : 80;

                $start = time();
                $timeout = $timeout + $start;

                if($sock = fsockopen($host, $port)) {
                        set_socket_blocking($sock, 0);
                        switch($proto) {
                                case "HTTP/1.1":
                                        set_socket_blocking($sock, 1);
                                        fputs($sock, sprintf("HEAD %s %s\n", $path, $proto));
                                        fputs($sock, sprintf("Host: %s\n\n", $host));
                                        break;
                                default:
                                        fputs($sock, sprintf("HEAD %s %s\n\n", $path, $proto));
                                }

                        while(!feof($sock) && $t<$timeout) {
                                $line .= fgets($sock,1);
                                $t = time();
                        }
                        fclose($sock);
                        $end = time();

                        if ($t>=$timeout) {
                                $http = parse_output($line);
                                $http["result"] = 502;
                                $http["message"] = "Timed Out";
                                $http["time_used"] = $end - $start;
                                $return = $http;
                        } elseif($line) {
                                $http = parse_output($line);
                                $http["time_used"] = $end - $start;
                                $return = $http;
                        }
                }
        }
        return $return;
}

function parse_output($line) {
        $lines = explode("\n", $line);
        if(substr($lines[0],0,4)=="HTTP") {
        list($http["protocol"], $http["result"], $http["message"]) = split("[[:space:]]+",$lines[0],3);
        } else if(substr($lines[0],0,7)=="Server:") {
                $http["server"] = substr($lines[0],8);
        }
        for ($i=1; $i<count($lines); $i++) {
                list($key, $val) = split(":[[:space:]]*", $lines[$i],2);
                $key = strtolower(trim($key));
                if ($key) {
                        $http[$key] = trim($val);
                } else {
                        break;
                }
        }
        return($http);
};


$text['N/A'] = "URL is not HTTP";
$text['OK'] = "OK";
$text['FAIL'] = "FAIL";
$text['DNS'] = "Server not resolve";
$text['100'] = "Continue";
$text['101'] = "Switching Protocols";
$text['200'] = "OK";
$text['201'] = "Created";
$text['202'] = "Accepted";
$text['203'] = "Non-Authoritative Information";
$text['204'] = "No Content";
$text['205'] = "Reset Content";
$text['206'] = "Partial Content";
$text['300'] = "Multiple Choices";
$text['301'] = "Moved Permanently";
$text['302'] = "Found";
$text['303'] = "See Other";
$text['304'] = "Not Modified";
$text['305'] = "Use Proxy";
$text['307'] = "Temporary Redirect";
$text['400'] = "Bad Request";
$text['401'] = "Unauthorized";
$text['402'] = "Payment Required";
$text['403'] = "Forbidden";
$text['404'] = "Not Found";
$text['405'] = "Method Not Allowed";
$text['406'] = "Not Acceptable";
$text['407'] = "Proxy Authentication Required";
$text['408'] = "Request Timeout";
$text['409'] = "Conflict";
$text['410'] = "Gone";
$text['411'] = "Length Required";
$text['412'] = "Precondition Failed";
$text['413'] = "Request Entity Too Large";
$text['414'] = "Request-URI Too Long";
$text['415'] = "Unsupported Media Type";
$text['416'] = "Requested Range Not Satisfiable";
$text['417'] = "Expectation Failed";
$text['500'] = "Internal Server Error";
$text['501'] = "Not Implemented";
$text['502'] = "Bad Gateway";
$text['503'] = "Service Unavailable";
$text['504'] = "Gateway Timeout";
$text['505'] = "HTTP Version Not Supported";

if(!function_exists('slug')) {
  function slug ($str) {
	$special = array('/','!','&','*','@','$','%','^','(',')','{','}','?','<','>',',','.',':',';','|','[',']');
	$str = strtolower(str_replace(" ","-",stripslashes($str)));	
	$str = str_replace("'","",$str);
	$str = str_replace('"','',$str);
	$str = str_replace($special,'',$str);
	return $str;
  }
}
?>