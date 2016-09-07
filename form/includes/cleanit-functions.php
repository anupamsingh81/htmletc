<?php
#=====================================================================
# MS Word HTML Cleanup Tool
# Ver. 2.7
# By Jeff Blum
# http://www.webmastersherpa.com/
# Released 07-19-2011
#
# Copyright Info: This application was written by Jeff Blum.
# Feel free to copy, cite, reference, sample, borrow 
# or plagiarize the contents.  However, if you don't mind,
# please let me know where it goes so that I can watch and take
# part in the development of it. An attribution and/or link 
# to Webmaster Sherpa (http://www.webmastersherpa.com) would also 
# be appreciated. 
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
#=====================================================================

// =======================================================================================================================
// === Cleanup functions
// =======================================================================================================================

// --------------------------------------------------------------------------------------------------------------
// --- $keepspaces is a variable you can set to have the cleanit function NOT removed extra spaces (they are 
// --- removed by default);
// $keepspaces = false;
// --------------------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------
// ---	@name	removeEmptyLines
// ---	@desc 	removes any empty lines in multi-line text
// ------------------------------------------------------------------------
if(!function_exists('removeEmptyLines')) {
	function removeEmptyLines($string) {
		return preg_replace("!^\s+(\D)!m", "\\1", $string);
	} 
}


// --------------------------------------------------------------------------------------------------------------
// ---	@name	str_ireplace
// ---	@desc 	re-create this native PHP function for PHP versions < PHP 5 that don't support it 
// ---	@returns	string
// --------------------------------------------------------------------------------------------------------------
if(!function_exists('str_ireplace')) {
	function str_ireplace ($search,$replace,$subject) {
		$search = preg_quote($search, "/");
		return preg_replace("/".$search."/i", $replace, $subject); 
	} 
}


// --------------------------------------------------------------------------------------------------------------
// ---	@name	stripos
// ---	@desc 	re-create this native PHP function for PHP versions that don't support it 
// ---	@returns	integer
// --------------------------------------------------------------------------------------------------------------
if (!function_exists("stripos")) {
  function stripos($str,$needle,$offset=0) {
     return strpos(strtolower($str),strtolower($needle),$offset);
  }
}


