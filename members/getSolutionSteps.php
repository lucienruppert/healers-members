<?php
    session_start();
	header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $fillId = (int)$_REQUEST["fillId"];

	$result = array();
	
	foreach(getSolutionSteps($fillId) as $cause){
		$val = new stdClass;
		$val->Name = iconv("Windows-1250", "UTF-8", $cause["Name"]);
		$val->Value = $cause["Id"];
		$val->IsChecked = $cause["IsChecked"] === "1";
		$result[] = $val;
	}
    print json_encode($result);
?>