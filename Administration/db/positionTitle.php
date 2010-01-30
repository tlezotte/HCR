<?php
/**
 * Request System
 *
 * settings.php display, add and edit system wide variables.
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
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Check User Access
 */
require_once('../../security/check_access.php'); 

/**
 * - Config Information
 */
require_once('../../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');



/* ----- START ADD VARIABLE ----- */
if ($_POST['action'] == "add") {
	$grade = $dbh->getOne("SELECT phone_model FROM Position WHERE grade='" . $_POST['grade'] . "'");	// Get Phone Model for that level

	$min = preg_replace("/,/", "", $_POST['min']);				// Remove commas from min value
	$max = preg_replace("/,/", "", $_POST['max']);				// Remove commas from max value
	$mid = (($max - $min) / 2) + $min;							// Calculate mid value
	$minmid = (($mid - $min) / 2) + $min;						// Calculate minmid value
	$midmax =  (($max - $mid) / 2) + $mid;						// Calculate midmax value

	$sql = "INSERT into Position (title_id, grade, title_name, min, minmid, mid, midmax, max, ot, flsa, phone_model, title_status) 
						  VALUES (NULL, 
								  '" . $_POST['grade'] . "',
								  '" . $_POST['title_name'] . "',
								  '" . number_format($min) . "',
								  '" . number_format($minmid) . "',
								  '" . number_format($mid) . "',
								  '" . number_format($midmax) . "',
								  '" . number_format($max) . "',
								  '" . $_POST['ot'] . "',
								  '" . $_POST['flsa'] . "',
								  '" . $grade['phone_model']. "',
								  '0')";
	$dbh->query($sql);																					
										
	/* Record transaction for history */
	History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));											
}
/* ----- END ADD VARIABLE ----- */


