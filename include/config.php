<?php
/**
 * Purachase Request System
 *
 * config.php all the commen PHP, Javscript and HTML.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/* Debug settings */
$default['debug'] = "off";
$default['debug_ip'] = "172.16.81.228";															//Only system to view debug information
$default['debug_email'] = "tlezotte@yourcompany.com";
$default['debug_capture'] = 'on';
/* Title Display */
$default['title0'] = "Welcome to the";
$default['title1'] = "Human Capital Request";
$default['title2'] = "";
/* Set Application Location */
$default['fs_home'] = "/http/hr/yourcompany/com/443";											//Website Home directory
$default['url_home'] = "/go/HCR";																	//Application Home
$default['FS_HOME'] = $default['fs_home'].$default['url_home'];										//Filesystem Location
$default['URL_HOME'] = "https://".$_SERVER['HTTP_HOST'].$default['url_home'];						//Web Location
$default['files_store'] = "files";																	//Upload directory
$default['upload'] = "/Administration/" . $default['files_store'];									//Upload file system
$default['UPLOAD'] = $default['FS_HOME'] . "/" . $default['upload'];								//Upload file system
$default['URL_UPLOAD'] = $default['URL_HOME'] . $default['upload'];
/* Maintenance Mode */
$default['maintenance'] = "off";
$default['bb_maintenance'] = "off";																	//Maintencance status for BlackBerry
$default['maintenance_time'] = "4:30pm";															//Return Time
/* Web Notify Mode */
$default['notify_web'] = "off";
$default['email_domain'] = "yourcompany.com";													//Email Domain
$default['email_from'] = "hcr@".$default['email_domain'];											//Email from address
/* RSS Feed */
$default['rss'] = "off";
$default['rss_file'] = "rss.xml";																	//RSS file name
$default['rss_items'] = "40";																		//Number of RSS items
$default['rss_image'] = "https://hr.yourcompany.com/Common/images/companyRSS.gif";			//RSS image
/* SMTP server */
$default['smtp'] = "mail.yourcompany.com";													//SMTP Mail server
$default['smtp_port'] = "25";																		//SMTP port
/* PDF file */
$default['pdf_logo'] = "https://hr.yourcompany.com/Common/images/Company.jpg";
$viewable_rows = "25";																				// Pagination Value
/* Request access variables */
$request_email = "tlezotte@yourcompany.com";
$request_name = "Thomas LeZotte";
/* Turn on or off Page Loading */
$default['pageloading'] = "off";
/* Minumn Wage */
$default['min_wage'] = '7.15';

/* Google APIs */
$default['google_analytics'] = "UA-2838165-2";														// Google Analytics

$default['newGroup'] = <<< END_OF_BCC
$mail->AddBCC('tlezotte@yourcompany.com');
$mail->AddBCC('lczuchaj@yourcompany.com');
$mail->AddBCC('jhollister@yourcompany.com');
$mail->AddBCC('sreinhold@yourcompany.com');
$mail->AddBCC('sabrinalow@yourcompany.com');
$mail->AddBCC('gjarvis@yourcompany.com');
$mail->AddBCC('sfasbend@yourcompany.com');
$mail->AddBCC('kmccaffr@yourcompany.com');
END_OF_BCC;

$default['disableGroup'] = <<< END_OF_BCC
$mail->AddBCC('jhollister@yourcompany.com');
$mail->AddBCC('sreinhold@yourcompany.com');
$mail->AddBCC('sabrinalow@yourcompany.com');
$mail->AddBCC('gjarvis@yourcompany.com');
$mail->AddBCC('sfasbend@yourcompany.com');
$mail->AddBCC('kmccaffr@yourcompany.com');
END_OF_BCC;


/* ------------------ START VARIABLES ----------------------- */
$blank = "";	/* Place holder */
$CHANGE = "<a href=\"javascript:void(0);\" ".
		  "onmouseover=\"return overlib('CHANGES TO THIS FIELD WILL BE SAVED', BORDER, 2, FGCOLOR, '#FDF500', BGCOLOR, '#000000', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
          "<img src=\"/Common/images/form-update.gif\" border=\"0\" align=\"absmiddle\"></a>";
$NOCHANGE = "<a href=\"javascript:void(0);\" ".
		    "onmouseover=\"return overlib('CHANGES TO THIS FIELD WILL <b>NOT</b> BE SAVED', BORDER, 2, FGCOLOR, '#FF6600', BGCOLOR, '#FF0000', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
            "<img src=\"/Common/images/red-no.gif\" border=\"0\" align=\"absmiddle\"></a>";		  
$WARNING = "<a href=\"javascript:void(0);\" ".
		   "onmouseover=\"return overlib('REQUIRED FIELD', BORDER, 2, FGCOLOR, '#DAAA17', BGCOLOR, '#AC120F', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
           "<img src=\"/Common/images/required.gif\" border=\"0\" align=\"absmiddle\"></a>";
$ADDITION = "<a href=\"javascript:void(0);\" ".
		   "onmouseover=\"return overlib('ADD PDF FILES TO CABINET', BORDER, 2, FGCOLOR, '#89C08F', BGCOLOR, '#3B8C4C', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
           "<img src=\"/Common/images/folder-plus.gif\" border=\"0\" align=\"absmiddle\"></a>";		   
/* ------------------ END VARIABLES ----------------------- */	


/**
 * - Load Functions
 */
include_once('functions.php');	

/**
 * - Load Language
 */
switch ($_COOKIE['language']) {
	case 'fr':
		include_once($default['FS_HOME'] . '/Language/fr/labels.php');	
	break;
	case 'en':
	default:
		include_once($default['FS_HOME'] . '/Language/en/labels.php');	
	break;
}
?>