<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $id = (int)$_REQUEST["id"];
    setFillEvaluated($id);

    print json_encode(true);
?>