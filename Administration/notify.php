<?php 
/**
 * Request System
 *
 * notify.php send an email notification to all users.
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
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm 
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
 * - Check users page access
 */
require_once('../security/check_access.php');

/**
 * - Config Information
 */
require_once('../include/config.php'); 

/**
 * - PEAR QuickForm
 */ 
require_once ('HTML/QuickForm.php');


/**
 * - Send emails out
 */ 
function sendMail($sendTo) {
	global $default;

	$message = stripslashes($_POST['body']);	
		  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = $default['title1'] . " Notification";

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
<p>$message</p>
</body>
</html>
END_OF_HTML;

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
	   echo "Message was not sent<br>";
	   echo "Mailer Error: " . $mail->ErrorInfo . "<br><br>";
	   echo "<a href=\"". $default['URL_HOME']. "\" class=\"dark\">" . $default['title1'] . "</a>";
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	//End sendMail

/* ---------- Functions ---------- */

/* Setup query to get users by group */
if ($_POST['stage'] == 'send') {
	switch ($_POST['group']) {
	case 'requester':
		$GROUP = "and requester = '1'";
		break;	
	case 'staffing':
		$GROUP = "and staffing = '1'";
		break;			
	case 'approver':
		$GROUP = "and one = '1' or two = '1'";
		break;	
	case 'cer':
		$GROUP = "and cer <> '0'";
		break;	
	default:
		$GROUP = '';
	}

	/* Send a test email to me */
	if ($_POST['test'] == '1') {
		sendMail($default['debug_email']);
	} else {
		$users_sql = "SELECT U.eid, E.fst, E.lst, E.email
					  FROM Users U, Standards.Employees E 
					  WHERE U.eid = E.eid and U.status = '0' and E.status = '0' $GROUP 
					  ORDER BY E.lst ASC";
		$users_query = $dbh->prepare($users_sql);	
		$users_sth = $dbh->execute($users_query);
		
		/* Send out emails to all users in the group */
		while($users_sth->fetchInto($USER)) {
			if (!is_null($USER[email])) {
				sendMail("$USER[email]");
			}
		}
		
		header("Location: index.php");
		exit;
	}
}


/**
 * - Update Message and Settings database
 */
if ($_POST['stage'] == 'change') {
//	$res = $dbh->query("UPDATE Settings SET value = '".$_POST['notify_web']."' WHERE variable = 'notify_web'");
	$dbh->query("INSERT into Message VALUES(NULL, 'email', NOW(), '".$_SESSION['eid']."', '".$_POST['message']."', '".$_POST['group']."')");
	
	header("Location: index.php");
	exit;
}

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name
							 FROM Users u, Standards.Employees e
							 WHERE e.eid = u.eid");	



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
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --><!-- InstanceBeginEditable name="head" -->      <!-- InstanceEndEditable -->
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
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include($default['FS_HOME'].'/include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

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
				<!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['hcr_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="../images/t.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<?php } else { ?>
<?php
$menu1 = $default['url_home'] . "/Administration/utilities.php";
$menu1_css = ($_SERVER['REQUEST_URI'] == $menu1) ? on : off;

$menu2 = $default['url_home'] . "/Administration/db/index.php";
$menu2_css = (preg_match("/db/", $_SERVER['REQUEST_URI'])) ? on : off;

$menu3 = $default['url_home'] . "/Administration/settings.php";
$menu3_css = ($_SERVER['REQUEST_URI'] == $menu3) ? on : off;

$menu4 = $default['url_home'] . "/Administration/users.php";
$menu4_css = ($_SERVER['REQUEST_URI'] == $menu4) ? on : off;
?>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tr>
	<td>&nbsp;</td>
	<td><table cellspacing="0" cellpadding="0" summary="" border="0">
		<tr>
		  <td nowrap><a href="<?= $menu4; ?>" class="<?= $menu4_css; ?>"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu3; ?>" class="<?= $menu3_css; ?>"> Settings </a></td>	
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu2; ?>" class="<?= $menu2_css; ?>"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu1; ?>" class="<?= $menu1_css; ?>"> Utilities </a></td>			  			  
		  <td nowrap>&nbsp;</td>
		</tr>
	</table></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
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
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>
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
    <!-- InstanceBeginEditable name="main" -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="200" valign="top"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td><!-- #BeginLibraryItem "/Library/utilities.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="notify.php" class="dark">Notify Users by Email</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="notify_web.php" class="dark">Notify Users by Webs</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="testemail.php" class="dark">Send Test Email </a></td>
                      </tr>
        </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="summary.php" class="dark">Usage Summary</a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="comments.php" class="dark">Comments</a></td>
                      </tr>
                    </table>
					<?php if ($_SESSION['hcr_access'] == '3') { ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="conversion.php" class="dark">Conversion</a></td>
                      </tr>
                    </table>
					<?php } ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="reminder_past.php" class="dark">Send Past Reminders </a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="updateCalendar.php" class="dark">Update  Calendar </a></td>
                      </tr>
                    </table>
					<table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="updateRSS.php" class="dark">Update  RSS </a></td>
                      </tr>
                    </table>
					<!--
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="javascript:void(0);" class="dark">ePOS</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection">&nbsp;&nbsp;&nbsp;<a href="../Administration/migrate.php" class="dark">Migrate Data </a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection">&nbsp;&nbsp;&nbsp;<a href="../Administration/epos_status.php" class="dark">Status</a></td>
            </tr>
          </table>--></td>
      <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --></td>
          </tr>
          <tr>
            <td><br>
              <br>
                <table width="190"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                          <td align="center"><span class="ColorHeaderSubSub">Messages</span></td>
                          <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="20"><img src="../images/detail.gif" width="18" height="20" align="absmiddle"> <a href="notify.php?l=all" class="dark">List All</a> </td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                          <td><img src="../images/spacer.gif" width="10" height="10"></td>
                          <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                        </tr>
                    </table></td>
                  </tr>
                </table>
            </td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td class="BGAccentVeryDark"><div align="left">
                          <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                              <td height="30" nowrap class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;Notify Users by Email...</td>
                              <td><div align="right">&nbsp;&nbsp;</div></td>
                            </tr>
                          </table>
                      </div></td>
                    </tr>
                    <tr>
                      <td class="BGAccentVeryDarkBorder"><?php
						// -------- Start Generate Form ------------------
						$form1 =& new HTML_QuickForm('Form1', 'post');
						$form1->setDefaults(array(
													'group' => $_POST['group'],
													'body' => $_POST['body']
						));
												 
						$GROUP = array("all"      => "All",
									   "reqester" => "Reqester",
									   "staffing"   => "Staffing",
									   "approver" => "Approver",
									   "cer"      => "CER");
									  
						$form1->addElement('select', 'group', 'Group:', $GROUP);
						$form1->addElement('textarea', 'body', 'Message:', array('rows' => 15, 'cols' => 70));
						$form1->addElement('checkbox', 'test', '', 'Send a test message');
						$form1->addElement('hidden', 'stage', 'send');
						$form1->addElement('image', 'submit', '../images/button.php?i=b70.png&l=Submit');
						
						$form1->addRule('body', 'Please a Message', 'required', null, 'client');
						$form1->display();		// Display Form
						?>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                    </tr>
                </table></td>
              </tr>
            </table>
            <br>
            <br></td>
          </tr>
          <tr>
            <td><?php
			/**
			* - Display all messages
			*/
			if ($_GET['l'] == 'all') {
				$messages_sq1 = $dbh->prepare("SELECT * FROM Message WHERE type='email'");
			?>
              <?php
			$messages_sth = $dbh->execute($messages_sq1);
			while($messages_sth->fetchInto($MESSAGE)) {
			?>
              <table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="30" colspan="2" class="BGAccentVeryDark">&nbsp;<b>
                    <?= ucwords(strtolower($EMPLOYEES[$MESSAGE['eid']])); ?> emailed this message on <?php $reqDate = explode(" ", $MESSAGE['posted']); echo date("M-d-Y", strtotime($reqDate[0])); ?> to <?= ucwords(strtolower($MESSAGE['group'])); ?>
                  </b></td>
                </tr>
                <tr>
                  <td  class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><?= $MESSAGE['message']; ?></td>
                      </tr>
                  </table></td>
                </tr>
              </table>
              <br>
              <?php } ?>
              <?php } ?></td>
          </tr>
        </table>
          <br>
          <br>
        <br></td>
      </tr>
    </table>
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