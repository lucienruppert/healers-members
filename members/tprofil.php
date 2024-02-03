<?php
    session_start();
    include_once('functions.php');
	if(!$userObject){
        include_once('index.php');
        exit;
    }

    $questUsers = selectQuestUsersTP(3);
?>

<html>
<head>
    <title>T-Profil</title>
    <meta http-equiv="content-type" content=<?php print "'text-html; charset=$CHARSET'"; ?>>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
    <link rel=stylesheet type='text/css' href='baseStyle2.css'>
    <style>
        html, body, table {
            height:100%;
        }
		
		body{
			margin: 0px;
		}
		
		#tblMain{
			height: calc(100% - 65px);
		}

        div.kitoltes, #divQuestFill {
            color: black;
            font-size: 16px;
            margin-top: 10px;
            margin-left: 10px;
            margin-bottom: 10px;
        }
        div.ertekelt{
            background-color: lightgrey;
        }
        div.kitoltes{
            cursor: pointer;
        }
        div.userName {
            font-size: 30px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        div.consultant {
            font-size: 20px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            margin-bottom: 50px;
        }
        div.changerButton {
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            margin-bottom: 50px;
        }
        div.groupName {
            font-weight: bold;
            font-size: 16pt;
            text-align: center;
            margin-top: 25px;
            margin-bottom: 25px;
            padding-left: 100px;
        }
        div.question {
            font-size: 12pt;
            margin-top: 15px;
            font-weight: bold;
            padding-left: 120px;
        }
        div.answer {
            font-size: 12pt;
            margin-top: 15px;
            padding-left: 120px;
            padding-right: 120px;
        }
        div.divJobbBlokk{
            font-size: 12pt;
            margin-left: 5px;
            margin-top: 0px;
        }
        div.divJobbGroup{
            margin-top: 20px;
        }
        div.divJobbOptions{
            margin-top: 7px;
        }
        div.divTarget{
            margin-top: 17px;
        }
        input[type=button]#btnDone{
            font-size: 16pt;
            font-weight: bold;
            width: 200px;
            height: 60px;
        }
        input[type=button].btnChanger{
            font-size: 16pt;
            font-weight: bold;
            height: 60px;
        }
        div.divJobbGomb{
            margin-top: 30px;
            margin-left: 30px;
            margin-bottom: 30px;
        }
        div.favFood{
            font-size: 12pt;
            padding-left: 120px;
        }
        div.chosenAnswer{
            font-weight: bold;
            color: red;
        }
        div.csodaSzam{
            color: lightgray;
            font-weight: bold;
            font-size: 20pt;
            width: 100%;
            text-align: center;
        }
        div.savok{
            width: 100%;
            text-align: center;
        }
        div#anyagcsereTipus{
            width: 100%;
            text-align: center;
        }
        div.divJobbOptions{
            padding-left: calc(55vw - 300px);
            text-align: left;
        }
        span.otherAnswer{
            background-color: yellow;
            font-size: 12pt;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="underscore-min.js"></script>
    <script>
        var fillId = null;
        var fillType = null;

        $(document).ready(function () {
            $("div.kitoltes").click(function () {
                fillDivQuestFill($(this).data("myid"), 1);
            });
            $("#btnDone").click(function () {
                $.post("https://luciendelmar.com/academy/sendAjanlas.php", { id: fillId, type: 1, acstipus: getRadioValue("acstipus"), target: $("#target").val() }, function(result){
                    if(result){
                        alert("Levél kiküldve!");
                    }
                    else{
                        alert("Nem lett kiküldve levél!");
                    }
                });
                if(fillId !== null){
                    $.post("https://luciendelmar.com/academy/tprofilEval.php", { id: fillId });
                    window.open("ajanlas.php?id=" + fillId + "&acstipus=" + getRadioValue("acstipus"), "_blank");
                }
            });
            $("body").on("click", ".btnChanger", function () {
                if(fillId !== null){
                    var type = parseInt($(this).data("type"), 10);
                    if(!isNaN(type))
                        fillDivQuestFill(fillId, 3 - type);
                }
            });
        });
        function htmlEncode(value){
            return $("<div/>").text(value).html();
        }
        function getRadioValue(name){
            return $('input[name=' + name + ']:checked').val();
        }
        function fillDivQuestFill(fid, type){
            fillId = null;
            fillType = null;
            $("#divQuestFill").html("");
            $.get("tprofilServer.php", { id: fid, type: type }, function (result) {
                if(result.success) {
                    fillId = fid;
                    fillType = type;
                    $("#divQuestFill").append($("<div class='userName'>" + htmlEncode(result.userName) + "</div>"));
                    $("#divQuestFill").append($("<div class='consultant'>" + "tanácsadó: " + htmlEncode(result.consultant) + "</div>"));
                    var txt = "";
                    if(type === 1)
                        txt = "Anyagcsere típus";
                    else if(type === 2)
                        txt = "Összes info";

                    $("#divQuestFill").append($("<div class='changerButton'><input class='btnChanger' data-type='" + type + "' type='button' value='" + txt + "'></div>"));
                    
                    if(type === 1){
                        $("#anyagcsereTipus").hide();
                        var answers = _.sortBy(result.answers, "groupId");
                        var groupName = null;
                        var myTable = $("<table style='width:100%'></table>");
                        var myRow, myCell;
                        for(var i = 0; i < answers.length; i++){
                            var group = answers[i];
                            if(group.groupName != groupName){
                                myRow = $("<tr></tr>");
                                myCell = $("<td colspan='2'></td>");

                                myCell.append($("<br><hr>"));
                                myRow.append(myCell);

                                myTable.append(myRow);

                                myRow = $("<tr></tr>");
                                myCell = $("<td colspan=2></td>");

                                myCell.append($("<div class='groupName'>" + htmlEncode(group.groupName) + "</div>"));
                                myRow.append(myCell);

                                myTable.append(myRow);

                                groupName = group.groupName;
                            }

                            myRow = $("<tr></tr>");
                            myCell = $("<td style='vertical-align:top'></td>");
                            myCell.append($("<div class='question'>" + htmlEncode(group.question) + "</div>"));
                            myRow.append(myCell);

                            myCell = $("<td style='vertical-align:top'></td>");
                            myCell.append($("<div class='answer'>" + htmlEncode(group.answer).replace(/(?:\r\n|\r|\n)/g, '<br>').replace("ß1", "<span class='otherAnswer'>").replace("ß2", "</span>") + "</div>"));
                            myRow.append(myCell);

                            myTable.append(myRow);
                        }
                        $("#divQuestFill").append(myTable);
                    }
                    else if(type === 2){
                        $("#anyagcsereTipus").show();
                        var myTable = $("<table style='width:100%;border-collapse:collapse;'></table>");
                        var myRow, myCell;
                        var insideTable = $("<table style='width:100%;border-collapse:collapse;' border='1'></table>");
                        var insideRow, insideCell;

                        myRow = $("<tr></tr>");
                        myCell = $("<td style='vertical-align:top' colspan='2'></td>");
                        myCell.append(insideTable);
                        myRow.append(myCell);

                        myTable.append(myRow);

                        var firstPartIds = [ 78, 79, 80, 81, 100, 101, 102 ];
                        // ezt kell átírni, ha a súlyozáson változtatni akarsz!
                        var sulyok =       [ 1,  1,  1,  1,  1,   1,   1 ];
                        var csodaSzam = 0;
                        for(var i = 0; i < firstPartIds.length; i++){
                            var answer = _.find(result.answers, function(ans){ return ans.questionId == firstPartIds[i]; })

                            insideRow = $("<tr></tr>");
                            insideCell = $("<td style='vertical-align:top'></td>");
                            insideCell.append($("<div class='question'>" + htmlEncode(answer.question) + "</div>"));
                            insideRow.append(insideCell);

                            insideCell = $("<td style='vertical-align:top'></td>");
                            insideCell.append($("<div class='answer'>" + htmlEncode(answer.answer).replace(/(?:\r\n|\r|\n)/g, '<br>').replace("ß1", "<span class='otherAnswer'>").replace("ß2", "</span>") + "</div>"));
                            insideRow.append(insideCell);

                            var txt = "";
                            var raw_answer = answer.raw_answer.split(";")[0];
                            if(raw_answer === "0"){
                                txt = "CH";
                                csodaSzam += -1 * sulyok[i];
                            }
                            else if(raw_answer === "1"){
                                txt = "F-PRO";
                                csodaSzam += sulyok[i];
                            }
                            else if(raw_answer === "2"){
                                txt = "VEGYES";
                            }
                            insideCell = $("<td style='vertical-align:middle'></td>");
                            insideCell.append($("<div style='font-weight:bold;font-weight:12pt;text-align:center;padding:0px 10px'>" + txt + "</div>"));
                            insideRow.append(insideCell);

                            insideCell = $("<td style='vertical-align:middle'></td>");
                            insideCell.append($("<div style='font-weight:bold;font-weight:12pt;text-align:center;padding:0px 10px'>" + sulyok[i] + "</div>"));
                            insideRow.append(insideCell);

                            insideTable.append(insideRow);
                        }
                        {
                            var answer = _.find(result.answers, function(ans){ return ans.questionId == 105; })
                            
                            insideTable = $("<table style='width:100%;margin-top:10px;'></table>");
                            myRow = $("<tr></tr>");

                            myCell = $("<td style='vertical-align:top;border:1px solid black;width:200px;'></td>");
                            myCell.append($("<div class='question'>" + htmlEncode(answer.question) + "</div>"));
                            myRow.append(myCell);

                            myCell = $("<td style='vertical-align:top;border:1px solid black;padding-bottom:20px;'></td>");
                            myCell.append(insideTable);
                            myRow.append(myCell);

                            myTable.append(myRow);

                            var allValues = answer.questionValues.split(";");
                            var answers = answer.raw_answer.split(";");
                            for(var i = 0; i < allValues.length; i++){
                                var item = allValues[i].split("ß");
                                var divClass = "";
                                if(_.contains(answers, item[0])){
                                    divClass=" chosenAnswer";
                                }
                                insideRow = $("<tr></tr>");
                                insideCell = $("<td style='vertical-align:top'></td>");
                                insideCell.append($("<div class='favFood" + divClass + "'>" + htmlEncode(item[1]) + "</div>"));
                                insideRow.append(insideCell);

                                insideTable.append(insideRow);
                            }
                        }

                        var secondPartIds = [ 103, 104, 106, 107, 108, 96, 109 ];
                        for(var i = 0; i < secondPartIds.length; i++){
                            var answer = _.find(result.answers, function(ans){ return ans.questionId == secondPartIds[i]; })

                            myRow = $("<tr></tr>");
                            myCell = $("<td style='vertical-align:top'></td>");
                            myCell.append($("<div class='question'>" + htmlEncode(answer.question) + "</div>"));
                            myRow.append(myCell);

                            myCell = $("<td style='vertical-align:top'></td>");
                            myCell.append($("<div class='answer'>" + htmlEncode(answer.answer).replace(/(?:\r\n|\r|\n)/g, '<br>').replace("ß1", "<span class='otherAnswer'>").replace("ß2", "</span>") + "</div>"));
                            myRow.append(myCell);

                            myTable.append(myRow);
                        }
                        {
                            var sum = sulyok.reduce((s, f) => { return s + f; }, 0)
                            var range = 2 * sum;
                            var current = csodaSzam + sum;
                            var percent = 100 * current / range;
                            var category = "???";
                            if(percent >= 0 && percent <= 20){
                                category = "Szénhidrát típus";
                                $("#acstipus_ch").prop("checked", true);
                            }
                            else if(percent > 20 && percent <= 40){
                                category = "Egyensúlyi szénhidrát típus";
                                $("#acstipus_balch").prop("checked", true);
                            }
                            else if(percent > 40 && percent <= 60){
                                category = "Egyensúlyi típus";
                                $("#acstipus_bal").prop("checked", true);
                            }
                            else if(percent > 60 && percent <= 80){
                                category = "Egyensúlyi zsír-fehérje típus";
                                $("#acstipus_balfpro").prop("checked", true);
                            }
                            else if(percent > 80 && percent <= 100){
                                category = "Zsír-fehérje típus";
                                $("#acstipus_fatpro").prop("checked", true);
                            }

                            myRow = $("<tr></tr>");
                            myCell = $("<td style='vertical-align:top;padding-top:20px;' colspan='2'></td>");
                            myCell.append($("<div class='csodaSzam'>" + category + " (" + percent.toFixed(2) + "%)</div>"));
                            myRow.append(myCell);

                            myTable.append(myRow);
                        }

                        $("#divQuestFill").append(myTable);
                    }
                }
                else {
                    $("#divQuestFill").html("");
                    $("#anyagcsereTipus").hide();
                }
            });
        }
    </script>
</head>
<body>
	<?php include("adminmenu.php") ?>
	
    <table id="tblMain" cellspacing="0" cellpadding="0" style="width:100%;" border="1">
        <colgroup>
            <col style="width:300px">
            <col>
        </colgroup>
        <tr>
            <td style="vertical-align:top;height:100%;">
            <div style="height:100%;overflow:auto;">
            <?php
                foreach($questUsers as $user){
                    $datum = substr($user["crdti"], 0, 10);
                    $plusClass = "";
                    if($user["IsEvaluated"] == 1){
                        $plusClass = " ertekelt";
                    }
                    print "<div class='kitoltes$plusClass' data-myid='${user["ID"]}'><b>$datum</b> ${user["userName"]}</div>";
                }
            ?>
            </div>
            </td>
            <td style="vertical-align:top;height:100%;">
                <div id="divQuestFill"></div>
                <div id="anyagcsereTipus" style="display:none">
                    <div class="divJobbBlokk">
                        <div class="divJobbOptions">
                            <input type="radio" id="acstipus_ch" name="acstipus" value="ch"><label for="acstipus_ch">CH (0%-20%)</label><br>
                            <input type="radio" id="acstipus_balch" name="acstipus" value="balch"><label for="acstipus_balch">BAL-CH (20%-40%)</label><br>
                            <input type="radio" id="acstipus_bal" name="acstipus" value="bal"><label for="acstipus_bal">BAL (40%-60%)</label><br>
                            <input type="radio" id="acstipus_balfpro" name="acstipus" value="balfpro"><label for="acstipus_balfpro">BAL-F-PRO (60%-80%)</label><br>
                            <input type="radio" id="acstipus_fatpro" name="acstipus" value="fatpro"><label for="acstipus_fatpro">FAT-PRO (80%-100%)</label><br>
                        </div>
                    </div>
                    <div class="divTarget">
                        <select id="target" name="target">
                            <option></option>
                            <option value="fitdiet4life@gmail.com">Nagy-Rigó Anita</option>
                            <option value="g.farkaslilla@gmail.com">Guidi-Farkas Lilla</option>
                        </select>
                    </div>
                    <div class="divJobbGomb"><input type="button" id="btnDone" value="KÉSZ"><div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>