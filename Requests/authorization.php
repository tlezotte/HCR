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
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');


/* ------------------ START PROCESSING DATA ----------------------- */
if ($_POST['stage'] == "four") {
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
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/christmas/default.css" />
  <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />  
  
  <script type="text/javascript" src="/Common/Javascript/styleswitcher.js"></script>
  
  <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>
  <!-- InstanceBeginEditable name="head" -->
  <script type="text/javascript" language="javascript">
	function submitThisForm(){
		document.Form.submit();
		document.getElementById('cmdSubmit').style.visibility = 'hidden';
	}
  </script>  
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
                          <td><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                          <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                          <td><a href="description.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                          <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                          <td><a href="technology.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                          <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                          <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                          <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                          <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                        </tr>
                        <tr>
                          <td colspan="9"><table width="100%"  border="0">
                              <tr>
                                <td width="15%" class="wizardPast">Information</td>
                                <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Description</div></td>
                                <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Technology</div></td>
                                <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Authorization</div></td>
                                <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                              </tr>
                          </table></td>
                        </tr>
                      </table>
                    </div>
                      <br>
                      <br>
                      <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="BGAccentVeryDark"><div align="left">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;
                                      <img src="../images/checkmark.gif" width="16" height="16" align="texttop">
                                    <?= $language['label']['stage4']; ?>...</td>
                                    <td width="50%"><div align="right"> </div></td>
                                  </tr>
                                </table>
                            </div></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder">
                            <div id="panelContent">
                             <table width="100%"  border="0">
                              <tr>
                                <td height="25"><vllabel form="Form" validators="app1" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app1']; ?>:</vllabel></td>
                                <td><select name="app1" id="app1">
                                  <option value="0">Select One</option>
                                  <?php
									  $app1_sth = $dbh->execute($app1_sql);
									  while($app1_sth->fetchInto($APP1)) {
										$selected = ($_SESSION['app1'] == $APP1[eid]) ? selected : $blank;
										print "<option value=\"".$APP1[eid]."\" ".$selected.">".ucwords(strtolower($APP1[lst].", ".$APP1[fst]))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app1" type="compare" control="app1" validtype="string" comparevalue="0" comparecontrol="app1" operator="ne"></td>
                              </tr>
                              <tr>
                                <td height="25"><vllabel form="Form" validators="app2" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app2']; ?>:</vllabel></td>
                                <td><select name="app2" id="app2">
                                  <option value="0">Select One</option>
                                  <?php
									  $app2_sth = $dbh->execute($app2_sql);								  
									  while($app2_sth->fetchInto($APP2)) {
										$selected = ($_SESSION['app2'] == $APP2[eid]) ? selected : $blank;
										print "<option value=\"".$APP2[eid]."\" ".$selected.">".ucwords(strtolower($APP2[lst].", ".$APP2[fst]))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app2" type="compare" control="app2" validtype="string" comparevalue="0" comparecontrol="app2" operator="ne"></td>
                              </tr>
                              <tr>
                                <td height="25"><vllabel form="Form" validators="app4" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app4']; ?>:</vllabel></td>
                                <td><select name="app4" id="app4">
                                  <option value="0">Select One</option>
                                  <?php
									  $app4_sth = $dbh->execute($app4_sql);								  
									  while($app4_sth->fetchInto($APP4)) {
										$selected = ($_SESSION['app4'] == $APP4['eid']) ? selected : $blank;
										print "<option value=\"".$APP4['eid']."\" ".$selected.">".ucwords(strtolower($APP4['lst'].", ".$APP4['fst']))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app4" type="compare" control="app4" validtype="string" comparevalue="0" comparecontrol="app4" operator="ne"></td>
                              </tr>
                              <tr>
                                <td height="25"><vllabel form="Form" validators="app5" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app5']; ?>:</vllabel></td>
                                <td><select name="app5" id="app5">
                                  <option value="0">Select One</option>
                                  <?php
									  $app5_sth = $dbh->execute($app5_sql);								  
									  while($app5_sth->fetchInto($APP5)) {
										$selected = ($_SESSION['app5'] == $APP5['eid']) ? selected : $blank;
										print "<option value=\"".$APP5['eid']."\" ".$selected.">".ucwords(strtolower($APP5['lst'].", ".$APP5['fst']))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app5" type="compare" control="app5" validtype="string" comparevalue="0" comparecontrol="app5" operator="ne"></td>
                              </tr>
                              <tr>
                                <td height="25"><vllabel form="Form" validators="app6" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app6']; ?>:</vllabel></td>
                                <td><select name="app6" id="app6">
                                  <option value="0">Select One</option>
                                  <?php
									  $app6_sth = $dbh->execute($app6_sql);								  
									  while($app6_sth->fetchInto($APP6)) {
										$selected = ($_SESSION['app6'] == $APP6['eid']) ? selected : $blank;
										print "<option value=\"".$APP6['eid']."\" ".$selected.">".ucwords(strtolower($APP6['lst'].", ".$APP6['fst']))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app6" type="compare" control="app6" validtype="string" comparevalue="0" comparecontrol="app6" operator="ne"></td>
                              </tr>
							  
                             <!-- <tr>
                                <td height="25"><vllabel form="Form" validators="app7" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app7']; ?>:</vllabel></td>
                                <td><select name="app7" id="app7">
                                  <option value="0">Select One</option>
                                  <?php
									  $app7_sth = $dbh->execute($app7_sql);								  
									  while($app7_sth->fetchInto($APP7)) {
										$selected = ($_SESSION['app7'] == $APP7['eid']) ? selected : $blank;
										print "<option value=\"".$APP7['eid']."\" ".$selected.">".ucwords(strtolower($APP7['lst'].", ".$APP7['fst']))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app7" type="compare" control="app7" validtype="string" comparevalue="0" comparecontrol="app7" operator="ne"></td>
                              </tr>-->
                              <tr>
                                <td height="25"><vllabel form="Form" validators="app8" class="valRequired2" errclass="valError">
                                  <?= $language['label']['app8']; ?>:</vllabel></td>
                                <td><select name="app8" id="app8">
                                  <option value="0">Select One</option>
                                  <?php
								  	  $app8_sth = $dbh->execute($app8_sql);
									  while($app8_sth->fetchInto($APP8)) {
										$selected = ($_SESSION['app8'] == $APP8['eid']) ? selected : $blank;
										print "<option value=\"".$APP8['eid']."\" ".$selected.">".ucwords(strtolower($APP8['lst'].", ".$APP8['fst']))."</option>";
									  }
									?>
                                </select>
                                <vlvalidator name="app8" type="compare" control="app8" validtype="string" comparevalue="0" comparecontrol="app8" operator="ne"></td>
                              </tr>
                            </table>
                            </div>
                            </td>
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
                                      <td>&nbsp;<a href="technology.php"><img src="../images/button.php?i=b70.png&l=<?= $language['label']['back']; ?>" border="0"></a></td>
                                      <td><div align="right">
                                        <input type="hidden" id="app7" name="app7" value="00000">
                                        <input name="stage" type="hidden" id="stage" value="four">
                                        <input name="action" type="hidden" id="action" value="new">
										<input type="image" name="Finished" src="../images/button.php?i=b90.png&l=<?= $language['label']['finished']; ?>" alt="<?= $language['label']['finished']; ?>">&nbsp;</div></td>
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