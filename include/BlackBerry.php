<?php
/**
 * - Forward BlackBerry users to BlackBerry version
 */
if (eregi("BlackBerry", $_SERVER['HTTP_USER_AGENT'])) {
	$forward = ereg_replace('/go/Request/','/go/Request/BlackBerry/',$_SERVER['REQUEST_URI']);
	header("Location: ".$forward);
	exit();
}
?>