<style>
    .cardPicStyle
    {
      margin-right:15px;
      height:300px;
    }
</style>

<?php

include_once("functions.php");

$page = 0;
$step = 5;

$isAndroid = false;
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    if(stripos($ua,'android') !== false) { // && stripos($ua,'mobile') !== false) {
        $isAndroid = true;
    }
$AndroidClass ="";
if($isAndroid) {
	$AndroidClass="Android";
}
$newsLetterHeader1 = "class='newsLetterHeader1".$AndroidClass."'";
$newsLetterHeader2 = "class='newsLetterHeader2".$AndroidClass."'";
$newsLetterConcept = "class='newsLetterConcept".$AndroidClass."'";


if($_REQUEST['subcontent'] == "programs"){
    $done = 93;
}
else if($_REQUEST['subcontent'] == "bliss"){
    $done = 97;
}
else if($_REQUEST['subcontent'] == "posts"){
    $done = 13;
}
else if($_REQUEST['subcontent'] == "testimonial"){
    $done = 11;
}
else if($_REQUEST['subcontent'] == "therapy"){
    $done = 15;
}
else if($_REQUEST['subcontent'] == "videos"){
    $done = 18;
}
else if($_REQUEST['subcontent'] == "diet"){
    $done = 12;
}
else if($_REQUEST['subcontent'] == "flowsongs"){
    print "
    </td></tr></table></td></tr></table>
        <div>
    ";
    $src = "writings";
    require("pictureNavigate2.php");
    print "</div>";
    return;
}
else if($_REQUEST['subcontent'] == "osszes"){
    $done = 95;
}
else{
    $done = array(93, 97, 13, 12, 11, 95, 15, 18);
}

if($_REQUEST['next'] == "next")
{
	$page = $_REQUEST['page']+1;
	
}

if($_REQUEST['prev'] == "prev")
{
	if($_REQUEST['page']>0)
	{
		$page = $_REQUEST['page']-1;
	}
	
}



// fordított sorrendben kell
$entries = getEntriesByDone($_POST['filterId'], $done,$page,$step);
$categories = getExercisesByDone($done);




if($_SESSION['language'] != 'hun'){
	$nextButton = "Next (5)";
	$prevButton = "Previous (5)";
	
}
else{
	$nextButton = "Következõ (5)";
	$prevButton = "Elõzõ (5)";
}
print "<table border='0' bgcolor='#FAFAFA'><tr><td valign='top'>";
print "<table border='1' bgcolor='#FAFAFA' cellpadding='10' cellspacing='0'><tr>";
print "<td align='center' valign='center' STYLE='background-repeat: no-repeat;'><a name='newsletter'></a>";
print "<form method='POST' action='#hirlevel_bookmark'>";
if($page>0) { print "<button type='submit' name='prev' value='prev' style='background: rgba(253, 134, 4,1);padding-left:12px;padding-right:12px;padding-top:6px;padding-bottom:" . $paddingbottom2 . ";font-size:16px;cursor:pointer;'>".$prevButton."</button>&nbsp;&nbsp;";}
print "<input type='hidden' name='page' value='".$page."'>";
print "<select name='filterId' onchange=\"if(this.value != 'chapter') this.form.submit();\" class='selectBg'>";
    $cnt = 0;
    for($i = 0; $i < count($categories); $i++){
        if($_SESSION['language'] != 'hun'){
            $categories[$i]['concept'] = $categories[$i]['concept_eng'];
        }
        if(!$categories[$i]['concept']){
            continue;
        }
        $cnt++;
    }
    if($_SESSION['language'] != 'hun'){
        $all = "All ({$cnt})";
    }
    else{
        $all = "Mind ({$cnt})";
    }
    print "\n<option value='' selected>{$all}";
    $currentId = 0;
    for($i = 0; $i < count($categories); $i++){
        /*
        if($currentId != $categories[$i]['ID']){
            print "\n<option value='chapter'>{$categories[$i]['name']}";
        }
        */
        if($_SESSION['language'] != 'hun'){
            $categories[$i]['sub_name'] = $categories[$i]['sub_name_eng'];
            $categories[$i]['concept'] = $categories[$i]['concept_eng'];
            $categories[$i]['crdti'] = substr($categories[$i]['crdti'], 5, 2) . '.' . substr($categories[$i]['crdti'], 8, 2) . '.' . substr($categories[$i]['crdti'], 0, 4);
        }
        if(!$categories[$i]['concept']){
            continue;
        }


        if($_POST['filterId'] > 0 && $_POST['filterId'] == $categories[$i]['sub_ID']){
            $selectedText = 'selected'; }
        else{
            $selectedText = '';
        }
        $text = $categories[$i]['crdti'] . ' ' . trim($categories[$i]['sub_name']);
        if($categories[$i]['concept_eng'] != ''){
            $mark = "";
        }
        else{
            $mark = "&#42;";
        }
        print "\n<option value='{$categories[$i]['sub_ID']}' $selectedText>&nbsp;&nbsp;&nbsp;&nbsp;{$text}{$mark}";
        $currentId = $categories[$i]['ID'];
    }
    print "\n</select>";
	if(Count($entries)==5) {print "&nbsp;&nbsp;<button type='submit' name='next' value='next' style='background: rgba(253, 134, 4,1);padding-left:12px;padding-right:12px;padding-top:6px;padding-bottom:" . $paddingbottom2 . ";font-size:16px;cursor:pointer;'>".$nextButton."</button>";}
    print "</form>";
