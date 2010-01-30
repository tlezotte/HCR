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



/* -- Get employee information from HCR -- */
$sql_request="SELECT r.department, r.plant, r.requestType, r.startDate, e.fst, e.lst 
			  FROM Requests r, Employees e
			  WHERE r.id=e.request_id
			   AND r.id=".$_POST['request_id'];
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
$sql_employees="UPDATE Employees SET eid='".$EID."' WHERE request_id=".$_POST['request_id'];
$dbh->query($sql_employees);

/* -- Update EID of EID Generator -- */
$sql_authorize="UPDATE Authorization SET generator='".$_SESSION['eid']."', generatorDate=NOW() WHERE request_id=".$_POST['request_id'];
$dbh->query($sql_authorize);
		
/* -- Record transaction for history -- */						  
History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql_standards)));																

/* -- Send out email message -- */
//$sendTo = $EMPLOYEE['email'];
//$subject = "Your your company information...";
//
//$message_body = <<< END_OF_BODY
//Your your company employment information:<br>
//<br>
//<b>Human Capital ID:</b> $EID<br>
//<b>Your Email Address:</b> $EMPLOYEE[email]<br>
//<b>Your Username:</b> $EMPLOYEE[username]<br>
//<b>Your Password:</b> $EMPLOYEE[password]<br>
//END_OF_BODY;
//
//$url = "http://intranet.yourcompany.com";
//
//sendGeneric2($sendTo, $subject, $message_body, $url);
		
header('Location: ../Requests/detail.php?id=' . $_POST['request_id']);
exit();	

 
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();

?>
