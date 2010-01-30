<?php
/**
 * Request System
 *
 * index.php allows enduser to select supplier for PO.
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
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');



/* ------------- START SESSION VARIABLES --------------------- */
if ($_POST['stage'] == "three") {
	/* Set form variables as session variables */
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]  = htmlentities($value, ENT_QUOTES);
	}

	/* Format optionalSoftware variable for session variables */
	$_SESSION['tech_optionalSoftware'] = formatSoftware($_POST['optionalSoftware']);
	
	/* Forward user to next page */
	header("Location: authorization.php"); 
}
/* ------------- END SESSION VARIABLES --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */
$employees_sql = "SELECT eid, fst, lst 
				  FROM Standards.Employees 
				  WHERE status = '0'
				  ORDER BY lst";						  
$employees_query = $dbh->prepare($employees_sql);							 

/* Getting Software */
$software_sql = $dbh->prepare("SELECT id, name FROM Standards.Software ORDER BY name");
/* ------------- END DATABASE CONNECTIONS --------------------- */


/* ------------------ START VARIABLES ----------------------- */
$items=3;								//Display items in a row
$items_counter=0;						//Start items counter
/* ------------------ END VARIABLES ----------------------- */


$ONLOAD_OPTIONS="prepareForm(); init();";
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
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/calendarmws.js"></SCRIPT>
    
	<script LANGUAGE="JavaScript" SRC="/Common/Javascript/usableforms.js"></script>
    
	<script LANGUAGE="JavaScript" src="/Common/Javascript/prototype/prototype.js"></script>
	<script LANGUAGE="JavaScript" src="/Common/Javascript/autoassist/autoassist.js"></script>
	<link href="/Common/Javascript/autoassist/autoassist.css" rel="stylesheet" type="text/css">
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
                  <td><br>
				  <div id="noPrint">
                    <table  border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                        <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                        <td><a href="description.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                        <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                      </tr>
                      <tr>
                        <td colspan="9"><table width="100%"  border="0">
                            <tr>
                              <td width="15%" class="wizardPast">Information</td>
                              <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Description</div></td>
                              <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Technology</div></td>
                              <td width="25%" class="wizardFuture"><div align="center">Authorization</div></td>
                              <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                            </tr>
                        </table></td>
                      </tr>
                    </table>
				    </div>
                    <br>
                    <br>
					<table><tbody id="waitingRoom" style="display:none"></tbody></table>
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
                      <table border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                          <td class="BGAccentVeryDark"><div align="left">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;
                                    <strong><img src="../images/computer.gif" width="16" height="16" align="texttop"></strong>
                                  <?= $language['label']['stage3']; ?>...</td>
                                  <td width="50%"><div align="left"> </div></td>
                                </tr>
                              </table>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="BGAccentVeryDarkBorder">
                            <div id="panelContent">
                             <table width="100%" border="0">
                              <tr>
                                <td colspan="2" class="errorMessage">IT requires 3 business days to process the request, Computers may take longer. </td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"><img src="../images/spacer.gif" width="10" height="10"></td>
                              </tr>
							  <?php
							  if (isset($_SESSION['replacement'])) {
							  		$transfer=(isset($_SESSION['tech_transfer'])) ? $_SESSION['tech_transfer'] : $_SESSION['replacement'];
							  ?>
                              <tr>
                                <td><vllabel form="Form" validators="tech_transfer" class="valRequired2" errclass="valError">Transfer Hardware and Software:</vllabel></td>
                                <td><select name="tech_transfer" id="tech_transfer">
                                  <option value="0">Select One</option>
                                  <option value="no" <?= ($_SESSION['tech_transfer'] == 'no') ? selected : $blank; ?>>No</option>
                                  <option value="yes" <?= ($_SESSION['tech_transfer'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  </select>
                                <vlvalidator name="tech_transfer" type="compare" control="tech_transfer" validtype="string" comparevalue="0" comparecontrol="tech_transfer" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><vllabel form="Form" validators="transfer_eid" class="valRequired2" errclass="valError">Transfer Hardware and Software From:<a href="javascript:void(0);" <?php help('', 'Start entering the employees LAST name.  The list will keep refining until you find the employee to select.', 'default'); ?>>&nbsp;<img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></vllabel></td>
                                <td>
								<input id="transfer_eid" name="transfer_eid" type="text" size="50" value="<?= $transfer; ?>" />
								<vlvalidator name="transfer2" type="compare" control="tech_transfer" validtype="string" comparevalue="0" comparecontrol="transfer" operator="ne">
								<vlvalidator name="transfer_eid" type="required" control="transfer_eid">
										<script type="text/javascript">
										Event.observe(window, "load", function() {
											var aa = new AutoAssist("transfer", function() {
												return "../Common/employees.php?v=all&q=" + this.txtBox.value;
											});
										});
										</script></td>
                              </tr>
							  <tr>
                                <td height="20" colspan="2"><hr width="90%" size="1" color="#999966"></td>
                              </tr>	
							  <?php } ?>						  
                              <tr>
                                <td><vllabel form="Form" validators="tech_computer" class="valRequired2" errclass="valError">Computer:</vllabel></td>
                                <td>
                                  <select name="tech_computer" id="tech_computer">
                                    <option value="0">Select One</option>
                                    <option value="no" <?= ($_SESSION['tech_computer'] == 'no') ? selected : $blank; ?>>No</option>
                                    <option value="desktop" <?= ($_SESSION['tech_computer'] == 'desktop') ? selected : $blank; ?>>Desktop</option>
                                    <option value="laptop" <?= ($_SESSION['tech_computer'] == 'laptop') ? selected : $blank; ?>>Laptop</option>
                                  </select>
                                  <vlvalidator name="tech_computer" type="compare" control="tech_computer" validtype="string" comparevalue="0" comparecontrol="tech_computer" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><vllabel form="Form" validators="tech_phone" class="valRequired2" errclass="valError">Desktop Phone:</vllabel></td>
                                <td><select name="tech_phone" id="tech_phone">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_phone'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_phone'] == 'no') ? selected : $blank; ?>>No</option>
                                </select>
                                <vlvalidator name="tech_phone" type="compare" control="tech_phone" validtype="string" comparevalue="0" comparecontrol="tech_phone" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><vllabel form="Form" validators="tech_cellular" class="valRequired2" errclass="valError">Cellular Phone:</vllabel></td>
                                <td><select name="tech_cellular" id="tech_cellular">
                                  <option value="0" rel="none">Select One</option>
                                  <option value="yes" rel="_transCellular" <?= ($_SESSION['tech_cellular'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" rel="none" <?= ($_SESSION['tech_cellular'] == 'no') ? selected : $blank; ?>>No</option>
                                </select>
                                <vlvalidator name="tech_cellular" type="compare" control="tech_cellular" validtype="string" comparevalue="0" comparecontrol="tech_cellular" operator="ne"></td>
                              </tr>
                              <tr rel="_transCellular">
                                <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">International Cellular Phone:</td>
                                <td><select name="tech_cellularInt" id="tech_cellularInt">
                                  <option value="no" <?= ($_SESSION['tech_cellularInt'] == 'no') ? selected : $blank; ?>>No</option>
                                  <option value="yes" <?= ($_SESSION['tech_cellularInt'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                </select></td>
                              </tr>
                              <tr rel="_transCellular">
                                <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Transfer Personal Phone Number:</td>
                                <td><input name="tech_cellularTrans" type="text" id="tech_cellularTrans" size="12" maxlength="12" value="<?= $_SESSION['tech_cellularTrans']; ?>"></td>
                              </tr>
                              <tr>
                                <td><vllabel form="Form" validators="tech_blackberry" class="valRequired2" errclass="valError">Blackberry Device:</vllabel></td>
                                <td><select name="tech_blackberry" id="tech_blackberry">
                                  <option value="0" rel="none">Select One</option>
                                  <option value="no" rel="none" <?= ($_SESSION['tech_blackberry'] == 'no') ? selected : $blank; ?>>No</option>
                                  <option value="data" rel="none" <?= ($_SESSION['tech_blackberry'] == 'data') ? selected : $blank; ?>>Yes - Email Only</option>
                                  <option value="yes" rel="_transBlackberry" <?= ($_SESSION['tech_blackberry'] == 'yes') ? selected : $blank; ?>>Yes - Phone and Email</option>
                                </select>
                                <vlvalidator name="tech_blackberry" type="compare" control="tech_blackberry" validtype="string" comparevalue="0" comparecontrol="tech_blackberry" operator="ne"></td>
                              </tr>
                              <tr rel="_transBlackberry">
                                <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">International Blackberry Phone:</td>
                                <td><select name="tech_blackberryInt" id="tech_blackberryInt">
                                  <option value="no" <?= ($_SESSION['tech_blackberryInt'] == 'no') ? selected : $blank; ?>>No</option>
                                  <option value="yes" <?= ($_SESSION['tech_blackberryInt'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                </select></td>
                              </tr>
                              <tr rel="_transBlackberry">
                                <td class="valNone"><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Transfer Personal Phone Number:</td>
                                <td><input name="tech_blackberryTrans" type="text" id="tech_blackberryTrans" size="12" maxlength="12" value="<?= $_SESSION['tech_blackberryTrans']; ?>"></td>
                              </tr>
                              <tr>
                                <td height="5" colspan="2"><img src="../images/spacer.gif" width="10" height="5"></td>
                              </tr>								  
                              <tr>
                                <td><vllabel form="Form" validators="tech_notesID" class="valRequired2" errclass="valError">Requires a Lotus Notes account:</vllabel></td>
                                <td><select name="tech_notesID" id="tech_notesID">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_notesID'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_notesID'] == 'no') ? selected : $blank; ?>>No</option>
                                </select>
                                <vlvalidator name="tech_notesID" type="compare" control="tech_notesID" validtype="string" comparevalue="0" comparecontrol="tech_notesID" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><vllabel form="Form" validators="tech_as400" class="valRequired2" errclass="valError">Requires AS/400 access:</vllabel></td>
                                <td><select name="tech_as400" id="tech_as400">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_as400'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_as400'] == 'no') ? selected : $blank; ?>>No</option>
                                </select>
                                <vlvalidator name="tech_as400" type="compare" control="tech_as400" validtype="string" comparevalue="0" comparecontrol="tech_as400" operator="ne"></td>
                              </tr>
                              <tr>
                                <td><vllabel form="Form" validators="tech_jobTracking" class="valRequired2" errclass="valError">Required to use the Job Tracking System:</vllabel></td>
                                <td><select name="tech_jobTracking" id="tech_jobTracking">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_jobTracking'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_jobTracking'] == 'no') ? selected : $blank; ?>>No</option>
                                </select>
                                <vlvalidator name="tech_jobTracking" type="compare" control="tech_jobTracking" validtype="string" comparevalue="0" comparecontrol="tech_jobTracking" operator="ne"></td>
                              </tr>
							  <tr>
                                <td><vllabel form="Form" validators="tech_request" class="valRequired2" errclass="valError">Requires access to Purchase Request System:</vllabel> </td>
                                <td><select name="tech_request" id="tech_request">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_request'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_request'] == 'no') ? selected : $blank; ?>>No</option>
                                </select>
                                  <vlvalidator name="tech_request" type="compare" control="tech_request" validtype="string" comparevalue="0" comparecontrol="tech_request" operator="ne"></td>
                              </tr>	
                              <tr>
                                <td height="5" colspan="2"><img src="../images/spacer.gif" width="10" height="5"></td>
                              </tr>								  	
							  <tr>
                                <td><vllabel form="Form" validators="tech_badge" class="valRequired2" errclass="valError">Requires Location Access Badge:</vllabel></td>
                                <td><select name="tech_badge" id="tech_badge">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_badge'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_badge'] == 'no') ? selected : $blank; ?>>No</option>
                                  </select>
                                  <vlvalidator name="tech_badge" type="compare" control="tech_badge" validtype="string" comparevalue="0" comparecontrol="tech_badge" operator="ne"></td>
                              </tr>								  					  
                              <tr>
                                <td><vllabel form="Form" validators="tech_vpn" class="valRequired2" errclass="valError">Requires access from home (VPN):</vllabel></td>
                                <td><select name="tech_vpn" id="tech_vpn">
                                  <option value="0">Select One</option>
                                  <option value="yes" <?= ($_SESSION['tech_vpn'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                  <option value="no" <?= ($_SESSION['tech_vpn'] == 'no') ? selected : $blank; ?>>No</option>
                                 </select>
                                 <vlvalidator name="tech_vpn" type="compare" control="tech_vpn" validtype="string" comparevalue="0" comparecontrol="tech_vpn" operator="ne"></td>
                              </tr>
                              <tr>
                                <td height="5" colspan="2"><img src="../images/spacer.gif" width="10" height="5"></td>
                              </tr>								  
                              <tr>
                                <td valign="top"><vllabel form="Form" validators="tech_software" class="valRequired2" errclass="valError">Default Software Installed:</vllabel>
                                  <blockquote class="TipText"> <a href="javascript:void();" class="dark" <?php help('', 'Default software installed', 'default'); ?>><img src="../images/next_button.gif" width="19" height="19" border="0" align="absmiddle">Microsoft Office Standard <br>
                                <img src="../images/next_button.gif" width="19" height="19" border="0" align="absmiddle">Lotus Notes</a></blockquote></td>
                                <td valign="top"><select name="tech_software" id="tech_software">
                                  <option value="0" rel="none">Select One</option>
                                  <option value="no" rel="none" <?= ($_SESSION['software'] == 'no') ? selected : $blank; ?>>Default Software Only</option>
                                  <option value="yes" rel="_optionalSoftware">Requires Optional Software</option>
                                </select>
                                <vlvalidator name="tech_software" type="compare" control="tech_software" validtype="string" comparevalue="0" comparecontrol="tech_software" operator="ne"></td>
                              </tr>
                              <tr rel="_optionalSoftware">
                                <td colspan="2" valign="top"><fieldset class="BGAccentDarkBorder">
                                  <legend class="BGAccentDarkLegend">Optional Software</legend>
                                    <table width="100%"  border="0">
                                      
                                      <?php
									  $software_sth = $dbh->execute($software_sql);
									  while($software_sth->fetchInto($SOFTWARE)) {									  
										$items_counter++;
										if ($items_counter == 1) { print "<tr>"; }
											$selected = ($_SESSION['optionalSoftware'] == $SOFTWARE[eid]) ? checked : $blank;
									  ?>
									  <td nowrap><input type="checkbox" name="optionalSoftware[]" id="<?= $SOFTWARE['id']; ?>" value="<?= $SOFTWARE['id']; ?>" ".$selected.">
											  <label for="<?= $SOFTWARE['id']; ?>">
												<?= ucwords(strtolower($SOFTWARE['name'])); ?>
											  </label></td>
									  <?php	
										if ($items_counter == $items) {
										  print "</tr>\n";
										  $items_counter=0;
										}
									  }
									?>
                                    </table>
                                </fieldset></td>
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
                                <td><a href="description.php">&nbsp;<img src="../images/button.php?i=b70.png&l=<?= $language['label']['back']; ?>" border="0"></a></td>
                                <td><div align="right">
                                    <input name="stage" type="hidden" id="stage" value="three">
                                    <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=<?= $language['label']['next']; ?>" alt="<?= $language['label']['next']; ?>" border="0">
                                  &nbsp; </div></td>
                              </tr>
                          </table></td>
                        </tr>
                      </table>
                    </form>
					 <table><tbody id="waitingRoom"></tbody></table>
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