<?php
    session_start();
    include_once('functions.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
    $consultantId = $userObject['ID'];
?>
<!DOCTYPE html>
<head>
    <title>Táplálék-kiegészítõk</title>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="baseStyle2.css">
    <link rel="stylesheet" href="tapkiegbevitel.css">
    <style>
    html, body, table {
        height:100%;
    }	
    body    {
        margin: 0px !important;
    }
    .color {
        background-color: <?php echo $color ?>;
    }
    </style>
    <script type="text/javascript" src="tapkiegbevitel.js"></script>
</head>
<body>
<?php include("adminmenu.php") ?>
<div class='grid'>
    <div id="list">
    </div> 
    <div class='bevitel'>
        <form id='form' method='post'>
            <div><textarea cols='34%' id='nev' readonly></textarea></div>
            <div><textarea rows='5' cols='34%' id='adagolas' placeholder='Adagolás'></textarea></div>
            <div><textarea rows='5' cols='34%' id='link' placeholder='Kedvezménykód/link'></textarea></div>
            <div class='saveButton'><input type='button' class='button color' value='MENT'></div>
            <div id='message'></div>
        </form>
    </div> 
</div>
</body>
</html>
