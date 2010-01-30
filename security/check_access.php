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

 
/* ----- CHECK USER LOGIN and ACCESS ----- */
if (is_null($_SESSION['username']) or $_SESSION['hcr_access'] <= 1) {
	$_SESSION['error'] = "Login Required";	
	header("Location: ../index.php");
} else {
	/* ---- Record time visited for Online status ---- */
	MarkOnline();	
}
?>