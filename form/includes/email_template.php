<?php
$body = "
<!DOCTYPE HTML PUBLIC \"-//W3C//Dtd HTML 4.0 transitional//EN\">
<html>
<head>
<title>Email Message (generated by phpAddEdit)</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
</head>
<body style=\"font: 12px Arial, Helvetica, sans-serif\">


<div align=\"center\">
<table style=\"border: 1px solid black\" cellspacing=\"0\" cellpadding=\"0\" width=\"582\">
  <tbody>

  <tr bgcolor=\"#EFF3F8\">
	<td colspan=\"2\">
	  <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
	  <tr bgcolor=\"#EFF3F8\">
		<td style=\"border-bottom: 1px solid black\">Logo Goes Here</td>
		<td align=\"center\" valign=\"middle\" style=\"border-bottom: 1px solid black\"><div style=\"font: bold 14px Arial, Helvetica, sans-serif; color: blue\">Tagline Goes Here</div></td>
	  </tr>
	  <tr bgcolor=\"#f5f5f5\"><td colspan=\"2\" style=\"border-top: 1px solid silver; height: 2px\"></td></tr>
	  </table>
	</td>
  </tr>

  <tr><td style=\"padding-left: 4px; padding-right: 4px\">
  ";

	// ---------------------------------------------------------
	// --- Edit above and below but leave the following line in!
	// ---------------------------------------------------------
	$body .= $mailBody;
	// ---------------------------------------------------------
	// ---------------------------------------------------------

  $body .= "
  </td></tr>

  <tr>
    <td colspan=\"2\" style=\"font: 10px verdana; border-top: 1px solid black; backgroun-color: #EFF3F8\" align=\"left\" bgcolor=\"#EFF3F8\">&nbsp;
	Company Name - copyright &copy; ".date("Y")." | Address Info
	</td>
  </tr>
  </tbody>
</table>
</div>

</body>
</html>
";

//echo $body;
?>