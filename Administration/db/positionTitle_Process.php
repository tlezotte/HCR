<?php
/**
 * Request System
 *
 * settings.php display, add and edit system wide variables.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
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
 * - Check User Access
 */
require_once('../../security/check_access.php'); 

/**
 * - Config Information
 */
require_once('../../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');



/* ----- START EDIT VARIABLE ----- */
if ($_POST['action'] == "edit") {
	$min = preg_replace("/,/", "", $_POST['min']);				// Remove commas from min value
	$max = preg_replace("/,/", "", $_POST['max']);				// Remove commas from max value
	$mid = (($max - $min) / 2) + $min;							// Calculate mid value
	$minmid = (($mid - $min) / 2) + $min;						// Calculate minmid value
	$midmax =  (($max - $mid) / 2) + $mid;						// Calculate midmax value

	$sql = "UPDATE Position SET grade='" . $_POST['grade'] . "',
								title_name='" . $_POST['title_name'] . "',
								min='" . number_format($min) . "',
								minmid='" . number_format($minmid) . "',
								mid='" . number_format($mid) . "',
								midmax='" . number_format($midmax) . "',
								max='" . number_format($max) . "',
								ot='" . $_POST['ot'] . "',
								flsa='" . $_POST['flsa'] . "'
					WHERE title_id=" . $_POST['title_id'];
	$dbh->query($sql);																					
											
	/* Record transaction for history */
	History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));	
	
	$message="Your updates have been saved.<br><br>Please click outside this window to continue.";
	$forward = "../../Common/blank.php?gb=reload&message=".$message;
	header('Location: '.$forward);
	exit();										
}
/* ----- END EDIT VARIABLE ----- */

/* ----- START DISABLE VARIABLE ----- */
if ($_POST['action'] == "delete") {

	$sql = "UPDATE Position SET title_status='1' WHERE title_id=" . $_POST['title_id'];
	$dbh->query($sql);																					
											
	/* Record transaction for history */
	History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));	
	
	$message="The current Position Title has been deleted.<br><br>Please click outside this window to continue.";
	$forward = "../../Common/blank.php?gb=reload&message=".$message;
	header('Location: '.$forward);
	exit();												
}
/* ----- END DISABLE VARIABLE ----- */


