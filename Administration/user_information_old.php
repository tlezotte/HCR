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
require_once('../Connections/connStandards.php'); 
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


/**
 * - Process $_POST['action']
 */
switch ($_POST['action']) {
	case 'update':
		$sql="UPDATE Employees SET dept='" . $_POST['dept'] . "', 
									shift='" . $_POST['shift'] . "', 
									phn='" . $_POST['phn'] . "', 
									lst='" . $_POST['lst'] . "', 
									fst='" . $_POST['fst'] . "', 
									mdl='" . $_POST['mdl'] . "', 
									Job_Description='" . $_POST['Job_Description'] . "', 
									Location='" . $_POST['Location'] . "', 
									language='" . $_POST['language'] . "', 
									email='" . $_POST['email'] . "' 
					   WHERE eid='" . $_SESSION['eid'] . "'";					   
		$dbh_standards->query($sql);

		/* Set language preference */
		setcookie(language, $_POST['language'], $cookie_expire, $default['url_home'] . "/");
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));							   
	break;
	
	case 'changepassword':
		$sql="UPDATE Employees SET password='" . $_POST['newpassword1'] . "' WHERE eid='" . $_SESSION['eid'] . "'";
		$dbh_standards->query($sql);	
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));			
	break;
}

/* ----- GET EMPLOYEE INFORMATION ----- */
$INFO = $dbh_standards->getRow("SELECT * FROM Employees WHERE eid='" . $_SESSION['eid'] . "'");

