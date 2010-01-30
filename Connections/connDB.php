<?php
/* Database Settings */ 
$default['server'] = "a2.yourcompany.com";
$default['username'] = "hcr";
$default['password'] = "human";
$default['database'] = "HCR";

/* -- PEAR DB connection -- */
require_once 'DB.php';
$dsn = "mysql://".$default['username'].":".$default['password']."@".$default['server']."/".$default['database'];
$dbh =& DB::connect($dsn);
$dbh->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh)) { die ($dbh->getMessage()); }  

/* -- PEAR MDB2 connection -- */
/*require_once 'MDB2.php';
$dsn = "mysql://".$default['username'].":".$default['password']."@".$default['server']."/".$default['database'];
$mdb2 =& MDB2::factory($dsn);
$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC); 
if (PEAR::isError($mdb2)) { die($mdb2->getMessage()); }*/

/* -- PHP MySQL connection -- */
//$cnStandards = mysql_pconnect($std['server'], $std['username'], $std['password']) or trigger_error(mysql_error(),E_USER_ERROR); 
?>