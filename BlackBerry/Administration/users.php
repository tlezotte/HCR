<?php
/**
 * Request System
 *
 * track.php track shipments.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 

/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

/* ----- START $_POST ----- */
switch ($_POST['action']) {
	/* ---------- EMAIL ACCESS REQUEST FORM ---------- */
	case "requestform":
			$url = "http://".$_SERVER['SERVER_NAME']."/register";
			$sendTo = $_POST['email']."@".$default['email_domain'];
			
			require("phpmailer/class.phpmailer.php");
		
			$mail = new PHPMailer();
			
			$mail->From     = $default['email_from'];
			$mail->FromName = $default['title1'];
			$mail->Host     = $default['smtp'];
			$mail->Mailer   = "smtp";
			$mail->AddAddress($sendTo);
			$mail->Subject = $default['title1'].": Access Request";

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<p><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></p>
<br>
Please select the link listed below to display the Access Request Form.<br>
<br>
URL: <a href="$url">$url</a><br>
</body>
</html>
END_OF_HTML;

			$mail->Body = $htmlBody;
			$mail->isHTML(true);
			
			if(!$mail->Send())
			{
			   echo "Message was not sent";
			   echo "Mailer Error: " . $mail->ErrorInfo;
			}
			
			// Clear all addresses and attachments for next loop
			$mail->ClearAddresses();
			$mail->ClearAttachments();	
	break;
}
/* ----- END $_POST ----- */

/* Update Summary */
Summary($dbh, 'Users - BlackBerry', $_SESSION['eid']);
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $language['label']['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 your company" />
<link href="../handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="center"><a href="../home.php"><img src="/Common/images/company200.gif" alt="your company" name="company" width="200" height="50" border="0"></a></td>
  </tr>
  <tr>
    <td><div align="center">
      <?= $language['label']['title1']; ?>    
    </div></td>
  </tr>
  <tr>
    <td><div align="center"><strong> User Administration </strong></div></td>
  </tr>
  <tr>
    <td height="10" class="center"><img src="../../images/spacer.gif" width="10" height="10"></td>
  </tr>
</table>
<table width="240" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="15" nowrap><img src="/Common/images/company_Bullet.gif" width="11" height="15"></td>
    <td><form name="form1" method="post" action="edit_users.php">
      <input name="letter" type="text" id="letter" size="7" maxlength="10">
      <input name="edit" type="submit" value="Edit" class="button">
    </form></td>
  </tr>
  <tr>
    <td width="15"><img src="/Common/images/company_Bullet.gif" width="11" height="15"></td>
    <td><form action="add_users.php" method="post" name="form2" id="form2">
      <input name="letter" type="text" id="letter" size="7" maxlength="10">
      <input name="add" type="submit" value="Add" class="button">
    </form></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="10"><img src="../../images/spacer.gif" width="10" height="10"></td>
  </tr>
  <tr>
    <td width="15"><img src="/Common/images/company_Bullet.gif" width="11" height="15"></td>
    <td valign="top"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="form3" id="form3">
      <input name="email" type="text" id="email" size="7" maxlength="10">
      <input name="action" type="hidden" id="action" value="requestform">
      <input name="request" type="submit" value="Request" class="button">
    </form></td>
  </tr>
</table>
</body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
?>