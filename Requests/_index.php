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



/* ----- CLEAR ERRORS ----- */
unset($_SESSION['error']);
unset($_SESSION['redirect']);

/* Unset all Session variables, then resets username and access */
if ($_GET['stage'] == "new") {
	clearSession();
	
	/* Forward user to next page */
	$forward=$_SERVER['PHP_SELF'] . "?type=" . $_SESSION['type']; 
	header("Location: " . $forward);	
}

/* ------------------ START PROCESSING DATA ----------------------- */
if ($_POST['stage'] == 'one') {
	/* Set form variables as session variables */
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]  = htmlentities($value, ENT_QUOTES, 'UTF-8');
	}
	
	/* Forward user to next page */
	if (array_key_exists('Next_x', $_POST)) {
		$forward="_authorization.php";
		header("Location: " . $forward);
	}
}
/* ------------------ END PROCESSING DATA ----------------------- */


/* ------------- START LOAD EMPLOYEE DATA --------------------- */
if (strlen($_POST['loaddata']) == 5) {
	/* Get the data from the last Request */
	$data_sql="SELECT r.id AS hcr, r.positionTitle, r.plant, r.department, c.agency, c.salaryGrade, c.salary, c.overTime, c.doubleTime, re.eid AS employee, CONCAT(re.fst,' ',re.lst, ' (', re.eid, ')') AS ajaxName, c.vacationDays, c.vehicleAllowance
			   FROM Requests r
				 left JOIN Compensation c ON c.request_id = r.id
				 left JOIN Employees re ON re.request_id = r.id
				WHERE r.id = (SELECT request_id
							  FROM Employees
							  WHERE eid='$_POST[loaddata]'
							  ORDER BY request_id DESC
							  LIMIT 1)
				AND c.status = 'A'
				ORDER BY r.id DESC, re.id DESC
				LIMIT 1";
	$DATA=$dbh->getRow($data_sql);
	
	/* Set form variables as session variables */
	foreach ($DATA as $key => $value) {
		switch ($key) {
			case 'salary': 
			case 'overTime':
			case 'doubleTime': $_SESSION[$key] = base64_decode(trim($value)); break;
			default: $_SESSION[$key]  = trim($value); break;
		}
	}	
}
/* ------------- END LOAD EMPLOYEE DATA --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */
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
/* ------------- Getting Salary Grades ------------- */
$GRADE = $dbh->getRow("SELECT grade, CONCAT(grade,' ($',min,' - $',mid,' - $',max,')') AS title_name, min, mid, max, ot, flsa
					   FROM Position 
					   WHERE title_id = " . $_SESSION['positionTitle']);
/* ---- Setup overtime fields ---- */
switch ($GRADE['ot']) {
	case 'E': $overtime = "Exempt"; $doubletime = "Exempt"; break;
	case 'ST': $overtime = "Straight Time"; $doubletime = "Straight Time"; break;
	case 'TH': $overtime = "Time and a half"; $doubletime = "Time and a half"; break;
}
$GRADE_NEW = $dbh->getRow("SELECT grade, CONCAT(grade,' ($',min,' - $',mid,' - $',max,')') AS title_name, min, mid, max, ot, flsa
					   	   FROM Position 
					   	   WHERE title_id = " . $_SESSION['positionTitle_new']);
/* ---- Setup overtime fields ---- */
switch ($GRADE_NEW['ot']) {
	case 'E': $overtime_new = "Exempt"; $doubletime_new = "Exempt"; break;
	case 'ST': $overtime_new = "Straight Time"; $doubletime_new = "Straight Time"; break;
	case 'TH': $overtime_new = "Time and a half"; $doubletime_new = "Time and a half"; break;
} 				   						  
/* Get Contract Agency names */								  					
$agency_sql = $dbh->prepare("SELECT id, name 
							 FROM Standards.ContractAgency
							 WHERE status='0'
							 ORDER BY name");
/* Getting position information */								
$positionTitle_sql = $dbh->prepare("SELECT * 
							        FROM Position 
							        WHERE title_status='0'
							        ORDER BY (grade + 0) ASC, title_name ASC");						 
/* Get Load Data Employees */								 
$load_sql="SELECT DISTINCT (e.eid) AS eid, CONCAT( e.lst, ', ', e.fst ) AS name
		   FROM Employees r 
		     INNER JOIN Standards.Employees e ON e.eid=r.eid
		   ORDER BY e.lst";	
$load_query=$dbh->prepare($load_sql);						 
/* ------------- END DATABASE CONNECTIONS --------------------- */

/* ---- Setup link to HCR from loaded data ---- */
$past_hcr = "<a href='detail.php?id=" . $_SESSION['hcr'] . "' title='HCR#" . $_SESSION['hcr'] . "' class='black' rel='gb_page_fs[]'>Data From HCR#" . $_SESSION['hcr'] . "<img src='/Common/images/popupicon.gif' border='0' /></a>";

/* ---- Set Request Type ---- */
$requestType = (array_key_exists('type', $_GET)) ? caps($_GET['type']) : caps($_SESSION['request_type']);

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
  <script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  
  <script type="text/javascript" src="/Common/Javascript/scriptaculous/prototype-min.js"></script>
  <script type="text/javascript" src="/Common/Javascript/scriptaculous/scriptaculous.js?load=effects"></script>
  <script type="text/javascript" src="/Common/Javascript/ps/treasure.js"></script>	
    
  <script type="text/javascript" src="/Common/Javascript/autoassist/autoassist.js"></script>
  <link href="/Common/Javascript/autoassist/autoassist.css" rel="stylesheet" type="text/css" />	 
     
  <link href="/Common/Javascript/Spry/widgets/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />  
  <link href="/Common/Javascript/Spry/widgets/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" /> 
  <link href="/Common/Javascript/Spry/widgets/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" /> 
  
  <script type="text/javascript">
	function selectItem() {
	 var obj = document.getElementById('candidateInt[]');
	 for(var no=0;no<obj.options.length;no++){
	  obj.options[no].selected = true;
	 }
	}
	function cent(amount) {
	// returns the amount in the .99 format 
		amount -= 0;
		amount = (Math.round(amount*100))/100;
		return (amount == Math.floor(amount)) ? amount + '.00' : (  (amount*10 == Math.floor(amount*10)) ? amount + '0' : amount);
	}
	function Calculate() {
	  var form = $('Form');
	  var salary = form.getInputs('salary');
	  var salaryNew = form.getInputs('salary_new');
	  
	  var percentage = (salaryNew - salary) / (salary * 100);
	  $('percentage').update(percentage);
	  $('percentage').innerHTML;
	  
	  //percentage = ((parseFloat(Form.salary_new.value) - parseFloat(Form.salary.value))) / parseFloat(Form.salary.value) * 100;
	  //Form.percentage.value = percentage;	
	  //increase = (parseFloat(Form.salary_new.value) - parseFloat(Form.salary.value));
	  //Form.increase.value = cent(increase);
	}
  </script>  
      
  <style type="text/css">
	.textfieldValidState input, input.textfieldValidState {
		background-color:#FFFFFF;
	}
	.textareaValidState textarea, textarea.textareaValidState {
		background-color:#FFFFFF;
	}
	.selectValidState select, select.selectValidState {
		background-color: #FFFFFF;
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
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><br>
                          <div id="noPrint">
                            <table  border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td><img src="../images/vnCurrent.gif" width="36" height="36" border="0" /></td>
                                <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18" /></td>
                                <td><img src="../images/vnFuture.gif" width="36" height="36" /></td>
                                <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18" /></td>
                                <td><img src="../images/vnFuture.gif" width="36" height="36" /></td>
                              </tr>
                              <tr>
                                <td colspan="7"><table width="100%"  border="0">
                                    <tr>
                                      <td width="25%" class="wizardCurrent">Information</td>
                                      <td width="25%" align="center" class="wizardFuture">Authorization</td>
                                      <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                                    </tr>
                                </table></td>
                              </tr>
                            </table>
                          </div>
                          <br>
                          <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td align="right"></td>
                            </tr>
                            <tr>
                              <td class="BGAccentVeryDark"><div align="left">
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;<?= $requestType; ?></td>
                                      <td width="50%" align="right"><span style="text-transform:capitalize; font-size:110%; font-weight:bold">&nbsp;</span></td>
                                    </tr>
                                  </table>
                              </div></td>
                            </tr>
                            <tr>
                              <td class="BGAccentVeryDarkBorder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                        <tr>
                                          <td height="25" class="BGAccentDark">&nbsp;<img src="../images/info.png" width="16" height="16" align="texttop"><span class="DarkHeaderSubSub"> <?= $language['label']['stage1.1']; ?>...</span></td>
                                        </tr>
                                        <tr>
                                          <td>
                                           <div class="panelContent">
                                            <table width="100%"  border="0">
                                              <tr>
                                                <td  class="requiredicon">Employee:</td>
                                                <td colspan="3"><span id="sprytextfield2">
                                                  <input name="ajaxName" type="text" id="ajaxName" size="40" value="<?= $_SESSION['ajaxName']; ?>" />
                                                  or 
                                                  <select name="loaddata" id="loaddata" onChange="this.form.submit();">
                                                    <option value="0">Load Open Position Data</option>
                                                    <?php
                                                    $load_sth = $dbh->execute($load_query);
                                                    while($load_sth->fetchInto($LOAD)) {
                                                        print "<option value=\"".$LOAD['eid']."\">" . caps($LOAD['name']) . "</option>";
                                                    }
                                                    ?>
                                                  </select>
                                                  <script type="text/javascript">
													Event.observe(window, "load", function() {
														var aa = new AutoAssist("ajaxName", function() {
															return "../data/employees.php?output=ajax&q=" + this.txtBox.value;
														});
													});
											      </script>                                                         
                                                  <span class="textfieldRequiredMsg">Please select an employee.</span></span>
                                                  <span id="sprytextfield3">
                                                  <input name="employee" type="hidden" id="ajaxEID" value="<?= $_SESSION['employee']; ?>" />                                           
                                                  <span class="textfieldRequiredMsg">Please select an employee.</span><span class="textfieldMinCharsMsg">Please select an employee.</span><span class="textfieldMaxCharsMsg">Please select an employee.</span></span></td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="requiredicon"><?= $language['label']['effectiveDate']; ?>:</td>
                                                <td colspan="3"><span id="sprydate1">
                                                  <input name="startDate" type="text" id="date1" size="10" maxlength="10" value="<?= $_SESSION['startDate']; ?>" onFocus="show_calendar('Form.date1')">
                                                  <span class="textfieldRequiredMsg">A Start Date is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></td>
                                              </tr>											  
                                              <tr>
                                                <td >&nbsp;</td>
                                                <td class="currentnew"><?= (array_key_exists('hcr', $_SESSION)) ? $past_hcr : 'Current'; ?></td>
                                                <td>&nbsp;</td>
                                                <td class="currentnew">New</td>
                                              </tr>
                                              <tr>
                                                <td class="requiredicon"><?= $language['label']['positionTitle']; ?>:</td>
                                                <td><span id="spryselect4">
                                                  <select name="positionTitle" id="positionTitle" onChange="this.form.submit();">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $positionTitle_sth = $dbh->execute($positionTitle_sql);
													  while($positionTitle_sth->fetchInto($POSITION)) {
														$selected = ($_SESSION['positionTitle'] == $POSITION['title_id']) ? selected : $blank;
														print "<option value=\"" . $POSITION['title_id'] . "\" ".$selected.">(" . $POSITION['grade'] . ") " . caps($POSITION['title_name']) . "</option>\n";
													  }
													?>
                                                  </select>
                                                </span></td>
                                                <td></td>
                                                <td class="requiredicon"><span id="spryselect1">
                                                  <select name="positionTitle_new" id="positionTitle_new" onChange="this.form.submit();">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $positionTitle_sth = $dbh->execute($positionTitle_sql);
													  while($positionTitle_sth->fetchInto($POSITION)) {
													    $selected = ($_SESSION['positionTitle_new'] == $POSITION['title_id']) ? selected : $blank;
														print "<option value=\"" . $POSITION['title_id'] . "\" ".$selected.">(" . $POSITION['grade'] . ") " . caps($POSITION['title_name']) . "</option>\n";
													  }
													?>
                                                  </select>
                                                </span>
                                                  <?php if ($_SESSION['hcr_access'] > '1') { ?>
                                                  <a href="../Administration/db/positionTitle.php?clean=true" title="Edit Position Titles" onClick="return GB_showFullScreen(this.title, this.href)"><img src="/Common/images/menuedit.gif" width="16" height="16" border="0" align="absmiddle"></a>
                                                  <?php } ?>
                                                  <vlvalidator name="positionTitle" type="compare" control="positionTitle" errmsg="Enter a Position Title." validtype="string" comparevalue="0" comparecontrol="positionTitle" operator="ne"></td>
                                              </tr>
                                              <tr>
                                                <td class="requiredicon"><?= $language['label']['plant']; ?>:</td>
                                                <td><span id="spryselect5">
                                                  <select name="plant" id="plant">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $plant_sth = $dbh->execute($plant_sql);
													  while($plant_sth->fetchInto($PLANT)) {
														$selected = ($_SESSION['plant'] == $PLANT['id']) ? selected : $blank;
														print "<option value=\"" . $PLANT['id'] . "\" ".$selected.">" . caps($PLANT[name]) . "</option>\n";
													  }
													?>
                                                  </select>
                                                </span></td>
                                                <td></td>
                                                <td class="requiredicon"><span id="spryselect2">
                                                  <select name="plant_new" id="plant_new">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $plant_sth = $dbh->execute($plant_sql);
													  while($plant_sth->fetchInto($PLANT)) {
													    $selected = ($_SESSION['plant_new'] == $PLANT['id']) ? selected : $blank;
														print "<option value=\"" . $PLANT['id'] . "\" ".$selected.">" . caps($PLANT[name]) . "</option>\n";
													  }
													?>
                                                  </select>
                                                </span></td>
                                              </tr>
                                              <tr>
                                                <td class="requiredicon"><?= $language['label']['department']; ?>:</td>
                                                <td><span id="spryselect6">
                                                  <select name="department" id="department">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $dept_sth = $dbh->execute($dept_sql);
													  while($dept_sth->fetchInto($DEPT)) {
														$selected = ($_SESSION['department'] == $DEPT['id']) ? selected : $blank;
														print "<option value=\"" . $DEPT['id'] . "\" " . $selected . ">(" . $DEPT['id'] . ") " . caps($DEPT[name]) . "</option>\n";
													  }
													?>
                                                  </select>
                                                </span></td>
                                                <td></td>
                                                <td class="requiredicon"><span id="spryselect3">
                                                  <select name="department_new" id="department_new">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $dept_sth = $dbh->execute($dept_sql);
													  while($dept_sth->fetchInto($DEPT)) {
													    $selected = ($_SESSION['department_new'] == $DEPT['id']) ? selected : $blank;
														print "<option value=\"" . $DEPT['id'] . "\" " . $selected . ">(" . $DEPT['id'] . ") " . caps($DEPT[name]) . "</option>\n";
													  }
													?>
                                                  </select>
                                                </span></td>
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
                                          <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub"> </span><span class="DarkHeaderSubSub"><strong><img src="../images/notes.gif" width="12" height="15" align="texttop"></strong>
                                                  <?= $language['label']['stage2']; ?>...</span></strong></td>
                                        </tr>
                                        <tr>
                                          <td>
                                           <div class="panelContent">
                                            <table width="100%"  border="0">
                                              <tr>
                                                <td valign="top" class="requiredicon"><?= $language['label']['justification']; ?>:</td>
                                                <td><span id="sprytextarea1">
                                                <textarea name="justification" cols="50" rows="5" wrap="VIRTUAL" id="justification"><?= stripslashes($_SESSION['justification']); ?></textarea>
                                                <span class="textareaRequiredMsg">A value is required.</span></span></td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="requiredicon"><?= $language['label']['primaryJob']; ?>:</td>
                                                <td><span id="sprytextarea2">
                                                  <textarea name="primaryJob" cols="50" rows="5" wrap="VIRTUAL" id="primaryJob"><?= stripslashes($_SESSION['primaryJob']); ?></textarea>
                                                <span class="textareaRequiredMsg">A value is required.</span></span></td>
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
                                          <td height="25" class="BGAccentDark"><strong>&nbsp;<span class="DarkHeaderSubSub"> </span><img src="../images/money.gif" width="20" height="17" align="texttop">&nbsp;<span class="DarkHeaderSubSub"><?= $language['label']['stage1.3']; ?>... </span></strong></td>
                                        </tr>
                                        <tr>
                                          <td>
                                            <div class="panelContent">
                                             <table width="100%"  border="0">
                                              <?php if ($_GET['type'] == 'conversion') { ?>
                                              <tr>
                                                <td><span >Contract Agency:</span></td>
                                                <td><select name="agency" id="agency">
                                                    <option value="0">Select One</option>
                                                    <?php
													  $agency_sth = $dbh->execute($agency_sql);
													  while($agency_sth->fetchInto($AGENCY)) {
														$selected = ($_SESSION['agency'] == $AGENCY['id']) ? selected : $blank;
														print "<option value=\"" . $AGENCY['id'] . "\" " . $selected . ">" . $AGENCY['name'] . "</option>\n";
													  }
													?>
                                                </select></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                              </tr>
                                              <tr>
                                                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                      <td >Current Bill Rate: </td>
                                                      <td align="right">$</td>
                                                    </tr>
                                                </table></td>
                                                <td><span id="sprytextfield10">
                                                <input name="billRate" type="text" id="billRate" value="<?= $_SESSION['billRate']; ?>" size="10" maxlength="10">
                                                <span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                              </tr>
                                              <?php } ?>
                                              <tr>
                                                <td>&nbsp;</td>
                                                <td class="currentnew"><?= (array_key_exists('hcr', $_SESSION)) ? $past_hcr : 'Current'; ?></td>
                                                <td>&nbsp;</td>
                                                <td class="currentnew">New</td>
                                              </tr>
                                              <tr>
                                                <td class="requiredicon" ><?= $language['label']['salaryGrade']; ?>:</td>
                                                <td><?= $GRADE['title_name']; ?>
                                                <input name="salaryGrade" type="hidden" id="salaryGrade" value="<?= $GRADE['grade']; ?>" /></td>
                                                <td align="right"></td>
                                                <td><?= $GRADE_NEW['title_name']; ?>
                                                <input name="salaryGrade_new" type="hidden" id="salaryGrade_new" value="<?= $GRADE_NEW['grade']; ?>" /></td>
                                              </tr>
                                              <tr>
                                                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                      <td class="requiredicon" ><?= $language['label']['salary']; ?>:</td>
                                                      <td align="right">$</td>
                                                    </tr>
                                                </table></td>
                                                <td ><span id="sprytextfield5">
                                                <input name="salary" type="text" id="salary" size="15" maxlength="15" autocomplete="off" value="<?= trim($_SESSION['salary']); ?>" onBlur="Calculate();">
                                                <span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldMinValueMsg">The value is less than minimum wage.</span></span></td>
                                                <td align="right" class="requiredicon" >$</td>
                                                <td ><span id="sprytextfield4">
                                                <input name="salary_new" type="text" id="salary_new" size="15" maxlength="15" autocomplete="off" value="<?= $_SESSION['salary_new']; ?>" onBlur="Calculate();">
                                                <span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldMinValueMsg">Salary less than the grade minimum.</span><span class="textfieldMaxValueMsg">Salary greater than the grade maximum.</span></span></td>
                                              </tr>
                                              <tr>
                                                <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Over Time:</td>
                                                <td ><?= $overtime; ?><input name="overTime" type="hidden" value="<?= $GRADE['ot']; ?>"></td>
                                                <td align="right" >&nbsp;</td>
                                                <td><?= $overtime_new; ?><input name="overTime_new" type="hidden" value="<?= $GRADE_NEW['ot']; ?>"></td>
                                              </tr>
                                              <tr>
                                                <td><img src="../images/1rightarrow.gif" width="16" height="16" align="absmiddle">Double Time:</td>
                                                <td ><?= $doubletime; ?><input name="doubleTime" type="hidden" value="<?= $GRADE['ot']; ?>"></td>
                                                <td align="right" >&nbsp;</td>
                                                <td><?= $doubletime_new; ?><input name="doubleTime_new" type="hidden" value="<?= $GRADE_NEW['ot']; ?>"></td>
                                              </tr>
                                              <tr>
                                                <td><?= $language['label']['vacationDays']; ?>:</td>
                                                <td ><input name="vacationDays" type="text" id="vacationDays" size="10" maxlength="2" value="<?= $_SESSION['vacationDays']; ?>"></td>
                                                <td align="right" >&nbsp;</td>
                                                <td ><input name="vacationDays_new" type="text" id="vacationDays_new" size="10" maxlength="2" value="<?= $_SESSION['vacationDays_new']; ?>"></td>
                                              </tr>
                                              <tr>
                                                <td><?= $language['label']['vehicleAllowance']; ?>:</td>
                                                <td ><label>
                                                  <select name="vehicleAllowance" id="vehicleAllowance">
                                                    <option value="0">Select One</option>
                                                    <option value="400"<?= ($_SESSION['vehicleAllowance'] == '400') ? ' selected' : ''; ?>>$400</option>
                                                    <option value="500"<?= ($_SESSION['vehicleAllowance'] == '500') ? ' selected' : ''; ?>>$500</option>
                                                    <option value="600"<?= ($_SESSION['vehicleAllowance'] == '600') ? ' selected' : ''; ?>>$600</option>
                                                    <option value="800"<?= ($_SESSION['vehicleAllowance'] == '800') ? ' selected' : ''; ?>>$800</option>
                                                  </select>
                                                </label></td>
                                                <td align="right" >&nbsp;</td>
                                                <td ><select name="vehicleAllowance_new" id="vehicleAllowance_new">
                                                  <option value="0">Select One</option>
                                                  <option value="400"<?= ($_SESSION['vehicleAllowance_new'] == '400') ? ' selected' : ''; ?>>$400</option>
                                                  <option value="500"<?= ($_SESSION['vehicleAllowance_new'] == '500') ? ' selected' : ''; ?>>$500</option>
                                                  <option value="600"<?= ($_SESSION['vehicleAllowance_new'] == '600') ? ' selected' : ''; ?>>$600</option>
                                                  <option value="800"<?= ($_SESSION['vehicleAllowance_new'] == '800') ? ' selected' : ''; ?>>$800</option>
                                                </select></td>
                                              </tr>
                                              
                                              <tr>
                                                <td height="5" colspan="4"><img src="../images/spacer.gif" width="10" height="5"></td>
                                              </tr>
                                              <tr>
                                                <td colspan="4"><!--<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                      <td align="right"><strong>Percentage:</strong></td>
                                                      <td align="right">&nbsp;</td>
                                                      <td width="150">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input name="percentage" type="text" class="hideinput" id="percentage" style="text-align: right;" value="<?= $_SESSION['percentage']; ?>" size="3" maxlength="3" readonly>
                                                        %</td>
                                                    </tr>
                                                    <tr>
                                                      <td align="right"><div id="percentage"></div><strong>Increase Amount:</strong></td>
                                                      <td align="right">$</td>
                                                      <td><input name="increase" type="text" class="hideinput" id="increase" style="text-align: right;" value="<?= $_SESSION['increase']; ?>" size="8" maxlength="8" readonly></td>
                                                    </tr>
                                                </table>--></td>
                                              </tr>
                                          </table>
                                          </div>
                                         </td>
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
                                      <input name="stage" type="hidden" id="stage" value="one" />
                                      <input name="request_type" type="hidden" id="request_type" value="<?= strtolower($requestType); ?>">
                                        <input name="Next" type="image" class="button" id="Next" src="../images/button.php?i=b70.png&l=<?= $language['label']['next']; ?>" alt="<?= $language['label']['next']; ?>" border="0">
                                      &nbsp;</div></td>
                                  </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                            </tr>
                          </table>
                          </form></td>
                      </tr>
                    </table>
                  </td>
                </tr>
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
    <script type="text/javascript" src="/Common/Javascript/overlibmws.js"></script>
  	<SCRIPT type="text/javascript" SRC="/Common/Javascript/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/Javascript/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/Javascript/overlibmws/calendarmws.js"></SCRIPT>
    
    <script type="text/javascript" src="/Common/Javascript/Spry/widgets/textfieldvalidation/SpryValidationTextField-min.js"></script>
    <script type="text/javascript" src="/Common/Javascript/Spry/widgets/textareavalidation/SpryValidationTextarea-min.js"></script>
    <script type="text/javascript" src="/Common/Javascript/Spry/widgets/selectvalidation/SpryValidationSelect.js"></script>    
      
    <script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprydate1", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], hint:"yyyy-mm-dd"});
		var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});
		var sprytextarea2 = new Spry.Widget.ValidationTextarea("sprytextarea2", {validateOn:["blur", "change"]});
		var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {validateOn:["blur", "change"], invalidValue:"0"});
		var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["change", "blur"]});
		var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
		var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["change", "blur"]});
		var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
		var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {invalidValue:"0", validateOn:["blur", "change"]});
		var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "currency", {validateOn:["blur", "change"], hint:"0", minValue:<?= $default['min_wage']; ?>, maxValue:<?= (isset($GRADE['max'])) ? str_replace(',', '', $GRADE['max']) : 1000000; ?>});
		var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "currency", {validateOn:["blur", "change"], minValue:<?= $default['min_wage']; ?>, hint:"0"});
		var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "currency", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"], hint:"Type Employee Last Name or ID"});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {minChars:5, maxChars:5, validateOn:["change"]});
//-->
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