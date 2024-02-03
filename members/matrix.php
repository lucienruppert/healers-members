<?php

session_start();
include_once('functions.php');
if(!$userObject){
    include_once('index.php');
    exit;
}
if($userObject['status'] != 9 && $userObject['status'] != 4){
    print "<script>alert('Nincs jogosultságod a lap megtekintéséhez!');</script>";
    include_once('index.php');
    exit;
}
if($userObject['status'] != 9){
    $readonly = "readonly";
}
else{
    $readonly = "";
}

$baseImg = 'ph.jpg';

if($_POST['actionType'] == 'store' && $_POST['selectedSubId'] > 0){
    $storeArray = array();
    $storeArray['ID'] = $_POST['selectedSubId'];
    $storeArray['name'] = $_POST['question'];
    $storeArray['concept'] = $_POST['answer'];
    $storeArray['exercise'] = $_POST['answer2'];
    $storeArray['imgName'] = $_POST['imgName'];
    $storeArray['imgPath'] = $_POST['imgPath'];
    $storeArray['updti'] = 'NOW()';
    $storeArray['exercises'] = $_POST['exercise'];
    $storeArray['subCategory'] = $_POST['subCategory'];
    updateLearningQuestion($storeArray);
}
else if($_POST['actionType'] == 'delete' && $_POST['selectedSubId'] > 0){
    deleteLearningQuestion($_POST['selectedSubId']);
    $_POST['selectedSubId'] = null;
}
else if($_POST['actionType'] == "newDefaultExercise"){
    createNewDefaultExercise();
}

if($_POST['imgPath'] == '' || $_POST['imgPath'] == '.'){
    $plusPath = '';
}
else{
    $plusPath = '/' . $_POST['imgPath'];
}

$exercises = getLearningQuestions();
$imgPathArray = getDirectoryNamesFromDirectory('phpics');
$imgArray = getFileNamesFromDirectory("phpics{$plusPath}");

if(is_array($imgArray)){
    natcasesort($imgArray);
}

if($_POST['selectedSubId'] > 0){
    $currentRecord = getCurrentLearningQuestion($_POST['selectedSubId']);
    for($i = 0; $i < count($currentRecord['exercises'][(string)(int)$_POST['subCategory']]); $i++){
        $selectedExercise[$currentRecord['exercises'][(string)(int)$_POST['subCategory']][$i]] = 'checked';
    }
}

print "<html>";
print "<head>
<title>Keresõ</title>
   <style>
	html, body, table {
		height:100%;
	}   
	body{
		margin: 0px;
	}   
	#mainForm{
		height: calc(100% - 65px);		
	}
	input[type=checkbox] {
        /* All browsers except webkit*/
        transform: scale(1.5);

        /* Webkit browsers*/
        -webkit-transform: scale(1.5);
      }
    .rdTextSubCategory{
        color:black;
        font-size:15px;
    }
    td.subCatRadioTd{
        cursor:pointer;
        background-color:lightgrey;
        width:100px;
        padding: 10px;
    }
    
    td.tdSubCatLinks{
        padding-left:30px;
    }
</style>

<meta http-equiv=\"content-type\" content=\"text-html; charset=$CHARSET\">
<link rel=stylesheet type='text/css' href='baseStyle2.css'>
<!--<script src='https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>-->
<script src='jquery-1.10.2.min.js' type='text/javascript'></script>";
?>

<script>
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function(searchString, position){
          position = position || 0;
          return this.substr(position, searchString.length) === searchString;
      };
    }

    $(document).ready(function(){
        $("#txtSearchExercise").keyup(function () {
            if (typeof searchTimeout !== "undefined"){
                clearTimeout(searchTimeout);
            }
            searchTimeout = setTimeout(narrowList, 300);
        });

        $("#btnNewExercise").click(function () {
            $('<form method="post"><input type="hidden" name="actionType" value="newDefaultExercise"></form>').appendTo('body').submit();
        });
        
        $(".subCatRadioTd").click(function () {
            $(this).find("input[type='radio']").trigger("click");
        });
        $(".subCatRadioTd input[type='radio']").click(function (e) {
            e.stopPropagation();
        });
        $(".subCatRadioTd input[type='radio']").change(function (e) {
            $("#mainForm #selectedSubId").val("<?php print $_POST['selectedSubId']; ?>");
            document.forms[0].submit();
        });
    });

    function narrowList(){
        var value = $("#txtSearchExercise").val();
        $(".aExercise").each(function () {
            if($(this).text().toLocaleLowerCase().indexOf(value.toLocaleLowerCase()) === -1){
                $(this).closest("tr").hide();
            }
            else{
                $(this).closest("tr").show();
            }
        });
    }
