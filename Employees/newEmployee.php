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
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');



/* ------------- START PROCESSING DATA --------------------- */
switch ($_POST['action']) {
	case 'Add':
		// Get next employee ID
		switch ($_POST['I_D']) {
			case 'Indirect': $EID = nextID('Contract'); break;
			case 'Direct': $EID = nextID('Direct'); break;
		}
		
		// Format username, password and email
		$fst = strtolower($_POST['fst']);
		$lst = strtolower($_POST['lst']);
		$letter = $fst{0};
		$second = $lst{0};
		
		$password = $letter . $second . $EID;
		$username = $letter."".substr($lst,0,7);
		$email = $username."@yourcompany.com";

		// Insert new employee
		$sql="INSERT INTO Employees (co, dept, shift, Location, I_D, hire, Job_Description, phn, lst, fst, mdl, eid, email, username, password, aging) 
		                     VALUES ('4', '".$_POST['dept']."', '".$_POST['shift']."', '".$_POST['Location']."', '".$_POST['I_D']."', '".$_POST['hire']."', '".$_POST['Job_Description']."', '".$_POST['phn']."', '".$_POST['lst']."', '".$_POST['fst']."', '".$_POST['mdl']."', '".$EID."', '".$email."', '".$username."', '".$password."', CURDATE())";	
		$dbh_standards->query($sql);
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));		
	break;
	case 'Send':
		// Send email to employee and BCC users
		sendNewEmployee($_POST['email'],$_POST['fullname'],$_POST['eid'],$_POST['Location'],$_POST['dept'],'on');
		// Send email to department assistant
		if ($_POST['sendmail'] != '0') {
			$sendmail = $_POST['sendmail']."@".$default['email_domain'];
			sendDisabledEmployee($sendmail,$_POST['fullname'],$_POST['eid'],$_POST['Location'],$_POST['dept'],'off');
		}
	break;
}
/* ------------- END PROCESSING DATA --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */
$company_sql = $dbh->prepare("SELECT id, name
						      FROM Standards.Companies
						      WHERE id > 0 AND status='0'
						      ORDER BY name");
$dept_sql = $dbh->prepare("SELECT id, CONCAT('(',id,') ',name ) AS name 
						   FROM Standards.Department 
						   WHERE status='0'
						   ORDER BY name");	
$location_sql = $dbh->prepare("SELECT id, name 
							   FROM Standards.Plants 
							   WHERE status='0'
							   ORDER BY name");							   					    
/* ------------- END DATABASE CONNECTIONS --------------------- */

$today = date("Y-m-d");



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
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>
  <script language="JavaScript" src="/Common/Javascript/pointers.js" type="text/javascript"></script>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_exclusive.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_draggable.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/calendarmws.js"></SCRIPT>   
  </head>

  <?php if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; } ?>
  <body <?= $ONLOAD; ?>>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
    <?php if ($_POST['action'] != 'Add') { ?>
    <br>
    <table  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td height="25" valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
          <tr>
            <td width="110"><vllabel form="Form" validators="fst" class="valRequired2" errclass="valError">First:</vllabel></td>
            <td><input name="fst" type="text" id="fst" size="20" maxlength="20" value="<?= $DATA['fst']; ?>">
                <vlvalidator name="fst" type="required" control="fst"></td>
          </tr>
          <tr>
            <td class="valNone">Middle:</td>
            <td><input name="mdl" type="text" id="mdl" size="5" maxlength="1" value="<?= $DATA['mdl']; ?>"></td>
          </tr>
          <tr>
            <td><vllabel form="Form" validators="lst" class="valRequired2" errclass="valError">Last:</vllabel></td>
            <td><input name="lst" type="text" id="lst" size="30" maxlength="30" value="<?= $DATA['lst']; ?>">
                <vlvalidator name="lst" type="required" control="lst"></td>
          </tr>
          <tr>
            <td><vllabel form="Form" validators="I_D" class="valRequired2" errclass="valError">Employment Type:</vllabel></td>
            <td><select name="I_D" id="I_D">
                <option value="0">Select One</option>
                <option value="direct">Direct</option>
                <option value="contract">Contract</option>
              </select>
                <vlvalidator name="I_D" type="compare" control="I_D" validtype="string" comparevalue="0" comparecontrol="I_D" operator="ne"></td>
          </tr>
          <tr>
            <td><vllabel form="Form" validators="Location" class="valRequired2" errclass="valError">Plant:</vllabel></td>
            <td><select name="Location" id="Location">
                <option value="0">Select One</option>
                <?php
						  $location_sth = $dbh->execute($location_sql);
						  while($location_sth->fetchInto($LOCATION)) {
						  	$selected = ($LOCATION['id'] == $DATA['Location']) ? selected : $blank;
							print "<option value=\"".$LOCATION[id]."\" ".$selected.">".ucwords(strtolower($LOCATION[name]))."</option>";
						  }
						?>
            </select>
            <vlvalidator name="Location" type="compare" control="Location" validtype="string" comparevalue="0" comparecontrol="Location" operator="ne"></td>
          </tr>
          <tr>
            <td><vllabel form="Form" validators="dept" class="valRequired2" errclass="valError">Department:</vllabel></td>
            <td><select name="dept" id="dept">
                <option value="0">Select One</option>
                <?php
						  $dept_sth = $dbh->execute($dept_sql);
						  while($dept_sth->fetchInto($DEPT)) {
							$selected = ($DEPT['id'] == $DATA['dept']) ? selected : $blank;
							print "<option value=\"".$DEPT[id]."\" ".$selected.">".ucwords(strtolower($DEPT[name]))."</option>";
						  }
						  ?>
            </select>
            <vlvalidator name="dept" type="compare" control="dept" validtype="string" comparevalue="0" comparecontrol="dept" operator="ne"></td>
          </tr>
          <tr>
            <td class="valNone">Job Description: </td>
            <td><input name="Job_Description" type="text" id="Job_Description" size="35" maxlength="50" value="<?= $DATA['Job_Description']; ?>"></td>
          </tr>
          <tr>
            <td class="valNone">Hire Date: </td>
            <td><strong>
              <input name="hire" type="text" id="hire" value="<?= $today; ?>" size="10" maxlength="10" readonly>
            <a href="javascript:show_calendar('Form.hire')" <?php help('', 'Click here to choose a date', 'default');; ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></strong></td>
          </tr>
          <tr>
            <td class="valNone">Shift:</td>
            <td><select name="select">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="valNone">Phone:</td>
            <td><input name="phn" type="text" id="phn" size="15" maxlength="15" value="<?= $DATA['phn']; ?>"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="500"  border="0" cellpadding="0" cellspacing="0">
            
            <tr>
              <td height="30"><table width="100%"  border="0">
                  <tr>
                    <td valign="top" nowrap>&nbsp;</td>
                    <td valign="bottom"><div align="right">
                        <input name="action" type="hidden" id="action" value="Add">
                        <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Add" border="0">
                      &nbsp;</div></td>
                  </tr>
              </table></td>
            </tr>
          </table>
            </div></td>
      </tr>
    </table>
    <br>
    <br>
    <?php } else { ?>
    <table  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td height="25" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td><table width="500"  border="0" cellpadding="0" cellspacing="0">
            <tr class="BGAccentVeryDark">
              <td height="30">&nbsp;&nbsp;<span class="DarkHeaderSubSub">New Employee Information...</span> </td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                  <tr>
                    <td width="110">First:</td>
                    <td><strong>
                      <?= $_POST['fst']; ?>
                    </strong></td>
                  </tr>
                  <tr>
                    <td>Middle:</td>
                    <td><strong>
                      <?= $_POST['mdl']; ?>
                    </strong></td>
                  </tr>
                  <tr>
                    <td>Last:</td>
                    <td><strong>
                      <?= $_POST['lst']; ?>
                    </strong></td>
                  </tr>
                  <tr>
                    <td>Employee ID:</td>
                    <td><strong>
                      <?= $EID; ?>
                    </strong></td>
                  </tr>
                  <tr>
                    <td>Email Address:</td>
                    <td><strong>
                      <?= $email; ?>
                    </strong></td>
                  </tr>
                  <tr>
                    <td height="10" colspan="2"><img src="../images/spacer.gif" width="10" height="10"></td>
                  </tr>
                  <tr>
                    <td nowrap>Email Information To:</td>
                    <td><select name="sendmail" id="sendmail">
                        <option value="0">Select One</option>
                        <option value="mmaison">Maison, Martha</option>
                        <option value="eschade">Schade, Eunice</option>
                    </select></td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td height="30"><table width="100%"  border="0">
                  <tr>
                    <td valign="top" nowrap>&nbsp;</td>
                    <td valign="bottom"><div align="right">
                        <input name="email" type="hidden" id="email" value="<?= $email; ?>">
                        <input name="fullname" type="hidden" id="fullname" value="<?= ucwords(strtolower($_POST['fst'] . " " . $_POST['lst'])); ?>">
                        <input name="eid" type="hidden" id="eid" value="<?= $EID; ?>">
                        <input name="action" type="hidden" id="action" value="Send">
                        <input name="imageField" type="image" id="imageField" src="../images/button.php?i=b70.png&l=Send" border="0">
                      &nbsp;</div></td>
                  </tr>
              </table></td>
            </tr>
          </table>
            </div></td>
      </tr>
    </table>
    <?php } ?><!-- #BeginLibraryItem "/Library/history.lbi" -->
  <script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
  </script>
  <?php if ($_SESSION['hcr_access'] == 3) { ?>
  <table width="190"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
            <td align="center"><span class="ColorHeaderSubSub">Administration</span> </td>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td><a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('../Administration/history.php?page=<?= $_SERVER[PHP_SELF]; ?>','history','scrollbars=yes,resizable=yes,width=875,height=800')" <?php help('', 'Get the history of this page', 'default'); ?>><strong> History </strong></a></td>
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
  <?php } ?>
  <!-- #EndLibraryItem --></form>
  </body>
</html>


<?php 
/**
 * - Display debug information 
 */
include_once('debug/footer.php');
/* 
 * - Disconnect from database 
 */
$dbh->disconnect();
?>