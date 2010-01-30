<?php
/**
 * Employee List
 *
 * index.php is the search page for the Employee List.
 *
 * @version 0.1
 * @link http://a2.yourcompany.com/go/Employees/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @global mixed $default[]
 * @filesource
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
 * - Common Information
 */
require_once('../include/config.php'); 



$q = strtolower($_GET['query']);					// Set search query to variable
//$eid = strtolower($_GET['eid']);
//$fst = strtolower($_GET['fst']);
//$lst = strtolower($_GET['lst']);

/* ---------- Send user to help screen ( Browser Search Bar ) ---------- */
if ($q == "help") {
	header("Location: search_help.php");
	exit(); 	
}

/* ---------- Check Search Type ( Browser Search Bar ) ---------- */
if (isset($_GET['query'])) {
	if ($q{1} == ":") {
		switch ($q{0}) {
			case 'f':
				$fst=substr($q, 2);					// Set first name only search
				$lst='';
				$query = "fst=" . $fst;
			break;
			case 'l':
				$fst='';
				$lst=substr($q, 2);					// Set last name only search
				$query = "lst=" . $lst;
			break;
		}
	} elseif (is_numeric($q)) {		
		$query = "eid=" . $q;						// Set Employee ID search
	} else {
		$fst = $q;									// Set first name for search
		$lst = $q;									// Set last name for search
		$query = "fst=" . $fst . "&lst=" . $lst;
	}
} else {
	$eid = (is_numeric($_GET['eid'])) ? 'eid=' . $eid : '';
	$fst = (strlen($_GET['fst']) >= 1) ? '&fst=' . $fst : '';
	$lst = (strlen($_GET['lst']) >= 1) ? '&lst=' . $lst : '';
	
	$search = $eid . $fst . $lst;
	$query = ($search{0} == '&') ? substr($search, 1) : $search;
}



//$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $language['label']['title1']; ?></title>
    <meta http-equiv="imagetoolbar" content="no" />
	<meta name="copyright" content="2007 your company" />
    <meta name="author" content="Thomas LeZotte" />
    
    <link href="/Common/noPrint.css" rel="stylesheet" type="text/css" />
    <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print" />
    <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet" />
    <link href="moreformating.css" type="text/css" charset="UTF-8" rel="stylesheet" />
    <!--[if IE]>
    <link href="moreformating_ie.css" type="text/css" charset="UTF-8" rel="stylesheet" />
    <![endif]--> 
    
    <?php if ($default['rss'] == 'on') { ?>
    <link rel="alternate" type="application/rss+xml" title="Human Capital - Employee List" href="<?= $default['URL_HOME']; ?>/Employees/<?= $default['rss_file']; ?>">
    <?php } ?>
    <link rel="search" type="application/opensearchdescription+xml" title="Employee Search" href="../Common/employee_search.xml">
    
	<script src="/Common/Javascript/overlibmws.js" type="text/javascript"></script>
    <script src="/Common/Javascript/overlibmws/overlibmws_iframe.js" type="text/javascript"></script>
    <script src="/Common/Javascript/disableEnterKey.js" type="text/javascript"></script>
        
    <script src="/Common/Javascript/googleAutoFillKill.js" type="text/javascript"></script>     
        
    <script src="/Common/Javascript/FloatingWindow.js" type="text/javascript"></script>
                
    <script src="/Common/Javascript/Spry/includes_minified/xpath.js" type="text/javascript"></script>
    <script src="/Common/Javascript/Spry/includes_minified/SpryData.js" type="text/javascript"></script>
    <script src="/Common/Javascript/Spry/includes_minified/SpryEffects.js" type="text/javascript"></script>
    <script src="/Common/Javascript/Spry/widgets/accordion/SpryAccordion.js" type="text/javascript"></script>
    <script type="application/javascript">var searchQuery='<?= $query; ?>';</script>
    <script src="../js/employees_spry_dataset.js" type="text/javascript"></script>
</head>

