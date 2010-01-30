<?php
/**
 * Request System
 *
 * authorization.php setup automatic printing to staffing.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
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
 * - Check user access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 



/* ------------------ START PROCESSING DATA ----------------------- */
if ($_POST['stage'] == "two") {
	/* Set form variables as session variables */
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]  = htmlentities($value, ENT_QUOTES, 'UTF-8');
	}
	
	header("Location: finished.php");
	exit(0);
}
/* ------------------ END PROCESSING DATA ----------------------- */


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app2_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.two = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app3_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.three = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");						   
$app4_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.four = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app5_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.five = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app6_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.six = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
$app8_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
					       FROM Users U, Standards.Employees E
					       WHERE U.eid = E.eid and U.eight = '1' and U.status = '0' and E.status = '0'
					       ORDER BY E.lst ASC");
/* ------------------ END DATABASE CONNECTIONS ----------------------- */


/* ---- Set Request Type ---- */
$requestType = (array_key_exists('type', $_GET)) ? caps($_GET['type']) : caps($_SESSION['request_type']);

//$ONLOAD_OPTIONS.="init();";
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
  <!-- InstanceBeginEditable name="head" -->
  <script src="/Common/Javascript/Spry/widgets/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>
  <link href="/Common/Javascript/Spry/widgets/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" /> 

  <style type="text/css">
	.textfieldValidState input, input.textfieldValidState {
		background-color:#FFFFFF;
	}
	.textareaValidState textarea, textarea.textareaValidState {
		background-color:#FFFFFF;
	}
	.selectValidState select, select.selectValidState {
		background-color: #FFFFFF;
	}	
  </style>    
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
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><br>
                    <div id="noPrint">
                      <table  border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                          <td><a href="_index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a><a href="technology.php"></a></td>
                          <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                          <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                          <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                          <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                        </tr>
                        <tr>
                          <td colspan="7"><table width="100%"  border="0">
                              <tr>
                                <td width="25%" class="wizardPast">Information</td>
                                <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Authorization</div></td>
                                <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                              </tr>
                          </table></td>
                        </tr>
                      </table>
                    </div>
                      <br>
                      <br>
                      <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="BGAccentVeryDark">
                             <table width="100%" border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="30" nowrap class="DarkHeaderSubSub">&nbsp;<?= $requestType; ?></td>
                                <td width="50%" align="right"><span style="text-transform:capitalize; font-size:110%; font-weight:bold">&nbsp;</span></td>
                              </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder"><div class="panelContent"><table width="100%"  border="0">
                               <tr>
                                <td><?= $language['label']['app1']; ?>:</td>
                                <td><span id="spryselect1">
                                  <select name="app1" id="app1">
                                    <option value="0">Select One</option>
                                    <?php
									  $app1_sth = $dbh->execute($app1_sql);								  
									  while($app1_sth->fetchInto($APP1)) {
										$selected = ($_SESSION['app1'] == $APP1['eid']) ? selected : $blank;
										print "<option value=\"".$APP1['eid']."\" ".$selected.">" . caps($APP1['lst'].", ".$APP1['fst']) . "</option>";
									  }
									?>
                                  </select>
                                  <span class="selectInvalidMsg">Please select a valid item.</span><span class="selectRequiredMsg">Please select an item.</span></span>
<vlvalidator name="app2" type="compare" control="app2" validtype="string" comparevalue="0" comparecontrol="app2" operator="ne"></td>
                              </tr>                             
                              <tr>
                                <td><?= $language['label']['app2']; ?>:</td>
                                <td><span id="spryselect2">
                                  <select name="app2" id="app2">
                                    <option value="0">Select One</option>
                                    <?php
									  $app2_sth = $dbh->execute($app2_sql);								  
									  while($app2_sth->fetchInto($APP2)) {
										$selected = ($_SESSION['app2'] == $APP2['eid']) ? selected : $blank;
										print "<option value=\"".$APP2['eid']."\" ".$selected.">" . caps($APP2['lst'].", ".$APP2['fst']) . "</option>";
									  }
									?>
                                  </select>
                                  <span class="selectInvalidMsg">Please select a valid item.</span><span class="selectRequiredMsg">Please select an item.</span></span>
