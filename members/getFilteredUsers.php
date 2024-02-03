<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $txt = $_REQUEST["txt"];
	
	$result = getFilteredNoteFillIds($txt);
	$result2 = selectFilteredQuestAnswers($txt);
    
    print json_encode(array_merge($result, $result2));
?>