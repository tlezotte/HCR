<?php 
/**
 * Human Capital Request System
 *
 * index.php is Login page.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCE/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @global mixed $default[]
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */


/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('include/BlackBerry.php');

/**
 * - Start Page Loading Timer
 */
include_once('include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('include/config.php'); 


/* ---- Forgot your password? ---- */
if ($_POST['action'] == 'forgot') {
	/* Get requested users information */
	$USER = $dbh->getRow("SELECT fst, lst, email, username, password
						  FROM Standards.Employees
						  WHERE eid = ?",array($_POST['eid']));
	
	/* Send out email message */
	$sendTo = $USER['email'];
	$subject = $default['title1'] . " Notification";

$message_body = <<< END_OF_BODY
An administrator has emailed you your username and password.  This login information<br>
can be used for most applications that are used at your company.<br>
<br>
Your username: <strong>$USER[username]</strong><br>
Your password: <strong>$USER[password]</strong><br>
END_OF_BODY;

	$url = $default['URL_HOME'];
	
	sendGeneric($sendTo, $subject, $message_body, $url);
	
	$message="An email with username and password information has been sent to " . caps($USER['fst']) . " " . caps($USER['lst']) . ".";
}


/* Forward user if they are allready logged in */
if (array_key_exists('username', $_SESSION) AND ! array_key_exists('error', $_SESSION)) {
	header("Location: home.php");
	exit();
}

/* Set Cookie Expiration */
$cookie_days = 30;
$cookie_expire = time()+60*60*24*$cookie_days;


/* ----- Check user login stats,  store that information into session and cookies ----- */
if (isset($_POST['username']) OR isset($_COOKIE['username'])) {
	if (isset($_COOKIE['username'])) {
		/* Set COOKIE variables as SESSION variables */
		$_SESSION['fullname'] = $_COOKIE['fullname'];
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['hcr_access']  = $_COOKIE['hcr_access'];
		$_SESSION['hcr_groups']  = $_COOKIE['hcr_groups'];
		$_SESSION['eid']  = $_COOKIE['eid'];
		$_SESSION['vacation']  = $_COOKIE['vacation'];
		$_SESSION['aprint']  = $_COOKIE['aprint'];
		
		$forward = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : "home.php";		
	} else {
		$login_query = $dbh->prepare("SELECT CONCAT(e.fst,' ',e.lst) AS fullname, e.username, e.password, u.access, u.eid, u.vacation, u.aprint, e.language, u.groups
									  FROM Users u 
									    LEFT JOIN Standards.Employees e ON e.eid=u.eid
									  WHERE e.username like '".$_POST['username']."' 
										AND u.status = '0' 
										AND e.status = '0';");
		$login_exe = $dbh->execute($login_query);
		$num_rows = $login_exe->numRows();
		$login_db = $login_exe->fetchInto($login);
		
		if ( $num_rows == '1' AND $_POST['password'] == $login['password']) {
		  /* Set form variables as session variables */
		  $_SESSION['fullname'] = $login['fullname'];
		  $_SESSION['username'] = $_POST['username'];
		  $_SESSION['hcr_access']  = $login['access'];
		  $_SESSION['hcr_groups']  = $login['groups'];
		  $_SESSION['eid']  = $login['eid'];
		  $_SESSION['vacation']  = $login['vacation'];
		  setcookie(language, $login['language'], $cookie_expire);
		  
		  /* Using SESSION variable set COOKIE variables for 30days */
		  if ($_POST['remember'] == "yes") {
			  setcookie(fullname, $_SESSION['fullname'], $cookie_expire);
			  setcookie(username, $_SESSION['username'], $cookie_expire);
			  setcookie(hcr_access, $_SESSION['hcr_access'], $cookie_expire);
			  setcookie(hcr_groups, $_SESSION['hcr_groups'], $cookie_expire);
			  setcookie(eid, $_SESSION['eid'], $cookie_expire);
			  setcookie(vacation, $_SESSION['vacation'], $cookie_expire);
			  setcookie(aprint, $_SESSION['aprint'], $cookie_expire);
		  }
		  
		  /* Show that user is logged in */
		  $res = $dbh->query("UPDATE Users SET online = '1' WHERE eid = '".$_SESSION['eid']."'");
		  
		  /* ----- Check Vacation status ----- */
		  if (strlen($login['vacation']) == 5 OR strlen($_COOKIE['requst_vacation']) == 5) {
			$forward = 'home.php?v=on';
		  } else {
			$forward = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : "home.php";
		  }
		} else {
			/* Incorrect Username and Password */
			$_SESSION['error'] = "<a href=\"Administration/forgotPassword.php\" title=\"Forgot your Password?\" class=\"white\" rel=\"gb_page[400, 250]\">Username or Password is incorrect<br>Forgot your username or password? Select here.</a>";
			$forward = "index.php"; 
		}
	}
	
	//unset($_SESSION['error']);			//Cleanup errors after proper login
	//unset($_SESSION['redirect']);		//Cleanup errors after proper login		
	
	header("Location: ".$forward); 
	exit();
}

/* Get Purchase Request users */
$employees_sql = "SELECT U.eid, E.fst, E.lst, E.email 
				  FROM Users U, Standards.Employees E
				  WHERE U.eid = E.eid and U.status = '0' and E.status = '0'
				  ORDER BY E.lst ASC"; 
$employees_query = $dbh->prepare($employees_sql);
$employees_sth = $dbh->execute($employees_query);


/* ----- Set message ----- */
if ($default['maintenance'] == 'on') {
	$hotMessage=true;
	$message="The system is down for maintenance<br>Estimated return time is " . $default['maintenance_time'] . "<br>Sorry for the inconvenience<br>";
}
if (array_key_exists('error',$_SESSION)) {
	//$hotMessage=true;
	$message=$_SESSION['error'] . "<br>";
}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title><?= $default['title1']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="imagetoolbar" content="no">
    <meta name="copyright" content="2004 your company" />
    <meta name="author" content="Thomas LeZotte" />
    <link type="text/css" rel="stylesheet" href="default.css" charset="UTF-8">
    <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/christmas/default.css" />
    <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />  

    <!--<link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/reset-fonts-grids/reset-fonts-grids.css" />-->   <!-- CSS Grid -->

	<script type="text/javascript" src="/Common/Javascript/styleswitcher.js"></script>
    
    <script type="text/javascript" src="/Common/Javascript/jquery/jquery-min.js"></script>
</head>

  <body class="yui-skin-sam">
   <div id="doc3" class="yui-t7">
  
    <div id="hd" style="height:50px">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/company.gif" width="300" height="50" border="0"> 
          </div>
          <div class="yui-u"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u">
              <div id="applicationTitle" style="font-weight:bold;font-size:115%;text-align:right">&nbsp;</div>
              <div id="loggedInUser" class="loggedInUser" style="text-align:right">&nbsp;</div>
              <div id="styleSwitcher" style="text-align:right">&nbsp;</div>
          </div>
      </div>		      
    </div>
    
    <div id="bd">
       <div class="yui-g" id="mm">
          <div class="yuimenubar" style="height:26px"></div>
		  <?php if (isset($message)) { ?>
          <div id="messageCenter" style="display:none"><div><?= $message; ?></div></div>
          <?php } ?>
        </div>
       
        <div class="yui-g">
        <div style="padding-top:50px; padding-bottom:50px; text-align:center"> 
            <span class="DarkHeaderSubSub"><?= $language['label']['title0']; ?></span>
            <br>
            <span class="DarkHeader"><?= $language['label']['title1']; ?></span> 
            <br>
            <span class="DarkHeaderSubSub"><?= $language['label']['title2']; ?></span>
        </div>
        <?php if ((is_null($_SESSION['username']) and $default['maintenance'] == 'off') or $_GET['maint'] == 'off') {?>
        <table id="loginBorder" align="center" cellpadding="0" cellspacing="0">
        <tr>
        <td>
        <table border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="300" height="250" align="center" valign="middle">
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="login" id="login" style="margin: 0">
              <table  border="0" align="center">
                <tr>
                  <td><label for="username">
                  <?= $language['label']['username']; ?>:</label></td>
                  <td><input name="username" type="text" id="username" onKeyPress="checkCapsLock( event )" size="25" maxlength="10" autocomplete="off" title="Hint|Username is usually the same as your email address without the @yourcompany.com"></td>
                </tr>
                <tr>
                  <td><label for="password">
                  <?= $language['label']['password']; ?>:</label></td>
                  <td><input name="password" type="password" id="password" onKeyPress="checkCapsLock( event )" size="25" title="Hint|Password is usually your first intial from your first and last name + your five digit employee ID"></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><input name="remember" type="checkbox" id="remember" value="yes">
                          <label for="remember"><a href="#" class="black" title="User Preference|<?= $language['help']['rememberlogin']; ?>"><?= $language['label']['rememberlogin']; ?></a></label></td>
                      <td align="right">
                         <input name="redirect" type="hidden" id="redirect" value="<?= $_SESSION['redirect']; ?>">
                         <input name="login" type="image" id="login" src="images/button.php?i=w70.png&l=Login" border="0"></td>
                    </tr>
                  </table></td>
                </tr>
            </table>
            </form></td>
            <td width="60" align="center" valign="middle" class="vLine"><img src="/Common/images/or.gif" width="37" height="37"></td>
            <td width="300" height="150" align="center" valign="middle">
          <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="admin" id="admin">
          <table border="0" align="center">
          <tr>
            <td height="25" colspan="2">Forgot your password?</td>
          </tr>
          <tr>
          <td><select name="eid" id="eid">
            <option value="0">Select One</option>
            <?php
            while($employees_sth->fetchInto($EMPOLYEES)) {
                print "<option value=\"".$EMPOLYEES['eid']."\" ".$selected.">".caps($EMPOLYEES['lst'].", ".$EMPOLYEES['fst'])."</option>";
            }
            ?>
          </select></td>
          <td align="right">
            <input name="action" type="hidden" id="action" value="forgot">
            <input name="login" type="image" id="login" src="images/button.php?i=w70.png&l=<?= $language['label']['send']; ?>" border="0"></td>
        </tr>
        <tr>
          <td colspan="2" height="35"><!--<table border="0" align="center" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td style="background-color: rgb(227, 227, 227);" height="2" width="200"><img src="/Common/images/spacer.gif" alt="" border="0" height="2" width="200"></td>
              </tr>
            </tbody>
          </table>--></td>
          </tr>
        <tr>
          <td colspan="2" height="25">&nbsp;</td>
        </tr>
        <tr>
          <td height="25">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
        </table>
        </form></td>
          </tr>
        </table>
        </td>
        </tr>
        </table>
        <?php } ?>
        </td>
        </tr>
        </table>
      </div>
   
      <div id="ft" style="padding-top:50px">
         <div class="yui-gb">
            <div class="yui-u first"><?php include($default['FS_HOME'].'/include/copyright.php'); ?></div>
            <div class="yui-u"><!-- FOOTER CENTER AREA -->&nbsp;</div>
            <div class="yui-u" style="text-align:right"><?php include($default['FS_HOME'].'/include/right_footer.php'); ?></div>
         </div>
      </div>
   </div>
    <script>
		var message='<?= $message; ?>';
		var msgClass='<?= $msgClass; ?>';
    </script>

    <script type="text/javascript" src="/Common/Javascript/capslock.js"></script>
    
    <script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.dimensions-min.js"></script>
    <script type="text/javascript" src="/Common/Javascript/jquery/cluetip/jquery.cluetip-min.js"></script>
    
    <script type="text/javascript" src="js/jQdefault.js"></script>

	<?php if (!$debug_page) { ?>   
    <script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript"></script>
    <script type="text/javascript">
    _uacct = "<?= $default['google_analytics']; ?>";
    urchinTracker();
    </script>
	<?php } ?>      
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
