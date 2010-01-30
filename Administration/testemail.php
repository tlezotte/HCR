<?php 
/**
 * Request System
 *
 * testemail.php send a test email to a user.
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
 * PHP Mailer
 * @link http://phpmailer.sourceforge.net/ 
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
 * - Config Information
 */
require_once('../include/config.php'); 

if ($_POST['stage'] == 'process') {
	/* Get requested users information */
	$USER = $dbh->getRow("SELECT fst, lst, email, username, password ".
						 "FROM Standards.Employees ".
						 "WHERE eid = ?",array($_POST['eid']));

	/* Set some variable for emails */
	$first_name = ucwords(strtolower($USER['fst']));
	$last_name = ucwords(strtolower($USER['lst']));
	$feedback_type = $TYPE[$_POST['type']];
				  
	// ---------- Start Email Comment
	require("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($USER['email'], $first_name." ".$last_name);
	$mail->Subject = $default['title1']." Notification";

/* Plain text message */
$textBody = <<< END_OF_HTML
\n-------------------------------------------------------\n
Welcome to the your company Purchase Request System\n
---------------------------------------------------------\n\n
This email was sent for test proposes ONLY\n
END_OF_HTML;

					$mail->Body = $textBody;
					
					if(!$mail->Send())
					{
					   echo "Message was not sent";
					   echo "Mailer Error: " . $mail->ErrorInfo;
					}
					
					// Clear all addresses and attachments for next loop
					$mail->ClearAddresses();
					$mail->ClearAttachments();
				// ---------- End Email Comment
				// -------- End Process Form data ------------------
	
	header('Location: ../index.php');
	exit;
}

/* Get Purchase Request users */
$employees_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst, E.email ".
					 		   "FROM Users U, Standards.Employees E ".
					 	 	   "WHERE U.eid = E.eid and U.status = '0' and E.status = '0' ".
					 		   "ORDER BY E.lst ASC");
$employees_sth = $dbh->execute($employees_sql);



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
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form">
          <br>
          <table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td class="GlobalButtonTextSelected">Select your name from the list below and click <strong>Done</strong>. In a couple of minutes you will receive an email with your username and password. </td>
            </tr>
            <tr>
              <td height="30">&nbsp;</td>
            </tr>
            <tr>
              <td class="BGAccentVeryDark"><div align="left">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td height="30" class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;Send test email...</td>
                      <td><div align="right"> </div></td>
                    </tr>
                  </table>
              </div></td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder"><table  border="0" align="center">
                  <tr>
                    <td nowrap><label for="username">Recipient's Name: </label></td>
                    <td><select name="eid" id="eid">
                        <option value="0">Select One</option>
                        <?php
				$employees_sth = $dbh->execute($employees_sql);
				while($employees_sth->fetchInto($EMPOLYEES)) {
					print "<option value=\"".$EMPOLYEES[eid]."\" ".$selected.">".ucwords(strtolower($EMPOLYEES[lst].", ".$EMPOLYEES[fst]))."</option>";
				}
				?>
                    </select></td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
            </tr>
            <tr>
              <td>
                <div align="right">
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td>&nbsp;</td>
                      <td><div align="right">
                          <input name="stage" type="hidden" id="stage" value="process">
                          <input name="login" type="image" id="login" src="../images/button.php?i=b70.png&l=Send" border="0">
&nbsp;&nbsp;</div></td>
                    </tr>
                  </table>
              </div></td>
            </tr>
          </table>
        </form></td>
      </tr>
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
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>