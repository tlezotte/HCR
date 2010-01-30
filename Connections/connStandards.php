<?php
/* Database Settings */ 
$std['database'] = "Standards";
$std['server'] = "a2.yourcompany.com";
$std['username'] = "standard2";
$std['password'] = "rp4std2";

/* -- PEAR DB connection -- */
require_once 'DB.php';
$dsn_standards = "mysql://".$std['username'].":".$std['password']."@".$std['server']."/".$std['database'];
$dbh_standards = DB::connect($dsn_standards);
$dbh_standards->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh_standards)) { die ($dbh_standards->getMessage()); } 

/* -- PEAR MDB2 connection -- */
/*$dsn_standards = "mysql://".$default['username'].":".$default['password']."@".$default['server']."/".$default['database'];
$mdb2_standards =& MDB2::factory($dsn_standards);
$mdb2_standards->setFetchMode(MDB2_FETCHMODE_ASSOC); 
if (PEAR::isError($mdb2_standards)) { die($mdb2_standards->getMessage()); }*/

/* -- PHP MySQL connection -- */
$cnStandards = mysql_pconnect($std['server'], $std['username'], $std['password']) or trigger_error(mysql_error(),E_USER_ERROR); 
?>