<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $noteType = (int)$_REQUEST["noteType"];
    $fillId = (int)$_REQUEST["fillId"];
    $note = $_REQUEST["note"];
    
	storeNote($fillId, $noteType, $note);
	
    print json_encode(true);
?>