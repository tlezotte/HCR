<?php
/**
 * Employee List
 *
 * user_new.php add a new user.
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
 * - Database Connection
 */
require_once('../Connections/connStandards.php');
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
	case 'save':
		$find = $dbh_standards->getRow("SELECT * FROM Standards.Employees WHERE eid='".$_POST['eid']."'");
		
		if (count($find) > 0) {
			$_SESSION['error'] = $_POST['fst']." ".$_POST['lst']." Already Exists";
			header("Location: ../error.php");
		}
		
		$fst = strtolower($_POST['fst']);
		$lst = strtolower($_POST['lst']);
		$letter = $fst{0};
		$second = $lst{0};
		
		$password = $letter . $second . $_POST['eid'];
		$username = $letter."".substr($lst,0,7);
		$email = $username."@".$default['email_domain'];
		
		$dbh_standards->query("INSERT INTO Employees (co, dept, phn, lst, fst, mdl, eid, email, username, password) VALUES ('1', '".$_POST['dept']."', '".$_POST['phn']."', '".$_POST['lst']."', '".$_POST['fst']."', '".$_POST['mdl']."', '".$_POST['eid']."', '".$email."', '".$username."', '".$password."')");
	default;
}
/* ------------- END PROCESSING DATA --------------------- */

/* ------------- START DATABASE CONNECTIONS --------------------- */
$company_sql = $dbh->prepare("SELECT id, name
						      FROM Standards.Companies
						      WHERE id > 0 AND status='0'
						      ORDER BY name");
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status='0'
						   ORDER BY name");						    
/* ------------- END DATABASE CONNECTIONS --------------------- */
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title><?= $language['label']['title1']; ?></title>
  <script type="text/javascript">function sf(){ document.Form.fst.focus(); }</script>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <script language="JavaScript" src="/Common/Javascript/pointers.js" type="text/javascript"></script>
  <script language="JavaScript" src="/Common/Javascript/gen_validatorv3.js" type="text/javascript"></script>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>  
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>



<body onload="sf()">
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
  <table  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="30" class="BGAccentVeryDark"><div class="DarkHeaderSub">&nbsp;&nbsp;Add a New User </div></td>
        </tr>
        <tr>
          <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
            <tr>
              <td width="110"><vllabel form="Form" validators="fst" class="valRequired" errclass="valError">First:</vllabel></td>
              <td><input name="fst" type="text" id="fst" size="20" maxlength="20">
                      <vlvalidator name="fst" type="required" control="fst"></td>
            </tr>
            <tr>
              <td class="valNone">Middle:</td>
              <td><input name="mdl" type="text" id="mdl" size="5" maxlength="1"></td>
            </tr>
            <tr>
              <td><vllabel form="Form" validators="lst" class="valRequired" errclass="valError">Last:</vllabel></td>
              <td><input name="lst" type="text" id="lst" size="30" maxlength="30">
                      <vlvalidator name="lst" type="required" control="lst"></td>
            </tr>
            <tr>
              <td><vllabel form="Form" validators="eid" class="valRequired" errclass="valError">Employee ID:</vllabel></td>
              <td><input name="eid" type="text" id="eid" size="5" maxlength="5">
                      <vlvalidator name="eid" type="required" control="eid" minlength="5" maxlength="5"></td>
            </tr>
            <tr>
              <td class="valNone">Department:</td>
              <td><select name="dept" id="dept">
                <option value="0" selected>Select One</option>
                <?php
					  $dept_sth = $dbh->execute($dept_sql);
					  while($dept_sth->fetchInto($DEPT)) {
						print "<option value=\"".$DEPT[id]."\">".ucwords(strtolower($DEPT[name]))."</option>";
					  }
					  ?>
              </select>
              </td>
            </tr>
            <tr>
              <td class="valNone">Phone:</td>
              <td><input name="phn" type="text" id="phn" size="15" maxlength="15"></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td valign="bottom"><img src="../images/spacer.gif" width="15" height="5" border="0"></td>
    </tr>
    <tr>
      <td valign="bottom"><div align="right">
        <input name="action" type="hidden" id="action" value="save">
        <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Create" border="0">
        &nbsp;&nbsp; </div></td>
    </tr>
  </table>
</form>
</body>
</html>


<?php
/* Disconnect from database */
$dbh->disconnect();
?>