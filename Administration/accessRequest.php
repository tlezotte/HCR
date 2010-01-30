<?php 
/**
 * Request System
 *
 * accessRequest.php allows users to request access to system.
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
 * PHP Mailer
 * @link http://phpmailer.sourceforge.net/ 
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
if ($debug_page) { $request_email = "tlezotte@yourcompany.com"; }

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php'); 
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
/**
 * -- Email 
 */
require("phpmailer/class.phpmailer.php");



/**
 * ---------------- $_POST REQUEST -----------------
 */
switch ($_POST['action']) {
	/** 
	 * -------------------- REQUEST ---------------------- 
	 */
	case 'request':  
		$email = $_POST['email']."@".$default['email_domain'];
		$name = ucwords(strtolower($_POST[fst]." ".$_POST[lst]));

		/* Send out email message */
		$sendTo = $request_email;
		$subject = "Access Request Notification - ".$default['title1'];

$message_body = <<< END_OF_BODY
The following user is requesting access to $default[title1]<br>
<br>
<b><a href="mailto:$email">$name</a></b><br>
<a href="$default[URL_HOME]/Administration/accessRequest.php?action=yes&eid=$_POST[eid]&fst=$_POST[fst]&mdl=$_POST[mdl]&lst=$_POST[lst]&email=$_POST[email]&phn=$_POST[phn]&dept=$_POST[dept]"><img src="$default[URL_HOME]/images/approved.gif" width="18" height="18" align="absmiddle" border="0" class="dark"> YES</a><br>
<a href="$default[URL_HOME]/Administration/accessRequest.php?action=no&eid=$_POST[eid]&fst=$_POST[fst]&lst=$_POST[lst]&email=$_POST[email]"><img src="$default[URL_HOME]/images/notapproved.gif" width="18" height="18" align="absmiddle" border="0" class="dark"> NO</a><br>
END_OF_BODY;

		$url = "https://".$default['server'].$default['url_home'];
		
		sendGeneric($sendTo, $subject, $message_body, $url);

		/* Record transaction for history */
		History(NULL, $_POST['action'], $_SERVER['PHP_SELF'], $name);
				
		$message="Your request has been forworded.<br><br>Please click outside this window to continue.";
		$forward = "../Common/blank.php?message=".$message;
		header('Location: '.$forward);
		exit();
	break;
}

/**
 * ---------------- $_GET REQUEST -----------------
 */
switch ($_GET['action']) {		
	/** 
	 * -------------------- YES --------------------- 
	 */	
	case 'generate':
		/* -- Get employee information from HCR -- */
		$sql_request="SELECT r.department, r.plant, r.requestType, r.startDate, e.fst, e.lst 
					  FROM Requests r, Employees e
					  WHERE r.id=e.request_id
					   AND r.id=".$_GET['request_id'];
		$request = $dbh->getRow($sql_request);	

		// Get next employee ID
		switch ($request['requestType']) {
			case '1': $EID = nextID('Direct'); break;						// Generate a Direct Employee ID
			default: $EID = nextID('Contract'); break;						// Generate a Contract Employee ID
		}	
		
		/* -- Generate username and password -- */	
		$EMPLOYEE=genUserPass($request['fst'], $request['lst'], $EID);		
		
		/* -- Add user to Standards -- */
		$sql_standards="INSERT INTO Employees (dept, Location, lst, fst, eid, email, username, password, aging) 
									   VALUES ('".$request['department']."', '".$request['plant']."', '".$request['lst']."', '".$request['fst']."', '".$EID."', '".$EMPLOYEE['email']."', '".$EMPLOYEE['username']."', '".$EMPLOYEE['password']."', CURDATE())";
		$dbh_standards->query($sql_standards);							

		/* -- Add EID to HCR -- */
		$sql_employees="UPDATE Requests SET employee='".$EID."' WHERE request_id=".$_GET['request_id'];
		$dbh->query($sql_employees);

		/* -- Update EID of EID Generator -- */
		$sql_authorize="UPDATE Authorization SET generator='".$_SESSION['eid']."', generatorDate=NOW() WHERE request_id=".$_GET['request_id'];
		$dbh->query($sql_authorize);
				
		/* -- Record transaction for history -- */						  
		History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql_standards)));																

		/* -- Send out email message -- */
		$sendTo = $EMPLOYEE['email'];
		$subject = "Your your company information...";

