<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once('functions.php');

if(!$userObject){
    include_once('index.php');
    exit;
}

if($userObject['status'] != 9 && $userObject['status'] != 4){
    print "<script>alert('Nincs jogosultságod a lap megtekintéséhez!');</script>";
    include_once('index.php');
    exit;
}

$id = $_GET["id"];
$currentRecord = getCurrentLearningQuestion($id);
$exercises = getLearningQuestions();

$subCat = array();
$subCat["0"] = array();
$subCat["1"] = array();
$subCat["2"] = array();
$subCat["3"] = array();
$subCat["4"] = array();

if(is_array($currentRecord['exercises'])){
    for($i = 0; $i < count($exercises); $i++){
        $_current = $exercises[$i];

        for($j = 0; $j <= 4; $j++){
            if(is_array($currentRecord['exercises'][(string)$j]) && in_array($_current['sub_ID'], $currentRecord['exercises'][(string)$j])){
                $subCat[(string)$j][] = $_current['sub_name'];
            }
        }
    }
}

function getListContent($cat, $subCat){
    $str = "<ol>";
    for($i = 0; $i < count($subCat[$cat]); $i++){
        $str .= "\n<li>" . $subCat[$cat][$i] . "</li>";
    }
    $str .= "</ol>";

    return $str;
}

?>

<html>
<head>
    <meta http-equiv="content-type" content="text-html; charset=<?php print $CHARSET; ?>">
    <style>
        div.fejlec{
            width:100%;
            font-size:24pt;
            font-weight:bold;
            margin-bottom: 40px;
            text-align:center;
        }

        div.listaFejlec{
            width:100%;
            font-size:14pt;
            font-weight:bold;
            margin-top: 30px;
            margin-bottom: 3px;
            text-align:center;
        }

        div.listaContainer{
            width:100%;
        }

        div.lista{
            font-size:12pt;
            width:300px;
            margin:auto;
        }
        
        li{
            white-space: nowrap;
            margin: 3px;
        }
    </style>
</head>
<body>
<div class="fejlec"><?php print $currentRecord["sub_name"]; ?></div>

<div class="listaFejlec">TÜNETEK</div>
<div class="listaContainer"><div class="lista"><?php print getListContent("1", $subCat); ?></div></div>

<div class="listaFejlec">LABOR</div>
<div class="listaContainer"><div class="lista"><?php print getListContent("2", $subCat); ?></div></div>

<div class="listaFejlec">OKOK</div>
<div class="listaContainer"><div class="lista"><?php print getListContent("3", $subCat); ?></div></div>

<div class="listaFejlec">BALANCE</div>
<div class="listaContainer"><div class="lista"><?php print getListContent("4", $subCat); ?></div></div>

<div class="listaFejlec">......</div>
<div class="listaContainer"><div class="lista"><?php print getListContent("0", $subCat); ?></div></div>

</body>
</html>