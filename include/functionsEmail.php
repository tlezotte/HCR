<?php
/**
 * - Load Common Email Functions
 */
include_once('/var/www/Common/PHP/functionsEmail.php');	


/**
 * -------- Message Layout 2 ---------------------------------------------------------------------------
 */	
function message2($BODY, $URL) {
	global $default;
	global $style;

$htmlBody = <<< END_OF_HTML
$style
</head>

<body>
<table width="640" border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="$default[URL_HOME]/images/email_header2.gif" width="646" height="74"></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <br>
	  <blockquote>$BODY</blockquote>
	  <br>
	  <br>		
    </td>
  </tr>
  <tr>
	<td class="header" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Employee Information Link</b></td>
  </tr>
  <tr>		
    <td class="message">
	  <br>
	  <blockquote><a href="$URL">$URL</a></blockquote>
	  <br>
    </td>
  </tr>
</table>
</body>
</html>
END_OF_HTML;

	return $htmlBody;
}


/**
 * -------- Message Layout 3 ---------------------------------------------------------------------------
 */	
function message3($BODY, $URL, $COMMENTS) {
	global $default;
	global $style;

$htmlBody = <<< END_OF_HTML
$style
</head>

<body>
<table width="640" border="0" align="center" cellspacing="0" cellpadding="0" >
  <tr>
    <td><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <br>
	  <blockquote>$BODY</blockquote>
	  <br>
	  <br>		
    </td>   
  <tr>
	<td class="header" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$default[title1] Link</b></td>
  </tr>
  <tr>		
    <td class="message">
	  <br>
	  <blockquote><a href="$URL">$URL</a></blockquote>
	  <br>
    </td>
  </tr>
  <tr>
    <td class="header"><span class="header">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Comments</strong></span></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <blockquote>$COMMENTS</blockquote>
	  <br>
    </td>
  </tr>
</table>
</body>
</html>
END_OF_HTML;

	return $htmlBody;
}



/* ------------------ START EMAIL FUNCTIONS ----------------------- */
/**
 * -------- Send out generic message ---------------------------------------------------------------------------
 */		
