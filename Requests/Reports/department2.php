<?php 
/**
 * - Start Page Loading Timer
 */
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');
/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php');
/**
 * - Check User Access
 */
require_once('../../security/check_access1.php');
/**
 * - Common Information
 */
require_once('../../include/config.php'); 

/* --- PEAR QuickForm --- */
require_once ('HTML/QuickForm.php');


/* --- Process Form1 Data --- */
if (isset($_GET['quarter']) and isset($_GET['year'])) {
	/* Set Quarterly start and end dates */
	switch ($_GET['quarter']) {
		case 'Q1':
					$start = $_GET['year']['y']."-01-01";
					$end = $_GET['year']['y']."-03-31";
					break;
		case 'Q2':
					$start = $_GET['year']['y']."-04-01";
					$end = $_GET['year']['y']."-06-30";
					break;		
		case 'Q3':
					$start = $_GET['year']['y']."-07-01";
					$end = $_GET['year']['y']."-09-30";
					break;	
		case 'Q4':
					$start = $_GET['year']['y']."-10-01";
					$end = $_GET['year']['y']."-12-31";
					break;		
	}
}

/* --- Process Form2 Data --- */
if (isset($_GET['sDate']) AND isset($_GET['eDate'])) {
	$sDate = $_GET['sDate'];
	$eDate = $_GET['eDate'];

	$start = $sDate['y']."-".$sDate['M']."-".$sDate['d'];
	$end = $eDate['y']."-".$eDate['M']."-".$eDate['d'];
}

/* Get Department information a purchases */
if (isset($_GET['quarter']) OR isset($_GET['sDate'])) {
$sql = <<< SQL
	SELECT s.id, s.name, sum( p.total ) AS total
	FROM PO p, Standards.Department s
	WHERE  s.id = p.department
	  AND p.reqDate >= '$start'
	  AND p.reqDate <= '$end'
	  AND p.status IN ('A','O','R')
	GROUP BY p.department
	ORDER BY total DESC
SQL;

$data_sql = $dbh->prepare($sql);
}

/* Get Plants and Employees from Stanards database */
$DEPARTMENT = $dbh->getAssoc("SELECT id, name FROM Standards.Department");
$SUPPLIER = $dbh->getAssoc("SELECT BTVEND AS id, BTNAME AS name FROM Vendor");
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u, Standards.Employees e 
							 WHERE e.eid = u.eid");	



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
  <script src="../../Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
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
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><!-- #BeginLibraryItem "/Library/rightMenu_PO.lbi" --><?php
$menu1 = $default['url_home'] . "/Requests/index.php";
$menu1_css = ($_SERVER['REQUEST_URI'] == $menu1) ? on : off;
$menu1_check = $default['url_home'] . "/Requests/detail.php";
$menu1_css = ($_SERVER['REQUEST_URI'] == $menu1_check) ? on : off;

$menu2 = $default['url_home'] . "/Requests/_index.php?action=adjustment";
$menu2_css = ($_SERVER['REQUEST_URI'] == $menu2) ? on : off;

$menu3 = $default['url_home'] . "/Requests/_index.php?action=transfer";
$menu3_css = ($_SERVER['REQUEST_URI'] == $menu3) ? on : off;

$menu4 = $default['url_home'] . "/Requests/_index.php?action=conversion";
$menu4_css = ($_SERVER['REQUEST_URI'] == $menu4) ? on : off;

$menu5 = $default['url_home'] . "/Requests/_index.php?action=promotion";
$menu5_css = ($_SERVER['REQUEST_URI'] == $menu5) ? on : off;

$menu6 = $default['url_home'] . "/Administration/index.php";
$menu6_css = ($_SERVER['REQUEST_URI'] == $menu6) ? on : off;
?>


