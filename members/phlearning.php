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
    updateLearningQuestion($storeArray);
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
    for($i = 0; $i < count($currentRecord['exercises']); $i++){
        $selectedExercise[$currentRecord['exercises'][$i]] = 'checked';
    }
}

print "<html>";
print "<body style='background: rgb(50, 102, 50);'>
<head>
<title>Keresõ</title>

   <style>

        input[type=checkbox] {
        /* All browsers except webkit*/
        transform: scale(1.5);

        /* Webkit browsers*/
        -webkit-transform: scale(1.5);
      }

</style>

<meta http-equiv=\"content-type\" content=\"text-html; charset=$CHARSET\">
<link rel=stylesheet type='text/css' href='white.css'>
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

print "<form method='post'>\n";
// 1. táblázat eleje
print "<table border=0 align='center' width='100%'>";
print "<tr>";

/*
print "<td style='vertical-align:top'>";
print "<div style='height:500;width:120;overflow:auto'>";

print "<select name='imgDirectories' onchange=\"
        document.getElementById('imgPath').value = this.value;
        document.getElementById('selectedSubId').value = '{$_POST['selectedSubId']}';
        document.forms[0].submit();
 \"><option></option>\n";
foreach($imgPathArray as $currentPath){
    if($currentPath == '..'){
        continue;
    }
    if($currentPath == $_POST['imgPath']){
        $selected = 'selected';
    }
    else{
        $selected = '';
    }
    print "<option value='{$currentPath}' $selected>$currentPath</option>\n";
}
print "</select><br>\n";
foreach((array)$imgArray as $currentImage){
    print "<img src='http://healers.digital/members/phpics{$plusPath}/{$currentImage}' width=100 border=0 onclick=\"
        document.getElementById('imgName').value = '$currentImage';
        document.getElementById('selectedSubId').value = '{$_POST['selectedSubId']}';
        document.forms[0].submit();
        \"><br>";
    print substr($currentImage, 0, strrpos($currentImage, '.')) . '<br>';
}
print "</div>";
print "</td>";
*/

print "<td align='right' style='vertical-align:top'>\n";
print "<input type='hidden' name='selectedSubId' id='selectedSubId' value=''>";
print "<input type='hidden' name='actionType' id='actionType' value=''>";
print "<input type='hidden' name='imgName' id='imgName' value='{$_POST['imgName']}'>";
print "<input type='hidden' name='imgPath' id='imgPath' value='{$_POST['imgPath']}'>";
// 2. táblázat eleje
print "<table border=0 width='500' style='padding-left:0px'>";
print "<tr><td align='right'>
<input {$readonly} type='text' name='question' maxlength='100' size='63' style='font-size:40px;color:black;background-color:#ffffff' value=\"{$currentRecord['sub_name']}\"></td></tr>";
if(!$_POST['imgName']){
    $_POST['imgName'] = $baseImg;
}

print "<tr><td align='center'>
            <div id='div_img' style='display:block'>
                <!--<img id='mainImg' src='http://healers.digital/members/phpics{$plusPath}/{$_POST['imgName']}' height=50 border=0 style='cursor:default'>-->
            </div>
            <div id='div_img_none' style='display:none'>
                <!--<img id='mainImg' src='http://healers.digital/members/phpics/{$baseImg}' height=50 border=0 style='cursor:default'>-->
            </div>
        </td></tr>";
/*
print "<tr><td align='center' style='padding-top:10px'><div id='div_answer2'>
        <textarea name='answer2' cols=90 rows=2 style='line-height:150%;font-family:arial;font-size:22px;color:black;background-color:#ffffff'>{$currentRecord['exercise']}</textarea></div></td></tr>";
*/
print "<tr><td align='center' style='padding-top:10px'><div id='div_answer'>
        <textarea {$readonly} name='answer' cols=99 rows=7 style='padding:20px;line-height:150%;font-family:arial;font-size:26px;color:black;background-color:#ffffff'>{$currentRecord['concept']}</textarea></div></td></tr>";

if($userObject['status'] == 9){
print "<tr><td align='center'><input type='button' style='padding:10px;' name='btnStore' value='                          SAVE                          ' onclick=\"
        with(this.form){
            selectedSubId.value = '{$_POST['selectedSubId']}';
            actionType.value = 'store';
            submit();
        }
    \"><br><br>";
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

print "<tr><td width='100%' style='padding-left:40px;padding-right:40px;line-height:200%;'>";
if(is_array($currentRecord['exercises'])){
    $wasHere = false;
    for($i = 0; $i < count($exercises); $i++){
        $_current = $exercises[$i];
        if(in_array($_current['sub_ID'], $currentRecord['exercises'])){
            if($wasHere){
                print ", ";
            }
            print "<a style='font-size:16px;color:white;' href='#' onclick=\"
                    with(document.forms[0]){
                        selectedSubId.value = '{$_current['sub_ID']}';
                        imgName.value = '{$_current['imgName']}';
                        imgPath.value = '{$_current['imgPath']}';
                        submit();
                    }
                \">{$_current['sub_name']}</a>\n";
            $wasHere = true;
        }
    }
}
print "</td></tr>";
// 2. táblázat vége
print "</table>\n";
print "</td>";
print "<td style='vertical-align:top;padding:5px;'>";
if($userObject['status'] == 4){
print "<input type='text' size='16' style='font-size:24px;' id='txtSearchExercise'>";
}
if($userObject['status'] == 9){
print "<input type='text' size='9' style='font-size:24px;' id='txtSearchExercise'>";
print "<input type='button' id='btnNewExercise' style='width:100px;height:35px;font-size:20px;' value='ÚJ'>";
}

print "\n<div id='exercises' style='width:280;height:685;overflow:auto'>";
print "<br><table border='0' style='width:210;' cellspacing=0 cellpadding=0>";
$prevId = 0;
for($i = 0; $i < count($exercises); $i++){
    $_current = $exercises[$i];
    /*
    // ha megváltozott a main category
    if($_current['ID'] != $prevId){
        print "\n<tr><th align='left'>&nbsp;<br>{$_current['name']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;</th></tr>";

    }
    */
    print "<tr><td style='line-height:300%;'><input type='checkbox' name='exercise[]' value='{$_current['sub_ID']}' {$selectedExercise[$_current['sub_ID']]}>
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