<?php
/**
 * Request System
 *
 * search.php search available PO.
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
$Dbg->DatabaseName="Request";

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


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE request_id = ? and type = 'PO'",array($PO['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");
/* Getting suppliers from Standards */						 
$SUPPLIERS = $dbh->getAssoc("SELECT id, name ".
						   "FROM Supplier ".
						   "WHERE status = '0' ".
						   "ORDER BY name");															
/* Getting plant locations from Standards.Plants */								
$plant_sql = $dbh->prepare("SELECT id, name FROM Standards.Plants ORDER BY name ASC");
/* Getting plant locations from Standards.Department */	
$dept_sql  = $dbh->prepare("SELECT * FROM Standards.Department ORDER BY name ASC");
/* Getting plant locations from Standards.Category */	
$category_sql = $dbh->prepare("SELECT * FROM Standards.Category ORDER BY name ASC");
/* Getting units from Standards.Units */	
$units_sql = $dbh->prepare("SELECT * FROM Standards.Units ORDER BY name ASC");
/* Getting companies from Standards.Companies */								
$company_sql = $dbh->prepare("SELECT id, name ".
						 "FROM Standards.Companies ".
						 "WHERE id > 0 ".
						 "ORDER BY name");
/* Getting suppliers from Suppliers */						 
$supplier_sql = $dbh->prepare("SELECT BTVEND AS id, BTNAME AS name
							   FROM Vendor
							   ORDER BY name");						 
/* Getting staffings from Users */			 
$staffing_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
						 "FROM Users U, Standards.Employees E ".
						 "WHERE U.eid = E.eid and U.staffing = '1' and U.status = '0' and E.status = '0' ".
						 "ORDER BY E.lst ASC"); 
/* Project Originator from Users.Requesters */
$req_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
						 "FROM Users U, Standards.Employees E ".
						 "WHERE U.eid = E.eid and U.requester = '1' ".
						 "ORDER BY E.lst ASC");
/* Getting approver 1's from Users */									 
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
					  "FROM Users U, Standards.Employees E ".
					  "WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0' ".
					  "ORDER BY E.lst ASC"); 
/* Getting approver 2's from Users */						  
$app2_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
					   "FROM Users U, Standards.Employees E ".
					   "WHERE U.eid = E.eid and U.two = '1' and U.status = '0' and E.status = '0' ".
					   "ORDER BY E.lst ASC"); 
					   
/* 
 * Getting PO numbers from PO 
 */	
$PO = $dbh->getAll("SELECT DISTINCT(po) FROM PO ORDER BY po ASC");			
/* Generate $POARRAY for Autocomplete Javascript */
foreach ($PO as $key => $value) {
	$POARRAY .= "'$value[po]',";
}
$POARRAY = substr($POARRAY, 0, -1);		//Remove the last coma in $POARRAY
		   
/* 
 * Getting JOB numbers from PO 
 */	
$JOB = $dbh->getAll("SELECT DISTINCT(job) FROM PO ORDER BY job ASC");
/* Generate $JOBARRAY for Autocomplete Javascript */
foreach ($JOB as $key => $value) {
	$JOBARRAY .= "'$value[job]',";
}
$JOBARRAY = substr($JOBARRAY, 0, -1);		//Remove the last coma in $JOBARRAY

/* 
 * Getting Part numbers from PO 
 */	
$PART = $dbh->getAll("SELECT DISTINCT(part) FROM Items ORDER BY part ASC");
/* Generate $PARTARRAY for Autocomplete Javascript */
foreach ($PART as $key => $value) {
	$PARTARRAY .= "'$value[part]',";
}
$PARTARRAY = substr($PARTARRAY, 0, -1);		//Remove the last coma in $PARTARRAY

/* 
 * Getting VT numbers from PO 
 */	
$VT = $dbh->getAll("SELECT DISTINCT(vt) FROM Items ORDER BY vt ASC");
/* Generate $VTARRAY for Autocomplete Javascript */
foreach ($VT as $key => $value) {
	$VTARRAY .= "'$value[vt]',";
}
$VTARRAY = substr($VTARRAY, 0, -1);		//Remove the last coma in $VTARRAY

