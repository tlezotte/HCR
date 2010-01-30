<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
	exit(0);

require("nusoap/nusoap.php");

$s = new soap_server;
$s->register('get_departments');
$s->register('get_locations');
$s->register('get_positionTitles');


function get_departments() {
	require_once('../Connections/connDB.php');				//Database Connection
	
	$sql = "SELECT * 
			FROM Standards.Department 
			WHERE status='0' 
			ORDER BY name ASC";
	
	$results = $dbh->getRow($sql);
	
	$dbh->disconnect();										//Disconnect from database
	
	return $results;
}

function get_locations() {
	require_once('../Connections/connDB.php');				//Database Connection
	
	$sql = "SELECT * 
			FROM Standards.Plants 
			WHERE status = '0'
			ORDER BY name ASC";
	
	$results = $dbh->getOne($sql);
	
	$dbh->disconnect();										//Disconnect from database
	
	return $results;
}

function get_positionTitles() {
	require_once('../Connections/connDB.php');				//Database Connection
	
	$sql = "SELECT * 
			FROM Position 
			WHERE title_status='0'
			ORDER BY (grade + 0) ASC, title_name ASC";
	
	$results = $dbh->getOne($sql);
	
	$dbh->disconnect();										//Disconnect from database
	
	return $results;
}


$s->service($HTTP_RAW_POST_DATA); //Executes the RPC
?>