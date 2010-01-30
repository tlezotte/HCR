<?php
	$hr_role = ($_SESSION['hcr_groups'] == 'hr') ? true : false;
	$ex_role = ($_SESSION['hcr_groups'] == 'ex') ? true : false;
	$access1 = ($_SESSION['hcr_access'] == 1) ? true : false;
	$access2 = ($_SESSION['hcr_access'] == 2) ? true : false;
	$access3 = ($_SESSION['hcr_access'] == 3) ? true : false;
?>

<div id="productsandservices" class="yuimenubar yuimenubarnav">
    <div class="bd">
        <ul class="first-of-type">
        	<!----- Start Menu One ----->
            <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="#">Open Position</a>
            <div id="openposition" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/index.php">New Position</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?my=true&access=0">My Open Positions</a></li>
                <?php if ($ex_role) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php">All Open Positions</a></li>
                <?php } ?>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu One ----->
            <?php if ($hr_role OR $ex_role) { ?>
            <!----- Start Menu Two ----->
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Wage Adjustment</a>
            <div id="adjustment" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/_index.php?type=adjustment">New Wage Adjustment</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=adjustment&status=N&my=true">My Wage Adjustments</a></li>
                <?php if ($ex_role) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=adjustment&status=N">All Wage Adjustments</a></li>
                <?php } ?>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu Two ----->  
            <!----- Start Menu Three ----->
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Transfer</a>
            <div id="transfer" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/_index.php?type=transfer">New Transfer</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=transfer&status=N&my=true">My Transfer</a></li>
                <?php if ($ex_role) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=transfer&status=N">All Transfer</a></li>
                <?php } ?>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu Three -----> 
            <!----- Start Menu Four ----->
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Conversion</a>
            <div id="conversion" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/_index.php?type=conversion">New Conversion</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=conversion&status=N&my=true">My Conversion</a></li>
                <?php if ($ex_role) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=conversion&status=N">All Conversion</a></li>
                <?php } ?>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu Four -----> 
            <!----- Start Menu Five ----->
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Promotion</a>
            <div id="promotion" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/_index.php?type=promotion">New Promotion</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=promotion&status=N&my=true">My Promotion</a></li>
                <?php if ($ex_role) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=promotion&status=N">All Promotion</a></li>
                <?php } ?>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu Five ----->  
            <!----- Start Menu Six ----->
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">All</a>
            <div id="all" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=all&status=N&my=true">My HCR</a></li>
                <?php if ($ex_role) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Requests/list.php?type=all&status=N">All HCR</a></li>
                <?php } ?>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu Six ----->
            <?php } ?>                                                                   
            <!----- Start Menu Seven ----->
            <?php if ($access1 OR $access2 OR $access3) { ?>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Administration</a>
            <div id="administration" class="yuimenu">
            <div class="bd">                    
            <ul>
            	<?php if ($access2 AND array_key_exists('id', $_GET)) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#" id="adminPanelMenu">Modify Requisition</a></li>
                <?php } ?>            
            	<?php if ($access3) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#" id="historyPanelMenu">Requisition History</a></li>
                <?php } ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/users.php">User Management</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Database</a>
                <div id="database" class="yuimenu">
                    <div class="bd">
                        <ul class="first-of-type">
                        	<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/db/positionTitle.php">Position Titles</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/db/controller.php">Controllers</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/db/vendors.php">AS/400 Vendors</a></li>
                        </ul>            
                    </div>
                </div>                    
                </li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Utilities</a>
                <div id="utilities" class="yuimenu">
                    <div class="bd">
                        <ul class="first-of-type">
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/notify.php">Notify Users by Email</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/notify_web.php">Notify Users by Webs</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/testemail.php">Send Test Email</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/summary.php">Usage Summary</a></li>     
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/comments.php">Comments</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/updateRSS.php">Update RSS</a></li>                                                   
                        </ul>            
                    </div>
                </div>                    
                </li>                
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/settings.php">Application Settings</a></li>
            </ul>                    
            </div>
            </div>                                        
            </li>
            <?php } else { ?>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">My Account</a>
            <div id="administration" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/user_information.php">Information</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/user_information.php#password">Change Password</a></li>
            </ul>
            </div>
            </div>      
            </li>            
            <?php } ?>
            <!----- End Menu Seven ----->     
        </ul>            
    </div>
</div>
<div id="messageCenter" style="display:none"><div><?= $message; ?></div></div>
<div id='adminPanel' style='display:none'></div>
<div id='historyPanel' style='display:none'></div>
