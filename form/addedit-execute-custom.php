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

if (!$page && $_GET["page"]) $page = $_GET["page"];
if (!$thispage) $thispage = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'],'/')+1);

$host = $_SERVER['HTTP_HOST']; 
if (!$website) {
	$website = $host;
	if (substr($website,0,4)=="www.") $website = substr($website,4);
	$websitehttp = "http://www.".$website;
}

/*
echo "<br><br>";
print_r($insert_id);
echo "page is " . $page . "; thispage is " . $thispage . "<br>";
echo "<br><br>";
print_r($insert_id);
echo "<br>";
*/

// --------------------------------------------------------------------------------------------------------------------------------
// --- below is code specific to the sample wordpress form included with phpAddEdit to write a new post
// --- write your own custom code using it as a guide...
// --------------------------------------------------------------------------------------------------------------------------------
if ($_POST["submitval"] && !$error && count($error_message)==0) {
	if ($page=="wordpress_content" || $thispage=="wordpress_content.php") {
		// --------------------------------------------------------------------------------------------------------------------------------
		// --- set the GUID field...this isn't really needed since we set the value in the form definition
		// --- when we created the form but it does illustrate what could be done...
		// --------------------------------------------------------------------------------------------------------------------------------
		$guid = $websitehttp.$addeditdir."/forms/wordpress_content.php?ID=".$insert_id["wp_posts"][0];
		$custom_sql = "UPDATE wp_posts SET guid='".$guid."' WHERE ID=".$insert_id["wp_posts"][0]."";
		//echo $custom_sql. "<br>";
		$db->query($custom_sql);
		if (!$db->result) $error_message .= "An error occurred updating the guid for this entry<br />";

		// --------------------------------------------------------------------------------------------------------------------------------
		// --- set the post_name field which is the post title stripped of all special characters and spaces
		// --------------------------------------------------------------------------------------------------------------------------------
		$post_name = slug($_POST["wp_posts_post_title"]);
		$custom_sql = "UPDATE wp_posts SET post_name='".$post_name."' WHERE ID=".$insert_id["wp_posts"][0]."";
		//echo $custom_sql. "<br>";
		$db->query($custom_sql);
		if (!$db->result) $error_message .= "An error occurred updating the post name for this entry<br />";

		// --------------------------------------------------------------------------------------------------------------------------------
		// --- update the wp_term_relationships table to increase the  count for the terms  used
		// --------------------------------------------------------------------------------------------------------------------------------
		$taxonomy = $_POST["wp_term_relationships_term_taxonomy_id"];
		//print_r($taxonomy);
		foreach ($taxonomy as $taxonomyid) {
			$custom_sql = "UPDATE wp_term_taxonomy SET count=count+1 WHERE term_id=".$taxonomyid;
			//echo $custom_sql. "<br>";
			$db->query($custom_sql);
			if (!$db->result) $error_message .= "An error occurred updating the category count<br />";
		}
	}
	
	// --------------------------------------------------------------------------------------------------------------------------------
	// --- below is colde to add the description to the table wp_term_taxonomy if someone adds a new 
	// --- category using the add_category sample form for a wordpress database (since this table acts 
	// --- like a cross-reference table phpAddEdit only can update two ID fields thus the need for this 
	// --- customization...
	// --------------------------------------------------------------------------------------------------------------------------------
	if ($page=="add_category" || $thispage=="add_category.php") {
		$post_name = slug($_POST["wp_posts_post_title"]);
		$custom_sql = "UPDATE wp_term_taxonomy SET description='".$_POST["wp_term_taxonomy_description"]."' WHERE term_taxonomy_id=".$insert_id["wp_term_taxonomy"][0]."";
		//echo $custom_sql. "<br>";
		$db->query($custom_sql);
		if (!$db->result) $error_message .= "An error occurred updating the description for this category<br />";
	}	
}
?>
