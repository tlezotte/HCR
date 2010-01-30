<?php
/**
 * - Load Common Functions
 */
include_once('/var/www/Common/PHP/functions.php');	



/* ------------------ START FUNCTIONS ----------------------- */
/**
 * - Check Authorization
 */
function CheckAuth($auth_eid, $auth_yn, $auth_com, $auth_date) {
	if (isset($auth_date)) {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib(' ".date("F d, Y - g:i:s A", strtotime($auth_date))."', CAPTION, 'Submission Date', TEXTPADDING, 10, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C');\" onMouseout=\"return nd();\">".
	       "<img src=\"../images/datetime.gif\" border=\"0\" align=\"absmiddle\"></a>";
	}
	if ($auth_yn == 'yes') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Approved Comments', TEXTPADDING, 10, BGCOLOR, '#006600', CGCOLOR, '#006600');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/approved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} elseif ($auth_yn == 'no') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Non-Approved Comments', TEXTPADDING, 10, BGCOLOR, '#FF0000', CGCOLOR, '#FF0000');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/notapproved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} 
/* 	if (isset($auth_eid)) {
	  echo "<img src=\"../images/waiting.gif\" width=\"18\" height=\"18\" alt=\"Waiting...\">";
	} */
}


/**
 * - Check Resend
 */
function CheckResend($auth_eid, $auth_yn, $auth_com, $auth_date) {
	if (isset($auth_date)) {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib(' ".$auth_date."', CAPTION, 'Submission Date', BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C');\" onMouseout=\"return nd();\">".
	       "<img src=\"../images/calendar.gif\" border=\"0\" align=\"absmiddle\"></a>";
	}
	if ($auth_yn == 'yes') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Approved Comments', BGCOLOR, '#006600', CGCOLOR, '#006600');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/approved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} elseif ($auth_yn == 'no') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Non-Approved Comments', BGCOLOR, '#FF0000', CGCOLOR, '#FF0000');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/notapproved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} 
}


/**
 * - Check Authorization Level
 */
function CheckAuthLevel($auth_level) {
	echo ($auth_level < 0) ? disabled : $blank;
}


/**
 * - Reset Session
 */
function clearSession() {
	/* 
	Set Session variables to regular variables
	Session variables will be unset at end of page
	but "username", "eid" and "access" will be reset
	*/
	$get_fullname = $_SESSION['fullname'];
	$get_username = $_SESSION['username'];
	$get_access = $_SESSION['hcr_access'];
	$get_eid = $_SESSION['eid'];
	$get_group = $_SESSION['hcr_groups'];
	$get_vacation = $_SESSION['vacation'];
		
	/* Unsets current session variables */
	session_unset();
	
	/* Reset username and access so the user does not need to relogin */
	$_SESSION['fullname'] = $get_fullname;
	$_SESSION['username'] = $get_username;
	$_SESSION['hcr_access'] = $get_access;
	$_SESSION['eid'] = $get_eid;
	$_SESSION['hcr_groups'] = $get_group;
	$_SESSION['vacation'] = $get_vacation;	
}	


/**
 * - Get the status fullname 
 */
function labelStatus($id) {
	switch ($id) {
	case N:
	   $status = "New";
	   break;
	case A:
	   $status = "Approved";
	   break;
	case X:
	   $status = "Denied";
	   break;
	case C:
	   $status = "Canceled";
	   break;  
	case O:
	   $status = "Completed";
	   break;	          
	}
	
	return $status;
}


/**
 * - Get the status fullname 
 */
function requestType($id) {
	switch ($id) {
	case 'new':
	   $status = "New Hire";
	   break;
	case 'adjustment':
	   $status = "Wage Adjustment";
	   break;
	case 'transfer':
	   $status = "Transfer";
	   break;
	case 'conversion':
	   $status = "Contract Conversion";
	   break; 	    	      
	case 'promotion':
	   $status = "Promotion";
	   break;    
	}
	
	return $status;
}


/**
 * - Convert Request Status
 */
function reqStatus($status) {

	switch ($status) {
		case N: $output = "New"; break;
		case A: $output = "Approved"; break;
		case X: $output = "Denied"; break;
		case C: $output = "Canceled"; break;         
	}
	
	return $output;
}


/**
 * - Submit the technology information to Lotus Notes
 */
function submitTechnologyToNotes($request_id) {
	global $dbh;

	/* Get technology information */
	$REQUEST = $dbh->getRow("SELECT r.id, CONCAT(m.fst, ' ', m.lst) AS name, p.name AS plant, d.name AS department, r.replacement, t.tech_computer, t.tech_printer, concat(ee.fst, ' ', ee.lst) AS app3, p.title_name AS positionTitle, t.tech_notesID, t.tech_as400, t.tech_vpn, r.startDate
							 FROM Requests r
								 INNER JOIN Technology t ON t.request_id=r.id
								 INNER JOIN Authorization a ON a.request_id=r.id
								 INNER JOIN Employees m ON m.request_id=r.id
								 INNER JOIN Standards.Employees e ON e.eid=r.req
								 INNER JOIN Standards.Employees ee ON ee.eid=a.app3
								 INNER JOIN Standards.Plants p ON p.id=r.plant
								 INNER JOIN Standards.Department d ON d.id=r.department
								 INNER JOIN Position p ON p.title_id=r.positionTitle
							 WHERE r.id=".$request_id);
	
	/* -- Check to see if Request needs to be sent to Notes -- */
	if ($REQUEST['tech_transfer'] != '0' OR $REQUEST['tech_computer'] == 'yes' OR $REQUEST['tech_printer'] == 'yes' OR $REQUEST['tech_notesID'] == 'yes' OR $REQUEST['tech_as400'] == 'yes') {
	
		$computer = ($REQUEST['tech_computer'] == 'no') ? 'no' : 'yes';
		$computerType = ($REQUEST['tech_computer'] != 'no') ? rawurlencode($REQUEST['tech_computer']) : '';
		
		/* Submit Technology information to Lotus Notes */
		$request="http://corpapps.corp.yourcompany.com/Corporate Databases/Computer.nsf/(phpagent)?OpenAgent".
														"&login&username=Staff%20Requester".
														"&password=staffrequester".
														"&HCR=" . rawurlencode($REQUEST['id']) . "".
														"&UName=" . rawurlencode(ucwords(strtolower($REQUEST['name']))) . "".
														"&Plant=" . rawurlencode(ucwords(strtolower($REQUEST['plant']))) . "".
														"&Department=" . rawurlencode(ucwords(strtolower($REQUEST['department']))) . "".
														"&Transfer=" . rawurlencode(ucwords(strtolower($REQUEST['replacement']))) . "".
														"&Computer=" . $computer . "".
														"&ComputerType=" . $computerType . "".
														"&Printer=" . rawurlencode($REQUEST['tech_printer']) . "".
														"&Manager=" . rawurlencode(ucwords(strtolower($REQUEST['app3']))) . "".
														"&posistionTitle=" . rawurlencode(ucwords(strtolower($REQUEST['positionTitle']))) . "".
														"&NotesID=" . rawurlencode($REQUEST['tech_notesID']) . "".
														"&AS400=" . rawurlencode($REQUEST['tech_as400']) . "".
														"&vpn=" . rawurlencode($REQUEST['tech_vpn']) . "".
														"&StartDate=" . rawurlencode($REQUEST['startDate']) . "";
		
		// Send out Tech to Notes URL
		sendGeneric('tlezotte@yourcompany.com', 'Tech to Notes', 'Tech to Notes', $request);
		
		// Initialize the session
		$session = curl_init($request);
		
		// Set curl options
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, false);
		
		// Make the request
		curl_exec($session);
		
		// Close the curl session
		curl_close($session);
	}
}


/**
 * - Format optionalSoftware variable for session variables
 */
function formatSoftware($software) {
	while (list($key, $val) = each($software)) {
	   $optionalSoftware .= "$val:";
	}
	
	$formatted = preg_replace("/:$/", "", $optionalSoftware);
	
	return $formatted;
}


/**
 * - Get Position Information
 */
function getPosition($position, $plant) {
	global $dbh;
	
	$value=($plant == 'none') ? 1 : $plant;		// Set status to active (1) if no plant is provided
	
	$data = $dbh->getRow("SELECT * 
						  FROM Users u, Standards.Employees e
						  WHERE u.eid=e.eid
						    AND u.$position='$value'
						  LIMIT 1");
	
	return $data;
}


/**
 * - Register a new employee in the Job Tracking System
 */
function registerJobTracking($eid) {
	global $REQUEST;						// Load Request data
	global $COMPA;							// Load Compensation data
	
	require('../Connections/jobTracking.php');					// Load Job Tracking database information
	
	$employee = getEmployee($REQUEST['eid']);										// Get employee information
	$interface=($REQUEST['department'] == '57') ? '1|2|10|6|8' : '5|2|3|7|4';		// Set JobTracking interface
	
	/* Add user to Job Tracking */
	$sql1="INSERT INTO JTS_users (eid, dept,level_access, status,Contract_House_ID, display_interfaces ) VALUES ('".$employee['username']."','".$REQUEST['department']."','4','0','".$COMPA['agency']."','".$interface."')";
	$dbh_jobTracking->query($sql1);
	
	/* Add new user to Job Tracking department list */
	$sql2="INSERT INTO JTS_users_Dept (eid,dept) VALUES ('".$employee['username']."', '".$REQUEST['department']."')";
	$dbh_jobTracking->query($sql2);
}


/**
 * - Register a new employee in the Job Tracking System
 */
function registerPurchaseRequest($eid) {
	require('../Connections/purchaseRequest.php');						// Load Purchase Request database information
	
	$sql="INSERT INTO Users (eid) VALUES (".$eid.")";
	$dbh_prs->query($sql);												//Add user to system
	
	History($_SESSION['eid'], 'add', $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));				// Record transaction for history
}


/**
 * - Next employee ID
 */
function nextID($database) {
	global $dbh_standards;	
	
	/* Get last ID number */
	$ID = $dbh_standards->getOne("SELECT MAX(id) FROM " . $database);
	$EID = $ID+1;
	
	/* Store latest Contract ID number */
	$id_sql="INSERT INTO " . $database . " VALUES ('" . $EID . "')";
	$dbh_standards->query($id_sql);
	
	return $EID;
}


/**
 * - Get Position Title
 */
function getPositionTitle($position, $request_type) {
	global $dbh;
								   						   
	if ($request_type != 'new') {
		$pt=explode(":", $position);
		$position=$pt[1];
	}

	/* Getting position information */								
	$sql="SELECT * FROM Position WHERE title_id=" . $position;
	$data = $dbh->getRow($sql);
	
	return $data;
}


function showMailIcon($approver, $approver_eid, $approver_name, $request_id) {
	$html="<a href=\"resend.php?approval=$approver&eid=$approver_eid&request_id=$request_id\" title=\"User Action|Resend request to $approver_name\"><img src=\"../images/resend_email.gif\" width=\"19\" height=\"16\" border=\"0\" align=\"absmiddle\"></a>";
	
	return $html;
}

function showCommentIcon($approver, $approver_name, $request_id) {
	$html="<a href=\"comments.php?action=add&eid=$approver&request_id=$request_id&type=private\" title=\"Message Center|Send private message to $approver_name\" onclick=\"return GB_show(this.title, this.href, 350, 450)\"><img src=\"../images/comments.gif\" width=\"19\" height=\"16\" border=\"0\" align=\"absmiddle\"></a>";
	
	return $html;
}

function displayAppComment($level, $action_level, $auth, $current_comment, $date) {
	$display = is_null($data) ? display : none;			// Check to see if approver, approved Request
	$appCom = $action_level . "Com";					// Set Comment variable
	
	if ($level == $action_level AND $_SESSION['eid'] == $auth AND $display == 'display') {
		$comment = "<input name=\"comment\" type=\"text\" size=\"50\" maxlength=\"75\">";
	} else {
		$comment = stripslashes(ucwords(strtolower($current_comment)));
	}
	
	return $comment;												  
} 

function displayAppButtons($id, $level, $action_level, $auth, $date) {
	$display = is_null($data) ? display : none;			// Check to see if approver, approved Request

	// Check Approver to Logged in user
	if ($level == $action_level AND $_SESSION['eid'] == $auth) {
$output = <<< END_OF_HTML
	<div id="cmdSubmit" style="display:$display">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	  <td><input name="yes" type="image" src="/Common/images/gtk-yes.gif" border="0"></td>
	  <td><input name="no" type="image" src="/Common/images/gtk-no.gif" border="0"></td>
	  <!--<td><input name="hold" type="image" src="/Common/images/gtk-pause.gif" border="0" title="Put a Hold on this Request"></td>-->
	</tr>
	</table>
<!--	<input name="request_id" type="hidden" value="$id">
	<input name="auth" type="hidden" value="$level">
	<input name="stage" type="hidden" value="update">-->
	</div>
END_OF_HTML;
	} else {
		$output = "&nbsp;";
	}
	
	return $output;
}

/* Format SSN number */
function format_ssn($ssn) {
	$ssn_clean = str_replace("-", "", $ssn);					// remove any hash marks
	$one = substr($ssn_clean, 0, 3);							// get first 3 numbers
	$two =  substr($ssn_clean, 3, 2);							// get next 2 numbers
	$three =  substr($ssn_clean, 5, 4);							// get last 4 numbers	
	
	return base64_encode($one . "-" . $two . "-" . $three);		// return encoded SSN number
}

/* Set available Approvers to switch at each level */
function getApprovers($AUTH, $TYPE) {
	global $_POST;				// Get $_POST
	
	switch ($AUTH) {
		case 'app1':
		   $APPROVERS  = "app2='".mysql_real_escape_string($_POST['app2'])."',";
		   $APPROVERS .= "app4='".mysql_real_escape_string($_POST['app4'])."',";
		   $APPROVERS .= "app5='".mysql_real_escape_string($_POST['app5'])."',";
		   $APPROVERS .= "app6='".mysql_real_escape_string($_POST['app6'])."',";
		   $APPROVERS .= "app7='".mysql_real_escape_string($_POST['app7'])."',";
		   $APPROVERS .= "app8='".mysql_real_escape_string($_POST['app8'])."',";
		   $APPROVERS .= "level='app2'";
		break;		
		case 'app2':
		   $APPROVERS  = "app4='".mysql_real_escape_string($_POST['app4'])."',";
		   $APPROVERS .= "app5='".mysql_real_escape_string($_POST['app5'])."',";
		   $APPROVERS .= "app6='".mysql_real_escape_string($_POST['app6'])."',";
		   $APPROVERS .= "app7='".mysql_real_escape_string($_POST['app7'])."',";
		   $APPROVERS .= "app8='".mysql_real_escape_string($_POST['app8'])."',";
		   $APPROVERS .= "level='app3'";
		break;	
		case 'app3':
		   $APPROVERS  = "app4='".mysql_real_escape_string($_POST['app4'])."',";
		   $APPROVERS .= "app5='".mysql_real_escape_string($_POST['app5'])."',";
		   $APPROVERS .= "app6='".mysql_real_escape_string($_POST['app6'])."',";
		   $APPROVERS .= "app7='".mysql_real_escape_string($_POST['app7'])."',";
		   $APPROVERS .= "app8='".mysql_real_escape_string($_POST['app8'])."',";
		   $APPROVERS .= "level='app4'";
		break;			
		case 'app4':
		   $APPROVERS  = "app5='".mysql_real_escape_string($_POST['app5'])."',";
		   $APPROVERS .= "app6='".mysql_real_escape_string($_POST['app6'])."',";
		   $APPROVERS .= "app7='".mysql_real_escape_string($_POST['app7'])."',";
		   $APPROVERS .= "app8='".mysql_real_escape_string($_POST['app8'])."',";
		   $APPROVERS .= "level='app5'";
		break;
		case 'app5':
		   $APPROVERS  = "app6='".mysql_real_escape_string($_POST['app6'])."',";
		   $APPROVERS .= "app7='".mysql_real_escape_string($_POST['app7'])."',";
		   $APPROVERS .= "app8='".mysql_real_escape_string($_POST['app8'])."',";
		   $APPROVERS .= "level='app6'";
		break;
		case 'app6':
		case 'app7':
		case 'app8':
			if ($TYPE = 'new') {
			   $APPROVERS = "level='staffing'";
			} else {
			   $APPROVERS = "level='staffing', status='A'";
			}
		break;											
	}	
	
	return $APPROVERS;
}


/**
 * - Display approvers name for Approvals area
 */
function displayApprover($id, $stage, $approver, $date) {
	global $dbh;
	
	/* Set SQL level from $approver */
	switch ($stage) {
		case 'app1': $level='one'; break;
		case 'app2': $level='two'; break;
		case 'app3': $level='one'; break;
		case 'app4': $level='four'; break;
		case 'app5': $level='five'; break;
		case 'app6': $level='six'; break;
		case 'app7': $level='seven'; break;
		case 'app8': $level='eight'; break;
		case 'controller': $level='controller'; break;
	}
	
	/* Getting approver from Users */
	if ($level != 'controller') {							 
		$query = $dbh->prepare("SELECT U.eid, E.fst, E.lst
								FROM Users U
								 INNER JOIN Standards.Employees E ON U.eid = E.eid
								WHERE U.$level = '1' AND U.status = '0' AND E.status = '0'
								ORDER BY E.lst ASC");
	} else {
		$query = $dbh->prepare("SELECT distinct E.eid, E.fst, E.lst
								FROM Standards.Controller c
								 INNER JOIN Standards.Employees E ON E.eid=c.controller
								WHERE E.status = '0'
								ORDER BY E.lst ASC");
	}
	
	/* Generate HTML output */
	if (is_null($date)) {
		$output  = "<select name=\"$stage\" id=\"$stage\">";
		$output .= "	<option value=\"0\">Select One</option>";
		
		$sth = $dbh->execute($query);
		while($sth->fetchInto($DATA)) {
			$selected = ($approver == $DATA['eid']) ? selected : $blank;
			$output .= "	<option value=\"" . $DATA['eid'] . "\" " . $selected . ">" . caps($DATA['lst'] . ", " . $DATA['fst']) . "</option>";
		}
		
		$output .= "</select>";
	} else {
		$sql = "SELECT e.eid, CONCAT(e.fst, ' ', e.lst) AS fullname
				FROM Authorization a
					INNER JOIN Standards.Employees e on a.$stage=e.eid
				WHERE a.request_id=" . $id; 
		$DATA = $dbh->getRow($sql); 

		$output  = caps($DATA['fullname']);
		$output .= "<input name=\"" . $stage . "\" type=\"hidden\" value=\"" . $DATA['eid'] . "\" />";			
	}

	return $output;	
}


/**
 * - Set the Authorization level for Approvals
 */
function setAuthLevel($id, $level) {
	global $dbh;
	
	$auth_sql="UPDATE Authorization SET level='" . $level . "' WHERE request_id=" . $id;
	$dbh->query($auth_sql);
	
	// Record transaction for history
	debug_capture($_SESSION['eid'], $id, $default['debug_capture'], $_SERVER['PHP_SELF'], addslashes(htmlentities($auth_sql)));				
}



/**
 * - Set the Request status
 */
function setRequestStatus($id, $status) {
	global $dbh;
	
	$status_sql="UPDATE Requests SET status='". $status ."' WHERE id=" . $id;
	$dbh->query($status_sql);
	
	// Record transaction for history
	debug_capture($_SESSION['eid'], $id, $default['debug_capture'], $_SERVER['PHP_SELF'], addslashes(htmlentities($status_sql)));	
}


/*function uploadFile() {
	$exp_file = explode(".",$_FILES['file']['name']);
	$file_ext = end($exp_file);
	$store = $default['files_store'];								//Store uploaded files to this directory
	$dest = $store."/".$PO_ID.".".$file_ext;
	$source = $_FILES['file']['tmp_name'];
	if (file_exists($source)) {
		if (is_writable($default['PO_UPLOAD'])) {
			copy($source, $dest);							//Copy temp upload to $store
		} else {
			$_SESSION['error'] = "Cannot upload file (".$_FILES['file']['name'].")";
			$_SESSION['redirect'] = "http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];
			
			header("Location: ../error.php");
		}
	}	
}*/
/* ------------------ END FUNCTIONS ----------------------- */	


/**
 * - Load Email Functions
 */
include_once('functionsEmail.php');	
/**
 * - Load Calendar Functions
 */
include_once('functionsCalendar.php');		
?>