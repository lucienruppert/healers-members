<?php

session_start();
include_once('functions.php');

if(!$userObject){
    include_once('index.php');
    exit;
}
$subchapterCount = 0;
$chapterArray = getChapters();
$gyakorloMod = 1;
$maxSubChapterCount =1;
//DEBUG($chapterArray);
if($userObject['status'] != 9){
    $readonly = "readonly";
}
else{
    $readonly = "";
}

//EÜ #1
//DEBUG($_POST);

if($_POST['submitSource'] == 'chapter'){
	$subChapterArray = getSubChaptersByChapterId($_POST['chapterId']);
	//DEBUG($subChapterArray);
	$subchapterCount = $_POST['subchapterCount'];
	$subchapterCount = $subchapterCount + $_POST['subchapterdir'];
	$maxSubChapterCount = Count($subChapterArray);
	if($subchapterCount>= $maxSubChapterCount)
	{
		$subchapterCount = 0;
	}
	if($subchapterCount< 0 )
	{
		$subchapterCount =  Count($subChapterArray)-1;
	}
	$currentRecord = getCurrentChapter($subChapterArray[$subchapterCount]["ID"]);

}

if( isset($_POST['gyakorlo']) )
{
	$gyakorloMod = $_POST['gyakorlo'];
} else {
	if(isset($_POST))
	{
		$gyakorloMod = 0;
	}
}

if($_POST['submitSource'] == 'store'){
        $storeArray = array();
        $storeArray['ID'] = $_POST['subChapterId'];
        $storeArray['name'] = $_POST['name'];
        $storeArray['concept'] = $_POST['concept'];
        $storeArray['updti'] = 'NOW()';
		
		if(updateChapterSimple($storeArray)){

			$subChapterArray = getSubChaptersByChapterId($_POST['chapterId']);
			$subchapterCount = $_POST['subchapterCount'];
			$currentRecord = getCurrentChapter($subChapterArray[$subchapterCount]["ID"]);
        }
        else{
            print "<script>alert('Hiba történt mentés közben');</script>";
        }

    }
	

$euContains = true;
?>

<html>
<head>
<?php
print"<meta http-equiv=\"content-type\" content=\"text-html; charset=$CHARSET\">
<link rel=stylesheet type='text/css' href='white.css'>"
?>
</head>
<script>
    function showDiv() {
        var my_disply = document.getElementById('conceptDiv').style.display;
        document.getElementById('conceptDiv').style.display = "block";
     }
</script>
<body style='background: rgba(241, 196, 15,1);'>


<br>
<form method='post'>

<input type='hidden' name='chapterId' id='chapterId' value=''>
<input type='hidden' name='subChapterId' id='subChapterId' value=''>
<input type='hidden' name='submitSource' id='submitSource' value=''>
<input type='hidden' name='subchapterCount' id='subchapterCount' value=''>
<input type='hidden' name='subchapterdir' id='subchapterdir' value=''>



