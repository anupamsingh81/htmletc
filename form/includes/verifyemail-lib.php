<?php
/*
	File : verifyemail_lib.php
	Version : 1.0
	Date : 20. july 2002
	Author : Lars B. Jensen, lars.jensen@ljweb.biz

	Module Description
	Module to verify if email is valid in three different levels.
	Level 1 : Correct formatting of email
	Level 2 : Server exists in DNS as MX record
	Level 3 : Does the user exist on the email given
	
	!!! Warning !!!
	Level 3 checking does take a considerable amount of time !!!


	Note
	Functions used in script is not implemented in windows versions of PHP !
	
	SMTP Reference
	http://www.ietf.org/rfc/rfc0821.txt?number=821
	

	Public Functions
	--------------------------------------------------------------
	function verifyemail_validateemail($email)
	function verifyemail_validatehost($email, $return_mxhost=0)
	function verifyemail_validateexists($email)

	Private Functions
	--------------------------------------------------------------
	function verifyemail_closesocket($socket)
	function verifyemail_localhost()
*/

	function verifyemail_validateemail($email) {
		if (!preg_match("/^([\w|\.|\-|_]+)@([\w||\-|_]+)\.([\w|\.|\-|_]+)$/i", $email)) {
			return false;
			exit;
		}
		return true;
	}


	function verifyemail_validatehost($email, $return_mxhost=0) {
		if (!verifyemail_validateemail($email)) {
			return false;
			exit;
		}
	
		list($local,$domain) = explode("@",$email,2);
		
		$mxhosts = array();
		if(!checkdnsrr($domain, "MX") || !getmxrr($domain, $mxhosts)) {
			return false;
			exit;
		}

		if ($return_mxhost) {
			return $mxhosts;
			exit;
		}
		
		return true;
	}


	function verifyemail_validateexists($email) {
		$mxhosts = verifyemail_validatehost($email, true);
		
		if (!is_array($mxhosts)) {
			return false;
			exit;
		}

		$forwardaddress = "";
		$found = false;
		$localhost = verifyemail_localhost();

		$mxsize = sizeof($mxhosts);
		for($i=0; $i<$mxsize; $i++)	{
			$socket = fsockopen($mxhosts[$i], 25);

			if(!$socket) continue;

			$foo = fgets($socket, 4096);

			# 220 <domain> Service ready
			if(!preg_match("/^220/i", $foo)) { 
				verifyemail_closesocket($socket);
				continue;
			}

			fputs($socket, "HELO ".$localhost."\r\n");
			$foo = fgets($socket, 4096);
			while (preg_match("/^220/i", $foo)) {
				$foo = fgets($socket, 4096);
			}

			fputs($socket, "VRFY ".$email."\r\n");
			$foo = fgets($socket, 4096);

			# 250 Requested mail action okay, completed
			if(preg_match("/^250/i", $foo)) {
				$found = true;
				verifyemail_closesocket($socket);

				break;
			}

			# 550 Requested action not taken: mailbox unavailable [E.g., mailbox not found, no access]
			if(preg_match("/^550/i", $foo)) {
				verifyemail_closesocket($socket);
				continue;
			}

			fputs($socket, "MAIL FROM: <".$email.">\r\n");
			$foo = fgets($socket, 4096);

			fputs($socket, "RCPT TO: <".$email.">\r\n");
			$foo = fgets($socket, 4096);

			# 250 Requested mail action okay, completed
			# 251 User not local; will forward to <forward-path>
			if(preg_match("/^[220|251]/i", $foo)) {
				$found = true;
				verifyemail_closesocket($socket);

				break;
			}
		
			verifyemail_closesocket($socket);
		}

		return $found;
	}


	function verifyemail_closesocket($socket) {
		fputs($socket, "QUIT\r\n");
		fclose($socket);
		
		return true;
	}


	function verifyemail_localhost() {
		$localhost = getenv("SERVER_NAME");
		if (!strlen($localhost)) $localhost = getenv("HOST");

		return $localhost;
	}
?>