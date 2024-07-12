<?php
session_start();
include_once('functions.php');
$Groups = getQuestionarieGroup("2");

$userObject["ID"] = rand(10000000, 99999999);
$kotelezosegHiba = false;
$saved = false;
$kotHibaList = array();
$queryList = array();

$consultantId = (int)$_REQUEST["cid"];
if (!($consultantId > 0)) {
    $consultantId = 1000;
}

if (count($_POST) > 0) {
    foreach ($Groups as $_groupRow) {
        $QuestionListByGroup = GetQuestionsByGroup($_groupRow["ID"]);
        foreach ($QuestionListByGroup as $_questionRow) {
            $ID = $_questionRow["ID"];
            $isRequired = $_questionRow["required"];
            //�GY LEHET TESZTELNI:
            //$isRequired = false;
            $isFilled = false;

            if ($_questionRow["Type"] == "Group") {
                $field_value = "";
                if (getPostElement($ID)) {
                    $arr = $_POST[$ID];
                    $isFilled = true;
                } else
                    $arr = array();

                if ($_POST[$ID . "_egyeb"]) {
                    $arr[] = $_POST[$ID . "_egyeb"];
                }
                if (count($arr) > 0)
                    $field_value = implode(";", $arr);
            } else {
                $field_value = getPostElement($ID);
                if ($field_value !== "" && $field_value != null)
                    $isFilled = true;
                if (getPostElement($ID . "_egyeb")) {
                    if (strlen($field_value) > 0)
                        $field_value .= ";";
                    $field_value .= $_POST[$ID . "_egyeb"];
                }
            }

            if ($isRequired && !$isFilled) {
                $kotelezosegHiba = true;
                $kotHibaList[] = $ID;
            }
            // a sz�ml�z�si c�met, email c�met �s a telefonsz�mot nem mentj�k le adatb�zisba adatbiztons�g miatt
            else if ($field_value !== "" && $field_value != null && !in_array((int)$ID, array(311, 137, 138)))
                $queryList[] = getQuestionaryAnswerQuery(2, $ID, $userObject["ID"], $field_value);
        }
    }
    $saved = !$kotelezosegHiba;
}
if ($saved) {
    $fillId = saveQuestFill($_POST[133], 2, $consultantId);
    foreach ($queryList as $query) {
        $query = str_replace(";placeholder�", $fillId, $query);
        $result = mysql_query($query);
        if (!$result) {
            print mysql_error();
            return false;
        }
    }
    $consUserObj = getUserObjById($consultantId);

    $body = "<html>
			<body>
				<div>Sz�ml�z�si c�m (ha m�g nem adtad meg): {$_POST[311]}</div>
				<div>Email c�m: {$_POST[137]}</div>
				<div>Telefonsz�m: {$_POST[138]}</div>
			</body>
		</html>";

    simpleMail($consUserObj["email"], iconv("latin2", "utf-8", $_POST[133]) . iconv("latin2", "utf-8", " kit�lt�tte a k�rd��vet"), $body);

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
            content: " *";
            color: red
        }

        .reqErrorMessage {
            color: white;
            background-color: red;
            font-weight: bold;
            padding-left: 5px;
        }

        .groupOption {
            cursor: pointer;
            padding: 3px;
            display: flex;
        }

        .groupOption:hover {
            background-color: #eee;
        }

        .checkedLabel {
            background-color: #ccc;
        }

        select {
            font-size: 18px;
        }

        input[type=checkbox],
        input[type=radio] {
            transform: scale(1.5);
            margin-right: 10px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if ($kotelezosegHiba) { ?>
                restoreFromCache();
                var obj = $(".reqError").first();
                if (obj.length > 0) {
                    obj[0].scrollIntoView();
                }
            <?php } else { ?>
                localStorage.clear();
            <?php } ?>

            $("#mainForm input, #mainForm textarea").change(function() {
                saveToCache(this);
            });

            $("input[type=checkbox], input[type=radio]", ".groupOption").change(function() {
                if ($(this).prop("checked")) {
                    if ($(this).is("input[type=radio]")) {
                        $(this).closest("td").find("input[type=radio]").each(function() {
                            $(this).closest(".groupOption").removeClass("checkedLabel");
                        });
                    }
                    $(this).closest(".groupOption").addClass("checkedLabel");
                } else {
                    $(this).closest(".groupOption").removeClass("checkedLabel");
                }
            });
            $("input[type=checkbox], input[type=radio]", ".groupOption").each(function() {
                if ($(this).prop("checked")) {
                    $(this).closest(".groupOption").addClass("checkedLabel");
                }
            });
        });

        function saveToCache(obj) {
            if ($(obj).is("input[type=radio]")) {
                $(obj).closest("td").find("input[type=radio]").each(function() {
                    localStorage.removeItem($(this).attr("id"));
                });
                localStorage.setItem($(obj).attr("id"), $(obj).val());
            } else if ($(obj).is("input[type=checkbox]")) {
                if ($(obj).prop("checked")) {
                    localStorage.setItem($(obj).attr("id"), $(obj).val());
                } else {
                    localStorage.removeItem($(obj).attr("id"));
                }
            } else {
                localStorage.setItem($(obj).attr("id"), $(obj).val());
            }
        }

        function restoreFromCache() {
            for (var i = 0, len = localStorage.length; i < len; ++i) {
                if ($("#" + localStorage.key(i)).length > 0) {
                    if ($("#" + localStorage.key(i)).is("input[type=radio]") || $("#" + localStorage.key(i)).is("input[type=checkbox]")) {
                        $("#" + localStorage.key(i)).prop("checked", true);
                    } else {
                        $("#" + localStorage.key(i)).val(localStorage.getItem(localStorage.key(i)));
                    }
                }
            }
        }
    </script>
