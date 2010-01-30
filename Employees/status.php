<?php
/**
 * Employee List
 *
 * user_new.php add a new user.
 *
 * @version 1.5
 * @link http://a2.yourcompany.com/go/Employees/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
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
require_once('../Connections/connDB.php'); 
/**
 * - Database Connection
 */
require_once('../Connections/connStandards.php');
/**
 * --- CHECK USER ACCESS --- 
 */
require_once('../security/check_user.php');
/**
 * - Access to Request
 */
require_once('../security/group_access.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 



switch ($_POST['action']) {
	case 'Disable':
			$sql="UPDATE Employees SET status='1' WHERE eid=" . $_POST['eid'];
			$dbh_standards->query($sql);
			
			/* Record transaction for history */
			History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));	
			
			// Send email to employee and BCC users
			sendDisabledMail($_POST['fullname'],$_POST['eid'],$_POST['plant'],$_POST['department'],'on');	

			$message="Employee Disabled.";
			$forward="../Common/blank.php?gb=close&message=".$message;					
			header("Location: ".$forward);
			exit();			
	break;
	case 'Enable':
			$sql="UPDATE Employees SET status='0' WHERE eid=" . $_POST['eid'];
			$dbh_standards->query($sql);
			
			/* Record transaction for history */
			History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));	

			$message="Employee Enabled...<br><br>Please click outside this window to continue.";
			$forward="../Common/blank.php?gb=close&message=".$message;					
			header("Location: ".$forward);						
			exit();				
	break;
}						   					    
/* ------------- END DATABASE CONNECTIONS --------------------- */



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
    <title><?= $default['title1']; ?>
    </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
</head>

  <body>
  <br>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
      <table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="BGAccentVeryDarkBorder">Click the <?= strtoupper($_GET['action']); ?> button to <?= strtolower($_GET['action']); ?> <strong><?= $_GET['fullname']; ?>'s</strong> access.</td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td valign="bottom"><img src="../images/spacer.gif" width="15" height="5" border="0"></td>
        </tr>
        <tr>
          <td valign="bottom"><div align="right">
            <input name="action" type="hidden" id="action" value="<?= $_GET['action']; ?>">
            <input name="fullname" type="hidden" id="fullname" value="<?= $_GET['fullname']; ?>">
            <input name="eid" type="hidden" id="eid" value="<?= $_GET['eid']; ?>">
            <input name="plant" type="hidden" id="plant" value="<?= $_GET['plant']; ?>">
            <input name="department" type="hidden" id="department" value="<?= $_GET['department']; ?>">
            &nbsp;
            <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=<?= strtoupper($_GET['action']); ?>" border="0">&nbsp;</div></td>
        </tr>
      </table>
  </form>
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