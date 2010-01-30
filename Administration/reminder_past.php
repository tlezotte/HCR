<?php
/**
 * Request System
 *
 * reminder_past.php sends out a reminder to requests older than >60.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../include/Timer.php');
$starttime = StartLoadTimer();
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
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


$po_query = "SELECT p.id, p.purpose, p.req, p.reqDate, a.app1, a.app1Date, a.app2, a.app2Date, a.app3, a.app3Date, a.staffing, a.staffingDate,
			 TO_DAYS(NOW()) - TO_DAYS(p.reqDate) AS curReq,
			 TO_DAYS(NOW()) - TO_DAYS(a.app1Date) AS curApp1,
			 TO_DAYS(NOW()) - TO_DAYS(a.app1Date) AS curApp1Staffing,
			 TO_DAYS(NOW()) - TO_DAYS(a.app2Date) AS curApp2Staffing,
			 TO_DAYS(NOW()) - TO_DAYS(a.app3Date) AS curApp3Staffing
			FROM Authorization a, PO p
			WHERE p.id = a.request_id and p.reqDate < DATE_SUB(NOW(), INTERVAL 60 DAY) and p.po IS NULL and p.status <> 'C'
			ORDER BY p.reqDate DESC"; 
$po_sql = $dbh->prepare($po_query); 						
$po_sth = $dbh->execute($po_sql);
$num_rows = $po_sth->numRows();

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");
/* ------------- END DATABASE CONNECTIONS --------------------- */