/* Getting CER numbers from CER */							 						 
$cer_sql = $dbh->prepare("SELECT id, cer 
                          FROM CER 
					      WHERE cer IS NOT NULL 
					      GROUP BY cer");
/* ------------------ END DATABASE CONNECTIONS ----------------------- */



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $language['label']['title1']; ?></title>
	<script language="JavaScript">
		function clikker(a,b,c)
		{
			if (a.style.display =="")
			{
				a.style.display = "none";
				b.src="<?= $default['url_home']; ?>/images/button.php?i=b90.png&l=Show Search";
				c.value = 0
			}
			else
			{
				a.style.display="";
				b.src="<?= $default['url_home']; ?>/images/button.php?i=b90.png&l=Hide Search";
				c.value = 1
			}
		}
	</script>	
	<script language="javascript" type="text/javascript" src="/Common/Javascript/autocomplete/actb.js"></script>
	<script language="javascript" type="text/javascript" src="/Common/Javascript/autocomplete/common.js"></script>
	<script>
	var vtarray=new Array(<?= $VTARRAY; ?>);
	var partarray=new Array(<?= $PARTARRAY; ?>);
	var jobarray=new Array(<?= $JOBARRAY; ?>);
	var poarray=new Array(<?= $POARRAY; ?>);
	</script>
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
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --><!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
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
				<!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_Main.lbi" --><?php
$menu1 = $default['url_home'] . "/Requests/index.php";
$menu2 = $default['url_home'] . "/Requests/list.php?action=my&access=0";
$menu3 = $default['url_home'] . "/Requests/list.php";
$menu4 = $default['url_home'] . "/Requests/search.php";
$menu5 = $default['url_home'] . "/Requests/Reports/index.php";
?>
<table width="200" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
	  <td>&nbsp;</td>
	  <td>
		<table cellspacing="0" cellpadding="0" summary="" border="0">
			<tr>
		  	  <td nowrap>&nbsp;<a href="<?= $menu1; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu1) ? on : off; ?>" onmouseover="return overlib('Start a new <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="<?= $menu2; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu2) ? on : off; ?>" onmouseover="return overlib('List of your <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">My Requests</a>&nbsp;</td>
			  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>	  
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu3; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu3) ? on : off; ?>" onmouseover="return overlib('List all <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">All Requests</a>&nbsp;</td>
			  <?php } ?>
			  <!--   
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu4; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu4) ? on : off; ?>" onmouseover="return overlib('Search all <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Search</a>&nbsp;</td>	
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/Dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu5; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu5) ? on : off; ?>" onmouseover="return overlib('Reports on spending habits', CAPTION, 'HELP', TEXTPADDING, 2, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C');" onmouseout="nd();">Reports</a>&nbsp;</td>
			  -->
			</tr>
		</table>
	  </td>
	  <td>&nbsp;</td>
	</tr>
</table><!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>

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
    <!-- InstanceBeginEditable name="main" --><br>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
      <table width="925"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="25" valign="top"><div align="right"><a href="javascript:void(0);"  <?php help("Show/Hide Search Criteria", 'default'); ?>><img src="../images/button.php?i=b90.png&l=Hide Search" border="0" id="ReqIcon" onClick="clikker(Req,ReqIcon,ReqForm);"></a>&nbsp;&nbsp;</div></td>
        </tr>
        <tr>
          <td>
		  <div style="display: display;" id="Req">
		  <input id="ReqForm" value="0" name="ReqForm" type="hidden">
		  <script type="text/javascript">Req.style.display='';</script>
		  <table  border="0" cellpadding="0" cellspacing="0">
            <tr class="BGAccentVeryDark">
              <td height="30">&nbsp;&nbsp;<span class="DarkHeaderSubSub">Search...</span> </td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                  <tr>
                    <td width="100">Company:</td>
                    <td><select name="company" id="company">
                        <option value="">Select One</option>
                        <?php
						  $company_sth = $dbh->execute($company_sql);
						  while($company_sth->fetchInto($COMPANY)) {
							$selected = ($_POST['company'] == $COMPANY[id]) ? selected : $blank;
							print "<option value=\"".$COMPANY[id]."\" ".$selected.">".$COMPANY[name]."</option>\n";
						  }
						?>
						</select></td>
                    <td width="125">PO Number:</td>
                    <td><input name="po" type="text" id="po" size="11" autocomplete="off"><script>var poobj = actb(document.getElementById('po'),poarray);</script></td>
                    <td width="125">Requester:</td>
                    <td><select name="req">
                        <option value="">Select One</option>
                        <?php
						  $req_sth = $dbh->execute($req_sql);
						  while($req_sth->fetchInto($REQ)) {
							$selected = ($_POST['req'] == $REQ[eid]) ? selected : $blank;
							print "<option value=\"".$REQ[eid]."\" ".$selected.">".ucwords(strtolower($REQ[lst])).", ".ucwords(strtolower($REQ[fst]))."</option>\n";
						  }
						  ?>
						</select></td>
                  </tr>
                  <tr>
                    <td nowrap>Department:</td>
                    <td><select name="department" id="department">
                        <option value="">Select One</option>
                        <?php
						  $dept_sth = $dbh->execute($dept_sql);
						  while($dept_sth->fetchInto($DEPT)) {
							$selected = ($_POST['department'] == $DEPT[id]) ? selected : $blank;
							print "<option value=\"".$row[id]."\" ".$selected." ".$readonly.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
						  }
						  ?>
						</select></td>
                    <td>Part Number: </td>
                    <td><input name="part" type="text" id="part" value="<?= $_POST['part']; ?>" autocomplete="off"><script>var partobj = actb(document.getElementById('part'),partarray);</script></td>
                    <td>Approver 1: </td>
                    <td><select name="app1" id="app1" disabled>
                        <option value="">Select One</option>
                        <?php
						  $app1_sth = $dbh->execute($app1_sql);
						  while($app1_sth->fetchInto($APP1)) {
							$selected = ($_POST['app1'] == $APP1[eid]) ? selected : $blank;
							print "<option value=\"".$APP1[eid]."\" ".$selected.">".ucwords(strtolower($APP1[lst].", ".$APP1[fst]))."</option>";
						  }
						 ?>
						</select></td>
                  </tr>
                  <tr>
                    <td>Plant:</td>
                    <td><select name="plant">
                        <option value="">Select One</option>
                        <?php
						  $plant_sth = $dbh->execute($plant_sql);
						  while($plant_sth->fetchInto($PLANT)) {
							$selected = ($_POST['plant'] == $PLANT[id]) ? selected : $blank;
							print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
						  }
						  ?>
						</select></td>
                    <td>Job Number: </td>
                    <td><input name="job" type="text" id="job" value="<?= $_POST['job']; ?>" autocomplete="off"><script>var jobobj = actb(document.getElementById('job'),jobarray);</script></td>
                    <td>Approver 2: </td>
                    <td><select name="app2" id="app2" disabled>
                        <option value="">Select One</option>
                        <?php
						  $app2_sth = $dbh->execute($app2_sql);
						  while($app2_sth->fetchInto($APP2)) {
							$selected = ($_POST['app2'] == $APP2[eid]) ? selected : $blank;
							print "<option value=\"".$APP2[eid]."\" ".$selected.">".ucwords(strtolower($APP2[lst].", ".$APP2[fst]))."</option>";
						  }
						  ?>
						</select></td>
                  </tr>
                  <tr>
                    <td>Ship To: </td>
                    <td><select name="ship">
                        <option value="">Select One</option>
                        <?php
					  $plant_sql_sth = $dbh->execute($plant_sql);
					  while($plant_sql_sth->fetchInto($PLANT)) {
					    $selected = ($_POST['ship'] == $PLANT[id]) ? selected : $blank;
						print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>VT Number: </td>
                    <td><input name="vt" type="text" id="vt" value="<?= $_POST['vt']; ?>" autocomplete="off"><script>var vtobj = actb(document.getElementById('vt'),vtarray);</script></td>
                    <td>Staffing:</td>
                    <td><select name="staffing" id="staffing" disabled>
                        <option value="">Select One</option>
                        <?php
				  $staffing_sth = $dbh->execute($staffing_sql);
				  while($staffing_sth->fetchInto($ISSUER)) {
				    $selected = ($_POST['staffing'] == $ISSUER[eid]) ? selected : $blank;
					print "<option value=\"".$ISSUER[eid]."\" ".$selected.">".ucwords(strtolower($ISSUER[lst].", ".$ISSUER[fst]))."</option>";
				  }
				  ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Vendor:</td>
                    <td><select name="sup" id="sup">
                        <option value="">Select One</option>
                        <?php
					  $supplier_sth = $dbh->execute($supplier_sql);
					  while($supplier_sth->fetchInto($SUPPLIER)) {
					    $selected = ($_POST['sup'] == $SUPPLIER[id]) ? selected : $blank;
						print "<option value=\"".$SUPPLIER[id]."\" ".$selected.">".ucwords(strtolower($SUPPLIER[name]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>Purpose:</td>
                    <td><input type="text" name="purpose" value="<?= $_POST['purpose']; ?>" autocomplete="off"></td>
                    <td nowrap>Request Status: </td>
                    <td><select name="status" id="status">
                        <option value="">Select One</option>
                        <option value="N">New</option>
                        <option value="A">Approved</option>
                        <option value="O">Ordered</option>
                        <option value="R">Received</option>
                        <option value="X">Not Approved</option>
                        <option value="C">Canceled</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td nowrap>CER Number: </td>
                    <td><select name="cer">
                      <option value="0">Select One</option>
                      <?php
						  $cer_sth = $dbh->execute($cer_sql);
						  while($cer_sth->fetchInto($CER)) {
							$selected = ($_POST['cer'] == $CER[id]) ? selected : $blank; 
							print "<option value=\"".$CER[id]."\" ".$selected.">".$CER[cer]."</option>\n";
						  }
						?>
                    </select></td>
                    <td nowrap>Item Description:</td>
                    <td><input type="text" name="descr"  value="<?= $_POST['descr']; ?>"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td height="30"><table width="100%"  border="0">
                  <tr>
                    <td valign="top" nowrap><span class="GlobalButtonTextDisabled">NOTE: Use a percent sign (%), for a wild card search </span></td>
                    <td valign="bottom"><div align="right">
                      <input name="action" type="hidden" id="action" value="search">
                        <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Search" border="0">&nbsp;
					</div></td>
                  </tr>
              </table></td>
            </tr>
          </table></div></td>
        </tr>
      </table>
    </form>
	  <?php 
	  /* Process search criteria */
	  if ($_POST['action'] == 'search') {
	  
		$S_PO = (!empty($_POST['po'])) ? "p.po LIKE '".$_POST['po']."' AND " : '';
		$S_REQ = (!empty($_POST['req'])) ? "p.req='".$_POST['req']."' AND " : '';
		$S_COM = (!empty($_POST['company'])) ? "p.company='".$_POST['company']."' AND " : '';
		$S_SUP = (!empty($_POST['sup'])) ? "p.sup='".$_POST['sup']."' AND " : '';
		//$S_PLA = (!empty($_POST['plant'])) ? "plant='".$_POST['plant']."' AND " : '';		
		$S_SHI = (!empty($_POST['ship'])) ? "p.ship='".$_POST['ship']."' AND " : '';
		$S_DEP = (!empty($_POST['department'])) ? "p.department='".$_POST['department']."' AND " : '';
		$S_JOB = (!empty($_POST['job'])) ? "p.job LIKE '".$_POST['job']."' AND " : '';
		$S_PUR = (!empty($_POST['purpose'])) ? "p.purpose LIKE '".$_POST['purpose']."' AND " : '';
		$S_STA = (!empty($_POST['status'])) ? "p.status='".$_POST['status']."' AND " : '';
		$S_CER = (!empty($_POST['cer'])) ? "p.cer='".$_POST['cer']."' AND " : '';
		
		$S_DES = (!empty($_POST['descr'])) ? "i.descr LIKE '".$_POST['descr']."' AND " : '';
		$S_PLA = (!empty($_POST['plant'])) ? "i.plant='".$_POST['plant']."' AND " : '';
		$S_PAR = (!empty($_POST['part'])) ? "i.part LIKE '".$_POST['part']."' AND " : '';
		$S_JOB = (!empty($_POST['job'])) ? "i.job LIKE '".$_POST['job']."' AND " : '';
		$S_VT = (!empty($_POST['vt'])) ? "i.vt LIKE '".$_POST['vt']."' AND " : '';

		$WHERE = "WHERE p.id = i.request_id AND
						$S_PO $S_REQ $S_COM $S_SUP $S_SHI $S_DEP $S_JOB $S_PUR $S_STA $S_CER 
						$S_DES $S_PLA $S_PAR $S_JOB $S_VT";
		$SEARCH = preg_replace("/AND\s+$/", "", $WHERE);
		
		if ($debug) {
			echo "WHERE: ".$WHERE."<BR>";
			echo "SEARCH: ".$SEARCH."<BR>";
		}
		
		/* SQL for PO list */
		$po_sql = "SELECT DISTINCT(p.id), p.po, p.purpose, p.reqDate, p.req, p.sup, p.total 
				   FROM PO p, Items i
				   $SEARCH 
				   ORDER BY p.reqDate DESC";
		$Dbg->addDebug($po_sql,DBGLINE_QUERY,__FILE__,__LINE__);
		$Dbg->DebugPerf(DBGLINE_QUERY);		   
		$po_query =& $dbh->prepare($po_sql);
		$po_sth = $dbh->execute($po_query);
		$num_rows = $po_sth->numRows();	
		$Dbg->DebugPerf(DBGLINE_QUERY);	
		
		/* Dont display column headers and totals if no requests */
		if ($num_rows == 0) {				
	  ?>
		<div align="center" class="DarkHeaderSubSub">No Requests Found</div>
		<?php } else { ?>	
		<!--
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="errorFirefox">Multiple Entries shown in Search Results is a display bug</td>
          </tr>
        </table>
		-->
    <br>
    <table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="BGAccentVeryDark"><div align="left">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Search Results... </td>
              <td>&nbsp;</td>
            </tr>
        </table>
    </div></td>
  </tr>
  <tr>
    <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%"  border="0">
                <tr>
                  <td height="25" class="BGAccentDark">&nbsp;</td>
                  <td class="BGAccentDark"><strong>&nbsp;PO</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Purpose</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Requested<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Supplier</strong></td>
                  <td class="BGAccentDark"><div align="center"><strong>&nbsp;Total</strong></div></td>
                </tr>
                <?php
					/* Reset items total variable */
					$itemsTotal = 0;
					$span = 4;
					
					/* Loop through list of POs */
					while($po_sth->fetchInto($PO)) {
						/* Line counter for alternating line colors */
						$counter++;
						$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
					?>
                <tr <?php pointer($row_color); ?>>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Get a Detailed view', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="print.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Click here to print this Purchase Order Request<br><br><b>Printer Setup:</b><br>Margins: .25<br>Orientation: Landscape', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/printer.gif" width="15" height="20" border="0" align="absmiddle"></a></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $PO[po]; ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower(substr(stripslashes($PO[purpose]), 0, 40))); ?>
                  <?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[req]])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo date("M-d-Y", strtotime($reqDate[0])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($SUPPLIERS[$PO[sup]])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="2%">$</td>
                        <td width="98%"><div align="right"><?= number_format($PO['total'], 2, '.', ','); ?></div></td>
                      </tr>
                  </table></td>
                </tr>
                <?php $itemsTotal += $PO[total]; ?>
                <?php } ?>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentDark"><table  border="0" align="right" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="padding"><strong>Total:</strong></td>
                  <td class="padding">&nbsp;$<?= number_format($itemsTotal, 2, '.', ','); ?></td>
                </tr>
            </table></td>
          </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;<span class="GlobalButtonTextDisabled"><?= $num_rows ?> Requests</span></td>
  </tr>
  </table>
	  <?php } ?>
	  <?php } ?>
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
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- InstanceEndEditable --></div></td>
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