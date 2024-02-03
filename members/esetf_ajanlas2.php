<?php
    session_start();
    include_once('functions.php');

    $id = (int)$_REQUEST["id"];
	
	$fill = selectQuestFill($id);
	$answers = selectQuestAnswersForAdmin($id);
	
	$megkereses_oka = array_pop(array_filter($answers, function($obj) {
		return $obj["questionId"] == 141;
	}));
	
	$legfontosabb_tunetek = array_pop(array_filter($answers, function($obj) {
		return $obj["questionId"] == 144;
	}));
	$legfTunNote = getNote($id, 144);
	if(!is_null($legfTunNote)){
		$legfontosabb_tunetek["answer"] = iconv("UTF-8", "Windows-1250", $legfTunNote);
	}
	
	$note = str_replace("\n", "<br />", iconv("UTF-8", "Windows-1250", getNote($id, 21)));
	
	$rootCauses = getRootCauses($id);
	
	$rcArray = array();
	foreach($rootCauses as $rootCause){
		if($rootCause["IsChecked"] === "1")
			$rcArray[] = "<li>" . $rootCause["Name"] . "</li>";
	}
	foreach($rootCauses as $rootCause){
		if($rootCause["Other"] != null)
			$rcArray[] = "<li>" . iconv("UTF-8", "Windows-1250", nl2br($rootCause["Other"])) . "</li>";
	}
	$rootCausesStr = "<ul>" . implode("", $rcArray) . "</ul>";

	$solutionSteps = getSolutionSteps($id);
	
	usort($solutionSteps, function($a, $b) {
		return $a['AnswerId'] <=> $b['AnswerId'];
	});
	
	$ssArray = array();
	foreach($solutionSteps as $solutionStep){
		if($solutionStep["IsChecked"] === "1")
			$ssArray[] = "<li>" . $solutionStep["Name"] . "</li>";
	}
	$solutionStepsStr = "<ul>" . implode("", $ssArray) . "</ul>";

	$eatMoreList = getEatMore($id);
	
	usort($eatMoreList, function($a, $b) {
		return $a['AnswerId'] <=> $b['AnswerId'];
	});
	
	$emArray = array();
	foreach($eatMoreList as $eatMore){
		if($eatMore["IsChecked"] === "1")
			$emArray[] = "<li>" . $eatMore["Name"] . "</li>";
	}
	$eatMoreStr = "<ul>" . implode("", $emArray) . "</ul>";

?>
<html>
<head>
    <style>
        body{
            font-family: Roboto !important;
            margin: 50px;
        }
        div.userName{
            margin-top: 20px;
            margin-bottom: 20px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            font-weight: bold;
            font-size: 16pt;
            text-align: center;
        }
        div.datum{
            margin-top: 20px;
            margin-bottom: 20px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            font-size: 16pt;
            text-align: center;
        }
        div.fooldal1{
            font-size: 30pt !important;
            margin-top: 40px;
        }
        div.fooldal2a{
          text-align: justify !important;
          text-justify: inter-word !important;
        }
        span.upperCase{
            font-weight: bold !important;
        }
        @media print {
            div.acstipus, div.hortipus, div.testatipus, div.eatingHabits, div.tovabbi, div.pagebreak {page-break-before: always;}
        }
        h1, h2, h3, h4, h5, h6{
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
		div.divCommon{
			font-size: 16px;
            line-height: 1.5;
			padding-bottom: 10px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: justify;
		}
		div.divTitle{
			font-weight: bold;
			padding-top: 30px;
		}
        div.divHeader{
			width: 25%;
			text-align: left;
			margin-top: 30px;
		}
	</style>
</head>
<body>
<?php
include_once('proLogos.php');
print "<div class='fooldal1' align='center'>Személyre szabott ajánlások";
print "</div>";
print "<div class='userName'>" . htmlentities($fill["userName"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1") . "</div>";
print "<div class='datum'>" . date("Y.m.d.") . "</div>";

if($megkereses_oka){
	print "<div class='divCommon divTitle'>{$megkereses_oka["question"]}</div>";
	print "<div class='divCommon'>\"{$megkereses_oka["answer"]}\"</div>";
}

if($legfontosabb_tunetek){
	print "<div class='divCommon divTitle'>{$legfontosabb_tunetek["question"]}</div>";
	print "<div class='divCommon'>\"{$legfontosabb_tunetek["answer"]}\"</div>";
}

?>
</body>
</html>

<?php

function showBodyContent($url, $repDict = null)
{
    $d = new DOMDocument;
    $mock = new DOMDocument;
    $contents = file_get_contents($url);
    if($repDict != null){
        foreach($repDict as $key => $value){
            $contents = str_replace($key, $value, $contents);
        }
    }
    $d->loadHTML($contents);
    $body = $d->getElementsByTagName('body')->item(0);
    foreach ($body->childNodes as $child){
        $mock->appendChild($mock->importNode($child, true));
    }
    $imgList = $mock->getElementsByTagName('img');
    for($i = 0; $i < $imgList->length; $i++) {
        $img = $imgList->item($i);
        $img->setAttribute('src', dirname($url) . '/' . $img->getAttribute('src'));
    }
    print $mock->saveHTML();
}
?>