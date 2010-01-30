<?php
/**
 * @link http://www.yourcompany.com/
 * @author	Thomas LeZotte (tom@lezotte.net)
 */


/**
 * - Forward BlackBerry users to BlackBerry version
 */
//require_once('../include/BlackBerry.php');

/**
 * - Start Page Loading Timer
 */
//include_once('../include/Timer.php');
//$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = true;
//include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Check User Access
 */
//require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php');


$USER = $dbh->getRow("SELECT * FROM Users WHERE eid=" . $_SESSION['eid']);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnMain.dwt.php" codeOutsideHTMLIsLocked="false" -->
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
  <!-- InstanceBeginEditable name="head" -->
    <link type="text/css" href="/Common/Javascript/yahoo/fonts/fonts-min.css" rel="stylesheet">
    <link type="text/css" href="/Common/Javascript/yahoo/assets/skins/sam/datatable.css" rel="stylesheet">    
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
	<?php 
      if ($_SESSION['hcr_access'] == '3') {
        //include('../Administration/include/detail.php');
      } 
    ?>	    
    <table border="0" align="center">
      <tr>
        <td colspan="2">
          <div align="right">
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" style="margin: 0">
              <table>
                <tr>
                  <td class="currentnew" title="Filter Action|Select your Role to filter the List.">Role</td>
                  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                  <td class="currentnew" title="Filter Action|Select the Request Type to filter the List.">Type</td>
                  <?php } ?>
                  <td class="currentnew" title="Filter Action|Select the Status of a Request to filter the List.">Status</td>
                  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                  <td rowspan="2"><img src="/Common/images/spacer.gif" width="15" height="5" /></td>
                  <td class="currentnew" title="Filter Action|View only My Requests.">My</td>
                  <?php } ?>
                </tr>
                <tr>
                  <td><select name="access" id="access" onChange="this.form.submit();">
                      <option value="none" <?php if ($_GET['access'] == 'none') { echo "selected"; } ?>>None</option>
                      <option value="0" <?php if ($_GET['access'] == '0') { echo "selected"; } ?>>Requester</option>
                      <?php if ($USER['one'] == '1') { ?><option value="1" <?php if ($_GET['access'] == '1') { echo "selected"; } ?>><?= $language['label']['app1']; ?></option><?php } ?>
                      <?php if ($USER['two'] == '1') { ?><option value="2" <?php if ($_GET['access'] == '2') { echo "selected"; } ?>><?= $language['label']['app2']; ?></option><?php } ?>
                      <?php if ($USER['three'] == '1') { ?><option value="3" <?php if ($_GET['access'] == '3') { echo "selected"; } ?>><?= $language['label']['app3']; ?></option><?php } ?>
                      <?php if ($USER['four'] == '1') { ?><option value="4" <?php if ($_GET['access'] == '4') { echo "selected"; } ?>><?= $language['label']['app4']; ?></option><?php } ?>
                      <?php if ($USER['five'] == '1') { ?><option value="5" <?php if ($_GET['access'] == '5') { echo "selected"; } ?>><?= $language['label']['app5']; ?></option><?php } ?>
                      <?php if ($USER['six'] == '1') { ?><option value="6" <?php if ($_GET['access'] == '6') { echo "selected"; } ?>><?= $language['label']['app6']; ?></option><?php } ?>
                      <?php if ($USER['seven'] == '1') { ?><option value="7" <?php if ($_GET['access'] == '7') { echo "selected"; } ?>><?= $language['label']['app7']; ?></option><?php } ?>
                      <?php if ($USER['eight'] == '1') { ?><option value="8" <?php if ($_GET['access'] == '8') { echo "selected"; } ?>><?= $language['label']['app8']; ?></option><?php } ?>
                    </select></td>
                  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                  <td><select name="type" id="type" onChange="this.form.submit();" class="listoptions">
                    <option value="new" <?= ($_GET['type'] == 'new') ? selected : $blank; ?>>Open Position</option>
                    <option value="adjustment" <?= ($_GET['type'] == 'adjustment') ? selected : $blank; ?>>Adjustment</option>
                    <option value="transfer" <?= ($_GET['type'] == 'transfer') ? selected : $blank; ?>>Transfer</option>
                    <option value="conversion" <?= ($_GET['type'] == 'conversion') ? selected : $blank; ?>>Conversion</option>
                    <option value="promotion" <?= ($_GET['type'] == 'promotion') ? selected : $blank; ?>>Promotion</option>
                    <option value="all" <?= ($_GET['type'] == 'all') ? selected : $blank; ?>>All</option>
                  </select></td>
                  <?php } ?>
                  <td><select name="status" id="status" onChange="this.form.submit();" class="listoptions">
                    <option value="N" <?= ($_GET['status'] == 'N') ? selected : $blank; ?>>New</option>
                    <option value="A" <?= ($_GET['status'] == 'A') ? selected : $blank; ?>>Approved</option>
                    <option value="D" <?= ($_GET['status'] == 'D') ? selected : $blank; ?>>Denied</option>
                    <option value="C" <?= ($_GET['status'] == 'C') ? selected : $blank; ?>>Canceled</option>
                  </select></td>
                  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                  <td><input name="my" type="checkbox" id="my" value="true" <?= ($_GET['access'] > 0) ? 'disabled' : $blank; ?> <?= ($_GET['my'] == 'true' OR $_GET['access'] > 0) ? 'checked' : $blank; ?> onChange="this.form.submit();" /></td>
                  <?php } ?>
                </tr>
              </table>
            </form>
            </div> 
            <div id="dataTable"></div>
        </td>
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
	<script type="text/javascript" src="/Common/Javascript/yahoo/utilities/utilities.js"></script>
    <script type="text/javascript" src="/Common/Javascript/yahoo/datasource/datasource-beta.js"></script>
    <script type="text/javascript" src="/Common/Javascript/yahoo/datatable/datatable-beta.js"></script>
    <script type="text/javascript">
		var searchRole = "<?= (isset($_GET['access'])) ? urlencode(trim($_GET['access'])) : "0"; ?>";
		var searchType = "<?= (isset($_GET['type'])) ? urlencode(trim($_GET['type'])) : "new"; ?>";
		var searchStatus = "<?= (isset($_GET['status'])) ? urlencode(trim($_GET['status'])) : "N"; ?>";
		var searchMy = "<?= ($_GET['my'] == 'true' OR $_GET['access'] > 0) ? 'true' : 'false'; ?>";
	</script> 
    <script type="text/javascript" src="../js/YUIlist.js"></script>
    <?php if ($debug_page) { ?>
    <script type="text/javascript" src="/Common/Javascript/firebug/firebug.js"></script>
    <?php } ?>
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
//include_once('debug/footer.php');

/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>