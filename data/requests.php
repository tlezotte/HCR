<?php
/**
 * Request System
 *
 * list.php displays available PO.
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
 * - Config Information
 */
require_once('../include/config.php'); 


/* SQL for access view */
if ($_GET['my'] == "true") {
	switch ($_GET['access']) {
		case '0': $get_my_access = "AND r.req='" . $_SESSION['eid'] ."'"; break;
		case '1': $get_my_access = "AND a.level = 'app1' AND a.app1='" . $_SESSION['eid'] ."'"; break;
		case '2': $get_my_access = "AND a.level = 'app2' AND a.app2='" . $_SESSION['eid'] ."'"; break;
		case '3': $get_my_access = "AND a.level = 'app3' AND a.app3='" . $_SESSION['eid'] ."'"; break;
		case '4': $get_my_access = "AND a.level = 'app4' AND a.app4='" . $_SESSION['eid'] ."'"; break;
		case '5': $get_my_access = "AND a.level = 'app5' AND a.app5='" . $_SESSION['eid'] ."'"; break;
		case '6': $get_my_access = "AND a.level = 'app6' AND a.app6='" . $_SESSION['eid'] ."'"; break;
		case '7': $get_my_access = "AND a.level = 'app7' AND a.app7='" . $_SESSION['eid'] ."'"; break;		
		case '8': $get_my_access = "AND a.level = 'app8' AND a.app8='" . $_SESSION['eid'] ."'"; break;		
	}
}

/* Setting up Status view */
if (!isset($where_status)) {
	switch ($_GET['status']) {
		case N: $where_status = "AND r.status = 'N'"; break;
		case A: $where_status = "AND r.status = 'A'"; break;
		case X: $where_status = "AND r.status = 'X'"; break;
		case C: $where_status = "AND r.status = 'C'"; break;  
		default: $where_status = "AND r.status NOT IN ('X', 'C')"; break;           
	}
}

/* -- set list view to mine or all -- */
if (!array_key_exists('access', $_GET)) {
	$get_my_requests = "AND r.req='" . $_SESSION['eid'] . "'";										// Only display my requests
}

$request_type = ($_GET['type'] != 'all') ? mysql_real_escape_string($_GET['type']) : '%';
$request_status = mysql_real_escape_string($_GET['status']);


/* SQL for PO list */
$sql = <<< END_OF_SQL
SELECT *, 
	r.id AS _ID, 
	DATE_FORMAT(r.reqDate, '%b %d, %Y') AS _DATE, 
	DATE_FORMAT(r.reqDate, '%H:%i') AS _TIME, 
	DATE_FORMAT(r.targetDate, '%b %d, %Y') AS _TARGETDATE,
	DATE_FORMAT(r.startDate, '%b %d, %Y') AS _STARTDATE
FROM Requests r
	LEFT JOIN Authorization a ON a.request_id=r.id
	LEFT JOIN Position p ON p.title_id=r.positionTitle
WHERE r.request_type LIKE '$request_type'
	AND r.status='$request_status'
	$get_my_access
	$get_my_requests
ORDER BY _ID desc
END_OF_SQL;