<body>
<div id="header">
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <img src="/Common/images/companyPrint.gif" alt="your company" name="Print" width="437" height="61" align="left" id="Print" />
  <div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../index.php"><img src="/Common/images/company.gif" alt="<?= $language['label']['title1']; ?> Home" name="company" width="300" height="50" border="0" align="left" /></a></td>
          <td align="right"></td>
        </tr>
        <tr>
          <td valign="bottom" align="right" colspan="2">&nbsp;<?php include($default['FS_HOME'].'/include/menu/main_right.php'); ?></td>
        </tr>
        <tr>
          <td width="100%" colspan="3">
            <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4" /></td>
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
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4" /></td>
                </tr>
                <tr>
                  <td class="BGGrayLight" rowspan="3"></td>
                  <td class="BGGrayMedium" rowspan="3"></td>
                  <td class="BGGrayDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3"></td>
                  <td class="BGColorDark" rowspan="3"><div style="width:200px" /></td>
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
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="../Administration/user_information.php" class="FieldNumberDisabled"><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled">[logout]</a>&nbsp;</div>
				  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                  </td>
                </tr>

                <tr>
                  <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25" /></td>

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

                  <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4" /></td>
                </tr>

                <tr>
                  <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4" /></td>

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

                  <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3" /></td>

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
</div>
<div id="SpryDataArea">
<div id="noPrint">
<form name="searchBarForm">
<table cellspacing="0" cellpadding="0" id="searchBarContainer">
  <tr>
    <td width="29" height="30" valign="bottom"><img src="../images/search_bar_left.gif" title="Filter employee list" width="29" height="30" /></td>
    <td height="30">
    <div id="searchBar">
        <input id="eidFilter" type="text" onkeyup="dsEmployees.filter(filterEID)" onFocus="clear_textbox();" value="EID" size="5" maxlength="5" />
        <input id="fstFilter" type="text" onKeyUp="dsEmployees.filter(filterFST)" onFocus="clear_textbox();" value="First Name" size="20" maxlength="20" />
        <input id="lstFilter" type="text" onKeyUp="dsEmployees.filter(filterLST)" onFocus="clear_textbox();" value="Last Name" size="20" maxlength="20" />
        <input id="deptFilter" type="text" onKeyUp="dsEmployees.filter(filterDEPT)" onFocus="clear_textbox();" value="Department" size="20" maxlength="20" />
        <input id="locationFilter" type="text" onKeyUp="dsEmployees.filter(filterLOCATION)" onFocus="clear_textbox();" value="Location" size="20" maxlength="20" />
    </div>    </td>
    <td width="29" height="30" valign="bottom"><img src="../images/search_bar_right.gif" width="29" height="30" /></td>
  </tr>
</table>
</form>
</div>
<div id="content" spry:region="dsEmployees" class="SpryHiddenRegion">
<!--<div style="text-align:left;">
	<input type="button" value="Prev" onclick="UpdatePage(pageOffset - pageSize);" />
	<input type="button" value="Next" onclick="UpdatePage(pageOffset + pageSize);" />
</div>-->
<div style="text-align:right; font-size:10px; width:720px; padding-top:45px;">{ds_RowCount} Employees found&nbsp;</div> 
<table border="0" cellpadding="0" cellspacing="0" class="tableBorder" id="employees">
	<tr id="columnHeadings">
        <th class="cellLeftHeadings" scope="col" spry:sort="eid" spry:choose="spry:choose">
        	<span spry:when="'{ds_SortColumn}' == 'eid' && '{ds_SortOrder}' == 'ascending'">EID<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
            <span spry:when="'{ds_SortColumn}' == 'eid' && '{ds_SortOrder}' == 'descending'">EID<img src="/Common/images/descending.gif" align="absmiddle" /></span>
            <span spry:default="spry:default">EID</span>
        </th>
	    <th class="cellLeftHeadings" scope="col" spry:sort="fst" spry:choose="spry:choose">
        	<span spry:when="'{ds_SortColumn}' == 'fst' && '{ds_SortOrder}' == 'ascending'">First Name<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
            <span spry:when="'{ds_SortColumn}' == 'fst' && '{ds_SortOrder}' == 'descending'">First Name<img src="/Common/images/descending.gif" align="absmiddle" /></span>
            <span spry:default="spry:default">First Name</span>
        </th>
		<th class="cellLeftHeadings" scope="col" spry:sort="lst" spry:choose="spry:choose">
        	<span spry:when="'{ds_SortColumn}' == 'lst' && '{ds_SortOrder}' == 'ascending'">Last Name<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
            <span spry:when="'{ds_SortColumn}' == 'lst' && '{ds_SortOrder}' == 'descending'">Last Name<img src="/Common/images/descending.gif" align="absmiddle" /></span>
            <span spry:default="spry:default">Last Name</span>
        </th>
		<th class="cellLeftHeadings" scope="col" spry:sort="dept" spry:choose="spry:choose">
        	<span spry:when="'{ds_SortColumn}' == 'dept' && '{ds_SortOrder}' == 'ascending'">Department<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
            <span spry:when="'{ds_SortColumn}' == 'dept' && '{ds_SortOrder}' == 'descending'">Department<img src="/Common/images/descending.gif" align="absmiddle" /></span>
            <span spry:default="spry:default">Department</span>
        </th>
	    <th class="cellLeftHeadings" scope="col" spry:sort="location" spry:choose="spry:choose">
        	<span spry:when="'{ds_SortColumn}' == 'location' && '{ds_SortOrder}' == 'ascending'">Location<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
            <span spry:when="'{ds_SortColumn}' == 'location' && '{ds_SortOrder}' == 'descending'">Location<img src="/Common/images/descending.gif" align="absmiddle" /></span>
            <span spry:default="spry:default">Location</span>
        </th>
	</tr>
    <tr>
        <td colspan="5">
        <div class="spryReady" spry:state="ready" spry:if="{ds_RowCount} == 0">No employees found.</div>
        <div class="spryLoading" spry:state="loading">Loading employee data...</div>
        <div class="spryFailed" spry:state="error">Failed to load employee data...</div></td>
    </tr>   
	<tr spry:repeat="dsEmployees" spry:even="evenRow" spry:odd="oddRow" spry:setrow="dsEmployees" spry:hover="rowHover" spry:select="rowSelected" spry:selected="selected"> 
  	    <td spry:choose="spry:choose">
          <div spry:when="'{@status}' == 'Current'">{@id} </div>
          <div spry:when="'{@status}' == 'Inactive'" style="color:#FF0000">{@id} </div></td>
		<td class="cellLeft">{fst}</td>
		<td class="cellLeft">{lst}</td>
	    <td class="cellLeft" spry:choose="spry:choose">
       		<div spry:when="'{dept}'.length != 0">({dept/@id}) {dept}</div></td>
		<td class="cellLeft" spry:choose="spry:choose">
        	<div spry:when="'{location}'.length != 0">({location/@conbr}) {location}</div></td>              
	</tr>
