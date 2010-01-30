<?php


/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');



$sql = "UPDATE Authorization 
					 SET app3yn='yes', 
						 app3Com='', 
						app4='39675',app5='39675',app6='39534',app7='99998',app8='40000',level='app5'
					 WHERE request_id = 206";				 
$res = $dbh->query($sql);

if (DB::isError($res)) {        // Check the result object in case there
    die($res->getMessage());    // was an error, and handle it here.
}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
