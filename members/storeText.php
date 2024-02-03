<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $type = (int)$_REQUEST["type"];
    $fillId = (int)$_REQUEST["fillId"];
    $value = $_REQUEST["value"];
    
	storeAdvisorText($type, $fillId, $value);
	
    print json_encode(true);
?>