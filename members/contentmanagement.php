<?php

session_start();
include_once('functions.php');

if($userObject['status'] != 9 and $userObject['status'] != 4){
    print "<script>alert('Nincs jogosultságod a lap megtekintéséhez!');</script>";
    include_once('index.php');
    exit;
}
/*
CREATE TABLE `book` (
`ID` BIGINT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 200 ) NOT NULL ,
`ref_ID` BIGINT,
`concept` TEXT,
`done` TINYINT DEFAULT '0' NOT NULL ,
`order` INT NOT NULL ,
`crdti` DATETIME NOT NULL ,
`updti` DATETIME,
PRIMARY KEY ( `ID` ) ,
FULLTEXT (
`concept`
)
);
*/
/*
temp();
exit;
function temp()
{
    $query = "SELECT DISTINCT jk.id
                FROM jelentkezok_kedvenc jk
                LEFT OUTER JOIN jelentkezok j ON j.ID = jk.jelentkezok_id
                WHERE j.ID IS NULL";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    $ids = array();
    while($row = mysql_fetch_row($result)) {
        $ids[] = $row[0];
    }
    if(count($ids) === 0){
        return 0;
    }
    $query = "delete from jelentkezok_kedvenc where id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
}
*/
if((int)$_POST['cardNumber'] > 0){
    $bookRecordByCardNumber = getBookRecordByCardNumber((int)$_POST['cardNumber']);
    $_POST['chapterId'] = $bookRecordByCardNumber['chapterId'];
    $_POST['subChapterId'] = $bookRecordByCardNumber['subChapterId'];
    $_POST['sourceId'] = $bookRecordByCardNumber['subChapterId'];
}
if($_POST['actionType'] == 'store'){
    $storeArray = array();
    $storeArray['ID'] = $_POST['sourceId'];
    $storeArray['name'] = $_POST['chName'];
    $storeArray['name_eng'] = $_POST['chNameEng'];
    $storeArray['name_si'] = $_POST['chNameSi'];
    $storeArray['name_it'] = $_POST['chNameIt'];
    $storeArray['name_gre'] = $_POST['chNameGre'];
    $storeArray['concept'] = $_POST['chConcept'];
    $storeArray['exercise'] = $_POST['chExercise'];
    $storeArray['concept_eng'] = $_POST['chConceptEng'];
    $storeArray['exercise_eng'] = $_POST['chExerciseEng'];
    $storeArray['concept_si'] = $_POST['chConceptSi'];
    $storeArray['exercise_si'] = $_POST['chExerciseSi'];
    $storeArray['concept_it'] = $_POST['chConceptIt'];
    $storeArray['exercise_it'] = $_POST['chExerciseIt'];
    $storeArray['done'] = $_POST['doneStatus'];
    $storeArray['ref_ID'] = $_POST['chapterRef'];
    $storeArray['order'] = $_POST['order'];
    $storeArray['updti'] = 'NOW()';

    if($_POST['newStore'] == '0'){
        updateChapter($storeArray);
        $checkedSubs = array();
        foreach($_POST as $key => $value){
            if(substr($key, 0, 14) == 'subChapterChk_'){
                $checkedSubs[] = (int)substr($key, 14);
            }
        }
        updateSubchapterSilenceMessages($_POST['chapterId'], $checkedSubs);
    }
    else{
        list($_POST['sourceId'], $_POST['chapterId'], $_POST['subChapterId']) = insertChapter($storeArray);
        if($_POST['subChapterId'] > 0){
            $_POST['submitSource'] = 'subChapter';
        }
        else{
            $_POST['submitSource'] = 'chapter';
        }
    }
}
if($_POST['actionType'] == 'delete'){
    deleteChapter($_POST['sourceId']);
}
if($_POST['actionType'] == 'sendEmail'){
    sendFormMail($_POST['subChapterId']);
}
if($_POST['actionType'] == 'sendSuccessEmail'){
    sendSuccessFormMail($_POST['subChapterId'], $_POST['emailGroup'], $_POST['pictureName']);
}
if($_POST['actionType'] == 'sendSilenceEmail'){
    $checkedSubs = array();
    foreach($_POST as $key => $value){
        if(substr($key, 0, 14) == 'subChapterChk_'){
            $checkedSubs[] = (int)substr($key, 14);
        }
    }
    $sentIds = sendSilenceMail($_POST['chapterId'], $checkedSubs, $_POST['emailGroup'], $_POST['pictureName'], $_POST['emailSubject']);
    setIsSilenceMessageSent($sentIds);
}

