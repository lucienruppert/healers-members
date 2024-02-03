<?php
    session_start();
    include_once('functions.php');

    $id = (int)$_REQUEST["id"];
    $acstipus = $_REQUEST["acstipus"];
    $fill = selectQuestFill($id);
    $answers = selectQuestAnswersForAdmin($id);

    $sex = -1;
    $birth = 0;
    $height = 0;
    $weight = 0;
    $lifestyle = -1;
    $eatingHabits = array();
    $testalkat = -1;
    $testalkat2 = -1;

    foreach($answers as $answer){
        if($answer["questionId"] == 126){
            $sex = $answer["raw_answer"];
        }
        else if($answer["questionId"] == 52){
            $birth = (int)trim($answer["answer"]);
        }
        else if($answer["questionId"] == 88){
            $height = (int)trim($answer["answer"]);
        }
        else if($answer["questionId"] == 90){
            $lifestyle = (int)trim($answer["raw_answer"]);
        }
        else if($answer["questionId"] == 118){
            $eatingHabits = $answer["answer_array"];
        }
        else if($answer["questionId"] == 110){
            if(is_array($answer["answer_array"]) && count($answer["answer_array"]) > 0) {
                reset($answer["answer_array"]);
                $testalkat = (int)key($answer["answer_array"]);
            }
        }
        else if($answer["questionId"] == 112){
            if(is_array($answer["answer_array"]) && count($answer["answer_array"]) > 0) {
                reset($answer["answer_array"]);
                $testalkat2 = (int)key($answer["answer_array"]);
            }
        }
    }

    if($testalkat == 0)
        $hortipus = "agya";
    else if($testalkat == 1)
        $hortipus = "pmir";
    else if($testalkat == 2)
        $hortipus = "petef";
    else if($testalkat == 3)
        $hortipus = "mellves";
    else
        $hortipus = null;

    $calMultipl = 1;
    if($testalkat2 == 0){
        $testatipus = "ekto";
        $calMultipl = 1.1;
    }
    else if($testalkat2 == 1){
        $testatipus = "mezo";
    }
    else if($testalkat2 == 2){
        $testatipus = "endo";
        $calMultipl = 0.9;
    }
    else{
        $testatipus = null;
    }

    //KALÓRIASZÁMOLÁS
    $age = (int)date("Y") - (int)$birth;
    
    $baseCalories = -1;
    $weight = ($height - 100);
    // nõ
    if($sex == 0){
        $weight *= 0.9;
        $baseCalories = round(447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age), 0);
    }
    // férfi
    else if($sex == 1){
        $baseCalories = round(88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age), 0);
    }
    
    $normalCalories = -1;
    if($lifestyle == 0){
        $normalCalories = $baseCalories * 1.1;
    }
    else if($lifestyle == 1){
        $normalCalories = $baseCalories * 1.2 * $calMultipl;
    }
    else if($lifestyle == 2){
        $normalCalories = $baseCalories * 1.3 * $calMultipl;
    }
    else if($lifestyle == 3){
        $normalCalories = $baseCalories * 1.4 * $calMultipl;
    }
    $normalCalories = round($normalCalories, 0);

