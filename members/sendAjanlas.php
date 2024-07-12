<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');

    $id = (int)$_REQUEST["id"];
    $type = (int)$_REQUEST["type"];
    $data = getTProfilServerData($id, $type);
    $target = $_REQUEST["target"];

    $acstipus = $_REQUEST["acstipus"];
    $ajanlasLink = "https://healers.digital/members/ajanlas.php?id=$id&acstipus=$acstipus";
    $tanacsado = $data['consultant'];

    $head = "
    <style>
        table {
              font-family:arial;
              font-size:12;
              color: black;
              text-align:left}

        span {
              font-family:arial;
              font-size:12;
              color:black;
              text-align:left}
        
        A {font-family:Tahoma;font-size:12;font-weight:plain}
        A:link    {text-decoration: none; color: black;}
        A:active  {text-decoration: none; color: black;}
        A:visited {text-decoration: none; color: black;}
        A:hover   {text-decoration: underline; color: black;}
        
        A.selected {font-family:Tahoma;font-size:12; color: black;}
        A.selected:link    {text-decoration: none; color: black;}
        A.selected:active  {text-decoration: none; color: black;}
        A.selected:visited {text-decoration: none; color: black;}
        A.selected:hover   {text-decoration: underline; color: black;}
        
        body {font-family:Tahoma;background-color: white;
                        scrollbar-track-color:white;
                        scrollbar-face-color:silver;
                        scrollbar-highlight-color:black;
                        scrollbar-shadow-color:gray}
                    
        html, body, table {
            height:100%;
        }
        div.kitoltes, #divQuestFill {
            color: black;
            font-size: 16px;
            margin-top: 10px;
            margin-left: 10px;
            margin-bottom: 10px;
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
            white-space: pre-line;
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
        div.favFood{
            font-size: 12pt;
            padding-left: 120px;
        }
        div.chosenAnswer{
            font-weight: bold;
            color: red;
        }
        div.csodaSzam{
            color: lightgrey;
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
    ";

    $body = "<div><a href='$ajanlasLink'>$ajanlasLink</a></div>";
    $groupName = null;
    $body .= "<table style='width:100%'>";
    foreach($data['answers'] as $group){
        if($group['groupName'] != $groupName){
            $body .= "<tr><td colspan='2'><br><hr></td></tr><tr><td colspan='2'><div class='groupName'>${group['groupName']}</div></td></tr>";
            $groupName = $group['groupName'];
        }
        $body .= "<tr><td style='vertical-align:top'><div class='question'>${group['question']}</div></td><td style='vertical-align:top;'><div class='answer'>" . str_replace(utf8_encode("�2"), "</span>", str_replace(utf8_encode("�1"), "<span class='otherAnswer'>", $group['answer'])) . "</div></td></tr>";
    }
    $body .= "</table>";
    $body = "<table cellspacing='0' cellpadding='0' style='width:100%;' border='1'><tr><td style='vertical-align:top;height:100%;'><div id='divQuestFill'>$body</div></td></tr></table>";

    $email = null;

    if(in_array($target, array("marina.boviz@gmail.com", "fitdiet4life@gmail.com", "g.farkaslilla@gmail.com", "eva@airmid.hu", "magdolna.buzasne@gmail.com" , "judit.komaromi.nemes@icloud.com" , "luciendelmar@gmail.com")))
        $email = $target;
    else{
        if($data['consultant'] == utf8_encode("B�v�z Marina")){
            $email = "marina.boviz@gmail.com";
            //$email = "luciendelmar@gmail.com";
        }
        else if($data['consultant'] == utf8_encode("Nagy-Rig� Anita")){
            $email = "fitdiet4life@gmail.com";
            //$email = "luciendelmar@gmail.com";
        }
        else if($data['consultant'] == utf8_encode("Guidi-Farkas Lilla")){
            $email = "g.farkaslilla@gmail.com";
            //$email = "luciendelmar@gmail.com";
        }
        else if($data['consultant'] == utf8_encode("MacPherson �va")){
            $email = "eva@airmid.hu";
            //$email = "luciendelmar@gmail.com";
        }
        else if($data['consultant'] == utf8_encode("L�szl� Magdolna")){
            $email = "magdolna.buzasne@gmail.com";
            //$email = "luciendelmar@gmail.com";

        }
        else if($data['consultant'] == utf8_encode("Kom�romi-Nemes Judit")){
            $email = "judit.komaromi.nemes@icloud.com";
            //$email = "luciendelmar@gmail.com";
        }
    }
    if($email != null && endiMail($email, "${data['userName']} - T�pl�lkoz�si profil", $body, "Lucien del Mar", "luciendelmar@gmail.com", array(), array(), array(), 'utf-8', array(), $head))
        print(json_encode(true));
    else {
        print(json_encode(false));
    }
?>