// --------------------------------------------------------------------------------------------------------------
// ---	@name	tidy
// ---	@desc 	this will fix special characters, extraneous tags, etc. - it is called by the main function
// --- 		cleanit but you could use it by itself also...
// ---	@returns	array
// --------------------------------------------------------------------------------------------------------------
function tidy($line,$font6="h1",$font5="h2",$font4="h3",$font3="h4",$font2="") {
	global $foundcode, $foundif, $keepspaces, $keepclasses;
	if (!isset($keepclasses)) $keepclasses = "N";
	if (!isset($foundcode)) $foundcode = false;
	if (!isset($foundif)) $foundif = false;

	// ---------------------------------------------------------------------------------------------------------------
	// --- sometimes FCKeditor pastes <b> statements and sometimes <strong> so first let's make all <b> <strong>
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("<b>","<strong>",$line);
	$line = str_ireplace("</b>","</strong>",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- replace <font size=""> statements...
	// --- probably o.k. to just assume one </font> per line since using FCKeditor cleanup but may 
	// ---   need to check this to verify...
	// ---   assume that if a size=6 exists it's b/c we are including chapters in the document...
	// ---------------------------------------------------------------------------------------------------------------
	if (stristr($line,"<strong><font size=\"6\">")) {
		$line = str_ireplace("<strong><font size=\"6\">","<".$font6.">",$line);
		$line = str_ireplace("</font></strong>","</".$font6.">",$line);
	}
	if (stristr($line,"<strong><font size=\"5\">")) {
		$line = str_ireplace("<strong><font size=\"5\">","<".$font5.">",$line);
		$line = str_ireplace("</font></strong>","</".$font5.">",$line);
	}
	if (stristr($line,"<strong><font size=\"4\">")) {
		$line = str_ireplace("<strong><font size=\"4\">","<".$font4.">",$line);
		$line = str_ireplace("</font></strong>","</".$font4.">",$line);
	}
	if (stristr($line,"<strong><font size=\"3\">")) {
		$line = str_ireplace("<strong><font size=\"3\">","<".$font3.">",$line);
		$line = str_ireplace("</font></strong>","</".$font3.">",$line);
	}
	if (stristr($line,"<font size=\"2\">")) {
		if ($font2!="") {
			$line = str_ireplace("<font size=\"2\">","<".$font2.">",$line);
			$line = str_ireplace("</font>","</".$font2.">",$line);
		} else {
			$line = str_ireplace("<font size=\"2\">","",$line);
			$line = str_ireplace("</font>","",$line);
		}
	}

	
	// ---------------------------------------------------------------------------------------------------------------
	// --- replace <div> statements that surround <hx> statements...
	// ---------------------------------------------------------------------------------------------------------------
	if (stristr($line,"<div><h")) {
		$line = str_ireplace("<div><h","<h",$line);
		$line = str_ireplace("</div>","",$line);
	}

	// ---------------------------------------------------------------------------------------------------------------
	// --- in fckeditor fck_paste.html file all <p> tags are converted to <div> but apparently this only 
	// --- works in IE and it kills this cleanit function which always looks for <p> tags for lists so 
	// --- let's just undo the conversion (I know this is lame but...)
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("<div","<p",$line);
	$line = str_ireplace("div>","p>",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- in case remove styles definitions option wasn't selected, let's remove all styles
	// ---------------------------------------------------------------------------------------------------------------
	$line = preg_replace("/ style=\"([^\"]*)\"/i","",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- convert <span class="CodeChar"> to <code> o/w remove <span class...
	// ---------------------------------------------------------------------------------------------------------------
	if (stristr($line,"<span class=\"CodeChar\">")) {
		// --- added next 2 lines 12-10-08 - JB - to account for small changes in way FCKeditor new version works...
		$line = str_ireplace("<span>","",$line);
		$line = str_ireplace("</span></span>","</span>",$line);

		$line = preg_replace("/<span class=\"CodeChar\">([^\<]*)<\/span>/i","<code>\\1</code>",$line);
		//$foundcode = true;		// --- removed 12-10-08 - JB - since these lines replace inline code text no need to mark $foundcode as true which will lead to an unpartnered </code> tag
	} 
	// --- treat <p class="Code"> as a block of code and not individual lines of code...
	if (!stristr($line,"<p class=\"Code\">") && $foundcode==true) {
		$line = "</code>\n" . $line;
		$foundcode = false;
	}
	if (stristr($line,"<p class=\"Code\">")) {
		if ($foundcode==false) {
			$line = str_ireplace('<p class="Code">','<code class="block">',$line);
			$line = str_ireplace('</p>','',$line);
			$foundcode = true;
		} else {
			$line = str_ireplace('<p class="Code">','',$line);
			$line = str_ireplace('</p>','',$line);
		}
	} else {
		if ($keepclasses=="N" && !stristr($line,"<td") && !stristr($line,"<ol") && !stristr($line,"<ul")) $line = preg_replace("/ class=\"([^\"]*)\"/i","",$line);
	}

	// ---------------------------------------------------------------------------------------------------------------
	// --- FCKedit is a good editor, even converting from MS Word - but still some cleanup is helpful...
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("<p>&nbsp;</p>","<br />",$line);
	$line = str_ireplace("<strong><span>","",$line);
	$line = str_ireplace("</span></strong>","",$line);
	$line = str_ireplace("<p><strong>&nbsp;</strong></p>","<br />",$line);
	$line = str_ireplace("<p><span>&nbsp;</span></p>","<br />",$line);
	$line = str_ireplace("<pre><p>","<pre>",$line);
	$line = str_ireplace("</p></pre>","</pre>",$line);
	
	// ---------------------------------------------------------------------------------------------------------------
	// --- if line is only </p> let's replace it with <br />...
	// ---------------------------------------------------------------------------------------------------------------
	if (substr($line,0,4)=="</p>") $line = str_ireplace("</p>","<br />",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- now handle pasting images from word and useless xml tags...
	// --- I haven't fully tested this. Basically, Firefox pastes a <img line but IE doesn't. Both paste a 
	// --- <v:imagedata line. I'm not sure about other browsers so for now if using Firefox I will ignore the
	// --- <v:imagedata line and stick with the <img line...
	// ---------------------------------------------------------------------------------------------------------------
	$browser = $_SERVER['HTTP_USER_AGENT'];
	//echo $browser;
	if (stristr($browser,"Firefox")) {
		// --- remove anything inside <!--[if...><![endif]--> tags.
		if (stristr($line,"<!--[if") || $foundif==true) {
			$foundif = true;
			if (stristr($line,"<![endif]")) $foundif = false;
			if (stristr($line,"<img")) {
				$line = substr($line,stripos($line,"<img"));
			} else {
				$line = "";
			}
		} 
	} else {
		$line = preg_replace("/<v:imagedata/i","<img",$line);
	}
	$line = preg_replace("/<v:[^>]+>/i","",$line);
	$line = preg_replace("/<v:[^>]+/i","",$line);
	$line = preg_replace("/<\/v:[^>]+>/i","",$line);
	$line = preg_replace("/<o:[^>]+>/i","",$line);
	$line = preg_replace("/<o:[^>]+/i","",$line);
	$line = preg_replace("/<\/o:[^>]+>/i","",$line);
	$line = str_ireplace('o:title=""/>',"",$line);
	$line = preg_replace("/o:[^>]+>/i","",$line);
	$line = preg_replace("/<st1:[^>]+>/i","",$line);
	$line = preg_replace("/<\/st1:[^>]+>/i","",$line);
	$line = preg_replace("/<p><img/i","<img",$line);
	$line = preg_replace("/<p> [ ]+<img/i","<img",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- replace all the Miscrosoft special characters
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("&rsquo;","'",$line);
	$line = str_ireplace("&lsquo;","'",$line);
	//$line = str_ireplace("&ndash;","--",$line);
	//$line = str_ireplace("&mdash;","--",$line);
	$line = str_ireplace("&shy;","-",$line);
	$line = str_ireplace("&hellip;","...",$line);
	$line = str_ireplace('&quot;','"',$line);
	$line = str_ireplace('&ldquo;','"',$line);
	$line = str_ireplace('&rdquo;','"',$line);
	$line = str_ireplace('','',$line);
	$line = str_ireplace('&bull;','',$line);
	$line = str_ireplace("&#8217;", "'", $line);
	$line = str_ireplace("&#61553;", "&middot;", $line);
	$line = str_ireplace("&#9675;","&middot;",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- replace extraneous strong statements and white spaces...this is the same as in the clean strongs function
	// ---------------------------------------------------------------------------------------------------------------
	$line = preg_replace("/(<strong>)+/i","<strong>",$line);
	$line = preg_replace("/(<\/strong>)/i","</strong>",$line);
	$line = preg_replace("/(<!--\[if !supportLists\]-->)(.+)(<strong>)(.+)(<!--\[endif\]-->)/i","\\1\\2\\4\\5",$line);
	$line = preg_replace("/(<!--\[if !supportLists\]-->)(.+)(<\/strong>)(.+)(<!--\[endif\]-->)/i","\\1\\2\\4\\5",$line);
	$line = preg_replace("/<p><strong><a title=(.+)<\/strong><\/p>/i","<p><a title=.+\\1</p>",$line);
	$line = str_ireplace("</strong><strong>","",$line);
	$line = preg_replace("/ [ ]+/i"," ",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- replace any remaining useless <font> tags MS Word adds...
	// ---------------------------------------------------------------------------------------------------------------
	$line = preg_replace("/<font[^>]+>/i","",$line);
	$line = str_ireplace("</font>","",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- replace extra strong statement (sometimes occurs when you see a list item where the number is bold)
	// ---------------------------------------------------------------------------------------------------------------
	if (preg_match("/<p><strong>([0-9]+)\./i",$line)) $line = str_ireplace("<p><strong>","<p>",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- attempt to correct list items
	// --- fix strange things related to lists...
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("&#9675;","&middot;",$line);
	$line = str_ireplace('<span>&middot;&nbsp;</span>','<span>&middot;<span>&nbsp;&nbsp;  </span></span>',$line);
	$line = str_ireplace("<span>?<span>","<span>&middot; <span>",$line);
	$line = str_ireplace("<span>â—‹<span>","<span>&middot; <span>",$line);
	$line = str_ireplace("<span>o‹<span>","<span>&middot; <span>",$line);
	$line = str_ireplace('<span>o<span>&nbsp;','&acirc; ',$line);
	$line = preg_replace('/<span>o(&nbsp;+)<\/span>+/i','<span>&acirc; </span>',$line);
	$line = str_ireplace("<span>-<span>&nbsp;&nbsp;","<span>&acirc; <span>",$line);
	$line = preg_replace("/<\/span>([a-z]+.)<span>/i","</span><span>\\1<span>",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- get rid of <!--[if !supportLists]--> and <!--[endif]--> (would get to it later with comments removal but need to do this special case first for lists...)
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("<!--[if !supportLists]--><strong>","",$line);
	$line = str_ireplace("<!--[if !supportLists]-->","",$line);
	$line = str_ireplace("<!--[endif]-->","",$line);
		
	// ---------------------------------------------------------------------------------------------------------------
	// --- replace all the useless <span> tags MS Word adds...
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("  </span>","",$line);
	$line = preg_replace('/<span[^>]+>/i','',$line);
	$line = str_ireplace("<span>","",$line);
	$line = str_ireplace("</span>","",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// ---   replace all the multiple space characters that are used instead of real lists (except for <code> lines)
	// ---------------------------------------------------------------------------------------------------------------
	if (!$incode && !$foundcode && !$keepspaces) {
		$line = preg_replace("/&nbsp;(&nbsp;+)/i", " ", $line);
		$line = preg_replace("/ [ ]+/i"," ",$line);
		$line = str_ireplace(" &nbsp;"," ",$line);
		$line = str_ireplace("&nbsp; "," ",$line);
	}

	// --- even if we want to leave extra spaces in output, we must account for list items...
	//$line = preg_replace("/([0-9]+)\.([&nbsp;]+)/i", "\\1. ", $line);
	//$line = preg_replace("/([a-z]+)\.([&nbsp;]+)/i", "\\1. ", $line);
	$line = preg_replace("/([0-9]+)\.(&nbsp;)/i", "\\1. ", $line);
	$line = preg_replace("/([a-z]+)\.(&nbsp;)/i", "\\1. ", $line);

	$line = preg_replace("/<p> ([a-z]+.)/i", "<p>\\1", $line);

	// --- the next lines are just for me - delete before releasing new version...
	//$line = str_ireplace("&agrave;","&rarr;",$line);
	$line = str_ireplace("&agrave;","&rArr;",$line);


	// ---------------------------------------------------------------------------------------------------------------
	// --- The cleanup items below were added for my personal use. They shouldn't harm 
	// --- your cleanup efforts, and it's slightly possible that they will be needed by you 
	// --- also, but feel free to remove them...
	// ---------------------------------------------------------------------------------------------------------------

	// ---------------------------------------------------------------------------------------------------------------
	// --- clean up <Hx><u>...</u></Hx> tags by removing <u></u> tags - can use CSS instead
	// ---------------------------------------------------------------------------------------------------------------
	$line = preg_replace('/<h2><a name=(.+)<u>/i','<h2><a name=\\1',$line);
	$line = str_ireplace("</u></a></h2>","</a></h2>",$line);

	// ---------------------------------------------------------------------------------------------------------------
	// --- clean up a silly thing mostly specific to my article conversions
	// ---------------------------------------------------------------------------------------------------------------
	$line = str_ireplace("</strong>:&nbsp;","</strong>: ",$line);
	//echo htmlentities($line)."<br />";

	return $line;
}

// --------------------------------------------------------------------------------------------------------------
// ---	@name	cleanit
// ---	@desc 	this is the main function that does the heavy lifting 
// ---	@returns	array
// --------------------------------------------------------------------------------------------------------------
function cleanit($lines,$image_path="/images/",$imagenum=1,$alt_prefix="") {
	global $foundcode;
	
	// --- good regex reference page - http://www.evolt.org/node/22700
	// --- VERY useful example: <a href="[^"]+">[^<]+</a> will find any a href - could easily modify for MS Word garbage...

	// --- A couple of things to know: when an <a name="_Toc... is found that means it deserves it's own heading
	// --- and since FCKeditor turns those lines into <font size=""> we will just look at the size number to assign 
	// --- an appropriate heading; 5 - <h1>; 4 - <h2>; 3 - <h3>; 2 - <h4>

	global $line, $list, $item, $in_item, $current_list, $current_level;
	$item = array();
	$in_item = 0;
	$nomatch = 0;
	$list_type = array();
	$list = array();
	$current_level = "0";
	$webmastersherpa = false;
	if (getenv('HTTP_HOST')=="www.webmastersherpa.com" or getenv('HTTP_HOST')=="webmastersherpa.com") $webmastersherpa = true;

	foreach ($lines as $line_num => $line) {
		// -------------------------------------------------------------------------------------------------------
		// --- the following is a special case for my needs so just ignore...
		// -------------------------------------------------------------------------------------------------------
		/*
		if (stristr($line,"<strong><font size=\"6\">")) {
			if (stristr($line,"Chapter ? &ndash; ")) $line = str_ireplace("Chapter ? &ndash; ","Chapter ? -- ",$line);
			if (stristr($line,"Chapter ? -- ")) {
				$line = str_ireplace("<strong><font size=\"6\">","<em class=\"chapter\">",$line);
			} else {
				$line = str_ireplace("<strong><font size=\"6\">","<em class=\"chapter\">Chapter ? -- ",$line);
			}
			$line = str_ireplace("</font></strong>","</em>",$line);
		}
		*/
		// -------------------------------------------------------------------------------------------------------

		// -----------------------------------------------------------------------------------------------------------------------------
		// --- let's handle <code></code> lines a bit differently...
		// -----------------------------------------------------------------------------------------------------------------------------
		if (stristr($line,"<code")) $incode = true;
		if (stristr($line,"</code")) $incode = false;

		// -----------------------------------------------------------------------------------------------------------------------------
		// --- tidy up the line...
		// -------------------------------------------------------------------------------------------------------
		if (!$incode) $line = tidy($line,"h1","h1","h2","h3","");

		// ---------------------------------------------------------------------------------------------------------------
		// --- NOTE: this function can handle as many levels of lists as possible with one major exception: NO symbol type can be used more than once
		// ---       if so, the function will assume that was a previous level and will close it...
		// --- NOTE: we must handle two different situations: 1. a list item is only one line long so <p></p> will
		// ---       be on the same line and 2. a list item runs multiple lines so the <p> is on one line and the </p> 
		// ---       is on a subsequent line.
		// ---------------------------------------------------------------------------------------------------------------
		$match = false;
		$tag["numeric"] = "ol";
		$tag["upperalpha"] = "ol";
		$tag["loweralpha"] = "ol";
		$tag["upperroman"] = "ol";
		$tag["lowerroman"] = "ol";
		$tag["middot"] = "ul";
		$tag["sect"] = "ul";
		$tag["circle"] = "ul";

		// --- sometimes we just need to manually end a list in the code - you can do that by adding a line where you
		// ---   want the list to be closed with the following code: <endlist>
		// --- you could just add a </ul> but this will keep the cleanit list tracking accurate and (hopefully) won't 
		// ---   end up closing lists after the fact, leading to html that doesn't validate...
		$endlist = false;
		if (stristr($line,"<endlist>")) {
			$endlist = true;
			$line = str_ireplace("<endlist>","",$line);
		}

		if (preg_match("/<p>([0-9]+)\)/i",$line) || preg_match("/<p>([0-9]+)\./i",$line) || preg_match("/<p>(\([a-z]\))/i",$line) || preg_match("/<p>([a-z]\))/i",$line) || preg_match("/<p>([a-z])\./i",$line) || preg_match("/<p>([IVXMDC]+)\./",$line) || preg_match("/<p>([ivxmdc]+)\./",$line) || preg_match("/<p>&middot;/i",$line) || preg_match("/<p>&sect;/i",$line) || preg_match("/<p>&acirc;/i",$line)) {
			$match = true;
			$in_item = 1;
			$current_list = "";
			$nomatch = 0;
		}
		// --- check if it's a table inside a list item - don't want to add/close lists if so...
		if (stristr($line,"<table")) $table = true; 

		// ----------------------------------------------------------------------------------------------------------------
		// --- First, see if the line is already a properly formed list item - sometimes FCKeditor does get it right after all. If so, skip this code...
		// ---
		if (preg_match("/<ul/i",$line) || preg_match("/<ol/i",$line) || preg_match("/<li/i",$line) || preg_match("/<\/li>/i",$line) || preg_match("/<\/ul>/i",$line) || preg_match("/<\/ol>/i",$line)) {
			$wordlist = true;
			if (stristr($line,"<li>") && !stristr($line,"</li>")) $line .= "</li>\n";
			if (stristr($line,"</li>") && !stristr($line,"<li>")) $line = str_ireplace("</li>","",$line);
		} else {
			// --- not sure how accurate the roman numeral match is - could catch other things by mistake - investigate later...
			if (preg_match("/<p>([0-9]+)\)/i",$line)) {
				$search = "<p>([0-9]+)\)";
				$current_list = "numeric";
				$class = "";
			}
			if (preg_match("/<p>([0-9]+)\./i",$line)) {
				$search = "<p>([0-9]+)\.";
				$current_list = "numeric";
				$class = "";
			}
			if (preg_match("/<p>([a-z])\./",$line)) {
				$search = "<p>([a-z])\.";
				$current_list = "loweralpha";
				$class = " class=\"loweralpha\"";
			}
			if (preg_match("/<p>(\([a-z]\))/i",$line)) {
				$search = "<p>(\([a-z]\))";
				$current_list = "loweralpha";
				$class = " class=\"loweralpha\"";
			}
			if (preg_match("/<p>([a-z]\))/i",$line)) {
				$search = "<p>([a-z]\))";
				$current_list = "loweralpha";
				$class = " class=\"loweralpha\"";
			}
			if (preg_match("/<p>([A-Z])\./",$line)) {
				$search = "<p>([A-Z])\.";
				$current_list = "upperalpha";
				$class = " class=\"upperalpha\"";
			}

			// --- this will catch roman numeral lists but it needs work - in particular, how can I 
			// --- reasonably differentiate between a C roman numeral list and a C alpha list? For now,
			// --- I am limiting the possible matched - so long as a alpha list doesn't reach i/I we
			// --- should be fine. Maybe I should pass a variable to toggle roman check on or off? 
			if (preg_match("/<p>([ivxm]+)\./",$line)) {
				$search = "<p>([ivxmdc]+)\.";
				$current_list = "lowerroman";
				$class = " class=\"lowerroman\"";
			}
			if (preg_match("/<p>([IVXM]+)\./",$line)) {
				$search = "<p>([IVXMDC]+)\.";
				$current_list = "upperroman";
				$class = " class=\"upperroman\"";
			}

			if (preg_match("/<p>&middot;/i",$line)) {
				$search = "<p>&middot;";
				$current_list = "middot";
				$class = " class=\"disc\"";
			}
			if (preg_match("/<p>&sect;/i",$line)) {
				$search = "<p>&sect;";
				$current_list = "sect";
				$class = " class=\"square\"";
			}
			if (preg_match("/<p>&acirc;/i",$line)) {
				$search = "<p>&acirc;";
				$current_list = "circle";
				$class = " class=\"circle\"";
			}
	
			// ------------------------------------------------------------------------------------------------------------
			// --- Does beginning of line indicate a list item? 
			// ------------------------------------------------------------------------------------------------------------
			if ($match) {
				// ------------------------------------------
				// --- If Yes, does this level already exist? 
				// ---
				if (!$list) $list = array();
				if (in_array($current_list, $list)) {
					// ----------------------------------------
					// --- If Yes, mark this line as a new item 
					// ---
					$line = preg_replace("/".$search." /i","<li>",$line);

					// -----------------------------------------------------------------------------------------------------
					// --- Now, was the last item a deeper level than this one? If Yes, close the deeper level list and delete from $list array...
					// ---
					$current_level = array_search($current_list,$list);
					$item[$current_level]++;
					if ((count($list)-1)>$current_level || $endlist) {
						//$line .= "<br>bob - ".count($list)."; tag[".$list[count($list)-1]."] is ".$tag[$list[count($list)-1]]."; item[".(count($list)-1)."] is ".$item[count($list)-1]." item[0] is ".$item[0]."<br>";
						if ($tag[$list[count($list)-1]]) $line = "</".$tag[$list[count($list)-1]]."><br />\n" . $line;
						$item[count($list)-1] = 0;
						unset($list[count($list)-1]);
					}
					// -----------------------------------------------------------------------------------------------------
					// --- repeat for occasion where two nested lists both end...
					// ---
					if ((count($list)-1)>$current_level || $endlist) {
						//$line .= "<br>bob2 - ".count($list)."; tag[".$list[count($list)-1]."] is ".$tag[$list[count($list)-1]]."; item[".(count($list)-1)."] is ".$item[count($list)-1]." item[0] is ".$item[0]."<br>";
						if ($tag[$list[count($list)-1]]) $line = "</".$tag[$list[count($list)-1]]."><br />\n" . $line;
						$item[count($list)-1] = 0;
						unset($list[count($list)-1]);
					}
				} else {
					// ---
					// --- If no, create new list
					// ---
					//if (stristr($line,"match goes here")) { print_r($list); echo "..<br>"; }
					//if (stristr($line,"match goes here")) echo "match is " . $match . "; list count is " . count($list) . "; current level is ". $current_level . "; items is " . $in_item ."; current list is ". $current_list . "; just closed is " . $jsutclosed ."<br>\n";
					$replace = "<".$tag[$current_list].$class.">\n<li>";
					$line = preg_replace("/".$search." /i",$replace,$line);		// -- 7-18-2011 - JB - replaced eregi_replace but not 100% sure if maybe I need to escape the $sarch variable when using preg_replace
					$count = count($list);
					$list[$count] = $current_list;
					$item[$count] = 1;
				}
	
				//$line .= "<br />" . "match is " . $match . "; list count is " . count($list) . "; item is " . $item[count($list)-1] ."; current list is ". $current_list . "; list[0] is " . $list[0] . "; list[1] is " . $list[1] . "; list[2] is " . $list[2] . "<br>\n";
			} else {
				$nomatch++;

				// --------------------------------------------------------------------------------------------------------
				// --- only worry about closing lists if (1) we're in a list; (2) there are no items
				// ---      (3) we're not in a list and displaying a table; (4) we aren't on a non-item text  
				// ---      line after just ending a list that FCKeditor converted properly from word 
				// --------------------------------------------------------------------------------------------------------
				if (count($list)>0 && $in_item==0 && !$table && !$wordlist && !$last_was_wordlist) {
					// --- we should track number of blank lines to see if we need to close lists (below) but in case 
					// ---   we come across a new heading we should just go ahead and close all lists now...
					if (stristr($line,"<em") || stristr($line,"<h1>") || stristr($line,"<h2>") || stristr($line,"<h3>") || $endlist) {
						for ($inc=count($list); $inc>=0; $inc--) {
							if ($list[$inc] && $tag[$list[$inc]]) $line = "</".$tag[$list[$inc]]."><br />\n".$line;
						}
						unset($list);
						$current_level = "";
						$nomatch = 0;
					}

					// -------------------------------------------------------------------------------------------------
					// --- if we encounter an image in a list item we don't want to increment our nomatch; in fact, 
					// --- since an image usually has a <br /> before and after, we will reset nomatch
					// -------------------------------------------------------------------------------------------------
					if ($nomatch<2 && stristr($line,"<img")) $nomatch = 0;
					//echo $line = "nomatch - " . $nomatch . "; current level is " .$current_level . "; list is " . count($list) . $line;

					// -------------------------------------------------------------------------------------------------
					// --- If we're in a multi-line list item, we won't encounter a </p> so no need to worry about 
					// --- closing the current list unless we do. BUT, if we just closed a list the last time and 
					// --- if the current line is a </p> or <br /> then we will want to close the current list
					// -------------------------------------------------------------------------------------------------
					//echo $nomatch . " - " . htmlentities($line)."<br>";
					if ( $list[$current_level] && (stristr(substr($line,-6),"</p>") || ($nomatch>=2 && substr($line,0,6)=="<br />")) ) {
					//if ($list[$current_level] && (stristr(substr($line,-6),"</p>") || substr($line,0,6)=="<br />")) {
						//$line = "<br>joe - $in_item ; nomatch is " . $nomatch . "; current level is ".$current_level."; list count is ".count($list)."; tag[".$list[count($list)-1]."] is ".$tag[$list[count($list)-1]]."; item[".(count($list)-1)."] is ".$item[count($list)-1]." item[0] is ".$item[0]."<br>" . $line;
						if ($tag[$list[$current_level]]) {
							$line = "</".$tag[$list[$current_level]]."><br />\n" . $line;
							unset ($list[$current_level]);
							$current_level--;
							if ($current_level<0) $current_level = 0;
							if (count($list)==0) $list = array();
						}
					}
					// --- if we are in a multi-level list and have 2+ nomatches then we should close 2 levels...
					if ($list[$current_level] && $nomatch>=2 && (substr($line,0,6)=="<br />" || stristr($line,"<img") || stristr($line,"<p"))) {
						if ($tag[$list[$current_level]]) {
							$line = "</".$tag[$list[$current_level]]."><br />\n" . $line;
							unset ($list[$current_level]);
							$current_level--;
							if ($current_level<0) $current_level = 0;
							if (count($list)==0) $list = array();
						}
					}
					// --- since we need to have 2 line breaks to indicate closing of multi-level list let's remove
					// --- the <br /> line(s) so we don't violate W3C standards (note we add a <br /> after closing list 
					if ($nomatch>=1) {
						$line = str_ireplace("<br />","",$line);
					} else {
						$line = str_ireplace("</p>","<br />",$line);
					}

				}
				//echo $nomatch . " - " . htmlentities($line)."<br>";
				//$line .= "nomatch is " . $nomatch . "; list count is " . count($list) . "; item is " . $item[count($list)-1] ."; current list is ". $current_list . "; list[0] is " . $list[0] . "; list[1] is " . $list[1] . "; list[2] is " . $list[2] . "<br>\n";

				// ---
				// --- let's close any manually added <endlist> that weren't handled already...
				if ($endlist) {
					//$line .= "<br>jackie<br>\n";
					$line = "</".$tag[$list[count($list)-1]].">\n" . $line;
					$item[count($list)-1] = 0;
					unset($list[count($list)-1]);
				}
			}
		}

		//echo $nomatch . " - " . htmlentities($line)."<br>";
		// ------------------------------------------------------------------------------------------------------------
		// --- If we're in a list item and we have a ending </p> let's close that list item...
		// ------------------------------------------------------------------------------------------------------------
		if ($in_item!=0 && stristr(substr($line,-6),"</p>")) {
			$line = str_ireplace("</p>","</li>",$line);
			$in_item = 0;
			//echo htmlentities($line)."<br>";
			//$line .= "just set item to zero<br>\n";
		}


		if (stristr($line,"</table")) $table = false;
		if ($last_was_wordlist) $last_was_wordlist = false;
		if ( (stristr($line,"</ul") || stristr($line,"</ol")) && $wordlist) {
			$wordlist = false;
			$last_was_wordlist = true;
		}
		
		// -----------------------------------------------------------------------------------------------------------
		// ------ handle exisitng lists (repace type="" for those that FCKeditor did convert) {do we want to keep the start= statements?}
		$line = preg_replace('/ type=[^>]+>/i','>',$line);

		// -----------------------------------------------------------------------------------------------------------
		// --- replace image with best guess of the right one to use. There are 2 ways to handle this. First, 
		// ---    we can copy all the images created when saving a MS Word document as filtered HTML to the 
		// ---    /images/<filename directory> (typically imgage0XX.XXX)
		// ---    Alternatively, FCKedtior creates multiple images (not sure how/why) in a temp directory. In 
		// ---    theory we could copy those files over to the same directory and just use the image # found in 
		// ---    the html created by FCKedtitor. I am choosing the former...
		// -----------------------------------------------------------------------------------------------------------
		$endpos = 0;
		if (!$numimages) {			// --- use for tracking when v:shapes exists instead of image#...
			$numimages = 0;
			if ($imagenum>1) $numimages = $imagenum;
		}
		if (stripos($line,".jpg") || stripos($line,".gif") || stripos($line,".png") || stristr($line,"<img") || stripos($line,"v:imagedata")) {
			// --- handle case where just closed a list item or entire list - need to keep the closing tag...(for wembastersherpa demo)
			if (substr($line,0,5)=="</li>" || substr($line,0,5)=="</ul>" || substr($line,0,5)=="</ol>") $pre = substr($line,0,5);
			$numimages++;
			$startpos = stripos($line,"image");
			$endpos = stripos($line,".jpg");
			if ($endpos==0) $endpos = stripos($line,".gif");
			if ($endpos==0) $endpos = stripos($line,".png");
			// --- handle case where no image # is created by FCKeditor but a v:shapes instead...
			if (!$startpos || isset($_POST["imagestartnum"])) {
				$vshapes = true;
				$startpos = stripos($line,"v:shapes=");
				$endpos = stripos($line,"/>");
				if ($numimages<1000) $image = $image_path."image".$numimages.".gif";
				if ($numimages<100) $image = $image_path."image0".$numimages.".gif";
				if ($numimages<10) $image = $image_path."image00".$numimages.".gif";
			} else {
				$image = $image_path.substr($line,$startpos,($endpos-$startpos+4));
			}
			if (!$webmastersherpa && $image_path!="/images/") {
				$size = @getimagesize("..".$image); 
				// --- may have guessed to use .gif when actually should have used .jpg so try again if no size...
				if (!$size) {
					$image = substr($image,0,strlen($image)-3)."jpg";
					//echo $image ."- " . $numimages. "<br>";
					$size = @getimagesize("..".$image); 
				}
				$width = $size[0]; 
				$height = $size[1]; 
				//echo $image . " imagesize - " . $width . "<br>";
			}
			$temp = '<img src="'.$image.'" ';
			if ($width) $temp .= 'width="'.$width.'" ';
			if ($height) $temp .= 'height="'.$height.'" ';
			//$alt = ' alt="'.$alt_prefix.' image number '.$imagenum.'"';
			$alt = ' alt="image number '.$imagenum.'"';
			$temp .= $alt . ' />';
			if ($webmastersherpa) {
				// --- for demo script on Webmastersherpa site just show the sherpa logo...
				$image_path = "/images/";
				// --- if we see src=" in the line code assume that some using the demo specified an image and leave it alone...
				if (!stristr($line,"src="))	$line = $pre . '<img src="/images/logo.gif" /><br />';
			}
			$imagenum++;
		}

		// --- if I wanted to try method 2, the following line would be a start but 
		// --- $temp = "<img src=\"".$imagepath."clip_image\" /><br />";
		// --- $line = preg_replace("/<img+[^>]+image([0-9]+)(\.[a-zA-Z]{3}).+>/i",$temp\\1\\2,$line);
		
		if ($vshapes && !$webmastersherpa) {
			$line = preg_replace("/<img+[^>]+>/i",$temp,$line);
			$vshapes = false;
		} else {
			if ($image_path=="/images/") {		// --- this means that the entry is direct from admin rather than via cleanit/add to db
				$line = $line;
			} else {
				$line = preg_replace("/<img+[^>]+image([0-9]+)(\.[a-zA-Z]{3}).+>/i",$temp,$line);
			}
		}
		// -----------------------------------------------------------------------------------------------------------
		
		// -----------------------------------------------------------------------------------------------------------
		// --- replace any comments (e.g. <!-- [if !supportFootnotes]-->, <!--[endif]-->, etc.
		$line = preg_replace("/<![^>]+>/i","",$line);

		// -----------------------------------------------------------------------------------------------------------
		// --- get rid of <a name=...> code...
		$count = substr_count($line,"<a name");
		// echo "number of name occurrences is " . $count;
		for ($i=0; $i<$count; $i++) {
			if (stristr($line,"<a name")) {
				$start = strpos($line, "<a name");
				$temp = substr($line,$start);
				$stop = strpos($temp, ">")+$start+1;
				$line1 = substr($line,0,$start);
				$line2 = substr($line,$stop);
				$line = $line1 . $line2;

				// now find the </a> and delete it...
				$start2 = strpos($line, "</a>");
				$line1 = substr($line,0,$start2);
				$stop2 = $start2+4;
				$line2 = substr($line,$stop2);
				$line = $line1 . $line2;
			}
		}

		// --- replace extra/multiple spaces (won't make a difference in display but tidies up the code...
		$line = preg_replace("/ [ ]+/i"," ",$line);
		$line = str_ireplace("<li> ","<li>",$line);
		$line = str_ireplace("<li>&nbsp;","<li>",$line);

		// --- Sometimes there are blank lines after FCKeditor works on MS Word pastes so get rid of them....
		if (strlen($line)<=3) {
			$line = str_replace("\r","",$line);
			$line = str_replace("\n","",$line);
		}

		// --- catch any line that is still encased in <p></p> statements...
		//$line = preg_replace("/<p[^>]+>/i","",$line);		// commented out 7-31-07 b/c it was screwing up <pre>...</pre> tags; replaced with next line but haven't fully tested it yet...
		$line = str_ireplace("<p[^r][^>]+>","\n",$line);
		$line = str_ireplace("<p>","\n",$line);
		$line = str_ireplace("</p>","<br />\n",$line);

		// --- replace &nbsp;/ to help with later footnote manipualation
		$line = str_ireplace("&nbsp;/"," /",$line);
		$line = str_ireplace("/&nbsp;","/ ",$line);

		// --- sometimes an <a title for footnote doesn't have a closing </a> tag - let's fix that...
		if (stristr($line,"<a title=") && stristr($line,"</h1>")) if (!stristr($line,"</a></h1>")) $line = str_ireplace("</h1>","</a></h1>",$line);
		if (stristr($line,"<a title=") && stristr($line,"</h2>")) if (!stristr($line,"</a></h2>")) $line = str_ireplace("</h2>","</a></h2>",$line);
		if (stristr($line,"<a title=") && stristr($line,"</h3>")) if (!stristr($line,"</a></h3>")) $line = str_ireplace("</h3>","</a></h3>",$line);
		if (stristr($line,"<a title=") && stristr($line,"</h4>")) if (!stristr($line,"</a></h4>")) $line = str_ireplace("</h4>","</a></h4>",$line);

		// --- use the following to handle case where a heading has no associated text but holds other sub-entries
		if (stristr($line,"<h2") && stristr($last_line,"<h1")) $line = "<br />\n" . $line;
		if (stristr($line,"<h3") && stristr($last_line,"<h2")) $line = "<br />\n" . $line;
		if (stristr($line,"<h4") && stristr($last_line,"<h3")) $line = "<br />\n" . $line;
		if (stristr($line,"<h5") && stristr($last_line,"<h4")) $line = "<br />\n" . $line;
		$last_line = $line;

		$converted_lines .= $line;
	}

	// --- now going to add a <div></div> wrapper around everything - for some reason this prevents 
	// --- FCKeditor from adding back <p> tags. NOTE: you can comment out this line and instead change the 
	// --- EnterMode option in fckconfig.js from p to br... 
	$converted_lines = "<div>".$converted_lines."</div>";

	// --- get rid of extra <br /> at beginning and extra &nbsp; at end...
	$converted_lines = str_ireplace("<div><br />","<div>",$converted_lines);
	$converted_lines = str_ireplace("&nbsp;</div>","</div>",$converted_lines);
	$converted_lines = str_ireplace("&nbsp;<br />","<br />",$converted_lines);
	$converted_lines = preg_replace("/ [ ]+/i"," ",$converted_lines);

	$converted_lines = str_ireplace("<br /></ol>","</ol>",$converted_lines);

	$converted_lines = preg_replace("/<v:[^>]+>/i","",$converted_lines);
	$converted_lines = preg_replace("/<\/v:[^>]+>/i","",$converted_lines);
	$converted_lines = preg_replace("/<o:[^>]+>/i","",$converted_lines);
	$converted_lines = preg_replace("/<\/o:[^>]+>/i","",$converted_lines);

	// --- get rid of extra spaces and that immediately follow a <li>...
	$converted_lines = preg_replace("/<li>(&nbsp;)+/i", "<li>", $converted_lines);

	// --- get rid of extra extra <strong> that are sometimes put back in (e.g., in Japanese text)
	$converted_lines = preg_replace("/(<strong>)+/i","<strong>",$converted_lines);
	$converted_lines = preg_replace("/(<\/strong>)+/i","</strong>",$converted_lines);

	// --- get rid of width statement in a table...
	if (!$tablewidths || $tablewidths!="N" && stristr($converted_lines,"<td")) $converted_lines = preg_replace("/ width=\"[^\"]+\"/i","",$converted_lines);

	
	// ---------------------------------------------------------------------------------------------------------------
	// --- finally, let's get rid of extra blank lines
	// ---------------------------------------------------------------------------------------------------------------
	$converted_lines = removeEmptyLines($converted_lines);

	return $converted_lines;
}
?>