<table  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>
	<?php if ($_SESSION['hcr_access'] >= 1) { ?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu6_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu6_css); ?>"><a href="<?= $menu6; ?>" class="<?= $menu6_css; ?>">Administration</a> </td>
        <td><img src="../images/rM2<?= $menu6_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table>
	<?php } else { ?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu6_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu6_css); ?>"><a href="<?= $menu6; ?>" class="<?= $menu6_css; ?>">My Account</a> </td>
        <td><img src="../images/rM2<?= $menu6_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table>
	<?php } ?>	</td>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu5_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu5_css); ?>"><a href="<?= $menu5; ?>" class="<?= $menu5_css; ?>">Promotion</a> </td>
        <td><img src="../images/rM2<?= $menu5_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table></td>	
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu4_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu4_css); ?>"><a href="<?= $menu4; ?>" class="<?= $menu4_css; ?>">Contract Conversion</a> </td>
        <td><img src="../images/rM2<?= $menu4_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table></td>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu3_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu3_css); ?>"><a href="<?= $menu3; ?>" class="<?= $menu3_css; ?>">Transfer</a> </td>
        <td><img src="../images/rM2<?= $menu3_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table></td>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu2_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu2_css); ?>"><a href="<?= $menu2; ?>" class="<?= $menu2_css; ?>">Wage Adjustment</a> </td>
        <td><img src="../images/rM2<?= $menu2_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table></td>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/rM1<?= $menu1_css; ?>.gif" width="12" height="17"></td>
        <td class="rightMenu<?= strtoupper($menu1_css); ?>"><a href="<?= $menu1; ?>" class="<?= $menu1_css; ?>">New Hire</a> </td>
        <td><img src="../images/rM2<?= $menu1_css; ?>.gif" width="12" height="17"></td>
      </tr>
    </table></td>			
    <td width="5"><img src="../../images/spacer.gif" width="5" height="10"></td>
  </tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>

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
		  	  <td nowrap>&nbsp;<a href="<?= $menu1; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu1) ? on : off; ?>" onMouseOver="return overlib('Start a new <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onMouseOut="nd();">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="<?= $menu2; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu2) ? on : off; ?>" onMouseOver="return overlib('List of your <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onMouseOut="nd();">My Requests</a>&nbsp;</td>
			  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>	  
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu3; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu3) ? on : off; ?>" onMouseOver="return overlib('List all <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onMouseOut="nd();">All Requests</a>&nbsp;</td>
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
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?> <a href="../../Administration/user_information.php" class="FieldNumberDisabled" <?php help('', 'Edit your user information', 'default'); ?>><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;&nbsp;<a href="../../logout.php" class="FieldNumberDisabled" <?php help('', 'Selecting [logout] will Log you out of the '.$default[title1].' and stop automatic cookie login', 'default'); ?>>[logout]</a>&nbsp;</div>
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
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><div id="noPrint">
        <table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
          <tbody>
            <tr>
              <td valign="top" width="13" background="../../images/asyltlb.gif"><img height="20" alt="" src="../../images/t.gif" width="13" border="0"></td>
              <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../../images/asybase.gif" width="145" border="0">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top" height="10" >&nbsp;</td>
                </tr>
                <tr>
                  <td>
				<?php
				$year = date("Y");

				$form1 =& new HTML_QuickForm('Form1', 'get');
				$form1->setDefaults(array(
					'year' => array('y' => $year)
				));
								
				$form1->addElement('header', '', 'Quaterly Report');
				$form1->addElement('select', 'quarter', 'Quarter:', array('Q1' => 'Q1', 'Q2' => 'Q2', 'Q3' => 'Q3', 'Q4' => 'Q4'));
				$form1->addElement('date', 'year', 'Year:', array('format'=>'y', 'minYear'=>2001, 'maxYear'=>$year));
				//$form1->addElement('hidden', 'stage', 'process');
				
				$form1->addElement('submit', 'submit', 'Submit');
				$form1->display();
				?></td>
                </tr>
                <tr>
                  <td height="10"><img src="../../images/spacer.gif" width="10" height="10"></td>
                </tr>
                <tr>
                  <td>
				<?php
				$month = date("m");
				$year = date("Y");
				
				$form2 =& new HTML_QuickForm('Form2', 'get');
				$form2->setDefaults(array(
					'sDate' => mktime(0, 0, 0, $month, 01, $year)
				));
				
				$form2->setConstants(array(
					'eDate' => time()
				));
				$form2->addElement('header', '', 'Custom Report');
				$form2->addElement('date', 'sDate', '', array('format'=>'M d y', 'minYear'=>2001, 'maxYear'=>$year));
				$form2->addElement('date', 'eDate', '', array('format'=>'M d y', 'minYear'=>2001, 'maxYear'=>$year));
				//$form2->addElement('hidden', 'stage', 'process');
				
				$form2->addElement('submit', 'submit', 'Submit');
				$form2->display();
				?></td>
                </tr>
              </table></td>
              <td valign="top" width="22" background="../../images/asyltrb.gif"><img height="20" alt="" src="../../images/t.gif" width="22" border="0"></td>
            </tr>
            <tr>
              <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../../images/asyltb.gif" width="200" border="0"></td>
            </tr>
            <tr>
              <td valign="top" colspan="3">&nbsp;</td>
            </tr>
            <tr>
              <td valign="top" colspan="3">&nbsp;</td>
            </tr>
          </tbody>
        </table>
    </div></td>
    <td><div align="center">
		<br>		
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="BGAccentDarkBorder"><table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25" align="center" class="BGAccentDark"><strong>Department Purchases</strong> </td>
                  </tr>
                  <tr>
                    <td valign="middle" class="xpHeaderTop"><div class="ApplicationSwitcherText">Top 10 between
                      <?= date("M d, Y",strtotime($start)); ?>
                      and
                      <?= date("M d, Y",strtotime($end)) ?>
                    </div></td>
                  </tr>
                  <tr>
                    <td>
                    <OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                        codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" 
                        WIDTH="400" 
                        HEIGHT="250" 
                        id="charts" 
                        ALIGN="">
                    <PARAM NAME=movie VALUE="/Common/Javascript/maani/charts.swf?library_path=/Common/Javascript/maani/charts_library&xml_source=test.xml">
                    <PARAM NAME=quality VALUE=high>
                    <PARAM NAME=bgcolor VALUE=#666666>
                    
                    <EMBED src="/Common/Javascript/maani/charts.swf?library_path=/Common/Javascript/maani/charts_library&xml_source=test.xml" 
                           quality=high 
                           bgcolor=#666666  
                           WIDTH="400" 
                           HEIGHT="250" 
                           NAME="charts" 
                           ALIGN="" 
                           swLiveConnect="true" 
                           TYPE="application/x-shockwave-flash" 
                           PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
                    </EMBED>
                    </OBJECT></td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </p><br>
			<br>
			<div style="page-break-before:always">
			<div align="left"><img src="/Common/images/CompanyPrint.gif" alt="your company" width="437" height="61" id="Print" /></div>
			<br>
			<span class="DarkHeader">Departmental Purchases</span><br>
			<span class="DarkHeaderSubSub">
			<?= date("M d, Y",strtotime($start)); ?> 
			to 
			<?= date("M d, Y",strtotime($end)) ?>
			</span><br>
			<br>	
			<table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="25" class="BGAccentDark">&nbsp;</td>
              </tr>
              <tr>
                <td><table border="0" cellpadding="0" cellspacing="0" class="xpHeaderBorder">
                  <tr>
                    <td width="50" class="xpHeaderLeft">&nbsp;</td>
                    <td class="xpHeaderTop">Name</td>
                    <td class="xpHeaderTopActive">Amount</td>
                  </tr>
                  <?php
			      $data_sth = $dbh->execute($data_sql);
				  while($data_sth->fetchInto($DATA)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? 'xpHeaderOdd' : 'xpHeaderEven';
				    $total_amount = $total_amount + $DATA['total'];
			      ?>
                  <tr>
                    <td nowrap class="xpHeaderLeft"><?= $counter; ?></td>
                    <td nowrap class="<?= $row_color; ?>"><a href="<?= $_SERVER['../../PO/Reports/PHP_SELF']; ?>?action=search&department=<?= $DATA['id']; ?>&<?= $_SERVER['QUERY_STRING']; ?>" class="black"><?= ucwords(strtolower($DATA['name'])); ?></a></td>
                    <td nowrap class="<?= $row_color; ?>"><div align="right">$
                          <?= number_format($DATA['total'], 2, '.', ','); ?>
                    </div></td>
                  </tr>
                  <?php } ?>
                  <tr>
                    <td class="xpHeaderLeft">&nbsp;</td>
                    <td class="xpHeaderTotal">Total:</td>
                    <td class="xpHeaderTotal"><div align="right">$ <?= number_format($total_amount, 2, '.', ','); ?>&nbsp;&nbsp;</div></td>
                  </tr>
                </table></td>
              </tr>
            </table>
			<br>
			</div>
			<?php 
			if ($_GET['action'] == 'search') { 
				$po_sql = "SELECT DISTINCT(id), po, purpose, reqDate, req, sup, total 
						   FROM PO
						   WHERE department=$_GET[department]  AND reqDate >= '$start' AND reqDate <= '$end' AND status IN ('A','O','R')
						   ORDER BY reqDate DESC";
				$Dbg->addDebug($po_sql,DBGLINE_QUERY,__FILE__,__LINE__);
				$Dbg->DebugPerf(DBGLINE_QUERY);		   
				$po_query =& $dbh->prepare($po_sql);
				$po_sth = $dbh->execute($po_query);
				$num_rows = $po_sth->numRows();	
				$Dbg->DebugPerf(DBGLINE_QUERY);	
			?>
            <div style="page-break-before:always">
              <div align="left"><img src="/Common/images/CompanyPrint.gif" alt="your company" width="437" height="61" id="Print" /></div>
              <br>
              <br>
              <span class="DarkHeader">Departmental Purchases</span><br>
              <span class="DarkHeaderSubSub"><?= ucwords(strtolower($DEPARTMENT[$_GET[department]])); ?>
              <br>
              <?= date("M d, Y",strtotime($start)); ?> to <?= date("M d, Y",strtotime($end)) ?>
              </span><br>
              <br>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="25" class="BGAccentDark">&nbsp;</td>
                </tr>
                <tr>
                  <td><table border="0" cellpadding="0" cellspacing="0" class="xpHeaderBorder">
                      <tr>
                        <td width="50" class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderTop">PO</td>
                        <td class="xpHeaderTop">Purpose</td>
                        <td class="xpHeaderTop">Requester</td>
                        <td class="xpHeaderTopActive">Requested</td>
                        <td class="xpHeaderTop">Supplier</td>
                        <td class="xpHeaderTop">Total</td>
                      </tr>
                      <?php
					  $total_amount = 0;
					  $po_sth = $dbh->execute($po_query);
					  while($po_sth->fetchInto($PO)) {
						/* Line counter for alternating line colors */
						$counter++;
						$row_color = ($counter % 2) ? 'xpHeaderOdd' : 'xpHeaderEven';
						$total_amount = $total_amount + $PO['total'];
					  ?>
                      <tr>
                        <td nowrap class="<?= $row_color; ?>"><a href="../detail.php?id=<?= $PO[id]; ?><?= $approval_option; ?>" <?php help('', 'Get a Detailed view', 'default'); ?>><img src="../../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="../print.php?id=<?= $PO[id]; ?>" <?php help('', 'Print a hardcopy', 'default'); ?>><img src="../../images/printer.gif" width="15" height="20" border="0" align="absmiddle"></a></td>
                        <td nowrap class="<?= $row_color; ?>"><a href="<?= $_SERVER['../../PO/Reports/PHP_SELF']; ?>?action=search&id=<?= $DATA['id']; ?>&start=<?= $start; ?>&end=<?= $end; ?>" class="black">
                          <?= $PO['po']; ?>
                        </a></td>
                        <td nowrap class="<?= $row_color; ?>">
                          <?= ucwords(strtolower(substr(stripslashes($PO[purpose]), 0, 40))); ?>
                        <?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?>                        </td>
                        <td nowrap class="<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[req]])); ?></td>
                        <td nowrap class="<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo date("M-d-Y", strtotime($reqDate[0])); ?></td>
                        <td nowrap class="<?= $row_color; ?>"><?= ucwords(strtolower($SUPPLIER[$PO[sup]])); ?></td>
                        <td nowrap class="<?= $row_color; ?>"><div align="right"> $
                          <?= number_format($PO['total'], 2, '.', ','); ?>
                        </div></td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderTotal">Total:</td>
                        <td class="xpHeaderTotal"><div align="right">$
                          <?= number_format($total_amount, 2, '.', ','); ?>
  &nbsp;&nbsp;</div></td>
                      </tr>
                  </table></td>
                </tr>
              </table>
              <br>
            </div>
			<? } ?>
    </div>
        <span class="NavBarInactiveLink"><br>
      </span></td>
  </tr>
</table>
<br>
<br>
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
          <td rowspan="2" valign="bottom"><img src="../../images/c-skir.gif" alt="" width="19" height="20" align="absmiddle" id="noPrint"></td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" nowrap><!-- InstanceBeginEditable name="copyright" --><?php include('../include/copyright.php'); ?><!-- InstanceBeginEditable --></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- #BeginLibraryItem "/Library/versionadmin.lbi" --><script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tbody>
    <tr>
      <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">v1.0</a></td>
      <td width="20" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../../images/notes.gif" alt="Release Notes" width="12" height="15" border="0" align="absmiddle"></a></div></td>
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
include_once('../../PO/Reports/debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>