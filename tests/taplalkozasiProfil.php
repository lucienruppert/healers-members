<?php
    session_start();
    include_once('functions.php');
    $Groups = getQuestionarieGroup("3");

    $userObject["ID"] = crc32(uniqid());
    $kotelezosegHiba = false;
    $kotHibaList = array();
    $queryList = array();
    $consultantId = 0;

    if(count($_POST)>0)
    {
        foreach($Groups as $_groupRow){
            $QuestionListByGroup = GetQuestionsByGroup($_groupRow["ID"]);
            foreach($QuestionListByGroup as $_questionRow){
                $ID = $_questionRow["ID"];
                $isRequired = $_questionRow["required"];
                $isFilled = false;

                if($_questionRow["Type"] == "Group")
                {
                    $field_value = "";
                    if($_POST[$ID]){
                        $arr = $_POST[$ID];
                        $isFilled = true;
                    }
                    else
                        $arr = array();

                    if($_POST[$ID . "_egyeb"]){
                        $arr[] = $_POST[$ID . "_egyeb"];
                    }
                    if(count($arr) > 0)
                        $field_value = implode(";", $arr);
                }
                else
                {
                    $field_value = $_POST[$ID];
                    if($field_value !== "" && $field_value != null)
                        $isFilled = true;
                    if($_POST[$ID . "_egyeb"]){
                        if(strlen($field_value) > 0)
                            $field_value .= ";";
                        $field_value .= $_POST[$ID . "_egyeb"];
                    }
                }
                
                if($isRequired && !$isFilled)
                {
                    $kotelezosegHiba = true;
                    $kotHibaList[] = $ID;
                    break;
                }
                else if($field_value !== "" && $field_value != null)
                    $queryList[] = getQuestionaryAnswerQuery(3, $ID, $userObject["ID"], $field_value);
            }
        }
        $saved = !$kotelezosegHiba;
    }
    if($saved){
        $fillId = saveQuestFill($_POST[50], 3, $consultantId);
        foreach($queryList as $query){
            $query = str_replace("�placeholder�", $fillId, $query);
            $result = mysql_query($query);
            if(!$result){
                print mysql_error();
                return false;
            }
        }
        header('Location: https://healers.digital/tests/thankyou.html');
        exit;
    }

    $linkSelect = "with(document.forms[0]){
                btnNewDesire.value = 'elkuld';
                submit();
            }";
?>
<html>
<head>
    <meta charset="latin2">
    <style>
     .required:after {
         content:" *" ;
         color:red
     }
     .reqErrorMessage{
        color: white;
        background-color: red;
        font-weight:bold;
        padding-left: 5px;
     }
     .groupOption{
        cursor:pointer;
        padding: 3px;
        display: flex;
     }
     .groupOption:hover{
        background-color:#eee;
     }
     .checkedLabel {
        background-color:#ccc;
     }
     select {
         font-size:18px;
     }
     input[type=checkbox], input[type=radio] {
         transform : scale(1.5);
         margin-right: 10px;
     }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).ready(function (){
        <?php if($kotelezosegHiba) { ?>
            var obj = $(".reqError").first();
            if(obj.length > 0){
                obj[0].scrollIntoView();
            }
        <?php } ?>

            $("input[type=checkbox], input[type=radio]", ".groupOption").change(function () {
                if($(this).prop("checked")){
                    if($(this).is("input[type=radio]")){
                        $(this).closest("td").find("input[type=radio]").each(function () {
                            $(this).closest(".groupOption").removeClass("checkedLabel");
                        });
                    }
                    $(this).closest(".groupOption").addClass("checkedLabel");
                }
                else{
                    $(this).closest(".groupOption").removeClass("checkedLabel");
                }
            });
            $("input[type=checkbox], input[type=radio]", ".groupOption").each(function () {
                if($(this).prop("checked")){
                    $(this).closest(".groupOption").addClass("checkedLabel");
                }
            });
        });
    </script>
