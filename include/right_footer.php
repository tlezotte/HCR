<span style="float:right;padding-right:20px">
    <?php if ($_SESSION['hcr_groups'] == 'hr' OR $_SESSION['hcr_groups'] == 'ex') { ?>
    <a href="javascript:void(0);" title="Help Center|Chat with the developer" onClick="window.open('<?= $default['url_home']; ?>/Help/chat.php','chat','width=250,height=400')"><img src="/Common/images/chaticon.gif" border="0" align="absmiddle" /></a>    
    <a href="http://embed.grandcentral.com/webcall/acdae7f63e398c5d710d62876207768d" title="Help Center|Emergency Support" rel="gb_page_center[475, 100]"><img src="/Common/images/emergencyicon.gif" border="0" align="absmiddle" /></a>
    <?php } ?>
    <?php if ($default['rss'] == "on") { ?>
    <a href="/go/HCR/Help/RSS/overview.php" title="RSS Information" rel="gb_page_center[600, 500]"><img src="/Common/images/livemarks16.gif" width="16" height="16" border="0" align="absmiddle"></a>
    <?php } ?>    
</span> 