/* ------------- START FORM DATA --------------------- */
$POSITION = $dbh->getRow("SELECT * 
						  FROM Position 
						  WHERE title_id = " . $_GET['title_id']);

$grades_sq1 = $dbh->prepare("SELECT distinct(grade), min, max
							 FROM Position
							 WHERE title_status='0'
							 ORDER BY (grade+0) ASC, min ASC");
/* ------------- END FORM DATA --------------------- */


/* Setup onLoad javascript program */
$ONLOAD_OPTIONS.="";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
    <title><?= $language['label']['title1']; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 your company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="../../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  </head>

  <body>
           <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
             <br>
             <table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                    
                    <tr>
                      <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="2">
                          <tr>
                            <td height="25" class="label"><vllabel form="Form" validators="grade" errclass="valError">Grade:</vllabel></td>
                            <td height="25" class="padding">
                              <select name="grade" id="grade" onChange="payrate(this.options[this.selectedIndex].value);">
                                <option value="0">Select</option>
                                <?php
								$grades_sth = $dbh->execute($grades_sq1);
								while($grades_sth->fetchInto($DATA)) {
								  $selected = ($DATA['grade'] == $POSITION['grade']) ? selected : $blank;
								  echo "<option value=\"" . $DATA['grade'] . "\" $selected>" . $DATA['grade'] . "</option>";
								}
							  ?>
                              </select>
						    <vlvalidator name="grade" type="compare" control="grade" validtype="string" comparevalue="0" comparecontrol="grade" operator="ne"></td>
                          </tr>
                          <tr>
                            <td height="25" nowrap class="label"><vllabel form="Form" validators="title_name" errclass="valError">Position Title:</vllabel></td>
                            <td class="padding"><input name="title_name" type="text" size="50" maxlength="50" value="<?= $POSITION['title_name']; ?>">
                            <vlvalidator name="title_name" type="required" control="title_name"></td>
                          </tr>
                          <tr>
                            <td height="25"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td class="label">
                                  <vllabel form="Form" validators="min" errclass="valError">Minimum:</vllabel></td>
                                <td align="right">$</td>
                              </tr>
                            </table></td>
                            <td class="padding"><input name="min" id="min" type="text" size="10" maxlength="7" value="<?= $POSITION['min']; ?>">
                              <vlvalidator name="min" type="required" control="min" minlength="5" maxlength="7"></td>
                          </tr>
                          <tr>
                            <td height="25"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td class="label"><vllabel form="Form" validators="max" errclass="valError">Maximum:</vllabel></td>
                                <td align="right">$</td>
                              </tr>
                            </table></td>
                            <td class="padding"><input name="max" id="max" type="text" size="10" maxlength="7" value="<?= $POSITION['max']; ?>">
                              <vlvalidator name="max" type="required" control="max" minlength="5" maxlength="7"></td>
                          </tr>
                          <tr>
                            <td height="25" class="label"><vllabel form="Form" validators="ot" errclass="valError">OT:</vllabel></td>
                            <td class="padding">
                                 <select name="ot" id="ot">
                                  <option value="0">Select One</option>
                                  <option value="E" <?= ($POSITION['ot'] == 'E') ? 'selected' : ''; ?>>Exempt</option>
                                  <option value="ST" <?= ($POSITION['ot'] == 'ST') ? 'selected' : ''; ?>>Straight</option>
                                  <option value="TH" <?= ($POSITION['ot'] == 'TH') ? 'selected' : ''; ?>>Time/Half</option>
                                </select> 
                                <vlvalidator name="ot" type="compare" control="ot" validtype="string" comparevalue="0" comparecontrol="ot" operator="ne">                          
                            </td>
                          </tr>
                          <tr>
                            <td height="25" class="label"><vllabel form="Form" validators="flsa" errclass="valError">FLSA:</vllabel></td>
                            <td class="padding">
                                <select name="flsa" id="flsa">
                                  <option value="0">Select One</option>
                                  <option value="N" <?= ($POSITION['flsa'] == 'N') ? 'selected' : ''; ?>>Non-Exempt</option>
                                  <option value="E" <?= ($POSITION['flsa'] == 'E') ? 'selected' : ''; ?>>Exempt</option>
                                </select>
                                <vlvalidator name="flsa" type="compare" control="flsa" validtype="string" comparevalue="0" comparecontrol="flsa" operator="ne">
                            </td>
                          </tr>
                      </table></td>
                    </tr>
                  </table></td>
              </tr>
              <tr>
                <td height="5"><img src="../../images/spacer.gif" width="5" height="5"></td>
              </tr>
              <tr>
                <td align="right">
				  <input name="title_id" type="hidden" id="title_id" value="<?= $_GET['title_id']; ?>">
				  <input type="hidden" name="action" value="<?= $_GET['action']; ?>">
				  <input name="<?= ucwords($_GET['action']); ?>" type="image" id="<?= ucwords($_GET['action']); ?>" src="../../images/button.php?i=b70.png&l=<?= ucwords($_GET['action']); ?>" border="0">
				  &nbsp;
				</td>
              </tr>
            </table>
		   </form> 
	    <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws.js"></SCRIPT>
        <SCRIPT LANGUAGE="JavaScript" SRC="/Common/Javascript/overlibmws/overlibmws_iframe.js"></SCRIPT>
        <script type="text/javascript">
        function payrate(grade) {
            <?php
                $min_sth = $dbh->execute($grades_sq1);
                while($min_sth->fetchInto($MIN)) {
                  echo "min[" . $MIN['grade'] . "] = \"" . $MIN['min'] . "\"\n";
                }
            ?>	
            <?php
                $max_sth = $dbh->execute($grades_sq1);
                while($max_sth->fetchInto($MAX)) {
                  echo "max[" . $MAX['grade'] . "] = \"" . $MAX['max'] . "\"\n";
                }
            ?>	
            document.getElementById("min").value = min[grade];
            document.getElementById("max").value = max[grade];
        }
        </script>           
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