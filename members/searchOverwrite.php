<?php
    session_start();
	header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');
	include_once('elelmiszer_functions.php');

    $term = $_REQUEST["term"];
	
	$eList = getElelmiszerList($term);
	
	$ret = array();

	foreach($eList as $kaja){
		$obj = new stdClass;
		$obj->id = $kaja["Id"];
		$obj->value = iconv("Windows-1250", "UTF-8", $kaja["Nev"]);
		
		$ret[] = $obj;
	}

    print json_encode($ret);
?>