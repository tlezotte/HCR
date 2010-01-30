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
include_once('../debug/header.php');

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

/* ----- START ADD VARIABLE ----- */
if ($_POST['action'] == "add") {
	$dbh->query("INSERT into Settings VALUES(NULL, '".$_POST['company']."','".$_POST['variable']."','".$_POST['value']."')");
}
/* ----- END ADD VARIABLE ----- */

/* ----- START UPDATE VARIABLE ----- */
if ($_POST['action'] == "update") {
	if ($_POST['delete'] == "yes") {
		$dbh->query("DELETE from Settings WHERE id=".$_POST['id']."");
	} else {
		$dbh->query("UPDATE Settings ".
					"SET company='".$_POST['company']."', variable='".$_POST['variable']."', value='".$_POST['value']."' ".
					"WHERE id=".$_POST['id']."");
	}
}
/* ----- END UPDATE VARIABLE ----- */

$COMPANY = $dbh->getAssoc("SELECT id, name 
						   FROM Standards.Companies 
						   WHERE status <> '1'
						   ORDER BY id");
$settings_sq1 = $dbh->prepare("SELECT * FROM Settings WHERE company = ? ORDER BY variable");
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
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Human Capital Request Announcements" href="<?= $default['../URL_HOME']; ?>/Request/<?= $default['rss_file']; ?>">
  <?php } ?>
  <?php if ($default['pageloading'] == 'on') { ?>
  <script language="JavaScript" src="/Common/Javascript/pageloading.js" type="text/javascript"></script>
  <?php } ?>
  <script language="JavaScript" src="/Common/Javascript/pointers.js" type="text/javascript"></script>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/googleAutoFillKill.js"></SCRIPT>
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --><!-- InstanceBeginEditable name="head" -->    
    <style type="text/css">
<!--
.mainsection {	font-size: 9pt;
	font-weight: bold;
	padding-left: 10px;
}
.subsection { 	font-size: 9pt; 
 	 font-weight: normal;
 		margin-left: 15px;
}
-->
    </style>
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
          <td valign="top"><a href="../../index.php"><img name="company" src="/Common/images/company.gif" width="300" height="50" border="0" alt="<?= $language['label']['title1']; ?> Home"></a></td>
          <td align="right" valign="top">
            <!-- InstanceBeginEditable name="topRightMenu" --><!-- #BeginLibraryItem "/Library/help.lbi" --><?php
$menu1 = $default['url_home'] . "/Employees/index.php";
$menu1_image = ($_SERVER['REQUEST_URI'] == $menu1) ? team_a : team;
$menu1_image_url = $default['url_home'] . "/images/". $menu1_image . ".gif";

$menu2 = $default['url_home'] . "/Calendar/index.php";
$menu2_image = ($_SERVER['REQUEST_URI'] == $menu2) ? calendar_a : calendar;
$menu2_image_url = "/Common/images/". $menu2_image . ".gif";
?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<table cellspacing="0" cellpadding="0" summary="" border="0">
<tr>
<?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
  <td><a href="<?= $menu1; ?>" <?php help('', 'your company Employee List', 'default'); ?>><img src="<?= $menu1_image_url; ?>" width="16" height="18" border="0"></a></td> 
  <td><img src="/Common/images/spacer.gif" width="15" height="18" /></td>  
  <td><a href="<?= $menu2; ?>" <?php help('', 'your company Start Date Calendar', 'default'); ?>><img src="<?= $menu2_image_url; ?>" width="18" height="18" border="0"></a></td>  
  <td><img src="/Common/images/spacer.gif" width="15" height="18" /></td> 
<?php } ?> 
  <td><a href="javascript:void(0);" onClick="MM_openBrWindow('../../Help/index.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
  <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../../Help/index.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">Help</a></td>
</tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable -->          </td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include('../../include/rightmenu.php'); ?>
            <?php include($default['FS_HOME'].'/include/menu/main_right.php'); ?>
          <!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3">

            <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghtl.gif" width="4"></td>

                  <td colspan="4">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ght.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td class="BGColorDark" valign="top" rowspan="2">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ght.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghtr.gif" width="4"></td>
                </tr>

                <tr>
                  <td class="BGGrayLight" rowspan="3"></td>
                  <td class="BGGrayMedium" rowspan="3"></td>
                  <td class="BGGrayDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3">
				<!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['hcr_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="../../images/t.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<?php } else { ?>
<?php
$menu1 = $default['url_home'] . "/Administration/utilities.php";
$menu1_css = ($_SERVER['REQUEST_URI'] == $menu1) ? on : off;

$menu2 = $default['url_home'] . "/Administration/db/index.php";
$menu2_css = (preg_match("/db/", $_SERVER['REQUEST_URI'])) ? on : off;

$menu3 = $default['url_home'] . "/Administration/settings.php";
$menu3_css = ($_SERVER['REQUEST_URI'] == $menu3) ? on : off;

$menu4 = $default['url_home'] . "/Administration/users.php";
$menu4_css = ($_SERVER['REQUEST_URI'] == $menu4) ? on : off;
?>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tr>
	<td>&nbsp;</td>
	<td><table cellspacing="0" cellpadding="0" summary="" border="0">
		<tr>
		  <td nowrap><a href="<?= $menu4; ?>" class="<?= $menu4_css; ?>"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu3; ?>" class="<?= $menu3_css; ?>"> Settings </a></td>	
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu2; ?>" class="<?= $menu2_css; ?>"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu1; ?>" class="<?= $menu1_css; ?>"> Utilities </a></td>			  			  
		  <td nowrap>&nbsp;</td>
		</tr>
	</table></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
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
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="../user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>
				  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                  </td>
                </tr>

                <tr>
                  <td valign="top"><img height="20" alt="" src="../../images/c-ghct.gif" width="25"></td>

                  <td valign="top" colspan="2">
                    <table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ghb.gif" border="0">
                      <tbody>
                        <tr>
                          <td height="4">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td valign="top" colspan="4"><img height="20" alt="" src="../../images/c-ghbr.gif" width="4"></td>
                </tr>

                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghbl.gif" width="4"></td>

                  <td>
                    <table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ghb.gif" border="0">
                      <tbody>
                        <tr>
                          <td>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>

                  <td><img height="4" alt="" src="../../images/c-ghcb.gif" width="3"></td>

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
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="200" valign="top"><table cellspacing="0" cellpadding="0" width="200" align="left"
summary="" border="0">
          <tbody>
            <tr>
              <td valign="top" width="13" background="../../images/asyltlb.gif"><img height="20" alt="" src="../../images/t.gif" width="13" border=
	  "0"></td>
              <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../../images/asybase.gif" width="145"
		border="0"> <br>
                  <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="#add" class="dark">Add New Variable </a></td>
                    </tr>
                  </table>
              </td>
              <td valign="top" width="22" background="../../images/asyltrb.gif"><img height="20" alt="" src="../../images/t.gif" width="22" border=
	  "0"></td>
            </tr>
            <tr>
              <td valign="top" width="22" colspan="3"><img height="37" alt=""
	  src="../../images/asyltb.gif" width="200" border="0"></td>
            </tr>
          </tbody>
        </table></td>
        <td valign="top"><br>
          <table  border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><?php 
					for ($i = 0; $i <= count($COMPANY) - 1; $i++) {
				  ?>
                  <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td height="30" colspan="2" class="BGAccentVeryDark">&nbsp;<b><?= $COMPANY[$i]; ?> </b></td>
                    </tr>
                    <tr>
                      <td colspan="2" class="BGAccentVeryDarkBorder"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                          <?php
							$settings_sth = $dbh->execute($settings_sq1, array($i));
							while($settings_sth->fetchInto($SETTINGS)) {
						  ?>
                          <form name="Form<?= $SETTINGS[id]; ?>" method="post" action="<?= $_SERVER['../PHP_SELF']; ?>">
                            <tr>
                              <td height="25">&nbsp;
                                  <input name="variable" type="text" value="<?= $SETTINGS[variable]; ?>" size="20"></td>
                              <td height="25">&nbsp;
                                  <input name="value" type="text" value="<?= $SETTINGS[value]; ?>" size="50">
                                  <input type="hidden" name="id" value="<?= $SETTINGS[id]; ?>">
                                  <input type="hidden" name="company" value="<?= $i; ?>">
                                  <input type="hidden" name="action" value="update">
                                  <input name="delete" type="checkbox" id="delete" value="yes">
                              </td>
                            <td valign="middle"><input name="imageField" type="image" src="../../images/button.php?i=b70.png&l=Update" border="0"></td>
                            </tr>
                          </form>
                          <?php } ?>
                      </table></td>
                    </tr>
                  </table>
                  <br>
                  <?php } ?>
              </td>
            </tr>
          </table>
          <br>
          <form action="<?= $_SERVER['../PHP_SELF']; ?>" method="post" name="formAdd" id="formAdd">
            <a name="add"></a>
            <table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr class="BGAccentVeryDark">
                <td height="30"><strong>&nbsp;Add New Variable </strong></td>
              </tr>
              <tr>
                <td height="25" class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="25"><strong>Company&nbsp;</strong></td>
                      <td><select name="company" id="company">
                          <?php 
							for ($i = 0; $i <= count($COMPANY) - 1; $i++) {
						  ?>
                          <option value="<?= $i; ?>"><?= $COMPANY[$i]; ?></option>
                          <?php } ?>
                      </select></td>
                    </tr>
                    <tr>
                      <td height="25"><strong>Variable</strong></td>
                      <td><input name="variable" type="text" id="variable" maxlength="50"></td>
                    </tr>
                    <tr>
                      <td height="25"><strong>Value</strong></td>
                      <td><input name="value" type="text" id="value" size="50" maxlength="100"></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="30"><div align="right">
                    <input name="action" type="hidden" id="action" value="add">
                  <input name="imageField" type="image" src="../../images/button.php?i=b70.png&l=Add" border="0">
				&nbsp;</div></td>
              </tr>
            </table>
        </form></td>
      </tr>
    </table>
	<br>
  <!-- InstanceEndEditable --><br>
    <br>   <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
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
                <td width="50%" nowrap><?php include('../../include/copyright.php'); ?></td>
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
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></div></td>
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
    <img src="../../images/spacer.gif" width="50" height="16" align="absmiddle">
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
include_once('../debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>