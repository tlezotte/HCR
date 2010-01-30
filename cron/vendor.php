<?php
/**
 * Request System
 *
 * vendor.php get vendor data from AS/400.
 *
 * @version 1.5
 * @link https://hr.yourcompany.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
 * @filesource
 */
 

/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - ODBC Database Connection
 */
require('../Connections/connODBC.php');
/**
 * - Database Connection
 */
require('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/**
 * - $_Get Information
 */
switch ($_GET['t']) {
	/* ---- FULL BACKUP AND INSTALL ---- */
	case 'full':
		if ($_GET['b'] != 'off') {
			$full_path = $default['UPLOAD'].'/'.$default['database'].'_'.date("Ymd").'.sql';
			/* ----- Dump Current state of database ----- */
			system('/usr/bin/mysqldump --databases '.$default['database'].' --tables Vendor -l -h '.$default['server'].' -u '.$default['username'].' -p'.$default['password'].' > '.$full_path);
			system('/bin/gzip ' . $full_path);
		}

		/**
		 * - Clean out current local Vendor DB
		 */	
		$dbh->query("DELETE FROM Vendor");
					
		/**
		 * - Getting Data from AS/400
		 */							
		//$sql="SELECT * FROM VEND FETCH FIRST 10 ROWS ONLY";	
		$sql="SELECT * FROM VEND";									// Get all rows from VEND
		$rs=odbc_exec($conn,$sql);
		if (!$rs) { exit(0); }										// Connection Failed
		   
		   
		/**
		 * - Process each record
		 */
		while(odbc_fetch_row($rs)) {
			$vendor_data = "";										// Reset variable
			for($i=1;$i<=odbc_num_fields($rs);$i++) { 
			   $field = odbc_result($rs,$i);  						// Getting each FIELD from ROW
			   $vendor_data .= "'".$field."',";						// Prepping SQL data
			 }
			$vendor_data2 = preg_replace("/\,$/", "", $vendor_data);				// Remove last comma from SQL data
			$vendor_sql = "INSERT INTO Vendor VALUES (" . $vendor_data2 . ")";
			$dbh->query($vendor_sql);
		}
	break;
	/* ---- HOURLY VENDOR INSTALL ---- */
	case 'hourly':
	
	break;
}


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
/**
 * - Disconnect from ODBC database
 */
odbc_close($conn);


/**
 * - Disconnect from ODBC database
 */
if ($_GET['html'] == 'on') {
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit();
}
?>
