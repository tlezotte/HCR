<?php 
/**
 * License Manager
 *
 * software.php generates graphs for software.
 *
 * @version 1.5
 * @link http://a2.yourcompany.com/go/License/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * ChartDirector
 * @link http://www.advsofteng.com/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
 */
 
/**
 * - Set debug mode
 */
$debug_page = false;

/**
 * - Start Page Loading Timer
 */
include_once('../../PO/include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php');
/**
 * - Check User Access
 */
require_once('../../security/check_user.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Software Report', $_SESSION['eid']);

/* --- PEAR QuickForm --- */
require_once ('HTML/QuickForm.php');


/* Set number of software products to display */
if (! array_key_exists('limit', $_GET)) {
	$_GET['limit'] = 10;
}

/* ------------------ START DATABASE ACCESS ----------------------- */ 
/* SQL for Licenses list */
$sql = <<< SQL
	SELECT s.name, l.version, sum( l.qty ) AS total
	FROM Strings l, Standards.Software s
	WHERE l.name = s.id
	  AND l.status = '1'
	GROUP BY s.name, l.version
	ORDER BY ? ?
SQL;

$data_sql = $dbh->prepare($sql);
/* ------------------ END DATABASE ACCESS ----------------------- */



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
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --><!-- InstanceBeginEditable name="head" -->  <!-- InstanceEndEditable -->
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
            <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable -->          </td>
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
		  	  <td nowrap>&nbsp;<a href="<?= $menu1; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu1) ? on : off; ?>" onmouseover="return overlib('Start a new <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="<?= $menu2; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu2) ? on : off; ?>" onmouseover="return overlib('List of your <?= $default['title1']; ?>', CAPTION, '', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">My Requests</a>&nbsp;</td>
			  <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>	  
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../../images/Dot.gif" width="10" height="10"></div></td>
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
                      <td><?php
				$year = date("Y");

				$form1 =& new HTML_QuickForm('Form1', 'get');
				$form1->setDefaults(array(
					'limit' => '10'
				));
								
				$form1->addElement('header', '', 'Chart Options');
				$form1->addElement('text', 'limit', 'Count:', array('size' => '5', 'maxlength' => '5'));
				
				$form1->addElement('image', 'submit', '../../images/button.php?i=b70.png&l=Submit');
				$form1->display();
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
    <td align="center"><div align="center"><br>
            <br>
            <img src="software_bar.php?limit=<?= $_GET['limit']; ?>"><br>
            <table  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="25">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDark"><div align="left">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Software Report... </td>
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
                                <td height="25" class="BGAccentDark"><strong>&nbsp;Software<img src="../../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Version</strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Amount</strong></td>
                              </tr>
                              <?php
								$data_sth = $dbh->execute($data_sql, array('s.name','ASC'));
								$num_rows = $data_sth->numRows();	
								
								while($data_sth->fetchInto($DATA)) {
									/* Line counter for alternating line colors */
									$counter++;
									$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									
									/* Display Action options only if owned by user */
									if ($user == $DATA['eid']) {
										$DATA['fullname'] = "";
									} else {
										$user = $DATA['eid'];
									}
								?>
                              <tr <?php pointer($row_color); ?>>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['name'])); ?></td>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['version'])); ?></td>
                                <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $DATA['total']; ?></td>
                              </tr>
                              <?php } // End PO while ?>
                          </table></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td>&nbsp;<span class="GlobalButtonTextDisabled">
                      <?= $num_rows ?> Licenses</span></td>
                  </tr>
                </table></td>
                <td width="50">&nbsp;</td>
                <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="25">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDark"><div align="left">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Software Report... </td>
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
                                <td height="25" class="BGAccentDark"><strong>&nbsp;Software</strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Version</strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Amount<strong><img src="../../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></strong></td>
                              </tr>
                              <?php
								$data_sth = $dbh->execute($data_sql, array('total','DESC'));
								$num_rows = $data_sth->numRows();	
								
								while($data_sth->fetchInto($DATA)) {
									/* Line counter for alternating line colors */
									$counter++;
									$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									
									/* Display Action options only if owned by user */
									if ($user == $DATA['eid']) {
										$DATA['fullname'] = "";
									} else {
										$user = $DATA['eid'];
									}
								?>
                              <tr <?php pointer($row_color); ?>>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['name'])); ?></td>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['version'])); ?></td>
                                <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $DATA['total']; ?></td>
                              </tr>
                              <?php } // End PO while ?>
                          </table></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td>&nbsp;<span class="GlobalButtonTextDisabled">
                      <?= $num_rows ?> Licenses</span></td>
                  </tr>
                </table></td>
              </tr>
            </table>
</div>
        </td>
  </tr>
</table>
<br>
	<br>
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
/* Disconnect from database */
$dbh->disconnect();
?>