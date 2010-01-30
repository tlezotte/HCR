<?php 
/**
 * Request System
 *
 * users.php list all users.
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
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../include/BlackBerry.php');
 
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
require_once('../security/check_access1.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ----- START ADD USER ----- */
switch ($_POST['action']) {
	/* ---------- ADD USER ---------- */
	case "add":
		$sql="INSERT into Users (eid, online) VALUES('".$_POST['addUser']."', '00000000000000')";
		$dbh->query($sql);
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));
	break;
	/* ---------- EMAIL ACCESS REQUEST FORM ---------- */
	case "requestform":
			$url = "http://".$_SERVER['SERVER_NAME']."/register";
			$sendTo = $_POST['email']."@".$default['email_domain'];
			
			require("phpmailer/class.phpmailer.php");
		
			$mail = new PHPMailer();
			
			$mail->From     = $default['email_from'];
			$mail->FromName = $default['title1'];
			$mail->Host     = $default['smtp'];
			$mail->Mailer   = "smtp";
			$mail->AddAddress($sendTo);
			$mail->Subject = $default['title1'].": Access Request";

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<p><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></p>
<br>
Please select the link listed below to display the Access Request Form.<br>
<br>
URL: <a href="$url">$url</a><br>
</body>
</html>
END_OF_HTML;

			$mail->Body = $htmlBody;
			$mail->isHTML(true);
			
			if(!$mail->Send())
			{
			   echo "Message was not sent";
			   echo "Mailer Error: " . $mail->ErrorInfo;
			}
			
			// Clear all addresses and attachments for next loop
			$mail->ClearAddresses();
			$mail->ClearAttachments();	
	break;
}
/* ----- END ADD USER ----- */

/* ----- START UPDATE USER ----- */
/*  Update all users privileges  */
if (array_key_exists('reset', $_GET)) {
	if ($_GET['reset'] == 'off') {
		$sql="UPDATE Users
			  SET one='0', two='0', three='0', four='0', five='0'
			  WHERE eid='".$_GET['eid']."'";
	} else {
		$sql="UPDATE Users
			  SET one='1', two='1', three='1', four='1', five='1'
			  WHERE eid='".$_GET['eid']."'";
	}		 
	$dbh->query($sql);

	header("Location: ".$_SERVER['PHP_SELF']);
	exit();
}

/*  Update users privileges  */
if (array_key_exists('action', $_GET)) {
	$sql="UPDATE Users
		  SET $_GET[action]='".$_GET[value]."'
		  WHERE eid='".$_GET['eid']."'";
	$dbh->query($sql);

	/* Record transaction for history */
	History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlspecialchars($sql)));
		
	header("Location: ".$_SERVER['PHP_SELF']);
	exit();
}
/* ----- END UPDATE USER ----- */


