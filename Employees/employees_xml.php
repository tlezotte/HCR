<?php
/**
 * Employee List
 *
 * index.php is the search page for the Employee List.
 *
 * @version 0.1
 * @link http://a2.yourcompany.com/go/Employees/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @global mixed $default[]
 * @filesource
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
require_once('../Connections/connStandards.php'); 
/**
 * --- CHECK USER ACCESS --- 
 */
require_once('../security/check_user.php');
/**
 * - Common Information
 */
require_once('../include/config.php'); 



/* ------------------ START DATABASE CONNECTIONS ----------------------- */
if (is_numeric($_GET['eid'])) {
	$data_sql = "SELECT *, e.eid AS _eid, p.name AS _location, d.name AS _dept, e.status AS _status
				 FROM Employees e
				   LEFT JOIN ComDevices c ON c.cell_eid=e.eid
				   LEFT JOIN Plants p ON p.id=e.Location
				   LEFT JOIN Department d ON d.id=e.dept
				 WHERE e.eid LIKE '" . $_GET['eid'] . "%'";
} else {
	if (strlen($_GET['fst']) >= 1 AND strlen($_GET['lst']) >= 1) {
		$how_to_search = ($_GET['fst'] == $_GET['lst']) ? 'OR' : 'AND';
		$data_sql = "SELECT *, e.eid AS _eid, p.name AS _location, d.name AS _dept, e.status AS _status
					 FROM Employees e
					   LEFT JOIN ComDevices c ON c.cell_eid=e.eid
					   LEFT JOIN Plants p ON p.id=e.Location
					   LEFT JOIN Department d ON d.id=e.dept
					 WHERE e.fst LIKE '" . $_GET['fst'] . "%' " . $how_to_search . " e.lst LIKE '" . $_GET['lst'] . "%'";
	} elseif (strlen($_GET['fst']) >= 1) {	
		$data_sql = "SELECT *, e.eid AS _eid, p.name AS _location, d.name AS _dept, e.status AS _status
					 FROM Employees e
					   LEFT JOIN ComDevices c ON c.cell_eid=e.eid
					   LEFT JOIN Plants p ON p.id=e.Location
					   LEFT JOIN Department d ON d.id=e.dept
					 WHERE e.fst LIKE '" . $_GET['fst'] . "%'";
	} elseif (strlen($_GET['lst']) >= 1) {	
		$data_sql = "SELECT *, e.eid AS _eid, p.name AS _location, d.name AS _dept, e.status AS _status
					 FROM Employees e
					   LEFT JOIN ComDevices c ON c.cell_eid=e.eid
					   LEFT JOIN Plants p ON p.id=e.Location
					   LEFT JOIN Department d ON d.id=e.dept
					 WHERE e.lst LIKE '" . $_GET['lst'] . "%'";
	}
}

$data_query = $dbh_standards->prepare($data_sql);
$data_sth = $dbh_standards->execute($data_query);
$num_rows = $data_sth->numRows();												 		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

$format_phone="(000)000-0000";



header('Content-type: text/xml');
header('Pragma: public');        
header('Cache-control: private');
header('Expires: -1');

$output .= "<employees>\n";

while($data_sth->fetchInto($DATA)) {
	$status = ($DATA['_status'] == 0) ? 'Current' : 'Inactive';
	$cellStatus = (strlen($DATA['cell_eid']) == 5) ? 'yes' : 'no';
	
$output .= "    <employee id=\"" . $DATA['_eid'] . "\" status=\"" . $status . "\">\n";
$output .= "        <lst>" . caps($DATA['lst']) . "</lst>\n";
$output .= "        <fst>" . caps($DATA['fst']) . "</fst>\n";
$output .= "        <dept id=\"" . caps($DATA['dept']) . "\">" . caps($DATA['_dept']) . "</dept>\n";
$output .= "        <location id=\"" . caps($DATA['Location']) . "\" conbr=\"" . caps($DATA['conbr']) . "\">" . caps($DATA['_location']) .  "</location>\n";
$output .= "        <hire>" . $DATA['hire'] . "</hire>\n";
$output .= "        <description>" . $DATA['Job_Description'] . "</description>\n";
$output .= "        <language>" . $DATA['language'] . "</language>\n";
$output .= "        <email>" . $DATA['email'] . "</email>\n";
$output .= "        <username>" . $DATA['username'] . "</username>\n";
$output .= "        <password>" . $DATA['password'] . "</password>\n";
$output .= "        <cell status=\"" . $cellStatus . "\">\n";
$output .= "          <number>" . str_format_number($DATA['cell_number'], $format_phone) . "</number>\n";
$output .= "          <model>" . $DATA['cell_model'] . "</model>\n";
$output .= "          <cycle>" . $DATA['cell_billCycle'] . "</cycle>\n";
$output .= "          <comments><![CDATA[" . $DATA['cell_comments'] . "]]></comments>\n";
$output .= "        </cell>\n";
$output .= "    </employee>\n";

}

$output .= "</employees>\n";
        
print $output;
?>


<?php 
/**
 * - Display debug information 
 */
include_once('debug/footer.php');
/* 
 * - Disconnect from database 
 */
$dbh->disconnect();
$dbh_standards->disconnect();
?>