</script>

<?php
print "</head>";
print "<body style='background: lightgrey;'>";

include("adminmenu.php");

print "<form id='mainForm' method='post'>\n";
// 1. táblázat eleje
print "<table border=0 align='center' width='100%'>";
print "<tr>";

print "<td align='right' style='vertical-align:top'>\n";
print "<input type='hidden' name='selectedSubId' id='selectedSubId' value=''>";
print "<input type='hidden' name='actionType' id='actionType' value=''>";
print "<input type='hidden' name='imgName' id='imgName' value='{$_POST['imgName']}'>";
print "<input type='hidden' name='imgPath' id='imgPath' value='{$_POST['imgPath']}'>";
// 2. táblázat eleje
print "<table border=0 width='500' style='padding-left:0px'>";
print "<tr><td align='right'>
<input {$readonly} type='text' name='question' maxlength='100' size='54' style='font-size:40px;color:black;background-color:#ffffff' value=\"{$currentRecord['sub_name']}\"></td></tr>";
if(!$_POST['imgName']){
    $_POST['imgName'] = $baseImg;
}

/*
print "<tr><td align='center'>
            <div id='div_img' style='display:block'>
                <!--<img id='mainImg' src='http://admin.luciendelmar.com/phpics{$plusPath}/{$_POST['imgName']}' height=50 border=0 style='cursor:default'>-->
            </div>
            <div id='div_img_none' style='display:none'>
                <!--<img id='mainImg' src='http://admin.luciendelmar.com/phpics/{$baseImg}' height=50 border=0 style='cursor:default'>-->
            </div>
        </td></tr>";
print "<tr><td align='center' style='padding-top:10px'><div id='div_answer2'>
        <textarea name='answer2' cols=90 rows=2 style='line-height:150%;font-family:arial;font-size:22px;color:black;background-color:#ffffff'>{$currentRecord['exercise']}</textarea></div></td></tr>";
*/
print "<tr><td align='center' style='padding-top:10px'><div id='div_answer'>
        <textarea {$readonly} name='answer' cols=80 rows=7 style='padding:20px;line-height:150%;font-family:arial;font-size:26px;color:black;background-color:#ffffff'>{$currentRecord['concept']}</textarea></div></td></tr>";

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
                $subCat[(string)$j][] = "<a style='font-size:16px;color:white;' href='#' onclick=\"
                    with(document.forms[0]){
                        selectedSubId.value = '{$_current['sub_ID']}';
                        imgName.value = '{$_current['imgName']}';
                        imgPath.value = '{$_current['imgPath']}';
                        submit();
                    }
                \">{$_current['sub_name']}</a>\n";;
            }
        }
    }
}

