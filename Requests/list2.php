<?php
/**
 * @link http://www.yourcompany.com/
 * @author	Thomas LeZotte (tom@lezotte.net)
 */


//include('include/functions.php');
/**
 * - Forward BlackBerry users to BlackBerry version
 */
//require_once('../include/BlackBerry.php');

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
 * - Config Information
 */
require_once('../include/config.php'); 
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnMain.dwt.php" codeOutsideHTMLIsLocked="false" -->
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
    <link type="text/css" href="/Common/Javascript/yahoo/fonts/fonts-min.css" rel="stylesheet">
	<script src="/Common/Javascript/yahoo/utilities/utilities.js" type="text/javascript"></script>
    <script src="/Common/Javascript/yahoo/datasource/datasource-beta.js" type="text/javascript"></script>
    <script src="/Common/Javascript/yahoo/datatable/datatable-beta.js" type="text/javascript"></script>
    <link type="text/css" href="/Common/Javascript/yahoo/assets/skins/sam/datatable.css" rel="stylesheet">    
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
             
       <div class="yui-g"><!-- InstanceBeginEditable name="main" -->
	<?php 
      if ($_SESSION['hcr_access'] == '3') {
        //include('../Administration/include/detail.php');
      } 
    ?>	    
    <table border="0" align="center">
          <tr>
            <td colspan="2">
          <div align="right">
                <form action="" method="get" style="margin: 0">
                  <table>
                    <tr>
                      <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                      <td class="currentnew" <?php help('', 'Select the Request Type to filter the List.', 'default'); ?>>Type</td>
                      <?php } ?>
                      <td class="currentnew" <?php help('', 'Select the Status of a Request to filter the List.', 'default'); ?>>Status</td>
                      <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                      <td rowspan="2"><img src="/Common/images/spacer.gif" width="15" height="5" /></td>
                      <td class="currentnew" <?php help('', 'View only My Requests.', 'default'); ?>>My</td>
                      <?php } ?>
                    </tr>
                    <tr>
                      <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                      <td><select name="type" id="type" onChange="this.form.submit();" class="listoptions">
                        <option value="new" <?= ($_GET['type'] == 'new') ? selected : $blank; ?>>Open Position</option>
                        <option value="adjustment" <?= ($_GET['type'] == 'adjustment') ? selected : $blank; ?>>Adjustment</option>
                        <option value="transfer" <?= ($_GET['type'] == 'transfer') ? selected : $blank; ?>>Transfer</option>
                        <option value="conversion" <?= ($_GET['type'] == 'conversion') ? selected : $blank; ?>>Conversion</option>
                        <option value="promotion" <?= ($_GET['type'] == 'promotion') ? selected : $blank; ?>>Promotion</option>
                        <option value="all" <?= ($_GET['type'] == 'all') ? selected : $blank; ?>>All</option>
                      </select></td>
                      <?php } ?>
                      <td><select name="status" id="status" onChange="this.form.submit();" class="listoptions">
                        <option value="N" <?= ($_GET['status'] == 'N') ? selected : $blank; ?>>New</option>
                        <option value="A" <?= ($_GET['status'] == 'A') ? selected : $blank; ?>>Approved</option>
                        <option value="D" <?= ($_GET['status'] == 'D') ? selected : $blank; ?>>Denied</option>
                        <option value="C" <?= ($_GET['status'] == 'C') ? selected : $blank; ?>>Canceled</option>
                      </select></td>
                      <?php if ($_SESSION['hcr_groups'] == 'ex' OR $_SESSION['hcr_groups'] == 'hr') { ?>
                      <td><input name="my" type="checkbox" id="my" value="true" <?= ($_GET['my'] == 'true') ? checked : $blank; ?> onChange="this.form.submit();" /></td>
                      <?php } ?>
                    </tr>
                  </table>
                 
                  <div id="dataTable"></div>
                  
                    <script type="text/javascript">
                    YAHOO.util.Event.addListener(window, "load", function() {
                        YAHOO.example.XHR_XML = new function() {
                            this.formatActions = function(elCell, oRecord, oColumn, sData) {
                                elCell.innerHTML = "<a href='detail.php?id=" + oRecord.getData("id") + "' title='Detailed View' class='dark'><img src='/Common/images/detail.gif' align='absmiddle' border='0'></a> " + oRecord.getData("id");
                            };
                            
                            this.formatRequestLevel = function(elCell, oRecord, oColumn, sData) {
                                elCell.innerHTML = "<img src='/Common/images/" + oRecord.getData("level") + ".gif' title='Waiting for " + oRecord.getData("requester") + " (" + oRecord.getData("level") + ")' />";
                            };	
    
            
                            var myColumnDefs = [
                                {key:"id", label:"ID", sortable:true, formatter:this.formatActions},
                                {key:"level", label:"", sortable:true, formatter:this.formatRequestLevel},
                                {key:"title", label:"Position Title", sortable:true},
                                {key:"requester", label:"Requester", sortable:true},
                                {key:"request_date", label:"Date", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},							
                                {key:"request_type", label:"Type", sortable:true}
                            ];
                            
                            var searchType = "<?= (isset($_GET['type'])) ? urlencode(trim($_GET['type'])) : "new"; ?>";
                            var searchStatus = "<?= (isset($_GET['status'])) ? urlencode(trim($_GET['status'])) : "N"; ?>";
                            var searchMy = "<?= (isset($_GET['my'])) ? urlencode(trim($_GET['my'])) : false; ?>";
                            var searchURL = "type=" + searchType + "&status=" + searchStatus + "&my=" + searchMy;
                                    
                            var myConfigs = {
                                initialRequest:searchURL,
                                sortedBy:{key:"id",dir:"desc"},
                                rowSingleSelect:false,
                                paginated:false,
                                paginator:{
                                    containers:null,
                                    currentPage:1,
                                    dropdownOptions:[25,50,100],
                                    pageLinks:0,
                                    rowsPerPage:50
                                }
                            };
                            
                            this.myDataSource = new YAHOO.util.DataSource("../data/requests.php?");
                            this.myDataSource.connMethodPost = false;
                            this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_XML;
                            this.myDataSource.responseSchema = {
                                resultNode: "request",
                                fields: [{key:"id", parser:YAHOO.util.DataSource.parseNumber},
                                              "level",
                                              "title",
                                              "requester",
                                         {key:"request_date", parser:YAHOO.util.DataSource.parseDate},
                                              "request_type"]
                            };
                    
                            this.myDataTable = new YAHOO.widget.DataTable("dataTable", myColumnDefs, this.myDataSource, myConfigs);					
                        };
                    });
                    
                    //YAHOO.namespace("tooltip.container");
                    //YAHOO.tooltip.container.tt1 = new YAHOO.widget.Tooltip("tt1", { context:"emergency", text:"Emergency Support" });
                    </script>                          
            </form>
              </div>
              </td>
          </tr>
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