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
 * - Access to Request
 */
require_once('../security/group_access.php');
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



header ("content-type: text/xml");

$output  = <<< XML
<ResultSet
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns="urn:yahoo:lcl"
        xsi:schemaLocation="urn:yahoo:lcl http://api.local.yahoo.com/LocalSearchService/V2/LocalSearchResponse.xsd"
        totalResultsAvailable="$num_rows"
        totalResultsReturned="$num_rows"
        firstResultPosition="1">
<sql><![CDATA[
              $data_sql
	   ]]></sql>	
XML;

$output .= "\n";

while($data_sth->fetchInto($DATA)) {
	$status = ($DATA['_status'] == 0) ? 'Current' : 'Inactive';
	$cellStatus = (strlen($DATA['cell_eid']) == 5) ? 'yes' : 'no';
	
$output .= "    <Result>\n";
$output .= "       <eid>" . $DATA['_eid'] . "</eid>\n";
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
$output .= "        <status>" . $status . "</status>\n";
$output .= "        <cell status=\"" . $cellStatus . "\">\n";
$output .= "          <cellStatus>" . $cellStatus . "</cellStatus>\n";
$output .= "          <cellNumber>" . str_format_number($DATA['cell_number'], $format_phone) . "</cellNumber>\n";
$output .= "          <cellModel>" . $DATA['cell_model'] . "</cellModel>\n";
$output .= "          <cellCycle>" . $DATA['cell_billCycle'] . "</cellCycle>\n";
$output .= "          <cellComments><![CDATA[" . $DATA['cell_comments'] . "]]></cellComments>\n";
$output .= "        </cell>\n";
$output .= "    </Result>\n";

}

$output .= "</ResultSet>\n";
        
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