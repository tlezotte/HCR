<?php
/**
 * Employee List
 *
 * user_new.php add a new user.
 *
 * @version 1.5
 * @link http://a2.yourcompany.com/go/Employees/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
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
 * - Database Connection
 */
require_once('../Connections/connStandards.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 



/* ------------- START DATABASE CONNECTIONS --------------------- */
/* ---------- Get employee Information ---------- */
$DATA=$dbh_standards->getRow("SELECT *, e.eid AS _eid, p.name AS _location, d.name AS _dept, e.status AS _status
							  FROM Employees e
							    LEFT JOIN ComDevices c ON c.cell_eid=e.eid
							    LEFT JOIN Plants p ON p.id=e.Location
							    LEFT JOIN Department d ON d.id=e.dept
							  WHERE e.eid=" . $_GET['eid']);
/* ------------- END DATABASE CONNECTIONS --------------------- */


/* --------------- START UPDATE DATABASE ----------------------- */
/* ------------- START UPDATE ------------------- */
if (array_key_exists('update_x', $_POST)) {
	if (array_key_exists('cell_eid', $_POST)) {
		$sql = "UPDATE ComDevices SET cell_number='" . $_POST['cell_number'] . "',
									  cell_model='" . $_POST['cell_model'] . "',
									  cell_billCycle='" . $_POST['cell_billCycle'] . "',
									  cell_comments='" . $_POST['cell_comments'] . "'
				WHERE cell_eid=" . $_POST['cell_eid'];
	} else {
		$sql = "INSERT INTO ComDevices (eid,
										cell_number, 
										cell_model, 
										cell_billCycle, 
										cell_comments)
							    VALUES ('" . $_SESSION['eid'] . "',
										'" . $_POST['cell_number'] . "', 
								        '" . $_POST['cell_model'] . "', 
										'" . $_POST['cell_billCycle'] . "', 
										'" . $_POST['cell_comments'] . "')";
	}
	$dbh_standards->query($sql);

	$message="Cell phone information has been updated.";
	$forward="../Common/blank.php?gb=close&message=".$message;					
	header("Location: ".$forward);						
	exit();		
}
/* ------------- END UPDATE ------------------- */

/* ------------- START DELETE ------------------- */
if (array_key_exists('delete_x', $_POST)) {
	$sql = "DELETE FROM ComDevices WHERE cell_eid=" . $_POST['cell_eid'];
	$dbh_standards->query($sql);
	
	$message="Cell phone information has been deleted.";
	$forward="../Common/blank.php?gb=close&message=".$message;					
	header("Location: ".$forward);						
	exit();	
}
/* ------------- END DELETE ------------------- */
/* --------------- END UPDATE DATABASE ----------------------- */


/* ------------- START VARIABLES --------------------- */
$email = split('@', $DATA['email']);
$status_icon = ($DATA['_status'] == 0) ? approved : notapproved;
$status = ($DATA['_status'] == '0') ? Active : Inactive;

$AddCellPhone = ($_GET['action'] == 'add' AND $_GET['type'] == 'cell') ? true : false;
$EditCellPhone = ($_SESSION['hcr_groups'] == 'hr' OR $AddCellPhone) ? true : false;

$format_phone="(000)000-0000";
/* ------------- END VARIABLES --------------------- */



//$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
    <title><?= $default['title1']; ?>
    </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2006 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/company.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <style type="text/css">
	body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
	}
  </style>
  </head>

  <body>
  <form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
    <table width="410" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="140">First Name: </td>
        <td class="label"><?= caps($DATA['fst']); ?></td>
      </tr>
      <tr>
        <td height="25">Middle Name: </td>
        <td class="label"><?= strtoupper($DATA['mdl']); ?></td>
      </tr>
      <tr>
        <td height="25">Last Name: </td>
        <td class="label"><?= caps($DATA['lst']); ?></td>
      </tr>
      <tr>
        <td height="25">Employee ID: </td>
        <td class="label"><?= $DATA['eid']; ?></td>
      </tr>
      <tr>
        <td height="25">Hire Date:</td>
        <td class="label"><?= $DATA['hire']; ?></td>
      </tr>
      <tr>
        <td height="25">Plant:</td>
        <td class="label"><?= caps($DATA['_location']); ?></td>
      </tr>
      <tr>
        <td height="25">Department:</td>
        <td class="label"><?= caps($DATA['_dept']); ?></td>
      </tr>
      <tr>
        <td height="25" nowrap>Job Description:&nbsp;&nbsp;</td>
        <td class="label"><?= caps($DATA['Job_Description']); ?></td>
      </tr>
      <tr>
        <td height="25">Shift:</td>
        <td class="label"><?= $DATA['shift']; ?></td>
      </tr>
      <tr>
        <td height="25">Email Address:</td>
        <td class="label"><?php if ($status == 'Active') { ?>
            <a href="mailto:<?= $DATA['email']; ?>" class="dark">
              <?= $DATA['email']; ?>
          </a>
            <?php } else { ?>
            <?= $DATA['email']; ?>
            <?php } ?>
        </td>
      </tr>
      <tr>
        <td height="25">Phone:</td>
        <td class="label"><?= $DATA['phn']; ?></td>
      </tr>
      <tr>
        <td height="25">Status:</td>
        <td class="label"><img src="../images/<?= $status_icon; ?>.gif" width="17" height="17" border="0" align="absmiddle"> <?= $status; ?></td>
      </tr>
      <?php if (strlen($DATA['cell_number']) > 0 OR ($AddCellPhone AND $status == 'Active')) { ?>
      <tr>
        <td height="25" colspan="2" class="BGAccentDark"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td><strong><img src="/Common/images/iphone18.gif" width="13" height="18" align="absmiddle">Cell Phone Information</strong> </td>
            <td align="right">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="25">Cell Number:</td>
        <td class="label"><?php 
		  if ($EditCellPhone) {
		  	echo "<input type='text' name='cell_number' size='10' max='10' value='" . $DATA['cell_number'] . "'>";
		  } else {
			echo str_format_number($DATA['cell_number'], $format_phone); 
		  }
		  ?></td>
      </tr>
      <tr>
        <td height="25">Cell Model: </td>
        <td class="label"><?php 
		  if ($EditCellPhone) {
		  	echo "<input type='text' name='cell_model' size='20' max='20' value='" . $DATA['cell_model'] . "'>";
		  } else {
			echo $DATA['cell_model']; 
		  }
		  ?></td>
      </tr>
      <tr>
        <td height="25">Billing Cycle: </td>
        <td class="label"><?php 
		  if ($EditCellPhone) {
		  	echo "<input type='text' name='cell_billCycle' size='10' max='2' value='" . $DATA['cell_billCycle'] . "'>";
		  } else {
			echo $DATA['cell_billCycle']; 
		  }
		  ?></td>
      </tr>
      <tr>
        <td height="25" valign="top">Comments:</td>
        <td class="label"><?php 
		  if ($EditCellPhone) {
		  	echo "<textarea name='cell_comments' cols='20' rows='4' value='" . $DATA['cell_comments'] . "'></textarea>";
		  } else {
			echo $DATA['cell_comments']; 
		  }
		  ?></td>
      </tr>
      <?php if ($EditCellPhone) { ?>
      <tr>
        <td height="25" valign="top">
		<?php if (isset($DATA['cell_eid'])) { ?>
		<input name="cell_eid" type="hidden" id="cell_eid" value="<?= $DATA['cell_eid']; ?>">
		<?php } ?>
		</td>
        <td class="label"><input name="update" type="image" id="update" src="../images/button.php?i=b110.png&l=Update" border="0">
		<?php if (isset($DATA['cell_eid'])) { ?>
        <input name="delete" type="image" id="delete" src="../images/button.php?i=b110.png&l=Delete" border="0">
		<?php } ?>
		</td>
      </tr>
      <?php } ?>
      <?php } ?>
    </table>
  </form>
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
$dbh_standards->disconnect();
?>