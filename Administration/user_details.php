<?php 
/**
 * Request System
 *
 * users.php list all users.
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
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../include/BlackBerry.php');
 
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
require_once('../security/check_access1.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 



/* -------------- START PROCESSING DATA -------------- */
switch ($_POST['action']) {
	case 'update':
		$update_sql="UPDATE Users SET access='".$_POST['access']."',
									  requester='".$_POST['requester']."',
									  one='".$_POST['one']."',
									  two='".$_POST['two']."',
									  four='".$_POST['four']."',
									  five='".$_POST['five']."',
									  six='".$_POST['six']."',
									  seven='".$_POST['seven']."',
									  eight='".$_POST['eight']."',
									  coordinator='".$_POST['coordinator']."',
									  staffing='".$_POST['staffing']."',
									  generator='".$_POST['generator']."',
									  desk='".$_POST['desk']."',
									  groups='".$_POST['groups']."',
									  status='".$_POST['status']."'
								WHERE eid=".$_POST['eid'];
		$dbh->query($update_sql);	
		
		$forward="../Common/blank.php?message=User Informatin was updated<br><br>Please click outside this window to continue.";					
		header("Location: ".$forward);
		exit();
	break;
}
/* -------------- START PROCESSING DATA -------------- */


/* -------------- START DATABASE ACCESS -------------- */
/* ----- Get employees permissions ----- */
$user_sql = "SELECT * 
			  FROM Users
			  WHERE eid=".$_GET['eid'];	  
$USER = $dbh->getRow($user_sql);

/* ----- Get employees full name ----- */				
$employees_sql = "SELECT eid, fst, lst 
				  FROM Standards.Employees 
				  WHERE eid=".$_GET['eid'];	 						  
$EMPLOYEE = $dbh->getRow($employees_sql);	

/* Get Plant information */
$plants_sql = $dbh->prepare("SELECT id, name
						     FROM Standards.Plants
						     WHERE status = '0'
						     ORDER BY name");						  
/* -------------- END DATABASE ACCESS -------------- */



$ONLOAD_OPTIONS.="init();";
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
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>
  <script language="JavaScript" src="/Common/Javascript/pointers.js" type="text/javascript"></script>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  </head>

  <?php if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; } ?>
  <body <?= $ONLOAD; ?>>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
	<br>
    <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
      <table  border="0" align="center" cellpadding="0" cellspacing="0">
        
        <tr>
          <td nowrap class="BGAccentVeryDarkBorder"><table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="24" nowrap class="padding">Requester:</td>
                <td><select name="requester" id="requester">
                    <option value="1" <?= ($USER['requester'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['requester'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td width="200" height="24" nowrap class="padding"><?= $language['label']['app1']; ?>:</td>
                <td width="150"><select name="one" id="one">
                    <option value="1" <?= ($USER['one'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['one'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app2']; ?>:</td>
                <td><select name="two" id="two">
                    <option value="1" <?= ($USER['two'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['two'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app4']; ?>:</td>
                <td><select name="four" id="four">
                    <option value="1" <?= ($USER['four'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['four'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app5']; ?>:</td>
                <td><select name="five" id="five">
                    <option value="1" <?= ($USER['five'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['five'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app6']; ?>:</td>
                <td><select name="six" id="six">
                    <option value="1" <?= ($USER['six'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['six'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app7']; ?>:</td>
                <td><select name="seven" id="seven">
                    <option value="1" <?= ($USER['seven'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['seven'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app8']; ?>:</td>
                <td><select name="eight" id="eight">
                    <option value="1" <?= ($USER['eight'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['eight'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="5" colspan="2" nowrap class="padding"><img src="../images/spacer.gif" width="10" height="5"></td>
              </tr>		
              <tr>
                <td height="24" nowrap class="padding">Staffing Coordinator: </td>
                <td><select name="staffing" id="staffing">
                    <option value="1" <?= ($USER['staffing'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['staffing'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>			  	  
              <tr>
                <td height="24" nowrap class="padding">HR Coordinator: </td>
                <td><select name="coordinator" id="coordinator">
                    <option value="1" <?= ($USER['coordinator'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['coordinator'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding">Desk Coordinator:</td>
                <td><select name="desk" id="desk">
                  <option value="0">Select One</option>
                  <?php
					  $plant_sth = $dbh->execute($plants_sql);
					  while($plant_sth->fetchInto($PLANTS)) {
						$selected = ($USER['desk'] == $PLANTS[id]) ? selected : $blank;
						print "<option value=\"".$PLANTS[id]."\" ".$selected.">".ucwords(strtolower($PLANTS[name]))."</option>\n";
					  }
					  ?>
                </select></td>
              </tr>			  
              <tr>
                <td height="24" nowrap class="padding">Employee ID Generator: </td>
                <td><select name="generator" id="generator">
                    <option value="1" <?= ($USER['generator'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['generator'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="5" colspan="2" nowrap class="padding"><img src="../images/spacer.gif" width="10" height="5"></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding">Group Access:</td>
                <td><select name="groups" id="groups">
                    <option value="0" <?= ($USER['groups'] == '0') ? selected : $blank; ?>>None</option>
                    <option value="ex" <?= ($USER['groups'] == 'ex') ? selected : $blank; ?>>Executive</option>
                    <option value="hr" <?= ($USER['groups'] == 'hr') ? selected : $blank; ?>>Human Resources</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding">Administration Access:</td>
                <td><select name="access" id="access">
                    <option value="0" <?= ($USER['access'] == '0') ? selected : $blank; ?>>None</option>
                    <option value="1" <?= ($USER['access'] == '1') ? selected : $blank; ?>>Level 1</option>
                    <option value="2" <?= ($USER['access'] == '2') ? selected : $blank; ?>>Level 2</option>
                    <option value="3" <?= ($USER['access'] == '3') ? selected : $blank; ?>>Level 3</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding">Application Access:</td>
                <td><select name="status" id="status">
                    <option value="0" <?= ($USER['status'] == '0') ? selected : $blank; ?>>Yes</option>
                    <option value="1" <?= ($USER['status'] == '1') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td height="5" nowrap><img src="../images/spacer.gif" width="10" height="5"></td>
        </tr>
        <tr>
          <td nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
                <td align="right"><input name="eid" type="hidden" id="eid" value="<?= $_GET['eid']; ?>">
                    <input name="action" type="hidden" id="action" value="update">
                    <input name="Done" type="image" class="button" id="Done" src="../images/button.php?i=b70.png&l=<?= $language['label']['update']; ?>" alt="<?= $language['label']['update']; ?>" border="0">
                  &nbsp;</td>
              </tr>
          </table></td>
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