<?php
/* Database Settings */ 
$jt['database'] = "JobTracking";
$jt['server'] = "a2.yourcompany.com";
$jt['username'] = "tracking";
$jt['password'] = "jobjob";

/* -- PEAR DB connection -- */
require_once 'DB.php';
$dsn_jobTracking = "mysql://".$jt['username'].":".$jt['password']."@".$jt['server']."/".$jt['database'];
$dbh_jobTracking = DB::connect($dsn_jobTracking);
$dbh_jobTracking->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh_jobTracking)) { die ($dbh_jobTracking->getMessage()); } 

/* -- PEAR MDB2 connection -- */
/*require_once 'MDB2.php';
$dsn_jobTracking = "mysql://".$jt['username'].":".$jt['password']."@".$jt['server']."/".$jt['database'];
$mdb2_jobTracking =& MDB2::factory($dsn_jobTracking);
$mdb2_jobTracking->setFetchMode(MDB2_FETCHMODE_ASSOC); 
if (PEAR::isError($mdb2_jobTracking)) { die($mdb2_jobTracking->getMessage()); }*/
?>