$chapterArray = getChapters();
if($_POST['actionType'] == 'toplist')
{
	$subChapterArray = getSubChaptersByChapterIdOrderByTop($_POST['chapterId']);
}
else {
	$subChapterArray = getSubChaptersByChapterId($_POST['chapterId']);
}
if($_POST['sourceId']){
    $currentRecord = getCurrentChapter($_POST['sourceId']);
    $bodyOnload .= "a_{$_POST['chapterId']}.scrollIntoView(true);";
    $bodyOnload .= "a_sub_{$_POST['subChapterId']}.scrollIntoView(true);";
    //print 'lalala';
}


print "<html>";
print "<body onload='$bodyOnload' style='background: rgba(241, 196, 15,1);'><head>
<title>Content management</title>
<meta http-equiv=\"content-type\" content=\"text-html; charset=$CHARSET\">
<link rel=stylesheet type='text/css' href='white.css'>
<script src='jquery-1.10.2.min.js' type='text/javascript'></script>
<script>
var searchTimeout;
$(document).ready(function () {
    $('#txtSearchCard, #txtSearchCard2').keyup(function () {
        if (searchTimeout != undefined){
            clearTimeout(searchTimeout);
        }
        searchTimeout = setTimeout(chooseCard, 1000);
    });";
//if($userObject['status'] != 9)
//    print "$('.onlyAdmin').hide();";

print "
});
function chooseSubChapter(chapterId, subChapterId)
{
    $('#chapterId').val(chapterId);
    $('#subChapterId').val(subChapterId);
    $('#sourceId').val(subChapterId);
    $('#submitSource').val('subChapter');
    $('#myForm').submit();
}
function chooseCard()
{
    var cardNumber = $('#txtSearchCard').val() > 0 ? $('#txtSearchCard').val() : $('#txtSearchCard2').val();
    $('#cardNumber').val(cardNumber);
    $('#submitSource').val('subChapter');
    $('#myForm').submit();
}";

print "</script>
</head>
";
print "<form id='myForm' action='contentmanagement.php' method='POST'>";
print "<input type='hidden' name='actionType' value=''>";
print "<input type='hidden' name='pictureName' value=''>";
print "<input type='hidden' name='emailSubject' value=''>";
print "<input type='hidden' name='newStore' value=''>";
print "<input type='hidden' name='chapterId' id='chapterId' value=''>";
print "<input type='hidden' name='subChapterId' id='subChapterId' value=''>";
print "<input type='hidden' name='submitSource' id='submitSource' value=''>";
print "<input type='hidden' name='sourceId' id='sourceId' value=''>";
print "<input type='hidden' name='chId' value='{$currentRecord['ID']}'>";
print "<input type='hidden' name='cardNumber' id='cardNumber' value=''>";


print "<table border=0><tr><td>";
if($userObject['status'] != 9){
    $readonly = "readonly";
}
else{
    $readonly = "";
}
print "<table border=0><tr><td><input style='padding-left:20px;font-size:40px;' type='text' name='chName' maxlength='200' size='44' value=\"{$currentRecord['name']}\" $readonly></td>";
if($userObject['status'] == 9){

              print "
              <td  width=200><input type='text' name='chNameEng' maxlength='200' size='75' value=\"{$currentRecord['name_eng']}\"></td>
              <!--<td colspan=100><input type='text' name='chNameGre' maxlength='200' size='60' value=\"{$currentRecord['name_gre']}\"></td>-->";
  }
  else{
              print "
              <td  width=200><input type='hidden' name='chNameEng' value=\"{$currentRecord['name_eng']}\"></td>
              <!--<td colspan=100><input type='text' name='chNameGre' maxlength='200' size='60' value=\"{$currentRecord['name_gre']}\"></td>-->";
  }