/* ------------------ START FUNCTIONS ----------------------- */
function sendMail($sendTo,$PO_Level,$REQUEST_ID,$PURPOSE,$DAYS,$REQUESTER) {
	global $default;
	
	$URL = "https://hr.yourcompany.com/go/HCR/PO/detail.php?id=".$REQUEST_ID."&approval=".$PO_Level;
	$URL2 = "https://hr.yourcompany.com/go/HCR/PO/cancelSlip.php?id=".$REQUEST_ID;
	$recipients = "tlezotte@yourcompany.com";
	//$recipients = $sendTo;

	// ---------- Start Email Comment
	require("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($recipients);
	$mail->Subject = "PO Reminder: " . ucwords(strtolower($PURPOSE));
	$mail->Priority  =  1;		//High Priority

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<p><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></p>
<br>
This email is an automatic reminder that a<br>
Purchase Order Request needs review.<br>
The Request was submitted to you $DAYS days ago.<br>
The purpose for this PO is: $PURPOSE<br><br>
URL: $URL<br><br>
NOTE: If the Request is not valid anymore, contact<br>
$REQUESTER to cancel the Request.<br><br>
URL: $URL2;
</body>
</html>
END_OF_HTML;

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
	   echo "Message was not sent<br>";
	   echo "Mailer Error: " . $mail->ErrorInfo . "<br><br>";
	   echo "<a href=\"". $default['URL_HOME']. "\" class=\"dark\">" . $default['title1'] . "</a>";
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}
/* ------------------ END FUNCTIONS ----------------------- */



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $language['label']['title1']; ?></title>
  <!-- InstanceEndEditable -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2006 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>

  <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/reset-fonts-grids/reset-fonts-grids.css" />   <!-- CSS Grid -->
  <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/assets/skins/custom/menu.css">  					<!-- Menu -->  
  
  <link type="text/css" href="/Common/Javascript/greybox5/gb_styles.css" rel="stylesheet" media="all">      
   
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/christmas/default.css" />
  <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />  
  
  <script type="text/javascript" src="/Common/Javascript/styleswitcher.js"></script>
  
  <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>
  <!-- InstanceBeginEditable name="head" -->  <!-- InstanceEndEditable -->
  </head>

<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
  
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/companyPrint.gif" name="Print" width="437" height="61" id="Print" />
            <a href="../home.php" title="<?= $default['title1']; ?>|Home Page"><img src="/Common/images/company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u" id="centerTitle"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u" style="text-align:right;margin:1em 0;padding:0;">
              <div id="applicationTitle" style="font-weight:bold;font-size:115%;text-align:right"><?= $language['label']['title1']; ?>&nbsp;</div>
              <div id="loggedInUser" class="loggedInUser" style="text-align:right"><strong><a href="Administration/user_information.php" class="loggedInUser" title="User Task|Edit your user information"><?= caps($_SESSION['fullname']); ?></a></strong>&nbsp;<a href="../logout.php" class="loggedInUser" title="User Task|Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;</div>
            <div id="styleSwitcher" style="text-align:right">Themes: <span id="defaultStyle" class="style" title="Style Switcher|Default Colors"><a href="#" onClick="setActiveStyleSheet('default'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="seasonalStyle" class="style" title="Style Switcher|Christmas Season"><a href="#" onClick="setActiveStyleSheet('seasonal'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="nightStyle" class="style" title="Style Switcher|Night Time Colors"><a href="#" onClick="setActiveStyleSheet('night'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span>&nbsp;</div>
          </div>
      </div>		      
   </div>
    
   <div id="bd">
       <div class="yui-g" id="mm"><?php include($default['FS_HOME'].'/include/main_menu.php'); ?></div>
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><br>
				  	<br>
					  <?php
						/* Dont display column headers and totals if no requests */
						if ($num_rows == 0) {
					  ?>
							<div align="center" class="DarkHeaderSubSub">No Requests Found</div>
					  <?php } else { ?>
                      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="BGAccentVeryDark"><div align="left">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Purchase Requests... </td>
                                    <td>&nbsp;</td>
                                  </tr>
                                </table>
                            </div></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder">
                              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td><table width="100%"  border="0">
                                      <tr>
                                        <td class="BGAccentDark"><strong>&nbsp;ID</strong></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Requested<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Approver 1 </strong></td>
                                        <td class="BGAccentDark"><div align="center"><strong>&nbsp;Approver 2 </strong></div></td>
                                        <td class="BGAccentDark"><div align="center"><strong>&nbsp;Approver 3 </strong></div></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Staffing&nbsp;</strong></td>
                                      </tr>
                                      <?php
									/* Reset items total variable */
									$itemsTotal = 0;
									
									while($po_sth->fetchInto($PO)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									?>
                                      <tr <?php pointer($row_color); ?>>
                                        <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><a href="../Requests/detail.php?id=<?= $PO[id]; ?>"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<?= $PO[id]; ?></td>
                                        <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($EMPLOYEES[$PO[req]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo $reqDate[0]; ?></td>
                                        <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($EMPLOYEES[$PO[app1]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[app2]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[app3]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[staffing]])); ?></td>
                                      </tr>
                                      <?php $itemsTotal += $PO[total]; ?>
                                      <?php } // End PO while ?>
                                  </table></td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td height="30" valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td valign="top">&nbsp;<span class="GlobalButtonTextDisabled"><?= $num_rows ?> Requests</span> </td>
                                  <td valign="bottom"><div align="right">
                                  <input name="stage" type="hidden" id="stage" value="process">
                                  <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Send" border="0">
                                  &nbsp;</div></td>
                                </tr>
                            </table></td>
                          </tr>
                        </table>
                      </form>
                      <?php } // End num_row if ?>
                  <br>                  </td></tr>
              </tbody>
          </table></td>
        </tr>
      </tbody>
      </table>
    <!-- InstanceEndEditable --></div>
   </div>
   
   <div id="ft" style="padding-top:50px">
     <div class="yui-gb">
        <div class="yui-u first"><?php include($default['FS_HOME'].'/include/copyright.php'); ?></div>
        <div class="yui-u"><!-- FOOTER CENTER AREA -->&nbsp;</div>
        <div class="yui-u" style="text-align:right;margin:1em 0;padding:0;"><?php include($default['FS_HOME'].'/include/right_footer.php'); ?></div>
     </div>
   </div>
     
</div>
    
<script>
	var message='<?= $message; ?>';
	var msgClass='<?= $msgClass; ?>';
</script>
    
<script type="text/javascript" src="/Common/Javascript/yahoo/yahoo-dom-event/yahoo-dom-event.js" ></script>		<!-- Menu, TabView, Datatable -->
<script type="text/javascript" src="/Common/Javascript/yahoo/container/container-min.js"></script> 				<!-- Menu -->
<script type="text/javascript" src="/Common/Javascript/yahoo/menu/menu-min.js"></script> 						<!-- Menu -->

<script type="text/javascript" src="/Common/Javascript/greybox5/options1.js"></script>
<script type="text/javascript" src="/Common/Javascript/greybox5/AJS.js"></script>
<script type="text/javascript" src="/Common/Javascript/greybox5/AJS_fx.js"></script>
<script type="text/javascript" src="/Common/Javascript/greybox5/gb_scripts.js"></script>
<?php if ($ONLOAD_OPTIONS) { ?>
<script language="javascript">
AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
</script>
<?php } ?>  

<script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.dimensions-min.js"></script>
<script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.cluetip-min.js"></script>

<script type="text/javascript" src="../js/jQdefault.js"></script>
<!-- InstanceBeginEditable name="js" -->
    
    <!-- InstanceEndEditable --> 
<script type="text/javascript">
/* ========== YUI Main Menu ========== */
YAHOO.util.Event.onContentReady("productsandservices", function () {
	var oMenuBar = new YAHOO.widget.MenuBar("productsandservices", { autosubmenudisplay: true, hidedelay: 750, lazyload: true });
	oMenuBar.render();
});
</script> 
	
<?php if (!$debug_page) { ?>   
<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "<?= $default['google_analytics']; ?>";
urchinTracker();
</script>
<?php } ?>
</body>
<!-- InstanceEnd --></html>
<?php 
/* ------------------ START DATA PROCESS ----------------------- */
if ($_POST['stage'] == 'process') {
	while($po_sth->fetchInto($PO)) {
		/* Calculate the remander */
		if (is_null($PO['app1Date'])) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[app1]"));
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));
			sendMail($APP['email'],'app1',$PO['id'],$PO['purpose'],$PO['curReq'],$REQ['name']);
		} else if (isset($PO['app2']) and is_null($PO['app2Date']) and $PO['curApp1'] >= 2 and $PO['curApp1'] % 2 == 0) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[app2]"));
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));	 						 
			sendMail($APP['email'],'app2',$PO['id'],$PO['purpose'],$PO['curApp1'],$REQ['name']);
		} else if (isset($PO['app2']) and is_null($PO['staffingDate']) and $PO['curApp2Staffing'] >= 2 and $PO['curApp2Staffing'] % 2 == 0) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[staffing]"));	
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));			 			 
			sendMail($APP['email'],'staffing',$PO['id'],$PO['purpose'],$PO['curApp2Staffing'],$REQ['name']);
		} else if ($PO['curApp1Staffing'] >= 2 and $PO[curApp1Staffing] % 2 == 0) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[staffing]"));
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));			 							  
			sendMail($APP['email'],'staffing',$PO['id'],$PO['purpose'],$PO['curApp1Staffing'],$REQ['name']);	
		}
	}
}
/* ------------------ END DATA PROCESS ----------------------- */

/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>