/* ------------------ START VARIABLES ----------------------- */
/* --- Pagination Variables --- */
$page_order = (array_key_exists('o', $_GET)) ? $_GET['o'] : "E.lst";								// Order By field
$page_direction = (array_key_exists('d', $_GET)) ? $_GET['d'] : "ASC";								// Order By field direction
$page_rows = $dbh->getRow("SELECT COUNT(E.eid) AS total 
						   FROM Users U, Standards.Employees E
						   WHERE U.eid = E.eid
			    			 AND E.status = '0'");				// Get total number of active Projects
$page_start = (array_key_exists('s', $_GET)) ? $_GET['s'] : "0";									// Page start row

$viewable_rows = ($viewable_rows > $page_rows['total']) ? $page_rows['total'] : $viewable_rows;		// Checks rows with default viewable_rows
$page_next = $page_start + $viewable_rows;															// Set next page
$page_previous = $page_start - $viewable_rows;														// Set previous page
$page_last = $page_rows['total'] - $viewable_rows;													// Set last page
$letter = (array_key_exists('letter', $_GET)) ? $_GET['letter'].'%' : '%';
$limit = (!array_key_exists('display', $_GET)) ? "LIMIT $page_start, $viewable_rows" : $blank;
/* ------------------ END VARIABLES ----------------------- */

/* ----- START DATABASE ACCESS ----- */
$users_sql = "SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, E.phn, U.access, U.requester, U.one, U.two, U.three, U.four, U.five, U.six, U.seven, U.status 
			  FROM Users U, Standards.Employees E 
			  WHERE U.eid = E.eid
			    AND E.status = '0'
				AND E.lst LIKE '$letter'
			  ORDER BY $page_order $page_direction
			  $limit";
$Dbg->addDebug($users_sql,DBGLINE_QUERY,__FILE__,__LINE__);		//Debug SQL
$Dbg->DebugPerf(DBGLINE_QUERY);									//Start debug timer  			  
$users_query = $dbh->prepare($users_sql);
$Dbg->DebugPerf(DBGLINE_QUERY);									//Stop debug timer 		 
$users_sth = $dbh->execute($users_query);
$num_rows = $users_sth->numRows();
					
$employees_sql = "SELECT eid, fst, lst 
				  FROM Standards.Employees 
				  WHERE status = '0'
				  ORDER BY lst";
$Dbg->addDebug($employees_sql,DBGLINE_QUERY,__FILE__,__LINE__);		//Debug SQL
$Dbg->DebugPerf(DBGLINE_QUERY);									//Start debug timer  						  
$employees_query = $dbh->prepare($employees_sql);							
$Dbg->DebugPerf(DBGLINE_QUERY);									//Stop debug timer  

$users2_sql = "SELECT *
			   FROM  Users U
				 INNER JOIN Standards.Employees E ON E.eid=U.eid
			   WHERE E.status = '0'
			   ORDER BY E.lst";
$users2_query = $dbh->prepare($users2_sql);			   
/* ----- END DATABASE ACCESS ----- */


//$ONLOAD_OPTIONS.="prepareForm();MM_preloadImages('../images/previous_button_on.gif','../images/next_button_on.gif')";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
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
	<script type="text/javascript" src="/Common/Javascript/scriptaculous/prototype.js"></script>
	<script type="text/javascript" src="/Common/Javascript/scriptaculous/scriptaculous.js?load=effects"></script>
	
	<script type="text/javascript" src="/Common/Javascript/autoassist/autoassist.js"></script>
	<link href="/Common/Javascript/autoassist/autoassist.css" rel="stylesheet" type="text/css">	  
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="10" valign="top"><br>
            <br>
            <table width="190"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                      <td align="center"><span class="ColorHeaderSubSub">Display Users </span> </td>
                      <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><table  border="0" align="center" cellpadding="5" cellspacing="0">
                          <tr>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=A" class="dark">A</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=B" class="dark">B</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=C" class="dark">C</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=D" class="dark">D</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=E" class="dark">E</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=F" class="dark">F</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=G" class="dark">G</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=H" class="dark">H</a></td>
                          </tr>
                          <tr>
                            <td align="center"><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=I" class="dark">I</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=J" class="dark">J</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=K" class="dark">K</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=L" class="dark">L</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=M" class="dark">M</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=N" class="dark">N</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=O" class="dark">O</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=P" class="dark">P</a></td>
                          </tr>
                          <tr>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=Q" class="dark">Q</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=R" class="dark">R</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=S" class="dark">S</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=T" class="dark">T</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=U" class="dark">U</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=V" class="dark">V</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=W" class="dark">W</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=X" class="dark">X</a></td>
                          </tr>
                          <tr>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=Y" class="dark">Y</a></td>
                            <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=Z" class="dark">Z</a></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td colspan="2"><div align="center"><a href="<?= $_SERVER['PHP_SELF']; ?>?display=all" class="dark"><strong>All</strong></a></div></td>
                          </tr>
                      </table></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                      <td><img src="../images/spacer.gif" width="10" height="10"></td>
                      <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                    </tr>
                </table></td>
              </tr>
            </table>
          <br>
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" onchange="this.form.submit();">
              <table width="191"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                        <td align="center"><span class="ColorHeaderSubSub">Current Position List </span> </td>
                        <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td nowrap><strong>Staffing Manager:</strong> </td>
                      </tr>
                      <tr>
                        <td nowrap>&nbsp;
                            <select name="staffing" id="staffing">
                              <?php
					      $staffing=getPosition('staffing','none');			// Get Staffing Information
						  
						  $users2_sth = $dbh->execute($users2_query);
						  while($users2_sth->fetchInto($USERS2)) {
							$selected = ($staffing['eid'] == $USERS2['eid']) ? selected : $blank;
							print "<option value=\"".$USERS2[eid]."\" ".$selected.">".ucwords(strtolower($USERS2[lst].", ".$USERS2[fst]))."</option>";
						  }
					  ?>
                            </select>
                            <input type="hidden" name="current_staffing" value="<?= $staffing['eid']; ?>"></td>
                      </tr>
                      <tr>
                        <td nowrap><strong>HR Coordinator:</strong></td>
                      </tr>
                      <tr>
                        <td nowrap>&nbsp;
                            <select name="coordinator" id="coordinator">
                              <?php
					      $coordinator=getPosition('coordinator','none');			// Get Coordinator Information
						  
						  $users2_sth = $dbh->execute($users2_query);
						  while($users2_sth->fetchInto($USERS2)) {
							$selected = ($coordinator['eid'] == $USERS2['eid']) ? selected : $blank;
							print "<option value=\"".$USERS2[eid]."\" ".$selected.">".ucwords(strtolower($USERS2[lst].", ".$USERS2[fst]))."</option>";
						  }
					  ?>
                            </select>
                            <input type="hidden" name="current_coordinator" value="<?= $coordinator['eid']; ?>"></td>
                      </tr>
                      <tr>
                        <td nowrap><strong>EID Generator:</strong></td>
                      </tr>
                      <tr>
                        <td nowrap>&nbsp;
                            <select name="generator" id="generator">
                              <?php
					      $generator=getPosition('generator','none');			// Get Generator Information
						  
						  $users2_sth = $dbh->execute($users2_query);
						  while($users2_sth->fetchInto($USERS2)) {
							$selected = ($generator['eid'] == $USERS2['eid']) ? selected : $blank;
							print "<option value=\"".$USERS2[eid]."\" ".$selected.">".ucwords(strtolower($USERS2[lst].", ".$USERS2[fst]))."</option>";
						  }
					  ?>
                            </select>
                            <input name="action" type="hidden" id="action" value="position">
                            <input type="hidden" name="current_generator" value="<?= $generator['eid']; ?>"></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                        <td><img src="../images/spacer.gif" width="10" height="10"></td>
                        <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                      </tr>
                  </table></td>
                </tr>
              </table>
            </form>
          <!--<table width="190"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><span class="ColorHeaderSubSub">Send Request Form </span> </td>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><input name="email2" type="text" id="email2" size="10" maxlength="20">
                              <input name="action2" type="hidden" id="action2" value="requestform"></td>
                          <td width="75"><input name="send2" type="image" id="send2" src="../images/button.php?i=b70.png&l=Send" align="bottom" border="0"></td>
                        </tr>
                      </table>
                  </form></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                  <td><img src="../images/spacer.gif" width="10" height="10"></td>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
        </table>--><!-- #BeginLibraryItem "/Library/online_users.lbi" --><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="190"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
            <td align="center"><span class="ColorHeaderSubSub">Online Users </span> </td>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <?php 
				$online_sql = "SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, U.access, U.address, U.status 
								FROM Users U, Standards.Employees E 
								WHERE U.eid = E.eid
								AND U.online > DATE_SUB(CURRENT_TIMESTAMP(),INTERVAL 5 MINUTE)
								ORDER BY E.lst ASC";
				$online_query = $dbh->prepare($online_sql);		 
				$online_sth = $dbh->execute($online_query);
				$num_online = $online_sth->numRows();
							   
				while($online_sth->fetchInto($USERS)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
					$address = ($USERS['address'] == '11.1.1.111') ? "BlackBerry" : $USERS['address'];
		  ?>
          <tr>
            <td width="20"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle"></td>
            <td><a href="javascript:void();" <?php if ($USERS['username'] != 'tlezotte') { ?> title="User Information|<b>Username:</b> <?= $USERS['username']; ?><br><b>Password:</b> <?= $USERS['password']; ?><br><b>Email:</b> <?= $USERS['email']; ?><BR><B>IP Address:</B> <?= $address; ?>" <?php } ?> class="black">
              <?= caps($USERS['lst'].", ".$USERS['fst']); ?>
            </a></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
            <td><img src="../images/spacer.gif" width="10" height="10"></td>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #EndLibraryItem --><br></td>
        <td valign="top">
		  <br>
		  <?php if ($num_rows == 0) { ?>
            <div align="center" class="DarkHeaderSubSub">No Users Found</div>
          <?php } else { ?>
            <table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td height="30" valign="top"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="From" id="Form" style="margin: 0">
                          <div align="right">
                            <table  border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td valign="top"><input id="ajaxName" name="ajaxName" type="text" size="40" />
                                    <script type="text/javascript">
												Event.observe(window, "load", function() {
													var aa = new AutoAssist("ajaxName", function() {
														return "../Common/employees.php?q=" + this.txtBox.value;
													});
												});
											</script>
                                    <input name="addUser" type="hidden" id="ajaxEID"></td>
                                <td valign="top">&nbsp;
                                    <input name="addUserButton" type="image" id="addUserButton" src="../images/button.php?i=b70.png&l=Add" align="bottom" border="0">
                                    <input name="action" type="hidden" id="action" value="add"></td>
                              </tr>
                            </table>
                          </div>
                      </form></td>
                    </tr>
                    <tr>
                      <td class="BGAccentVeryDark"><div align="left">
                          <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="50%" height="30" class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;User Permissions...</td>
                              <td width="50%"><div align="right">&nbsp;</div></td>
                            </tr>
                          </table>
                      </div></td>
                    </tr>
                    <tr>
                      <td class="BGAccentVeryDarkBorder"><table  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                <tr class="BGAccentDark">
                                  <td width="200" height="25"><strong>&nbsp;Name&nbsp;</strong></td>
                                  <td><strong>&nbsp;Request&nbsp;</strong></td>
                                  <td width="50" align="center"><strong>&nbsp;Requesting&nbsp; </strong></td>
                                  <td width="50" align="center"><strong>&nbsp;&nbsp;HR&nbsp;&nbsp;</strong></td>
                                  <td width="50" align="center"><strong>&nbsp;Functional&nbsp;</strong></td>
                                  <td width="50" align="center"><strong>&nbsp;Executive&nbsp;</strong></td>
                                  <td width="60" align="center"><strong>&nbsp;Admin&nbsp;</strong></td>
                                  <td width="100" align="center"><strong>&nbsp;Status&nbsp;</strong></td>
                                </tr>
                                <?php 
									while($users_sth->fetchInto($USERS)) {
										/* Line counter for alternating line colors */
										$counter++;
				
										/* ----------------- REQUESTER --------------------- */
										switch ($USERS['requester']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$requester_url = $_SERVER['PHP_SELF']."?action=requester&value=1&eid=".$USERS['eid'];
													$requester_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Requester privileges";
												} else {
													$requester_url = "javascript:void(0);";
													$requester_help = "Requester Status";								
												}
												$requester_class = "no";
												$requester_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$requester_url = $_SERVER['PHP_SELF']."?action=requester&value=0&eid=".$USERS['eid'];
													$requester_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Requester privileges";
												} else {
													$requester_url = "javascript:void(0);";
													$requester_help = "Requester Status";
												}
												$requester_class = "yes";
												$requester_message = "YES";	
											break;
										}
				
										/* ----------------- APPROVER 1 --------------------- */						
										switch ($USERS['one']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$one_url = $_SERVER['PHP_SELF']."?action=one&value=1&eid=".$USERS['eid'];
													$one_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Requesting Leader privileges";
												} else {
													$one_url = "javascript:void(0);";
													$one_help = "Requesting Leader Status";								
												}
												$one_class = "no";
												$one_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$one_url = $_SERVER['PHP_SELF']."?action=one&value=0&eid=".$USERS['eid'];
													$one_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Requesting Leader privileges";
												} else {
													$one_url = "javascript:void(0);";
													$one_help = "Requesting Leader Status";
												}
												$one_class = "yes";
												$one_message = "YES";	
											break;
										}
				
										/* ----------------- APPROVER 2 --------------------- */
										switch ($USERS['two']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$two_url = $_SERVER['PHP_SELF']."?action=two&value=1&eid=".$USERS['eid'];
													$two_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." HR Manager/Director privileges";
												} else {
													$two_url = "javascript:void(0);";
													$two_help = "HR Manager/Director Status";								
												}
												$two_class = "no";
												$two_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$two_url = $_SERVER['PHP_SELF']."?action=two&value=0&eid=".$USERS['eid'];
													$two_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." HR Manager/Director privileges";
												} else {
													$two_url = "javascript:void(0);";
													$two_help = "HR Manager/Director Status";
												}
												$two_class = "yes";
												$two_message = "YES";	
											break;
										}
				
										/* ----------------- APPROVER 3 --------------------- */
										switch ($USERS['three']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$three_url = $_SERVER['PHP_SELF']."?action=three&value=1&eid=".$USERS['eid'];
													$three_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Department Leader privileges";
												} else {
													$three_url = "javascript:void(0);";
													$three_help = "Department Leader Status";								
												}
												$three_class = "no";
												$three_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$three_url = $_SERVER['PHP_SELF']."?action=three&value=0&eid=".$USERS['eid'];
													$three_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Department Leader privileges";
												} else {
													$three_url = "javascript:void(0);";
													$three_help = "Department Leader Status";
												}
												$three_class = "yes";
												$three_message = "YES";	
											break;
										}
				
										/* ----------------- APPROVER 4 --------------------- */
										switch ($USERS['four']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$four_url = $_SERVER['PHP_SELF']."?action=four&value=1&eid=".$USERS['eid'];
													$four_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 4 privileges";
												} else {
													$four_url = "javascript:void(0);";
													$four_help = "Approver 4 Status";								
												}
												$four_class = "no";
												$four_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$four_url = $_SERVER['PHP_SELF']."?action=four&value=0&eid=".$USERS['eid'];
													$four_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 4 privileges";
												} else {
													$four_url = "javascript:void(0);";
													$four_help = "Approver 4 Status";
												}
												$four_class = "yes";
												$four_message = "YES";	
											break;
										}
										/* ----------------- APPROVER 5 --------------------- */
										switch ($USERS['five']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$five_url = $_SERVER['PHP_SELF']."?action=five&value=1&eid=".$USERS['eid'];
													$five_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 5 privileges";
												} else {
													$five_url = "javascript:void(0);";
													$five_help = "Approver 5 Status";								
												}
												$five_class = "no";
												$five_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$five_url = $_SERVER['PHP_SELF']."?action=five&value=0&eid=".$USERS['eid'];
													$five_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 5 privileges";
												} else {
													$five_url = "javascript:void(0);";
													$five_help = "Approver 5 Status";
												}
												$five_class = "yes";
												$five_message = "YES";	
											break;
										}
				
										/* ----------------- REQUESTER --------------------- */
										switch ($USERS['staffing']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$staffing_url = $_SERVER['PHP_SELF']."?action=staffing&value=1&eid=".$USERS['eid'];
													$staffing_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Staffing privileges";
												} else {
													$staffing_url = "javascript:void(0);";
													$staffing_help = "Staffing Status";								
												}
												$staffing_class = "no";
												$staffing_message = "NO";							
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$staffing_url = $_SERVER['PHP_SELF']."?action=staffing&value=0&eid=".$USERS['eid'];
													$staffing_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Staffing privileges";
												} else {
													$staffing_url = "javascript:void(0);";
													$staffing_help = "Staffing Status";
												}
												$staffing_class = "yes";
												$staffing_message = "YES";	
											break;
										}
																																																	
										/* -- Setup and Calculate the Administration access -- */
										switch ($USERS['access']) {
											case '1':
												$level1_icon="wait1.gif";
												$level2_icon="wait2off.gif";
												$level3_icon="wait3off.gif";
												if ($_SESSION['hcr_access'] >= 2) {
													$level1_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
													$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
													$level3_url=$_SERVER['PHP_SELF']."?action=access&value=3&eid=".$USERS['eid'];
													$level1_help="Revoke level 1 administration access";	
													$level2_help="Grant level 2 administration access";
													$level3_help="Grant level 3 administration access";																										
												} else {
													$level1_url="javascript:void(0);";
													$level2_url="javascript:void(0);";
													$level3_url="javascript:void(0);";	
													$level1_help="Level 1 administration access";	
													$level2_help="Level 2 administration access";
													$level3_help="Level 3 administration access";																	
												}				
												break;
											case '2':
												$level1_icon="wait1off.gif";
												$level2_icon="wait2.gif";
												$level3_icon="wait3off.gif";
												if ($_SESSION['hcr_access'] == 3) {
													$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
													$level2_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
													$level3_url=$_SERVER['PHP_SELF']."?action=access&value=3&eid=".$USERS['eid'];
													$level1_help="Grant level 1 administration access";	
													$level2_help="Revoke level 2 administration access";
													$level3_help="Grant level 3 administration access";																										
												} else {
													$level1_url="javascript:void(0);";
													$level2_url="javascript:void(0);";
													$level3_url="javascript:void(0);";	
													$level1_help="Level 1 administration access";	
													$level2_help="Level 2 administration access";
													$level3_help="Level 3 administration access";																	
												}													
												break;
											case '3':
												$level1_icon="wait1off.gif";
												$level2_icon="wait2off.gif";
												$level3_icon="wait3.gif";
												if ($_SESSION['hcr_access'] == 3) {
													$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
													$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
													$level3_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
													$level1_help="Grant level 1 administration access";	
													$level2_help="Grant level 2 administration access";
													$level3_help="Revoke level 3 administration access";									
												} else {
													$level1_url="javascript:void(0);";
													$level2_url="javascript:void(0);";
													$level3_url="javascript:void(0);";	
													$level1_help="Level 1 administration access";	
													$level2_help="Level 2 administration access";
													$level3_help="Level 3 administration access";																									
												}																	
												break;
											case '4':
												$level1_icon="wait1off.gif";
												$level2_icon="wait2off.gif";
												$level3_icon="wait3.gif";
												if ($_SESSION['hcr_access'] == 3) {
													$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
													$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
													$level3_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
													$level1_help="Grant level 1 administration access";	
													$level2_help="Grant level 2 administration access";
													$level3_help="Revoke level 3 administration access";									
												} else {
													$level1_url="javascript:void(0);";
													$level2_url="javascript:void(0);";
													$level3_url="javascript:void(0);";	
													$level1_help="Level 1 administration access";	
													$level2_help="Level 2 administration access";
													$level3_help="Level 3 administration access";																									
												}																	
												break;
											case '5':
												$level1_icon="wait1off.gif";
												$level2_icon="wait2off.gif";
												$level3_icon="wait3.gif";
												if ($_SESSION['hcr_access'] == 3) {
													$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
													$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
													$level3_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
													$level1_help="Grant level 1 administration access";	
													$level2_help="Grant level 2 administration access";
													$level3_help="Revoke level 3 administration access";									
												} else {
													$level1_url="javascript:void(0);";
													$level2_url="javascript:void(0);";
													$level3_url="javascript:void(0);";	
													$level1_help="Level 1 administration access";	
													$level2_help="Level 2 administration access";
													$level3_help="Level 3 administration access";																									
												}																	
												break;																
											default:
												$level1_icon="wait1off.gif";
												$level2_icon="wait2off.gif";
												$level3_icon="wait3off.gif";
												if ($_SESSION['hcr_access'] == 3) {
													$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
													$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
													$level3_url=$_SERVER['PHP_SELF']."?action=access&value=3&eid=".$USERS['eid'];
													$level1_help="Grant level 1 administration access";	
													$level2_help="Grant level 2 administration access";
													$level3_help="Grant level 3 administration access";																	
												} else {
													$level1_url="javascript:void(0);";
													$level2_url="javascript:void(0);";
													$level3_url="javascript:void(0);";	
													$level1_help="Level 1 administration access";	
													$level2_help="Level 2 administration access";
													$level3_help="Level 3 administration access";								
												}																
												break;							
										}
				
										/* ----------------- ACCESS STATUS --------------------- */						
										switch ($USERS['status']) {
											case '0':
												if ($_SESSION['hcr_access'] >= 2) {
													$status_url = $_SERVER['PHP_SELF']."?action=status&value=1&eid=".$USERS['eid'];
													$status_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." access";
												} else {
													$status_url = "javascript:void(0);";
													$status_help = "Access Status";
												}
												$status_class = "yes";
												$status_message = "ACTIVE";	
											break;
											case '1':
												if ($_SESSION['hcr_access'] >= 2) {
													$status_url = $_SERVER['PHP_SELF']."?action=status&value=0&eid=".$USERS['eid'];
													$status_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." access";
												} else {
													$status_url = "javascript:void(0);";
													$status_help = "Access Status";								
												}
												$status_class = "no";
												$status_message = "DISABLE";
											break;
										}
								  ?>
                                <tr>
                                  <td nowrap><a href="forgotPassword.php?action=process&eid=<?= $USERS['eid']; ?>" title="User Actions|Pick email icon to send '.caps($USERS['fst']." ".$USERS['lst']).' their username and password."><img src="../images/resend_email.gif" width="19" height="16" border="0" align="absmiddle"></a>
                                      <?php if ($_SESSION['hcr_access'] == '3') { ?>
                                      <a href="<?= $_SERVER['PHP_SELF']; ?>?reset=off&eid=<?= $USERS['eid']; ?>" title="User Actions|Switch OFF Approver and Staffing privileges."><img src="../images/1downarrow.gif" width="16" height="16" border="0" align="absmiddle"></a><a href="<?= $_SERVER['PHP_SELF']; ?>?reset=on&eid=<?= $USERS['eid']; ?>" title="User Actions|Switch ON Approver and Staffing privileges."><img src="../images/1uparrow.gif" width="16" height="16" border="0" align="absmiddle"></a>
                                      <?php } ?>
                                      <a href="#" onClick="return GB_show('<?= caps($USERS['fst']." ".$USERS['lst']); ?>s Permissions', '<?= $default['URL_HOME']; ?>/Administration/user_details.php?eid=<?= $USERS['eid']; ?>', 460, 415)" <?php if ($USERS['username'] != 'tlezotte') { ?> title="User Information|Username: <?= $USERS['username']; ?><br>Password: <?= $USERS['password']; ?><br>EID: <?= $USERS['eid']; ?><br>Email: <?= $USERS['email']; ?><br>Phone: <?= $USERS['phn']; ?>"<?php } ?> class="black">
                                      <?= caps($USERS['lst'].", ".$USERS['fst']); ?>
                                    </a></td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><a href="<?= $requester_url; ?>" class="<?= $requester_class; ?>" title="User Actions|<?= $requester_help; ?>">
                                    <?= $requester_message; ?>
                                  </a> </td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><a href="<?= $one_url; ?>" class="<?= $one_class; ?>" title="User Actions|<?= $one_help; ?>">
                                    <?= $one_message; ?>
                                  </a> </td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><a href="<?= $two_url; ?>" class="<?= $two_class; ?>" title="User Actions|<?= $two_help; ?>">
                                    <?= $two_message; ?>
                                  </a> </td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><a href="<?= $four_url; ?>" class="<?= $four_class; ?>" title="User Actions|<?= $four_help; ?>">
                                    <?= $four_message; ?>
                                  </a> </td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><a href="<?= $five_url; ?>" class="<?= $five_class; ?>" title="User Actions|<?= $five_help; ?>">
                                    <?= $five_message; ?>
                                  </a></td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><?php if ($USERS['username'] != 'tlezotte') { ?>
                                      <a href="<?= $level1_url; ?>" title="User Actions|<?= $level1_help; ?>"><img src="../images/<?= $level1_icon; ?>" width="19" height="16" border="0"></a><a href="<?= $level2_url; ?>" title="User Actions|<?= $level2_help; ?>"><img src="../images/<?= $level2_icon; ?>" width="19" height="16" border="0"></a><a href="<?= $level3_url; ?>" title="User Actions|<?= $level3_help; ?>"><img src="../images/<?= $level3_icon; ?>" width="19" height="16" border="0"></a>
                                      <?php } ?>
                                  </td>
                                  <td align="center" bgcolor="#<?= $row_color; ?>"><?php if ($USERS['username'] != 'tlezotte') { ?>
                                      <a href="<?= $status_url; ?>" class="<?= $status_class; ?>" title="User Actions|<?= $status_help; ?>">
                                      <?= $status_message; ?>
                                      </a>
                                    <?php } ?></td>
                                </tr>
                                <?php } ?>
                            </table></td>
                          </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td><?php if ($_GET['display'] != 'all') { 
			  		if ($num_rows >= $viewable_rows) {
			  ?>
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="50%" height="25">&nbsp;<span class="GlobalButtonTextDisabled">
                                <?= ($page_start+1)."-".($page_next)." out of ".$page_rows['total']; ?>
                                Users</span></td>
                              <td width="50%" align="right" valign="bottom"><table border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <?php if ($page_previous > 0) { ?>
                                    <td width="22"><a href="<?= $_SERVER['PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=1"; ?>" title="Navigation|Return the the beginning"><img src="../images/previous_button.gif" name="beginning" width="19" height="19" border="0" id="beginning" ></a></td>
                                    <td width="100"><a href="<?= $_SERVER['PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_previous; ?>" class="pagination" title="Navigation|Jump to the previous page"><img src="../images/previous_button.gif" name="previous" width="19" height="19" border="0" align="top" id="previous">PREVIOUS</a></td>
                                    <?php } ?>
                                    <td width="100">&nbsp;</td>
                                    <?php if ($page_rows['total'] > $page_next) { ?>
                                    <td width="65" align="right"><a href="<?= $_SERVER['PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_next; ?>" class="pagination" title="Navigation|Jump to the next page">NEXT<img src="../images/next_button.gif" name="next" width="19" height="19" border="0" align="top" id="Image1"></a></td>
                                    <td width="22" align="right"><a href="<?= $_SERVER['PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_last; ?>" title="Navigation|Jump to the last page"><img src="../images/next_button.gif" name="end" width="19" height="19" border="0" id="end""></a></td>
                                    <?php } ?>
                                  </tr>
                              </table></td>
                            </tr>
                          </table>
                        <!-- #EndLibraryItem -->
                          <?php } else {?>
                          <span class="GlobalButtonTextDisabled">&nbsp;
                            <?= $num_rows ?>
                            Users</span>
                          <?php } ?>
                          <?php } else { ?>
                          <span class="GlobalButtonTextDisabled">&nbsp;
                            <?= $num_rows ?>
                            Users</span>
                          <?php } ?>
                      </td>
                    </tr>
                </table></td>
              </tr>
            </table>
          <?php } ?></td>
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