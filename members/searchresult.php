<html><head>
<script src='jquery-1.10.2.min.js' type='text/javascript'></script>
<script type='text/javascript'>
    $(document).ready(function () {
        $('.selectionRow').click(function () {
            window.opener.chooseSubChapter($(this).find('.spanChapterId').html(), $(this).find('.spanSubChapterId').html());
            window.close();
        });
    });
</script>
<?php
    include_once('functions.php');
    print "<meta http-equiv=\"content-type\" content=\"text-html; charset=$CHARSET\">";
?>
<link rel=stylesheet type='text/css' href='baseStyle.css'>
<style>
    .spanChapterId, .spanSubChapterId
    {
        display: none;
    }
</style>
</head><body style='background: rgba(241, 196, 15,1);'>
<form action='searchresult.php' method='POST'>
<?php

print "<input type='hidden' name='actionType' value=''>";
if($_POST['actionType'] != 'afterSubmit'){
    print "
     <input type='hidden' name='searchValue' value=''>
     <script>
        document.forms[0].searchValue.value = window.opener.document.forms[0].searchValue.value;
        document.forms[0].actionType.value = 'afterSubmit';
        document.forms[0].submit();
     </script>
     ";
}
else{
	
    $text = $_POST['searchValue'];
    $result = searchChapters($text);

    if(count((array)$result) == 0){
        print "<script>window.close();</script>";
        exit;
    }

    print "<table border=1 style='width:900px'>";

    if(count((array)$result) > 1){
        foreach((array)$result as $row) {
            $row['concept'] = str_replace(chr(13) . chr(10), "<br>", $row['concept']);
            $row['exercise'] = str_replace(chr(13) . chr(10), "<br>", $row['exercise']);
            printRow($row['chapterName'], $row['chapterId'], $row['subChapterId'], $row['subChapterName'], $row['concept'], $row['exercise']);
        }
    }
    else if(count((array)$result) == 1){
        $row = $result[0];
        $row['concept'] = str_replace(chr(13) . chr(10), "<br>", $row['concept']);
        $row['exercise'] = str_replace(chr(13) . chr(10), "<br>", $row['exercise']);
        printRow($row['chapterName'], $row['chapterId'], $row['subChapterId'], $row['subChapterName'], $row['concept'], $row['exercise']);

        $row['concept_eng'] = str_replace(chr(13) . chr(10), "<br>", $row['concept_eng']);
        $row['exercise_eng'] = str_replace(chr(13) . chr(10), "<br>", $row['exercise_eng']);
        printRow($row['chapterNameEng'], $row['chapterId'], $row['subChapterId'], $row['subChapterNameEng'], $row['concept_eng'], $row['exercise_eng']);

        $row['concept_it'] = str_replace(chr(13) . chr(10), "<br>", $row['concept_it']);
        $row['exercise_it'] = str_replace(chr(13) . chr(10), "<br>", $row['exercise_it']);
        printRow($row['chapterNameIt'], $row['chapterId'], $row['subChapterId'], $row['subChapterNameIt'], $row['concept_it'], $row['exercise_it']);
    }
    print "</table>";

}

function printRow($chapterName, $chapterId, $subChapterId, $subChapterName, $concept, $exercise)
{
    print "<tr class='selectionRow'>";
    print "<td style='font:bold'>";
    print "{$chapterName}";
    print "<span class='spanChapterId'>{$chapterId}</span><span class='spanSubChapterId'>{$subChapterId}</span>";
    print "&nbsp;</td>";
    print "<td>";
    print "{$subChapterName}";
    print "&nbsp;</td>";
    print "<td>";
    print "{$concept}";
    print "&nbsp;</td>";
    print "<td style='width:200px;vertical-align:top;'>";
    print "{$exercise}";
    print "&nbsp;</td>";
    print "</tr>";
}

?>
</form>
</body></html>