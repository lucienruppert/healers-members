<?php
    session_start();
    include_once('functions.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
?>
<head>
<title>Étrend</title>
<?php include_once('headLinks.php'); ?>
<link rel="stylesheet" href="generateMenu.css">
<script type="text/javascript" src="generateMenu.js" defer></script>
</head>
<body onload=Feldolgoz();>   
    <div class='container'>
        <div class='headerImageDiv'><img class='headerImage' src="images/yha.png"></div>
        <div class='header'>Személyre szabott mintaétrend</div> 
        <div id='menu' class='pagebreak'></div>
        <div class='recipes' id='recipes'></div>
        <div id="end"></div>
    </div>
    <button onclick="topFunction()" id="button">Vissza</button>
</body>
</html>