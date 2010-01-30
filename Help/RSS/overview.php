<?php
/**
 * Request System
 *
 * overview.php explains features of RSS.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
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
 * - Config Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'RSS: Overview', $_SESSION['eid']);



$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $language['label']['title1']; ?>
    </title>
	<SCRIPT LANGUAGE="JavaScript">

	function ClipBoard()
	{
		holdtext.innerText = copytext.innerText;
		Copied = holdtext.createTextRange();
		Copied.execCommand("Copy");
	}
	
	</SCRIPT>
    
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
  <!-- <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/dojo/dojo.js"></SCRIPT> --> 
</head>

  <body <?= $ONLOAD; ?>>
  <table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><p><span class="NavBarInactiveLink"><span class="DarkHeaderSubSub">
        <?= $default['title0']; ?>
        </span><br>
          <span class="DarkHeader">
            <?= $language['label']['title1']; ?>
            's</span><br>
          <span class="DarkHeader">RSS 2.0 Feed</span><br>
      </span></p></td>
    </tr>
    <tr>
      <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="229"><img src="../../images/r_1.gif" width="229" height="88"></td>
            <td background="../../images/r_2.gif">&nbsp;</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center" bgcolor="#f2f2f2"><p><span class="g4"><b>R</b>eally <b>S</b>imple <b>S</b>yndication (RSS)</span> can be understood as a web syndication protocol that is primarily used by news websites and weblogs. RSS allows a web developer to publish content on their website in a format that a computer program can easily understand and digest. This allows users to easily repackage the content on their own websites or blogs, or privately on their own computers.</p>
          <p>RSS simply repackages the content as a list of data items, such as the date of a news story, a summary of the story and a link to it. A program known as an RSS aggregator or feed reader can then check RSS-enabled webpages for the user, and display any updated articles that it finds. This is more convenient than having the user repeatedly visit their favorite news websites, because it makes sure that the reader only sees material that they haven't seen before. Web-based RSS aggregators are also available, offering the user an alternative to using dedicated software, and making the user's feeds available on any computer with Web access.</p>
        <p>Below, Venture
          <?= $language['label']['title1']; ?>
          offers several RSS feeds with headlines, descriptions and links back to the Capital Expenses and Purchase Orders for the full story.<br>
          <br>
        </p></td>
    </tr>
    <tr>
      <td height="30"><a name="feeds"></a></td>
    </tr>
    <tr>
      <td height="30" class="BGAccentDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="25" colspan="2" class="BGAccentDark">&nbsp;&nbsp;<strong>RSS Feeds </strong></td>
          </tr>
          <tr>
            <td width="30" height="25"><div align="center"><a href="http://<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>" <?php help('', 'Copy RSS URL to Clipboard for Mozilla based browsers (Firefox) Right Click on the icon and select <b>Copy Link Location</b>', 'default'); ?>><img src="../../images/livemarks16.gif" width="16" height="16" border="0" align="absmiddle"></a></div></td>
            <td nowrap><a href="javascript:void(0);" <?php help("Click text to copy RSS URL to Clipboard for Microsoft Internet Explorer only", 'default'); ?>>
              <BUTTON class="rss" onClick="ClipBoard();">Purchase Orders</BUTTON>
              </a> <span id="copytext" style="visibility:hidden;">http://
                <?= $default['URL_HOME']; ?>
                /PO/
                <?= $default['rss_file']; ?>
                </span>
              <TEXTAREA name="holdtext" ID="holdtext" STYLE="display:none;"></TEXTAREA></td>
          </tr>
          <tr>
            <td height="25">&nbsp;</td>
            <td><b>Description:</b> A feed of the last
              <?= $default['rss_items']; ?>
              submitted and approved Purchase Order Requests. This feed is updated when ever a transaction takes place.</td>
          </tr>
          <tr>
            <td width="30" height="25"><div align="center"><a href="http://<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>" <?php help('', 'Copy RSS URL to Clipboard for Mozilla based browsers (Firefox) Right Click on the icon and select <b>Copy Link Location</b>', 'default'); ?>><img src="../../images/livemarks16.gif" width="16" height="16" border="0" align="absmiddle"></a></div></td>
            <td nowrap><a href="javascript:void(0);" <?php help("Click text to copy RSS URL to Clipboard for Microsoft Internet Explorer only", 'default'); ?>>
              <BUTTON class="rss" onClick="ClipBoard();">Capital Expenses</BUTTON>
              </a> <span id="copytext" style="visibility:hidden;">http://
                <?= $default['URL_HOME']; ?>
                /CER/
                <?= $default['rss_file']; ?>
                </span>
              <TEXTAREA name="holdtext" ID="holdtext" STYLE="display:none;"></TEXTAREA></td>
          </tr>
          <tr>
            <td height="25">&nbsp;</td>
            <td><b>Description:</b> A feed of the last
              <?= $default['rss_items']; ?>
              submitted and approved Capital Expense Requests. This feed is updated when ever a transaction takes place.</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td height="30"><a name="software"></a></td>
    </tr>
    <tr>
      <td height="30" class="BGAccentDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="25" class="BGAccentDark">&nbsp;<strong>&nbsp;RSS Software</strong> </td>
          </tr>
          <tr>
            <td height="25" class="padding"><a href="http://intranet.yourcompany.com/modules.php?name=Downloads&d_op=getit&lid=26" class="dark">Sharp Reader</a> - SharpReader is an RSS/Atom Aggregator for Windows</td>
          </tr>
          <tr>
            <td height="25" class="padding"><a href="http://intranet.yourcompany.com/modules.php?name=Downloads&d_op=getit&lid=30" class="dark">RSS Bandit</a> - RSS Bandit is an RSS/Atom Aggregator for Windows</td>
          </tr>
          <!--
                <tr>
                  <td height="25" class="padding"><a href="http://intranet.yourcompany.com/modules.php?name=Downloads&d_op=getit&lid=27" class="dark">Blog Navigator</a> - Blog Navigator is a program that makes it easy to read RSS feed from the Internet.</td>
                </tr>-->
          <tr>
            <td height="25" class="padding"><a href="http://intranet.yourcompany.com/modules.php?name=Downloads&d_op=getit&lid=20" class="dark">Thunderbird</a> - is Mozilla's next generation e-mail client with RSS Integration.</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" bgcolor="#CC9966" class="padding">Prior to running SharpReader or RSS Bandit, you will need to install the .NET Framework, version 1.1. If you do not currently have the .NET Framework installed, you can get it at <a href="http://windowsupdate.microsoft.com" target="_blank" class="black"><strong>Windows Update</strong></a>.</td>
    </tr>
  </table>
  </body>
</html>
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