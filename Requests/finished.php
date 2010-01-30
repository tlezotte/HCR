<?php
/**
 * Request System
 *
 * authorization.php setup automatic printing to staffing.
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
 * - Check user access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/*
 * ------------------ START PROCESSING DATA ----------------------- 
 */
if ($_SESSION['action'] == 'new') {
	/* ---------- Insert Request data ---------- */
	$request_sql = "INSERT INTO Requests (id, request_type, req, reqDate, positionTitle, department, plant, positionStatus, replacement, positionType, requestType, contractTime, targetDate, budgetPosition, utilize, headCount, currentHeadCount, budget, justification, file_name, file_type, file_ext, file_size, jobDescription, primaryJob, secondaryJob) VALUES 
										(NULL,
										'new',
										'" . mysql_real_escape_string($_SESSION['eid']) . "',
										NOW(),
										'" . mysql_real_escape_string($_SESSION['positionTitle']) . "',
										'" . mysql_real_escape_string($_SESSION['department']) . "',
										'" . mysql_real_escape_string($_SESSION['plant']) . "',
										'" . mysql_real_escape_string($_SESSION['positionStatus']) . "',
										'" . mysql_real_escape_string($_SESSION['replacement']) . "',
										'" . mysql_real_escape_string($_SESSION['positionType']) . "',
										'" . mysql_real_escape_string($_SESSION['requestType']) . "',
										'" . mysql_real_escape_string($_SESSION['contractTime']) . "',
										'" . mysql_real_escape_string($_SESSION['targetDate']) . "',
										'" . mysql_real_escape_string($_SESSION['budgetPosition']) . "',
										'" . mysql_real_escape_string($_SESSION['utilize']) . "',
										'" . mysql_real_escape_string($_SESSION['headCount']) . "',
										'" . mysql_real_escape_string($_SESSION['currentHeadCount']) . "',
										'" . mysql_real_escape_string($_SESSION['budget']) . "',
										'" . mysql_real_escape_string($_SESSION['justification']) . "',
										'" . mysql_real_escape_string($_SESSION['file_name']) . "',
										'" . mysql_real_escape_string($_SESSION['file_type']) . "',
										'" . mysql_real_escape_string($_SESSION['file_ext']) . "',
										'" . mysql_real_escape_string($_SESSION['file_size']) . "',
										'" . mysql_real_escape_string($_SESSION['description']) . "',
										'" . mysql_real_escape_string($_SESSION['primaryJob']) . "',
										'" . mysql_real_escape_string($_SESSION['secondaryJob']) . "'
										)";										 	
	echo ($debug_page) ? $request_sql.'<br><br>' : $blank;
	$dbh->query($request_sql);
							
	$REQUEST_ID = $dbh->getOne("select max(id) from Requests");
	
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($request_sql)));		// Record transaction for history
	}
	
	$staffing=getPosition('staffing','none');			// Get Staffing Information 
	$coordinator=getPosition('coordinator','none');		// Get Coordinator Information 
	$generator=getPosition('generator','none');			// Get Generator Information
	
	/* ---------- Insert Authorization data ---------- */
	/* Automatically approve Approver 7 or COO - HR 12/14/2006 */
	$auth_sql = "INSERT INTO Authorization (id, request_id, app1, app2, app3, app4, app5, app6, app7, app8, staffing, coordinator, generator, level) VALUES
											(NULL,
											 '" . $REQUEST_ID . "',
											 '" . mysql_real_escape_string($_SESSION['app1']) . "',
											 '" . mysql_real_escape_string($_SESSION['app2']) . "',
											 '" . mysql_real_escape_string($_SESSION['app1']) . "',
											 '" . mysql_real_escape_string($_SESSION['app4']) . "',
											 '" . mysql_real_escape_string($_SESSION['app5']) . "',
											 '" . mysql_real_escape_string($_SESSION['app6']) . "',
											 '99998',
											 '" . mysql_real_escape_string($_SESSION['app8']) . "',
											 '" . mysql_real_escape_string($staffing['eid']) . "',
											 '" . mysql_real_escape_string($coordinator['eid']) . "',
											 '" . mysql_real_escape_string($generator['eid']) . "',
											 'app1'
											 )";
	echo ($debug_page) ? $auth_sql.'<br><br>' : $blank;
	$dbh->query($auth_sql);
	//sendGeneric('tlezotte@yourcompany.com', 'New HCE', $auth_sql, $REQUEST_ID);			// Test email for new HCRs
	
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($auth_sql)));		// Record transaction for history
	}
	
	/* ---------- Insert Technology data ---------- */
	$tech_sql = "INSERT INTO Technology (tech_id, request_id, tech_transfer, tech_computer, tech_printer, tech_cellular, tech_cellularInt, tech_cellularTrans, tech_blackberry, tech_blackberryInt, tech_blackberryTrans, tech_phone, tech_badge, tech_notesID, tech_as400, tech_request, tech_jobTracking, tech_vpn, tech_optionalSoftware, tech_status) VALUES 
										(NULL,
										'" . $REQUEST_ID . "',
										'" . mysql_real_escape_string($_SESSION['tech_transfer']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_computer']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_printer']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_cellular']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_cellularInt']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_cellularTrans']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_blackberry']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_blackberryInt']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_blackberryTrans']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_phone']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_badge']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_notesID']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_as400']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_request']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_jobTracking']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_vpn']) . "',
										'" . mysql_real_escape_string($_SESSION['tech_optionalSoftware']) . "',
										'N'
										)";	
	echo ($debug_page) ? $tech_sql.'<br><br>' : $blank;																			
	$dbh->query($tech_sql);
	
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($tech_sql)));		// Record transaction for history
	}


} else {


	/* ---------- Insert Request data ---------- */
	$request_sql = "INSERT INTO Requests (id, request_type, req, reqDate, positionTitle, department, plant, startDate, justification, primaryJob, status) VALUES
										(NULL,
										'" . mysql_real_escape_string($_SESSION['request_type']) . "',
										'" . mysql_real_escape_string($_SESSION['eid']) . "',
										NOW(),
										'" . mysql_real_escape_string($_SESSION['positionTitle'] . ":" . $_SESSION['positionTitle_new']) . "',
										'" . mysql_real_escape_string($_SESSION['department'] . ":" . $_SESSION['department_new']) . "',
										'" . mysql_real_escape_string($_SESSION['plant'] . ":" . $_SESSION['plant_new']) . "',
										'" . mysql_real_escape_string($_SESSION['startDate']) . "',
										'" . mysql_real_escape_string($_SESSION['justification']) . "',
										'" . mysql_real_escape_string($_SESSION['primaryJob']) . "',
										'N'
										)";										 	
	$dbh->query($request_sql);

	/* Get REQUEST auto_increment ID */							
	$REQUEST_ID = $dbh->getOne("select max(id) from Requests");	

	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($request_sql)));		// Record transaction for history
	}
	
	/* ---------- Insert Authorization data ---------- */
	$auth_sql = "INSERT INTO Authorization (id, request_id, app1, app2, app3, app4, app5, app6, app8, level) VALUES
											(NULL,
											 '" . $REQUEST_ID . "',
										     '" . mysql_real_escape_string($_SESSION['app1']) . "',
										  	 '" . mysql_real_escape_string($_SESSION['app2']) . "',
										  	 '" . mysql_real_escape_string($_SESSION['app1']) . "',
										  	 '" . mysql_real_escape_string($_SESSION['app4']) . "',
										  	 '" . mysql_real_escape_string($_SESSION['app5']) . "',
											 '" . mysql_real_escape_string($_SESSION['app6']) . "',
											 '" . mysql_real_escape_string($_SESSION['app8']) . "',
											 'app1'
										  	 )";
	$dbh->query($auth_sql);

	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($auth_sql)));		// Record transaction for history
	}
		
	/* ---------- Insert Curent Conpensation data ---------- */
	$comp_sql = "INSERT INTO Compensation (id, request_id, recordDate, salaryGrade, salary, overTime, doubleTime, billRate, percentage, increase, vehicleAllowance, vacationDays, agency, status) VALUES 
										  (NULL,
										  '" . $REQUEST_ID . "',
										  NOW(),
										  '" . mysql_real_escape_string(base64_encode($_SESSION['salaryGrade']) . ":" . base64_encode($_SESSION['salaryGrade_new'])) . "',
										  '" . mysql_real_escape_string(base64_encode(preg_replace("/,/", "", $_SESSION['salary'])) . ":" . base64_encode(preg_replace("/,/", "", $_SESSION['salary_new']))) . "',
										  '" . mysql_real_escape_string($_SESSION['overTime'] . ":" . $_SESSION['overTime_new']) . "',
										  '" . mysql_real_escape_string($_SESSION['doubleTime'] . ":" . $_SESSION['doubleTime_new']) . "',
										  '" . mysql_real_escape_string($_SESSION['billRate']) . "',
										  '" . mysql_real_escape_string($_SESSION['percentage']) . "',
										  '" . mysql_real_escape_string($_SESSION['increase']) . "',
										  '" . mysql_real_escape_string(base64_encode($_SESSION['vehicleAllowance']) . ":" . base64_encode($_SESSION['vehicleAllowance_new'])) . "',
										  '" . mysql_real_escape_string($_SESSION['vacationDays'] . ":" . $_SESSION['vacationDays_new']) . "',
										  '" . mysql_real_escape_string($_SESSION['agency']) . "',
										  'A'
										  )";	
	$dbh->query($comp_sql);
	
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($comp_sql)));		// Record transaction for history
	}
	
	/* ------------- Insert Employee Information ------------- */
	$emp_sql = "INSERT INTO Employees (id, request_id, eid) VALUES 
										(NULL,
										'" . $REQUEST_ID . "',											  
										'" . mysql_real_escape_string($_SESSION['employee']) . "'											
										)";	
	$dbh->query($emp_sql);		

	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $REQUEST_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($emp_sql)));		// Record transaction for history
	}
}
/*
 * ------------------ END PROCESSING DATA ----------------------- 
 */
 
 
/* ------------------------------------------------------------------------ */


iCalendar('Desired');						// Update Calendar
clearSession();								// Clear session


/* Display Debug Information */
include_once('debug/footer.php');
/* Disconnect from database */
$dbh->disconnect();


/* Forward to router */
if ($debug_page) {
	exit(0);
} else {
	header("Location: router.php?request_id=".$REQUEST_ID."&approval=app0");
}
/* ------------------ END PROCESSING DATA ----------------------- */
?>