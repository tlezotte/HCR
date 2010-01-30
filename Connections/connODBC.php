<?php
/* Database Settings */ 
$default['odbc_driver'] = "iSeries Access ODBC Driver";
$default['odbc_system'] = "os400";
$default['odbc_library'] = "company";
$default['odbc_username'] = "LEZOTTET";
$default['odbc_password'] = "ODBC";

/* DSN Settings */
$dsn = "DRIVER=".$default['odbc_driver'].";SYSTEM=".$default['odbc_system'].";DEFAULTLIBRARIES=".$default['odbc_library'];

/* Connect to Database */
$conn=odbc_connect($dsn, $default['odbc_username'], $default['odbc_password']);
if (!$conn) { exit("Connection Failed: " . $conn); }
?>