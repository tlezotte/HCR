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

<html><!-- InstanceBegin template="/Templates/vnMain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
  <title><?= $language['label']['title1']; ?></title>
  <script type="text/javascript">
function sf(){ document.Form.field.focus(); }
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
  <!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
  </head>

<body class="yui-skin-sam" onLoad="MM_preloadImages('../images/button.php?i=inputField.png&amp;l=<?= $INFO[password]; ?>')">
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
      <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="200" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
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
          <td align="center"><form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
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
                              <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub"> Personal Information </span></strong></td>
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