/* ------------- START FORM DATA --------------------- */
$settings_sq1 = $dbh->prepare("SELECT * 
							   FROM Position 
							   WHERE title_status = '0' 
							   ORDER BY (grade+0), min ASC");

$grades_sq1 = $dbh->prepare("SELECT distinct(grade), min, max
							 FROM Position
							 WHERE title_status='0'
							 ORDER BY (grade+0) ASC, title_name DESC, min ASC");
/* ------------- END FORM DATA --------------------- */



$ONLOAD_OPTIONS.="";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
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
  <link href="../../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/christmas/default.css" />
  <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />  
  
  <script type="text/javascript" src="/Common/Javascript/styleswitcher.js"></script>
  
  <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>
  <!-- InstanceBeginEditable name="head" -->
  <style type="text/css">
	<!--
	A {
		color: #000000;
	}
	-->
  </style>  
  <!-- InstanceEndEditable -->
  </head>

<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
  
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/companyPrint.gif" name="Print" width="437" height="61" id="Print" />
            <a href="../../home.php" title="<?= $default['title1']; ?>|Home Page"><img src="/Common/images/company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u" id="centerTitle"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u" style="text-align:right;margin:1em 0;padding:0;">
              <div id="applicationTitle" style="font-weight:bold;font-size:115%;text-align:right"><?= $language['label']['title1']; ?>&nbsp;</div>
              <div id="loggedInUser" class="loggedInUser" style="text-align:right"><strong><a href="Administration/user_information.php" class="loggedInUser" title="User Task|Edit your user information"><?= caps($_SESSION['fullname']); ?></a></strong>&nbsp;<a href="../../logout.php" class="loggedInUser" title="User Task|Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;</div>
            <div id="styleSwitcher" style="text-align:right">Themes: <span id="defaultStyle" class="style" title="Style Switcher|Default Colors"><a href="#" onClick="setActiveStyleSheet('default'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="seasonalStyle" class="style" title="Style Switcher|Christmas Season"><a href="#" onClick="setActiveStyleSheet('seasonal'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="nightStyle" class="style" title="Style Switcher|Night Time Colors"><a href="#" onClick="setActiveStyleSheet('night'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span>&nbsp;</div>
          </div>
      </div>		      
   </div>
    
   <div id="bd">
       <div class="yui-g" id="mm"><?php include($default['FS_HOME'].'/include/main_menu.php'); ?></div>
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->
          <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
            <div id="addPositionContent" style="display:none">
              <table  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="30" colspan="2" class="BGAccentVeryDark">&nbsp;<b>Add Position Title</b></td>
                      </tr>
                      <tr>
                        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="2">
                            <tr class="BGAccentDark">
                              <td height="25" class="padding"><strong>
                                <vllabel form="Form" validators="grade" errclass="valError">Grade</vllabel>
                              </strong></td>
                              <td height="25" class="padding"><strong>
                                <vllabel form="Form" validators="title_name" errclass="valError">Position Title</vllabel>
                              </strong></td>
                              <td height="25" class="padding"><strong>
                                <vllabel form="Form" validators="min" errclass="valError">Minimum</vllabel>
                              </strong></td>
                              <td height="25" valign="middle" class="padding"><strong>
                                <vllabel form="Form" validators="max" errclass="valError">Maximum</vllabel>
                              </strong></td>
                              <td valign="middle" class="padding"><strong><vllabel form="Form" validators="ot" errclass="valError">OT</vllabel></strong></td>
                              <td valign="middle" class="padding"><strong><vllabel form="Form" validators="flsa" errclass="valError">FLSA</vllabel></strong></td>
                            </tr>
                            <tr>
                              <td><select name="grade" id="grade" onChange="payrate(this.options[this.selectedIndex].value);">
                                  <option value="0">Select</option>
                                  <?php
                                    $grades_sth = $dbh->execute($grades_sq1);
                                    while($grades_sth->fetchInto($DATA)) {
                                      echo "<option value=\"" . $DATA['grade'] . "\">" . $DATA['grade'] . "</option>";
                                    }
                                  ?>
                                </select>
                                  <vlvalidator name="grade" type="compare" control="grade" validtype="string" comparevalue="0" comparecontrol="grade" operator="ne"></td>
                              <td><input name="title_name" type="text" size="50" maxlength="50">
                                  <vlvalidator name="title_name" type="required" control="title_name"></td>
                              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td>$</td>
                                    <td><input name="min" id="min" type="text" size="10" maxlength="7">
                                        <vlvalidator name="min" type="required" control="min" minlength="5" maxlength="7"></td>
                                  </tr>
                              </table></td>
                              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td>$</td>
                                    <td><input name="max" id="max" type="text" size="10" maxlength="7">
                                        <vlvalidator name="max" type="required" control="max" minlength="5" maxlength="7"></td>
                                  </tr>
                              </table></td>
                              <td>
                                <select name="ot" id="ot">
                                  <option value="0">Select One</option>
                                  <option value="E">Exempt</option>
                                  <option value="ST">Straight</option>
                                  <option value="TH">Time/Half</option>
                                </select>
                                <vlvalidator name="ot" type="compare" control="ot" validtype="string" comparevalue="0" comparecontrol="ot" operator="ne">
                              </td>
                              <td>
                                <select name="flsa" id="flsa">
                                  <option value="0">Select One</option>
                                  <option value="N">Non-Exempt</option>
                                  <option value="E">Exempt</option>
                                </select>
                                <vlvalidator name="flsa" type="compare" control="flsa" validtype="string" comparevalue="0" comparecontrol="flsa" operator="ne">
                              </td>
                            </tr>
                        </table></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td height="5"><img src="../../images/spacer.gif" width="5" height="5"></td>
                </tr>
                <tr>
                  <td align="right"><input type="hidden" name="action" value="add">
                      <input name="imageField" type="image" src="../../images/button.php?i=b70.png&l=Add" border="0">
                    &nbsp; </td>
                </tr>
              </table>
            </div>
          </form>
            <table  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                        <td height="30" colspan="2"><table width="100%" border="0">
                          <tr>
                            <td><?php if ($_GET['clean'] == 'true') { ?><a href="<?= $_SERVER['PHP_SELF']; ?>" title="Database Content|Edit Position Title" target="_blank" class="mainsection"><img src="/Common/images/form-update.gif" width="20" height="18" border="0" align="absmiddle">&nbsp;Edit Position Title&nbsp;</a><?php } ?></td>
                            <td align="right"><a href="javascript:void();" title="Database Content|New Position Title" id="openPosition" class="mainsection"><img src="../../images/add.gif" width="14" height="14" border="0" align="absmiddle">&nbsp;Add New Position Title&nbsp;</a><a href="javascript:void();" title="Database Content|New Position Title" id="closePosition" style="display:none" class="mainsection"><img src="/Common/images/subtraction.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;Close New Position Title&nbsp;</a></td>
                          </tr>
                        </table></td>
                    </tr>
                      <tr>
                        <td height="30" colspan="2" class="BGAccentVeryDark">&nbsp;<b>Position Titles </b></td>
                      </tr>
                      <tr>
                        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="2" id="positionTable">
                            <tr class="BGAccentDark">
                              <td height="25" class="padding">&nbsp;</td>
                              <td height="25" class="padding"><strong>Grade</strong></td>
                              <td height="25" class="padding"><strong>Position Title </strong></td>
                              <td height="25" class="padding"><strong>Minimum</strong></td>
                              <td height="25" valign="middle" class="padding"><strong>Maximum</strong></td>
                              <td valign="middle" class="padding"><strong>OT</strong></td>
                              <td valign="middle" class="padding"><strong>FLSA</strong></td>
                            </tr>
                            <?php
								$settings_sth = $dbh->execute($settings_sq1);
								while($settings_sth->fetchInto($SETTINGS)) {
						    ?>
                            <tr>
                              <td valign="top"><?php if ($_GET['clean'] != 'true') { ?><a href="positionTitle_Process.php?title_id=<?= $SETTINGS['title_id']; ?>&action=edit" title="Database Content|Edit Position Title" rel="gb_page_center[500, 260]"><img src="../../images/detail.gif" width="18" height="20" border="0"></a><a href="positionTitle_Process.php?title_id=<?= $SETTINGS['title_id']; ?>&action=delete" title="Database Content|Delete Position Title" rel="gb_page_center[500, 260]"><img src="../../images/Disable.gif" width="17" height="17" border="0"></a><?php } ?></td>
                              <td align="center" valign="top"><?= $SETTINGS['grade']; ?></td>
                              <td valign="top" class="padding2"><?= caps($SETTINGS['title_name']); ?></td>
                              <td valign="top" class="padding2">$ <?= $SETTINGS['min']; ?></td>
                              <td valign="top" class="padding2">$ <?= $SETTINGS['max']; ?></td>
                              <td align="center" valign="top"><?= ($SETTINGS['ot'] == "TH") ? "1&frac12" : $SETTINGS['ot']; ?></td>
                              <td align="center" valign="top"><?= $SETTINGS['flsa']; ?></td>
                            </tr>
                            <?php } ?>
                        </table></td>
                      </tr>
                  </table></td>
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

<script type="text/javascript" src="../../js/jQdefault.js"></script>
<!-- InstanceBeginEditable name="js" -->
<script type="text/javascript" src="../../js/jQposition.js"></script>
<script language="JavaScript">
	function payrate(grade) {
		var min=new Array();
		<?php
			$min_sth = $dbh->execute($grades_sq1);
			while($min_sth->fetchInto($MIN)) {
			  echo "min[" . $MIN['grade'] . "] = \"" . $MIN['min'] . "\";\n";
			}
		?>
		var max=new Array();
		<?php
			$max_sth = $dbh->execute($grades_sq1);
			while($max_sth->fetchInto($MAX)) {
			  echo "max[" . $MAX['grade'] . "] = \"" . $MAX['max'] . "\";\n";
			}
		?>	
		document.getElementById("min").value = min[grade];
		document.getElementById("max").value = max[grade];
	}
</script>    
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