<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<style>

input {
	font-size:12px;
}

.file-upload-ajax-iframe {
	width:100%; 
	height:10px; 
	display:none; 
	overflow:auto;
}
.file-upload-status {
	color:#999; 
	border:1px dotted #999; 
	padding:2px 5px 2px 5px; 
	width:100%;
}
</style>

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

$limituploads = 'true';
if (!$_GET["limituploads"]) $limituploads = 'false';
?>

<script language="JavaScript" type="text/javascript"><!--

/* This function is called when user selects file in file dialog */
function jsUpload(uploadfield,uploadfieldname,idname) {
	var sel = document.getElementById(uploadfieldname);
	//alert(uploadfield.form.name);
	//alert(idname);

	/* Check for valid file extension */
    var re_text = /\.gif|\.png|\.jpeg|\.jpg|\.bmp|\.txt|\.doc|\.pdf|\.pot|\.ppt|\.xls|\.xml|\.zip/i;
    var filename = sel.value;

    /* Checking file type */
    if (filename.search(re_text) == -1) {
		alert("File does not have an accepted extension (e.g. graphic, pdf, txt, xml, zip)");
		uploadfield.form.reset();
		return false;
    }

	uploadfield.form.submit();
	document.getElementById("uploadstatus").innerHTML='<span style="color:red; background: url(/addedit/images/working.gif) 50% 50% no-repeat;">Uploading<\/span>';
	uploadfield.disabled = <?php echo $limituploads ?>;	// --- use this if you want to allow only one upload...
    return true;
}

function makeFrame(url) {
   ifrm = document.createElement("IFRAME");
   ifrm.setAttribute("src", url);
   ifrm.style.width = 100+"%";
   ifrm.style.height = 100+"%";
   ifrm.style.border = 0;
   document.body.appendChild(ifrm);
} 
-->
</script>
</head>

<?php
// -------------------------------------------------------------------------------------------------
// --- modified from work found at: 
// --- http://www.anyexample.com/programming/php/php_ajax_example__asynchronous_file_upload.xml
// --- NOTES: use the CSS class .file-upload-iframe to set the dimensions of the iframe; 
// ---        you may add more error checking see 
// ---        http://www.php.net/manual/en/features.file-upload.errors.php for details; 
// ---        to change the allowed file types, edit the line above that starts 'var re_text ='
// -------------------------------------------------------------------------------------------------

//print_r($_GET);
$allowsetdir = $_GET["allowsetdir"];
$files_directory = urldecode($_GET["files_directory"]);
if ($_POST["directory"]) $files_directory = urldecode($_POST["directory"]);
$upload_dir = $_SERVER['DOCUMENT_ROOT'].$files_directory; 	// --- Directory for file storing filesystem path
if (substr($upload_dir,-1)=="/") $upload_dir = substr($upload_dir,0,strlen($upload_dir)-1);
$web_upload_dir = $files_directory; 						// --- Directory for file storing Web-Server dir 
//echo "dir: ".$upload_dir;
//echo "dir: ".$web_upload_dir;

$idname = $_GET["idname"];
//echo "idname: " . $idname;

// ---------------------------------------------------------
// --- testing upload dir is writeable...
// ---------------------------------------------------------
$tf = $upload_dir.'/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false) die("Fatal error! {$upload_dir} is not writable. Set 'chmod 777 {$upload_dir}' or something like this");
fclose($f);
unlink($tf);

// ---------------------------------------------------------
// --- FILEFRAME section of the script
// ---------------------------------------------------------
if (isset($_POST['fileframe'])) {
	$result = 'ERROR';
	$result_msg = 'No FILE field found';

	//print_r($_FILES['file']);
	if (isset($_FILES['file'])) {
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
			$filename = $_FILES['file']['name']; // file name 
			if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.'/'.$filename)) {
				// --- main action - move uploaded file to $upload_dir 
				$result = 'OK';
			} else {
				$result_msg = 'Could Not Move File ' . $filename;
			}
		}
		elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else 
			$result_msg = 'Unknown error';
	}

	echo '<html><head><title>-</title></head><body>';
    echo '<script language="JavaScript" type="text/javascript">'."\n";
    echo 'var parDoc = window.parent.document;';
    echo 'var superParDoc = parent.parent.document;';

    // --- this code is outputted to IFRAME (embedded frame) main page is a 'parent'
    if ($result == 'OK') {
		echo 'var sel = superParDoc.getElementById("'.$idname.'");';
		echo 'sel.options[sel.options.length] = new Option("'.$filename.'","'.$filename.'",false,true);';
		echo 'parDoc.getElementById("uploadstatus").innerHTML=\'<code><span style="color:green;">File Uploaded</span></code>\';';
		//echo 'alert(sel.options.length);';
    } else {
		echo 'parDoc.getElementById("uploadstatus").innerHTML=\'<span style="color:green;">ERROR: '.$result_msg.'</span>\';';
    }

    echo "\n".'</script>';
	echo "\n".'</body></html>';

	exit(); 
}
// --- FILEFRAME section END
// ---------------------------------------------------------
?>


<!-- 
<form name="main-form" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" enctype="multipart/form-data" style="margin:0 0 5px 0;">
-->
<div style="font-size:11px; font-family:verdana;">
<form name="main-form" target="upload_iframe" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" enctype="multipart/form-data" style="margin:0 0 5px 0;">
<input type="hidden" name="fileframe" value="true">
<?php
if ($allowsetdir) { 
	echo "&nbsp; &nbsp; Upload Directory: <input type=\"text\" id=\"directory\" name=\"directory\" size=\"$uploadsize\" value=\"$files_directory\" />\n";
}
?>
<input type="file" name="file" id="file" size="<?php echo $uploadsize ?>"><input type="button" id="ajaxbutton" value="Upload" onclick="jsUpload(this,'file','<?php echo $idname ?>')" />
</form>

<iframe name="upload_iframe" class="file-upload-ajax-iframe"></iframe>
Upload status: <span class="file-upload-status" id="uploadstatus">nothing uploaded yet</span>
</div>

</body>
</html>