<?php
/**
 * Request System
 *
 * logout.php logs the user out.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */


/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('include/BlackBerry.php');
 
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('include/config.php'); 


/* Show that user is logged out */
$res = $dbh->query("UPDATE Users SET online='00000000000000' WHERE eid = '".$_SESSION['eid']."'");

/* Unsetting all Session variables */
unset($_SESSION['username']);
unset($_SESSION['fullname']);
unset($_SESSION['eid']);
unset($_SESSION['hcr_access']);
unset($_SESSION['hcr_groups']);
unset($_SESSION['vacation']);
unset($_SESSION['error']);
unset($_SESSION['redirect']);
	
/* Unsetting all Cookie variables */	
setcookie(username, $_SESSION['username'], time() - 3600);
setcookie(hcr_access, $_SESSION['hcr_access'], time() - 3600);
setcookie(hcr_groups, $_SESSION['hcr_groups'], time() - 3600);
setcookie(eid, $_SESSION['eid'], time() - 3600);
setcookie(vacation, $_SESSION['vacation'], time() - 3600);
			  	  
header("Location: index.php");

/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();

?>