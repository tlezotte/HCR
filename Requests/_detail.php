<?php
/**
 * Request System
 *
 * detail.php displays detailed information on PO.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * PDF Toolkit
 * @link http://www.accesspdf.com/
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
$debug_page = true;
include_once('debug/header.php');


/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Access to Request
 */
require_once('../security/request_access.php');



/* -------------------------------------------------------------				
 * ------------- START DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */
$ID = (array_key_exists('id', $_POST)) ? $_POST['id'] : $_GET['id'];

/* Getting Request information */
$REQUEST = $dbh->getRow("SELECT *, 
							DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate, 
							DATE_FORMAT(targetDate,'%M %e, %Y') AS _targetDate, 
							DATE_FORMAT(startDate,'%M %e, %Y') AS _startDate
						FROM Requests r
						  INNER JOIN Employees e ON e.request_id=r.id
						WHERE r.id = ".$ID);
						
						
/* --- START REDIRECT NOT NEW HIRES TO DETAIL.PHP ----- */
if ($REQUEST['request_type'] == 'new') {
	$approval = (array_key_exists('approval', $_GET)) ? "&approval=$_GET[approval]" : '';

	$forward="detail.php?id=".$ID . $approval;
	header("Location: ".$forward);
}
/* --- END REDIRECT NOT NEW HIRES TO _DETAIL.PHP ----- */

						
/* ------------- Getting Actual Compensation Request ------------- */
$COMPA = $dbh->getRow("SELECT * 
					   FROM Compensation 
					   WHERE request_id = ".$ID." AND status='A' 
					   ORDER BY id DESC
					   LIMIT 1");
/* ------------- Getting Authoriztions Request ------------- */


/* ------------- Getting Authorization for Request ------------- */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE request_id = ".$ID);


/* --- START REDIRECT APPROVALS ----- */
if ($AUTH[$AUTH['level']] == $_SESSION['eid'] AND $_GET['switch'] != 'auto' AND !isset($_GET['approval'])) {
	$forward="https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id=" . $ID . "&switch=auto&approval=" . $AUTH['level'];
	header("Location: ".$forward);
}
/* --- END REDIRECT APPROVALS ----- */


/* ------------- Getting Employee names from Standards database ------------- */
$EMPLOYEES = $dbh->getAssoc("SELECT eid, CONCAT(fst,' ',lst) AS name FROM Standards.Employees");		
/* ------------- Getting Salary Grades ------------- */						 						 
$SALARYGRADE = $dbh->getRow("SELECT *
							  FROM Position
							  WHERE title_id=".$REQUEST['positionTitle']);
/* ------------- Getting Salary Grades ------------- */
$salaryGrade_sql = $dbh->prepare("SELECT DISTINCT(grade)
								  FROM Position
								  GROUP BY grade
								  ORDER BY (grade+0) ASC");							 							 				
/* ------------- Getting plant locations from Standards.Plants ------------- */							
$PLANT = $dbh->getAssoc("SELECT id, name FROM Standards.Plants ORDER BY name ASC");
/* ------------- Getting plant locations from Standards.Department ------------- */
$DEPT  = $dbh->getAssoc("SELECT id, name FROM Standards.Department ORDER BY name ASC");	
/* ------------- Getting position titles ------------- */
$POSITIONTITLE = $dbh->getAssoc("SELECT title_id, title_name
						         FROM Position
						         WHERE title_status='0'
								 ORDER BY title_name ASC");							  						  
/* ------------- Getting Contract Agency names ------------- */						  					
$AGENCY = $dbh->getAssoc("SELECT id, name FROM Standards.ContractAgency");
/* ------------- Getting employees that are in the HR group ------------- */
$hr_sql = "SELECT e.eid, CONCAT( e.lst, ', ', e.fst ) AS fullname
		   FROM Users u
			INNER JOIN Standards.Employees e ON u.eid = e.eid
		   WHERE groups = 'hr' AND e.status='0'
		   ORDER BY e.lst";	
$hr_query = $dbh->prepare($hr_sql);			   						 
/* ------------- Getting Approvers List ------------- */						  				 
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app2_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.two = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");						   
$app4_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.four = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app3_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");							   					   
$app5_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.five = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app6_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.six = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app8_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.eight = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");				 				  	
/* -------------------------------------------------------------				
 * ------------- END DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */


/* ------------- ******* START APPROVAL PROCESSING ******* --------------------- */
if ($_POST['stage'] == "update" or $_POST['stage'] == "post_update") {
	
	/* -------------------------------------------------------------
	 * ---------- START REQUESTER PROCESSING ----------------------- 
	 * -------------------------------------------------------------
	 */
	if ($_POST['auth'] == "req" OR $_SESSION['hcr_groups'] == 'ex') {

		/* ---------------- START CANCEL REQUEST ----------------  */	
		if ($_POST['cancel'] == 'yes') {
			$dbh->query("UPDATE Requests SET status='C' WHERE id = ".$_POST['request_id']);
			header("location: list.php?action=my&access=0");
			exit();
		}
		/* ---------------- END CANCEL REQUEST ----------------  */

		/* ---------------- START RESTORE REQUEST ----------------  */
		if ($_POST['restore'] == 'yes') {
			$dbh->query("UPDATE Requests SET status='N' WHERE id = ".$_POST['request_id']);
			header("Location: router.php?request_id=".$_POST['request_id']."&approval=app0");
			exit();
		}
		/* ---------------- END RESTORE REQUEST ----------------  */


		/* Forword to detail view */
		header("Location: detail.php?id=".$_POST['request_id']."");
		exit();
	}
	/* -------------------------------------------------------------
	 * ---------- END REQUESTER PROCESSING ------------------------ 
	 * -------------------------------------------------------------
	 */
	
	
	/* -------------------------------------------------------------
	 * ---------- START APPROVAL PROCESSING ------------------------ 
	 * -------------------------------------------------------------
	 */
	if (ereg("app", $_POST['auth'])) {
		/* Change status for non approved request */
		if (array_key_exists('no_x', $_POST)) {
		  setRequestStatus($_POST['request_id'], 'X');
		}
		
		// Get available Approvers to switch at each level
		$APPROVERS = getApprovers($_POST['auth'], $REQUEST['request_type']);
						
		/* Update the approvals for the PO */
		$sql = "UPDATE Authorization 
					 SET ".$_POST['auth']."yn='" . mysql_real_escape_string(htmlentities($_POST['yn'], ENT_COMPAT, 'UTF-8')) . "', 
					     ".$_POST['auth']."Date=NOW(), 
						 ".$_POST['auth']."Com='" . mysql_real_escape_string(htmlentities($_POST['comment'], ENT_COMPAT, 'UTF-8')) . "', 
						$APPROVERS
					 WHERE request_id = ".$_POST['request_id'];				 
		$dbh->query($sql);
		
		// Record transaction for history
		debug_capture($_SESSION['eid'], $_POST['request_id'], $default['debug_capture'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		

		// Set status after final approval
		if ($_POST['auth'] == 'app6' AND $_POST['app6yn'] == 'yes') {
		  setRequestStatus($_POST['request_id'], 'O');		// Mark request as Completed
		}
		
		// Forword user
		$forward = "router.php?request_id=".$_POST['request_id']."&approval=".$_POST['auth']."&yn=".$_POST['yn'];
		
		header("Location: ".$forward);
		exit();
	}
	/* -------------------------------------------------------------
	 * ---------- END APPROVAL PROCESSING ------------------------ 
	 * -------------------------------------------------------------
	 */
}

/* -------------------------------------------------------------
 * ---------- START APPROVED PROCESSING --------------------- 
 * -------------------------------------------------------------
 */
if ($_GET['action'] == 'approved' OR $_GET['action'] == 'notapproved') {
	/* Insert Processed Date */				 
	$auth_sql = "UPDATE Authorization 
				 SET coordinatorDate=NOW(),
				     approved='$_GET[action]' 
				 WHERE request_id = ".$_GET['request_id'];
	$dbh->query($auth_sql);	

	$forward = "router.php?request_id=".$_GET['request_id']."&approval=coordinator&action=".$_GET['action']."&fullname=".$_GET['fullname'];	
	header("Location: ".$forward);
	exit();		
}
/* -------------------------------------------------------------
 * ---------- END APPROVED PROCESSING --------------------- 
 * -------------------------------------------------------------
 */	  
/* ------------------ ******* END APPROVAL PROCESSING ******* ----------------------- */

/* ------------- Getting Comments Information ------------- */
$post_sql = "SELECT * FROM Postings 
			 WHERE request_id = ".$ID." 
			   AND type LIKE 'global'
			 ORDER BY posted DESC";
$LAST_POST = $dbh->getRow($post_sql);		// Get the last posted comment
$post_query = $dbh->prepare($post_sql);						   
$post_sth = $dbh->execute($post_query);
$post_count = $post_sth->numRows();	


/* ------------------ ******* START VARIABLES ******* ----------------------- */
$status=labelStatus($REQUEST['status']);		// Get status name

//$showContent=showContent($_GET['approval']);	// Set display and color status

if (!array_key_exists('approval', $_GET) AND $REQUEST['status'] != 'O') {
	$message="Communication can not be delivered to employee. <br>Human Resources will inform you when all approvals have been completed.";
}

$positionTitle=explode(":", $REQUEST['positionTitle']);
$department=explode(":", $REQUEST['department']);
$plant=explode(":", $REQUEST['plant']);
$salaryGrade=explode(":", $COMPA['salaryGrade']);
$salary=explode(":", $COMPA['salary']);
$overTime=explode(":", $COMPA['overTime']);
$doubleTime=explode(":", $COMPA['doubleTime']);
$vacationDays=explode(":", $COMPA['vacationDays']);
$vehicleAllowance=explode(":", $COMPA['vehicleAllowance']);

/* ---- Setup overtime fields ---- */
switch ($overTime[0]) {
	case 'E': $overTime[0] = "Exempt"; $doubleTime[0] = "Exempt"; break;
	case 'ST': $overTime[0] = "Straight Time"; $doubleTime[0] = "Straight Time"; break;
	case 'TH': $overTime[0] = "Time and a half"; $doubleTime[0] = "Time and a half"; break;
} 	

/* ---- Setup overtime fields ---- */
switch ($overTime[1]) {
	case 'E': $overTime[1] = "Exempt"; $doubleTime[1] = "Exempt"; break;
	case 'ST': $overTime[1] = "Straight Time"; $doubleTime[1] = "Straight Time"; break;
	case 'TH': $overTime[1] = "Time and a half"; $doubleTime[1] = "Time and a half"; break;
}

/* ------------- Get who denied the Requisition ------------- */
if ($REQUEST['status'] == 'X') {
	$who = array_search('no', $AUTH);
	$canceled = '#' . substr($who,0,4) . 'Status';
}

/* ------------- Check current level and current user ------------- */
switch ($REQUEST['status']) {
	case 'C':
	case 'X':
	case 'A':
	case 'O': unset($_GET['approval']); break;
}
	
if (array_key_exists('approval', $_GET)) {
	if ($AUTH[$AUTH['level']] != $_SESSION['eid']) {
		$message="<img src=\"/Common/images/nochange.gif\" align=\"absmiddle\" /> You are not authorized to approve this requisition.";
		unset($_GET['approval']);
	} elseif ($_GET['approval'] != $AUTH['level']) {
		$message="<img src=\"/Common/images/nochange.gif\" align=\"absmiddle\" /> This Requisition is currently not at your approval level.";
		unset($_GET['approval']);
	} elseif ($_GET['switch'] == 'auto') {
		$message="<div class=\"appJump\"<img src=\"/Common/images/action.gif\" align=\"absmiddle\" /> This requisition is waiting for your approval</div>";
	}
} elseif ($REQUEST['status'] == 'N') {
	$message="<div class=\"appJump\"<img src=\"/Common/images/action.gif\" align=\"absmiddle\" /> This requisition is waiting for action from " . caps($EMPLOYEES[$AUTH[$AUTH['level']]] . "</div>");
}
/* ------------------ ******* END VARIABLES ******* ----------------------- */
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $language['label']['title1']; ?></title>
  <!-- InstanceEndEditable -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2006 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>

  <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/reset-fonts-grids/reset-fonts-grids.css" />   <!-- CSS Grid -->
  <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/assets/skins/custom/menu.css">  					<!-- Menu -->  
  
  <link type="text/css" href="/Common/Javascript/greybox5/gb_styles.css" rel="stylesheet" media="all">      
   
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/christmas/default.css" />
  <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />  
  
  <script type="text/javascript" src="/Common/Javascript/styleswitcher.js"></script>
  
  <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>
  <!-- InstanceBeginEditable name="head" -->
    
  <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>

  <!-- InstanceEndEditable -->
  </head>

<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
  
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/companyPrint.gif" name="Print" width="437" height="61" id="Print" />
            <a href="../home.php" title="<?= $default['title1']; ?>|Home Page"><img src="/Common/images/company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u" id="centerTitle"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u" style="text-align:right;margin:1em 0;padding:0;">
              <div id="applicationTitle" style="font-weight:bold;font-size:115%;text-align:right"><?= $language['label']['title1']; ?>&nbsp;</div>
              <div id="loggedInUser" class="loggedInUser" style="text-align:right"><strong><a href="Administration/user_information.php" class="loggedInUser" title="User Task|Edit your user information"><?= caps($_SESSION['fullname']); ?></a></strong>&nbsp;<a href="../logout.php" class="loggedInUser" title="User Task|Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;</div>
            <div id="styleSwitcher" style="text-align:right">Themes: <span id="defaultStyle" class="style" title="Style Switcher|Default Colors"><a href="#" onClick="setActiveStyleSheet('default'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="seasonalStyle" class="style" title="Style Switcher|Christmas Season"><a href="#" onClick="setActiveStyleSheet('seasonal'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="nightStyle" class="style" title="Style Switcher|Night Time Colors"><a href="#" onClick="setActiveStyleSheet('night'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span>&nbsp;</div>
          </div>
      </div>		      
   </div>
    
   <div id="bd">
       <div class="yui-g" id="mm"><?php include($default['FS_HOME'].'/include/main_menu.php'); ?></div>
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->    
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td>
          </td>
        </tr>
        <tr>
          <td><form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form">
            <table border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td height="26" colspan="2">
                        <div id="requestStatusContainer" style="float: right">
                            <div id="requestStatusTitle">Status Indicator</div>
                            <div id="requestStatus" title="User Task|Click the Requisition Status window to jump to approvals panel"><?= $status; ?><input type="hidden" name="status" value="<?= $REQUEST['status']; ?>">
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr class="BGAccentVeryDark">
                      <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;<?= caps($REQUEST['request_type']); ?></td>
                      <td width="50%" align="right"><span class="DarkHeader"> Number: HC-<?= $ID; ?>&nbsp;&nbsp;</span></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                          <tr>
                            <td height="25" class="BGAccentDark"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td>&nbsp;<img src="../images/info.png" width="16" height="16" align="texttop"><span class="DarkHeaderSubSub"> <?= $language['label']['stage1.1']; ?>... </span> </td>
                                <td><div align="right" id="status" class="DarkHeaderSub">&nbsp;</div></td>
                              </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td>
                              <div class="panelContent">
                               <table width="100%"  border="0">
                                <tr>
                                  <td width="175" >Requester:</td>
                                  <td class="label"><?= caps($EMPLOYEES[$REQUEST['req']]); ?></td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td >Request Date:</td>
                                  <td class="label"><?= $REQUEST['_reqDate']; ?></td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td height="10" colspan="4" ><img src="../images/spacer.gif" width="5" height="10"></td>
                                  </tr>
                                <tr>
                                  <td >Employee:</td>
                                  <td width="240" class="label"><?= caps($EMPLOYEES[$REQUEST['eid']]); ?></td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td valign="top"><?= $language['label']['startDate']; ?>:</td>
                                  <td class="label"><?= $REQUEST['_startDate']; ?></td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td >&nbsp;</td>
                                  <td class="currentnew">Current</td>
                                  <td>&nbsp;</td>
                                  <td class="currentnew">New</td>
                                </tr>
                                <tr>
                                  <td><?= $language['label']['positionTitle']; ?>:</td>
                                  <td class="label"><?= caps($POSITIONTITLE[$positionTitle[0]]); ?></td>
                                  <td>&nbsp;</td>
                                  <td class="label"><?= caps($POSITIONTITLE[$positionTitle[1]]); ?></td>
                                </tr>
                                <tr>
                                  <td ><?= $language['label']['plant']; ?>:</td>
                                  <td class="label"><?= caps($PLANT[$plant[0]]); ?></td>
                                  <td>&nbsp;</td>
                                  <td class="label"><?= caps($PLANT[$plant[1]]); ?></td>
                                </tr>
                                <tr>
                                  <td ><?= $language['label']['department']; ?>:</td>
                                  <td class="label"><?= "(".$department[0].") ".caps($DEPT[$department[0]]); ?></td>
                                  <td>&nbsp;</td>
                                  <td class="label"><?= "(".$department[1].") ".caps($DEPT[$department[1]]); ?></td>
                                </tr>
                            </table>
                           </div>
                          </td>
                         </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                          <tr>
                            <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub"><img src="../images/notes.gif" width="12" height="15" align="texttop"> <?= $language['label']['stage2']; ?>...</span></strong></td>
                          </tr>
                          <tr>
                            <td>
                             <div class="panelContent">
                              <table width="100%"  border="0">
                              <tr>
                                <td width="175" valign="top"><?= $language['label']['justification']; ?>:</td>
                                <td class="label"><textarea name="justification" cols="90" rows="10" wrap="VIRTUAL" readonly id="justification" class="BGAccentDarkBorder"><?= stripslashes($REQUEST['justification']); ?></textarea></td>
                              </tr>
                              <tr>
                                <td valign="top"><?= $language['label']['primaryJob']; ?>:</td>
                                <td class="label"><textarea name="primaryJob" cols="90" rows="10" wrap="VIRTUAL" readonly id="primaryJob" class="BGAccentDarkBorder"><?= stripslashes($REQUEST['primaryJob']); ?></textarea></td>
                              </tr>
                            </table>
                            </div>
                           </td>
                         </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                          <tr>
                            <td height="25" class="BGAccentDark"><strong>&nbsp;<img src="../images/money.gif" width="20" height="17" align="texttop">&nbsp;<span class="DarkHeaderSubSub"><?= $language['label']['stage1.3']; ?>... </span></strong></td>
                          </tr>
                          <tr>
                            <td>
                             <div class="panelContent">
                              <table width="100%"  border="0">
                            	<?php if ($REQUEST['request_type'] == 'conversion') { ?>
                                <tr>
                                  <td width="175"><span >Contract Agency:</span></td>
                                  <td width="240" class="label"><?= $AGENCY['name']; ?></td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td >Current Bill Rate: </td>
                                        <td align="right">$</td>
                                      </tr>
                                  </table></td>
                                  <td class="label"><?= $COMPA['billRate']; ?></td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <?php } ?>
                                <tr>
                                  <td>&nbsp;</td>
                                  <td class="currentnew">Current</td>
                                  <td>&nbsp;</td>
                                  <td class="currentnew">New</td>
                                </tr>
                                <tr>
                                  <td ><?= $language['label']['salaryGrade']; ?>:</td>
                                  <td class="label"><?= base64_decode($salaryGrade[0]); ?></td>
                                  <td>&nbsp;</td>
                                  <td class="label"><?= base64_decode($salaryGrade[1]); ?></td>
                                </tr>
                                <tr>
                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td ><?= $language['label']['salary']; ?>:</td>
                                        <td align="right">$</td>
                                      </tr>
                                  </table></td>
                                  <td class="label"><?= number_format(base64_decode($salary[0]),2); ?></td>
                                  <td align="right" >$</td>
                                  <td class="label"><?= number_format(base64_decode($salary[1]),2); ?></td>
                                </tr>
                                <tr>
                                  <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Over Time:</td>
                                  <td class="label"><?= $overTime[0]; ?></td>
                                  <td>&nbsp;</td>
                                  <td class="label"><?= $overTime[1]; ?></td>
                                </tr>
                                <tr>
                                  <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Double Time:</td>
                                  <td class="label"><?= $doubleTime[0]; ?></td>
                                  <td >&nbsp;</td>
                                  <td class="label"><?= $doubleTime[1]; ?></td>
                                </tr>
                                <tr>
                                  <td>Vacation:</td>
                                  <td class="label"><?= $vacationDays[0]; ?> days</td>
                                  <td >&nbsp;</td>
                                  <td class="label"><?= $vacationDays[1]; ?> days</td>
                                </tr>
                                <tr>
                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td >Car Allowance:</td>
                                        <td align="right">$</td>
                                      </tr>
                                    </table></td>
                                  <td class="label"><?= number_format(base64_decode($vehicleAllowance[0]),0); ?></td>
                                  <td align="right" >$</td>
                                  <td class="label"><?= number_format(base64_decode($vehicleAllowance[1]),0); ?></td>
                                </tr>
                                <tr>
                                  <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                </tr>
                                <tr>
                                  <td colspan="4"><!--<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="415" align="right"><strong>Percentage:</strong></td>
                                        <td align="right">&nbsp;</td>
                                        <td><?= $COMPA['percentage']; ?>%</td>
                                      </tr>
                                      <tr>
                                        <td align="right"><strong>Increase Amount:</strong></td>
                                        <td align="right">$</td>
                                        <td><?= $COMPA['increase']; ?></td>
                                      </tr>
                                  </table>--></td>
                                </tr>
                            </table>
                           </div>
                          </td>
                        </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlueBorder : BGAccentDarkBorder; ?>"><table width="100%" border="0">
                          <tr>
                            <td width="100%" height="25" colspan="6" class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlue : BGAccentDark; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td>&nbsp;<a href="javascript:switchComments();" class="<?= (array_key_exists('approval', $_GET)) ? white : black; ?>"><strong><img src="../images/comments.gif" width="19" height="16" border="0" align="texttop"><span class="DarkHeaderSubSub">&nbsp;Comments</span></a></td>
                                  <td width="120"><a href="comments.php?request_id=<?= $ID; ?>&eid=<?= $_SESSION['eid']; ?>" title="Message Center|Post a new comment" rel="gb_page_center[675,325]" class="<?= (array_key_exists('approval', $_GET)) ? addWhite : addBlack; ?>">NEW COMMENT</a></td>
                                </tr>
                            </table></td>
                          </tr>
                        <td>
						<div class="panelContent">
						<?php if ($post_count > 0) { ?>
                        <div id="commentsHeader" onClick="switchComments();">There
                          <?= ($post_count > 1) ? are : is; ?>
                          currently <strong>
                            <?= $post_count; ?>
                            </strong> comment
                          <?= ($post_count > 1) ? s : ''; ?>
                          . The last comment was posted on <strong>
                            <?= date('F d, Y \a\t H:i A', strtotime($LAST_POST['posted'])); ?>
                            </strong>.<br>
                          <br>
                          <div class="clickToView">Click to view all Comments.</div>
                        </div>
                          <?php } else { ?>
                        <div id="commentsHeader">There are currently <strong>NO</strong> comments.</div>
                          <?php } ?>
                        <div width="95%" border="0" align="center" id="commentsArea" style="display:none" onClick="switchComments();"> <br>
                            <?php
								$count=0;
								while($post_sth->fetchInto($POST)) {
									$count++;
							  ?>
                            <div class="comment">
                              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td width="55" rowspan="3" valign="top" class="comment_datenum"><div class="comment_month">
                                      <?= date("M", strtotime($POST['posted'])); ?>
                                    </div>
                                      <div class="comment_day">
                                        <?= date("d", strtotime($POST['posted'])); ?>
                                      </div>
                                    <div class="comment_year">
                                        <?= date("y", strtotime($POST['posted'])); ?>
                                    </div></td>
                                  <td class="comment_wrote"><?= caps($EMPLOYEES[$POST[eid]]); ?>
                                    wrote... </td>
                                </tr>
                                <tr>
                                  <td class="commentbody"><?= caps(stripslashes($POST['comment'])); ?></td>
                                </tr>
                                <tr>
                                  <td class="comment_date"><?= date("h:i A", strtotime($POST['posted'])); ?></td>
                                </tr>
                              </table>
                            </div>
                          <br>
                            <?php } ?>
                        </div></div></td>
                      </table></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlueBorder : BGAccentDarkBorder; ?>">
                       <div id="approvals_panel" class="panelContent">
                        <table width="100%" border="0">
                          <tr class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlue : BGAccentDark; ?>">
                            <td width="150" height="25" nowrap>&nbsp;</td>
                            <td width="30" nowrap>&nbsp;</td>
                            <td width="150" nowrap>&nbsp;</td>
                            <td width="125" nowrap><strong>Date</strong></td>
                            <td width="20" align="center" nowrap><img src="/Common/images/clock.gif" width="16" height="16"></td>
                            <td width="200"><strong>Comment</strong></td>
                            <td width="75" nowrap><strong>
                              <?= (array_key_exists('approval', $_GET)) ? 'Approval' : $blank; ?>
                            </strong></td>
                          </tr>
                          <tr>
                            <td nowrap>Requester:</td>
                            <td align="center" nowrap><?= showCommentIcon($REQUEST['req'],  caps($EMPLOYEES[$REQUEST['req']]), $REQUEST['id']); ?></td>
                            <td nowrap class="label"><?= caps($EMPLOYEES[$REQUEST['req']]); ?></td>
                            <td nowrap class="label"><?= $REQUEST['_reqDate']; ?></td>
                            <td nowrap class="TrainActive">-</td>
                            <td nowrap>&nbsp;</td>
                            <td nowrap>&nbsp;</td>
                          </tr>
                          <!-- START APPROVER 1 -->
                          <tr <?= ($_GET['approval'] == 'app1') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app1']; ?>:</td>
                            <td align="center" nowrap><?php 
													  if (is_null($AUTH['app1Date'])) {
                                                    	echo showMailIcon('app1', $AUTH['app1'], caps($EMPLOYEES[$AUTH['app1']]), $REQUEST['id']);
                                                      } else { 
													    echo showCommentIcon($AUTH['app1'], caps($EMPLOYEES[$AUTH['app1']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app1Date'])) { echo date("F d, Y", strtotime($AUTH['app1Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app1Date'])) { echo abs(ceil((strtotime($REQUEST['reqDate']) - strtotime($AUTH['app1Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app1', $_GET['approval'], $AUTH['app1'], $AUTH['app1Com'], $AUTH['app1Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 1 -->
                          <!-- START APPROVER 2 -->
                          <tr <?= ($_GET['approval'] == 'app2') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app2']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app2Date']) AND !is_null($AUTH['app1Date']) AND $AUTH['app2'] != '0') {
                                                    	echo showMailIcon('app2', $AUTH['app2'], caps($EMPLOYEES[$AUTH['app2']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app2Date'])) { 
													    echo showCommentIcon($AUTH['app2'], caps($EMPLOYEES[$AUTH['app2']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app2Date'])) { echo date("F d, Y", strtotime($AUTH['app2Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app2Date'])) { echo abs(ceil((strtotime($AUTH['app1Date']) - strtotime($AUTH['app2Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app2', $_GET['approval'], $AUTH['app2'], $AUTH['app2Com'], $AUTH['app2Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 2 -->
                          <!-- END APPROVER 3 -->
                          <tr <?= ($_GET['approval'] == 'app3') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app3']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app3Date']) AND !is_null($AUTH['app2Date']) AND $AUTH['app3'] != '0') {
                                                    	echo showMailIcon('app3', $AUTH['app3'], caps($EMPLOYEES[$AUTH['app3']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app3Date'])) { 
													    echo showCommentIcon($AUTH['app3'], caps($EMPLOYEES[$AUTH['app3']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app3Date'])) { echo date("F d, Y", strtotime($AUTH['app3Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app3Date'])) { echo abs(ceil((strtotime($AUTH['app2Date']) - strtotime($AUTH['app3Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app3', $_GET['approval'], $AUTH['app3'], $AUTH['app3Com'], $AUTH['app3Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 3 -->
                          <!-- START APPROVER 4 -->
                          <tr <?= ($_GET['approval'] == 'app4') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app4']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app4Date']) AND !is_null($AUTH['app2Date']) AND $AUTH['app4'] != '0') {
                                                    	echo showMailIcon('app4', $AUTH['app4'], caps($EMPLOYEES[$AUTH['app4']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app4Date'])) { 
													    echo showCommentIcon($AUTH['app4'], caps($EMPLOYEES[$AUTH['app4']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app4Date'])) { echo date("F d, Y", strtotime($AUTH['app4Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app4Date'])) { echo abs(ceil((strtotime($AUTH['app3Date']) - strtotime($AUTH['app4Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app4', $_GET['approval'], $AUTH['app4'], $AUTH['app4Com'], $AUTH['app4Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 4 -->
                          <!-- START APPROVER 5 -->
                          <tr <?= ($_GET['approval'] == 'app5') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app5']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app5Date']) AND !is_null($AUTH['app4Date']) AND $AUTH['app5'] != '0') {
                                                    	echo showMailIcon('app5', $AUTH['app5'], caps($EMPLOYEES[$AUTH['app5']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app5Date'])) { 
													    echo showCommentIcon($AUTH['app5'], caps($EMPLOYEES[$AUTH['app5']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app5', $AUTH['app5'], $AUTH['app5Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app5Date'])) { echo date("F d, Y", strtotime($AUTH['app5Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app5Date'])) { echo abs(ceil((strtotime($AUTH['app4Date']) - strtotime($AUTH['app5Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app5', $_GET['approval'], $AUTH['app5'], $AUTH['app5Com'], $AUTH['app5Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app5', $AUTH['app5'], $AUTH['app5Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 5 -->
                          <!-- START APPROVER 6 -->
                          <tr <?= ($_GET['approval'] == 'app6') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app6']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app6Date']) AND !is_null($AUTH['app5Date']) AND $AUTH['app6'] != '0') {
                                                    	echo showMailIcon('app6', $AUTH['app6'], caps($EMPLOYEES[$AUTH['app6']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app6Date'])) { 
													    echo showCommentIcon($AUTH['app6'], caps($EMPLOYEES[$AUTH['app6']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app6', $AUTH['app6'], $AUTH['app6Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app6Date'])) { echo date("F d, Y", strtotime($AUTH['app6Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app6Date'])) { echo abs(ceil((strtotime($AUTH['app5Date']) - strtotime($AUTH['app6Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app6', $_GET['approval'], $AUTH['app6'], $AUTH['app6Com'], $AUTH['app6Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app6', $AUTH['app6'], $AUTH['app6Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 6 -->
                          <!-- START APPROVER 7 -->
                          <?php if ($app7_status) { ?>
                          <tr <?= ($_GET['approval'] == 'app7') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app7']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app7Date']) AND !is_null($AUTH['app6Date']) AND $AUTH['app7'] != '0') {
                                                    	echo showMailIcon('app7', $AUTH['app7'], caps($EMPLOYEES[$AUTH['app7']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app7Date'])) { 
													    echo showCommentIcon($AUTH['app7'], caps($EMPLOYEES[$AUTH['app7']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app7', $AUTH['app7'], $AUTH['app7Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app7Date'])) { echo date("F d, Y", strtotime($AUTH['app7Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app7Date'])) { echo abs(ceil((strtotime($AUTH['app6Date']) - strtotime($AUTH['app7Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app7', $_GET['approval'], $AUTH['app7'], $AUTH['app7Com'], $AUTH['app7Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app7', $AUTH['app7'], $AUTH['app7Date']); ?></td>
                          </tr>
                          <?php } ?>
                          <!-- END APPROVER 7 -->
                          <!-- START APPROVER 8 -->
                          <tr <?= ($_GET['approval'] == 'app8') ? $highlight : $blank; ?>>
                            <td nowrap><?= $language['label']['app8']; ?>:</td>
                            <td align="center" nowrap><?php if (is_null($AUTH['app8Date']) AND !is_null($AUTH['app7Date']) AND $AUTH['app8'] != '0') {
                                                    	echo showMailIcon('app8', $AUTH['app8'], caps($EMPLOYEES[$AUTH['app8']]), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app8Date'])) { 
													    echo showCommentIcon($AUTH['app8'], caps($EMPLOYEES[$AUTH['app8']]), $REQUEST['id']);
													  }
													  ?></td>
                            <td nowrap class="label">                              <?= displayApprover($_GET['id'], 'app8', $AUTH['app8'], $AUTH['app8Date']); ?>                            </td>
                            <td nowrap class="label"><?php if (isset($AUTH['app8Date'])) { echo date("F d, Y", strtotime($AUTH['app8Date'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app8Date'])) { echo abs(ceil((strtotime($AUTH['app6Date']) - strtotime($AUTH['app8Date'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label"><?= displayAppComment('app8', $_GET['approval'], $AUTH['app8'], $AUTH['app8Com'], $AUTH['app8Date']); ?></td>
                            <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app8', $AUTH['app8'], $AUTH['app8Date']); ?></td>
                          </tr>
                          <!-- END APPROVER 8 -->
                          <!-- START STAFFING -->
                          <tr <?= ($_GET['approval'] == 'generator') ? $highlight : $blank; ?>>
                            <td nowrap>EID Generator: </td>
                            <td align="center" nowrap><?php if (!empty($AUTH['coordinatorDate']) AND empty($EINFO['eid'])) { ?>
                                <a href="<?= $default['URL_HOME']; ?>/Administration/generateEID.php?request_id=<?= $ID; ?>" title="HR Actions|Generate an employee ID"><img src="../images/vcard.gif" width="20" height="16" border="0"></a>
                                <?php } ?>                            </td>
                            <td nowrap class="label"><?= caps($EMPLOYEES[$AUTH['generator']]); ?></td>
                            <td nowrap class="label"><?php if (isset($AUTH['generatorDate'])) { echo date("F d, Y", strtotime($AUTH['generatorDate'])); } ?></td>
                            <td nowrap class="TrainActive"><?php if (isset($AUTH['generatorDate'])) { echo abs(ceil((strtotime($AUTH['coordinatorDate']) - strtotime($AUTH['generatorDate'])) / (60 * 60 * 24))); } ?></td>
                            <td nowrap class="label">-</td>
                            <td nowrap>&nbsp;</td>
                          </tr>
                          <tr class="xpHeaderTotal">
                            <td height="25" nowrap>Totals:</td>
                            <td nowrap>&nbsp;</td>
                            <td nowrap>&nbsp;</td>
                            <td nowrap>&nbsp;</td>
                            <td nowrap class="TrainActive"><?= abs(ceil((strtotime($REQUEST['reqDate']) - strtotime($AUTH['generatorDate'])) / (60 * 60 * 24))); ?></td>
                            <td nowrap class="TipLabel">Days</td>
                            <td nowrap>&nbsp;</td>
                          </tr>
                      </table>
                      </div>
                     </td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="5" valign="bottom"><img src="../images/spacer.gif" width="5" height="5"></td>
              </tr>
              <tr>
                <td><?php if ($REQUEST['status'] != 'X') { ?>
                    <div id="noPrint2">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%" valign="middle"><?php if (($_SESSION['eid'] == $REQUEST['req'] AND $REQUEST['status'] != 'C' AND empty($_GET['approval'])) OR $_SESSION['hcr_groups'] == 'ex') { ?>
                              <table  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td width="20" valign="middle"><input name="cancel" type="checkbox" id="cancel" value="yes"></td>
                                  <td><input name="imageField" type="image" src="../images/button.php?i=w130.png&l=Cancel Request" alt="Cancel Request" border="0"></td>
                                </tr>
                              </table>
                            <?php } ?>
                              <!--
								<?php if ($_SESSION['eid'] == $REQUEST['req'] AND ($REQUEST['status'] == 'X' OR $REQUEST['status'] == 'C')) { ?>
                                   <table  border="0" cellspacing="0" cellpadding="0">
                                     <tr>
                                       <td width="20" valign="middle"><input name="restore" type="checkbox" id="restore" value="yes"></td>
                                       <td><input name="imageField" type="image" src="../images/button.php?i=w130.png&l=Restore Request" alt="Restore Request" border="0"></td>
                                     </tr>
                                   </table>
                                <?php } ?>
                                -->
                          </td>
                          <td width="50%" align="right"><?php if (($_SESSION['eid'] == $AUTH[$_GET['approval']] OR
									 							   $_SESSION['eid'] == $REQUEST['req'] OR
																   $_SESSION['eid'] == $AUTH['staffing'])) { ?>
                              <?php
								 if (isset($_GET['approval'])) {
									/* Set auth level to GET[approval] */
									$auth_value = $_GET['approval'];
								 } elseif ($_SESSION['eid'] == $REQUEST['req']) {
									/* Allow update if GET[approval] was sent and Requester is viewing */
									$auth_value = "req";
								 } elseif ($_SESSION['eid'] == $AUTH['staffing']) {
									/* Allow update if GET[approval] was sent and Requester is viewing */
									$auth_value = "staffing";
								 }
								 
								 /* Set type of update before or after PO was issued */
								 $update_stage = (empty($REQUEST[po])) ? "update" : "post_update";
							   ?>
                              <input name="auth" type="hidden" id="auth" value="<?= $auth_value; ?>">
                              <input name="auth_id" type="hidden" id="auth_id" value="<?= $AUTH['id']; ?>">
                              <input name="request_id" type="hidden" id="request_id" value="<?= $ID; ?>">
                              <input name="action" type="hidden" id="action" value="<?= $update_stage ?>">
                              <input name="stage" type="hidden" id="stage" value="<?= $update_stage ?>">
                          	  <!--<input name="imageField" type="image" src="../images/button.php?i=b150.png&l=Update Request" alt="Update Request" border="0">-->
                              <?php } ?>
                            &nbsp;</td>
                        </tr>
                      </table>
                    </div>
                  <?php } ?>
                </td>
              </tr>
            </table>
          </form></td>
        </tr>
      </tbody>
      </table>
  <!-- InstanceEndEditable --></div>
   </div>
   
   <div id="ft" style="padding-top:50px">
     <div class="yui-gb">
        <div class="yui-u first"><?php include($default['FS_HOME'].'/include/copyright.php'); ?></div>
        <div class="yui-u"><!-- FOOTER CENTER AREA -->&nbsp;</div>
        <div class="yui-u" style="text-align:right;margin:1em 0;padding:0;"><?php include($default['FS_HOME'].'/include/right_footer.php'); ?></div>
     </div>
   </div>
     
</div>
    
<script>
	var message='<?= $message; ?>';
	var msgClass='<?= $msgClass; ?>';
</script>
    
<script type="text/javascript" src="/Common/Javascript/yahoo/yahoo-dom-event/yahoo-dom-event.js" ></script>		<!-- Menu, TabView, Datatable -->
<script type="text/javascript" src="/Common/Javascript/yahoo/container/container-min.js"></script> 				<!-- Menu -->
<script type="text/javascript" src="/Common/Javascript/yahoo/menu/menu-min.js"></script> 						<!-- Menu -->

<script type="text/javascript" src="/Common/Javascript/greybox5/options1.js"></script>
<script type="text/javascript" src="/Common/Javascript/greybox5/AJS.js"></script>
<script type="text/javascript" src="/Common/Javascript/greybox5/AJS_fx.js"></script>
<script type="text/javascript" src="/Common/Javascript/greybox5/gb_scripts.js"></script>
<?php if ($ONLOAD_OPTIONS) { ?>
<script language="javascript">
AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
</script>
<?php } ?>  

<script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.dimensions-min.js"></script>
<script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.cluetip-min.js"></script>

<script type="text/javascript" src="../js/jQdefault.js"></script>
<!-- InstanceBeginEditable name="js" -->
    <script>
		var request_id='<?= $_GET['id']; ?>';
		var approval='<?= $_GET['approval']; ?>';
		var level='<?= $AUTH['level']; ?>';
		var status='<?= $REQUEST['status']; ?>';
		var canceled='<?= $canceled; ?>';
		var message='<?= $message; ?>';
		var msgClass='<?= $msgClass; ?>';
    </script>
    <script type="text/javascript" src="../js/jQdefault.js"></script>
    <script type="text/javascript" src="../js/jQdetail.js"></script>
    
    <script type="text/javascript" src="/Common/Javascript/jquery/scrollTo/jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.dimensions-min.js"></script>
    <script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.cluetip-min.js"></script>
    <!-- InstanceEndEditable --> 
<script type="text/javascript">
/* ========== YUI Main Menu ========== */
YAHOO.util.Event.onContentReady("productsandservices", function () {
	var oMenuBar = new YAHOO.widget.MenuBar("productsandservices", { autosubmenudisplay: true, hidedelay: 750, lazyload: true });
	oMenuBar.render();
});
</script> 
	
<?php if (!$debug_page) { ?>   
<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "<?= $default['google_analytics']; ?>";
urchinTracker();
</script>
<?php } ?>
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