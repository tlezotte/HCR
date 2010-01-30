<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
	exit(0);

require("nusoap/nusoap.php");

$s = new soap_server;
$s->register('get_salary');
$s->register('get_agency');


function get_salary($eid) {
	require_once('../Connections/connDB.php');				//Database Connection
	
	$sql = "SELECT c.salaryType, c.salary, c.overTime, c.doubleTime
			FROM Compensation c
				INNER JOIN Requests r ON r.id = c.request_id
				INNER JOIN Employees e ON e.request_id = c.request_id
			WHERE e.eid = '" . mysql_real_escape_string($eid) . "'";
	
	$results = $dbh->getRow($sql);
	
	$dbh->disconnect();										//Disconnect from database
	
	$results['salary'] = base64_decode($results['salary']);
	$results['overTime'] = base64_decode($results['overTime']);
	$results['doubleTime'] = base64_decode($results['doubleTime']);
	
	return $results;
}

function get_agency($eid) {
	require_once('../Connections/connDB.php');				//Database Connection
	
	$sql = "SELECT a.name
			FROM Compensation c
				INNER JOIN Requests r ON r.id = c.request_id
				INNER JOIN Employees e ON e.request_id = c.request_id
				INNER JOIN Standards.ContractAgency a ON a.id=c.agency
			WHERE e.eid = '" . mysql_real_escape_string($eid) . "'";
	
	$results = $dbh->getOne($sql);
	
	$dbh->disconnect();										//Disconnect from database
	
	return $results;
}

$s->service($HTTP_RAW_POST_DATA); //Executes the RPC
?>