<table border='0' align='center'>
	<tr>
		<td style='vertical-align:top'>
			<div id='chapters' style='width:220;height:500;overflow:auto'>
				<table border=0 style='width:200;'>
					<? foreach($chapterArray as $chapter) {

						//DEBUG($chapter);
						//DEBUG($userObject['status']);

						if($chapter["done"] == 10) {
							if (strpos($chapter["chapterName"], 'EÜAK') !== false) {
								$euContains = true;
							} else {
								$euContains = false;
							}
						}
						//DEBUG($euContains);

						if(($euContains == true && $userObject['status'] != 9) || ($userObject['status'] == 9))
						{
							$tdStyle = '';
							$aStyle = '';
							if($chapter["done"] == 10) {
								$tdStyle = 'font-weight:bold;font-size:11pt;';
								$aStyle .= 'font-weight:bold;font-size:11pt;';
							} else {
								$tdStyle .= 'padding-left:10px;';
								$aStyle .= 'font-weight:regular;font-size:11pt;';
							}
					?>
						<tr>
							<td style='<?print($tdStyle);?>'>
							<? if($chapter["done"] == 90) {?>
							<a id='<? print($chapter["ID"]);?>' href='#' style='<? print($aStyle)?>' onclick="
									with(document.forms[0]){
										submitSource.value = 'chapter';
										chapterId.value = '<? print($chapter['ID']);?>';
										subchapterCount.value  = 0;
										gyakorlo.value = '<?print($gyakorloMod);?>';
										submit();
									}">
								<?print($chapter["chapterName"]);?>
							</a>
							<?} else {
								print($chapter["chapterName"]);
							}?>

						</td>
					</tr>
					<?}
					}?>
				</table>
			</div>
		</td>
		<td width='20'>&nbsp;</td>
                <td style='vertical-align:top'>
		<table border='0' cellpadding='0' align='center'>
			<tr align='left'>
				<td style='color:black;font-size:20px;'><input <?print($readonly);?> type='text' name='name' style='font-size:40px;' size='30' value='<?print($currentRecord["name"]);?>'>&nbsp;&nbsp;<?print($subchapterCount+1);?>/<?print($maxSubChapterCount);?></td>
			</tr>
		        <tr><td height='10'>&nbsp;</td></tr>
			<tr align='left'>
			<?php if($gyakorloMod == 1) {?>
				<td><div id="conceptDiv" style='display:none'><textarea <?print($readonly);?> name='concept' cols='40' rows='12' style='font-size:30px;font-weight:600'><?print($currentRecord["concept"]);?></textarea><div>
				<?
			} else {
			?>
				<td><textarea <?print($readonly);?> name='concept' cols='40' rows='12' style='font-size:30px;font-weight:600'><?print($currentRecord["concept"]);?></textarea>
			<?}?>
				</td>
			</tr>
		</table>
		</td>
		</tr>
			<tr align='left'>
			<td style='padding:20;'><input type='checkbox' id = 'gyakorlo' name='gyakorlo' value='' <?if($gyakorloMod == 1) {print("checked");}?> onclick="
				with(document.forms[0]){
					submitSource.value = 'chapter';
					chapterId.value = '<?print($_POST['chapterId']);?>';
					subChapterId.value = '<?print($currentRecord['ID']);?>';
					subchapterCount.value  = '<?print($subchapterCount);?>';
					subchapterdir.value  = '0';
					gyakorlo.value = '<?if($gyakorloMod==1) {print("bazzz");} else {print("1");}?>';
					submit();
				}"> <font size="4">gyakorló mód</font></td>
			<td></td>
                        <td height='70' valign='bottom'>
			<input type='button' style='width:200;height:90;background:white;' name='<?print("subChapterChk_".$currentRecord['ID']."vissza");?>' value='VISSZA' onclick="
				with(document.forms[0]){
					submitSource.value = 'chapter';
					chapterId.value = '<?print($_POST['chapterId']);?>';
					subChapterId.value = '<?print($currentRecord['ID']);?>';
					subchapterCount.value  = '<?print($subchapterCount);?>';
					subchapterdir.value  = '-1';
					gyakorlo.value = '<?print($gyakorloMod);?>';
					submit();
				}">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<?php if($userObject['status'] == 9){?>

                         <input type='button' style='width:100;height:90;background:grey;' name='<?print("subChapterChk_".$currentRecord['ID']."ment");?>' value='MENT' onclick="
				with(document.forms[0]){
					submitSource.value = 'store';
					chapterId.value = '<?print($_POST['chapterId']);?>';
					subChapterId.value = '<?print($currentRecord['ID']);?>';
					subchapterCount.value  = '<?print($subchapterCount);?>';
					gyakorlo.value = '<?print($gyakorloMod);?>';
					submit();
				}">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?}?>

			<input type='button' style='width:200;height:90;background:white;' name='<?print("subChapterChk_".$currentRecord['ID']."kov");?>' value='ELÕRE' onclick="
				with(document.forms[0]){
					submitSource.value = 'chapter';
					chapterId.value = '<?print($_POST['chapterId']);?>';
					subChapterId.value = '<?print($currentRecord['ID']);?>';
					subchapterCount.value  = '<?print($subchapterCount);?>';
					gyakorlo.value = '<?print($gyakorloMod);?>';
					subchapterdir.value  = '1';
					submit();
				}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if($gyakorloMod == 1) {?>
				<input type='button' style='width:100;height:90;background:white;' name='mutat' value='MUTAT' onclick="showDiv()">
			<?}?>
			</td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</form>
<?

/*
if($_POST['sourcePage'] == 'contentmanagement2'){
    if($_POST['actionType'] == 'store'){
        $storeArray = array();
        $storeArray['ID'] = $_POST['bookId'];
        $storeArray['name'] = $_POST['name'];
        $storeArray['concept'] = $_POST['concept'];
        $storeArray['updti'] = 'NOW()';

        if(updateChapterSimple($storeArray)){
        }
        else{
            print "<script>alert('Hiba tï¿½rtï¿½nt mentï¿½s kï¿½zben');</script>";
        }
    }
    $chapterName = $_POST['name'];
    $chapterConcept = $_POST['concept'];
    $chapterId = $_POST['bookId'];
}
else{
    $randomSubChapter = getRandomSubChapterSpecial();
    $chapterName = $randomSubChapter['name'];
    $chapterConcept = $randomSubChapter['concept'];
    $chapterId = $randomSubChapter['id'];
}


print "
<html>
<head></head>
<body>
<form method='post'>
<table border='1' cellpadding='10' align='center'>
<tr align='center'><td><input type='submit' value='MENT' onclick=\"this.form.actionType.value='store';\"></td></tr>
<tr align='center'><td><input type='text' name='name' style='font-size:20px;font-weight:600' size='60' value='" . $chapterName . "'></td></tr>
<tr align='center'><td><input type='button' value='MUTAT' onclick='window.location.href = unescape(window.location.pathname);'></td></tr>
<tr align='center'><td><textarea name='concept' cols='50' rows='16' style='font-size:20px;font-weight:600'>" . str_replace("\\\"", '"', $chapterConcept) . "</textarea></td></tr>
</table>
<input type='hidden' name='bookId' value='$chapterId'>
<input type='hidden' name='actionType' value=''>
<input type='hidden' name='sourcePage' value='contentmanagement2'>
</form>
</body>
</html>
";

*/
?>
