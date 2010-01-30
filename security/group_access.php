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
if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') {

} else {
	$_SESSION['error'] = "You are not authorized to access this area";
	
	header("Location: ../error.php");
	exit();
}
?>