/* Get Plants and Employees from Stanards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u
							   INNER JOIN Standards.Employees e ON e.eid = u.eid");	
$PLANT = $dbh->getAssoc("SELECT id, name FROM Standards.Plants");
$DEPT = $dbh->getAssoc("SELECT id, name FROM Standards.Department");					 		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

function shortPurpose($purpose) {
	$output = htmlspecialchars(caps(substr(stripslashes($purpose), 0, 40)), ENT_QUOTES, 'UTF-8');
	if (strlen($purpose) >= 40) { 
		$output .= "..."; 
	}

	return $output;
}

$PT = array('1' => 'Full-Time',
			'2' => 'Part-Time');
$RT = array('1' => 'Direct',
			'2' => 'Contract',
			'3' => 'Contract or Direct');
			
								  
$format_phone="(000)000-0000";


if ($_GET['output'] == 'json') {

	require_once 'json/JSON.php';
	
	$data = $dbh->getAll($sql);	
	
	$json = new Services_JSON();
	$output = '{"ResultSet":{"Result":' . $json->encode($data) . '}}';

} else {

	header('Content-type: text/xml');
	header('Pragma: public');     
	header('Cache-control: private');
	header('Expires: -1');
	
	$output .= "<hcr>\n";
	if ($debug_page) {
		$output .= "        <sql><![CDATA[" . $sql . "]]></sql>\n";	
	}
	
	$query = $dbh->prepare($sql);
	$sth = $dbh->execute($query);
	$num_rows = $sth->numRows();	
	while($sth->fetchInto($DATA)) {
		$positionTitle=getPositionTitle($DATA['positionTitle'], $DATA['request_type']);			// Get Position Title
	
		$output .= "    <request>\n";
		$output .= "        <id>" . $DATA['_ID'] . "</id>\n";	
		$output .= "        <request_type>" . caps($DATA['request_type']) . "</request_type>\n";	
		$output .= "        <request_date>" . $DATA['_DATE'] . "</request_date>\n";	
		$output .= "        <requester eid=\"" . $DATA['req'] . "\" date=\"" . $DATA['_DATE'] . "\" time=\"" . $DATA['_TIME'] . "\">" . caps($EMPLOYEES[$DATA['req']]) . "</requester>\n";	
		$output .= "        <position>\n";
		$output .= "        	<title id=\"" . $DATA['positionTitle'] . "\" grade=\"" . $DATA['grade'] . "\">" . caps(str_replace("&", "and", $positionTitle['title_name'])) . "</title>\n";	
		$output .= "        	<position_type>" . caps($PT[$DATA['positionType']]) . "</position_type>\n";	
		$output .= "        	<request_type time=\"" . $DATA['contractTime'] . "\">" . caps($RT[$DATA['requestType']]) . "</request_type>\n";	
		$output .= "			<status>" . caps($DATA['positionStatus']) . "</status>\n";
		$output .= "			<replacement eid=\"" . $DATA['replacement'] . "\">" . caps($DATA['replacement']) . "</replacement>\n";
		$output .= "		</position>\n";
		$output .= "        <location id=\"" . $DATA['plant'] . "\">" . caps($PLANT[$DATA['plant']]) . "</location>\n";
		$output .= "        <department id=\"" . $DATA['department'] . "\">" . caps($DEPT[$DATA['department']]) . "</department>\n";
		$output .= "        <desired_start_date>" . $DATA['_TARGETDATE'] . "</desired_start_date>\n";
		$output .= "        <actual_start_date>" . $DATA['_STARTDATE'] . "</actual_start_date>\n";
		$output .= "        <level>" . $DATA['level'] . "</level>\n";	
		$output .= "        <status>" . reqStatus($DATA['status']) . "</status>\n";
		$output .= "        <authorization>\n";
		$output .= "        	<approver1 eid=\"" . $DATA['app1'] . "\" yn=\"" . $DATA['app1yn'] . "\" date=\"" . $DATA['app1Date'] . "\">" . caps($EMPLOYEES[$DATA['app1']]) . "</approver1>\n";
		$output .= "        	<approver2 eid=\"" . $DATA['app2'] . "\" yn=\"" . $DATA['app2yn'] . "\" date=\"" . $DATA['app2Date'] . "\">" . caps($EMPLOYEES[$DATA['app2']]) . "</approver2>\n";
		$output .= "        	<approver3 eid=\"" . $DATA['app3'] . "\" yn=\"" . $DATA['app3yn'] . "\" date=\"" . $DATA['app3Date'] . "\">" . caps($EMPLOYEES[$DATA['app3']]) . "</approver3>\n";
		$output .= "        	<approver4 eid=\"" . $DATA['app4'] . "\" yn=\"" . $DATA['app4yn'] . "\" date=\"" . $DATA['app4Date'] . "\">" . caps($EMPLOYEES[$DATA['app4']]) . "</approver4>\n";
		$output .= "        	<approver5 eid=\"" . $DATA['app5'] . "\" yn=\"" . $DATA['app5yn'] . "\" date=\"" . $DATA['app5Date'] . "\">" . caps($EMPLOYEES[$DATA['app5']]) . "</approver5>\n";
		$output .= "        	<approver6 eid=\"" . $DATA['app6'] . "\" yn=\"" . $DATA['app6yn'] . "\" date=\"" . $DATA['app6Date'] . "\">" . caps($EMPLOYEES[$DATA['app6']]) . "</approver6>\n";
		$output .= "        	<approver7 eid=\"" . $DATA['app7'] . "\" yn=\"" . $DATA['app7yn'] . "\" date=\"" . $DATA['app7Date'] . "\">" . caps($EMPLOYEES[$DATA['app7']]) . "</approver7>\n";
		$output .= "        	<approver8 eid=\"" . $DATA['app8'] . "\" yn=\"" . $DATA['app8yn'] . "\" date=\"" . $DATA['app8Date'] . "\">" . caps($EMPLOYEES[$DATA['app8']]) . "</approver8>\n";
		$output .= "        	<staffing eid=\"" . $DATA['staffing'] . "\" yn=\"" . $DATA['staffingyn'] . "\" date=\"" . $DATA['staffingDate'] . "\">" . caps($EMPLOYEES[$DATA['staffing']]) . "</staffing>\n";				
		$output .= "        </authorization>\n";	
		$output .= "    </request>\n";
	}
	
	$output .= "</hcr>\n";
	
}
        
print $output;
?>


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