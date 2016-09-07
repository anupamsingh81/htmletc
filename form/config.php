<?php
// ** MySQL settings ** //
define('DB_NAME', 'x');     			// The name of the database
define('DB_USER', 'x');     			// Your MySQL username
define('DB_PASSWORD', 'x'); 			// ...and password
define('DB_HOST', 'localhost');   		// 99% chance you won't need to change this value
define('DB_CHARSET', 'UTF8'); 			// Character set for DB (can be changed for each individual form separately)

// ** Administrator settings ** //
define('ADMIN_USERNAME', 'admin');	 	// feel free to change this value
define('ADMIN_PASSWORD', 'admin');	 	// ...but definitely change this password

// ** Misc settings ** //
define('FCK_PATH', ''); 			// if you already use FCKeditor for another app you can specify it's location here 
									// (e.g. /other_app/FCKeditor/ ) - use the trailing slash!
define('CLEANIT', 'Y'); 			// use cleanit function on FCKeditor fields

// --------------------------------------------------------------------------------------
// --- set debug flags - use this to output different levels of debugging 
// --------------------------------------------------------------------------------------
$sql_debug = false;			// --- use to echo the insert or update sql statements
$execute_debug = false;		// --- use this if you want to NOT perform the sql execute statements...
$error_check_debug = false;	// --- use to echo info about the error checking routines
$detail_debug = false;		// --- use to echo other potentially useful information (primary keys, etc.)
// --------------------------------------------------------------------------------------
?>