<script type="text/javascript">
<!-- Code to make the submit button go inactive once pressed - prevents inpatient users from submitting more than once -->
<!-- for alternative way to do this see: http://javascript.internet.com/forms/universal-form-validator.html -->
function LockSubmit(formname, submitbutton) {
  eval('document.'+formname+'.'+submitbutton+'.value = "Please Wait..."');
  eval('document.'+formname+'.'+submitbutton+'.disabled=true');
  eval('document.'+formname+'.submit()');
  return true;
}

// ---------------------------
// --- Browser Support Code
// ---------------------------
function GetXmlHttpObject() {
  var xmlHttp=null;
  try {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
  }
  catch (e) {
    // Internet Explorer
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e) {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}

// ---------------------------
// --- akismetApiCheck
// ---------------------------
function akismetApiCheck(form) {
	//<![CDATA[
	ajaxRequest=GetXmlHttpObject();
	if (ajaxRequest==null) {
		alert ("Your browser does not support AJAX!");
		return;
	} 

	var apikey = form.akismet_key.value;

	// --- Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange=function() {
		if(ajaxRequest.readyState==4) {
			var split = ajaxRequest.responseText.split(" ");
			var start = ajaxRequest.responseText.indexOf("<");
			var response = ajaxRequest.responseText.substr(start);
			document.getElementById('apicheck').innerHTML=response;
	    } else {
			document.getElementById('apicheck').innerHTML='<span class="working" style="color:red">Validating...<\/span>';
		}
	}

	var url = "addedit-ajax.php?function=akismetapicheck&apikey="+apikey;

	<?php
	//if ($addeditdir) echo "url = \"" . $addeditdir . "\"+url;"; 
	if (substr($host,0,4)!="www.") $host = "www." . $host;
	if (substr($host,0,4)!="http") $siteURI = "http://" . $host;
	$addeditdir = "/".substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1)."/";
	echo "url = \"" . $siteURI . $addeditdir . "\"+url;"; 
	?>
	
	//alert(url);
	ajaxRequest.open("GET",url,true);
	ajaxRequest.send(null);
	//]]>
}

// ---------------------------
// --- ajaxAddOther
// ---------------------------
function ajaxAddOther(table,field,value,otherid,selectid,encoding) {
	//<![CDATA[
	var statusid = selectid+'-status';

	ajaxRequest=GetXmlHttpObject();
	if (ajaxRequest==null) {
		alert ("Your browser does not support AJAX!");
		return;
	} 

	var sel = document.getElementById(selectid);
	var other = document.getElementById(otherid);
	var othervalue = other.value;

	othervalue = othervalue.replace('&quot;','"');
	othervalue = othervalue.replace(',',' ');
	othervalue = othervalue.replace(';','\;');
	othervalue = othervalue.replace('+','\+');
	othervalue = othervalue.replace('$','\$');
	othervalue = othervalue.replace('?','\?');
	othervalue = othervalue.replace('^','\^');
	othervalue = othervalue.replace('*',' ');
	othervalue = othervalue.replace('=',' ');
	//alert(sel.options.length);

	// --- Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange=function() {
		if(ajaxRequest.readyState==4) {
			//sel.options[sel.options.length] = new Option(othervalue,othervalue,false,true);
			var split = ajaxRequest.responseText.split(" ");
			var otherid = split[0];
			var start = ajaxRequest.responseText.indexOf("<");
			var response = ajaxRequest.responseText.substr(start);

			// --- if we had a successful message, then let's add the "other" text input as a new selected option
			if (response.match("Added")) {
				// --- if option field and option display are different we assume the value is the next insert id which is returned from the php function, o/w just use the option display for display and value
				if (otherid!="" && field!=value) {
					sel.options[sel.options.length] = new Option(othervalue,otherid,false,true);
				} else {
					sel.options[sel.options.length] = new Option(othervalue,othervalue,false,true);
				}
			}
			document.getElementById(statusid).innerHTML=response;
	    } else {
			document.getElementById(statusid).innerHTML='<span class="working" style="color:red">Adding<\/span>';
		}
	}

	//var url = "addedit-ajax.php?function=addother&table="+table+"&field="+field+"&othervalue="+othervalue+"&encoding="+encoding;
	var url = "addedit-ajax.php?function=addother&table="+table+"&field="+field+"&othervalue="+othervalue+"&encoding="+encoding;
	url = url + "&addother_select_sql=<?php echo urlencode($addother_select_sql) ?>";
	url = url + "&addother_insert_sql=<?php echo urlencode($addother_insert_sql) ?>";

	<?php
	//if ($addeditdir) echo "url = \"" . $addeditdir . "\"+url;"; 
	if (substr($host,0,4)!="www.") $host = "www." . $host;
	if (substr($host,0,4)!="http") $siteURI = "http://" . $host;
	$addeditdir = "/".substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1)."/";
	echo "url = \"" . $siteURI . $addeditdir . "\"+url;"; 
	?>
	//alert(url);
	ajaxRequest.open("GET",url,true);
	ajaxRequest.send(null);
	//]]>
}

