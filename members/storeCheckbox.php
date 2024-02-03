<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $type = (int)$_REQUEST["type"];
    $fillId = (int)$_REQUEST["fillId"];
    $value = (int)$_REQUEST["value"];
	$isChecked = (int)$_REQUEST["isChecked"];
    
	storeAdvisorCheckbox($type, $fillId, $value, $isChecked === 1);
	
    print json_encode(true);
?>