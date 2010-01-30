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


/* ------------- START DATA PROCESSING --------------------- */
switch ($_POST['action']) {
	case 'update':
		$sql="UPDATE Employees SET fst='".$_POST['fst']."', 
								   mdl='".$_POST['mdl']."', 
								   lst='".$_POST['lst']."',
								   eid='".$_POST['eid']."', 
								   Location=' ".$_POST['Location']."',
								   dept=' ".$_POST['dept']."',
								   hire=' ".$_POST['hire']."',
								   Job_Description=' ".$_POST['Job_Description']."',
								   phn=' ".$_POST['phn']."'
							 WHERE eid=".$_POST['eid'];
		$dbh_standards->query($sql);
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));

		$message=ucwords(strtolower($_POST['fst'].' '.$_POST['lst']))." Updated...<br><br>Please click outside this window to continue.";
		$forward="../Common/blank.php?message=".$message;					
		header("Location: ".$forward);											
	break;
}
/* ------------- END DATA PROCESSING --------------------- */

/* ------------- START DATABASE CONNECTIONS --------------------- */
/* Get employee Information */
$DATA=$dbh->getRow("SELECT * FROM Standards.Employees WHERE eid=" . $_GET['eid']);

/* Get Plant Information */
$PLANT = $dbh->getAssoc("SELECT id, name FROM Standards.Plants");
/* Get Department Information */	
$DEPARTMENT = $dbh->getAssoc("SELECT id, name FROM Standards.Department");	
/* Getting plant locations from Standards.Plants */								
$plant_sql = $dbh->prepare("SELECT id, name FROM Standards.Plants WHERE status='0' ORDER BY name ASC");
/* Getting plant locations from Standards.Department */	
$dept_sql  = $dbh->prepare("SELECT * FROM Standards.Department ORDER BY name ASC");
/* ------------- END DATABASE CONNECTIONS --------------------- */

/* ------------- START VARIABLES --------------------- */
$email = split('@', $DATA['email']);
$status = ($DATA['status'] == '0') ? Active : Inactive;
/* ------------- END VARIABLES --------------------- */



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
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>  
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_exclusive.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_draggable.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/calendarmws.js"></SCRIPT>  
  </head>

  <body>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
  <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
    <table width="200" border="0" cellspacing="0" cellpadding="0">
<tr>
          <td>First Name: </td>
          <td><input name="fst" type="text" class="hiddenInput" value="<?= ucwords(strtolower($DATA['fst'])); ?>" size="20" maxlength="20"></td>
</tr>
        
        <tr>
          <td height="25" valign="bottom" nowrap>Middle Name: </td>
          <td valign="bottom"><input name="mdl" type="text" class="hiddenInput" value="<?= strtoupper($DATA['mdl']); ?>" size="10" maxlength="10"></td>
      </tr>
        
        <tr>
          <td height="25" valign="bottom">Last Name: </td>
          <td valign="bottom"><input name="lst" type="text" class="hiddenInput" value="<?= ucwords(strtolower($DATA['lst'])); ?>" size="30" maxlength="30"></td>
      </tr>
        
        <tr>
          <td height="25" valign="bottom">Employee ID: </td>
          <td valign="bottom"><input name="eid" type="text" class="hiddenInput" value="<?= $DATA['eid']; ?>" size="5" maxlength="5"></td>
      </tr>
        <tr>
          <td height="25" valign="bottom">Hire Date:</td>
          <td valign="bottom"><strong>
            <input name="hire" type="text" id="hire" value="<?= $DATA['hire']; ?>" size="10" maxlength="10" readonly class="hiddenInput">
            <a href="javascript:show_calendar('Form.hire')" <?php help('', 'Click here to choose a date', 'default');; ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></strong></td>
      </tr>
        <tr>
          <td height="25" valign="bottom">Plant:</td>
          <td valign="bottom"><select name="Location" id="Location" class="hiddenInput">
            <option value="0">Select One</option>
            <?php
				  $plant_sql_sth = $dbh->execute($plant_sql);
				  while($plant_sql_sth->fetchInto($row)) {
					$selected = ($DATA['Location'] == $row[id]) ? selected : $blank;
					print "<option value=\"".$row[id]."\" ".$selected.">".ucwords(strtolower($row[name]))."</option>\n";
				  }
			  ?>
          </select></td>
      </tr>
        
        <tr>
          <td height="25" valign="bottom">Department:</td>
          <td valign="bottom"><select name="dept" id="dept" class="hiddenInput">
            <option value="0">Select One</option>
            <?php
				  $dept_sth = $dbh->execute($dept_sql);
				  while($dept_sth->fetchInto($row)) {
					$selected = ($DATA['dept'] == $row[id]) ? selected : $blank;
					print "<option value=\"".$row[id]."\" ".$selected.">(".$row[id].") ".ucwords(strtolower($row[name]))."</option>\n";
				  }
			  ?>
          </select></td>
      </tr>
        
        <tr>
          <td height="25" valign="bottom" nowrap>Job Description: </td>
          <td valign="bottom"><input name="Job_Description" type="text" class="hiddenInput" value="<?= ucwords(strtolower($DATA['Job_Description'])); ?>" size="30" maxlength="40"></td>
      </tr>
        
        <tr>
          <td height="25" valign="bottom">Shift:</td>
          <td valign="bottom"><strong>
            <?= $DATA['shift']; ?>
          </strong></td>
      </tr>
        <tr>
          <td height="25" valign="bottom" nowrap>Email Address:</td>
          <td valign="bottom"><strong><a href="mailto:<?= $DATA['email']; ?>" class="dark">
            <?= $email[0]; ?>
          </a></strong></td>
      </tr>
        <tr>
          <td height="25" valign="bottom">Phone:          </td>
          <td valign="bottom"><input name="phn" type="text" class="hiddenInput" value="<?= $DATA['phn']; ?>" size="15" maxlength="15"></td>
      </tr>
        <tr>
          <td height="25" valign="bottom">Status:</td>
          <td valign="bottom"><strong>
            <?= $status; ?>
          </strong></td>
      </tr>
        <tr>
          <td align="right"><img src="../images/spacer.gif" width="10" height="10"></td>
          <td align="right">&nbsp;</td>
      </tr>
        <tr>
          <td align="center"><input name="action" type="hidden" id="action" value="update">
          <input name="imageField" type="image" src="../images/button.php?i=b110.png&l=Update" border="0"></td>
          <td align="center">&nbsp;</td>
      </tr>
    </table>
  </form>
  <br><!-- #BeginLibraryItem "/Library/history.lbi" -->
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
  <!-- #EndLibraryItem -->
  
  
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