?>
<html>
<head>
    <style>
        body{
            font-family: Roboto !important;
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
            font-size: 12pt;
            text-align: center;
        }
        div.fooldal1{
            font-size: 16pt !important;
            font-weight: bold !important;
        }
        div.fooldal2a{
          text-align: justify !important;
          text-justify: inter-word !important;
        }
        div.baseCalories, div.normalCalories{
            margin-top: 10px;
            margin-bottom: 10px;
            font-weight: bold;
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
    </style>
</head>
<body>
<?php

print "<div class='fooldal1' align='center'>";
showBodyContent("tp_ajanlas_reszek/1FOOLDAL/FOOLDAL1/FOOLDAL1.html");
print "</div>";

print "<div class='userName'>" . htmlentities($fill["userName"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1") . "</div>";
print "<div class='datum'>" . date("Y.m.d.") . "</div>";

print "<div class='fooldal2a' align='center'>";
showBodyContent("tp_ajanlas_reszek/1FOOLDAL/FOOLDAL2A/FOOLDAL2A.html");
print "</div>";
print "<div class='fooldal2b' align='center'>";
showBodyContent("tp_ajanlas_reszek/1FOOLDAL/FOOLDAL2B/FOOLDAL2B.html");
print "</div>";

print "<div class='baseCalories' align='center'>$baseCalories kcal</div>";

print "<div class='fooldal3' align='center'>";
showBodyContent("tp_ajanlas_reszek/1FOOLDAL/FOOLDAL3/FOOLDAL3.html");
print "</div>";

print "<div class='normalCalories' align='center'>$normalCalories kcal</div>";

print "<div class='fooldal4' align='center'>";
showBodyContent("tp_ajanlas_reszek/1FOOLDAL/FOOLDAL4/FOOLDAL4.html");
print "</div>";

print "<div class='acstipus'>";
$repDict["pagebreak"] = "<div class='pagebreak'></div>";
if($acstipus == "ch"){
    $repDict["beta1"] = round($normalCalories * 0.6 / 4.1);
    $repDict["beta2"] = round($normalCalories * 0.2 / 9.3);
    $repDict["beta3"] = round($normalCalories * 0.2 / 4.2);
    showBodyContent("tp_ajanlas_reszek/2CH/2CH.html", $repDict);
}
else if($acstipus == "balch"){
    $repDict["beta1"] = round($normalCalories * 0.45 / 4.1);
    $repDict["beta2"] = round($normalCalories * 0.30 / 9.3);
    $repDict["beta3"] = round($normalCalories * 0.25 / 4.2);
    showBodyContent("tp_ajanlas_reszek/2BALCH/2BALCH.html", $repDict);
}
else if($acstipus == "bal"){
    $repDict["beta1"] = round($normalCalories * 0.4 / 4.1);
    $repDict["beta2"] = round($normalCalories * 0.35 / 9.3);
    $repDict["beta3"] = round($normalCalories * 0.25 / 4.2);
    showBodyContent("tp_ajanlas_reszek/2BAL/2BAL.html", $repDict);
}
else if($acstipus == "balfpro"){
    $repDict["beta1"] = round($normalCalories * 0.35 / 4.1);
    $repDict["beta2"] = round($normalCalories * 0.35 / 9.3);
    $repDict["beta3"] = round($normalCalories * 0.3 / 4.2);
    showBodyContent("tp_ajanlas_reszek/2BALZSIRFEH/2BALZSIRFEH.html", $repDict);
}
else if($acstipus == "fatpro"){
    $repDict["beta1"] = round($normalCalories * 0.3 / 4.1);
    $repDict["beta2"] = round($normalCalories * 0.4 / 9.3);
    $repDict["beta3"] = round($normalCalories * 0.3 / 4.2);
    showBodyContent("tp_ajanlas_reszek/2ZSIRFEH/2ZSIRFEH.html", $repDict);
}
print "</div>";

print "<div class='hortipus'>";
if($hortipus == "agya")
    showBodyContent("tp_ajanlas_reszek/3AGYALAPI/3AGYALAPI.html");
else if($hortipus == "pmir")
    showBodyContent("tp_ajanlas_reszek/3PAJZSMIRIGY/3PAJZSMIRIGY.html");
else if($hortipus == "petef")
    showBodyContent("tp_ajanlas_reszek/3PETEFESZEK/3PETEFESZEK.html");
else if($hortipus == "mellves")
    showBodyContent("tp_ajanlas_reszek/3MELLEKVESE/3MELLEKVESE.html");
print "</div>";

print "<div class='testatipus'>";
if($testatipus == "ekto")
    showBodyContent("tp_ajanlas_reszek/4EKTOMORF/4EKTOMORF.html");
else if($testatipus == "mezo")
    showBodyContent("tp_ajanlas_reszek/4MEZOMORF/4MEZOMORF.html");
else if($testatipus == "endo")
    showBodyContent("tp_ajanlas_reszek/4ENDOMORF/4ENDOMORF.html");
print "</div>";

reset($eatingHabits);
$firstKey = key($eatingHabits);
if(count($eatingHabits) == 1 && $firstKey != 18 || count($eatingHabits) > 1){
    print "<div class='eatingHabits'>";
    showBodyContent("tp_ajanlas_reszek/5SZOKASOK/0SZ/0SZ.html");
    foreach($eatingHabits as $key => $habit){
        if($key != 18){
            $modKey = $key + 1;
            showBodyContent("tp_ajanlas_reszek/5SZOKASOK/{$modKey}SZ/{$modKey}SZ.html");
        }
    }
    showBodyContent("tp_ajanlas_reszek/5SZOKASOK/20SZ/20SZ.html");
    print "</div>";
}

print "<div class='tovabbi'>";
$repDict = array();
$repDict["pagebreak"] = "<div class='pagebreak'></div>";
showBodyContent("tp_ajanlas_reszek/6TOVABBI/6TOVABBI.html", $repDict);
print "</div>";


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