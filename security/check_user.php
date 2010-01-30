<?php
/**
 * Human Capital System
 *
 * check_user.php check to see if user has logged in.
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
 * - Security functions
 */ 
include('functions.php');


/* ----- CHECK USER LOGIN ----- */
if ($default['maintenance'] == 'on' AND $_SESSION['hcr_access'] != '3') {
	unset($_SESSION['username']);
	unset($_SESSION['eid']);
	unset($_SESSION['hcr_access']);
	
	header("Location: ../index.php");
	exit();
}

if (is_null($_SESSION['username'])) {
	$_SESSION['error'] = "Unauthorized Area - Please Login";
	$_SESSION['redirect'] = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	header("Location: ../index.php");
	exit();
} else {
	/* ---- Record time visited for Online status ---- */
	MarkOnline();	
}
?>