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

// ---------------------------------------------------------------------------------------------
// --- The phpAddEdit script often deletes multiple rows and then re-adds them, making any 
// --- auto_increment keys quickly baloon. This script can be used to re-sequence a table's 
// --- primary key field...
// ---------------------------------------------------------------------------------------------

include_once ("config.php");
include_once ("addedit-functions.php");
include_once ("includes/dbconnect.inc.php");

function resequence ($table,$primarykey) {
  global $db;
  $sql = "SELECT * from $table ORDER BY $primarykey";
  //echo $sql;
  $rows = $db->get_results($sql);
  //print_r($rows);
  for ($n=1; $n<=count($rows); $n++) {
  	$value = $rows[$n-1]->$primarykey;
	$update_sql = "UPDATE $table SET $primarykey=".$n." WHERE $primarykey='".mysql_real_escape_string($value)."'";
	echo $update_sql."<br>";
	$db->query($update_sql);
  }

  // -- now let's alter the table to reset the auto increment and sort order...
  $alter_sql = "ALTER TABLE $table ORDER BY $primarykey";
  $db->query($alter_sql);
  $alter_sql = "ALTER TABLE $table AUTO_INCREMENT=$n";
  $db->query($alter_sql);
}

resequence("content2chapter","rel_id");
resequence("content2source","rel_id");
resequence("content2content","rel_id");

resequence("source2author","rel_id");
resequence("source2publication","rel_id");

printMessage ("Finished");
?>