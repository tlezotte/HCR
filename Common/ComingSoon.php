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
require_once('../include/BlackBerry.php');

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
 * - PEAR QuickForm
 */
require_once ('HTML/QuickForm.php');

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
		$forward = "Request/detail.php?id=".$DATA['id'];
	} else {
		$_SESSION['error'] = "HC-".$_POST['number']." was not found";
		$forward = "error.php";
	}

	header("Location: ".$forward);
	exit();
}



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
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>
  <?php if ($default['pageloading'] == 'on') { ?>
  <script language="JavaScript" src="/Common/Javascript/pageloading.js" type="text/javascript"></script>
  <?php } ?>
  <script language="JavaScript" src="/Common/Javascript/pointers.js" type="text/javascript"></script>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --><!-- InstanceBeginEditable name="head" -->
  <script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
  </script>
  <!-- InstanceEndEditable -->
  </head>

  <body <?= $ONLOAD; ?>>
    <?php if ($default['pageloading'] == 'on') { ?>
	<div id="hidepage" class="loadpage"> 
	<table width=100%><tr><td>&nbsp;&nbsp;<img src="/Common/images/pageloading.gif" width="200" height="45" align="absmiddle"></td></tr></table></div> 
	<?php } ?>  
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <img src="/Common/images/companyPrint.gif" alt="your company" name="Print" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../index.php"><img name="company" src="/Common/images/company.gif" width="300" height="50" border="0" alt="<?= $language['label']['title1']; ?> Home"></a></td>
          <td align="right" valign="top">
            <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable -->          </td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3">

            <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4"></td>

                  <td colspan="4">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td class="BGColorDark" valign="top" rowspan="2">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4"></td>
                </tr>

                <tr>
                  <td class="BGGrayLight" rowspan="3"></td>
                  <td class="BGGrayMedium" rowspan="3"></td>
                  <td class="BGGrayDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3">
				<!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_spacer.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="../images/t.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>

                  <td class="BGColorDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="2"></td>
                  <td class="BGColorDark" rowspan="2"></td>
                  <td class="BGColorDark" rowspan="2"></td>
                  <td class="BGGrayDark" rowspan="2"></td>
                  <td class="BGGrayMedium" rowspan="2"></td>
                  <td class="BGGrayLight" rowspan="2"></td>
                </tr>

                <tr>
                  <td class="BGColorDark" width="100%">
				  <?php 
				  	if (isset($_SESSION['username'])) {
				  ?>
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="../Administration/user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>
				  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                  </td>
                </tr>

                <tr>
                  <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25"></td>

                  <td valign="top" colspan="2">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4"></td>
                </tr>

                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4"></td>

                  <td>
                    <table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                      <tbody>
                        <tr>
                          <td>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3"></td>

                  <td colspan="7">
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" -->
    <div align="center">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top"><div align="center"><br>
            <br>
            <br>
            <br>
                <span class="DarkHeaderSubSub">
                Coming Soon... </span><span class="DarkHeaderSubSub">
                </span><br>
            <br>
            <br>
            <br>
            <br>
            <span class="NavBarInactiveLink">Select from  menu on the right side of the screen<br>
            </span></div></td>
        </tr>
        <tr>
          <td valign="top"><img src="../images/spacer.gif" width="10" height="50"></td>
        </tr>
        
        <tr>
          <td valign="top"><!--<strong>&nbsp;&nbsp;Quickmenu:</strong> <a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('Help/References/company.php','references','resizable=yes,width=800,height=800')" <?php help('', 'your company Credit References','default'); ?>>References</a> <img src="images/Dot.gif" width="10" height="10"> <a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('Request/track.php','track','resizable=yes,width=825,height=850')" <?php help('', 'Track Shipments or Deliveries from FedEx, UPS, USPS and DHL','default'); ?>>Track Shipments</a>--> </td>
        </tr>
      </table>      
      <span class="NavBarInactiveLink"><br>
      </span>
    </div>
    <!-- InstanceEndEditable --><br>
    <br>

    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td colspan="2">
          </td>
        </tr>

        <tr>
          <td>
          </td>
          <td rowspan="2" valign="bottom"><img src="../images/c-skir.gif" alt="" width="19" height="20" align="absmiddle" id="noPrint"></td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" nowrap><!-- InstanceBeginEditable name="copyright" --><?php include('../include/copyright.php'); ?><!-- InstanceBeginEditable --></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" -->
                  <div align="right"><a href="javascript:void(0);" <?php help('', 'Release Notes', 'default'); ?>><img src="../images/notes.gif" width="12" height="15" border="0" align="absmiddle" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"></a>&nbsp;&nbsp;<a href="javascript:void(0);" <?php help('', 'Really Simple Syndication (RSS)', 'default'); ?>><img src="../images/livemarks16.gif" width="16" height="16" border="0" align="absmiddle" onClick="MM_openBrWindow('../Help/RSS/overview.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"></a></div>
                <!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td colspan="2">
          </td>
        </tr>
      </tbody>
  </table>
   <?php if ($_SESSION['hcr_access'] >= 1) { ?>
  	<div class="TrainVisited" id="noPrint">
    <?php StopLoadTimer(); ?>
    <img src="../images/spacer.gif" width="50" height="16" align="absmiddle">
    <?= onlineCount(); ?>
	</div>
   <?php } ?>
    <br>
	
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
