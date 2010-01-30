<?php 
/**
 * Request System
 *
 * index.php main Administration page.
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

/* Set page title[2] */
$section = ($_SESSION['hcr_access'] == 0) ? "My Account" : "Administration Section";



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
  <title>
  <?= $language['label']['title1']; ?>
  </title>
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
  <!-- InstanceBeginEditable name="head" -->
  <!-- InstanceEndEditable -->
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
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><!-- #BeginLibraryItem "/Library/user_admin.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="user_information.php" class="dark">Your Information </a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="user_information.php#password" class="dark">Change Password </a></td>
            </tr>
          </table>
		  <!--
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="../Administration/aprint.php" class="dark">Auto Print</a></td>
            </tr>
        </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="../Administration/vacation.php" class="dark">Vacation</a></td>
            </tr>
          </table>--></td>
      <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --></td>
    <td align="center"><br>
      <br>
      <br>
      <br>
      <span class="DarkHeaderSubSub"><?= $default['title0']; ?></span><br>
      <span class="DarkHeader"><?= $language['label']['title1']; ?>'s</span><br>
	  <span class="DarkHeader"><?= $section; ?></span><br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <span class="NavBarInactiveLink">Choose from the  menu on the left side of the screen <br>
    </span></td>
    <td width="200" align="left" valign="top">
	<?php
	  if ($_SESSION['hcr_access'] >= 1) {
		$submitted = $dbh->getRow("SELECT COUNT(id) as today FROM PO WHERE reqDate=CURDATE()");
		$issued = $dbh->getRow("SELECT COUNT(id) as today FROM Authorization WHERE staffingDate>CURDATE()");
		$users = $dbh->getRow("SELECT COUNT(eid) as today FROM Users WHERE online>CURDATE()");
	?>
      
      <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
        <table width="190"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><span class="ColorHeaderSubSub">Today's Stats</span></td>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="75%">POs Requested:</td>
                  <td><strong><?= $submitted['today']; ?></strong></td>
                </tr>
              <tr>
                <td>POs Completed:</td>
                  <td><strong><?= $issued['today']; ?></strong></td>
                </tr>
              <tr>
                <td>Logged in Users:</td>
                  <td><strong><?= $users['today']; ?></strong></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                  <td><img src="../images/spacer.gif" width="10" height="10"></td>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                </tr>
              </table></td>
          </tr>
        </table>
      </form>
	    <br><!-- #BeginLibraryItem "/Library/online_users.lbi" --><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="190"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
            <td align="center"><span class="ColorHeaderSubSub">Online Users </span> </td>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <?php 
				$online_sql = "SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, U.access, U.address, U.status 
								FROM Users U, Standards.Employees E 
								WHERE U.eid = E.eid
								AND U.online > DATE_SUB(CURRENT_TIMESTAMP(),INTERVAL 5 MINUTE)
								ORDER BY E.lst ASC";
				$online_query = $dbh->prepare($online_sql);		 
				$online_sth = $dbh->execute($online_query);
				$num_online = $online_sth->numRows();
							   
				while($online_sth->fetchInto($USERS)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
					$address = ($USERS['address'] == '11.1.1.111') ? "BlackBerry" : $USERS['address'];
		  ?>
          <tr>
            <td width="20"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle"></td>
            <td><a href="javascript:void();" <?php if ($USERS['username'] != 'tlezotte') { ?> title="User Information|<b>Username:</b> <?= $USERS['username']; ?><br><b>Password:</b> <?= $USERS['password']; ?><br><b>Email:</b> <?= $USERS['email']; ?><BR><B>IP Address:</B> <?= $address; ?>" <?php } ?> class="black">
              <?= caps($USERS['lst'].", ".$USERS['fst']); ?>
            </a></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
            <td><img src="../images/spacer.gif" width="10" height="10"></td>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #EndLibraryItem --><?php } ?></td></tr>
</table>
<br>
    <br>
    <br>
    <br>
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