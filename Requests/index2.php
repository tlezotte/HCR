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


/* ----- CLEAR ERRORS ----- */
unset($_SESSION['error']);
unset($_SESSION['redirect']);

/* ------------- START SESSION VARIABLES --------------------- */
if ($_POST['stage'] == "one") {
	/* Set form variables as session variables */
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]  = htmlentities($value, ENT_QUOTES);
	}

	/* Forward user to next page */
/*	$candidateInt1=getEmployee($_POST['candidateInt1']);
	$_SESSION['candidateInt1']=$candidateInt1['eid'];
	$candidateInt2=getEmployee($_POST['candidateInt2']);
	$_SESSION['candidateInt2']=$candidateInt2['eid'];
	$candidateInt3=getEmployee($_POST['candidateInt3']);
	$_SESSION['candidateInt3']=$candidateInt3['eid'];	
	$interview=getEmployee($_POST['interview']);
	$_SESSION['interview']=$interview['eid'];
	$interviewTeam1=getEmployee($_POST['interviewTeam1']);
	$_SESSION['interviewTeam1']=$interviewTeam1['eid'];
	$interviewTeam2=getEmployee($_POST['interviewTeam2']);
	$_SESSION['interviewTeam2']=$interviewTeam2['eid'];
	$interviewTeam3=getEmployee($_POST['interviewTeam3']);
	$_SESSION['interviewTeam3']=$interviewTeam3['eid'];*/
		
	
	/* Forward user to next page */
	header("Location: description.php"); 
}

/* Unset all Session variables, then resets username and access */
if ($_GET['stage'] == "new") {
	clearSession();
	
	/* Forward user to next page */
	header("Location: " . $_SERVER['PHP_SELF']); 	
}
/* ------------- END SESSION VARIABLES --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */				 
/* Get employees that are in the HR group */
$hr_sql = "SELECT e.eid, CONCAT( e.lst, ', ', e.fst ) AS fullname
		   FROM Users u
			INNER JOIN Standards.Employees e ON u.eid = e.eid
		   WHERE groups = 'hr'
		   ORDER BY e.lst";