</table>
</div>
<div id="noPrint">
<div id="topbar">
  <div spry:detailregion="dsEmployees" id="boxshot" class="SpryHiddenRegion"><img src="/Common/images/portraits/{@id}.jpg" width="236" height="236" /></div>
		<div id="Acc1" class="Accordion">
		  <div class="AccordionPanel">
			<div class="AccordionPanelTab"><h3>Details</h3></div>
		  	<div spry:detailregion="dsEmployees" class="AccordionPanelContent">
					<div>Employee ID: <span class="detailValues">{@id}</span></div>
                    <div>First Name: <span class="detailValues">{fst}</span></div>
           	  		<div>Last Name: <span class="detailValues">{lst}</span></div>
                    <div>Hire Date: <span class="detailValues">{hire}</span></div>
                    <div>Department: <span class="detailValues" spry:if="'{dept}'.length != 0">({dept/@id}) {dept}</span></div>
                    <div>Location: <span class="detailValues" spry:if="'{location}'.length != 0">({location/@conbr}) {location}</span></div>
                    <div>Language: <span class="detailValues">{language}</span></div>                    
                    <span spry:choose="spry:choose"> 
                        <div spry:when="'{@status}' == 'Current'">Status: <span class="detailValues">{@status}</span></div>
                        <div spry:when="'{@status}' == 'Inactive'">Status: <span class="detailValuesRed">{@status}</span></div>
                    </span>
                    <hr />
                    <div>Email: <span class="detailValues"><a href="mailto:{email}" class="black">{email}</a></span></div>
                    <div>Username: <span class="detailValues">{username}</span></div>
                    <div>Password: <span class="detailValues">{password}</span></div>
		  	</div>
			</div>
			<div class="AccordionPanel">
			  <div class="AccordionPanelTab"><h3>Cell Phone</h3></div>
			  <div spry:detailregion="dsCell" class="AccordionPanelContent">
                	<div>Number: <span class="detailValues">{number}</span></div>
                    <div>Model: <span class="detailValues">{model}</span></div>
                    <div>Cycle: <span class="detailValues">{cycle}</span></div>
                    <div>Comments: <span class="detailValues">{comments}</span></div>
		      </div>
		  </div>
		</div>
</div>
</div>
<script type="text/javascript">
<!--
var Acc1 = new Spry.Widget.Accordion("Acc1");
-->
</script> 
</div> 

<div id="floatingFooter">
        <div class="footerContent">&copy; 2007 your company, LLC</span></div>
</div>
</body>
</html>
