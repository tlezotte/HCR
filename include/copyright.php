<?php if ($_SESSION['hcr_access'] == 3) { ?>
<div id="floatingAdminOFF" class="noPrint" style="display:none">
    <?php //StopLoadTimer(); ?>
    <img src="/Common/images/spacer.gif" width="50" height="16" align="absmiddle">
    <?= //onlineCount(); ?>
</div>
<?php } ?>

<div id="floatingFooterOFF" style="padding-bottom:100px">
    <span style="float:left;">
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
    &nbsp;&nbsp;<a href="javascript:void(0);" title="SSL Certificate Information|<?= $certificate; ?>"><img src="/Common/images/lock.gif" width="13" height="15" border="0"></a>
    <?php } ?>
    &copy;2007 your company, LLC</span>
</div>