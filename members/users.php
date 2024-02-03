<?php
    session_start();
    include_once('functions.php');
    include_once('getAllProUsers.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
?>

<html>
<head>
    <title>Users</title>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="baseStyle2.css">
    <link rel="stylesheet" href="users.css">
    <script type='text/javascript' src='users.js'></script>
</head>
<body>
    <?php include("adminmenu.php");
    $now = date("Y-m-d H:i:s");
    ?>
    <div class="grid">
    <?php 
    $i = 0;
    foreach($allProUsers as $users){ 
        $i++;
        print "<div class='rows number'>$i</div>";
        print "<div class='rows'>" . iconv("UTF-8", "ISO-8859-2", htmlspecialchars($users['vezeteknev'])) . "&nbsp;" . iconv("UTF-8", "ISO-8859-2", htmlspecialchars($users['keresztnev'])) . "&nbsp;(" . iconv("UTF-8", "ISO-8859-2", htmlspecialchars($users['ID'])) . ")</div>";
        print "<div class='rows'>" . $numClients . "</div>";
        $kulonbseg = ceil((strtotime($now) - strtotime($users['last_login']))/3600);
        if ($kulonbseg < 24) {
            $elteltido = $kulonbseg . " órája";
        } else if ($kulonbseg == 24) {
            $elteltido = "Pont egy napja";
        } else if ($kulonbseg > 24) {
            $nap = intval($kulonbseg/24);
            $ora = $kulonbseg % 24;
            $elteltido = $nap . " napja és " . $ora . " órája";
        }
        print "<div class='rows'>" . $elteltido . "</div>";
        print "<div><form class='form' method='post'><input type='button' class='button' id=" . htmlspecialchars($users['ID']) . " value=BACKUP></form></div>";
    }
    ?> 
    </div> 
</body>
</html>