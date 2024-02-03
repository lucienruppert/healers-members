<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $noteType = (int)$_REQUEST["noteType"];
    $fillId = (int)$_REQUEST["fillId"];
    
	$note = getNote($fillId, $noteType);

	// ha override típusú a noteType, vagyis a felhasználó által beírt értéket írták felül note-tal (ilyenkor a noteType == questionId)
	if(is_null($note) && in_array($noteType, array(144))){
		$answers = selectQuestAnswersForAdmin($fillId);
		$answer = array_pop(array_filter($answers, function($obj) {
			return $obj["questionId"] == $_REQUEST["noteType"];
		}));		
		
		if(!is_null($answer)){
			$note = iconv("Windows-1250", "UTF-8", $answer["answer"]);
			//$note = $answer["answer"];		
		}		
	}
    print json_encode($note);
?>