<?php
/**
 * Request System
 *
 * detail.php displays detailed information on PO.
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
 * PDF Toolkit
 * @link http://www.accesspdf.com/
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
 * - Config Information
 */
require_once('../include/config.php'); 


/* -------------------------------------------------------------
 * ---------- START DESK COORDINATOR PROCESSING ----------------
 * -------------------------------------------------------------
 */
if ($_POST['auth'] == 'desk') {
	/* Insert Desk location */				 
	$desk_sql = "UPDATE Employees 
				 SET desk='".$_POST['desk']."' 
				 WHERE request_id = ".$_POST['request_id'];
	$dbh->query($desk_sql);	


	$forward = "list.php?action=my&access=0";
	header("Location: ".$forward);
	exit();		
}
/* -------------------------------------------------------------
 * ---------- END DESK COORDINATOR PROCESSING ------------------
 * -------------------------------------------------------------
 */	 
	  

/* -------------------------------------------------------------
 * ---------- START DATABASE CONNECTIONS ------------------
 * -------------------------------------------------------------
 */	
/* ---------- Getting Request information ---------- */
$REQUEST = $dbh->getRow("SELECT *, DATE_FORMAT(r.startDate,'%M %e, %Y') AS _startDate, p.name AS plant_name
						FROM Requests r
						  INNER JOIN Employees e ON r.id=e.request_id
						  INNER JOIN Technology t ON r.id=t.request_id
						  INNER JOIN Standards.Plants p ON r.plant=p.id
						  INNER JOIN Position pt ON r.positionTitle=pt.title_id
						WHERE r.id = ".$_GET['id']."
						ORDER BY e.id DESC");
/* ---------- Getting Software ---------- */
$software_sql = "SELECT id, name FROM Standards.Software ORDER BY name";
$software_query = $dbh->prepare($software_sql);	
$SOFTWARE = $dbh->getAssoc($software_sql);	
/* ---------- Get Ethnicity information ---------- */
$ETHNICITY = $dbh->getAssoc("SELECT id, name FROM Ethnicity WHERE status='0' ORDER BY name");
/* ---------- Get EEO information ---------- */
$EEO = $dbh->getAssoc("SELECT id, name FROM EEO WHERE status='0' ORDER BY name");
/* ------------- Getting Department information ------------- */
$DEPARTMENT = $dbh->getAssoc("SELECT id, name FROM Standards.Department");

/* ---------- START REDIRECT NOT NEW HIRES TO _DETAIL.PHP ---------- */
if ($REQUEST['request_type'] != 'new') {
	$forward="_detail.php?id=".$_GET['id'];
	header("Location: ".$forward);
}
/* ---------- END REDIRECT NOT NEW HIRES TO _DETAIL.PHP ---------- */					 				  	
/* -------------------------------------------------------------
 * ---------- END DATABASE CONNECTIONS ------------------
 * -------------------------------------------------------------
 */	

$positionTitle=getPositionTitle($REQUEST['positionTitle'], $REQUEST['request_type']);		// Get position title name


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
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/calendarmws.js"></SCRIPT>	
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/usableforms.js"></SCRIPT>	
    <style type="text/css">
<!--
.textarea {
	width: 600px;
}
-->
    </style>
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
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td>
					  <table><tbody id="waitingRoom" style="display:none"></tbody></table>
					  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td></td>
                                  <td height="26" valign="top"><div align="right">&nbsp;</div></td>
                                </tr>
                                <tr class="BGAccentVeryDark">
                                  <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;
                                      <?= $language['label']['title1']; ?>...</td>
                                  <td width="50%" align="right"><span class="DarkHeader"> Number: HC-<?= $_GET['id']; ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp; </span></td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td class="BGAccentDarkBorder"><table width="100%" border="0">
                                      <tr>
                                        <td height="25" class="BGAccentDark">&nbsp;&nbsp;<strong><img src="../images/personal.gif" width="16" height="16" border="0" align="texttop">&nbsp;Employee Information...</strong></td>
                                      </tr>
                                      <tr>
                                        <td nowrap><div id="div">
                                            <div  id="employeeinformation" style="display:<?= $showContent['employeeinformation']; ?>">
                                              <table width="100%" border="0">
                                                <tr>
                                                  <td width="150" nowrap>
                                                    <?= $language['label']['actualStart']; ?>:</td>
                                                  <td width="200"><strong>
                                                    <?= $REQUEST['_startDate']; ?>
                                                  </strong></td>
                                                  <td width="150" nowrap><?= $language['label']['positionTitle']; ?>: </td>
                                                  <td><strong>
                                                    <?= ucwords(strtolower($positionTitle['title_name'])); ?>
                                                  </strong></td>
                                                </tr>
                                                <tr>
                                                  <td height="5" colspan="4"><hr width="100%" size="1" noshade color="#CCCC99"></td>
                                                </tr>
                                                <tr>
                                                  <td nowrap><?= $language['label']['eid']; ?>: </td>
                                                  <td><strong>
                                                    <?= $REQUEST['eid']; ?></strong></td>
                                                  <td nowrap>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                  <td nowrap>
                                                    <?= $language['label']['firstname']; ?>:</td>
                                                  <td><strong>
                                                    <?= ucwords(strtolower($REQUEST['fst'])); ?>
                                                  </strong></td>
                                                  <td nowrap>
                                                    <?= $language['label']['lastname']; ?>: </td>
                                                  <td><strong>
                                                    <?= ucwords(strtolower($REQUEST['lst'])); ?>
                                                  </strong></td>
                                                </tr>
                                                <?php if ($_GET['approval'] == 'communication') { ?>												
                                                <tr>
                                                  <td>
                                                    <?= $language['label']['address1']; ?>: </td>
                                                  <td><strong>
                                                    <?= ucwords(strtolower($REQUEST['address1'])); ?>
                                                  </strong></td>
                                                  <td>
                                                    <?= $language['label']['address2']; ?>: </td>
                                                  <td><strong>
                                                    <?= ucwords(strtolower($REQUEST['address2'])); ?>
                                                  </strong></td>
                                                </tr>
                                                <tr>
                                                  <td>
                                                    <?= $language['label']['city']; ?>: </td>
                                                  <td><strong>
                                                    <?= ucwords(strtolower($REQUEST['city'])); ?>
                                                  </strong></td>
                                                  <td>
                                                    <?= $language['label']['state']; ?>: </td>
                                                  <td><strong>
                                                    <?= strtoupper($REQUEST['state']); ?>
                                                  </strong></td>
                                                </tr>
                                                <tr>
                                                  <td>
                                                    <?= $language['label']['zip']; ?>: </td>
                                                  <td><strong>
                                                    <?= $REQUEST['zipcode']; ?>
                                                  </strong></td>
                                                  <td>
                                                    <?= $language['label']['country']; ?>:</td>
                                                  <td><strong>
                                                    <?= strtoupper($REQUEST['country']); ?>
                                                  </strong></td>
                                                </tr>
                                                <tr>
                                                  <td>
                                                    <?= $language['label']['phone1']; ?>: </td>
                                                  <td><strong>
                                                    <?= $REQUEST['phn']; ?>
                                                  </strong></td>
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                </tr>
												<?php } ?>											
                                                <tr>
                                                  <td height="5" colspan="4"><hr width="100%" size="1" noshade color="#CCCC99"></td>
                                                </tr>											
                                                <tr>
                                                  <td><?= $language['label']['plant']; ?>:</td>
                                                  <td><strong>
                                                    <?= $REQUEST['plant_name']; ?>
                                                  </strong></td>
                                                  <td><?= $language['label']['department']; ?>:</td>
                                                  <td><strong><?= "(" . $REQUEST['department'] . ") " . $DEPARTMENT[$REQUEST['department']]; ?></strong></td>
                                                </tr>
                                                <tr>
                                                  <td>Desk Location: </td>
                                                  <td><?php if ($_GET['approval'] == 'desk') { ?>
                                                    <input name="desk" type="text" id="desk" maxlength="50">
                                                    <?php } else { ?>
                                                    <strong>
                                                    <?= strtoupper($REQUEST['desk']); ?>
                                                    </strong>
                                                    <?php } ?></td>
                                                  <td>Desk Phone: </td>
                                                  <td><strong>
                                                    <?=(strtolower($REQUEST['tech_phone']) == 'yes') ? ucwords(strtolower("cisco ".$positionTitle['phone_model'])) : NONE; ?>
                                                  </strong></td>
                                                </tr>
                                              </table>
                                            </div>
                                        </div></td>
                                      </tr>
                                  </table></td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                      <tr>
                                        <td height="25" class="BGAccentDark">&nbsp;&nbsp;<strong><img src="../images/computer.gif" width="16" height="16" border="0" align="texttop">&nbsp;<?= $language['label']['stage3']; ?>...</strong></td>
                                      </tr>
                                      <tr>
                                        <td><table width="100%" border="0">
                                            <tr>
                                              <td width="275">Transfer Technology from Employee: </td>
                                              <td colspan="2"><strong>
                                                <?= ucwords($REQUEST['tech_transfer']); ?>
                                              </strong></td>
                                              <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                              <td>Computer:</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_computer']); ?>
                                              </strong></td>
                                              <td width="295">Desktop Phone:</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_phone']); ?>
                                              </strong></td>
                                            </tr>
                                            <tr>
                                              <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td width="278">Cellular Phone:</td>
                                                    <td><strong>
                                                      <?= ucwords($REQUEST['tech_cellular']); ?>
                                                    </strong></td>
                                                  </tr>
                                                  <tr>
                                                    <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">International Cellular Phone: </td>
                                                    <td><strong>
                                                      <?= ucwords($REQUEST['tech_cellularInt']); ?>
                                                    </strong></td>
                                                  </tr>
                                                  <tr>
                                                    <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Transfer Phone Number: </td>
                                                    <td><?= $REQUEST['tech_cellularTrans']; ?></td>
                                                  </tr>
                                              </table></td>
                                              <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td width="300">Blackberry Device:</td>
                                                    <td><strong>
                                                      <?= ucwords($REQUEST['tech_blackberry']); ?>
                                                    </strong></td>
                                                  </tr>
                                                  <tr>
                                                    <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">International Blackberry Phone:</td>
                                                    <td><strong>
                                                      <?= ucwords($REQUEST['tech_blackberryInt']); ?>
                                                    </strong></td>
                                                  </tr>
                                                  <tr>
                                                    <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Transfer Phone Number:</td>
                                                    <td><strong>
                                                      <?= ucwords($REQUEST['tech_blackberryTrans']); ?>
                                                    </strong></td>
                                                  </tr>
                                              </table></td>
                                            </tr>
                                            <tr>
                                              <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                            </tr>
                                            <tr>
                                              <td>Requires Location Access Badge:</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_badge']); ?>
                                              </strong></td>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                              <td>Requires a Lotus Notes account:</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_notesID']); ?>
                                              </strong></td>
                                              <td>Required to use the Job Tracking System:</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_jobTracking']); ?>
                                              </strong></td>
                                            </tr>
                                            <tr>
                                              <td>Requires AS/400 access:</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_as400']); ?>
                                              </strong></td>
                                              <td>Requires access from home (VPN):</td>
                                              <td><strong>
                                                <?= ucwords($REQUEST['tech_vpn']); ?>
                                              </strong></td>
                                            </tr>
                                            <tr>
                                              <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                            </tr>
                                            <tr>
                                              <td valign="top">Default Software Installed:<br>
                                                  <img src="../images/next_button.gif" width="19" height="19" align="absmiddle"><span class="TipLabel">Microsoft Office Standard</span> <br>
                                                  <img src="../images/next_button.gif" width="19" height="19" align="absmiddle"><span class="TipLabel">Lotus Notes</span></td>
                                              <td valign="top">&nbsp;</td>
                                              <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td>Required Optional Software: </td>
                                                  </tr>
                                                  <tr>
                                                    <td><?php
														$software = explode(":", $REQUEST['tech_optionalSoftware']);	// Seperate optional software
														while (list($arg, $val) = each($software)) {
														?>
														<img src="../images/next_button.gif" width="19" height="19" align="absmiddle"><span class="TipLabel"><?= caps($SOFTWARE[$val]); ?></span><br>
														<?php } ?>
													</td>
                                                  </tr>
                                                  <?php
												  $software_sth = $dbh->execute($software_query);
												  while($software_sth->fetchInto($DATA)) {	
														$software = explode(":", $REQUEST['optionalSoftware']);				// Seperate optional software
														while (list($arg, $val) = each($software)) {
															if ( $val == $DATA['id'] ) {
																echo "<tr><td><img src=\"../images/next_button.gif\" width=\"19\" height=\"19\" align=\"absmiddle\"><strong>".$DATA['name']."</strong></td></tr>";
															}
														}
													}
												  ?>
                                              </table></td>
                                              <td valign="top">&nbsp;</td>
                                            </tr>
                                          </table>
                                            </div></td>
                                      </tr>
                                  </table></td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td height="5" valign="bottom"><img src="../images/spacer.gif" width="5" height="5"></td>
                          </tr>
                          <tr>
                            <td height="5" valign="bottom">
							<?php if ($_GET['approval'] == 'desk') { ?>
							<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td width="50%" valign="middle"><!--
									<?php if ($_SESSION['eid'] == $REQUEST['req'] AND ($REQUEST['status'] == 'X' OR $REQUEST['status'] == 'C')) { ?>
                                       <table  border="0" cellspacing="0" cellpadding="0">
                                         <tr>
                                           <td width="20" valign="middle"><input name="restore" type="checkbox" id="restore" value="yes"></td>
                                           <td><input name="imageField" type="image" src="../images/button.php?i=w130.png&l=Restore Request" alt="Restore Request" border="0"></td>
                                         </tr>
                                       </table>
									<?php } ?>
									--></td>
                                <td width="50%" align="right"><?php if (($_SESSION['eid'] == $AUTH[$_GET['approval']] OR
									 										  $_SESSION['eid'] == $REQUEST['req'] OR
																			  $_SESSION['eid'] == $AUTH['staffing'])) { ?>
                                  <input name="request_id" type="hidden" id="request_id" value="<?= $_GET['id']; ?>">
                                    <input name="auth" type="hidden" id="auth" value="desk">
                                    <input name="imageField" type="image" src="../images/button.php?i=b150.png&l=Update Request" alt="Update Request" border="0">
                                    <?php } ?>
                                &nbsp;</td>
                              </tr>
                            </table>
							<?php }?>
							</td>
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

