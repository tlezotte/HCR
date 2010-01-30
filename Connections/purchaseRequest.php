<?php
/* Database Settings */ 
$prs['server'] = "a2.yourcompany.com";
$prs['username'] = "req";
$prs['password'] = "req123";
$prs['database'] = "Request";

/* -- PEAR DB connection -- */
require_once 'DB.php';
$dsn_prs = "mysql://".$prs['username'].":".$prs['password']."@".$prs['server']."/".$prs['database'];
$dbh_prs =& DB::connect($dsn_prs);
$dbh_prs->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh_prs)) { die ($dbh_prs->getMessage()); }  

/* -- PEAR MDB2 connection -- */
/*require_once 'MDB2.php';
$dsn_prs = "mysql://".$prs['username'].":".$prs['password']."@".$prs['server']."/".$prs['database'];
$mdb2_prs =& MDB2::factory($dsn_prs);
$mdb2_prs->setFetchMode(MDB2_FETCHMODE_ASSOC); 
if (PEAR::isError($mdb2_prs)) { die($mdb2_prs->getMessage()); }*/
?>