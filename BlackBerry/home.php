<?php 
/**
 * Request System
 *
 * home.php is the default page after a seccessful login.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
 */
 

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


/**
 * - Check to see if a web notice needs to be displayed 
 */
if ($default['notify_web'] == 'on' and !isset($_COOKIE['notify_web'])) {
	header("Location: notice.php");
	exit;
}

if ($_POST['action'] == 'search') {
	$PO = $dbh->getRow("SELECT id FROM PO WHERE po = '".$_POST['po']."'");
	
	if (isset($PO)) {
		$forward = "PO/detail.php?id=".$PO['id'];
	} else {
		$_SESSION['error'] = "Purchase Order ".$_POST['po']." was not found";
		$forward = "error.php";
	}

	header("Location: ".$forward);
	exit();
}

$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u, Standards.Employees e 
							 WHERE e.eid = u.eid");	
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $language['label']['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 your company" />
<link href="handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td nowrap><div align="center"><img src="/Common/images/company200.gif" alt="your company" name="company" width="200" height="50"></div></td>
  </tr>
  <tr>
    <td nowrap><div align="center">
      <?= $language['label']['title1']; ?>
    </div></td>
  </tr>
  <tr>
    <td nowrap><div align="center"><strong> Main Menu </strong></div></td>
  </tr>
</table>
<table width="240" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="15" nowrap>&nbsp;<img src="/Common/images/company_Bullet.gif" width="11" height="15">&nbsp;<a href="PO/list.php?action=my&access=0" class="dark">Requisition Request</a></td>
  </tr>
  <!--
  <tr>
    <td><img src="/Common/images/company_Bullet.gif" width="11" height="15">&nbsp;<a href="CER/list.php" class="dark">Capital Acquisition</a></td>
  </tr>
  -->
  <?php if ($_SESSION['hcr_access'] >= 1) { ?>
  <tr>
    <td>&nbsp;<img src="/Common/images/company_Bullet.gif" width="11" height="15">&nbsp;<a href="Administration/index.php" class="dark">Administration</a></td>
  </tr>
  <?php } ?>
  <tr>
    <td height="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;<img src="/Common/images/company_Bullet.gif" width="11" height="15">&nbsp;
      <form name="form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
        <span class="dark">PO:</span>
        <input name="action" type="hidden" id="action" value="search">
        <input name="po" type="text" id="po" size="5" maxlength="5">
		<input name="get" type="submit" value="Get" class="button">
    </form></td>
  </tr>
  <tr>
    <td>&nbsp;<img src="/Common/images/company_Bullet.gif" width="11" height="15">&nbsp;<a href="PO/track.php" class="dark">Track Shipments</a></td>
  </tr>
  <tr>
    <td height="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;<img src="/Common/images/company_Bullet.gif" width="11" height="15">&nbsp;<a href="logout.php" class="dark">Logout <?= ucwords(strtolower($EMPLOYEES[$_SESSION[eid]])); ?></a></td>
  </tr>
</table>
</body>
</html>


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