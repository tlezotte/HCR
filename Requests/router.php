<?php
/**
 * Human Capital Request
 *
 * router.php sends required emails.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Request
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * PHP Mailer
 * @link http://phpmailer.sourceforge.net/
 */
 

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
 * - Config Information
 */
require_once('../include/config.php'); 



/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting Request information */
$REQUEST = $dbh->getRow("SELECT *
						 FROM Requests r
						   INNER JOIN Position p ON r.positionTitle = p.title_id
						   INNER JOIN Authorization a ON r.id = a.request_id
						 WHERE r.id = ?",array($_GET['request_id']));
/* Getting Authoriztions for above Request */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE request_id = ".$_GET['request_id']);								 				 								   		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */



/* ------------------ START PROCESSING ---------------------------------------------------------------------- */
/* -------------------------------------------------------------
 * ---------- START APPROVER COMMENTS PROCESSING ---------------
 * -------------------------------------------------------------
 */
if ($_GET['yn'] == 'no') {
	$data = getEmployee($REQUEST['req']);															// Get Approver's email
						 
	sendDeny($data['email'], $_GET['request_id'], caps($REQUEST['name']));

	/* Create RSS file or continue to list.php */
	if ($default['rss'] == 'on') {
		$forward = "rss.php";
	} else {
		$forward = "list.php?action=my&access=0";
	}

	if ($debug_page AND $_SERVER['REMOTE_ADDR'] == $default['debug_ip']) {
		echo "email: ".$data['email']."<br>";
		echo "id: ".$_GET['request_id']."<br>";	
		echo "Y AND N SECTION<br><br>";
		exit();
	} else {
		header("Location: ".$forward);
		exit();
	}
}
/* -------------------------------------------------------------
 * ---------- END APPROVER COMMENTS PROCESSING ---------------
 * -------------------------------------------------------------
 */



/* -------------------------------------------------------------
 * ---------- START APPROVAL PROCESSING --------------------- 
 * -------------------------------------------------------------
 */
$reqApprover = substr($_GET['approval'],3);																//Extract PO approval from previous PO

for ($key = ++$reqApprover; $key <= 6; $key++) {
	$nextREQ = 'app'.$key;					//Set Request name
	
	/* Check which PO level for approver */
	if (isset($AUTH[$nextREQ]) and $AUTH[$nextREQ] != 0) {
		$Approver = substr($_GET['approval'],3);														//Extract Request approval from previous Request
		$nextAPP = 'app'.++$Approver;																	//Set Request name
		
		/* Check which Request level for approver */
		if (isset($AUTH[$nextAPP]) and $AUTH[$nextAPP] != 0) {
			$data = getEmployee($AUTH[$nextAPP]);														// Get Approver's email
	  
			$positionTitle=getPositionTitle($REQUEST['title_id'], $REQUEST['request_type']);			// Get Position Title
			
			//$dbh->query("UPDATE Authorization SET level='".$nextPO."' WHERE request_id=".$_GET['request_id']);	// Record next level to approve	
							   
			sendMail($data['email'], $nextAPP, $_GET['request_id'], $positionTitle['title_name']);
				
			/* Create RSS file or continue to list.php */
			if ($default['rss'] == 'on' and $_GET['approval'] == 'app0') {
				$forward = "rss.php";
			} else {
				$forward = "list.php?action=my&access=0";
			}		
	
			if ($debug_page and $_SESSION['eid'] == '08745') {
				echo "email: ".$data['email']."<br>";
				echo "id: ".$_GET['request_id']."<br>";			
				echo "APPROVAL SECTION<br><br>";
				exit();
			} else {
				header("Location: ".$forward);
				exit();
			}
		} 
	}
}

/* ---------- Send approval to Purchasing when there is no more APP's ---------- */
//setRequestStatus($REQUEST['id'], 'A');									// Update PO status
/* -------------------------------------------------------------
 * ---------- END APPROVAL PROCESSING --------------------- 
 * -------------------------------------------------------------
 */


/* -------------------------------------------------------------
 * ---------- START STAFFING PROCESSING --------------------- 
 * -------------------------------------------------------------
 */
$staffing=getPosition('staffing','none');														// Get Staffing Information
$positionTitle=getPositionTitle($REQUEST['title_id'], $REQUEST['request_type']);				// Get Position Title
sendMail($staffing['email'], 'staffing', $_GET['request_id'], $positionTitle['title_name']);	// Send email to Staffing Manager
//$dbh->query("UPDATE Authorization SET level='staffing' WHERE request_id=".$_GET['request_id']);	// Record next level to approve	
/* -------------------------------------------------------------
 * ---------- END STAFFING PROCESSING --------------------- 
 * -------------------------------------------------------------
 */
 	
	
if ($debug_page and $_SESSION['eid'] == '08745') {
	echo "email: ".$data['email']."<br>";
	echo "id: ".$_GET['request_id']."<br>";
	echo "NO MORE APPS SECTION<br><br>";
	exit();
} else {
	header("Location: list.php?action=my&access=0");
	exit();
}
/* ------------------ END PROCESSING --------------------------------------------------------------------------- */


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>