function sendGeneric2($sendTo, $subject, $message_body, $url) {
	global $default;
	global $language;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = "Human Resources";
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = $subject;

	$htmlBody = message2($message_body, $url);		

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/**
 * -------- Send out email for approval ---------------------------------------------------------------------------
 */		
function sendMail($sendTo,$request_level,$request_id,$positionTitle) {
	global $default;
	global $language;

/* HTML message */	
$message_body = <<< END_OF_BODY
You have a new Human Capital Request to <b>Approve</b>.<br>
The position title for this request is: <b>$positionTitle</b><br>
END_OF_BODY;
				
	$url = $default['URL_HOME']."/Requests/detail.php?id=".$request_id."&approval=".$request_level;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $language['label']['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HC-".$request_id.": ".$positionTitle;

	$htmlBody = message1($message_body, $url);		

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/**
 * -------- Send denied email --------------------------------------------------------------------------- 
 */	
function sendDeny($sendTo,$request_id,$positionTitle) {
	global $default;
	global $language;

/* HTML message */	
$message_body = <<< END_OF_BODY
Human Capital Request HC-$request_id has been <i>DENIED</i>.<br>
The position title for this request is: <b>$positionTitle</b><br>
END_OF_BODY;
					
	$url = $default['URL_HOME']."/Requests/detail.php?id=".$request_id;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $language['label']['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HC-".$request_id.": DENIED - ".$positionTitle;

	$htmlBody = message1($message_body, $url);		

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/** 
 * -------- Send approved email ---------------------------------------------------------------------------
 */	
function sendApproved($sendTo,$request_id,$positionTitle,$REQUESTNum) {
	global $default;
	global $language;

/* HTML message */	
$message_body = <<< END_OF_BODY
Human Capital Request HC-$request_id has been approved.<br>
The position title for this request is: <b>$positionTitle</b><br>
END_OF_BODY;
			
	$url = $default['URL_HOME']."/Requests/detail.php?id=".$request_id;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $language['label']['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HC-".$request_id.": ".$positionTitle;

	$htmlBody = message1($message_body, $url);		

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}



/** 
 * -------- Send approved email ---------------------------------------------------------------------------
 */	
function sendCoordinator($sendTo, $request_id, $fullname) {
	global $default;
	global $language;

/* HTML message */	
$message_body = <<< END_OF_BODY
<b>$fullname</b> has been approved for hire, now needs to complete a physical and drug screening.<br>
Select the link below to get <b>$fullname</b>&#39;s contact information.<br>
END_OF_BODY;
			
	$url = $default['URL_HOME']."/Requests/detail.php?id=".$request_id."&approval=coordinator";
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $language['label']['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HC-".$request_id.": New Employee for Processing";

	$htmlBody = message1($message_body, $url);			

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}



/** 
 * -------- Send approved email ---------------------------------------------------------------------------
 */	
function sendCoordinatorStatus($sendTo, $request_id, $fullname, $action) {
	global $default;
	global $language;
	
	$format_action=($action == 'approved') ? 'Approved' : 'Not Approved';

/* HTML message */	
$message_body = <<< END_OF_BODY
$fullname was <b>$format_action</b> for hire at your company.<br>
<br>
Check the comments areas for more information.<br>
END_OF_BODY;

	$url = $default['URL_HOME']."/Requests/detail.php?id=".$request_id;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $language['label']['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HC-".$request_id.": ".$format_action." - Approval Process is Complete";

	$htmlBody = message1($message_body, $url);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/** 
 * -------- Send Employee ID generation email ---------------------------------------------------------------------------
 */	
function sendGenerateEID($sendTo, $request_id, $fullname) {
	global $default;
	global $language;

/* HTML message */				
$message_body = <<< END_OF_BODY
<b>$fullname</b> is a new employee at your company.<br>
Do you want to generate an Employee ID for <b>$fullname</b>?<br>
<br>
<img src="$default[URL_HOME]/images/personal.gif" width="16" height="16" align="absmiddle" border="0" class="dark">&nbsp;<a href="$default[URL_HOME]/Requests/summary.php?id=$request_id&approval=generator"><b>Generate Employee ID</b></a><br>
END_OF_BODY;
	
	$url = $default['URL_HOME']."/Requests/detail.php?id=".$request_id;
			  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $language['label']['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HC-".$request_id.": Generate New Employee ID";

	$htmlBody = message1($message_body, $url);
	
	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/**
 * -------- Send out notice of new employee -----------------------------------------------
 */	
function sendNewEmployee($sendTo,$fullname,$eid,$plant,$department,$bcc) {
	global $default;
	
	$now = date("F j, Y, g:i a");

/* HTML message */				
$message_body = <<< END_OF_BODY
The following employee has been added to the Employee database.<br>
<br>
Full Name: <b>$fullname</b><br>
Employee ID: <b>$eid</b><br>
Plant: <b>$plant</b><br>
Department: <b>$department</b><br>
Date: <b>$now</b><br>
END_OF_BODY;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['employee_title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	if ($bcc == 'on') {
		$default['newGroup'];
	}
	$mail->Subject = "Employee Information for ".$fullname;

	$htmlBody = message1($message_body, $default['employee_url']);

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/**
 * -------- Send out notice of disabled employee -----------------------------------------------
 */		
function sendDisabledEmployee($fullname,$eid,$plant,$department,$bcc) {
	global $default;
	
	$now = date("F j, Y, g:i a");

/* HTML message */				
$message_body = <<< END_OF_BODY
The following employee has been <b>DISABLED</b> in the Employee database.<br>
<br>
Full Name: <b>$fullname</b><br>
Employee ID: <b>$eid</b><br>
Plant: <b>$plant</b><br>
Department: <b>$department</b><br>
Date: <b>$now</b><br>
END_OF_BODY;
	
	$url = "http://a2.yourcompany.com/go/Employees/index.php";
			  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['employee_title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress('tlezotte@yourcompany.com', 'IT Group');
	if ($bcc == 'on') {
		$default['disableGroup'];
	}
	$mail->Subject = "Disabled Employee Information for ".$fullname;

	$htmlBody = message1($message_body, $default['employee_url']);

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/** 
 * -------- Resend approved email -----------------------------------------------
 */	
function sendResend($sendTo,$APPROVAL,$REQUEST_ID,$POSITION) {
	global $default;
	global $COMMENTS;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];	
	$app5 = $COMMENTS[app5][0];
	$app5Com = $COMMENTS[app5][1];
	$app6 = $COMMENTS[app6][0];
	$app6Com = $COMMENTS[app6][1];
	$app7 = $COMMENTS[app7][0];
	$app7Com = $COMMENTS[app7][1];	

/* Email message */			
$message_body = <<< END_OF_HTML
You have a new $default[title1] to <b>Approve</b>.<br>
The position title for this Request is: <b>$POSITION</b><br>
END_OF_HTML;

	/* Request URL */
	$url = $default['URL_HOME']."/Requests/detail.php?id=".$REQUEST_ID."&approval=".$APPROVAL;

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
$app5 &quot;$app5Com&quot;<br>
$app6 &quot;$app6Com&quot;<br>
$app7 &quot;$app7Com&quot;<br>
END_OF_HTML;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "HCR: ".$POSITION;

	$htmlBody = message3($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	


/** 
 * -------- Send Desk Coordinator -----------------------------------------------
 */	
function sendDeskCoordinator($request_id, $plant) {
	global $default;
	
	$desk=getPosition('desk', $plant);																// Get Desk Coordinator Information
	$sendTo = $desk['email'];																		// Send message to
	$subject = $default['title1'] . " - Desk Assignment";												// Email subject
	
/* Message body */
$message_body = <<< END_OF_BODY
There is a new employee at your company. They need<br>
a desk location assigned to them.<br>
END_OF_BODY;

	$url = $default['URL_HOME']."/Requests/summary.php?id=".$request_id."&approval=desk";	// Message URL
	
	
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = $subject;
	
	$htmlBody = message1($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();	
}


/** 
 * -------- Send Desk Coordinator -----------------------------------------------
 */	
function sendPhoneTechnician($request_id, $plant) {
	global $default;
	
	$sendTo = "tholiva@yourcompany.com,dstepke@yourcompany.com";
	//$sendTo = $desk['email'];																			// Send message to
	$subject = $default['title1'] . " - Desk Phone";														// Email subject
	
/* Message body */
$message_body = <<< END_OF_BODY
There is a new employee at your company. They have<br>
a desk phone requested for them.<br>
END_OF_BODY;

	$url = $default['URL_HOME']."/Requests/summary.php?id=".$request_id;		// Message URL
	
	
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress("tholeva@yourcompany.com", "Tim Holeva");
	$mail->AddAddress("dstepke@yourcompany.com", "Dave Stepke");
	$mail->AddAddress("fciavarr@yourcompany.com", "Frank Ciavarro");
	$mail->AddCC("kmccaffr@yourcompany.com", "Karen McCaffrey");
	$mail->Subject = $subject;
	
	$htmlBody = message1($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();	
}


/** 
 * -------- Send Desk Coordinator -----------------------------------------------
 */	
function sendCellularCoordinator($request_id, $plant) {
	global $default;
	
	$sendTo = "kmccaffr@yourcompany.com";									// Send message to
	$subject = $default['title1'] . " - Cellular and Blackberry";				// Email subject
	
/* Message body */
$message_body = <<< END_OF_BODY
There is a new employee at your company. They have<br>
a Blackberry and/or cellular phone requested for them.<br>
END_OF_BODY;

	$url = $default['URL_HOME']."/Requests/summary.php?id=".$request_id."&approval=communication";		// Message URL
	
	
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = $subject;
	
	$htmlBody = message1($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();	
}


/** 
 * -------- Send for Blackberry request -----------------------------------------------
 */	
function sendBlackberryRequest($request_id) {
	global $default;
	
	$sendTo = "asteuer@yourcompany.com";									// Send message to																		
	$subject = $default['title1'] . " - Blackberry Request";					// Email subject
	
/* Message body */
$message_body = <<< END_OF_BODY
There is currently a new employee request.<br>
The new employees manager has requested a Blackberry.<br>
END_OF_BODY;

	$url = $default['URL_HOME']."/Requests/summary.php?id=".$request_id;		// Message URL
	
	
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = $subject;
	
	$htmlBody = message1($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();	
}
/* ------------------ END EMAIL FUNCTIONS ----------------------- */	
?>