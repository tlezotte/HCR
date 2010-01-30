<?php
/**
 * Request System
 *
 * information.php enduser enters information about PO.
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
 * - Check User Access
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


/* ------------- START PAGE PROCESSING --------------------- */
if ($_POST['stage'] == "two") {	
	/* Set form variables as session variables */
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]  = htmlentities($value, ENT_QUOTES);
	}
	
	/* ----- Forward to router ----- */
	header("Location: technology.php");
	exit();
}
/* ------------- END PAGE PROCESSING --------------------- */



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $language['label']['title1']; ?></title>
	<script type="text/javascript">
<!--
function sf(){ document.Form.company.focus(); }
//-->
</script>
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
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/calendarmws.js"></SCRIPT>
	<script type="text/javascript" src="/Common/Javascript/googiespell/AmiJS.js"></script>
	<script type="text/javascript" src="/Common/Javascript/googiespell/googiespell.js"></script>
	<script type="text/javascript" src="/Common/Javascript/googiespell/cookiesupport.js"></script>
	<link href="/Common/Javascript/googiespell/googiespell.css" rel="stylesheet" type="text/css" media="all" />

<style type="text/css">
  .textarea {
    /*  START GoogieSpell reqs.*/
    line-height: 1em;
    font-size: 1em;
    padding: 2px;
    font-family: sans-serif;
    /*  END GoogieSpell reqs.*/

    width: 600px;
    height: 335px;
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
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" --><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
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
                        <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                      </tr>
                      <tr>
                        <td colspan="9"><table width="100%"  border="0">
                            <tr>
                              <td width="15%" class="wizardPast">Information</td>
                              <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Description</div></td>
                              <td width="25%" class="wizardFuture"><div align="center">Technology</div></td>
                              <td width="25%" class="wizardFuture"><div align="center">Authorization</div></td>
                              <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                            </tr>
                        </table></td>
                      </tr>
                    </table>
				  </div>
                    <br>
					<br>
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="Form" id="Form" runat="vdaemon">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="BGAccentVeryDark"><div align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<strong><img src="../images/notes.gif" width="12" height="15" align="texttop"></strong>                                          <?= $language['label']['stage2']; ?>...</td>
                                        <td width="50%"><div align="left"> </div></td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="BGAccentVeryDarkBorder">
                                  <div class="panelContent">
                                    <table width="100%"  border="0">
<!--                                    <tr>
                                      <td class="valNone"><span>
                                        <?= $language['label']['attachment']; ?>:</span></td>
                                      <td><input name="file" type="file" size="38" value="<?= $_SESSION['file']; ?>" readonly="true"></td>
                                    </tr>-->
                                    <tr>
                                      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td height="25">&nbsp;</td>
                                        </tr>
                                        <tr>
                                          <td><vllabel form="Form" validators="primaryJob" class="valRequired2" errclass="valError">
                                            <?= $language['label']['primaryJob']; ?>:</vllabel></td>
                                        </tr>
                                      </table>
                                      <br></td>
                                      <td><textarea name="primaryJob" cols="50" rows="10" wrap="VIRTUAL" id="primaryJob"><?= stripslashes($_SESSION['primaryJob']); ?></textarea>
                                      <vlvalidator name="primaryJob" type="required" control="primaryJob"></td>
                                    </tr>
                                    <tr>
                                      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td height="25">&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td class="valNone"><?= $language['label']['secondaryJob']; ?>:</td>
                                          </tr>
                                        </table>
                                      </td>
                                      <td><textarea name="secondaryJob" cols="50" rows="10" wrap="VIRTUAL" id="secondaryJob"><?= stripslashes($_SESSION['secondaryJob']); ?></textarea></td>
                                    </tr>
                                </table>
                                </div>
                                </td>
                              </tr>
                              <tr>
                                <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                              </tr>
                              <tr>
                                <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><a href="index.php">&nbsp;<img src="../images/button.php?i=b70.png&l=<?= $language['label']['back']; ?>" border="0"></a></td>
                                    <td><div align="right">
                                      <input name="stage" type="hidden" id="stage" value="two">
                                      <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=<?= $language['label']['next']; ?>" alt="<?= $language['label']['next']; ?>" border="0">
&nbsp;                                    </div></td>
                                  </tr>
                                </table></td>
                              </tr>
                            </table>
                    </form>
						  <script type="text/javascript">
							  var googie1 = new GoogieSpell("/Common/Javascript/googiespell/", "/Common/Javascript/googiespell/spellchecker.php?lang=");
							  googie1.decorateTextarea("primaryJob");
						  </script>
						  <script type="text/javascript">
							  var googie2 = new GoogieSpell("/Common/Javascript/googiespell/", "/Common/Javascript/googiespell/spellchecker.php?lang=");
							  googie2.decorateTextarea("secondaryJob");
						  </script>										  					
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