/* Get Plant information */
$plants_sql = $dbh->prepare("SELECT id, name
						     FROM Standards.Plants
						     WHERE status = '0'
						     ORDER BY name");
/* Get Department information */							 
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status = '0' 
						   ORDER BY name");



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
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
  <!-- InstanceEndEditable -->
  </head>

  <body onLoad="MM_preloadImages('../images/button.php?i=inputField.png&amp;l=<?= $INFO[password]; ?>')" <?= $ONLOAD; ?>>
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
            <!-- InstanceBeginEditable name="topRightMenu" -->
              <div align="right" style="font-weight:bold;font-size:115%">
                <?= $language['label']['title1']; ?>
                &nbsp;</div>
              <div align="right" class="FieldNumberDisabled"><strong><a href="user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>>
                <?= ucwords(strtolower($_SESSION['fullname'])); ?>
              </a></strong>&nbsp;<a href="../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>			
		  <!-- InstanceEndEditable -->          </td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include($default['FS_HOME'].'/include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

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
				<!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['hcr_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="../images/t.gif" width="200" height="5" border="0"></td>
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
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu3; ?>" class="<?= $menu3_css; ?>"> Settings </a></td>	
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="<?= $menu2; ?>" class="<?= $menu2_css; ?>"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
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
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>
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
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><!-- #BeginLibraryItem "/Library/user_admin.lbi" -->
          <table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
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
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><!-- #BeginLibraryItem "/Library/history.lbi" -->
        <script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
        </script>
        <?php if ($_SESSION['hcr_access'] == 3) { ?>
        <table width="190"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><span class="ColorHeaderSubSub">Administration</span> </td>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('history.php?page=<?= $_SERVER[PHP_SELF]; ?>','history','scrollbars=yes,resizable=yes,width=875,height=800')" <?php help('', 'Get the history of this page', 'default'); ?>><strong> History </strong></a></td>
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
        <?php } ?>
        <!-- #EndLibraryItem --></td>
      </tr>
    </table></td>
    <td align="center">
	<form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
    <br>
    <br>
    <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="BGAccentVeryDark"><div align="left">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" height="30" class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;My Account...</td>
                  <td width="50%"><div align="left"> </div></td>
                </tr>
              </table>
          </div></td>
        </tr>
        <tr>
          <td class="BGAccentVeryDarkBorder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                <tr>
                  <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub">
                    Personal Information
                  </span></strong></td>
                </tr>
                <tr>
                  <td><table width="100%"  border="0">
                    <tr>
                      <td width="150">Employee ID:</td>
                      <td><?= $INFO['eid']; ?></td>
                    </tr>
                    <tr>
                      <td>Name:</td>
                      <td><input name="fst" type="text" id="fst" size="20" maxlength="20" value="<?= $INFO['fst']; ?>">
                          <vlvalidator name="fst" type="required" control="fst" errmsg="Your first name is required.">
                          <input name="mdl" type="text" id="mdl" size="5" maxlength="10" value="<?= $INFO['mdl']; ?>">
                          <input name="lst" type="text" id="lst" size="30" maxlength="30" value="<?= $INFO['lst']; ?>">
                          <vlvalidator name="lst" type="required" control="lst" errmsg="Your last name is required."></td>
                    </tr>
                    <tr>
                      <td>Email:</td>
                      <td><input name="email" type="text" id="email" size="50" maxlength="50" value="<?= $INFO['email']; ?>">
                          <vlvalidator name="email" type="email" control="email" errmsg="Email address is incorrect."></td>
                    </tr>
                    <tr>
                      <td>Plant:</td>
                      <td><select name="Location">
                          <option value="0">Select One</option>
                          <?php
						  $plant_sth = $dbh->execute($plants_sql);
						  while($plant_sth->fetchInto($PLANTS)) {
							$selected = ($INFO['Location'] == $PLANTS[id]) ? selected : $blank;
							print "<option value=\"".$PLANTS[id]."\" ".$selected.">".ucwords(strtolower($PLANTS[name]))."</option>\n";
						  }
						  ?>
                      </select></td>
                    </tr>
                    <tr>
                      <td>Department:</td>
                      <td><select name="dept" id="dept">
                          <option value="0">Select One</option>
                          <?php
						  $dept_sth = $dbh->execute($dept_sql);
						  while($dept_sth->fetchInto($DEPT)) {
							$selected = ($INFO['dept'] == $DEPT[id]) ? selected : $blank;
							print "<option value=\"".$DEPT[id]."\" ".$selected.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
						  }
						  ?>
                      </select></td>
                    </tr>
                    <tr>
                      <td>Shift: </td>
                      <td><select name="shift">
                          <option value="1" <?= ($INFO['shift'] == '1') ? selected : $blank; ?>>First</option>
                          <option value="2" <?= ($INFO['shift'] == '2') ? selected : $blank; ?>>Second</option>
                          <option value="3" <?= ($INFO['shift'] == '3') ? selected : $blank; ?>>Third</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td>Job Description: </td>
                      <td><input name="Job_Description" type="text" id="Job_Description" size="40" maxlength="40" value="<?= $INFO['Job_Description']; ?>"></td>
                    </tr>
                    <tr>
                      <td>Phone:</td>
                      <td><input name="phn" type="text" id="phn" size="15" maxlength="15" value="<?= $INFO['phn']; ?>"></td>
                    </tr>
                    <tr>
                      <td>Hire Date: </td>
                      <td><?= date("F d, Y", strtotime($INFO['hire'])); ?></td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                <tr>
                  <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub"> Application Information </span></strong></td>
                </tr>
                <tr>
                  <td><table width="100%" border="0">
                    <tr>
                      <td width="150">Language:</td>
                      <td><select name="language">
                        <option value="en" <?= ($INFO['language'] == 'en') ? selected : $blank; ?>>English</option>
                        <option value="fr" <?= ($INFO['language'] == 'fr') ? selected : $blank; ?>>French</option>
                      </select>
                      </td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
          </table>
          </td>
        </tr>
        <tr>
          <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
        </tr>
        <tr>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a href="authorization.php">&nbsp;</a></td>
                <td><div align="right">
                    <input name="action" type="hidden" id="action" value="update">
                    <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Update" alt="Update" border="0">
                  &nbsp; </div></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td><vlsummary class="valErrorList" headertext="Error(s) found:" displaymode="bulletlist"></td>
        </tr>
      </table>
	</form>
	  <br>
	  <br>
	  <br>
    <form name="Form2" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
      <br>
      <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="BGAccentVeryDark"><div align="left">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<a name="password"></a>Change Password...</td>
                  <td width="50%"><div align="left"> </div></td>
                </tr>
              </table>
          </div></td>
          </tr>
        <tr>
          <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
              
              <tr>
                <td>Current Password:</td>
                <td><img src="../images/button.php?i=inputField.png&l=Mouseover&c=warn" width="146" height="22" id="Image11" onMouseOver="MM_swapImage('Image11','','../images/button.php?i=inputField.png&amp;l=<?= $INFO[password]; ?>',1)" onMouseOut="MM_swapImgRestore()"></td>
              </tr>
              <tr>
                <td><vllabel form="Form2" validators="newpassword1" errclass="valError">New Password:</vllabel></td>
                <td><input name="newpassword1" type="password" id="newpassword1" size="20" maxlength="20">
                  <vlvalidator name="newpassword1" type="required" control="newpassword1" errmsg="New Password requires 7-20 characters." minlength="7">
                  <vlvalidator name="PassCmp" type="compare" control="newpassword1" errmsg="Both Password fields must be equal" validtype="string" comparecontrol="newpassword2" operator="e"></td>
              </tr>
              <tr>
                <td nowrap><vllabel form="Form2" validators="newpassword2" errclass="valError">Confirm New Password:</vllabel></td>
                <td><input name="newpassword2" type="password" id="newpassword2" size="20" maxlength="20">
                  <vlvalidator name="newpassword2" type="required" control="newpassword2" errmsg="Change New Password requires 7-20 characters ." minlength="7"></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
          </tr>
        <tr>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a href="authorization.php">&nbsp;</a></td>
                <td><div align="right">
                  <input name="action" type="hidden" id="action" value="changepassword">
                    <input name="imageField2" type="image" src="../images/button.php?i=b70.png&l=Change" alt="Change" border="0">
                  &nbsp; </div></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td><vlsummary class="valErrorList" headertext="Error(s) found:" displaymode="bulletlist"></td>
          </tr>
      </table>
    </form>
    <br>
    </td>
    <td width="200" align="left" valign="top">&nbsp;</td>
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
          <td rowspan="2" valign="bottom"><img src="images/c-skir.gif" alt="" width="19" height="20" align="absmiddle" id="noPrint"></td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" nowrap><?php include('../include/copyright.php'); ?></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" -->
                  <?php include($default['FS_HOME'].'/include/version.php'); ?>
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