</head>
<body>
<form method="post" action="" method="POST">
<div>
<table width='800px' border="0" align="center" style="font-family:arial">
<tr>
<td>

    <table>
    <tr>
    <td style='padding-top:50px;padding-bottom:50px;font-size:40pt;text-align:center;color:#fd8604'><b>T�pl�lkoz�si Profil&trade;</td></tr>
    <tr>
    <td style='text-align:justify;line-height:200%;font-size:12pt'>
    A k�rd�sekkel t�pl�lkoz�sod �s anyagcser�d vizsg�ljuk. Mindig azt a v�laszt jel�ld be, ami a legink�bb/legt�bbsz�r igaz r�d. Ha �gy �rzed, hogy egyik v�lasz sem illik r�d t�k�letesen, akkor v�laszd azt, amelyik legjobban megk�zel�ti a val�s�got. Ne azt n�zd, hogy milyennek gondolod magad, hanem pr�b�lj meg r��rezni, hogy milyen vagy val�j�ban. Nincsenek j� vagy rossz v�laszok. Ha esetleg n�h�ny v�laszban nem vagy biztos, mert sosem figyelted meg ezeket a dolgokat �nmagadon (pl. energi�t ad� �telekkel kapcsolatban), akkor adj magadnak n�h�ny napot, figyeld meg a t�pl�lkoz�si szok�saidat, �s csak ut�na k�ldd el a v�laszaidat. L�gy �szinte, sz�nj el�g id�t a v�laszad�sra �s egyed�l t�ltsd ki a tesztet.
    </td></tr>
    <td style='padding-top:50px;text-align:center;line-height:200%;font-size:9pt'>
    A felm�r�s a Your Health Academy szellemi tulajdona, kiz�r�lag saj�t �s partnerh�l�zatunk �gyfelei sz�m�ra. Minden jog fenntartva!
    </td></tr>
    </table>

