<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');
	include_once('elelmiszer_functions.php');

    $id = (int)$_REQUEST["id"];
    $col = $_REQUEST["col"];
    $value = (int)$_REQUEST["value"];
	$isChecked = ((int)$_REQUEST["isChecked"] === 1);
    
	$valtozo = storeChange($id, $col, $value, $isChecked);
	
    print json_encode($valtozo || true);
?>