if($currentRecord){
    $checked = array();
    $checked["1"] = ($_POST["subCategory"] == "1" ? "checked = 'checked'" : "");
    $checked["2"] = ($_POST["subCategory"] == "2" ? "checked = 'checked'" : "");
    $checked["3"] = ($_POST["subCategory"] == "3" ? "checked = 'checked'" : "");
    $checked["4"] = ($_POST["subCategory"] == "4" ? "checked = 'checked'" : "");
    $checked["0"] = (!$_POST["subCategory"] ? "checked = 'checked'" : "");

    print "<tr><td>
        <table style='width:100%'>
            <tr><td class='subCatRadioTd'><input type='radio' name='subCategory' class='rdSubCategory' value='1' {$checked["1"]}><span class='rdTextSubCategory'>TÜNETEK</span></td><td class='tdSubCatLinks'>" . implode(", ", $subCat["1"]) . "</td></tr>
            <tr><td class='subCatRadioTd'><input type='radio' name='subCategory' class='rdSubCategory' value='2' {$checked["2"]}><span class='rdTextSubCategory'>LABOR</span></td><td class='tdSubCatLinks'>" . implode(", ", $subCat["2"]) . "</td></tr>
            <tr><td class='subCatRadioTd'><input type='radio' name='subCategory' class='rdSubCategory' value='3' {$checked["3"]}><span class='rdTextSubCategory'>OKOK</span></td><td class='tdSubCatLinks'>" . implode(", ", $subCat["3"]) . "</td></tr>
            <tr><td class='subCatRadioTd'><input type='radio' name='subCategory' class='rdSubCategory' value='4' {$checked["4"]}><span class='rdTextSubCategory'>BALANCE</span></td><td class='tdSubCatLinks'>" . implode(", ", $subCat["4"]) . "</td></tr>
            <tr><td class='subCatRadioTd'><input type='radio' name='subCategory' class='rdSubCategory' value='' {$checked["0"]}><span class='rdTextSubCategory'>......</span></td><td class='tdSubCatLinks'>" . implode(", ", $subCat["0"]) . "</td></tr>
        </table>
    </td></tr>";
}
if($userObject['status'] == 9){
    print "<tr><td align='center'>


    <input type='button' style='padding:10px 100px;font-size:30px' name='btnStore' value='SAVE' onclick=\"
        with(this.form){
            selectedSubId.value = '{$_POST['selectedSubId']}';
            actionType.value = 'store';
            submit();
        }
    \">

    <br>

    <input type='button' style='padding:10px 10px;font-size:10px' name='btnStore' value='TÖRLÉS' onclick=\"
        if(confirm('Biztos törölni akarod?')) {
            with(this.form){
                selectedSubId.value = '{$_POST['selectedSubId']}';
                actionType.value = 'delete';
                submit();
            }
        }
    \">

    <input type='button' style='padding:10px 10px;font-size:10px' name='btnStore' value='MEGJELENÍTÉS' onclick=\"
        window.open('matrixShow.php?id={$_POST['selectedSubId']}');
    \">
    <br><br>";
    /*
    print "<input type='button' name='btnShowAnswer' value='Answer please!'
            onclick=\"
            if(document.getElementById('div_answer').style.visibility == 'hidden'){
                document.getElementById('div_answer').style.visibility = 'visible';
                document.getElementById('div_img').style.display = 'block';
                document.getElementById('div_img_none').style.display = 'none';
                this.disabled = true;
            }
        \">";
    */
    print "</td></tr>";
}
// 2. táblázat vége
print "</table>\n";
print "</td>";
print "<td style='vertical-align:top;padding:5px;'>";
print "<input type='text' size='9' style='font-size:24px;' id='txtSearchExercise'>";
if($userObject['status'] == 9){
print "<input type='button' id='btnNewExercise' style='width:100px;height:32px;font-size:18px;' value='ÚJ'>";
}
print "\n<div id='exercises' style='width:280;height:650;overflow:auto'>";
print "<br><table border='0' style='width:210;padding:5px' cellspacing=0 cellpadding=0>";
$prevId = 0;
for($i = 0; $i < count($exercises); $i++){
    $_current = $exercises[$i];
    /*
    // ha megváltozott a main category
    if($_current['ID'] != $prevId){
        print "\n<tr><th align='left'>&nbsp;<br>{$_current['name']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;</th></tr>";

    }
    */
    print "<tr><td style='line-height:200%;'><input type='checkbox' name='exercise[]' value='{$_current['sub_ID']}' {$selectedExercise[$_current['sub_ID']]}>
        &nbsp;<a style='font-size:16px;color:white' name='bkmrk_{$_current['sub_ID']}' href='#' class='aExercise' onclick=\"
                with(document.forms[0]){
                    selectedSubId.value = '{$_current['sub_ID']}';
                    imgName.value = '{$_current['imgName']}';
                    imgPath.value = '{$_current['imgPath']}';
                    document.forms[0].action = '#bkmrk_{$_current['sub_ID']}';
                    submit();
                }
            \">{$_current['sub_name']}</a></td></tr>";
    $prevId = $exercises[$i]['ID'];
}
// 3. table zárása
print "</table>";
print "</div>";
print "</td>";
print "</tr>";
// 1. táblázat vége
print "</table>";
print "</form>\n";
print "</body></html>";
?>