</head>

<body>
    <form id="mainForm" method="post" action="" method="POST">
        <div style="display:none">
            <?php
            print "<input type='hidden' name='cid' value='{$consultantId}'>";
            ?>
        </div>
        <div>
            <table width='800px' border="0" align="center" style="font-family:arial">
                <tr>
                    <td>

                        <table border='0' width='100%'>
                            <tr>
                                <td style='padding-top:50px;font-size:40pt;text-align:center;color:#1B5A09'><b>Esetfelm�r� k�rd��vv</b></td>
                            </tr>
                            <tr>
                                <td style='padding-top:50px;font-size:12pt;text-align:justify;line-height:150%;color:#000000'>Ha a beviteli mez� m�ret�n�l hosszabb v�laszt szeretn�l �rni, azt nyugodtan megteheted, mert a rendszer �gy is elt�rolja. A megadott inform�ci�t Adatv�delmi Nyilatkozatunknak megfelel�en kezelj�k, csak az egy�ttm�k�d�s idej�re t�roljuk �s k�r�sre azonnal t�r�lj�k.</td>
                            </tr>
                            <tr>
                                <td style='padding-top:20px;text-align:center;font-size:9pt'>&copy; Minden jog fenntartva.</td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td>
                        <?
                        foreach ($Groups as $_groupRow) {
                        ?>
                            <div align="left" style="align:left;">
                                <?
                                if ($_groupRow["show_label"] == 1) {
                                ?>
                                    <table width="800" border="0" align="left">
                                        <tr>
                                            <td height="40">
                                        </tr>
                                        <tr>
                                            <td height="60" style='font-size:25px;color:#ffffff;background: rgba(27, 90, 9,1);'>
                                                <?
                                                echo "&nbsp;&nbsp;&nbsp;<b>" . $_groupRow["Name"];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="10">
                                        </tr>
                                    </table>
                                <?
                                }
                                ?>
                                <br>
                                <table width="800px" border="0" align="left">
                                    <?
                                    $QuestionListByGroup = GetQuestionsByGroup($_groupRow["ID"]);

                                    //DEBUG($QuestionListByGroup);
                                    foreach ($QuestionListByGroup as $_questionRow) {
                                        $ID = $_questionRow["ID"];
                                        $Value = "";
                                        $classes = array();
                                        $isReqError = false;
                                        if (in_array($ID, $kotHibaList)) {
                                            $classes[] = "reqError";
                                            $isReqError = true;
                                        }
                                        if ($_questionRow["required"])
                                            $classes[] = "required";

                                        // K�RD�SEK ;
                                    ?>
                                        <tr align="left">
                                            <td style='padding-top:50px;padding-bottom:20px;align:justify;line-height:150%;font-size:20px'>
                                                <span class=<? echo "'" . implode(" ", $classes) . "'"; ?> style="font-weight:bold"><? echo $_questionRow["Name"]; ?></span>
                                                <? echo $isReqError ? "<div class='reqErrorMessage'>A k�rd�s megv�laszol�sa k�telez�</div>" : ""; ?>
                                            </td>
                                        </tr>
                                        <? if ($_questionRow["img_name"]) { ?>
                                            <tr>
                                                <td><img src=<? echo "tpImages/" . $_questionRow["img_name"]; ?>></td>
                                            </tr>
                                        <? } ?>
                                        <? if ($_questionRow["Type"] == "String") { ?>
                                            <tr align="left">
                                                <td height="40">
                                                    <?php if ($_questionRow["Rows"] == 1) { ?>
                                                        <input type="text" size="56" style="font-size:26px;width:794px;" name="<? echo $ID; ?>" id="<? echo $ID; ?>" value="<? echo getPostElement($ID); ?>" onclick="" />
                                                    <?php } else { ?>
                                                        <textarea size="56" style="font-size:26px;width:794px;" rows=<?php print "\"" . $_questionRow["Rows"] . "\""; ?> name="<? echo $ID; ?>" id="<? echo $ID; ?>" onclick="" /><? echo htmlentities(getPostElement($ID)); ?></textarea>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <? }
                                        if ($_questionRow["Type"] == "Date") { ?>
                                            <tr align="left">
                                                <td height="40">
                                                    <input type="text" name="<? echo $ID; ?>" id="DATE" value="<? echo getPostElement($ID); ?>" onclick="clearit(this, 0)" />
                                                </td>
                                            </tr>
                                        <? }
                                        if ($_questionRow["Type"] == "CheckBox") { ?>
                                            <tr align="left">
                                                <td height="40">
                                                    <?
                                                    $radioArray = explode(";", $_questionRow["Values"]);
                                                    for ($i = 0; $i < count($radioArray); $i++) {
                                                        list($key, $val) = explode("�", $radioArray[$i]);
                                                        $temp_id = getPostElement($ID);
                                                        if ($temp_id != null && (int)$temp_id === $i)
                                                            $chk = "checked";
                                                        else
                                                            $chk = "";
                                                    ?>
                                                        <p><label class='groupOption'><input type="radio" name="<? echo $ID; ?>" value="<? echo $key; ?>" id="<? echo $ID . "_" . $key; ?>" <? echo $chk ?> /><span><? echo $val; ?></span></label></p>
                                                    <?
                                                    } ?>
                                                    <?php if ($_questionRow["other_use"]) { ?>
                                                        <p><span style="display:inline-block;height:80px;vertical-align:middle;margin-right:10px">Egy�b:</span><textarea style="font-size:26px;width:600px;" name="<? echo $ID . "_egyeb"; ?>" id="<? echo $ID . "_egyeb"; ?>" rows=2><? echo getPostElement($ID . '_egyeb'); ?></textarea></p>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <? }
                                        if ($_questionRow["Type"] == "Group") {
                                            $selectedArray = explode(";", getArrayElement($_questionRow, "answer"));
                                        ?>
                                            <tr align="left">
                                                <td height="40">
                                                    <?
                                                    $chkArray = explode(";", $_questionRow["Values"]);

                                                    for ($i = 0; $i < count($chkArray); $i++) {
                                                        list($key, $val) = explode("�", $chkArray[$i]);

                                                        if (isset($_POST[$ID]) && $_POST[$ID] != null && in_array($key, $_POST[$ID]))
                                                            $selected = "checked";
                                                        else
                                                            $selected = "";
                                                    ?>
                                                        <p><label class='groupOption'><input type="checkbox" name="<? echo $ID . '[]'; ?>" id="<? echo $ID . "_" . $key; ?>" value="<? echo $key; ?>" <? echo $selected; ?> /><? echo $val; ?></label></p>
                                                    <? } ?>
                                                    <?php if ($_questionRow["other_use"]) { ?>
                                                        <p>
															<span style="display:inline-block;height:80px;vertical-align:middle;margin-right:10px">Egy�b:</span>
															<textarea style="font-size:26px;width:600px;" name="<? echo $ID . "_egyeb"; ?>" id="<? echo $ID . "_egyeb"; ?>" rows=2><? echo getPostElement($ID . '_egyeb'); ?></textarea>
														</p>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <? }
                                        if ($_questionRow["Type"] == "List") { ?>
                                            <tr align="left">
                                                <td height="40">
                                                    <select name="<? echo $ID; ?>" size="1" id="<? echo $ID; ?>">
                                                        <option value=''></option>
                                                        <?
                                                        $listArray = explode(";", $_questionRow["Values"]);
                                                        for ($i = 0; $i < count($listArray); $i++) {
                                                            list($key, $val) = explode("�", $listArray[$i]);

                                                            if ($_POST[$ID] != null && (int)$_POST[$ID] === $i)
                                                                $chk = "selected";
                                                            else
                                                                $chk = "";
                                                        ?>
                                                            <option value="<? echo $key; ?>" <? echo $chk; ?>><? echo $val ?></option>
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
                <tr>
                    <td style="padding-top: 20px;"><input type="checkbox" id="cbAdatv" value="1"><label for="cbAdatv">Meg�rtettem �s elfogadtam az <a href="https://yourhealth.store/at/" target="_blank">Adatv�delmi T�j�koztat�t.</a></label></td>
                </tr>
                <tr>
                    <td align='center' height='200'>
                        <input style='padding-top:20px;padding-bottom:20px;padding-left:50px;padding-right:50px;font-size:25px;font-weight:bold;color:#ffffff;background: rgba(27, 90, 9,1);cursor:pointer' type='button' name='btnNewDesire' value='ELK�LD' onclick="if(!$('#cbAdatv').prop('checked')){ alert('K�rj�k fogadja el az Adatv�delmi T�j�koztat�t!'); return; } $(this).prop('disabled', true);with(document.forms[0]){ submit(); }">
                    </td>
                </tr>
            </table>
        </div>
    </form>
</body>

</html>