</td>
</tr>
    <tr>
    <td>
    <?
    foreach($Groups as $_groupRow){
    ?>
    <div align="left" style="align:left;">
        <?
            if($_groupRow["show_label"]==1)
            {
                ?>
                    <table width="800" border="0" align = "left">
                    <tr><td height="40"></tr>
                    <tr><td height="60" style='font-size:25px;color:#ffffff;background: rgba(253, 134, 4,1);'>
                    <?
                        echo "&nbsp;&nbsp;&nbsp;<b>".$_groupRow["Name"];
                    ?>
                    </td></tr>
                    <tr><td height="10"></tr>
                    </table>
                <?
            }
        ?>
        <br>
        <table width="800px" border="0" align = "left">
        <?
            $QuestionListByGroup = GetQuestionsByGroup($_groupRow["ID"]);

            //DEBUG($QuestionListByGroup);
            foreach($QuestionListByGroup as $_questionRow){
                $ID = $_questionRow["ID"];
                $Value ="";
                $classes = array();
                $isReqError = false;
                if(in_array($ID, $kotHibaList)){
                    $classes[] = "reqError";
                    $isReqError = true;
                }
                if($_questionRow["required"])
                    $classes[] = "required";

            // K�RD�SEK ;
            ?>
              <tr align="left">
                <td style='padding-top:50px;padding-bottom:20px;align:justify;line-height:150%;font-size:20px'>
                    <span class=<? echo "'" . implode(" ", $classes) . "'"; ?> style="font-weight:bold"><? echo $_questionRow["Name"];?></span>
                    <? echo $isReqError ? "<div class='reqErrorMessage'>A k�rd�s megv�laszol�sa k�telez�!</div>" : ""; ?>
                </td>
              </tr>
              <? if($_questionRow["img_name"]){ ?>
                <tr><td><img src=<? echo "tpImages/" . $_questionRow["img_name"]; ?>></td></tr>
              <? } ?>
                <? if($_questionRow["Type"] == "String") { ?>
                        <tr align = "left">
                            <td height="40">
                                <input type="text" size="56" style="font-size:26px" name="<?echo $ID;?>" id="<?echo $ID;?>" value="<?echo $_POST[$ID];?>" onclick=""/>
                            </td>
                        </tr>
                <? } if($_questionRow["Type"] == "Date") { ?>
                        <tr align = "left">
                            <td height="40" >
                                <input type="text" name="<?echo $ID;?>" id="DATE" value ="<?echo $_POST[$ID];?>" onclick="clearit(this, 0)"/>
                            </td>
                        </tr>
                <? } if($_questionRow["Type"] == "CheckBox") { ?>
                        <tr align = "left">
                            <td height="40" >
                                <?
                                    $radioArray = explode(";", $_questionRow["Values"]);
                                    for($i=0;$i<count($radioArray);$i++)
                                    {
                                        list($key, $val) = explode("�", $radioArray[$i]);

                                        if($_POST[$ID] != null && (int)$_POST[$ID] === $i)
                                            $chk="checked";
                                        else
                                            $chk="";
                                        ?>
                                            <p><label class='groupOption'><input type="radio" name="<?echo $ID;?>" value="<?echo $key;?>" id="<?echo $ID."_".$key;?>" <?echo $chk?> /><span><?echo $val;?></span></label></p>
                                        <?
                                    } ?>
                                    <?php if($_questionRow["other_use"]){ ?>
                                    <p><span style="display:inline-block;height:80px;vertical-align:middle;margin-right:10px">Egy�b:</span><textarea style="font-size:26px;width:600px;" name="<?echo $ID."_egyeb";?>" id="<?echo $ID."_egyeb";?>" rows=2><?echo $_POST[$ID . '_egyeb'];?></textarea></p>
                                    <?php } ?>
                            </td>
                        </tr>
                <? } if($_questionRow["Type"] == "Group") {
                        $selectedArray = explode(";", $row["answer"]);
                        ?>
                        <tr align = "left">
                            <td height="40" >
                                <?
                                    $chkArray = explode(";", $_questionRow["Values"]);
                                    for($i=0;$i<count($chkArray);$i++)
                                    {
                                        list($key, $val) = explode("�", $chkArray[$i]);

                                        if($_POST[$ID] != null && in_array($key, $_POST[$ID]))
                                            $selected = "checked";
                                        else
                                            $selected = "";
                                    ?>
                                    <p><label class='groupOption'><input type="checkbox" name="<? echo $ID . '[]'; ?>" id="<?echo $ID."_".$key;?>" value="<? echo $key; ?>" <?echo $selected;?> /><?echo $val;?></label></p>
                                <? } ?>
                                <?php if($_questionRow["other_use"]){ ?>
                                <p><span style="display:inline-block;height:80px;vertical-align:middle;margin-right:10px">Egy�b:</span><textarea style="font-size:26px;width:600px;" name="<?echo $ID."_egyeb";?>" id="<?echo $ID."_egyeb";?>" rows=2><?echo $_POST[$ID . '_egyeb'];?></textarea></p>
                                <?php } ?>
                            </td>
                        </tr>
                <? } if($_questionRow["Type"] == "List") { ?>
                        <tr align = "left">
                            <td height="40" >
                                <select name="<? echo $ID;?>" size="1" id="<? echo $ID;?>">
                                    <option value=''></option>
                                <?
                                    $listArray = explode(";", $_questionRow["Values"]);
                                    for($i=0;$i<count($listArray);$i++)
                                    {
                                        list($key, $val) = explode("�", $listArray[$i]);

                                        if($_POST[$ID] != null && (int)$_POST[$ID] === $i)
                                            $chk="selected";
                                        else
                                            $chk="";
                                    ?>
                                        <option value="<?echo $key;?>" <?echo $chk;?> ><?echo $val ?></option>
                                    <?
                                    }
                                ?>
                                </select>
                            </td>
                        </tr>
                        <?
                    }
                }
          ?>
        </table>
    </div>
    <? } ?>
</td>
</tr>
<!--
<tr>
<td>

    <table>
    <tr>
    <td style='padding-top:50px;font-size:20pt'><b>T�PL�LKOZ�SI NAPL�</td></tr>
    <tr>
    <td style='padding-bottom:50px;text-align:justify;line-height:200%;font-size:12pt'>
A T�pl�lkoz�si napl� kit�lt�se mindenk�ppen javasolt legal�bb 5 napra. Amint azt is elk�ldted nek�nk emailben, elk�sz�tj�k a ki�rt�kel�sedet �s egyeztethetj�k az id�pontot a sz�beli ki�rt�kel�sre.
    </td></tr>
    </table>

</td>
</tr>
-->

<tr>
<td align='center' height='200'>
<input style='padding-top:20px;padding-bottom:20px;padding-left:50px;padding-right:50px;font-size:25px;font-weight:bold;color:#ffffff;background: rgba(253, 134, 4,1);cursor:pointer' type='button' name='btnNewDesire' value='ELK�LD' onclick="with(document.forms[0]){ submit(); }">
</td>
</tr>
</table>
</div>
</form>
</body>
</html>