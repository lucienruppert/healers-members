<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');
	include_once('elelmiszer_functions.php');

	$newUserName = iconv("UTF-8", "Windows-1250", $_REQUEST["userName"]);
	$newConsId = $userObject['ID'];
	
	addOrphanUser($newUserName, $newConsId);
	
	print json_encode(true);
?>