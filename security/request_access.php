<?php
/**
 * Request System
 *
 * check_access.php check for user access.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Security
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */

/**
 * - Security related functions
 */
require_once('functions.php');
/**
 * - Set debug mode
 */
$debug_page = false;


$ID = (array_key_exists('id', $_GET)) ? $_GET['id'] : $_POST['request_id'];		// Set Request ID

/* Check to see if Viewer is invalved in this Request */
$sql = "SELECT a.id
		 FROM Authorization a
		   INNER JOIN Requests r ON a.request_id=r.id
		 WHERE a.request_id='$ID'
		   AND (r.req = '$_SESSION[eid]'
			OR a.app1 = '$_SESSION[eid]'
			OR a.app2 = '$_SESSION[eid]'
			OR a.app3 = '$_SESSION[eid]'
			OR a.app4 = '$_SESSION[eid]'
			OR a.app5 = '$_SESSION[eid]'
			OR a.app6 = '$_SESSION[eid]'
			OR a.app7 = '$_SESSION[eid]')";
$request = $dbh->getRow($sql);


/* Debug Section */
if ($debug_page) {
	echo "SQL: " . $sql . "<br><br>";
	echo "request['id']: " . $request['id'] . "<br><br>";
	echo "ID: " . $ID . "<br><br>";
	echo "_SESSION['hcr_groups']: " . $_SESSION['hcr_groups'] . "<br><br>";
	echo (isset($request)) ? ACCESS : DENIED;
	//exit();
}


/* ----- CHECK USER LOGIN and ACCESS ----- */
if (isset($request) OR $_SESSION['hcr_groups'] == 'hr' OR $_SESSION['hcr_groups'] == 'ex') {
	echo "";
} else {
	$_SESSION['error'] = "Your are not authorized to view this Request";
	
	header("Location: ../error.php");
	exit();
}
?>