</script>

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

include_once ("addedit-functions.php");

// ====================================================================================================
// --- Pre-define every type of field and then just call based on the file type
// ====================================================================================================

// ----------------------------------------------------------------------------
// --- button
// ----------------------------------------------------------------------------
function button($img="", $height="", $width="", $align="middle", $alt="", $text="", $event) {
	printf("<button type=submit><img src=\"%s\" height=\"$height\" width=\"$width\" $align=\"$align\" alt=\"$alt\" %s />%s</button>\n",$event,$text);
}


// ----------------------------------------------------------------------------
// --- checkbox
// ----------------------------------------------------------------------------
function checkbox($name="", $list="", $option_value="", $option_display="", $default_value="", $event="", $align="vertical") {
	global $db;
	if (!isset($default_value) || $default_value=="") $default_value = stripslashes($_POST[$name]);
	//echo "default is " . $default_value;
	if (substr($list,0,7)=="select ") {
		$result = $db->get_results($list);
		foreach ($result as $myrow) {
			($myrow->$option_value==$default_value) ? $selected = " checked=\"checked\"" : $selected="";
			printf("<input type=\"checkbox\" name=\"%s\" value=\"%s\"$selected %s /> %s\n",$name,$myrow->$option_value,$event,$myrow->$option_display);
		}
	} else {
		if ( !is_array($list) ) {
			//echo "is an array";
			$list_array = explode(",",$list);
			//print_r($list_array);
		} else {
			$list_array = $list;
		}
		foreach($list_array as $key=>$field) {
			$value = substr($field,0,strpos($field,"=>"));
			$display = substr($field,strpos($field,"=>")+2);

			($value==$default_value) ? $selected = " checked=\"checked\"" : $selected="";
			printf("<input type=\"checkbox\" name=\"%s\" value=\"%s\"$selected %s /> %s\n",$name,$value,$event,$display);
			if ($align=="vertical") echo "<br />";
		}
	}
}


// ----------------------------------------------------------------------------
// --- date (3 separate dropdowns for mo-dd-yyyy)
// ----------------------------------------------------------------------------
function datefield($name="") {
	global $yearplus, $yearminus;
	if (!$yearplus) $yearplus = 10; if (!$yearminus) $yearminus = 10;
	
	$yyname = $name."yy";
	$yy = $thisyy = date("Y");
	if ($_POST[$yyname]) $yy = stripslashes($_POST[$yyname]);
	$moname = $name."mo";
	$mo = $thismo = date("m");
	if ($_POST[$moname]) $mo = stripslashes($_POST[$moname]);
	$ddname = $name."dd";
	$dd = $thisdd = date("d");
	if ($_POST[$ddname]) $dd = stripslashes($_POST[$ddname]);
		
	// --- Print labels
	printf("<table id=\"datefield\"><tr><td>Month</td><td>Day</td><td>Year</td></tr>\n");
	
	printf("<tr>\n");
	// --- Month dropdown
	$selectid = str_replace("_","-",$moname);
	printf("<td><select id=\"%s\" name=\"$moname\" size=\"1\">\n",$selectid);
		for ($i=1; $i<=12; $i++) {
			if ($i<10) {
				$zeroi = "0".$i;
			} else {
				$zeroi = $i;
			}
			($i==$mo) ? $selected = "selected=\"selected\"" : $selected="";
			$mo_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",$zeroi,$zeroi);
		}
		echo $mo_options;
	printf("</select></td>\n");

	// --- Day dropdown
	$selectid = str_replace("_","-",$ddname);
	printf("<td><select id=\"%s\" name=\"$ddname\" size=\"1\">\n",$selectid);
		for ($i=1; $i<=31; $i++) {
			if ($i<10) {
				$zeroi = "0".$i;
			} else {
				$zeroi = $i;
			}
			($i==$dd) ? $selected = "selected=\"selected\"" : $selected="";
			$dd_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",$zeroi,$zeroi);
		}
		echo $dd_options;
	printf("</select></td>\n");

	// --- Year dropdown
	$selectid = str_replace("_","-",$yyname);
	printf("<td><select id=\"%s\" name=\"$yyname\" size=\"1\">\n",$selectid);
		for ($i=$thisyy-$yearminus; $i<=$thisyy+$yearplus; $i++) {
			($i==$yy) ? $selected = "selected=\"selected\"" : $selected="";
			$yy_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",$i,$i);
		}
		echo $yy_options;
	printf("</select></td>\n");
	printf("</tr></table>\n");
}


