<?php
/* ----- CHECK USER LOGIN and ACCESS ----- */
if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') {

} else {
	$_SESSION['error'] = "You are not authorized to access this area";
	
	header("Location: ../error.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset={CHARSET}" />
	<title>{php}echo $language['label']['title1'];{/php}</title>
  	<link rel="stylesheet" type="text/css" href="{DEFAULT_PATH}templates/{TEMPLATE}/default.css" />
	<!-- switch rss_available on -->
	<link rel="alternate" type="application/rss+xml" title="RSS" href="{DEFAULT_PATH}/rss/rss.php?cal={CAL}&amp;rssview={CURRENT_VIEW}">
	<!-- switch rss_available off -->		
	{EVENT_JS}	
	<link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
	<link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
	<link href="../default.css" rel="stylesheet" type="text/css" media="screen">	
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>  
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/prototype/prototype.js"> </script>
	<SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/windows/window.js"></script> 
	<script language="JavaScript" type="text/JavaScript">
	<!--
		function MM_openBrWindow(theURL,winName,features) { //v2.0
		window.open(theURL,winName,features);
	}
	//-->
	</script>
</head>
<body>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    <img src="/Common/images/CadencePrint.gif" alt="Cadence Innovation" name="Print" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../index.php"><img name="Cadence" src="/Common/images/Cadence.gif" width="300" height="50" border="0" alt="Human Capital Request Home"></a></td>
          <td align="right" valign="top">
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><a href="../Employees/index.php" onmouseover="return overlib('Cadence Innovation Employee List', CAPTION, '', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();"><img src="../images/team.gif" width="16" height="18" border="0"></a></td>
	  <td><img src="../images/spacer.gif" width="15" height="18" /></td>
	  <td><a href="../Calendar/month.php" onmouseover="return overlib('Cadence Innovation Start Date Calendar', CAPTION, '', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();"><img src="/Common/images/calendar_a.gif" width="16" height="16" border="0"></a></td>
	  <td><img src="../images/spacer.gif" width="15" height="18" /></td>	  
	  <td><a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/index.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
	  <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/index.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">Help</a></td>
	</tr>
	</table>
</td>
</tr>
<tr>
  <td valign="bottom" align="right" colspan="2">{php}include("../include/rightmenu.php");{/php}</td>
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
		<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="../images/t.gif" width="200" height="5" border="0"></td>
    </tr>
</table></td>

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
				  <div align="right" class="FieldNumberDisabled"><strong><?= $language['label']['welcome']; ?>Welcome <?= $_SESSION['fullname'] ?></strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled" onmouseover="return overlib('Selecting [logout] will Log you out of the Human Capital Request and stop automatic cookie login', CAPTION, '', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">[logout]</a>&nbsp;</div>
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
  <br />
<form name="eventPopupForm" id="eventPopupForm" method="post" action="includes/event.php" style="display: none;">
  <input type="hidden" name="date" id="date" value="" />
  <input type="hidden" name="time" id="time" value="" />
  <input type="hidden" name="uid" id="uid" value="" />
  <input type="hidden" name="cpath" id="cpath" value="" />
  <input type="hidden" name="event_data" id="event_data" value="" />
</form>
