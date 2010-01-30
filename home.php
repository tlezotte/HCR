<?php 
/**
 * Request System
 *
 * home.php is the default page after a seccessful login.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
 */


/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('include/BlackBerry.php');

/**
 * - Start Page Loading Timer
 */
include_once('include/Timer.php');
$starttime = StartLoadTimer();
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
 * - Check User Access
 */
require_once('security/check_user.php');
/**
 * - Config Information
 */
require_once('include/config.php'); 


/**
 * - Check to see if a web notice needs to be displayed 
 */
if ($default['notify_web'] == 'on' and !isset($_COOKIE['notify_web'])) {
	header("Location: notice.php");
	exit;
}

if ($_POST['action'] == 'search') {
	$DATA = $dbh->getRow("SELECT id FROM Requests WHERE id='".$_POST['number']."'");
	
	if (isset($DATA)) {
		$forward = "Requests/detail.php?id=".$DATA['id'];
	} else {
		$_SESSION['error'] = "HC-".$_POST['number']." was not found";
		$forward = "error.php";
	}

	header("Location: ".$forward);
	exit();
}


/* ------------- START DATABASE CONNECTIONS --------------------- */
$requests_sql = "SELECT r.id, r.request_type, p.title_name
				 FROM Requests r
				   LEFT JOIN Position p ON p.title_id=r.positionTitle
				 WHERE r.req like '" . $_SESSION['eid'] . "' AND r.status = 'N'
				 ORDER BY r.id DESC";						
$requests_query = $dbh->prepare($requests_sql);
$requests_sth = $dbh->execute($requests_query);
$num_rows = $requests_sth->numRows();
/* ------------- END DATABASE CONNECTIONS --------------------- */
			

//$ONLOAD_OPTIONS.="";
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
  <link href="default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/christmas/default.css" />
  <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />  
  
  <script type="text/javascript" src="/Common/Javascript/styleswitcher.js"></script>
  
  <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>
  <!-- InstanceBeginEditable name="head" -->

  <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/fonts/fonts-min.css" />							<!-- Datatable, TabView -->
  <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/assets/skins/sam/datatable.css" />				<!-- Datatable -->

  <!-- InstanceEndEditable -->
  </head>

<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
  
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/companyPrint.gif" name="Print" width="437" height="61" id="Print" />
            <a href="home.php" title="<?= $default['title1']; ?>|Home Page"><img src="/Common/images/company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u" id="centerTitle"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u" style="text-align:right;margin:1em 0;padding:0;">
              <div id="applicationTitle" style="font-weight:bold;font-size:115%;text-align:right"><?= $language['label']['title1']; ?>&nbsp;</div>
              <div id="loggedInUser" class="loggedInUser" style="text-align:right"><strong><a href="Administration/user_information.php" class="loggedInUser" title="User Task|Edit your user information"><?= caps($_SESSION['fullname']); ?></a></strong>&nbsp;<a href="logout.php" class="loggedInUser" title="User Task|Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;</div>
            <div id="styleSwitcher" style="text-align:right">Themes: <span id="defaultStyle" class="style" title="Style Switcher|Default Colors"><a href="#" onClick="setActiveStyleSheet('default'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="seasonalStyle" class="style" title="Style Switcher|Christmas Season"><a href="#" onClick="setActiveStyleSheet('seasonal'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="nightStyle" class="style" title="Style Switcher|Night Time Colors"><a href="#" onClick="setActiveStyleSheet('night'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span>&nbsp;</div>
          </div>
      </div>		      
   </div>
    
   <div id="bd">
       <div class="yui-g" id="mm"><?php include($default['FS_HOME'].'/include/main_menu.php'); ?></div>
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->
        <div align="center">    
          <table width="99%"  border="0" align="center" cellpadding="0" cellspacing="0" id="HomePage">
            <tr>
              <td width="300" align="left" valign="top">
               <div id="searchPanel" style="border: thin solid #7F7F7F; background-color:#EDF5FF; width:300px; padding:5px">
                  <form name="form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
                    <table  border="0" align="center" cellpadding="0" cellspacing="2">
                      <tr>
                        <td><strong>Number: HCR-<input name="action" type="hidden" id="action" value="search"></strong></td>
                        <td><input name="number" type="text" id="number" size="7" maxlength="10"></td>
                        <td><input name="search" type="image" id="search" src="images/button.php?i=w70.png&l=Search" alt="Search" border="0"></td>
                      </tr>
                    </table>
                  </form>
                </div>
                <div id="myRequestsTable" class="infoPanel"></div>
                <div id="marketTable" class="infoPanel"></div>
              </td>
              <td valign="top">
                <div align="center">
                    <div style="padding-top:50px; padding-bottom:50px">
                        <span class="DarkHeaderSubSub"><?= $language['label']['title0']; ?></span>
                        <br>
                        <span class="DarkHeader"><?= $language['label']['title1']; ?></span>
                        <br>
                        <span class="DarkHeaderSubSub"><?= $language['label']['title2']; ?></span>
                    </div>
                </div>
              </td>
              <td width="300" align="right" valign="top">
                  <div id="ChangeLogTable" class="infoPanel"></div>
                  <div id="ShrmTable" class="infoPanel"></div>
                  <div id="WeatherTable" class="infoPanel"></div>
              </td>
            </tr>
          </table>      
          <div id="ChangeLog" style="display:none">
              <img src="images/next_button.gif" width="19" height="19" border="0" align="absmiddle"> Return to <?= $default['title1']; ?> Home Page</a><br>
              <br>
              <iframe frameborder="0" height="800" width="98%" name="ChangeLog"></iframe>
          </div>  
        </div>    
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

<script type="text/javascript" src="js/jQdefault.js"></script>
<!-- InstanceBeginEditable name="js" -->
	<script type="text/javascript" src="/Common/Javascript/yahoo/utilities/utilities.js"></script>					<!-- Datatable -->
    <script type="text/javascript" src="/Common/Javascript/yahoo/datasource/datasource-beta-min.js"></script>		<!-- Datatable -->
    <script type="text/javascript" src="/Common/Javascript/yahoo/datatable/datatable-beta-min.js"></script>			<!-- Datatable -->
    <script type="text/javascript" src="/Common/Javascript/yahoo/connection/connection-min.js" ></script>			<!-- Datatable -->

	<script type="text/javascript" src="js/YUIhome.js"></script>  
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
