<?php
	$hr_role = ($_SESSION['hcr_groups'] == 'hr') ? true : false;
	$access1 = ($_SESSION['hcr_access'] == 1) ? true : false;
	$access2 = ($_SESSION['hcr_access'] == 2) ? true : false;
	$access3 = ($_SESSION['hcr_access'] == 3) ? true : false;
?>
        
<div style="padding-top:5px; padding-bottom:10px">
  <div id="mainmenu" class="yuimenubar yuimenubarnav">
    <div class="bd">
        <ul class="first-of-type">
            <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="#">Open Position</a></li>
            <?php if ($hr_role) { ?>                    
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Wage Adjustment</a></li>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Transfer</a></li>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Conversion</a></li>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Promotion</a></li>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">All</a></li>
            <?php } ?>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Administration</a></li>
            <?php if ($access3OFF) { ?>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#" title="Administrative Actions">&nbsp;<img src="/Common/images/adminAction2.gif" onclick="new Effect.toggle('adminPanel', 'slide')" width="12" height="12" border="0" align="absmiddle" /></a></li>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#" title="SQL History">&nbsp;<img src="/Common/images/adminSQLAction2.gif"  onclick="new Effect.toggle('debugPanel', 'slide')" width="24" height="12" border="0" align="absmiddle" /></a></li>
            <?php } ?>            
            <?php if ($hr_role) { ?>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="../Employees/index.php" title="Employee List">Employees</a></li>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="../Calendar/index.php" title="Employee Calendar">Calendar</a></li>
            <?php } ?>
      </ul>
    </div>
  </div>
  <div id="messageCenter" <?= ($hotMessage) ? 'class="hotMessage"' : ''; ?> style="display:none"><div><?= $message; ?></div></div>
</div>