$message_body = <<< END_OF_BODY
Your your company employment information:<br>
<br>
<b>Human Capital ID:</b> $EID<br>
<b>Your Email Address:</b> $EMPLOYEE[email]<br>
<b>Your Username:</b> $EMPLOYEE[username]<br>
<b>Your Password:</b> $EMPLOYEE[password]<br>
END_OF_BODY;

		$url = "http://intranet.yourcompany.com";
		
		sendGeneric2($sendTo, $subject, $message_body, $url);
				
		header('Location: ../index.php');
		exit();	
	break;	
}

/* ------------- START DATABASE CONNECTIONS --------------------- */
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status='0'
						   ORDER BY name");						    
/* ------------- END DATABASE CONNECTIONS --------------------- */



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
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  </head>

  <?php if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; } ?>
  <body <?= $ONLOAD; ?>>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"><br>
    </div>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon">
    <br>
    <table  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                  <tr>
                    <td width="110"><vllabel form="Form" validators="fst" class="valRequired2" errclass="valError">First:</vllabel></td>
                    <td><input name="fst" type="text" id="fst" size="20" maxlength="20">
                        <vlvalidator name="fst" type="required" control="fst"></td>
                  </tr>
                  <tr>
                    <td class="valNone">Middle:</td>
                    <td><input name="mdl" type="text" id="mdl" size="5" maxlength="1"></td>
                  </tr>
                  <tr>
                    <td><vllabel form="Form" validators="lst" class="valRequired2" errclass="valError">Last:</vllabel></td>
                    <td><input name="lst" type="text" id="lst" size="30" maxlength="30">
                        <vlvalidator name="lst" type="required" control="lst"></td>
                  </tr>
                  <tr>
                    <td><vllabel form="Form" validators="email" class="valRequired2" errclass="valError">Email Address:</vllabel></td>
                    <td><input name="email" type="text" id="email" size="10" maxlength="15">@<?= $default['email_domain']; ?>
                    <vlvalidator name="email" type="required" control="email"></td>
                  </tr>
                  <tr>
                    <td><vllabel form="Form" validators="eid" class="valRequired2" errclass="valError">Employee ID:</vllabel></td>
                    <td><input name="eid" type="text" id="eid" size="5" maxlength="5">
                        <vlvalidator name="eid" type="required" control="eid" errmsg="Employee ID requires 5 digits" minlength="5" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td class="valNone">Department:</td>
                    <td><select name="dept" id="dept">
                        <option value="0" selected>Select One</option>
                        <?php
						  $dept_sth = $dbh->execute($dept_sql);
						  while($dept_sth->fetchInto($DEPT)) {
							print "<option value=\"".$DEPT[id]."\">(".$DEPT[id].")".ucwords(strtolower($DEPT[name]))."</option>";
						  }
						?>
                      </select></td>
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
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><span class="Copyright"><strong>NOTE:</strong> Your Access Request is being sent<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to
<?= $request_name; ?>
for approval. </span></td>
                <td align="right"><input name="action" type="hidden" id="action" value="request">
                    <input name="imageField" type="image" src="../images/button.php?i=b150.png&l=<?= $language['label']['sendrequest']; ?>" border="0">
                  &nbsp;&nbsp; </td>
              </tr>
            </table>
        </div></td>
      </tr>
      <tr>
        <td valign="bottom"><br>
        <vlsummary form="Form" class="valErrorListSmall" headertext="Form Errors:" displaymode="bulletlist" showsummary="true" messagebox="false"></td>
      </tr>
      
      <tr>
        <td height="50" valign="bottom"><!-- #BeginLibraryItem "/Library/history.lbi" -->
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
            <td><a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('history.php?page=<?= $_SERVER[PHP_SELF]; ?>','history','scrollbars=yes,resizable=yes,width=875,height=800')" <?php help('', 'Get the history of this page', 'default'); ?>><strong> History </strong></a></td>
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
  <!-- #EndLibraryItem --></td>
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