/* print "<td  width=200><input type='text' name='chNameIt' maxlength='200' size='60' value=\"{$currentRecord['name_it']}\"></td>";
*/
print "</tr></table>";

if($userObject['status'] == 9) {
    //print "<div style='display:none'>";
	print "<div>";
print "<table>";
if($_POST['submitSource'] == 'subChapter' or $_POST['submitSource'] == 'chapter' or $_POST['actionType'] == 'new'){
    print "\n<tr><td><select name='chapterRef'>";
    if($_POST['actionType'] == 'new'){
        print "\n<option>";
    }
    if($_POST['submitSource'] == 'chapter'){
        print "\n<option value=''>";
    }
    foreach($chapterArray as $row){
        if($row['ID'] == $currentRecord['ref_ID']){
            $selected = ' selected';
        }
        else{
            $selected = '';
        }
        if($row['done'] == 10){
            $tab = "";
        }
        else {
            $tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        print "\n<option value='{$row['ID']}'{$selected}>{$tab}{$row['chapterName']}";
    }
    print "\n</select></td>";
}
if($_POST['actionType'] != 'new'){
    print "<td><select name='order'>";
    print "\n<option>";
    switch($_POST['submitSource']){
        case 'chapter':
            $currentArray = $chapterArray;
            $keyName = 'chapterName';
            break;
        case 'subChapter':
            $currentArray = $subChapterArray;
            $keyName = 'subChapterName';
            break;
    }
    foreach((array)$currentArray as $row){
        if($row['ID'] == $currentRecord['ID']){
            continue;
        }
        if($keyName == 'subChapterName'){
            $tab = $row['crdti'] . '&nbsp;&nbsp;-&nbsp;&nbsp;';
        }
        else{
            $tab = '';
        }
        print "\n<option value='{$row['ID']}'>{$tab} {$row[$keyName]}";
    }
    print "\n</select>";
}
if($_POST['submitSource'] == 'subChapter' or $_POST['submitSource'] == 'chapter' or $_POST['actionType'] == 'new'){
    $stateArray = array(
       92 => 'BEJEGYZÉS (92)',
       90 => 'ALFEJET (90)',
       10 => 'FEJEZET (10)',
       18 => 'BLOG videó (18)',
       97 => 'BLOG lét öröme (97)',
       13 => 'BLOG írások (13)',

//       16 => 'ÉLETTÉRKÉP (kérdések) (16)',
//       17 => 'ÉLETTÉRKÉP (cikkek, videók) (17)',
//       99 => 'ÉLETTÉRKÉP (gyakorlatok) (99)',
//       98 => 'ÉLETTÉRKÉP (visszajelzés) (98)',
//       94 => 'KÁRTYA/CIKK (félkész) (94)',
//       96 => 'KÁRTYA (KÉSZ) (96)',
//       91 => 'App bejegyzés (91)',
//       95 => 'BLOG összes (95)',
//       14 => 'BLOG flow dalok (14)',
//       93 => 'BLOG programok (93)',
//       11 => 'BLOG progi visszajelzések (11)',
//       12 => 'BLOG táplálkozás (12)',

    );
//    print "<tr>";
    print "\n<select name='doneStatus'>";
    foreach($stateArray as $key => $value){
        if($key == $currentRecord['done']){
            $selected = ' selected';
        }
        else{
            $selected = '';
        }
        print "<option value='$key'$selected>$value";
    }
    print "</select></td>";
}
print "</tr>";

if($_POST['actionType'] == 'new'){
    $newStore = '1';
}
else{
    $newStore = '0';
}
$orszagok = getRegisteredCountries();
print "<tr>
    <td colspan=10 nowrap>
    <!-- <input type='button' name='sendAttachmentMail' value=' Mellékletküldés ' onclick=\"window.open('attachmentMail.php','attachmentMail','height=450,width=600,toolbar=no,status=no,menubar=no,resizable=yes,location=no,scrollbars=yes')\">
    -->
 <select name='emailGroup'>
	<option value='teszt' selected>Teszt(S-C)
    <option value='subscribed_hun'>Magyar(S-C)
    <option value='subscribed_eng'>Angol(S-C)
	<option value='teszt_lg'>Teszt(LINGO)
	<option value='lingocasa_hun'>Magyar(LINGO)

 </select>";

print "
 <input type='button' name='sendSuccessMessage' value='  KörEmail  ' onclick=\"
    if(" . (int)$_POST['subChapterId'] . " > 0){

            with(document.forms[0]){
                actionType.value = 'sendSuccessEmail';
                submitSource.value = '{$_POST['submitSource']}';
                sourceId.value = '{$_POST['sourceId']}';
                chapterId.value = '{$_POST['chapterId']}';
                subChapterId.value = '{$_POST['subChapterId']}';
                newStore.value = '$newStore';
                submit();
            }

/*
        if(confirm('Biztos ezeknek a felhasználóknak akarsz levelet küldeni?')){
            with(document.forms[0]){
                actionType.value = 'sendSuccessEmail';
                submitSource.value = '{$_POST['submitSource']}';
                sourceId.value = '{$_POST['sourceId']}';
                chapterId.value = '{$_POST['chapterId']}';
                subChapterId.value = '{$_POST['subChapterId']}';
                newStore.value = '$newStore';
                submit();
            }
        }
*/
    }
    else{
        alert('Nincs kiválasztva alfejezet!');
    }
 \"><!--<input type='button' name='sendSilenceMessage' value='   Aktív csend   ' onclick=\"
    if(" . (int)$_POST['chapterId'] . " > 0){
        if(nev = prompt('Kiküldendõ kép neve?', 'ldm')){
            if(targy = prompt('A levél tárgya?', 'Aktív-Csend újabb bejegyzések')){
                with(document.forms[0]){
                    actionType.value = 'sendSilenceEmail';
                    pictureName.value = nev;
                    emailSubject.value = targy;
                    submitSource.value = '{$_POST['submitSource']}';
                    sourceId.value = '{$_POST['sourceId']}';
                    chapterId.value = '{$_POST['chapterId']}';
                    subChapterId.value = '{$_POST['subChapterId']}';
                    newStore.value = '$newStore';
                    submit();
                }
            }
        }
    }
    else{
        alert('Nincs kiválasztva fejezet!');
    }
 \">--><input type='button' name='newRecord' value='        Új        ' onclick=\"
    with(document.forms[0]){
        actionType.value = 'new';
        submit();
    }
 \"><input type='button' name='deleteRecord' value='      Töröl      ' onclick=\"
    if(confirm('Biztos törölni akarod?')){
        with(document.forms[0]){
            actionType.value = 'delete';
            submitSource.value = '{$_POST['submitSource']}';
            sourceId.value = '{$_POST['sourceId']}';
            chapterId.value = '{$_POST['chapterId']}';
            subChapterId.value = '{$_POST['subChapterId']}';
            newStore.value = '$newStore';
            submit();
        }
    }
 \"><input type='button' name='printViewRecord' value='   Nézet   ' onclick='
        if(" . (int)$_POST['subChapterId'] . " > 0){
            open( \"contentprintview.php?subchapter_ID={$_POST['subChapterId']}\" , \"Contentprintview\", \"height=900,width=850,toolbar=no,status=yes,menubar=yes,resizable=yes,location=no,scrollbars=yes\");
        }
        else{
            alert(\"Nincs kiválasztva alfejezet!\");
        }
 '><input type='button' name='storeRecord' value='        TÁROL        ' onclick=\"
    with(document.forms[0]){
        actionType.value = 'store';
        submitSource.value = '{$_POST['submitSource']}';
        sourceId.value = '{$_POST['sourceId']}';
        chapterId.value = '{$_POST['chapterId']}';
        subChapterId.value = '{$_POST['subChapterId']}';
        newStore.value = '$newStore';
        submit();
    }
 \">
    <input type='text' name='searchValue' size='30' value=''>
    <input type='button' name='searchSubmit' value='  Keres  ' onclick=\"open( 'searchresult.php' , 'Searchresult', 'height=680,width=940,toolbar=no,status=yes,menubar=yes,resizable=yes,location=no,scrollbars=yes');\">
    <input type='text' name='searchCard' id='txtSearchCard' size='3' value=''>
    <span style='font-weight:bold;margin-left:10px;font-size:12px;'>{$currentRecord['card_number']}</span>



	<input type='button' name='showTopList' value='        Top list        ' onclick=\"
    with(document.forms[0]){
        actionType.value = 'toplist';
        submitSource.value = 'chapter';
        sourceId.value = '733061';
        chapterId.value = '733061';
        submit();
    }
 \">
    </td></tr>
	</table>";
print "</div>";
}
if($userObject['status'] != 9){
print "<table>";
print "<tr>
    <td colspan=10 nowrap>";

print "

    <input type='text' name='searchValue' size='30' value=''>
    <input type='button' name='searchSubmit' value='  Keres  ' onclick=\"open( 'searchresult.php' , 'Searchresult', 'height=680,width=940,toolbar=no,status=yes,menubar=yes,resizable=yes,location=no,scrollbars=yes');\">

	
    </td></tr>
	</table>";
}
/*if($userObject['status'] != 9){
    print "<input type='button' name='storeRecord' value='        TÁROL        ' onclick=\"
    with(document.forms[0]){
        actionType.value = 'store';
        submitSource.value = '{$_POST['submitSource']}';
        sourceId.value = '{$_POST['sourceId']}';
        chapterId.value = '{$_POST['chapterId']}';
        subChapterId.value = '{$_POST['subChapterId']}';
        newStore.value = '$newStore';
        submit();
    }
 \">
 <input type='text' name='searchCard' id='txtSearchCard2' size='3' value=''>
<span style='font-weight:bold;margin-left:10px;font-size:12px;'>{$currentRecord['card_number']}</span>
 ";

} */
print "</td></tr></table>";

print "\n<table><tr>";

/*******************************************************************************/
/* Fejezetek                                                                   */
/*******************************************************************************/
print "\n<td style='vertical-align:top'>";
print "\n<div id='chapters' style='width:300;height:500;overflow:auto'>";

print "\n<table border=0 style='width:230;'>";

foreach($chapterArray as $row) {


	if($row["done"] == 10) {
		if (strpos($row["chapterName"], 'EÜ') !== false) {
			$euContains = true;
		} else {
			$euContains = false;
		}
	}
	if(($euContains == true && $userObject['status'] != 9) || ($userObject['status'] == 9))
	{
		if($row['ID'] == $_POST['chapterId']){
			$tdStyle = 'background-color:#2E2EFE;font-size:20pt;';
			$aStyle = 'color:white;';
		}
		else{
			$tdStyle = '';
			$aStyle = '';
		}
		if($row['done'] == 10) {
			$isFocim = true;
			$aStyle .= 'font-weight:bold;font-size:12pt;';
		}
		else{
			$isFocim = false;
			$tdStyle .= 'padding-left:20px;font-size:12pt;';
		}

		// $row['Concept'] = str_replace(chr(13) . chr(10), "<br>", $row['Concept']);
		print "<tr>";
		print "<td style='$tdStyle'><a id='a_{$row['ID']}' href='#' style='$aStyle' onclick=\"
		with(document.forms[0]){
			submitSource.value = 'chapter';
			sourceId.value = '{$row['ID']}';
			chapterId.value = '{$row['ID']}';
			submit();
		}
	 \">{$row['chapterName']}";

		if(!$isFocim)
			print "&nbsp;<b>({$row['subChaptersNumber']})</b>";
		if($userObject['status'] == 9) { print "&nbsp;{$row['ID']}"; }
		print "</a></td>";
		print "</tr><tr><td></td></tr>";
	}
}
print "\n</table>";

print "\n</div>";
print "\n</td>";

/*******************************************************************************/
/* Alfejezetek                                                                 */
/*******************************************************************************/



print "\n<td style='vertical-align:top'>";
print "\n<div id='subChapters' style='width:200;height:500;overflow:auto'>";

print "\n<table border=1'>";

foreach($subChapterArray as $row) {
    if($row['forumEntryNumber'] > 0){
        $bold = "<b>";
    }
    else{
        $bold = "";
    }
    if($row['is_silence_message_sent'] == '1'){
        $checked = 'checked';
    }
    else{
        $checked = '';
    }
    if(strpos($row['concept_eng'],'<img') === false){
        $style = '';
    }
    else{
        $style = 'background-color:green;color:white;';
    }
    $timePart = substr($row['crdti'], 0, 10);
    print "<tr>";
    print "<td style='{$style}'><input type='checkbox' name='subChapterChk_{$row['ID']}' value='1' {$checked}><a id='a_sub_{$row['ID']}' href='#' style='{$style}' onclick=\"
    with(document.forms[0]){
        submitSource.value = 'subChapter';
        sourceId.value = '{$row['ID']}';
        chapterId.value = '{$row['ref_ID']}';
        subChapterId.value = '{$row['ID']}';
        submit();
    }
 \"><b>{$row['subChapterName']}</b>";
	if($userObject['status'] == 9) { print "<br>{$timePart} ({$row['ID']}) ({$row['done']}) <font color='red'>({$row['mailCount']})</font> </b>"; }
	print "</a></td>";
    print "</tr>";
}
print "\n</table>";

print "\n</div>";
print "\n</td>";

print "\n<td style='vertical-align:top'>";
print "\n<table style='font-size:10'><tr>";
/***********/
/* Concept */
/***********/

if(($_POST['submitSource'] == 'subChapter' or $_POST['actionType'] == 'new')){
    print "\n<td valign='top' class='onlyAdmin'><textarea {$readonly} style='padding:15px;font-size:22px;font-weight:600;background-color:white;color:gray' name='chConcept' cols=50 rows=18>{$currentRecord['concept']}</textarea></td>";
   // print "\n<td valign='top' class='onlyAdmin'><textarea style='padding:15px;font-size:20px;font-weight:600;background-color:white;color:gray' name='chConceptEng' cols=9 rows=17>{$currentRecord['concept_eng']}</textarea></td>";
/*    print "\n<td valign='top'><textarea style='font-size:16px;font-weight:600;background-color:white;color:gray' name='chConceptIt' cols=30 rows=18>{$currentRecord['concept_it']}</textarea></td>";
*/
}
print "\n</tr>";
print "\n</table>";

/*print "\n<table style='font-size:10'><tr>";*/
/***********/
/* Exercise */
/***********/
/*if($_POST['submitSource'] == 'subChapter' or $_POST['actionType'] == 'new'){
    print "\n<td class='onlyAdmin'><textarea style='font-size:16px;font-weight:600;background-color:white;color:gray' name='chExercise' cols=60 rows=2>{$currentRecord['exercise']}</textarea></td>";
    print "\n<td class='onlyAdmin'><textarea style='font-size:16px;font-weight:600;background-color:white;color:gray' name='chExerciseEng' cols=15 rows=2>{$currentRecord['exercise_eng']}</textarea></td>";
/*    print "\n<td><textarea style='font-size:12px;font-weight:600;background-color:white;color:gray' name='chExerciseIt' cols=34 rows=4>{$currentRecord['exercise_it']}</textarea></td>";
}
print "\n</tr>";
print "\n</table>";
*/

print "</td>";

print "\n</tr>";
print "\n</table>";
print "\n</form>";
print "</body>";
print "</html>";


?>