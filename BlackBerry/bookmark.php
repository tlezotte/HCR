<?php 
/**
 * Request System
 *
 * bookmark.php emails link and display instructions.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */

 
/**
 * - Start Page Loading Timer
 */
include_once('../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Check User Access
 */
require_once('../security/check_user.php'); 
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ----- START $_POST VARIABLES ----- */
switch ($_POST['action']) {
	/* ---------- EMAIL ACCESS REQUEST FORM ---------- */
	case "requestlink":
			$url = "https://".$_SERVER['SERVER_NAME'].$default['url_home']."/BlackBerry/index.php";
			$data = $dbh->getRow("SELECT * FROM Standards.Employees WHERE eid=".$_SESSION['eid']);
			
			require("phpmailer/class.phpmailer.php");
		
			$mail = new PHPMailer();
			
			$mail->From     = $default['email_from'];
			$mail->FromName = $default['title1'];
			$mail->Host     = $default['smtp'];
			$mail->Mailer   = "smtp";
			$mail->AddAddress($data['email']);
			$mail->Subject = $default['title1'].": Access Request";

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<br>
Select the link listed below to display the BlackBerry version of the $default[title1].<br>
Remember to bookmark this page in your BlackBerry after it loads.<br>
<br>
URL: <a href="$url">$url</a><br>
</body>
</html>
END_OF_HTML;
/* HTML message */

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

			/* Update Summary */
			Summary($dbh, 'BlackBerry Access', $_SESSION['eid']);
	break;
}
/* ----- END $_POST VARIABLES ----- */



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $language['label']['title1']; ?></title>
  <!-- InstanceEndEditable -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>
  <?php if ($default['pageloading'] == 'on') { ?>
  <script language="JavaScript" src="/Common/Javascript/pageloading.js" type="text/javascript"></script>
  <?php } ?>
  <script language="JavaScript" src="/Common/Javascript/pointers.js" type="text/javascript"></script>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --><!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
  </head>

  <body <?= $ONLOAD; ?>>
    <?php if ($default['pageloading'] == 'on') { ?>
	<div id="hidepage" class="loadpage"> 
	<table width=100%><tr><td>&nbsp;&nbsp;<img src="/Common/images/pageloading.gif" width="200" height="45" align="absmiddle"></td></tr></table></div> 
	<?php } ?>  
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <img src="/Common/images/companyPrint.gif" alt="your company" name="Print" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../index.php"><img name="company" src="/Common/images/company.gif" width="300" height="50" border="0" alt="<?= $language['label']['title1']; ?> Home"></a></td>
          <td align="right" valign="top">
            <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable -->          </td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3">

            <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4"></td>

                  <td colspan="4">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td class="BGColorDark" valign="top" rowspan="2">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4"></td>
                </tr>

                <tr>
                  <td class="BGGrayLight" rowspan="3"></td>
                  <td class="BGGrayMedium" rowspan="3"></td>
                  <td class="BGGrayDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3">
				<!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_Spacer.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="../images/t.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>

                  <td class="BGColorDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="2"></td>
                  <td class="BGColorDark" rowspan="2"></td>
                  <td class="BGColorDark" rowspan="2"></td>
                  <td class="BGGrayDark" rowspan="2"></td>
                  <td class="BGGrayMedium" rowspan="2"></td>
                  <td class="BGGrayLight" rowspan="2"></td>
                </tr>

                <tr>
                  <td class="BGColorDark" width="100%">
				  <?php 
				  	if (isset($_SESSION['username'])) {
				  ?>
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="../Administration/user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>
				  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                  </td>
                </tr>

                <tr>
                  <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25"></td>

                  <td valign="top" colspan="2">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4"></td>
                </tr>

                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4"></td>

                  <td>
                    <table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                      <tbody>
                        <tr>
                          <td>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3"></td>

                  <td colspan="7">
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" --><br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10" valign="top"><br>
    <br></td>
    <td><table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="DarkHeader">&nbsp;<img src="/Common/images/bb_bullet.gif" width="20" height="20" align="texttop"> <strong>Features</strong></td>
          <td rowspan="11"><img src="../images/spacer.gif" width="20" height="10"></td>
          <td rowspan="11" bgcolor="#001762"><img src="../images/spacer.gif" width="5" height="10"></td>
          <td rowspan="11"><img src="../images/spacer.gif" width="20" height="10"></td>
          <td><img src="/Common/images/bb_logo.gif" width="135" height="40" border="0"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td nowrap><span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>View Purchase Requests </td>
          <td height="20"><form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
		      <img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Press the
              <input name="send" type="image" id="send" src="../images/button.php?i=b150.png&l=Send Email Link" align="bottom" border="0"> 
              Button. 
              <input name="action" type="hidden" id="action" value="requestlink">
			 </form></td>
        </tr>
        <tr>
          <td nowrap><span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>Approve Purchase Requests </td>
          <td height="20" nowrap><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> You will receive an email on your BlackBerry with the Link.</td>
        </tr>
        <tr>
          <td><span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>Track Shippments </td>
          <td height="20"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Select the <strong>Link</strong> from the email. </td>
        </tr>
        <tr>
          <td><?php if ($_SESSION['hcr_access'] >= 1) { ?>
            <span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>User Administration<?php } ?></td>
          <td height="20"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> After the Login Page loads on your BlackBerry, bookmark it. </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td height="20"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Push the <strong>Click Wheel</strong> in. </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Select <strong>Add Bookmark</strong> from menu. </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Click <strong>Add</strong> from the Bookmark popup.</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class="MessageBoxLink"><div align="center">Leave this page open until you have completed the instructions.</div></td>
        </tr>
        
        
      </table></td>
  </tr>
</table>
<br>
  <!-- InstanceEndEditable --><br>
    <br>   <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td colspan="2">
          </td>
        </tr>

        <tr>
          <td>
          </td>
          <td rowspan="2" valign="bottom"><img src="images/c-skir.gif" alt="" width="19" height="20" align="absmiddle" id="noPrint"></td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" nowrap><?php include('../include/copyright.php'); ?></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" -->
                  <?php include($default['FS_HOME'].'/include/version.php'); ?>
                <!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td colspan="2">
          </td>
        </tr>
      </tbody>
  </table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td colspan="2">
          </td>
        </tr>
      </tbody>
  </table>
   <?php if ($_SESSION['hcr_access'] >= 1) { ?>
  	<div class="TrainVisited" id="noPrint">
    <?php StopLoadTimer(); ?>
    <img src="../images/spacer.gif" width="50" height="16" align="absmiddle">
    <?= onlineCount(); ?>
	</div>
   <?php } ?>
    <br>
	
  </body>
<!-- InstanceEnd --></html>


<?php 
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>