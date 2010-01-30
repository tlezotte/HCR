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
$debug_page = false;
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
 * - Display Status
 */
require_once('../include/displayStatus.php');
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Access to Request
 */
require_once('../security/request_access.php');
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');



/* --- START REDIRECT TO SUMMARY.PHP ----- */
if ($_GET['approval'] == 'communication' OR $_GET['approval'] == 'desk') {
	$forward="summary.php?id=".$_GET['id']."&approval=".$_GET['approval'];
	header("Location: ".$forward);
	exit();
}
/* --- END REDIRECT TO SUMMARY.PHP ----- */

/* --- START ADMINISTRATION SECTION ----- */
if ($_GET['administration'] == 'conversion') {
	$_SESSION['conversion'] = $_GET['action']($_GET['data']);
	
	header("Location: ".$_GET['redirect']);
	exit();	
}
/* --- END ADMINISTRATION SECTION ----- */

/* --- START CANCEL PURCHASE ORDER ----- */
if ($_POST['stage'] == "update") {
	if ($_POST['auth'] == "req" OR $_SESSION['hcr_groups'] == 'ex') {
		if ($_POST['cancel'] == 'yes') {
			setRequestStatus($_POST['request_id'], 'C');
			
			header("location: list.php?action=my&access=0");
			exit();
		}
	}
}
/* --- END CANCEL PURCHASE ORDER ----- */	
		
/* -------------------------------------------------------------				
 * ------------- START DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */
$ID = (array_key_exists('id', $_POST)) ? $_POST['id'] : $_GET['id'];
 
/* ------------- Getting Request information ------------- */
$REQUEST = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate, DATE_FORMAT(targetDate,'%M %e, %Y') AS _targetDate, DATE_FORMAT(startDate,'%M %e, %Y') AS _startDate
						FROM Requests
						WHERE id = ".$ID);


/* --- START REDIRECT NOT NEW HIRES TO _DETAIL.PHP ----- */
if ($REQUEST['request_type'] != 'new') {
	$approval = (array_key_exists('approval', $_GET)) ? "&approval=$_GET[approval]" : '';

	$forward="_detail.php?id=".$ID . $approval;
	header("Location: ".$forward);
}
/* --- END REDIRECT NOT NEW HIRES TO _DETAIL.PHP ----- */


/* ------------- Getting Authoriztions Request ------------- */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE request_id = ".$ID);