$hr_query = $dbh->prepare($hr_sql);		    
/* Getting plant locations from Standards.Department */	
$dept_sql  = $dbh->prepare("SELECT * 
							FROM Standards.Department 
							WHERE status='0' 
							ORDER BY name ASC");
/* Getting plant locations from Standards.Plants */								
$plant_sql = $dbh->prepare("SELECT id, name 
							FROM Standards.Plants 
							WHERE status = '0'
							ORDER BY name ASC");
/* Getting Salary Grades */							 						 
$salaryGrade_sql = $dbh->prepare("SELECT DISTINCT(grade), CONCAT(grade,' ($',min,' : $',mid,' : $',max,')') AS name
								  FROM Position
								  GROUP BY grade
								  ORDER BY (grade+0) ASC");
/* Getting position information */								
$positionTitle_sql = $dbh->prepare("SELECT title_id, title_name 
								    FROM Position 
								    WHERE title_status='0'
								    ORDER BY title_name ASC");									  	
/* Get Contract Agency names */								  					
$agency_sql = $dbh->getAssoc("SELECT id, name 
							  FROM Agency
							  WHERE status='0'
							  ORDER BY name");							
/* ------------- END DATABASE CONNECTIONS --------------------- */


$ONLOAD_OPTIONS.="prepareForm(); init();";
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
    <link rel="stylesheet" type="text/css" href="/Common/Javascript/jquery/autocomplete/jquery.autocomplete.css" />
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
                        <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
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
                              <td width="15%" class="wizardCurrent">Information</td>
                              <td width="25%" class="wizardFuture"><div align="center">Description</div></td>
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
					<table><tbody id="waitingRoom" style="display:none"></tbody></table>
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
                      <table border="0" align="center" cellpadding="0" cellspacing="0">
					    <tr>
						  <td align="right"><a href="<?= $_SERVER['PHP_SELF']."?stage=new"; ?>" title="User Action|Remove current Request from memory and start a new Request"><img src="../images/button.php?i=b110.png&l=Clear Request" border="0" id="noPrint"></a>&nbsp;&nbsp;</td>
						</tr>
                        <tr>
                          <td class="BGAccentVeryDark"><div align="left">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Open Position...</td>
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
                                    </span><img src="../images/info.png" width="16" height="16" align="texttop"><span class="DarkHeaderSubSub">
                                    <?= $language['label']['stage1.1']; ?>...
                                    </span></strong></td>
                                </tr>
                                <tr>
                                  <td>
                                  <div class="panelContent">
                                    <table width="100%"  border="0">
                                      <tr>
                                        <td width="225"><vllabel form="Form" validators="positionTitle" class="valRequired2" errclass="valError">
  <?= $language['label']['positionTitle']; ?>:</vllabel></td>
                                        <td><select name="positionTitle" id="positionTitle">
                                          <option value="0">Select One</option>
                                          <?php
											  $positionTitle_sth = $dbh->execute($positionTitle_sql);
											  while($positionTitle_sth->fetchInto($POSITION)) {
												$selected = ($_SESSION['positionTitle'] == $POSITION['title_id']) ? selected : $blank;
												print "<option value=\"".$POSITION['title_id']."\" ".$selected.">".ucwords(strtolower($POSITION['title_name']))."</option>\n";
											  }
											?>
                                        </select>
                                          <?php if ($_SESSION['hcr_access'] > '1') { ?>
                                          <a href="../Administration/db/positionTitle.php?clean=true" title="Administration Action|Edit Position Titles" onClick="return GB_showFullScreen(this.title, this.href)"><img src="/Common/images/menuedit.gif" width="16" height="16" border="0" align="absmiddle"></a>
                                          <?php } ?>
<vlvalidator name="positionTitle" type="compare" control="positionTitle" errmsg="Enter a Position Title." validtype="string" comparevalue="0" comparecontrol="positionTitle" operator="ne"></td>
                                      </tr>
                                      
                                      <tr>
                                        <td><vllabel form="Form" validators="plant" class="valRequired2" errclass="valError">
                                          <?= $language['label']['plant']; ?>:</vllabel></td>
                                        <td><select name="plant" id="plant">
                                            <option value="0">Select One</option>
                                            <?php
											  $plant_sth = $dbh->execute($plant_sql);
											  while($plant_sth->fetchInto($PLANT)) {
												$selected = ($_SESSION['plant'] == $PLANT[id]) ? selected : $blank;
												print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
											  }
											?>
                                        </select>
                                        <vlvalidator name="plant" type="compare" control="plant" errmsg="Select the location." validtype="string" comparevalue="0" comparecontrol="plant" operator="ne"></td>
                                      </tr>
                                      <tr>
                                        <td><vllabel form="Form" validators="department" class="valRequired2" errclass="valError">
                                          <?= $language['label']['department']; ?>:</vllabel></td>
                                        <td><select name="department" id="department">
                                          <option value="0">Select One</option>
                                          <?php
											  $dept_sth = $dbh->execute($dept_sql);
											  while($dept_sth->fetchInto($DEPT)) {
												$selected = ($_SESSION['department'] == $DEPT[id]) ? selected : $blank;
												print "<option value=\"".$DEPT[id]."\" ".$selected.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
											  }
											?>
                                        </select>
                                          <vlvalidator name="department" type="compare" control="department" errmsg="Select the department." validtype="string" comparevalue="0" comparecontrol="department" operator="ne"></td>
                                      </tr>
                                      <tr>
                                        <td><vllabel form="Form" validators="positionStatus" class="valRequired2" errclass="valError">
                                          <?= $language['label']['positionStatus']; ?>:</vllabel></td>
                                        <td><select name="positionStatus" id="positionStatus">
                                            <option value="0" rel="none">Select One</option>
                                            <option value="new" rel="none" <?= ($_SESSION['positionStatus'] == 'new') ? selected : $blank; ?>>New</option>
                                            <option value="replacement" rel="_replacement" <?= ($_SESSION['positionStatus'] == 'replacement') ? selected : $blank; ?>>Replacement</option>
                                          </select>
                                            <vlvalidator name="positionStatus" type="compare" control="positionStatus" validtype="string" comparevalue="0" comparecontrol="positionStatus" operator="ne"></td>
                                      </tr>
                                      <tr rel="_replacement">
                                        <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle"><!--<vllabel form="Form" validators="replacement" class="valRequired2" errclass="valError">-->
                                            <?= $language['label']['replacement']; ?>:<!--</vllabel>-->&nbsp;<a href="javascript:void(0);" title="Help|Start entering the employees LAST name.  The list will keep refining until you find the employee to select."><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                        <td><input id="replacement" name="replacement" type="text" size="40" title="Help|Start typing the employees last name or employee ID" />
                                          <!--<vlvalidator name="replacement" type="compare" control="replacement" validtype="string" comparevalue="replacement" comparecontrol="positionStatus" operator="e">--></td>
                                      </tr>
                                      <tr>
                                        <td><vllabel form="Form" validators="positionType" class="valRequired2" errclass="valError">
                                          <?= $language['label']['positionType']; ?>:</vllabel></td>
                                        <td><select name="positionType" id="positionType">
                                            <option value="0">Select One</option>
                                            <option value="1" <?= ($_SESSION['positionType'] == '1') ? selected : $blank; ?>>Full-Time</option>
                                            <option value="2" <?= ($_SESSION['positionType'] == '2') ? selected : $blank; ?>>Part-Time</option>
                                          </select>
                                            <vlvalidator name="positionType" type="compare" control="positionType" validtype="string" comparevalue="0" comparecontrol="positionType" operator="ne"></td>
                                      </tr>
                                      <tr>
                                        <td><vllabel form="Form" validators="requestType" class="valRequired2" errclass="valError">
                                          <?= $language['label']['requestType']; ?>:</vllabel></td>
                                        <td><select name="requestType" id="requestType">
                                          <option value="0" rel="none">Select One</option>
                                          <option value="1" rel="none" <?= ($_SESSION['requestType'] == '1') ? selected : $blank; ?>>Direct</option>
                                          <option value="2" rel="_contract" <?= ($_SESSION['requestType'] == '2') ? selected : $blank; ?>>Contract</option>
                                          <option value="3" rel="_contract" <?= ($_SESSION['requestType'] == '3') ? selected : $blank; ?>>Contract Direct</option>
                                         </select>
                                         <vlvalidator name="requestType" type="compare" control="requestType" validtype="string" comparevalue="0" comparecontrol="requestType" operator="ne"></td>
                                      </tr>
                                      <tr rel="_contract">
                                        <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">
                                          <vllabel form="Form" validators="contractTime" class="valRequired2" errclass="valError">
                                            <?= $language['label']['contractTime']; ?>:</vllabel></td>
                                        <td><select name="contractTime" id="contractTime">
                                            <option value="0">Select One</option>
                                            <option value="3" <?= ($_SESSION['contractTime'] == '3') ? selected : $blank; ?>>3 Months</option>
                                            <option value="6" <?= ($_SESSION['contractTime'] == '6') ? selected : $blank; ?>>6 Months</option>
                                            <option value="9" <?= ($_SESSION['contractTime'] == '9') ? selected : $blank; ?>>9 Months</option>
                                          </select>
                                          <vlvalidator name="contractTime" type="compare" control="contractTime" validtype="string" comparevalue="2" comparecontrol="requestType" operator="ge"></td>
                                      </tr>
                                      <tr>
                                        <td valign="top"><vllabel form="Form" validators="targetDate" class="valRequired2" errclass="valError">
                                          <?= $language['label']['targetDate']; ?>:</vllabel></td>
                                        <td><input name="targetDate" type="text" id="targetDate" size="10" maxlength="10" value="<?= $_SESSION['targetDate']; ?>" class="popupcalendar">
                                          <a href="javascript:show_calendar('Form.targetDate')">
                                          <vlvalidator name="targetDate" type="required" control="targetDate">
                                          </a></td>
                                      </tr>
                                  </table>
                                  </div>
                                  </td>
                                </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                            </tr>
                            <tr>
                              <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                <tr>
                                  <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub">
                                    </span><img src="../images/money.gif" width="20" height="17" align="texttop"><span class="DarkHeaderSubSub">
                                    <?= $language['label']['stage1.2']; ?>...
                                    </span></strong></td>
                                </tr>
                                <tr>
                                  <td>
                                    <div class="panelContent">
                                    <table width="100%"  border="0">
                                      <tr>
                                        <td width="225"><vllabel form="Form" validators="budgetPosition" class="valRequired2" errclass="valError">
                                          <?= $language['label']['budgetPosition']; ?>:</vllabel></td>
                                        <td><select name="budgetPosition" id="budgetPosition">
                                            <option value="0">Select One</option>
                                            <option value="yes" <?= ($_SESSION['budgetPosition'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                            <option value="no" <?= ($_SESSION['budgetPosition'] == 'no') ? selected : $blank; ?>>No</option>
                                          </select>
                                            <vlvalidator name="budgetPosition" type="compare" control="budgetPosition" validtype="string" comparevalue="0" comparecontrol="budgetPosition" operator="ne"></td>
                                      </tr>
                                      <tr>
                                        <td valign="top"><span class="valRequired2">
                                          <?= $language['label']['justification']; ?>:</span><br></td>
                                        <td><textarea name="justification" cols="45" rows="10" id="justification"><?= stripslashes($_SESSION['justification']); ?></textarea>
                                          <vlvalidator name="justification" type="required" control="justification" errmsg="Enter a Budget Justification."></td>
                                      </tr>
                                      <tr>
                                        <td valign="top">&nbsp;</td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td><vllabel form="Form" validators="utilize" class="valRequired2" errclass="valError">
                                          <?= $language['label']['utilize']; ?>:</vllabel> <a href="javascript:void(0);" title="Help|Can this position be satisfied by utilizing staff from the department to assist?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                        <td><select name="utilize" id="utilize">
                                            <option value="0">Select One</option>
                                            <option value="yes" <?= ($_SESSION['utilize'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                            <option value="no" <?= ($_SESSION['utilize'] == 'no') ? selected : $blank; ?>>No</option>
                                        </select>
                                          <vlvalidator name="utilize" type="compare" control="utilize" validtype="string" comparevalue="0" comparecontrol="utilize" operator="ne">
                                          <span class="mainsection">(Staff from the department to assist)</span></td>
                                      </tr>
                                      <tr>
                                        <td><vllabel validators="headCount" class="valRequired2" errclass="valError">
                                          <?= $language['label']['headCount']; ?>:</vllabel> <a href="javascript:void(0);" title="Help|What is departments&#39;s approved budgeted head count (excluding this position)?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                        <td><input name="headCount" type="text" id="headCount" size="10" value="<?= $_SESSION['headCount']; ?>">
                                          <vlvalidator name="headCount" type="required" control="headCount">
                                          <span class="mainsection">(Excluding this Position)</span></td>
                                      </tr>
                                      <tr>
                                        <td><vllabel form="Form" validators="currentHeadCount" class="valRequired2" errclass="valError">
                                          <?= $language['label']['currentHeadCount']; ?>:</vllabel> <a href="javascript:void(0);" title="Help|What is departments&#39;s current head count (excluding this position)?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                        <td><input name="currentHeadCount" type="text" id="currentHeadCount" size="10" value="<?= $_SESSION['currentHeadCount']; ?>">
                                          <vlvalidator name="currentHeadCount" type="required" control="currentHeadCount">
                                          <span class="mainsection">(Excluding this Position)</span> </td>
                                      </tr>
                                      <tr>
                                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td nowrap><vllabel form="Form" validators="budget" class="valRequired2" errclass="valError">
                                                <?= $language['label']['budget']; ?>:</vllabel> <a href="javascript:void(0);" title="Help|What is the amount in department&#39;s budget for this position?"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                                              <td align="right"><strong>$</strong></td>
                                            </tr>
                                        </table></td>
                                        <td><input name="budget" type="text" id="budget" size="15" value="<?= stripslashes($_SESSION['budget']); ?>">
                                        <vlvalidator name="budget" type="required" control="budget">
                                        <span class="mainsection">(Annualized)</span></td>
                                      </tr>
                                  </table>
                                  </div>
                                  </td>
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
                                  <input name="stage" type="hidden" id="stage" value="one">
                                    <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=<?= $language['label']['next']; ?>" alt="<?= $language['label']['next']; ?>" border="0">
                                  &nbsp; </div></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
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
<script type="text/javascript" src="/Common/Javascript/jquery/bgiframe/jquery.bgiframe.js"></script>
<script type="text/javascript" src="/Common/Javascript/jquery/autocomplete/jquery.autocomplete.min.js"></script>

<script type="text/javascript" src="/Common/Javascript/jquery/ui/ui.datepicker-min.js"></script>

<script type="text/javascript" src="../js/jQnew.js"></script>

<script type="text/javascript" SRC="/Common/Javascript/usableforms.js"></script>      
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

