<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_cnStandards = "a2.yourcompany.com";
$database_cnStandards = "Standards";
$username_cnStandards = "standard2";
$password_cnStandards = "rp4std2";
$cnStandards = mysql_pconnect($hostname_cnStandards, $username_cnStandards, $password_cnStandards) or trigger_error(mysql_error(),E_USER_ERROR); 
?>