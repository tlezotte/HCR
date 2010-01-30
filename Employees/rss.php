<?php
/**
 * Request System
 *
 * rss.php generates RSS feed.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
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
 * - Database Connection Information
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


$rss_items = $default['rss_items'] / 2;

/* Getting Submitted PO information */
$query = <<< SQL
	SELECT *, e.eid AS _eid, p.name AS _location, d.name AS _dept, e.status AS _status
	FROM Standards.Employees e
	   LEFT JOIN Standards.Plants p ON p.id=e.Location
	   LEFT JOIN Standards.Department d ON d.id=e.dept
	ORDER BY e.hire DESC
	LIMIT $rss_items
SQL;
$sql = $dbh->prepare($query);
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

/* ------------------ START VARIABLES ----------------------- */
/* Generate at RFC 2822 formatted date */
//$pubDate = date("r");
$filename = $default['rss_file'];
/* ------------------ END VARIABLES ----------------------- */



/* ------------------------------------------ CREATE RSS 2.0 FILE ----------------------------------------- */

//header('Content-Type: text/xml');

$rss  = "<?xml version=\"1.0\"?>\n";
$rss .= "<rss version=\"2.0\">\n";
$rss .= "	<channel>\n";
$rss .= "		<title>your company Employee List</title>\n"; 
$rss .= "		<link>".$default['URL_HOME']."/Employees/index.php</link>\n";
$rss .= "		<description>List of $LABEL transactions using the $default[title1]</description>\n";
$rss .= "		<pubDate>$pubDate</pubDate>\n";
$rss .= "		<copyright>2007 your company</copyright>\n";
$rss .= "		<webMaster>webmaster@".$default['email_domain']."</webMaster>\n";
$rss .= "		<category>$default[title1]</category>\n";
$rss .= "		<image>\n";
$rss .= "			<title>your company</title>\n";
$rss .= "			<url>$default[rss_image]</url>\n";
$rss .= "			<width>150</width>\n";
$rss .= "			<height>50</height>\n";
$rss .= "			<link>http://intranet.yourcompany.com/</link>\n";
$rss .= "		</image>\n";

$sth = $dbh->execute($sql);
while($sth->fetchInto($DATA)) {
	$employmentType = ($DATA['I_D'] == 'Direct') ? 'Direct' : 'Contract';
	$employmentStatus = ($DATA['_status'] == 0) ? 'Current' : '<span style=\'color: #FF0000;\'>Inactive</span>';
	
	$rss .= "		<item>\n";
	$rss .= "			<title>" . caps($DATA['fst'] . " " . $DATA['lst']) . "</title>\n";	
	$rss .= "			<link>" . $default['URL_HOME'] . "/Employees/index.php?eid=" . $DATA['eid'] . "</link>\n";
	$rss .= "			<author>" . $default['title1'] . "</author>\n";
	$rss .= "			<description><![CDATA[Employee ID: <strong>" . $DATA['_eid'] . "</strong><br>
	                                          Department: <strong>" . caps($DATA['_dept']) . "</strong><br>
											  Location: <strong>" . caps($DATA['_location']) . "</strong><br>
											  Employment Status: <strong>" . $employmentStatus . "</strong><br>
											  Employment Type: <strong>" . $employmentType . "</strong><br>]]></description>\n";
	$rss .= "			<category>" . caps($DATA['_dept']) . "</category>\n";
	$rss .= "			<category>" . caps($DATA['_location']) . "</category>\n";
	$rss .= "			<category>" . $employmentStatus . "</category>\n";
	$rss .= "			<category>" . $employmentType . "</category>\n";
	$rss .= "			<pubDate>" . $DATA['hire'] . "</pubDate>\n";		
	$rss .= "		</item>\n";	
}

$rss .= "	</channel>\n";
$rss .= "</rss>\n";
/* ------------------------------------------ CREATE RSS 2.0 FILE ----------------------------------------- */

if ($debug) {
	echo "RSS_ITEMS: ".$rss_items."<br>";
	echo "DEFAULT: ".$default['rss_items']."<br>";
	echo "QUERY: <br>".$submitted_query."<br>";
	echo "FILENAME: ".$filename."<br>";
	echo "RSS: <BR>".$rss;
	exit;
}

/* ------------------ START RSS.XML FILE ----------------------- */
// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {
	// Open $filename for writing
   if (!$handle = fopen($filename, 'w')) {
		$_SESSION['error'] = "Cannot open file ($filename)";
		
		header("Location: ../error.php");
        exit;
   }
   // Write $rss to our opened file.
   if (fwrite($handle, $rss) === FALSE) {
		$_SESSION['error'] = "Cannot write to file ($filename)";
		
		header("Location: ../error.php");   
        exit;
   }
   //echo "Success, wrote ($somecontent) to file ($filename)";
   fclose($handle);
} else {
	$_SESSION['error'] = "The file $filename is not writable";
	
	header("Location: ../error.php");   
    exit;
}
/* ------------------ END RSS.XML FILE ----------------------- */

/* Forward user to list.php after RSS file is created */
header("Location: list.php?action=my&access=0");

/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>