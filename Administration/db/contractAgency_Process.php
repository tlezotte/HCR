<?php
/**
 * Request System
 *
 * settings.php display, add and edit system wide variables.
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
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
require_once('../../Connections/connStandards.php'); 
/**
 * - Check User Access
 */
require_once('../../security/check_access.php'); 

/**
 * - Config Information
 */
require_once('../../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');



/* ----- START EDIT VARIABLE ----- */
if ($_POST['action'] == "edit") {
	$URL = preg_replace("|http://|", "", $_POST['URL']);				// Remove http:// from value
	
	$sql = "UPDATE ContractAgency SET name='" . $_POST['name'] . "',
									  URL='" . $URL . "'
								WHERE id=" . $_POST['id'];
	$dbh_standards->query($sql);																					
											
	/* Record transaction for history */
	History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));	
	
	$message="Your updates have been saved.<br><br>Please click outside this window to continue.";
	$forward = "../../Common/blank.php?message=".$message;
	header('Location: '.$forward);
	exit();										
}
/* ----- END EDIT VARIABLE ----- */

/* ----- START DISABLE VARIABLE ----- */
if ($_POST['action'] == "delete") {

	$sql = "UPDATE ContractAgency SET status='1' WHERE id=" . $_POST['id'];
	$dbh_standards->query($sql);																					
											
	/* Record transaction for history */
	History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));	
	
	$message="The current Position Title has been deleted.<br><br>Please click outside this window to continue.";
	$forward = "../../Common/blank.php?message=".$message;
	header('Location: '.$forward);
	exit();												
}
/* ----- END DISABLE VARIABLE ----- */


/* ------------- START FORM DATA --------------------- */
$AGENCY = $dbh_standards->getRow("SELECT * 
								  FROM ContractAgency 
								  WHERE id = " . $_GET['id']);
/* ------------- END FORM DATA --------------------- */


/* Setup onLoad javascript program */
$ONLOAD_OPTIONS.="";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $language['label']['title1']; ?>
    </title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  </head>

  <body <?= $ONLOAD; ?>>
           <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
             <br>
             <table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                    
                    <tr>
                      <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="2">
                          <tr>
                            <td height="25"><strong><vllabel form="Form" validators="name" errclass="valError">Name:</vllabel></strong></td>
                            <td height="25" class="padding"><input name="name" type="text" id="name" value="<?= $AGENCY['name']; ?>" size="50" maxlength="50">
                              <vlvalidator name="name" type="required" control="name"></td>
                          </tr>
                          <tr>
                            <td height="25" nowrap><strong>URL:</strong></td>
                            <td class="padding"><input name="URL" type="text" id="URL" value="<?= $AGENCY['URL']; ?>" size="50" maxlength="50"></td>
                          </tr>
                          
                      </table></td>
                    </tr>
                  </table></td>
              </tr>
              <tr>
                <td height="5"><img src="../../images/spacer.gif" width="5" height="5"></td>
              </tr>
              <tr>
                <td align="right">
				  <input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
				  <input type="hidden" name="action" value="<?= $_GET['action']; ?>">
				  <input name="<?= ucwords($_GET['action']); ?>" type="image" id="<?= ucwords($_GET['action']); ?>" src="../../images/button.php?i=b70.png&l=<?= ucwords($_GET['action']); ?>" border="0">
				  &nbsp;
				</td>
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
$dbh_standards->disconnect();
?>