/* --- START REDIRECT APPROVALS ----- */
if ($AUTH[$AUTH['level']] == $_SESSION['eid'] AND $_GET['switch'] != 'auto' AND !isset($_GET['approval'])) {
	$forward="https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id=" . $ID . "&switch=auto&approval=" . $AUTH['level'];
	header("Location: ".$forward);
}
/* --- END REDIRECT APPROVALS ----- */

				
/* ------------- Getting technology information ------------- */
$TECH = $dbh->getRow("SELECT * FROM Technology WHERE request_id = ".$ID);
/* ------------- Getting Estimated Compensation Request ------------- */
$COMP = $dbh->getRow("SELECT * FROM Compensation WHERE request_id = ".$ID." AND status='E'");
/* ------------- Getting Actual Compensation Request ------------- */
$COMPA = $dbh->getRow("SELECT * 
					   FROM Compensation 
					   WHERE request_id = ".$ID." AND status='A' 
					   ORDER BY id DESC
					   LIMIT 1");
/* ------------- Getting Employee Information ------------- */
$EINFO = $dbh->getRow("SELECT *, CONCAT(fst, ' ', lst) AS fullname 
					   FROM Employees 
					   WHERE request_id = ".$ID."
					   ORDER BY id DESC
					   LIMIT 1");
/* ------------- Getting Employee names from Standards database ------------- */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name
							 FROM Users u, Standards.Employees e
							 WHERE e.eid = u.eid");		
/* ------------- Getting Salary Grades ------------- */						 						 
$SALARYGRADE = $dbh->getRow("SELECT *
							  FROM Position
							  WHERE title_id=".$REQUEST['positionTitle']);
/* ------------- Getting Ethnicity information ------------- */
$ethnicity_sql = "SELECT id, name FROM Ethnicity WHERE status='0' ORDER BY name";
$ETHNICITY = $dbh->getAssoc($ethnicity_sql);
$ethnicity_query = $dbh->prepare($ethnicity_sql);
/* ------------- Getting EEO information ------------- */
$eeo_sql = "SELECT id, name FROM EEO WHERE status='0' ORDER BY name";
$EEO = $dbh->getAssoc($eeo_sql);
$eeo_query = $dbh->prepare($eeo_sql);
/* ------------- Getting Salary Grades ------------- */
$salaryGrade_sql = $dbh->prepare("SELECT DISTINCT(grade)
								  FROM Position
								  GROUP BY grade
								  ORDER BY (grade+0) ASC");							  							 	
/* ------------- Getting Employee names from Standards database ------------- */
$employees_sql = $dbh->prepare("SELECT eid, CONCAT(lst,', ',fst) AS name
							    FROM Standards.Employees
								ORDER BY lst");								 							 				
/* ------------- Getting plant locations from Standards.Plants ------------- */							
$plant_sql = $dbh->prepare("SELECT id, name FROM Standards.Plants WHERE status='0' ORDER BY name ASC");
/* ------------- Getting plant locations from Standards.Department ------------- */
$dept_sql  = $dbh->prepare("SELECT * FROM Standards.Department ORDER BY name ASC");
/* ------------- Getting companies from Standards.Companies ------------- */								
$company_sql = $dbh->prepare("SELECT id, name
						      FROM Standards.Companies
						      WHERE id > 0
						      ORDER BY name");	
/* ------------- Getting position titles ------------- */
$positionTitle_sql = $dbh->prepare("SELECT title_id, title_name
						            FROM Position
						            WHERE title_status='0'
									ORDER BY title_name ASC");							  						  
/* ------------- Getting Software ------------- */
$software_sql = $dbh->prepare("SELECT id, name FROM Standards.Software ORDER BY name");	
/* ------------- Getting Contract Agency names ------------- */						  					
$agency_sql = $dbh->prepare("SELECT id, name 
							 FROM Standards.ContractAgency
							 WHERE status='0'
							 ORDER BY name");
$AGENCY = $dbh->getAssoc("SELECT id, name FROM Standards.ContractAgency");
/* ------------- Getting employees that are in the HR group ------------- */
$hr_sql = "SELECT e.eid, CONCAT( e.lst, ', ', e.fst ) AS fullname
		   FROM Users u
			INNER JOIN Standards.Employees e ON u.eid = e.eid
		   WHERE two='1' AND e.status='0' AND u.status='0'
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
$app3_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");						   
$app4_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.four = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");						   
$app5_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.five = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app6_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.six = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app7_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.seven = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app8_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.eight = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");					   				 				  	
/* -------------------------------------------------------------				
 * ------------- END DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */


/* -------------------------------------------------------------				
 * ------------- START APPROVAL PROCESSING -------------------
 * -------------------------------------------------------------
 */	
if ($_POST['stage'] == "update") {
	/* -------------------------------------------------------------
	 * ---------- START APP2 PROCESSING ------------------------ 
	 * -------------------------------------------------------------
	 */
	if ($_POST['auth'] == "app2") {
		/* ------------- Build interview team member list ------------- */
		$interviewTeam = $_POST['interviewTeam1'].":".$_POST['interviewTeam2'].":".$_POST['interviewTeam3'].":".$_POST['interviewTeam4'].":".$_POST['interviewTeam5'];
		/* ------------- Build internal candidates list ------------- */
		$internalCandidates = $_POST['candidateInt1'].":".$_POST['candidateInt2'].":".$_POST['candidateInt3'].":".$_POST['candidateInt4'].":".$_POST['candidateInt5'].":".$_POST['candidateInt6'];
		
		/* ------------- Insert Start Date ------------- */
		$request_sql = "UPDATE Requests 
						SET jobDescription='" . mysql_real_escape_string($_POST['jobDescription']) . "', 
							recruitment='" . mysql_real_escape_string($_POST['recruitment']) . "',
							candidateInt='" . mysql_real_escape_string($internalCandidates) . "',
							candidateExt='" . mysql_real_escape_string($_POST['candidateExt']) . "',
							interviewLeader='" . mysql_real_escape_string($_POST['interviewLeader']) . "',
							interviewTeam='" . mysql_real_escape_string($interviewTeam) . "',
							interviewHR='" . mysql_real_escape_string($_POST['interviewHR']) . "'
						WHERE id=" . $_POST['request_id'];
		$dbh->query($request_sql);
		
		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($request_sql)));		// Record transaction for history
		}
	}
	/* -------------------------------------------------------------
	 * ---------- END APP2 PROCESSING ------------------------ 
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
		  setRequestStatus($_POST['request_id'], 'A');			// Mark request as Approved
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
	 	
	
	/* -------------------------------------------------------------
	 * ---------- START STAFFING PROCESSING ------------------------ 
	 * -------------------------------------------------------------
	 */
	if ($_POST['auth'] == "staffing") {
		$forward = "list.php?action=my&access=0";							// Default forwarded page
			
		if ($_POST['inform'] != 'only') {
			/* ------------- Insert Start Date ------------- */
			$request_sql = "UPDATE Requests 
							SET startDate='" . mysql_real_escape_string($_POST['startDate']) . "', 
								requestType='" . mysql_real_escape_string($_POST['requestTypeA']) . "',
								status='O'
							WHERE id=" . $_POST['request_id'];
			$dbh->query($request_sql);
											 
			/* ------------- Insert Conpensation data ------------- */
			$comp_sql = "INSERT INTO Compensation (id, request_id, recordDate, salaryGrade, salaryType, salary, overTime, doubleTime, vehicleAllowance, vacationDays, relocation, incentive, signingBonus, agency, status) VALUES 
													(NULL,
													'" . mysql_real_escape_string($_POST['request_id']) . "',
													NOW(),
													'" . mysql_real_escape_string($_POST['salaryGradeA']) . "',
													'" . mysql_real_escape_string($_POST['salaryTypeA']) . "',
													'" . mysql_real_escape_string(base64_encode(preg_replace("/,/", "", $_POST['salaryA']))) . "',
													'" . mysql_real_escape_string(base64_encode($_POST['overTimeA'])) . "',
													'" . mysql_real_escape_string(base64_encode($_POST['doubleTimeA'])) . "',
													'" . mysql_real_escape_string(base64_encode($_POST['vehicleAllowanceA'])) . "',
													'" . mysql_real_escape_string($_POST['vacationDaysA']) . "',
													'" . mysql_real_escape_string($_POST['relocationA']) . "',
													'" . mysql_real_escape_string(base64_encode($_POST['incentiveA'])) . "',
													'" . mysql_real_escape_string(base64_encode($_POST['signingBonusA'])) . "',
													'" . mysql_real_escape_string($_POST['agencyA']) . "',
													'A'
													)";	
			$dbh->query($comp_sql);	
			
			/* ------------- Employee Information ------------- */
			$emp_sql = "INSERT INTO Employees (id, request_id, fst, lst, address1, address2, city, state, zipcode, country, phn, ssn, ethnicity, eeo) VALUES 
												(NULL,
												'" . mysql_real_escape_string($_POST['request_id']) . "',											  
												'" . mysql_real_escape_string($_POST['fst']) . "',
												'" . mysql_real_escape_string($_POST['lst']) . "',
												'" . mysql_real_escape_string($_POST['address1']) . "',
												'" . mysql_real_escape_string($_POST['address2']) . "',
												'" . mysql_real_escape_string($_POST['city']) . "',
												'" . mysql_real_escape_string($_POST['state']) . "',
												'" . mysql_real_escape_string($_POST['zipcode']) . "',
												'" . mysql_real_escape_string($_POST['country']) . "',
												'" . mysql_real_escape_string($_POST['phn']) . "',
												'" . mysql_real_escape_string(format_ssn($_POST['ssn'])) . "',
												'" . mysql_real_escape_string($_POST['ethnicity']) . "',
												'" . mysql_real_escape_string($_POST['eeo']) . "'											
												)";	
			$dbh->query($emp_sql);	
		}
		
		/* ----- Execute this area one for intial time ----- */
		if ($_POST['staffing_status'] == 'Submit' OR $_POST['inform'] == 'only') {
			/* Insert Processed Date */
			if ($_POST['staffing_status'] == 'Submit') {	 
				$auth_sql = "UPDATE Authorization SET staffingDate=NOW() WHERE request_id = ".$_POST['request_id'];
				$dbh->query($auth_sql);
			}
				
			/* Submit the technology information to Lotus Notes */
			submitTechnologyToNotes($_POST['request_id']);

			/* Send new employee information to HR Coordinator for direct employees */
			if ($REQUEST['requestType'] == '1') {
				$coordinator=getPosition('coordinator','none');															// Get Coordinator Information
				sendCoordinator($coordinator['email'], $_POST['request_id'], $_POST['fst']." ".$_POST['lst']);			// Send email to Coordinator
				$forward = "list.php?action=my&access=0";
			} else {
				$forward = $_SERVER['PHP_SELF']."?id=".$_POST['request_id']."&coordinator=contract";					// Mark HR Coordinator as "contract" automaticly
			}
			
			/* Send email to Desk Coordinator */
			sendDeskCoordinator($_POST['request_id'], $_POST['plant']);
	
			/* Send email to Desk Technician only for HQ, Chesterfield and ITC */
			if ($REQUEST['plant'] == '9' OR $REQUEST['plant'] == '27' OR $REQUEST['plant'] == '35') {
				sendPhoneTechnician($_POST['request_id'], $_POST['plant']);
			}

			/* Send email to Cellular and Blackberry phone Coordinator */
			if ($TECH['tech_cellular'] == 'yes' OR $TECH['tech_blackberry'] == 'yes') {
				sendCellularCoordinator($_POST['request_id'], $_POST['plant']);
			}
			
			/* Inform Atilio about Blackberry request */
			if ($TECH['tech_blackberry'] == 'yes') {
				sendBlackberryRequest($ID);
			}
							
			/* Update Calendar */
			iCalendar('Actual');
		}
		
		/* Forword user to next page */
		header("Location: ".$forward);
		exit();
	}
	/* -------------------------------------------------------------
	 * ---------- END STAFFING PROCESSING ------------------------ 
	 * -------------------------------------------------------------
	 */
	
	
	/* -------------------------------------------------------------
	 * ---------- START GENERATOR PROCESSING --------------------- 
	 * -------------------------------------------------------------
	 */
	if ($_POST['auth'] == 'generator') {
		/* Register new employee into the Job Tracking System */
		if ($REQUEST['jobTracking'] == 'yes') {
			registerJobTracking($EINFO['eid']);
		}
		
		/* Register new employee into the Purcahse Request System */
		if ($REQUEST['purchaseRequest'] == 'yes') {
			registerPurchaseRequest($EINFO['eid']);
		}
		
		/* Send out New Employee Notices */
		//sendNewEmployee($sendTo,$EINFO['fullname'],$eid,$plant,$department,$bcc);
	
		$forward = "list.php?action=my&access=0";
		header("Location: ".$forward);
		exit();		
	}
	/* -------------------------------------------------------------
	 * ---------- END GENERATOR PROCESSING --------------------- 
	 * -------------------------------------------------------------
	 */	 
}
/* -------------------------------------------------------------				
 * ------------- END APPROVAL PROCESSING -------------------
 * -------------------------------------------------------------
 */	
 
 
/* -------------------------------------------------------------
 * ---------- START APPROVED PROCESSING --------------------- 
 * -------------------------------------------------------------
 */
//if ($_GET['action'] == 'approved' OR $_GET['action'] == 'notapproved') {
	/* Insert Processed Date */				 
/*	$auth_sql = "UPDATE Authorization 
				 SET coordinatorDate=NOW(),
				     approved='$_GET[action]' 
				 WHERE request_id = ".$_GET['request_id'];
	$dbh->query($auth_sql);	

	$forward = "router.php?request_id=".$_GET['request_id']."&approval=coordinator&action=".$_GET['action']."&fullname=".$_GET['fullname'];	
	header("Location: ".$forward);
	exit();		
}*/
/* -------------------------------------------------------------
 * ---------- END APPROVED PROCESSING --------------------- 
 * -------------------------------------------------------------
 */	 
	 
	 
/* -------------------------------------------------------------
 * ---------- START COORDINATOR PROCESSING --------------------- 
 * -------------------------------------------------------------
 */
switch ($_GET['coordinator']) {
	case 'approved':
	case 'contract':
		/* Send an email informing EID generator of new employee */
		$generator=getPosition('generator','none');															// Get Generator Information
		sendGenerateEID($generator['email'], $ID, $EINFO['fullname']); 								// Email EID Generaotor
		
		$staffing=getPosition('staffing','none');															// Get Staffing Information			
		sendCoordinatorStatus($staffing['email'], $ID, $EINFO['fullname'], $_GET['coordinator']);	// Email Staffing Information

		$dbh->query("UPDATE Authorization 
		             SET coordinatorDate=NOW(), 
					     coordinatorCom='$_GET[coordinator]'
					 WHERE request_id=".$ID);
		
		$forward = "list.php?action=my&access=0";
		header("Location: ".$forward);
		exit();	
	break;
	case 'notapproved':
		/* Send an email informing EID generator of new employee */
		$staffing=getPosition('staffing','none');															// Get Coordinator Information
		sendCoordinatorStatus($staffing['email'], $ID, $EINFO['fullname'], $_GET['coordinator']);	// Email Staffing Information
		
		$dbh->query("UPDATE Authorization 
					 SET coordinatorDate=NOW(), 
					 	 coordinatorCom='$_GET[coordinator]'
					 WHERE request_id=".$ID);
		
		$forward = "list.php?action=my&access=0";
		header("Location: ".$forward);
		exit();	
	break;
}	
/* -------------------------------------------------------------
 * ---------- END COORDINATOR PROCESSING --------------------- 
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
$status=labelStatus($REQUEST['status']);					// Get status name

$showContent=showContent($_GET['approval']);				// Set display and color status

$transfer=getEmployee($TECH['tech_transfer']);				// Get transfers fullname
$replacement=getEmployee($REQUEST['replacement']);			// Get replacements fullname

$staffingDate=(isset($AUTH['staffingDate'])) ? datetime : datetime_off;			//
$staffingHelp=(isset($AUTH['staffingDate'])) ? date("F d, Y - g:i:s A", strtotime($AUTH['staffingDate'])) : 'Waiting...';
$coordinatorDate=(isset($AUTH['coordinatorDate'])) ? datetime : datetime_off;	//
$coordinatorHelp=(isset($AUTH['coordinatorDate'])) ? date("F d, Y - g:i:s A", strtotime($AUTH['coordinatorDate'])) : 'Waiting...';
$generatorDate=(isset($AUTH['generatorDate'])) ? datetime : datetime_off;		//	
$generatorHelp=(isset($AUTH['generatorDate'])) ? date("F d, Y - g:i:s A", strtotime($AUTH['generatorDate'])) : 'Waiting...';								  

$interviewTeam=explode(':', $REQUEST['interviewTeam']);
$candidateInt=explode(':', $REQUEST['candidateInt']);
										  
$items=4;													// Display items in a row
$items_counter=0;											// Start items counter
$staffing_status = (count($COMPA) == 0) ? Submit : Update;	// Set staffing data status
$highlight='class="highlight"';								// Highlighted style sheet class
$SALARYRANGE="Min &#36;" . $SALARYGRADE['min'] . 
			 "     Mid &#36;" . $SALARYGRADE['mid'] . 
			 "     Max &#36;" . $SALARYGRADE['max'];		//  Setup hidden salary range data
$app7_status=false;											// Display status of the approver 7 section

$dontDisplay = "style='display:none'";

/* ------------- Add message form for generator ------------- */
if ($_GET['approval'] == 'generator' AND empty($EINFO['eid'])) {
$message = <<< END_OF_HTML
	<form action="../Administration/generateEID.php" method="post" name="Form1" id="Form1" style="margin:0px;">
	   Press to <input name="imageField2" type="image" src="../images/button.php?i=b150.png&l=Generate" alt="Generate Employee ID" border="0" align="absmiddle"> an Employee ID
	   <input name="request_id" type="hidden" id="request_id" value="$_GET[id]">
	</form>
END_OF_HTML;
}

/* ------------- Check current level and current user ------------- */
switch ($REQUEST['status']) {
	case 'C':
	case 'X':
	case 'O': unset($_GET['approval']); break;
	case 'A': if ($_GET['approval'] != 'staffing') { unset($_GET['approval']); } break;	
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
} elseif ($PO['status'] == 'N') {
	$message="<div class=\"appJump\"<img src=\"/Common/images/action.gif\" align=\"absmiddle\" /> This requisition is waiting for action from " . caps($EMPLOYEES[$AUTH[$AUTH['level']]] . "</div>");
}
/* ------------------ ******* END VARIABLES ******* ----------------------- */


//require_once('attachment.php'); 				// Display attachment icon


//$ONLOAD_OPTIONS.="prepareForm()";
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
  <link type="text/css" href="/Common/Javascript/greybox5/gb_styles.css" rel="stylesheet" media="all">
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
	<?php 
//	  if ($_SESSION['hcr_access'] == '3') {
//	    include('../Administration/include/detail.php');
//		include('../Administration/include/sql_debug.php');
//	  } 
	?>    
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td align="center"><a name="top"></a><br>
                    <table><tbody id="waitingRoom" style="display:none"></tbody></table>
						<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon" onSubmit="submitonce(this)">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td>
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
									  <td align="left" valign="bottom">&nbsp;</td>									
                                      <td valign="bottom"><div id="requestStatusContainer" style="float: right">
                                        <div id="requestStatusTitle">Status Indicator</div>
                                        <div id="requestStatus" title="User Task|Click the Requisition Status window to jump to approvals panel">
                                          <?= $status; ?>
                                          <input name="status" type="hidden" id="status" value="<?= $REQUEST['status']; ?>">
                                        </div>
                                      </div></td>
                                    </tr>
                                    <tr class="BGAccentVeryDark">
                                      <td height="30" nowrap class="DarkHeaderSubSubWhite">&nbsp;&nbsp;Open Position...</td>
                                      <td align="right"><span class="DarkHeaderWhite">Number:<strong>&nbsp;HCR-<?= $REQUEST['id']; ?>&nbsp;&nbsp;</strong></span></td>
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
                                                <td><strong>&nbsp;&nbsp;<img src="../images/info.png" width="16" height="16" align="texttop">&nbsp;<?= $language['label']['stage1.1']; ?>...
                                                </strong> </td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </table></td>
                                        </tr>
                                        <tr>
                                          <td nowrap><div class="panelContent">
                                           <table width="100%"  border="0">
                                            <tr>
                                              <td nowrap><?= $language['label']['positionTitle']; ?>:</td>
                                              <td width="2%"></td>
                                              <td><select name="positionTitle" id="positionTitle" <?= $inputStatus['positionTitle']; ?>>
                                                  <option value="0">Select One</option>
                                                  <?php
													  $positionTitle_sth = $dbh->execute($positionTitle_sql);
													  while($positionTitle_sth->fetchInto($POSITION)) {
														$selected = ($REQUEST['positionTitle'] == $POSITION['title_id']) ? selected : $blank;
														print "<option value=\"".$POSITION['title_id']."\" ".$selected.">".ucwords(strtolower($POSITION['title_name']))."</option>\n";
													  }
													?>
                                              </select></td>
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                            </tr>
                                            <tr>
                                              <td>Requester:</td>
                                              <td>&nbsp;</td>
                                              <td class="label">
                                                <?= ucwords(strtolower($EMPLOYEES[$REQUEST['req']])); ?>                                                <?php
										  	/* ----- Display In Care Of value ---- */
										  	if (!empty($REQUEST['incareof'])) {
										  		print " c/o " . ucwords(strtolower($INCAREOF['name']));
											}
                                          ?>                                              </td>
                                              <td>Request Date:</td>
                                              <td width="2%">&nbsp;</td>
                                              <td class="label">                                                <?= $REQUEST['_reqDate']; ?>                                              </td>
                                            </tr>
                                            <tr>
                                              <td height="5" colspan="6"><img src="../images/spacer.gif" width="5" height="5"></td>
                                            </tr>
                                            <tr>
                                              <td><?= $language['label']['plant']; ?>:</td>
                                              <td>&nbsp;</td>
                                              <td><select name="plant" id="plant" <?= $inputStatus['plant']; ?>>
                                                  <option value="0">Select One</option>
                                                  <?php
													  $plant_sth = $dbh->execute($plant_sql);
													  while($plant_sth->fetchInto($PLANT)) {
														$selected = ($REQUEST['plant'] == $PLANT['id']) ? selected : $blank;
														print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
													  }
													?>
                                              </select></td>
                                              <td><?= $language['label']['department']; ?>:</td>
                                              <td>&nbsp;</td>
                                              <td><select name="department" id="department" <?= $inputStatus['department']; ?>>
                                                  <option value="0">Select One</option>
                                                  <?php
											  $dept_sth = $dbh->execute($dept_sql);
											  while($dept_sth->fetchInto($DEPT)) {
												$selected = ($REQUEST['department'] == $DEPT[id]) ? selected : $blank;
												print "<option value=\"".$DEPT[id]."\" ".$selected.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
											  }
											?>
                                              </select></td>
                                            </tr>
                                            <tr>
                                              <td nowrap><?= $language['label']['positionStatus']; ?>:</td>
                                              <td>&nbsp;</td>
                                              <td class="label"><?= caps($REQUEST['positionStatus']); ?></td>
                                              <td><?= $language['label']['replacement']; ?>:</td>
                                              <td></td>
                                              <td class="label"><?= caps($REQUEST['replacement']); ?></td>
                                            </tr>
                                            <tr>
                                              <td><?= $language['label']['positionType']; ?>:</td>
                                              <td>&nbsp;</td>
                                              <td><table border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td><select name="positionType" id="positionType" <?= $inputStatus['positionType']; ?>>
                                                        <option value="0">Select One</option>
                                                        <option value="1" <?= ($REQUEST['positionType'] == '1') ? selected : $blank; ?>>Full-Time</option>
                                                        <option value="2" <?= ($REQUEST['positionType'] == '2') ? selected : $blank; ?>>Part-Time</option>
                                                    </select></td>
                                                  </tr>
                                              </table></td>
                                              <td><?= $language['label']['requestType']; ?>:</td>
                                              <td>&nbsp;</td>
                                              <td><select name="requestType" id="requestType" <?= $inputStatus['requestType']; ?>>
                                                  <option value="0" rel="none">Select One</option>
                                                  <option value="1" rel="_direct" <?= ($REQUEST['requestType'] == '1') ? selected : $blank; ?>>Direct</option>
                                                  <option value="2" rel="_contract" <?= ($REQUEST['requestType'] == '2') ? selected : $blank; ?>>Contract</option>
                                                  <option value="3">Contract or Direct</option>
                                              </select></td>
                                            </tr>
                                            <tr rel="_contract">
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                              <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                                  <?= $language['label']['contractTime']; ?>:</td>
                                              <td>&nbsp;</td>
                                              <td><select name="contractTime" id="contractTime" <?= $inputStatus['contractTime']; ?>>
                                                  <option value="0">Select One</option>
                                                  <option value="3" <?= ($REQUEST['contractTime'] == '3') ? selected : $blank; ?>>3 Months</option>
                                                  <option value="6" <?= ($REQUEST['contractTime'] == '6') ? selected : $blank; ?>>6 Months</option>
                                                  <option value="9" <?= ($REQUEST['contractTime'] == '9') ? selected : $blank; ?>>9 Months</option>
                                              </select></td>
                                            </tr>
                                            <tr>
                                              <td nowrap><?= $language['label']['targetDate']; ?>:</td>
                                              <td nowrap>&nbsp;</td>
                                              <td class="label">                                                <?= $REQUEST['_targetDate']; ?>                                              </td>
                                              <td nowrap><?= $language['label']['actualStart']; ?>:</td>
                                              <td nowrap>&nbsp;</td>
                                              <td class="label">                                                <?= $REQUEST['_startDate']; ?>                                              </td>
                                            </tr>
                                          </table></div></td>
                                        </tr>
                                        

                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td>&nbsp;</td>
                                  </tr>
								  <tr><td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td height="25" class="BGAccentDark">&nbsp;&nbsp;<a href="javascript:void();" class="black"><strong><img src="../images/computer.gif" width="16" height="16" border="0" align="texttop">&nbsp;<?= $language['label']['stage3']; ?>...</strong></a></td>
                                    </tr>
                                    <tr>
                                      <td>
									  <div class="panelContent" id="state3" style="display:<?= $showContent['stage3']; ?>">
									  <table width="100%" border="0">
									  <?php if ($REQUEST['transfer'] == 'yes') { ?>
                                        <tr>
                                          <td width="25%">Transfer Technology from Employee: </td>
                                          <td colspan="2" class="label"><?= caps($REQUEST['transfer_eid']); ?></td>
                                          <td width="15%">&nbsp;</td>
                                        </tr>
                                        <tr>
                                          <td height="20" colspan="4"><hr width="90%" size="1" color="#999966"></td>
                                        </tr>
										<?php } ?>
                                        <tr>
                                          <td>Computer:</td>
                                          <td width="25%" class="label"><?= caps($TECH['tech_computer']); ?></td>
                                          <td width="35%">Desktop Phone:</td>
                                          <td class="label"><?= caps($TECH['tech_phone']); ?></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td width="58%">Cellular Phone:</td>
                                              <td width="42%" class="label"><?= caps($TECH['tech_cellular']); ?></td>
                                            </tr>
                                            <tr <?= ($TECH['tech_cellularTrans'] == 'no') ? $dontDisplay : $blank; ?>>
                                              <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">International Cellular Phone: </td>
                                              <td class="label"><?= caps($TECH['tech_cellularInt']); ?></td>
                                            </tr>
                                            <tr <?= ($TECH['tech_cellularTrans'] == 'no') ? $dontDisplay : $blank; ?>>
                                              <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Transfer Phone Number: </td>
                                              <td class="label"><?= $TECH['tech_cellularTrans']; ?></td>
                                            </tr>
                                          </table></td>
                                          <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td width="87%">Blackberry Device:</td>
                                              <td width="13%" class="label"><?= caps($TECH['tech_blackberry']); ?></td>
                                            </tr>
                                            <tr <?= ($TECH['tech_blackberry'] == 'no') ? $dontDisplay : $blank; ?>>
                                              <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">International Blackberry Phone:</td>
                                              <td class="label"><?= caps($TECH['tech_blackberryInt']); ?></td>
                                            </tr>
                                            <tr <?= ($TECH['tech_blackberry'] == 'no') ? $dontDisplay : $blank; ?>>
                                              <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Transfer Phone Number:</td>
                                              <td class="label"><?= $TECH['tech_blackberryTrans']; ?></td>
                                            </tr>
                                          </table></td>
                                        </tr>
                                        
                                        <tr>
                                          <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                        </tr>
                                        <tr>
                                          <td>Requires Location Access Badge:</td>
                                          <td class="label"><?= caps($TECH['tech_badge']); ?></td>
                                          <td>Requires access from home (VPN):</td>
                                          <td class="label"><?= caps($TECH['tech_vpn']); ?></td>
                                        </tr>
                                        <tr>
                                          <td>Requires a Lotus Notes account:</td>
                                          <td class="label"><?= caps($TECH['tech_notesID']); ?></td>
                                          <td>Required to use the Job Tracking System:</td>
                                          <td class="label"><?= caps($TECH['tech_jobTracking']); ?></td>
                                        </tr>
                                        <tr>
                                          <td>Requires AS/400 access:</td>
                                          <td class="label"><?= caps($TECH['tech_as400']); ?></td>
                                          <td>Requires access to Purchase Request System:</td>
                                          <td class="label"><?= caps($TECH['tech_request']); ?></td>
                                        </tr>
                                        <tr>
                                          <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                        </tr>
                                        <tr>
                                          <td valign="top">Default Software Installed:<br><a href="javascript:();" title="Information|The listed softwares are installed by default on every system" class="TipLabel">
                                          &nbsp;&nbsp;<img src="../images/next_button.gif" width="19" height="19" align="absmiddle" border="0">Microsoft Office Standard<br>
                                          &nbsp;&nbsp;<img src="../images/next_button.gif" width="19" height="19" align="absmiddle" border="0">Lotus Notes</a></td>
                                          <td valign="top" class="label"><?= (empty($TECH['tech_optionalSoftware'])) ? 'No' : 'Yes'; ?></td>
                                          <td valign="top">&nbsp;</td>
                                          <td valign="top">&nbsp;</td>
                                        </tr>
                                        <tr <?= (empty($TECH['tech_optionalSoftware'])) ? $dontDisplay : $blank; ?>>
                                          <td colspan="4" valign="top"><fieldset>
                                            <legend class="BGAccentDarkLegend">Optional Software</legend>
                                              <table width="100%"  border="0">
                                              <?php
											  $software_sth = $dbh->execute($software_sql);
											  while($software_sth->fetchInto($SOFTWARE)) {
												$selected_class="gray";								// Default class
												$selected="disabled";								// Disable checkbox by default		  
												$items_counter++;
												if ($items_counter == 1) { print "<tr>"; }
													$software = explode(":", $TECH['tech_optionalSoftware']);	// Seperate optional software
													while (list($arg, $val) = each($software)) {
														if ( $val == $SOFTWARE['id'] ) {
															$selected="checked";
															$selected_class="dark";
														}
													}
											  ?>
											  <td nowrap><input type="checkbox" name="optionalSoftware[]" id="<?= $SOFTWARE['id']; ?>" value="<?= $SOFTWARE['id']; ?>" <?= $selected; ?> <?= $inputStatus['optionalSoftware']; ?>>
												  <label for="<?= $SOFTWARE['id']; ?>" class="<?= $selected_class; ?>">
													<?= caps($SOFTWARE['name']); ?>
												  </label></td>
											  <?php	
												if ($items_counter == $items) {
												  print "</tr>\n";
												  $items_counter=0;
												}
											  }
											 ?>
                                              </table>
                                          </fieldset></td>
                                        </tr>
                                      </table>
									  </div></td>
                                    </tr>
                                  </table></td></tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								  <tr>
								    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                      <tr>
                                        <td height="25" nowrap class="BGAccentDark">&nbsp;&nbsp;<a href="javascript:void();" class="black"><strong><img src="../images/notes.gif" width="16" height="16" border="0" align="texttop"><?= $language['label']['stage2']; ?>...
                                        </strong></a></td>
                                      </tr>
                                      <tr>
                                        <td>
										<div class="panelContent" id="stage2" style="display:<?= $showContent['stage2']; ?>">
										<table width="100%"  border="0">
                                          <tr>
                                            <td width="200" nowrap><?= $language['label']['attachment']; ?>:</td>
                                            <td nowrap class="label"><?= $Attachment; ?></td>
                                            <?php if ($_SESSION['eid'] == $REQUEST['req'] AND $_SESSION['eid'] != $AUTH['staffing']) { ?>
                                            <td align="right">&nbsp;</td>
                                            <?php } ?>
                                          </tr>
<!--                                          <tr>
                                            <td nowrap>File Cabinet:</td>
                                            <td nowrap><?= $Attachment2; ?></td>
                                            <?php if ($_SESSION['eid'] == $REQUEST['req']) { ?>
                                            <td><input name="file2" type="file" id="file2" size="50"></td>
                                            <?php } ?>
                                          </tr>-->
                                          <tr>
                                            <td height="5" colspan="3" nowrap><img src="../images/spacer.gif" width="5" height="5"></td>
                                          </tr>
										  <?php if ($_GET['approval'] != 'app2') { ?>
                                          <tr>
                                            <td valign="top" nowrap><?= $language['label']['description']; ?>:</td>
                                            <td colspan="2" valign="top" class="label"><textarea name="jobDescription2" cols="90" rows="10" wrap="VIRTUAL" readonly id="description" class="BGAccentDarkBorder"><?= stripslashes($REQUEST['jobDescription']); ?>
                                            </textarea></td>
                                          </tr>
										  <?php } ?>
                                          <tr>
                                            <td valign="top" nowrap><?= $language['label']['primaryJob']; ?>:</td>
                                            <td colspan="2" valign="top" class="label">
                                              <textarea name="primaryJob" cols="90" rows="10" wrap="VIRTUAL" readonly id="primaryJob" class="BGAccentDarkBorder"><?= stripslashes($REQUEST['primaryJob']); ?></textarea></td>
                                          </tr>
                                          <tr>
                                            <td valign="top" nowrap><?= $language['label']['secondaryJob']; ?>:</td>
                                            <td colspan="2" valign="top" class="label"><textarea name="secondaryJob" cols="90" rows="10" wrap="VIRTUAL" readonly id="secondaryJob" class="BGAccentDarkBorder"><?= stripslashes($REQUEST['secondaryJob']); ?>
                                            </textarea></td>
                                          </tr>
                                        </table>
										</div></td>
                                      </tr>
                                    </table></td>
							    </tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								  <tr>
								    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                      <tr>
                                        <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;<img src="../images/money.gif" width="20" height="17" align="texttop"><a href="javascript:void();" class="black"> <?= $language['label']['budgetPosition']; ?>... </a></strong></td>
                                      </tr>
                                      <tr>
                                        <td>
										<div class="panelContent" id="budgetPosition" style="display:<?= $showContent['budgetPosition']; ?>">
									  <table width="100%"  border="0">
                                            <tr>
                                              <td width="200"><?= $language['label']['budgetPosition']; ?>:</td>
                                              <td class="label"><?= caps($REQUEST['budgetPosition']); ?></td>
                                            </tr>
                                            <tr>
                                              <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td><?= $language['label']['justification']; ?>:</td>
                                                  </tr>
                                                </table>
                                                  <br></td>
                                              <td class="label"><textarea name="justification" cols="90" rows="10" wrap="VIRTUAL" readonly="readonly" class="BGAccentDarkBorder" id="justification"><?= stripslashes($REQUEST['justification']); ?>
                                        </textarea></td>
                                            </tr>
                                            <tr>
                                              <td valign="top">&nbsp;</td>
                                              <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                              <td>
                                                  <?= $language['label']['utilize']; ?>: <a href="javascript:void(0);" title="Help|Can this position be satisfied by utilizing staff from the department to assist?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                              <td class="label"><?= caps($REQUEST['utilize']); ?></td>
                                            </tr>
                                            <tr>
                                              <td>
                                                  <?= $language['label']['headCount']; ?>: <a href="javascript:void(0);" title="Help|What is the department\&#39;s approved budgeted head count (excluding this position)?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                              <td class="label"><?= $REQUEST['headCount']; ?></td>
                                            </tr>
                                            <tr>
                                              <td>
                                                  <?= $language['label']['currentHeadCount']; ?>: <a href="javascript:void(0);" title="Help|What is departments\&#39;s current head count (excluding this position)?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                              <td class="label"><?= $REQUEST['currentHeadCount']; ?></td>
                                            </tr>
                                            <tr>
                                              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td nowrap>
                                                        <?= $language['label']['budget']; ?>: <a href="javascript:void(0);" title="Help|What is the amount in department\&#39;s budget for this position?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                                    <td align="right"><strong>$</strong></td>
                                                  </tr>
                                              </table></td>
                                              <td class="label"><?= stripslashes($REQUEST['budget']); ?></td>
                                            </tr>
                                        </table></div></td>
                                      </tr>
                                    </table></td>
							    </tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								<?php if ($_GET['approval'] == 'app2') { ?>
								  <tr>
								    <td class="<?= $showContent['stage1.3']['accent_border']; ?>"><table width="100%" border="0">
                                      <tr>
                                        <td height="25" class="<?= $showContent['stage1.3']['accent']; ?>">&nbsp;&nbsp;<span class="<?= $showContent['actualcompensation']['accent']; ?>"><a name="compensation"></a></span><a href="javascript:void();" class="<?= $showContent['stage1.3']['accent_text']; ?>"><strong><img src="../images/money.gif" width="20" height="17" border="0" align="texttop">&nbsp;<?= $language['label']['stage1.3']; ?>...</strong></a></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>
										<div id="noPrint">
										<div class="panelContent" id="stage1.3" style="display:<?= $showContent['stage1.3']['display']; ?>">
										<table width="100%" border="0">
                                          <tr>
                                            <td nowrap><?= $language['label']['salaryGrade']; ?>:</td>
                                            <td width="100"><strong><?= $SALARYGRADE['grade']; ?></strong></td>
                                            <td nowrap><?= $language['label']['salaryRange']; ?>: </td>
                                            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                <td><img src="../images/button.php?i=spacer400.png&l=M O U S E O V E R" width="400" height="22" id="Image21" onMouseOver="swapImage('Image21','','../images/button.php?i=spacer400.png&l=<?= $SALARYRANGE; ?>',1)" onMouseOut="swapImgRestore()"></td>
                                              </tr>
                                            </table></td>
                                          </tr>
                                          <tr>
											  <td valign="top"><vllabel form="Form" validators="jobDescription" class="valRequired2" errclass="valError">
											    <?= $language['label']['description']; ?>:</vllabel></td>
										    <td colspan="3"><textarea name="jobDescription" cols="90" rows="15" wrap="VIRTUAL" id="jobDescription"></textarea>
										      <vlvalidator name="jobDescription" type="required" control="jobDescription" errmsg="Enter the Job Description."></td> 
                                          </tr>
                                        </table>
										</div>
										</div></td>
                                      </tr>
                                    </table></td>
							    </tr>
								  <tr>
                                    <td>&nbsp;</td>
							    </tr>
								<?php } ?>
								  <tr>
                                    <td class="<?= $showContent['actualcompensation']['accent_border']; ?>"><table width="100%" border="0">
                                        <tr>
                                          <td height="25" class="<?= $showContent['actualcompensation']['accent']; ?>">&nbsp;&nbsp;<a href="javascript:void();" class="<?= $showContent['actualcompensation']['accent_text']; ?>"><strong><img src="../images/money.gif" width="20" height="17" border="0" align="texttop">&nbsp;Actual Compensation Information...</strong></a></td>
                                        </tr>
                                        <tr>
                                          <td><div id="noPrint">
                                            <div class="panelContent" id="actualcompensation" style="display:<?= $showContent['actualcompensation']['display']; ?>">
											<?php if ($_GET['approval'] == 'staffing') { ?>
                                              <table width="100%" border="0">
											  <?php if ($REQUEST['requestType'] == '3') { ?>
                                                <tr>
                                                  <td nowrap><vllabel form="Form" validators="requestTypeA" class="valRequired2" errclass="valError">
                                                    <?= $language['label']['requestType']; ?>:</vllabel></td>
                                                  <td><select name="requestTypeA" id="requestTypeA" <?= $inputStatus['requestTypeA']; ?>>
														<option value="0">Select One</option>
														<option value="1">Direct</option>
														<option value="2">Contract</option>
                                                  </select>
                                                  <vlvalidator name="requestTypeA" type="compare" control="requestTypeA" validtype="string" comparevalue="0" comparecontrol="requestTypeA" operator="ne"></td>
                                                  <td nowrap class="valNone">&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
												<?php } ?>
                                                <tr>
                                                  <td width="150"nowrap><vllabel form="Form" validators="salaryTypeA" class="valRequired2" errclass="valError"><?= $language['label']['salaryGrade']; ?>:</vllabel></td>
                                                  <td width="150"><select name="salaryGradeA" id="salaryGradeA" <?= $inputStatus['salaryGradeA']; ?>>
                                                    <option value="0">Select One</option>
                                                    <?php
														  $salaryGrade_sth = $dbh->execute($salaryGrade_sql);
														  while($salaryGrade_sth->fetchInto($SALARYGRADE2)) {
															$selected = ($COMPA['salaryGrade'] == $SALARYGRADE2['grade']) ? selected : $blank;
															print "<option value=\"".$SALARYGRADE2['grade']."\" ".$selected.">".$SALARYGRADE2['grade']."</option>\n";
														  }
														?>
                                                  </select>
                                                  <vlvalidator name="salaryGradeA" type="compare" control="salaryGradeA" validtype="string" comparevalue="0" comparecontrol="salaryGradeA" operator="ne"></td>
                                                  <td width="150" nowrap class="valNone"><?= $language['label']['salaryRange']; ?>:</td>
                                                  <td><img src="../images/button.php?i=spacer400.png&l=M O U S E O V E R" width="400" height="22" id="Image211" onMouseOver="swapImage('Image211','','../images/button.php?i=spacer400.png&amp;l=<?= $SALARYRANGE; ?>',1)" onMouseOut="swapImgRestore()"></td>
                                                </tr>
                                                <tr>
                                                  <td nowrap><vllabel form="Form" validators="salaryGradeA" class="valRequired2" errclass="valError">
                                                    <?= $language['label']['salaryType']; ?>:</vllabel></td>
                                                  <td><select name="salaryTypeA" id="salaryTypeA">
                                                    <option value="0">Select One</option>
                                                    <option value="salary" <?= ($COMPA['salaryType'] == 'salary') ? selected : $blank; ?>>Salary</option>
                                                    <option value="hourly" <?= ($COMPA['salaryType'] == 'hourly') ? selected : $blank; ?>>Hourly</option>
                                                  </select>
                                                    <vlvalidator name="salaryTypeA" type="compare" control="salaryTypeA" validtype="string" comparevalue="0" comparecontrol="salaryTypeA" operator="ne"></td>
                                                  <td nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td>
														<vllabel form="Form" validators="salaryA" class="valRequired2" errclass="valError">
														<?= ($REQUEST['requestType'] == '1') ? $language['label']['salary'] : $language['label']['billrate'] ; ?>:</vllabel></td>
                                                        <td align="right">$</td>
                                                      </tr>
                                                  </table></td>
                                                  <td>
                                                  <input name="salaryA" type="text" id="salaryA" size="10" maxlength="10" value="<?= base64_decode($COMPA['salary']); ?>" autocomplete="off" <?= $inputStatus['salaryA']; ?>>
                                                      <vlvalidator name="salaryA" type="required" control="salaryA">&nbsp;OT$
                                                  <input name="overTimeA" type="text" id="overTimeA" size="10" maxlength="10" value="<?= base64_decode($COMPA['overTime']); ?>" autocomplete="off" <?= $inputStatus['overTimeA']; ?>>&nbsp;DT$
                                                  <input name="doubleTimeA" type="text" id="doubleTimeA" size="10" maxlength="10" auto value="<?= base64_decode($COMPA['doubleTime']); ?>" autocomplete="off" <?= $inputStatus['doubleTimeA']; ?>></td>
                                                </tr>
                                                <tr>
                                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td><vllabel form="Form" validators="vehicleAllowanceA" class="valRequired2" errclass="valError"><?= $language['label']['vehicleAllowance']; ?>:</vllabel></td>
                                                        <td align="right">$</td>
                                                      </tr>
                                                  </table></td>
                                                  <td><input name="vehicleAllowanceA" type="text" id="vehicleAllowanceA" size="10" maxlength="10" value="<?= base64_decode($COMPA['vehicleAllowance']); ?>" autocomplete="off" <?= $inputStatus['vehicleAllowanceA']; ?>>
                                                  <vlvalidator name="vehicleAllowanceA" type="required" control="vehicleAllowanceA"></td>
                                                  <td><vllabel form="Form" validators="agencyA" class="valRequired2" errclass="valError"><?= $language['label']['agency']; ?>:</vllabel></td>
                                                  <td><select name="agencyA" id="agencyA" <?= $inputStatus['agencyA']; ?>>
                                                      <option value="0">Select One</option>
                                                      <?php
														  $agency_sth = $dbh->execute($agency_sql);
														  while($agency_sth->fetchInto($AGENCY)) {
															$selected = ($COMPA['agency'] == $AGENCY[id]) ? selected : $blank;
															print "<option value=\"".$AGENCY[id]."\" ".$selected.">".$AGENCY[name]."</option>\n";
														  }
														?>
                                                    </select>
                                                    <a href="../Administration/db/contractAgency.php?view=basic" onClick="return GB_showFullScreen(this.title, this.href)" title="Administrative Task|Click here to edit Contract Agency."><img src="/Common/images/menuedit.gif" width="16" height="16" border="0" align="absmiddle"></a>
                                                    <vlvalidator name="agencyA" type="compare" control="agencyA" validtype="string" comparevalue="0" comparecontrol="agencyA" operator="ne"></td>
                                                </tr>
                                                <tr>
                                                  <td><vllabel form="Form" validators="vacationDaysA" class="valRequired2" errclass="valError">
                                                      <?= $language['label']['vacationDays']; ?>:</vllabel></td>
                                                  <td><input name="vacationDaysA" type="text" id="vacationDaysA" size="10" maxlength="10" value="<?= $COMPA['vacationDays']; ?>" <?= $inputStatus['vacationDays']; ?>>
                                                      <vlvalidator name="vacationDaysA" type="required" control="vacationDaysA"></td>												  
                                                  <td class="valNone"><?= $language['label']['relocation']; ?>: </td>
                                                  <td><select name="relocationA" id="relocationA" <?= $inputStatus['relocationA']; ?>>
                                                      <option value="0">0</option>
                                                      <option value="1" <?= ($COMPA['relocation'] == '1') ? selected : $blank; ?>>1</option>
                                                      <option value="2" <?= ($COMPA['relocation'] == '2') ? selected : $blank; ?>>2</option>
                                                      <option value="3" <?= ($COMPA['relocation'] == '3') ? selected : $blank; ?>>3</option>
                                                      <option value="4" <?= ($COMPA['relocation'] == '4') ? selected : $blank; ?>>4</option>
                                                  </select></td>												  
                                                </tr>
                                                <tr>												
                                                  <td><vllabel form="Form" validators="incentiveA" class="valRequired2" errclass="valError"><?= $language['label']['compensation']; ?>:</vllabel></td>	
                                                  <td><table border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td><input name="incentiveA" type="text" id="incentiveA" size="10" maxlength="10" value="<?= base64_decode($COMPA['incentive']); ?>" autocomplete="off" <?= $inputStatus['incentiveA']; ?>></td>
                                                        <td align="right">%</td>
                                                      </tr>
                                                    </table>
                                                      <vlvalidator name="incentiveA" type="required" control="incentiveA"></td>												  
                                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td><vllabel form="Form" validators="signingBonusA" class="valRequired2" errclass="valError">
                                                          <?= $language['label']['signingBonus']; ?>:</vllabel></td>
                                                        <td align="right">$</td>
                                                      </tr>
                                                  </table></td>
                                                  <td><input name="signingBonusA" type="text" id="signingBonusA" size="10" maxlength="10" value="<?= base64_decode($COMPA['signingBonus']); ?>" autocomplete="off" <?= $inputStatus['signingBonus']; ?>>
                                                      <vlvalidator name="signingBonusA" type="required" control="signingBonusA"></td>													
                                                </tr>
                                              </table>
											  <?php } else { ?>
                                                <table width="100%" border="0">
                                                <tr>
                                                  <td width="150" nowrap><?= $language['label']['salaryGrade']; ?>:</td>
                                                  <td width="150" class="label"><?= $SALARYGRADE['grade']; ?></td>
                                                  <td width="150" nowrap><?= $language['label']['salaryRange']; ?>:</td>
                                                  <td><img src="../images/button.php?i=spacer400.png&l=M O U S E O V E R" width="400" height="22" id="Image212" onMouseOver="swapImage('Image212','','../images/button.php?i=spacer400.png&amp;l=<?= $SALARYRANGE; ?>',1)" onMouseOut="swapImgRestore()"></td>
                                                </tr>
                                                <tr>
                                                  <td nowrap><?= $language['label']['salaryType']; ?>:</td>
                                                  <td class="label"><?= ucwords(strtolower($COMPA['salaryType'])); ?></td>
                                                  <td nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td>
                                                          <?= $language['label']['salary']; ?>:</td>
                                                        <td align="right">$</td>
                                                      </tr>
                                                  </table></td>
                                                  <td><img src="../images/button.php?i=spacer100.png&l=MOUSEOVER" name="Image221" width="100" height="22" id="Image221" onMouseOver="swapImage('Image221','','../images/button.php?i=spacer100.png&amp;l=<?= number_format(base64_decode($COMPA['salary']),2); ?>',1)" onMouseOut="swapImgRestore()">&nbsp;OT$ <img src="../images/button.php?i=spacer100.png&l=MOUSEOVER" name="Image311" width="100" height="22" id="Image311" onMouseOver="swapImage('Image311','','../images/button.php?i=spacer100.png&amp;l=<?= number_format(base64_decode($COMPA['overTime']),2); ?>',1)" onMouseOut="swapImgRestore()">&nbsp;DT$ <img src="../images/button.php?i=spacer100.png&l=MOUSEOVER" name="Image411" width="100" height="22" id="Image411" onMouseOver="swapImage('Image411','','../images/button.php?i=spacer100.png&amp;l=<?= number_format(base64_decode($COMPA['doubleTime']),2); ?>',1)" onMouseOut="swapImgRestore()">                                                  </td>
                                                </tr>
                                                <tr>											
                                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td>
                                                            <?= $language['label']['vehicleAllowance']; ?>:</td>
                                                        <td align="right">$</td>
                                                      </tr>
                                                  </table></td>
                                                  <td class="label"><?= number_format($COMPA['vehicleAllowance'],2); ?></td>											  
                                                  <td><?= $language['label']['agency']; ?>:</td>
                                                  <td class="label"><?= ucwords(strtolower($AGENCY[$COMPA[agency]])); ?></td>
                                                </tr>
                                                <tr>
                                                  <td><?= $language['label']['vacationDays']; ?>:</td>
                                                  <td class="label"><?= $COMPA['vacationDays']; ?></td>
                                                  <td><?= $language['label']['relocation']; ?>: </td>
                                                  <td class="label"><?= $COMPA['relocation']; ?></td>											  
                                                </tr>
                                                <tr>
                                                  <td><?= $language['label']['compensation']; ?>:</td>
                                                  <td class="label"><table border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td><?= base64_decode($COMPA['incentive']); ?></td>
                                                        <td align="right">%</td>
                                                      </tr>
                                                  </table></td>												
                                                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td><?= $language['label']['signingBonus']; ?>:</td>
                                                        <td align="right">$</td>
                                                      </tr>
                                                  </table></td>
                                                  <td class="label"><?= number_format(base64_decode($COMPA['signingBonus']),2); ?></td>													
                                                </tr>
                                              </table>
											  <?php } ?>
                                            </div>
                                          </div></td>
                                        </tr>
                                    </table></td>
							    </tr>
								  <tr>
                                    <td>&nbsp;</td>
							    </tr>
								  <tr>
                                    <td class="<?= $showContent['employeeinformation']['accent_border']; ?>"><table width="100%" border="0">
                                        <tr>
                                          <td height="25" class="<?= $showContent['employeeinformation']['accent']; ?>">&nbsp;&nbsp;<a href="javascript:void();" class="<?= $showContent['employeeinformation']['accent_text']; ?>"><strong><img src="../images/personal.gif" width="16" height="16" border="0" align="texttop">&nbsp;Employee Information...</strong></a></td>
                                        </tr>
                                        <tr>
                                          <td nowrap><div id="noPrint">
                                            <div class="panelContent"  id="employeeinformation" style="display:<?= $showContent['employeeinformation']['display']; ?>">
											<?php if ($_GET['approval'] == 'staffing') { ?>
                                            <table width="100%" border="0">
                                                  <tr>
                                                    <td nowrap><vllabel form="Form" validators="startDate" class="valRequired2" errclass="valError">
                                                      <?= $language['label']['actualStart']; ?>:</vllabel></td>
                                                    <td><input name="startDate" type="text" id="startDate" value="<?= $REQUEST['startDate']; ?>" size="10" maxlength="10" class="popupcalendar">
                                                      <vlvalidator name="startDate" type="required" control="startDate">
                                                      </a></td>
                                                    <td nowrap>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                  </tr>
												  <tr>
                                                    <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                                  </tr>												  
                                                  <tr>
                                                    <td class="valNone" nowrap><?= $language['label']['firstname']; ?>:</td>
                                                    <td><input name="fst" type="text" id="fst" size="25" maxlength="25" value="<?= $EINFO['fst']; ?>" <?= $inputStatus['fst']; ?>></td>
                                                    <td class="valNone" nowrap><?= $language['label']['lastname']; ?>:</td>
                                                    <td><input name="lst" type="text" id="lst" size="50" maxlength="50" value="<?= $EINFO['lst']; ?>" <?= $inputStatus['lst']; ?>></td>
                                                  </tr>
                                                  <tr>
                                                    <td class="valNone"><?= $language['label']['address1']; ?>:</td>
                                                    <td><input name="address1" type="text" id="address1" size="50" maxlength="50" value="<?= $EINFO['address1']; ?>" <?= $inputStatus['address1']; ?>></td>
                                                    <td class="valNone"><?= $language['label']['address2']; ?>: </td>
                                                    <td><input name="address2" type="text" id="address2" size="50" maxlength="50" value="<?= $EINFO['address2']; ?>" <?= $inputStatus['address2']; ?>></td>
                                                  </tr>
                                                  <tr>
                                                    <td class="valNone"><?= $language['label']['city']; ?>:</td>
                                                    <td><input name="city" type="text" id="city" size="25" maxlength="25" value="<?= $EINFO['city']; ?>" <?= $inputStatus['city']; ?>></td>
                                                    <td class="valNone"><?= $language['label']['state']; ?>:</td>
                                                    <td><select name="state" id="state" <?= $inputStatus['state']; ?>>
                                                        <option value="0">Choose a State
                                                      <option value="AL" <?= ($EINFO['state'] == "AL") ? selected : $blank; ?>>Alabama
                                                      <option value="AK" <?= ($EINFO['state'] == "AK") ? selected : $blank; ?>>Alaska
                                                      <option value="AB" <?= ($EINFO['state'] == "AB") ? selected : $blank; ?>>Alberta
                                                      <option value="AZ" <?= ($EINFO['state'] == "AZ") ? selected : $blank; ?>>Arizona
                                                      <option value="AR" <?= ($EINFO['state'] == "AR") ? selected : $blank; ?>>Arkansas
                                                      <option value="BC" <?= ($EINFO['state'] == "BC") ? selected : $blank; ?>>British Columbia
                                                      <option value="CA" <?= ($EINFO['state'] == "CA") ? selected : $blank; ?>>California
                                                      <option value="CO" <?= ($EINFO['state'] == "CO") ? selected : $blank; ?>>Colorado
                                                      <option value="CT" <?= ($EINFO['state'] == "CT") ? selected : $blank; ?>>Connecticut
                                                      <option value="DE" <?= ($EINFO['state'] == "DE") ? selected : $blank; ?>>Delaware
                                                      <option value="DC" <?= ($EINFO['state'] == "DC") ? selected : $blank; ?>>District Of Columbia
                                                      <option value="FL" <?= ($EINFO['state'] == "FL") ? selected : $blank; ?>>Florida
                                                      <option value="GA" <?= ($EINFO['state'] == "GA") ? selected : $blank; ?>>Georgia
                                                      <option value="HI" <?= ($EINFO['state'] == "HI") ? selected : $blank; ?>>Hawaii
                                                      <option value="ID" <?= ($EINFO['state'] == "ID") ? selected : $blank; ?>>Idaho
                                                      <option value="IL" <?= ($EINFO['state'] == "IL") ? selected : $blank; ?>>Illinois
                                                      <option value="IN" <?= ($EINFO['state'] == "IN") ? selected : $blank; ?>>Indiana
                                                      <option value="IA" <?= ($EINFO['state'] == "IA") ? selected : $blank; ?>>Iowa
                                                      <option value="KS" <?= ($EINFO['state'] == "KS") ? selected : $blank; ?>>Kansas
                                                      <option value="KY" <?= ($EINFO['state'] == "KY") ? selected : $blank; ?>>Kentucky
                                                      <option value="LA" <?= ($EINFO['state'] == "LA") ? selected : $blank; ?>>Louisiana
                                                      <option value="ME" <?= ($EINFO['state'] == "ME") ? selected : $blank; ?>>Maine
                                                      <option value="MB" <?= ($EINFO['state'] == "MB") ? selected : $blank; ?>>Manitoba
                                                      <option value="MD" <?= ($EINFO['state'] == "MD") ? selected : $blank; ?>>Maryland
                                                      <option value="MA" <?= ($EINFO['state'] == "MA") ? selected : $blank; ?>>Massachusetts
                                                      <option value="MI" <?= ($EINFO['state'] == "MI") ? selected : $blank; ?>>Michigan
                                                      <option value="MN" <?= ($EINFO['state'] == "MN") ? selected : $blank; ?>>Minnesota
                                                      <option value="MS" <?= ($EINFO['state'] == "MS") ? selected : $blank; ?>>Mississippi
                                                      <option value="MO" <?= ($EINFO['state'] == "MO") ? selected : $blank; ?>>Missouri
                                                      <option value="MT" <?= ($EINFO['state'] == "MT") ? selected : $blank; ?>>Montana
                                                      <option value="NE" <?= ($EINFO['state'] == "NE") ? selected : $blank; ?>>Nebraska
                                                      <option value="NV" <?= ($EINFO['state'] == "NV") ? selected : $blank; ?>>Nevada
                                                      <option value="NB" <?= ($EINFO['state'] == "NB") ? selected : $blank; ?>>New Brunswick
                                                      <option value="NH" <?= ($EINFO['state'] == "NH") ? selected : $blank; ?>>New Hampshire
                                                      <option value="NJ" <?= ($EINFO['state'] == "NJ") ? selected : $blank; ?>>New Jersey
                                                      <option value="NY" <?= ($EINFO['state'] == "NY") ? selected : $blank; ?>>New York
                                                      <option value="NF" <?= ($EINFO['state'] == "NF") ? selected : $blank; ?>>Newfoundland
                                                      <option value="NC" <?= ($EINFO['state'] == "NC") ? selected : $blank; ?>>North Carolina
                                                      <option value="ND" <?= ($EINFO['state'] == "ND") ? selected : $blank; ?>>North Dakota
                                                      <option value="NT" <?= ($EINFO['state'] == "NT") ? selected : $blank; ?>>Northwest Territories
                                                      <option value="NS" <?= ($EINFO['state'] == "NS") ? selected : $blank; ?>>Nova Scotia
                                                      <option value="OH" <?= ($EINFO['state'] == "OH") ? selected : $blank; ?>>Ohio
                                                      <option value="OK" <?= ($EINFO['state'] == "OK") ? selected : $blank; ?>>Oklahoma
                                                      <option value="ON" <?= ($EINFO['state'] == "ON") ? selected : $blank; ?>>Ontario
                                                      <option value="OR" <?= ($EINFO['state'] == "OR") ? selected : $blank; ?>>Oregon
                                                      <option value="PA" <?= ($EINFO['state'] == "PA") ? selected : $blank; ?>>Pennsylvania
                                                      <option value="OR" <?= ($EINFO['state'] == "OR") ? selected : $blank; ?>>Oregon
                                                      <option value="PE" <?= ($EINFO['state'] == "PE") ? selected : $blank; ?>>Prince Edward Island
                                                      <option value="PQ" <?= ($EINFO['state'] == "PQ") ? selected : $blank; ?>>Province du Quebec
                                                      <option value="RI" <?= ($EINFO['state'] == "RI") ? selected : $blank; ?>>Rhode Island
                                                      <option value="SK" <?= ($EINFO['state'] == "SK") ? selected : $blank; ?>>Saskatchewan
                                                      <option value="SC" <?= ($EINFO['state'] == "SC") ? selected : $blank; ?>>South Carolina
                                                      <option value="SD" <?= ($EINFO['state'] == "SD") ? selected : $blank; ?>>South Dakota
                                                      <option value="TN" <?= ($EINFO['state'] == "TN") ? selected : $blank; ?>>Tennessee
                                                      <option value="TX" <?= ($EINFO['state'] == "TX") ? selected : $blank; ?>>Texas
                                                      <option value="UT" <?= ($EINFO['state'] == "UT") ? selected : $blank; ?>>Utah
                                                      <option value="VT" <?= ($EINFO['state'] == "VT") ? selected : $blank; ?>>Vermont
                                                      <option value="VA" <?= ($EINFO['state'] == "VA") ? selected : $blank; ?>>Virginia
                                                      <option value="WA" <?= ($EINFO['state'] == "WA") ? selected : $blank; ?>>Washington
                                                      <option value="WV" <?= ($EINFO['state'] == "WV") ? selected : $blank; ?>>West Virginia
                                                      <option value="WI" <?= ($EINFO['state'] == "WI") ? selected : $blank; ?>>Wisconsin
                                                      <option value="WY" <?= ($EINFO['state'] == "WY") ? selected : $blank; ?>>Wyoming
                                                      <option value="YT" <?= ($EINFO['state'] == "YT") ? selected : $blank; ?>>Yukon Territory
                                                      </select>                                                     </td>
                                                  </tr>
                                                  <tr>
                                                    <td class="valNone"><?= $language['label']['zip']; ?>:</td>
                                                    <td><input name="zipcode" type="text" id="zipcode" size="10" maxlength="10" value="<?= $EINFO['zipcode']; ?>" <?= $inputStatus['zipcode']; ?>></td>
                                                    <td class="valNone"><?= $language['label']['country']; ?>:</td>
                                                    <td><select name="country" id="country" <?= $inputStatus['country']; ?>>
                                                        <option value="US" <?= ($EINFO['country'] == "US") ? selected : $blank; ?>>United States</option>
                                                        <option value="CA" <?= ($EINFO['country'] == "CA") ? selected : $blank; ?>>Canada</option>
                                                    </select></td>
                                                  </tr>
                                                  <tr>
                                                    <td class="valNone"><?= $language['label']['phone1']; ?>:</td>
                                                    <td><input name="phn" type="text" id="phn" size="13" maxlength="13" value="<?= $EINFO['phn']; ?>" <?= $inputStatus['phn']; ?>></td>
                                                    <td class="valNone"><?= $language['label']['ssn']; ?>:</td>
                                                    <td><input name="ssn" type="text" id="ssn" size="11" maxlength="11" autocomplete="off" value="<?= base64_decode($EINFO['ssn']); ?>"></td>
                                                  </tr>
                                                  <tr>
                                                    <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                                  </tr>
                                                  <tr>
                                                    <td><vllabel form="Form" validators="eeo" class="valRequired2" errclass="valError">
                                                      <?= $language['label']['eeo']; ?>:</vllabel></td>
                                                    <td><select name="eeo" id="eeo" <?= $inputStatus['eeo']; ?>>
                                                      <option value="0">Select One</option>
                                                      <?php
														  $eeo_sth = $dbh->execute($eeo_query);
														  while($eeo_sth->fetchInto($EEO_DATA)) {
															$selected = ($EINFO['eeo'] == $EEO_DATA['id']) ? selected : $blank;
															print "<option value=\"".$EEO_DATA['id']."\" ".$selected.">(".$EEO_DATA['id'].") ".$EEO_DATA['name']."</option>\n";
														  }
													  ?>
                                                    </select>
                                                    <vlvalidator name="eeo" type="compare" control="eeo" validtype="string" comparevalue="0" comparecontrol="eeo" operator="ne"></td>
                                                    <td><vllabel form="Form" validators="ethnicity" class="valRequired2" errclass="valError">
                                                      <?= $language['label']['ethnicity']; ?>:</vllabel></td>
                                                    <td><select name="ethnicity" id="ethnicity" <?= $inputStatus['ethnicity']; ?>>
                                                      <option value="0">Select One</option>
                                                      <?php
														  $eth_sth = $dbh->execute($ethnicity_query);
														  while($eth_sth->fetchInto($ETHNICITY_DATA)) {
															$selected = ($EINFO['ethnicity'] == $ETHNICITY_DATA['id']) ? selected : $blank;
															print "<option value=\"".$ETHNICITY_DATA['id']."\" ".$selected.">(".$ETHNICITY_DATA['id'].") ".$ETHNICITY_DATA['name']."</option>\n";
														  }
													  ?>
                                                    </select>
                                                    <vlvalidator name="ethnicity" type="compare" control="ethnicity" validtype="string" comparevalue="0" comparecontrol="ethnicity" operator="ne"></td>
                                                  </tr>
                                                  <tr>
												  	<?php if ($REQUEST['requestType'] == '1') { ?>
                                                    <td class="valNone"><?= $language['label']['screening']; ?>:</td>
                                                    <td class="label"><?= ucwords($AUTH['coordinatorCom']); ?></td>
													<?php } else { ?>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>													
													<?php } ?>
                                                  </tr>
                                                  <tr>
                                                    <td class="valNone"><?= $language['label']['eid']; ?>: </td>
                                                    <td class="label"><?= $EINFO['eid']; ?></td>
                                                    <td class="valNone"><?= $language['label']['desklocation']; ?>:</td>
                                                    <td class="label"><?= $EINFO['desk']; ?></td>
                                                  </tr>
												  <?php if ($_GET['approval'] == 'staffing') { ?>							  
                                                  <tr class="highlight">
                                                    <td height="40" colspan="4" align="center" valign="middle"><input name="submit" type="image" src="../images/button.php?i=b150.png&l=<?= $staffing_status; ?> Data" alt="<?= $staffing_status; ?> Staffing Data" border="0">
                                                      <table border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                          <td><vlsummary form="Form" class="valErrorList" headertext="Please correct the errors marked in red and listed below:" displaymode="bulletlist" showsummary="true" messagebox="false"></td>
                                                        </tr>
                                                      </table></td>
                                                  </tr>
												  <?php } ?>
											  </table>
										      <?php } else { ?>
                                              .<?= $_GET['approval']; ?>.
                                                <table width="100%" border="0">
                                                  <tr>
                                                    <td nowrap><?= $language['label']['firstname']; ?>:</td>
                                                    <td class="label"><?= ucwords(strtolower($EINFO['fst'])); ?></td>
                                                    <td nowrap><?= $language['label']['lastname']; ?>: </td>
                                                    <td class="label"><?= ucwords(strtolower($EINFO['lst'])); ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td><?= $language['label']['address1']; ?>: </td>
                                                    <td class="label"><?= ucwords(strtolower($EINFO['address1'])); ?></td>
                                                    <td><?= $language['label']['address2']; ?>: </td>
                                                    <td class="label"><?= ucwords(strtolower($EINFO['address2'])); ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td><?= $language['label']['city']; ?>: </td>
                                                    <td class="label"><?= ucwords(strtolower($EINFO['city'])); ?></td>
                                                    <td><?= $language['label']['state']; ?>: </td>
                                                    <td class="label"><?= strtoupper($EINFO['state']); ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td><?= $language['label']['zip']; ?>: </td>
                                                    <td class="label"><?= $EINFO['zipcode']; ?></td>
                                                    <td><?= $language['label']['country']; ?>:</td>
                                                    <td class="label"><?= strtoupper($EINFO['country']); ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td><?= $language['label']['phone1']; ?>: </td>
                                                    <td class="label"><?= $EINFO['phn']; ?></td>
													<?php if ($_SESSION['hcr_groups'] == 'hr') { ?>
                                                    <td><?= $language['label']['ssn']; ?>:</td>
                                                    <td><img src="../images/button.php?i=inputField.png&l=Mouseover" width="146" height="22" id="Image111" onMouseOver="swapImage('Image111','','../images/button.php?i=inputField.png&amp;l=<?= base64_decode($EINFO['ssn']); ?>',1)" onMouseOut="swapImgRestore()">
                                                      <?php } else { ?>
														<td>&nbsp;</td>
														<td>&nbsp;</td>	                                                      
                                                      <?php } ?>
                                                  </tr>
                                                  <tr>
                                                    <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                                  </tr>
                                                  <tr>
                                                    <td><?= $language['label']['eeo']; ?>:</td>
                                                    <td class="label"><?= "(".$EINFO['eeo'].")".$EEO[$EINFO[eeo]]; ?></td>
                                                    <td><?= $language['label']['ethnicity']; ?>:</td>
                                                    <td class="label"><?= "(".$EINFO['ethnicity'].")".$ETHNICITY[$EINFO[ethnicity]]; ?></td>
                                                  </tr>
                                                  <tr>
												  <?php if ($REQUEST['requestType'] == '1') { ?>
                                                    <td><?= $language['label']['screening']; ?>:</td>
                                                    <td class="label"><?= ucwords($AUTH['coordinatorCom']); ?></td>
												  <?php } else { ?>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>												  
												  <?php } ?>
                                                  </tr>
                                                  <tr>
                                                    <td><?= $language['label']['eid']; ?>: </td>
                                                    <td class="label"><?= $EINFO['eid']; ?></td>
                                                    <td><?= $language['label']['desklocation']; ?>:</td>
                                                    <td class="label"><?= $EINFO['desk']; ?></td>
                                                  </tr>
                                              </table>												
											  <?php } ?>
                                            </div>
                                          </div></td>
                                        </tr>
                                    </table></td>
							      </tr>
								  <tr>
									<td>&nbsp;</td>
								  </tr>
								  <tr>
                                    <td class="<?= $showContent['stage1.4']['accent_border']; ?>">
									<?php if ($_GET['approval'] == 'app2') { ?>
									<table width="100%"  border="0">
                                        <tr>
                                          <td height="25" class="<?= $showContent['stage1.4']['accent']; ?>">&nbsp;&nbsp;<a href="javascript:void();" class="<?= $showContent['stage1.4']['accent_text']; ?>"><strong><img src="../images/team.gif" width="16" height="18" border="0" align="texttop">&nbsp;
                                          <?= $language['label']['stage1.4']; ?>...</strong></a></td>
                                        </tr>
                                        <tr>
                                          <td><div class="panelContent"  id="stage1.4" style="display:<?= $showContent['stage1.4']['display']; ?>">
                                              <table width="100%" border="0">
                                                <tr>
                                                  <td rowspan="10" valign="top"><table width="100%" border="0">
                                                      <tr>
                                                        <td width="225"><vllabel form="Form" validators="recruitment" class="valRequired2" errclass="valError">Post Recruitment:</vllabel></td>
                                                        <td><select name="recruitment" id="recruitment" <?= $inputStatus['recruitment']; ?>>
                                                            <option value="0" rel="none">Select One</option>
                                                            <option value="internally" rel="_internally" <?= ($REQUEST['recruitment'] == 'internally') ? selected : $blank; ?>>Internally</option>
                                                            <option value="externally" rel="_externally" <?= ($REQUEST['recruitment'] == 'externally') ? selected : $blank; ?>>Externally</option>
                                                            <option value="both" rel="_both" <?= ($REQUEST['recruitment'] == 'both') ? selected : $blank; ?>>Internally and Externally</option>
                                                            <option value="confidential" rel="none" <?= ($REQUEST['recruitment'] == 'confidential') ? selected : $blank; ?>>Confidential</option>
                                                        </select>
                                                        <vlvalidator name="recruitment" type="compare" control="recruitment" errmsg="Select how to display recruitment." validtype="string" comparevalue="0" comparecontrol="recruitment" operator="ne"></td>
                                                      </tr>
                                                      <tr rel="_internally">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt1" name="candidateInt1" type="text" size="40" value="<?= $candidateInt[0]; ?>" <?= $inputStatus['candidateInt1']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt1", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_internally">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt2" name="candidateInt2" type="text" size="40" value="<?= $candidateInt[1]; ?>" <?= $inputStatus['candidateInt2']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt2", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_internally">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt3" name="candidateInt3" type="text" size="40" value="<?= $candidateInt[2]; ?>" <?= $inputStatus['candidateInt3']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt3", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_externally">
                                                        <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">External Candidate:</td>
                                                        <td><input name="candidateExt" type="text" id="candidateExt" size="40" maxlength="50" value="<?= stripslashes($REQUEST['candidateExt']); ?>" <?= $inputStatus['candidateExt']; ?>></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt1" name="candidateInt1" type="text" size="40" value="<?= $candidateInt[3]; ?>" <?= $inputStatus['candidateInt1']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt1", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt2" name="candidateInt2" type="text" size="40" value="<?= $candidateInt[4]; ?>" <?= $inputStatus['candidateInt2']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt2", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt3" name="candidateInt3" type="text" size="40" value="<?= $candidateInt[5]; ?>" <?= $inputStatus['candidateInt3']; ?> />
                                                            <script type="text/javascript">
//															Event.observe(window, "load", function() {
//																var aa = new AutoAssist("candidateInt3", function() {
//																	return "../Common/employees.php?q=" + this.txtBox.value;
//																});
//															});
															</script></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">External Candidate:</td>
                                                        <td><input name="candidateExt" type="text" id="candidateExt" size="40" maxlength="50" value="<?= stripslashes($REQUEST['candidateExt']); ?>" <?= $inputStatus['candidateExt']; ?>></td>
                                                      </tr>
                                                  </table></td>
                                                  <td><span><vllabel form="Form" validators="interviewLeader" class="valRequired2" errclass="valError">Interview Team Leader:</vllabel></span></td>
                                                  <td><input id="interviewLeader" name="interviewLeader" type="text" size="40" value="<?= $REQUEST['interviewLeader']; ?>" <?= $inputStatus['interviewLeader']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewLeader", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script>
                                                      <vlvalidator name="interviewLeader" type="required" control="interviewLeader" errmsg="Enter a Interview Leader"></td>
                                                </tr>
                                                <tr rel="_direct">
                                                  <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Human Resources Member:</td>
                                                  <td><select name="interviewHR" id="interviewHR">
                                                      <option value="0">Select One</option>
                                                      <?php
														  $hr_sth = $dbh->execute($hr_query);
														  while($hr_sth->fetchInto($HR)) {
															$selected = ($REQUEST['interviewHR'] == $HR['eid']) ? selected : $blank;
															print "<option value=\"".$HR['eid']."\" ".$selected.">" . ucwords(strtolower($HR['fullname'])) . "</option>\n";
														  }
													  ?>
                                                  </select></td>
                                                </tr>
                                                <tr>
                                                  <td><vllabel form="Form" validators="interviewTeam1" class="valRequired2" errclass="valError"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle"><?= $language['label']['teamMember']; ?>:</vllabel></td>
                                                  <td><input id="interviewTeam1" name="interviewTeam1" type="text" size="40" value="<?= $interviewTeam[0]; ?>" <?= $inputStatus['interviewTeam1']; ?> />
                                                      <script type="text/javascript">
														</script>
                                                      <vlvalidator name="interviewTeam1" type="required" control="interviewTeam1" errmsg="Enter the first person in the interview team.">
                                                      <script type="text/javascript">
//													    Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam1", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle"><?= $language['label']['teamMember']; ?>:</td>
                                                  <td><input id="interviewTeam2" name="interviewTeam2" type="text" size="40" value="<?= $interviewTeam[1]; ?>" <?= $inputStatus['interviewTeam2']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam2", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle"><?= $language['label']['teamMember']; ?>:</td>
                                                  <td><input id="interviewTeam3" name="interviewTeam3" type="text" size="40" value="<?= $interviewTeam[2]; ?>" <?= $inputStatus['interviewTeam3']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam3", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle"><?= $language['label']['teamMember']; ?>:</td>
                                                  <td><input id="interviewTeam4" name="interviewTeam4" type="text" size="40" value="<?= $interviewTeam[3]; ?>" <?= $inputStatus['interviewTeam4']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam4", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle"><?= $language['label']['teamMember']; ?>:</td>
                                                  <td><input id="interviewTeam5" name="interviewTeam5" type="text" size="40" value="<?= $interviewTeam[4]; ?>" <?= $inputStatus['interviewTeam5']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam5", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr rel="_both">
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                                <tr rel="_both">
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                                <tr rel="_both">
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                              </table>
                                          </div></td>
                                        </tr>
                                    </table>
									<?php } else { ?>
                                      <table width="100%"  border="0">
                                        <tr>
                                          <td height="25" class="<?= $showContent['stage1.4']['accent']; ?>">&nbsp;&nbsp;<a href="javascript:void();"  class="<?= $showContent['stage1.4']['accent_text']; ?>"><strong><img src="../images/team.gif" width="16" height="18" border="0" align="texttop">&nbsp;
                                                  <?= $language['label']['stage1.4']; ?>...</strong></a></td>
                                        </tr>
                                        <tr>
                                          <td><div class="panelContent" id="stage1.4" style="display:<?= $showContent['stage1.4']['display']; ?>">
                                          <table width="100%" border="0">
                                                <tr>
                                                  <td rowspan="10" valign="top"><table width="100%" border="0">
                                                      <tr>
                                                        <td width="225">Post Recruitment:</td>
                                                        <td><select name="recruitment" id="select" <?= $inputStatus['recruitment']; ?>>
                                                          <option value="0" rel="none">Select One</option>
                                                          <option value="internally" rel="_internally" <?= ($REQUEST['recruitment'] == 'internally') ? selected : $blank; ?>>Internally</option>
                                                          <option value="externally" rel="_externally" <?= ($REQUEST['recruitment'] == 'externally') ? selected : $blank; ?>>Externally</option>
                                                          <option value="both" rel="_both" <?= ($REQUEST['recruitment'] == 'both') ? selected : $blank; ?>>Internally and Externally</option>
                                                          <option value="confidential" rel="none" <?= ($REQUEST['recruitment'] == 'confidential') ? selected : $blank; ?>>Confidential</option>
                                                        </select></td>
                                                      </tr>
                                                      <tr rel="_internally">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt12" name="candidateInt12" type="text" size="40" value="<?= $candidateInt[0]; ?>" <?= $inputStatus['candidateInt1']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt1", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_internally">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt22" name="candidateInt22" type="text" size="40" value="<?= $candidateInt[1]; ?>" <?= $inputStatus['candidateInt2']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt2", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_internally">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt32" name="candidateInt32" type="text" size="40" value="<?= $candidateInt[2]; ?>" <?= $inputStatus['candidateInt3']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt3", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_externally">
                                                        <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">External Candidate:</td>
                                                        <td><input name="candidateExt2" type="text" id="candidateExt2" size="40" maxlength="50" value="<?= stripslashes($REQUEST['candidateExt']); ?>" <?= $inputStatus['candidateExt']; ?>></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt12" name="candidateInt12" type="text" size="40" value="<?= $candidateInt[3]; ?>" <?= $inputStatus['candidateInt1']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt1", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt22" name="candidateInt22" type="text" size="40" value="<?= $candidateInt[4]; ?>" <?= $inputStatus['candidateInt2']; ?> />
                                                            <script type="text/javascript">
//																Event.observe(window, "load", function() {
//																	var aa = new AutoAssist("candidateInt2", function() {
//																		return "../Common/employees.php?q=" + this.txtBox.value;
//																	});
//																});
															</script></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td valign="top"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Internal Candidates:</td>
                                                        <td><input id="candidateInt32" name="candidateInt32" type="text" size="40" value="<?= $candidateInt[5]; ?>" <?= $inputStatus['candidateInt3']; ?> />
                                                            <script type="text/javascript">
//															Event.observe(window, "load", function() {
//																var aa = new AutoAssist("candidateInt3", function() {
//																	return "../Common/employees.php?q=" + this.txtBox.value;
//																});
//															});
															</script></td>
                                                      </tr>
                                                      <tr rel="_both">
                                                        <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">External Candidate:</td>
                                                        <td><input name="candidateExt2" type="text" id="candidateExt2" size="40" maxlength="50" value="<?= stripslashes($REQUEST['candidateExt']); ?>" <?= $inputStatus['candidateExt']; ?>></td>
                                                      </tr>
                                                  </table></td>
                                                  <td><span>Interview Team Leader:</span></td>
                                                  <td><input id="interviewLeader2" name="interviewLeader2" type="text" size="40" value="<?= $REQUEST['interviewLeader']; ?>" <?= $inputStatus['interviewLeader']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewLeader", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr rel="_direct">
                                                  <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Human Resources Member:</td>
                                                  <td><select name="select" id="select2">
                                                      <option value="0">Select One</option>
                                                      <?php
														  $hr_sth = $dbh->execute($hr_query);
														  while($hr_sth->fetchInto($HR)) {
															$selected = ($REQUEST['interviewHR'] == $HR['eid']) ? selected : $blank;
															print "<option value=\"".$HR['eid']."\" ".$selected.">" . ucwords(strtolower($HR['fullname'])) . "</option>\n";
														  }
													  ?>
                                                  </select></td>
                                                </tr>
                                                <tr>
                                                  <td><span><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                                        <?= $language['label']['teamMember']; ?>:</span></td>
                                                  <td><input id="interviewTeam12" name="interviewTeam12" type="text" size="40" value="<?= $interviewTeam[0]; ?>" <?= $inputStatus['interviewTeam1']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam1", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td><span><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                                        <?= $language['label']['teamMember']; ?>:</span></td>
                                                  <td><input id="interviewTeam22" name="interviewTeam22" type="text" size="40" value="<?= $interviewTeam[1]; ?>" <?= $inputStatus['interviewTeam2']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam2", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td><span><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                                        <?= $language['label']['teamMember']; ?>:</span></td>
                                                  <td><input id="interviewTeam32" name="interviewTeam32" type="text" size="40" value="<?= $interviewTeam[2]; ?>" <?= $inputStatus['interviewTeam3']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam3", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td><span><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                                        <?= $language['label']['teamMember']; ?>:</span></td>
                                                  <td><input id="interviewTeam42" name="interviewTeam42" type="text" size="40" value="<?= $interviewTeam[3]; ?>" <?= $inputStatus['interviewTeam4']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam4", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr>
                                                  <td><span><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                                        <?= $language['label']['teamMember']; ?>:</span></td>
                                                  <td><input id="interviewTeam52" name="interviewTeam52" type="text" size="40" value="<?= $interviewTeam[4]; ?>" <?= $inputStatus['interviewTeam5']; ?> />
                                                      <script type="text/javascript">
//														Event.observe(window, "load", function() {
//															var aa = new AutoAssist("interviewTeam5", function() {
//																return "../Common/employees.php?q=" + this.txtBox.value;
//															});
//														});
													  </script></td>
                                                </tr>
                                                <tr rel="_both">
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                                <tr rel="_both">
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                                <tr rel="_both">
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                              </table>
                                          </div></td>
                                        </tr>
                                      </table>
									  <?php } ?>
								    </td>
							    </tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								  <tr>
								    <td class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlueBorder : BGAccentDarkBorder; ?>"><table width="100%" border="0">
                                      <tr>
                                        <td width="100%" height="25" colspan="6" class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlue : BGAccentDark; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td>&nbsp;<a href="javascript:switchComments();" class="<?= (array_key_exists('approval', $_GET)) ? white : black; ?>" title="Help|Show or Hide the Comments"><strong><img src="../images/comments.gif" width="19" height="16" border="0" align="texttop">&nbsp;Comments</strong></a></td>
                                              <td width="120"><a href="comments.php?request_id=<?= $ID; ?>&eid=<?= $_SESSION['eid']; ?>" title="Post a new comment" rel="gb_page_center[675,325]" class="<?= (array_key_exists('approval', $_GET)) ? addWhite : addBlack; ?>">NEW COMMENT</a></td>
                                            </tr>
                                        </table></td>
                                      </tr>
                                      <td><div class="panelContent"><?php if ($post_count > 0) { ?>
                                              <div id="commentsHeader">There <?= ($post_count > 1) ? are : is; ?> currently <strong> <?= $post_count; ?> </strong> comment <?= ($post_count > 1) ? s : ''; ?>. The last comment was posted on <strong><?= date('F d, Y \a\t H:i A', strtotime($LAST_POST['posted'])); ?></strong>.
                                                <div class="clickToView">Click to view all Comments.</div>
                                              </div>
                                        <?php } else { ?>
                                              <div id="commentsHeader">There are currently <strong>NO</strong> comments.</div>
                                        <?php } ?>
                                              <div width="95%" border="0" align="center" id="commentsArea" style="display:none"> <br>
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
                                            <td class="comment_wrote"><?= ucwords(strtolower($EMPLOYEES[$POST[eid]])); ?>
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
                                </div></td>
                                    </table></td>
							    </tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								  <tr>
								    <td class="<?= $showContent['stage5']['accent_border']; ?>"><table width="100%"  border="0">
                                      <tr>
                                        <td width="405" height="25" colspan="6" class="<?= $showContent['stage5']['accent']; ?>"><strong>&nbsp;&nbsp;<a name="approvals"></a><img src="../images/checkmark.gif" width="16" height="16" align="absmiddle">&nbsp;Approvals...</strong></td>
                                      </tr>
                                      <tr>
                                        <td height="25" colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                              <td valign="top"><div class="panelContent"><table border="0">
                                                <tr>
                                                  <td height="25" nowrap class="<?= $showContent['stage5']['accent']; ?>">&nbsp;</td>
                                                  <td width="30" nowrap class="<?= $showContent['stage5']['accent']; ?>">&nbsp;</td>
                                                  <td nowrap class="<?= $showContent['stage5']['accent']; ?>">&nbsp;</td>
                                                  <td nowrap class="<?= $showContent['stage5']['accent']; ?>">Date</td>
                                                  <td width="20" align="center" nowrap class="<?= $showContent['stage5']['accent']; ?>"><img src="/Common/images/clock.gif" width="16" height="16"></td>
                                                  <td width="450" nowrap class="<?= $showContent['stage5']['accent']; ?>">Comment</td>
                                                  <td nowrap class="<?= $showContent['stage5']['accent']; ?>"><?= (array_key_exists('approval', $_GET)) ? 'Approval' : $blank; ?></td>
                                                </tr>
                                                <tr>
                                                  <td nowrap>Requester:</td>
                                                  <td align="center" nowrap><?= showCommentIcon($REQUEST['req'], ucwords(strtolower($EMPLOYEES[$REQUEST['req']])), $REQUEST['id']); ?></td>
                                                  <td nowrap class="label"><?= caps($EMPLOYEES[$REQUEST['req']]); ?></td>
                                                  <td nowrap class="label"><?= $REQUEST['_reqDate']; ?></td>
                                                  <td nowrap class="TrainActive">-</td>
                                                  <td nowrap class="label">-</td>
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
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
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
                                                    	echo showMailIcon('app2', $AUTH['app2'], ucwords(strtolower($EMPLOYEES[$AUTH['app2']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app2Date'])) { 
													    echo showCommentIcon($AUTH['app2'], ucwords(strtolower($EMPLOYEES[$AUTH['app2']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
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
                                                    	echo showMailIcon('app3', $AUTH['app3'], ucwords(strtolower($EMPLOYEES[$AUTH['app3']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app3Date'])) { 
													    echo showCommentIcon($AUTH['app3'], ucwords(strtolower($EMPLOYEES[$AUTH['app3']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
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
                                                    	echo showMailIcon('app4', $AUTH['app4'], ucwords(strtolower($EMPLOYEES[$AUTH['app4']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app4Date'])) { 
													    echo showCommentIcon($AUTH['app4'], ucwords(strtolower($EMPLOYEES[$AUTH['app4']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
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
                                                    	echo showMailIcon('app5', $AUTH['app5'], ucwords(strtolower($EMPLOYEES[$AUTH['app5']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app5Date'])) { 
													    echo showCommentIcon($AUTH['app5'], ucwords(strtolower($EMPLOYEES[$AUTH['app5']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app5', $AUTH['app5'], $AUTH['app5Date']); ?></td>
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
                                                    	echo showMailIcon('app6', $AUTH['app6'], ucwords(strtolower($EMPLOYEES[$AUTH['app6']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app6Date'])) { 
													    echo showCommentIcon($AUTH['app6'], ucwords(strtolower($EMPLOYEES[$AUTH['app6']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app6', $AUTH['app6'], $AUTH['app6Date']); ?></td>
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
                                                    	echo showMailIcon('app7', $AUTH['app7'], ucwords(strtolower($EMPLOYEES[$AUTH['app7']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app7Date'])) { 
													    echo showCommentIcon($AUTH['app7'], ucwords(strtolower($EMPLOYEES[$AUTH['app7']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app7', $AUTH['app7'], $AUTH['app7Date']); ?></td>
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
                                                    	echo showMailIcon('app8', $AUTH['app8'], ucwords(strtolower($EMPLOYEES[$AUTH['app8']])), $REQUEST['id']);
                                                      } else if (!is_null($AUTH['app8Date'])) { 
													    echo showCommentIcon($AUTH['app8'], ucwords(strtolower($EMPLOYEES[$AUTH['app8']])), $REQUEST['id']);
													  }
													  ?></td>
                                                  <td nowrap class="label"><?= displayApprover($_GET['id'], 'app8', $AUTH['app8'], $AUTH['app8Date']); ?></td>
                                                  <td nowrap class="label"><?php if (isset($AUTH['app8Date'])) { echo date("F d, Y", strtotime($AUTH['app8Date'])); } ?></td>
                                                  <td nowrap class="TrainActive"><?php if (isset($AUTH['app8Date'])) { echo abs(ceil((strtotime($AUTH['app6Date']) - strtotime($AUTH['app8Date'])) / (60 * 60 * 24))); } ?></td>
                                                  <td nowrap class="label"><?= displayAppComment('app8', $_GET['approval'], $AUTH['app8'], $AUTH['app8Com'], $AUTH['app8Date']); ?></td>
                                                  <td nowrap><?= displayAppButtons($ID, $_GET['approval'], 'app8', $AUTH['app8'], $AUTH['app8Date']); ?></td>
                                                </tr>
												<!-- END APPROVER 8 -->
												<!-- START STAFFING -->
                                                <tr <?= ($_GET['approval'] == 'staffing') ? $highlight : $blank; ?>>
                                                  <td nowrap>Staffing Manager:</td>
                                                  <td nowrap>&nbsp;</td>
                                                  <td nowrap class="label"><?= ucwords(strtolower($EMPLOYEES[$AUTH['staffing']])); ?></td>
                                                  <td nowrap class="label"><?php if (isset($AUTH['staffingDate'])) { echo date("F d, Y", strtotime($AUTH['staffingDate'])); } ?></td>
                                                  <td nowrap class="TrainActive"><?php if (isset($AUTH['staffingDate'])) { echo abs(ceil((strtotime($AUTH['app8Date']) - strtotime($AUTH['staffingDate'])) / (60 * 60 * 24))); } ?></td>
                                                  <td nowrap class="label">-</td>
                                                  <td nowrap>&nbsp;</td>
                                                </tr>
												<?php
												/* Only display HR Coordinator for Direct employees */
												if ($REQUEST['requestType'] == '1') { 
												?>
                                                <tr <?= ($_GET['approval'] == 'coordinator') ? $highlight : $blank; ?>>
                                                  <td nowrap>Physical/Drug Screening: </td>
                                                  <td nowrap>&nbsp;</td>
                                                  <td nowrap class="label"><?= ucwords(strtolower($EMPLOYEES[$AUTH['coordinator']])); ?></td>
                                                  <td nowrap class="label"><?php if (isset($AUTH['coordinatorDate'])) { echo date("F d, Y", strtotime($AUTH['coordinatorDate'])); } ?></td>
                                                  <td nowrap class="TrainActive"><?php if (isset($AUTH['coordinatorDate'])) { echo abs(ceil((strtotime($AUTH['staffingDate']) - strtotime($AUTH['coordinatorDate'])) / (60 * 60 * 24))); } ?></td>
                                                  <td nowrap class="label"><?= ucwords($AUTH['coordinatorCom']); ?></td>
                                                  <td nowrap>&nbsp;</td>
                                                </tr>
												<?php } ?>
                                                <tr <?= ($_GET['approval'] == 'generator') ? $highlight : $blank; ?>>
                                                  <td nowrap>EID Generator: </td>
                                                  <td align="center" nowrap>
												  <?php if (!empty($AUTH['coordinatorDate']) AND empty($EINFO['eid'])) { ?>
                                                 	<a href="<?= $default['URL_HOME']; ?>/Administration/generateEID.php?request_id=<?= $ID; ?>" title="Help|Generate an employee ID"><img src="../images/vcard.gif" width="20" height="16" border="0"></a>
												  <?php } ?></td>
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
                                              </table></div></td>
                                        </tr>
                                          </table></td>
                                      </tr>
                                    </table></td>
							    </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td height="5" valign="bottom"><img src="../images/spacer.gif" width="5" height="5"></td>
                            </tr>
                            <tr>
                              <td><?php if ($REQUEST['status'] != 'X') { ?>
                                <div id="noPrint">
                                 <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                   <tr>
                                     <td width="50%" valign="middle"><?php if (($_SESSION['eid'] == $REQUEST['req'] AND $REQUEST['status'] != 'C' AND empty($_GET['approval'])) OR $_SESSION['hcr_groups'] == 'ex') { ?>
                                       <table  border="0" cellspacing="0" cellpadding="0">
                                         <tr>
                                           <td width="20" valign="middle"><input name="cancel" type="checkbox" id="cancel" value="yes"></td>
                                           <td><input name="imageField" type="image" src="../images/button.php?i=w130.png&l=Cancel Request" alt="Cancel Request" border="0"></td>
                                         </tr>
                                       </table>
									<?php } ?><!--
									<?php if ($_SESSION['eid'] == $REQUEST['req'] AND ($REQUEST['status'] == 'X' OR $REQUEST['status'] == 'C')) { ?>
                                       <table  border="0" cellspacing="0" cellpadding="0">
                                         <tr>
                                           <td width="20" valign="middle"><input name="restore" type="checkbox" id="restore" value="yes"></td>
                                           <td><input name="imageField" type="image" src="../images/button.php?i=w130.png&l=Restore Request" alt="Restore Request" border="0"></td>
                                         </tr>
                                       </table>
									<?php } ?>
									--></td>
									 
                                     <td width="50%" align="right">
										 <input name="app7" id="app7" type="hidden" value="99998">
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
                                       <span class="<?= $showContent['actualcompensation']['accent']; ?>"><a href="#" class="<?= $showContent['actualcompensation']['accent_text']; ?>"></a></span>
									   <input name="staffing_status" type="hidden" id="staffing_status" value="<?= $staffing_status; ?>">
                                       <input name="auth" type="hidden" id="auth" value="<?= $auth_value; ?>">
                                       <input name="auth_id" type="hidden" id="auth_id" value="<?= $AUTH['id']; ?>">
                                       <input name="request_id" type="hidden" id="request_id" value="<?= $ID; ?>">
                                       <input name="stage" type="hidden" id="stage" value="<?= $update_stage ?>">
									   <?php if (array_key_exists('inform', $_GET)) { ?>
									   <input name="inform" type="hidden" id="infrom" value="<?= $_GET['inform']; ?>">
									   <?php } ?>
									   <!-- <img src="../images/button.php?i=b150.png&l=Update Request" name="Update Request" alt="" id="cmdSubmit" onclick="submitThisForm()"> -->
									&nbsp;</td>
                                   </tr>
                                 </table>
                                </div>
								<?php } ?></td>
                            </tr>
                            <tr align="center">
                              <td>
							  <?php if ($_GET['approval'] == 'app2') { ?>
							  <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td><vlsummary form="Form" class="valErrorList" headertext="Please correct the following errors:" displaymode="bulletlist" showsummary="true" messagebox="false"></td>
                                </tr>
                              </table>
							  <?php } ?>
							  </td>
                            </tr>
                          </table>  
                   	</form>
					<br>
					<?php include('../Administration/include/detail.php'); ?></td>
                </tr>
              </tbody>
          </table></td>
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
    <script type="text/JavaScript">
        <!--
        function swapImgRestore() { //v3.0
              var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
            }
            
            function findObj(n, d) { //v4.01
              var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
                d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
              if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
              for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
              if(!x && d.getElementById) x=d.getElementById(n); return x;
            }
            
            function swapImage() { //v3.0
              var i,j=0,x,a=swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
               if ((x=findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
            }
        
        function preloadImages() { //v3.0
          var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
            var i,j=d.MM_p.length,a=preloadImages.arguments; for(i=0; i<a.length; i++)
            if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
        }
        //-->
    </script>
    
    <script>
        var request_id='<?= $_GET['id']; ?>';
        var approval='<?= $_GET['approval']; ?>';
        var level='<?= $AUTH['level']; ?>';
        var status='<?= $REQUEST['status']; ?>';
        var canceled='<?= $canceled; ?>';
        var message='<?= $message; ?>';
        var msgClass='<?= $msgClass; ?>';
    </script>
    
    <script type="text/javascript" src="/Common/Javascript/jquery/scrollTo/jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="/Common/Javascript/jquery/ui/ui.datepicker-min.js"></script>
    <script type="text/javascript" src="../js/jQdetail.js"></script>
        
    <SCRIPT type="text/javascript" SRC="/Common/Javascript/usableforms.js"></SCRIPT> 
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