// ----------------------------------------------------------------------------
// --- file upload
// --- NOTES: basic file upload - not AJAX 
// ----------------------------------------------------------------------------
function file_upload($name="", $default_value="", $size="25", $maxlen="255", $displayfile="N") {
	$value = stripslashes($_POST[$name]);
	if (!isset($value) || $value=="") $value = $default_value;
	($value) ? $value_display=$value : $value_display="none";
	if ($displayfile=="Y" && (substr($value,-4)==".gif" || substr($value,-4)==".jpg" || substr($value,-4)==".jpeg" || substr($value,-4)==".png") ) {
		echo "<a href=\"javascript:displayFile('$value',400,400);\"><img src=\"$value\" align=\"left\" style=\"width:50px; height:50px; border:1px solid black; margin-right:5px;\" alt=\"default file image\" /></a>\n";
	}
	printf ("Current File: %s<br />\n",$value_display);	
	printf("<input type=\"file\" name=\"%s\" size=\"$size\" maxlength=\"$maxlen\" />\n",$name);
	
	// --- we could create a hidden field to track the original value - not using now but could be useful in the future...
	//printf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />\n",$name."_orig",$value);
}

// ----------------------------------------------------------------------------
// --- file upload (AJAX)
// --- NOTES: this is really a multirow_other selectbox with the "other" being
// ---        a file upload which will automatically upload file(s) and update
// ---        the multirow selectbox.
// ----------------------------------------------------------------------------
function file_upload_ajax ($name="", $default_value="", $files_directory, $selectsize="50", $displayfile="N", $align="vertical") {
	global $addeditdir, $allowsetdir, $limituploads, $file_upload_ajax_type;

	//echo "files directory: " . $_SERVER['DOCUMENT_ROOT'].$files_directory;

	$value = stripslashes($_POST[$name]);
	if (!isset($value) || $value=="") $value = $default_value;
	($value) ? $value_display=$value : $value_display="none";
	//echo "default is "; print_r($default_value); echo "<br />";
	$idname = str_replace("_","-",$name);
	$previewid = str_replace("_","-",$name)."-preview";

	// open files directory 
	$myDirectory = opendir(getenv('DOCUMENT_ROOT').$files_directory);

	// get each entry
	while($entryName = readdir($myDirectory)) {
		$dirArray[] = $entryName;
	}

	// close directory
	closedir($myDirectory);

	// sort 'em
	sort($dirArray);

	for($index=0; $index < count($dirArray); $index++) {
		if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files

			if ($default_value) {
				if (is_array($default_value)) {
					(in_array($dirArray[$index],$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
				} else {
					(stristr($dirArray[$index],$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
				}
			}

			if ($selected) {
				$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($dirArray[$index]),cleanup($dirArray[$index]));
			} else {
				$options .= sprintf("<option value=\"%s\" onclick=\"loadImage('$idname','$files_directory');\">%s</option>\n",cleanup($dirArray[$index]),cleanup($dirArray[$index]));
			}

		}
	}

	echo "<div class=\"file-upload\">\n";
		echo "<div class=\"file-upload-left\">\n";
		if ($file_upload_ajax_type=="single") {
			printf("<select name=\"%s\" id=\"%s\" size=\"$selectsize\" %s>\n",$name."[]",$idname,$event);
		} else {
			printf("<select name=\"%s\" id=\"%s\" multiple=\"multiple\" size=\"$selectsize\" %s>\n",$name."[]",$idname,$event);
		}
			echo $selected_options;
			echo $options;
		printf("</select>\n");
		echo "</div>\n";

		$help = "Use the browse button to select a file to upload to the server. It should then automatically update the files listing for you to select.\\n\\nDouble-click to preview an image...";
		echo "<div class=\"file-upload-right\">\n";
		printf(" <a href=\"javascript:alert('%s')\"><img src=\"%s\" alt=\"Need to Know More?\" height=\"12\" width=\"12\" align=\"bottom\" hspace=\"2\" vspace=\"5\" border=\"0\" /></a>\n",$help,$thispath."/images/info.gif");

		if ($align=="vertical") echo "<br />\n";
		if (!$displayfile || $displayfile=="Y") {
			if (is_array($default_value)) {
				foreach ($default_value as $value) {
					if ((substr($value,-4)==".gif" || substr($value,-4)==".jpg" || substr($value,-4)==".jpeg" || substr($value,-4)==".png") ) {
						$image = $files_directory.$value;
					}
				}
			} else {
				if ((substr($value,-4)==".gif" || substr($value,-4)==".jpg" || substr($value,-4)==".jpeg" || substr($value,-4)==".png") ) {
					$image = $files_directory.$value;
				}
			}
			echo "<img id=\"$previewid\" align=\"left\" style=\"width:50px; height:50px; border:1px solid black; margin:5px 5px 0 5px;\" alt=\" preview \" />\n";
		}
		echo "</div>\n";

	echo "<br />\n";
	echo "<div class=\"file-upload-iframe-container\">\n";
	echo '<iframe name="container" class="file-upload-iframe" src="'.$addeditdir.'/includes/ajax_file_upload.inc.php?files_directory='.urlencode($files_directory).'&amp;idname='.$idname.'&amp;uploadsize='.$uploadsize.'&amp;allowsetdir='.$allowsetdir.'&amp;limituploads='.$limituploads.'"></iframe>';
	echo "</div>\n";

	// --- we could create a hidden field to track the original value - not using now but could be useful in the future...
	//printf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />\n",$name."_orig",$value);
	echo "</div>\n";

}


// ----------------------------------------------------------------------------
// --- hidden
// ----------------------------------------------------------------------------
function hidden($name="", $value="") {
	if (!isset($value) || $value=="") $value = stripslashes($_POST[$name]);
	$value = stripslashes($value);
	$value = str_replace('"','&quot;',$value);	
	printf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />\n",$name,$value);
}


// ----------------------------------------------------------------------------
// --- password
// ----------------------------------------------------------------------------
function password($name="", $value="", $confirm="Y", $size="25", $maxlen="255") {
	global $passwordconfirm, $addeditdir, $md5, $editing;

	if (!$confirm) $confirm = "Y";
	if (!isset($value) || $value=="") $value = stripslashes($_POST[$name]);
	if ($value && !$passwordconfirm) $passwordconfirm = $value;
	if ($md5 && $editing) $value = $passwordconfirm = "";
	//echo "value is " . $value;
	printf("<input type=\"password\" name=\"%s\" size=\"$size\" value=\"%s\" maxlength=\"$maxlen\" />",$name,$value);
	if ($confirm=="Y") {
		printf("<br /> <span style='color:red'>Confirm Password:</span>\n");
		printf("<br /><input type=\"password\" name=\"passwordconfirm\" size=\"$size\" value=\"%s\" maxlength=\"$maxlen\"  onfocus=\"if(this.value=='your')this.value='';\"  onblur=\"if(this.value=='')this.value='your'\" />\n",$passwordconfirm);
	}
}


// ----------------------------------------------------------------------------
// --- radio button
// ----------------------------------------------------------------------------
function radio($name="", $list="", $option_value="", $option_display="", $default_value="", $event="", $align="vertical") {
	global $db;

	//echo "default is " . $default_value;
	if (substr($list,0,7)=="select ") {
		$result = $db->get_results($list);
		foreach ($result as $myrow) {
			($myrow->$option_value==$default_value) ? $selected = "checked=\"checked\"" : $selected="";
			printf("<input type=\"radio\" name=\"%s\" value=\"%s\" $selected %s/>%s\n",$name,$myrow->$option_value,$event,$myrow->$option_display);
		}
	} else {
		if ( !is_array($list) ) {
			//echo "is an array";
			$list_array = explode(",",$list);
			//print_r($list_array);
		} else {
			$list_array = $list;
		}
		foreach($list_array as $key=>$field) {
			$value = substr($field,0,strpos($field,"=>"));
			$display = substr($field,strpos($field,"=>")+2);

			($value==$default_value) ? $selected = "checked=\"checked\"" : $selected="";
			printf("<input type=\"radio\" name=\"%s\" value=\"%s\" $selected %s/>%s\n",$name,$value,$event,$display);
			if ($align=="vertical") echo "<br />\n";
		}
	}
}


// ----------------------------------------------------------------------------
// --- selectbox (basic, single choice dropdown)
// ----------------------------------------------------------------------------
function selectbox($name="", $list="", $size=1, $option_value="", $option_display="", $default_value="", $event="") {
	global $db, $selectboxblank, $sql_debug, $detail_debug;

	if (!$size) $size=1;
	$value = stripslashes($_POST[$name]);
	if (!isset($default_value) || $default_value=="") $default_value = $value;
	
	//echo "default value is " . $default_value . "; option_value is " . $option_value . "; option_display is " . $option_display . "<br>";
	//echo "list is " . $list;
	if (@substr(strtolower($list),0,7)=="select ") {
		if (!$option_display || !$option_value) printMessage("It appears you didn't setup up your selectbox field <code>".$name."</code> properly - please investigate your <code>option=>desc</code> setup","red");
		$result = $db->get_results($list);
		if (!$result) {
			if ($sql_debug || $detail_debug) printMessage("there may be a problem with your form setup for field <code>".$name."</code> - quite likely it is the sql statement or options you used to populate a selectbox. You are using the following select code:<br /><code>".$list."</code>","red");
		} else {
			if ($selectboxblank) $options .= sprintf("<option value=\"\">&nbsp;</option>\n");
			foreach ($result as $myrow) {
				($myrow->$option_value==$default_value || $myrow->$option_display==$default_value) ? $selected = "selected=\"selected\"" : $selected="";
				if ($selected) {
					$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				} else {
					$options .= sprintf("<option value=\"%s\">%s</option>\n",cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				}					
			}
		}
	} else {
		if (!is_array($list)) $list = explode(",",$list);
		foreach($list as $key=>$field) {
			if (stristr($field,"=>")) {
				$key = substr($field,0,strpos($field,"=>")); 
				$field = substr($field,strpos($field,"=>")+2); 
			}
			($key==$default_value) ? $selected = "selected=\"selected\"" : $selected="";
			if ($selected) {
				$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($key),cleanup($field));
			} else {
				$options .= sprintf("<option value=\"%s\">%s</option>\n",cleanup($key),cleanup($field));
			}					
		}
	}

	$selectid = str_replace("_","-",$name);
	printf("<select id=\"%s\" name=\"$name\" size=\"$size\" %s>\n",$selectid,$event);
		echo $selected_options;
		echo $options;
	printf("</select>\n");
}

// ----------------------------------------------------------------------------
// --- selectbox_other (AJAX)
// --- NOTES: basic single choice dropdown with ability to add other entry
// ----------------------------------------------------------------------------
function selectbox_other($name="", $list="", $size=1, $option_value="", $option_display="", $default_value="", $align="", $event="") {
	global $db, $$option_value, $addeditdir, $encoding, $selectboxblank, $sql_debug, $detail_debug;

	if (!$size) $size=1;
	$other_name = $name . "_other";
	$value = stripslashes($_POST[$name]);
	if (!$default_value) $default_value = $value;
	$other_value = stripslashes($_POST[$other_name]);
	if (substr(strtolower($list),0,7)=="select ") {
		if (!$option_display || !$option_value) printMessage("It appears you didn't setup up your selectbox field <code>".$name."</code> properly - please investigate your <code>option=>desc</code> setup","red");
		$temp1 = strpos(strtolower($list),"from") + 5;
		$temp2 = substr($list,$temp1);
		$temp3 = strpos($temp2," ");
		$table = substr($temp2,0,$temp3);
		$result = $db->get_results($list);
		if (!$result) {
			if ($sql_debug || $detail_debug) printMessage("there may be a problem with your form setup for field <code>".$name."</code> - quite likely it is the sql statement or options you used to populate a selectbox. You are using the following select code:<br /><code>".$list."</code>","red");
		} else {
			if ($size==1 && $selectboxblank) $options .= sprintf("<option value=\"\">&nbsp;</option>\n");
			foreach ($result as $myrow) {
				($myrow->$option_value==$default_value || $myrow->$option_display==$default_value) ? $selected = "selected=\"selected\"" : $selected="";
				if ($selected) {
					$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				} else {
					$options .= sprintf("<option value=\"%s\">%s</option>\n",cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				}		
			}
		}
	} else {
		if (!is_array($list)) $list = explode(",",$list);
		foreach($list as $key=>$field) {
			if (stristr($field,"=>")) {
				$key = substr($field,0,strpos($field,"=>")); 
				$field = substr($field,strpos($field,"=>")+2); 
			}
			($key==$default_value) ? $selected = "selected=\"selected\"" : $selected="";
			if ($selected) {
				$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($key),cleanup($field));
			} else {
				$options .= sprintf("<option value=\"%s\">%s</option>\n",cleanup($key),cleanup($field));
			}
		}
	}

	$selectid = str_replace("_","-",$name);
	$otherid = str_replace("_","-",$other_name);
	$statusid = $selectid."-status";

	printf("<select id=\"%s\" name=\"$name\" size=\"$size\" %s>\n",$selectid,$event);
		if ($selected_options) echo $selected_options;
		echo $options;
	printf("</select>\n");

	echo "<br />\n";
	printf("<input type=\"text\" id=\"%s\" name=\"%s\" size=\"$other_size\" value=\"%s\" maxlength=\"125\" />\n",$otherid, $other_name,$other_value);
	printf("<input type=\"button\" value=\"Add\" onclick=\"ajaxAddOther('%s','%s','%s','%s','%s','%s')\" />",$table,$option_display,$option_value,$otherid,$selectid,$encoding);

	$help = "You only need to fill out the text box if the selectbox doesn\'t contain the choice you are looking for.";
	printf("<a href=\"javascript:alert('%s')\"><img src=\"%s\" alt=\"Need to Know More?\" height=\"12\" width=\"12\" align=\"bottom\" hspace=\"2\" border=\"0\" /></a>\n",$help,$thispath."/images/info.gif");
	echo '<br /><div style="margin:5px; 0 5px 0;">status: <span id="'.$statusid.'" style="color:#999; border:1px dotted #999; padding:2px 5px 2px 5px;">nothing added yet</span></div>';
}


// ----------------------------------------------------------------------------
// --- selectbox_multiple 
// --- NOTES: just a basic selectbox but you can specify how many 
// ---        rows to show and more than one option can be selected.
// ----------------------------------------------------------------------------
function selectbox_multiple($name="", $list="", $size="5", $option_value="", $option_display="", $default_value="", $event="") {
	// for a CSS multiple select box (no need for ctrl key check out http://www.phpcomplete.com/archives/2004/08/26/select-multiple-done-right/
	global $db, $$option_value, $addeditdir, $selectboxblank, $sql_debug, $detail_debug;

	if (!isset($size) || $size=="") $size=1;
	if (!isset($value) || $value=="") $value = stripslashes($_POST[$name]);
	if (!isset($default_value) || $default_value=="") $default_value = $value;
	// --- if not an array and containts a comma, let's convert to an array...
	if (!is_array($default_value)) $default_value = explode(",",$default_value);
	//print_r($default_value);
	//echo $list;
	if (@substr(strtolower($list),0,7)=="select ") {
		if (!$option_display || !$option_value) printMessage("It appears you didn't setup up your selectbox field <code>".$name."</code> properly - please investigate your <code>option=>desc</code> setup","red");
		$result = $db->get_results($list);
		if (!$result) {
			if ($sql_debug || $detail_debug) printMessage("there may be a problem with your form setup for field <code>".$name."</code> - quite likely it is the sql statement or options you used to populate a selectbox. You are using the following select code:<br /><code>".$list."</code>","red");
		} else {
			if ($selectboxblank) $options .= sprintf("<option value=\"\"></option>\n");
			foreach ($result as $myrow) {
				$optionclass = $myrow->type;  // --- NOTE: $optionclass is one of the few variables you should manually change as I haven't figured out a good way to make it a variable and since very few people will ever care about it besides me...
				if ($default_value) {
					if (is_array($default_value)) {
						if (count($default_value) && !empty($default_value[0])) {
							(in_array($myrow->$option_value,$default_value) || in_array($myrow->$option_display,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
						}
					} else {
						(stristr($myrow->$option_value,$default_value) || stristr($myrow->$option_display,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
					}
				}
				if ($selected) {
					$selected_options .= sprintf("<option class=\"%s\" value=\"%s\" $selected>%s</option>\n",$optionclass,cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				} else {
					$options .= sprintf("<option class=\"%s\" value=\"%s\">%s</option>\n",$optionclass,cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				}
			}
		}
	} else {
		if (!is_array($list)) $list = explode(",",$list);
		foreach($list as $key=>$field) {
			if (stristr($field,"=>")) {
				$key = substr($field,0,strpos($field,"=>")); 
				$field = substr($field,strpos($field,"=>")+2); 
			}
				if (is_array($default_value)) {
					(in_array($key,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
				} else {
					(stristr($key,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
				}

				if ($selected) {
					$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($key),cleanup($field));
				} else {
					$options .= sprintf("<option value=\"%s\">%s</option>\n",cleanup($key),cleanup($field));
				}
		}
	}

	printf("<select name=\"%s\" multiple=\"multiple\" size=\"$size\" %s>\n",$name."[]",$event);
		if ($selected_options) echo $selected_options;
		echo $options;
	printf("</select>\n");
}


// ----------------------------------------------------------------------------
// --- multiple selectbox_other (AJAX)
// --- NOTES: allows you to instantly add a field to the db table from which 
// ---        the selectbox is populated, then updates the selectbox.
// ----------------------------------------------------------------------------
function selectbox_multiple_other ($name="", $list="", $size="5", $option_value="", $option_display="", $default_value="", $align, $event="") {
	// --- for a CSS multiple select box (no need for ctrl key check out http://www.phpcomplete.com/archives/2004/08/26/select-multiple-done-right/
	global $db, $$option_value, $addeditdir, $encoding, $selectboxblank, $sql_debug, $detail_debug;

	if (!isset($size) || $size=="") $size=1;
	$other_name = $name . "_other";
	$value = stripslashes($_POST[$name]);
	if (!isset($default_value) || $default_value=="") $default_value = $value;
	// --- if not an array, let's convert to an array...assuming the default values are a comma separated string (if not, we'll have a single entry array)
	if (!is_array($default_value)) $default_value = explode(",",$default_value);
	//echo "default is "; print_r($default_value); 
	$other_value = stripslashes($_POST[$other_name]);
	if (is_array($other_value)) $other_value="";
	if (substr(strtolower($list),0,7)=="select ") {
		if (!$option_display || !$option_value) printMessage("It appears you didn't setup up your selectbox field <code>".$name."</code> properly - please investigate your <code>option=>desc</code> setup","red");
		$temp1 = strpos(strtolower($list),"from") + 5;
		$temp2 = substr($list,$temp1);
		$temp3 = strpos($temp2," ");
		$table = substr($temp2,0,$temp3);
		$result = $db->get_results($list);
		if (!$result) {
			if ($sql_debug || $detail_debug) printMessage("there may be a problem with your form setup for field <code>".$name."</code> - quite likely it is the sql statement or options you used to populate a selectbox. You are using the following select code:<br /><code>".$list."</code>","red");
		} else {
			if ($selectboxblank) $options .= sprintf("<option value=\"\"></option>\n");
			foreach ($result as $myrow) {
				$optionclass = $myrow->type;  // --- NOTE: $optionclass is one of the few variables you should manually change as I haven't figured out a good way to make it a variable and since very few people will ever care about it besides me...
				if ($default_value) {
					if (is_array($default_value)) {
						if (count($default_value) && !empty($default_value[0])) {
							(in_array($myrow->$option_value,$default_value) || in_array($myrow->$option_display,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
						}
					} else {
						(stristr($myrow->$option_value,$default_value) || stristr($myrow->$option_display,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
					}
				}
				if ($selected) {
					$selected_options .= sprintf("<option class=\"%s\" value=\"%s\" $selected>%s</option>\n",$optionclass,cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				} else {
					$options .= sprintf("<option class=\"%s\" value=\"%s\">%s</option>\n",$optionclass,cleanup($myrow->$option_value),cleanup($myrow->$option_display));
				}
			}
		}
	} else {
		if (!is_array($list)) $list = explode(",",$list);
		foreach($list as $key=>$field) {
			if (stristr($field,"=>")) {
				$key = substr($field,0,strpos($field,"=>")); 
				$field = substr($field,strpos($field,"=>")+2); 
			}
			if ($default_value) {
				if (is_array($default_value)) {
					(in_array($key,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
				} else {
					(stristr($key,$default_value)) ? $selected = "selected=\"selected\"" : $selected="";
				}
			}

			if ($selected) {
				$selected_options .= sprintf("<option value=\"%s\" $selected>%s</option>\n",cleanup($key),cleanup($field));
			} else {
				$options .= sprintf("<option value=\"%s\">%s</option>\n",cleanup($key),cleanup($field));
			}
		}
	}

	$selectid = str_replace("_","-",$name);
	$otherid = str_replace("_","-",$other_name);
	$statusid = $selectid."-status";

	printf("<select id=\"%s\" name=\"%s\" multiple=\"multiple\" size=\"$size\" %s>\n",$selectid,$name."[]",$event);
		if ($selected_options) echo $selected_options;
		echo $options;
	printf("</select>\n");

	echo "<br />\n";
	printf("<input type=\"text\" id=\"%s\" name=\"%s\" size=\"$other_size\" value=\"%s\" maxlength=\"125\" />\n",$otherid,$other_name,$other_value);
	printf("<input type=\"button\" value=\"Add\" onclick=\"ajaxAddOther('%s','%s','%s','%s','%s','%s')\" />",$table,$option_display,$option_value,$otherid,$selectid,$encoding);
	$help = "You only need to fill out the text box if the selectbox doesn\'t contain the choice you are looking for.";
	printf("<a href=\"javascript:alert('%s')\"><img src=\"%s\" alt=\"Need to Know More?\" height=\"12\" width=\"12\" align=\"bottom\" hspace=\"2\" border=\"0\" /></a>\n",$help,$thispath."/images/info.gif");
	echo '<br /><div style="margin:5px; 0 5px 0;">status: <span id="'.$statusid.'" style="color:#999; border:1px dotted #999; padding:2px 5px 2px 5px;">nothing added yet</span></div>';
	echo "\n";
}


// ----------------------------------------------------------------------------
// --- submit button
// --- NOTES: this submit button includs a lock function so it can only be 
// ---        submitted once.
// ----------------------------------------------------------------------------
function submit($name="go", $value="submit", $class="", $event="") {
	global $form_name;
	if ($event) {
		if (substr($event,-1)==";") $event = substr($event,0,strlen($event)-1);
		printf("<input type=\"submit\" name=\"%s\" value=\"%s\" class=\"%s\" onclick=\"$event; return LockSubmit('$form_name','$name');\" />\n",$name,$value,$class);
	} else {
		printf("<input type=\"submit\" name=\"%s\" value=\"%s\" class=\"%s\" onclick=\"return LockSubmit('$form_name','$name');\" />\n",$name,$value,$class);
	}
	printf("<input type=\"hidden\" name=\"submitval\" value=\"1\" />\n");
}


// ----------------------------------------------------------------------------
// --- text
// --- NOTES: not an actual field, but could be used to add info to your form
// ----------------------------------------------------------------------------
function text($text) {
	printf("%s",$text);
}


// ----------------------------------------------------------------------------
// --- textarea
// ----------------------------------------------------------------------------
function textarea($name="", $value="", $rows="5", $cols="70") {
	if (!isset($value) || $value=="") $value = stripslashes($_POST[$name]);
	printf("<textarea name=\"%s\" rows=\"$rows\" cols=\"$cols\">%s</textarea>\n",$name,$value);
}


// ----------------------------------------------------------------------------
// --- textarea_FCKedit
// --- NOTES: a textarea using the open source FCKeditor instead of a plan input
// ----------------------------------------------------------------------------
function textarea_FCKedit($name="", $value="", $rows="5", $cols="70", $toolbar="Default") {
	global $addeditdir;
	if (substr($addeditdir,-1)=="/") $addeditdir = substr($addeditdir,0,strlen($addeditdir)-1);
	//echo $addeditdir;

	if ($_POST["keepspaces"]) $keepspaces = $_POST["keepspaces"];
	if ($_POST["keepclasses"]) $keepclasses = $_POST["keepclasses"];

	if ($rows<200) $rows=200;
	if ($cols<400) $cols="100%";
	if (!isset($value) || $value=="") $value = $_POST[$name];
	$value = stripslashes($value);
	$tmpString = trim($value);
	// --- replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	if (defined('FCK_PATH') && FCK_PATH!="") {
		include_once(getenv('DOCUMENT_ROOT').FCK_PATH."fckeditor.php") ;
	} else {
		// --- why all the below? b/c I used to use FCKeditor but now use default fckeditor directory name
		if (getenv('HTTP_HOST')=="www.phpaddedit.com" or getenv('HTTP_HOST')=="phpaddedit.com") {
			if (file_exists("/FCKeditor/fckeditor.php")) {
				$basepath = "/FCKeditor/";
			} else {
				$basepath = "/fckeditor/";
			}
		} else {
			if (file_exists("FCKeditor/fckeditor.php")) {
				$basepath = $addeditdir."/FCKeditor/";
			} else {
				$basepath = $addeditdir."/fckeditor/";
			}
		}
		$fckinclude = $basepath."fckeditor.php";
		include_once(getenv('DOCUMENT_ROOT')."/".$fckinclude);
	}
	
	$oFCKeditor = new FCKeditor($name);
	$oFCKeditor->BasePath = $basepath;	
	$oFCKeditor->ToolbarSet = $toolbar;
	$oFCKeditor->Height = $rows;
	$oFCKeditor->Width = $cols;
	$oFCKeditor->Value = $value;
	$oFCKeditor->Create();

	// --- if cleanit option is enabled (yes by default) then let's show a couple of options to either keep extra spaces or class definitions...
	if (CLEANIT=="Y") {
		if ($keepspaces=="Y") $spacesactive = " checked='checked'";
		echo '<input type="checkbox" name="keepspaces" value="Y"'.$spacesactive.' /> <a href="javascript:alert(\'By default the cleanit function removes extra spaces. If you are converting text that has extra spaces which should be kept then select this checkbox\');">Keep Spaces</a> &nbsp; ';
		if ($keepclasses=="Y") $classesactive = " checked='checked'";
		echo '<input type="checkbox" name="keepclasses" value="Y"'.$classesactive.' /> <a href="javascript:alert(\'By default the cleanit function strips out most class definitions. If you prefer to keep the class statements then select this checkbox\');">Keep classes</a> &nbsp; ';
	}
}


// ----------------------------------------------------------------------------
// --- textbox
// ----------------------------------------------------------------------------
function textbox($name="", $value="", $size="25", $maxlen="255", $event="") {
	if (!isset($maxlen) || $maxlen=="") $maxlen=255;
	if (!isset($value) || $value=="") $value = stripslashes($_POST[$name]);
	//$value = htmlentities($value);
	$value = stripslashes($value);
	$value = str_replace('"','&quot;',$value);	
	printf("<input type=\"text\" name=\"%s\" size=\"$size\" value=\"%s\" maxlength=\"$maxlen\" %s />\n",$name,$value,$event);
}


// ----------------------------------------------------------------------------
// --- textbox_noedit
// --- NOTES: a basic textbox but which cannot be edited.
// ----------------------------------------------------------------------------
function textbox_noedit($name="", $value="", $size="25", $maxlen="255", $event="") {
	if (!isset($maxlen) || $maxlen=="") $maxlen=255;
	if (!isset($value) || $value=="") $value = stripslashes($_POST[$name]);
	$value = stripslashes($value);
	$value = str_replace('"','&quot;',$value);	
	printf("<input class=\"text-noedit\" type=\"text\" name=\"%s\" size=\"$size\" value=\"%s\" maxlength=\"$maxlen\" readonly=\"readonly\" %s />\n",$name,$value,$event);
}
?>