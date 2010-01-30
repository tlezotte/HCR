<?php
/**
 * Request System
 *
 * summary.php lists the usage of some sections.
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

/* Module Totals */
$totals_sql = "SELECT module, count(module) AS Access FROM Summary
               GROUP BY module
               ORDER BY Access DESC";
$totals_query = $dbh->prepare($totals_sql);
$totals_sth = $dbh->execute($totals_sql);
$totals_rows = $totals_sth->numRows();

/* Employee Totals */
$totals2_sql = "SELECT eid, count(eid) AS Access FROM Summary
               GROUP BY eid
               ORDER BY Access DESC";
$totals2_query = $dbh->prepare($totals2_sql);
$totals2_sth = $dbh->execute($totals2_query);
$totals2_rows = $totals2_sth->numRows();

/* Detailed Module Information */
$summary_sql = "SELECT *
				FROM Summary
				ORDER BY access DESC";	   
$summary_query = $dbh->prepare($summary_sql); 
$summary_sth = $dbh->execute($summary_query);
$num_rows = $summary_sth->numRows();

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");
/* ------------- END DATABASE CONNECTIONS --------------------- */



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
                      <table  border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="BGAccentVeryDark"><div align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Module Summary... </td>
                                        <td>&nbsp;</td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><table width="100%"  border="0">
                                          <tr>
                                            <td class="BGAccentDark"><strong>&nbsp;Module</strong></td>
                                            <td class="BGAccentDark"><strong>&nbsp;Access<strong><img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</strong></strong></td>
                                          </tr>
                                          <?php
									/* Reset items total variable */
									$itemsTotal = 0;
									
									while($totals_sth->fetchInto($TOTALS)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									?>
                                          <tr <?php pointer($row_color); ?>>
                                            <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $TOTALS['module']; ?></td>
                                            <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $TOTALS['Access']; ?></td>
                                          </tr>
                                          <?php } // End SUMMARY while ?>
                                      </table></td>
                                    </tr>
                                </table></td>
                              </tr>
                              <tr>
                                <td valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td valign="top">&nbsp;<span class="GlobalButtonTextDisabled">
                                        <?= $totals_rows ?> Modules</span> </td>
                                      <td valign="bottom"><div align="right"> </div></td>
                                    </tr>
                                </table></td>
                              </tr>
                            </table></td>
                            <td width="20">&nbsp;</td>
                          <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td class="BGAccentVeryDark"><div align="left">
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Employee Summary... </td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                              </div></td>
                            </tr>
                            <tr>
                              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><table width="100%"  border="0">
                                        <tr>
                                          <td class="BGAccentDark"><strong>&nbsp;Employee</strong></td>
                                          <td class="BGAccentDark"><strong>&nbsp;Access<strong><img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</strong></strong></td>
                                        </tr>
                                        <?php
									/* Reset items total variable */
									$itemsTotal = 0;
									
									while($totals2_sth->fetchInto($TOTALS2)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									?>
                                        <tr <?php pointer($row_color); ?>>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($EMPLOYEES[$TOTALS2['eid']])); ?></td>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $TOTALS2['Access']; ?></td>
                                        </tr>
                                        <?php } // End SUMMARY while ?>
                                    </table></td>
                                  </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td valign="top">&nbsp;<span class="GlobalButtonTextDisabled">
                                      <?= $totals2_rows ?> Employees</span> </td>
                                    <td valign="bottom"><div align="right"> </div></td>
                                  </tr>
                              </table></td>
                            </tr>
                          </table></td>
                        </tr>
                          <tr valign="top">
                            <td colspan="3"><br>
                              <table border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="BGAccentVeryDark"><div align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Detailed Usage Summary... </td>
                                        <td>&nbsp;</td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><table width="100%"  border="0">
                                          <tr>
                                            <td class="BGAccentDark"><strong>&nbsp;Module</strong></td>
                                            <td class="BGAccentDark"><strong>&nbsp;Employee</strong></td>
                                            <td class="BGAccentDark"><strong>&nbsp;Date<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</strong></td>
                                          </tr>
                                          <?php
									/* Reset items total variable */
									$itemsTotal = 0;
									
									while($summary_sth->fetchInto($SUMMARY)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									?>
                                          <tr <?php pointer($row_color); ?>>
                                            <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $SUMMARY['module']; ?></td>
                                            <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($EMPLOYEES[$SUMMARY[eid]])); ?></td>
                                            <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?php $access = explode(" ", $SUMMARY[access]); echo $access[0]; ?></td>
                                          </tr>
                                          <?php } // End SUMMARY while ?>
                                      </table></td>
                                    </tr>
                                </table></td>
                              </tr>
                              <tr>
                                <td valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td valign="top">&nbsp;<span class="GlobalButtonTextDisabled">
                                        <?= $num_rows ?>
            Requests</span> </td>
                                      <td valign="bottom"><div align="right"> </div></td>
                                    </tr>
                                </table></td>
                              </tr>
                            </table></td>
                          </tr>
                      </table>
                        <?php } // End num_row if ?>
                        <br>
                  </form>                  </td></tr>
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
