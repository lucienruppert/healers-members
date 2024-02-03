<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');
	include_once('elelmiszer_functions.php');

	$id = (int)$_REQUEST["id"];
	
	$newId = addNewConsultation($id);
	$cons = getConsultations($newId);
	
	print json_encode(array('newId' => $newId, 'lastDate' => $cons[0]["CrDate"]));
?>