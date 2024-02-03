<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $id = (int)$_REQUEST["id"];
    $type = (int)$_REQUEST["type"];
    $data = getTProfilServerData($id, $type);
    
    print json_encode($data);
?>