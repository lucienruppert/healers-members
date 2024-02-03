<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $answerId = (int)$_REQUEST["answerId"];
    $isHighlighted = $_REQUEST["isHighlighted"] == "true";
	
	$isHighlighted = ChangeHighlighted($answerId, $isHighlighted);
    
    print json_encode(array(
        'isHighlighted' => $isHighlighted
    ));
?>