print "</td>";
print "</tr>";
print "<tr><td align='right'>";
//print "<div style='width:600;height:400;overflow:auto'>";
//$mainWidth3 = "100%";
print "<a name='hirlevel_bookmark'></a>";
print "<table width='$mainWidth3' bgcolor='#FAFAFA' border=1 align='right' cellpadding='10' cellspacing='0' style='width:$mainWidth3!important'>";

foreach($entries as $entry) {
    if(!($entry['ID'] > 0)){
        continue;
    }
    if($_SESSION['language'] != 'hun'){
        $entry['sub_name'] = $entry['sub_name_eng'];
        $entry['concept'] = $entry['concept_eng'];
        $entry['crdti'] = substr($entry['crdti'], 5, 2) . '.' . substr($entry['crdti'], 8, 2) . '.' . substr($entry['crdti'], 0, 4);
    }
    if(!$entry['concept']){
        continue;
    }

    if($_SESSION['language'] == "eng") {
        $facebookLink = "https://www.facebook.com/sharer/sharer.php?u=http://www.luciendelmar.com/showBlogEng.php?id={$entry['sub_ID']}";
        $facebookImg = 'fbround.png';
    }
    else {
        $facebookLink = "https://www.facebook.com/sharer/sharer.php?u=http://www.luciendelmar.com/showBlogHun.php?id={$entry['sub_ID']}";
        $facebookImg = 'fbmegosztas.png';
    }

    if(!$_POST['filterId']){
        $numTitle = "#{$cnt}";
    }

    $entry['concept'] = str_replace(chr(13) . chr(10), "<br>", $entry['concept']);
    print "<tr><td width='400' ".$newsLetterHeader1."><b>{$numTitle} {$entry['sub_name']}</b></td>
               <td ".$newsLetterHeader2." align='right' width='10%'><b>
                {$entry['crdti']}
<!--
                <a href=\"#\" onclick=\"event.stopPropagation();window.open('$facebookLink','Subscribe','height=300,width=600,toolbar=no,status=no,menubar=no,resizable=yes,location=no,scrollbars=yes')\" style='color:#FFBF00;cursor:pointer;' title=''>
                    <img border='0' style='height:18px;' src='images/{$facebookImg}'></img></a>
-->
               </td>
               </tr>";
    print "<tr><td ".$newsLetterConcept." colspan=2 'style='background-color:'>{$entry['concept']}</td></tr>";
//    print "<tr><td colspan=2>&nbsp;</td></tr>";
    $cnt--;
}
print "</table>";
//print "</div>";
print "</td></tr>";
if(Count($entries)>2) {
print "<tr><td align='center' valign='center' STYLE='background-repeat: no-repeat;'><a name='newsletter'></a>";
print "<form method='POST' action='#hirlevel_bookmark'>";
if($page>0) { print "<button type='submit' name='prev' value='prev' style='background: rgba(253, 134, 4,1);padding-left:12px;padding-right:12px;padding-top:6px;padding-bottom:" . $paddingbottom2 . ";font-size:16px;cursor:pointer;'>".$prevButton."</button>&nbsp;&nbsp;";}
print "<input type='hidden' name='page' value='".$page."'>";
print "<select name='filterId' onchange=\"if(this.value != 'chapter') this.form.submit();\" class='selectBg'>";
    $cnt = 0;
    for($i = 0; $i < count($categories); $i++){
        if($_SESSION['language'] != 'hun'){
            $categories[$i]['concept'] = $categories[$i]['concept_eng'];
        }
        if(!$categories[$i]['concept']){
            continue;
        }
        $cnt++;
    }
    if($_SESSION['language'] != 'hun'){
        $all = "All ({$cnt})";
    }
    else{
        $all = "Mind ({$cnt})";
    }
    print "\n<option value='' selected>{$all}";
    $currentId = 0;
    for($i = 0; $i < count($categories); $i++){
        /*
        if($currentId != $categories[$i]['ID']){
            print "\n<option value='chapter'>{$categories[$i]['name']}";
        }
        */
        if($_SESSION['language'] != 'hun'){
            $categories[$i]['sub_name'] = $categories[$i]['sub_name_eng'];
            $categories[$i]['concept'] = $categories[$i]['concept_eng'];
            $categories[$i]['crdti'] = substr($categories[$i]['crdti'], 5, 2) . '.' . substr($categories[$i]['crdti'], 8, 2) . '.' . substr($categories[$i]['crdti'], 0, 4);
        }
        if(!$categories[$i]['concept']){
            continue;
        }


        if($_POST['filterId'] > 0 && $_POST['filterId'] == $categories[$i]['sub_ID']){
            $selectedText = 'selected'; }
        else{
            $selectedText = '';
        }
        $text = $categories[$i]['crdti'] . ' ' . trim($categories[$i]['sub_name']);
        if($categories[$i]['concept_eng'] != ''){
            $mark = "";
        }
        else{
            $mark = "&#42;";
        }
        print "\n<option value='{$categories[$i]['sub_ID']}' $selectedText>&nbsp;&nbsp;&nbsp;&nbsp;{$text}{$mark}";
        $currentId = $categories[$i]['ID'];
    }
    print "\n</select>";
	if(Count($entries)==5) {print "&nbsp;&nbsp;<button type='submit' name='next' value='next' style='background: rgba(253, 134, 4,1);padding-left:12px;padding-right:12px;padding-top:6px;padding-bottom:" . $paddingbottom2 . ";font-size:16px;cursor:pointer;'>".$nextButton."</button>";} 
    print "</form>";
print "</td></tr>";
}
print "</table>";
//print "</td>";

//        print"<td style='vertical-align:top;'>";
//               include('subscribefromevents.php');
//        print "</td>";

//print "</tr></table>"

?>