<!-- <center class="V9"><br />{L_POWERED_BY} <a class="psf" href="http://phpicalendar.net/">PHP iCalendar {VERSION}</a><br /> -->
<!-- switch rss_valid on -->
<!-- <p>
<a style="color:gray" href="http://feeds.archive.org/validator/check?url={FOOTER_CHECK}">
<img src="{BASE}images/valid-rss.png" alt="[Valid RSS]" title="Validate my RSS feed" width="88" height="31" border="1" vspace="3" /></a>
</p> -->
<!-- switch rss_valid off -->
<!-- switch rss_powered on -->
<!-- {L_THIS_SITE_IS} <a class="psf" href="{BASE}rss/index.php?cal={CAL}&amp;getdate={GETDATE}">RSS-Enabled</a><br /> -->
<!-- switch rss_powered off -->
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
                <td width="50%" nowrap><span class="Copyright"><img src="/Common/images/spacer.gif" width="15" height="5" border="0" align="absmiddle">
<?php 
if ($_SERVER['HTTPS'] == 'on') { 
	
	$certificate = "<b>Organization:</b> ". $_SERVER['SSL_SERVER_S_DN_O'] ."<br>" .
				   "<b>Location:</b> ". $_SERVER['SSL_SERVER_S_DN_L'] ."<br>" .
				   "<b>State:</b> ". $_SERVER['SSL_SERVER_S_DN_ST'] ."<br>" .
				   "<b>Email:</b> " . $_SERVER['SSL_SERVER_S_DN_Email'] . "<br><br>" .
				   "<b>Version:</b> ". $_SERVER['SSL_SERVER_M_VERSION'] ." - ". $_SERVER['SSL_CIPHER'] ."<br>" .
				   "<b>Created:</b> ". $_SERVER['SSL_SERVER_V_START'] ."<br>" .
				   "<b>Expires:</b> ". $_SERVER['SSL_SERVER_V_END'] ."<br>";
?>
<a href="javascript:void(0);" onmouseover="return overlib('<?= $certificate; ?>', CAPTION, '', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();"><img src="/Common/images/lock.gif" width="13" height="15" border="0" align="texttop"></a>&nbsp;&nbsp;
<?php } ?>
Copyright &copy; 2006, Cadence Innovation. All rights reserved.</span></td>
                <td width="50%"><div id="noPrint" align="right"></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td colspan="2">
          </td>
        </tr>
      </tbody>
  </table>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>  
<!--Page generated in {GENERATED1} seconds.<br />
Template generated in {GENERATED2} seconds.-->
</center>
</body>
</html>