<vlvalidator name="app2" type="compare" control="app2" validtype="string" comparevalue="0" comparecontrol="app2" operator="ne"></td>
                              </tr>
                              
                              <tr>
                                <td><?= $language['label']['app4']; ?>:</td>
                                <td><span id="spryselect3">
                                  <select name="app4" id="app4">
                                    <option value="0">Select One</option>
                                    <?php
									  $app4_sth = $dbh->execute($app4_sql);								  
									  while($app4_sth->fetchInto($APP4)) {
										$selected = ($_SESSION['app4'] == $APP4['eid']) ? selected : $blank;
										print "<option value=\"".$APP4['eid']."\" ".$selected.">" . caps($APP4['lst'].", ".$APP4['fst']) . "</option>";
									  }
									?>
                                  </select>
                                  <span class="selectInvalidMsg">Please select a valid item.</span><span class="selectRequiredMsg">Please select an item.</span></span>
<vlvalidator name="app4" type="compare" control="app4" validtype="string" comparevalue="0" comparecontrol="app4" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><?= $language['label']['app5']; ?>:</td>
                                <td><span id="spryselect4">
                                  <select name="app5" id="app5">
                                    <option value="0">Select One</option>
                                    <?php
									  $app5_sth = $dbh->execute($app5_sql);								  
									  while($app5_sth->fetchInto($APP5)) {
										$selected = ($_SESSION['app5'] == $APP5['eid']) ? selected : $blank;
										print "<option value=\"".$APP5['eid']."\" ".$selected.">" . caps($APP5['lst'].", ".$APP5['fst']) . "</option>";
									  }
									?>
                                  </select>
                                  <span class="selectInvalidMsg">Please select a valid item.</span><span class="selectRequiredMsg">Please select an item.</span></span>
<vlvalidator name="app5" type="compare" control="app5" validtype="string" comparevalue="0" comparecontrol="app5" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><?= $language['label']['app6']; ?>:</td>
                                <td><span id="spryselect5">
                                  <select name="app6" id="app6">
                                    <option value="0">Select One</option>
                                    <?php
									  $app6_sth = $dbh->execute($app6_sql);								  
									  while($app6_sth->fetchInto($APP6)) {
										$selected = ($_SESSION['app6'] == $APP6[eid]) ? selected : $blank;
										print "<option value=\"".$APP6[eid]."\" ".$selected.">".caps($APP6[lst].", ".$APP6[fst])."</option>";
									  }
									?>
                                  </select>
                                  <span class="selectInvalidMsg">Please select a valid item.</span><span class="selectRequiredMsg">Please select an item.</span></span>
<vlvalidator name="app6" type="compare" control="app6" validtype="string" comparevalue="0" comparecontrol="app6" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><?= $language['label']['app8']; ?>:</td>
                                <td><span id="spryselect6">
                                  <select name="app8" id="app8">
                                    <option value="0">Select One</option>
                                    <?php
								  	  $app8_sth = $dbh->execute($app8_sql);
									  while($app8_sth->fetchInto($APP8)) {
										$selected = ($_SESSION['app8'] == $APP8['eid']) ? selected : $blank;
										print "<option value=\"" . $APP8['eid'] . "\" ".$selected.">" . caps($APP8['lst'].", ".$APP8['fst']) . "</option>";
									  }
									?>
                                  </select>
                                  <span class="selectInvalidMsg">Please select a valid item.</span><span class="selectRequiredMsg">Please select an item.</span></span>
<vlvalidator name="app8" type="compare" control="app8" validtype="string" comparevalue="0" comparecontrol="app8" operator="ne"></td>
                              </tr>
                            </table></div></td>
                          </tr>
                          <tr>
                            <td height="5"><img src="../images/spacer.gif" width="5" height="5">
                            </td>
                          </tr>
                          <tr>
                            <td>
                                <div align="right">
                                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td>&nbsp;<a href="_index.php"><img src="../images/button.php?i=b70.png&l=<?= $language['label']['back']; ?>" border="0"></a></td>
                                      <td><div align="right">
                                        <input name="stage" type="hidden" id="stage" value="two" />
                                        <input name="request_type" type="hidden" id="request_type" value="<?= $_SESSION['request_type']; ?>">
                                        <input name="Done" type="image" class="button" id="Done" src="../images/button.php?i=b70.png&l=<?= $language['label']['done']; ?>" alt="<?= $language['label']['done']; ?>" border="0">&nbsp;</div></td>
                                    </tr>
                                  </table>
                              </div></td>
                          </tr>
                        </table>
                    </form>				
                      <br>
                  </td></tr>
              </tbody>
          </table></td>
        </tr>
      </tbody>
    </table>
<script type="text/javascript">
<!--
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {invalidValue:"0", validateOn:["blur", "change"]});
//-->
</script>    
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