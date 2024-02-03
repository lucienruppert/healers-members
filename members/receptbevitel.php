<?php

// *********************************************************** 
// ******** NYISD MEG MAGYAR KÓDOLÁSBAN!!! ******************* 
// *********************************************************** 

    session_start();
    include_once('functions.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
?>
<!DOCTYPE html>
<head>
    <title>Receptbevitel</title>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="baseStyle2.css">
    <link rel="stylesheet" href="receptbevitel.css">
    <style>
        html, body, table {
            height:100%;
        }	
		body{
			margin: 0px !important;
		}
		.grid{ 
			height: calc(100% - 65px);
		}
    </style>
    <script type="text/javascript" src="receptbevitel.js"></script>
</head>
<body>
<?php include("adminmenu.php") ?>
<div class='grid'>
    <!-- MEGTEKINTÕ -->
    <div id='controlbox'>
        <div id='elnevezes_cb'></div>
        <div id='kep_cb'></div>
        <div class='label'>Hozzávalók</div>
        <div class='box_style' id='hozzavalok_cb'></div>
        <div class='label'>Elkészítés</div>
        <div class='box_style' id='elkeszites_cb'></div>
        <div class='label'>Adag</div>
        <div class='box_style' id='adag_cb'></div>
        <div class='box_style' id='alkoto_cb'></div>
    </div> 
    <!-- BEVITEL -->
    <div>
        <form id='receptSave' method='post' target='iframe' action='kepfeltoltes.php' enctype='multipart/form-data'>
            <div><input type='text' size='63' id='elnevezes' placeholder='Elnevezés'></div>
            <div><textarea rows='14' cols='62' id='hozzavalok' placeholder='Hozzávalók'></textarea></div>
            <div><textarea rows='14' cols='62' id='elkeszites' placeholder='Elkészítés'></textarea></div>
            <div><input type='text' size='63' id='adag' placeholder='Adag'></div>
            <div><input type='text' size='63' id='alkoto' placeholder='Forrás'></div>
            <div id='kepButton'><input type='submit' name='kepfeltoltes' id='kepfeltoltes'><input type='file' size='63' id='kep' name='kep' value='null'></div>
            <div><input type='hidden' name='id' id='id'></div>
            <div id='saveButton'><input type='button' id='createButton' value='MENT / MUTAT'></div>
        </form>
    </div>
    <iframe id='iframe' name='iframe' style='display:none'></iframe>
    <!-- JOBB OLDALI RECEPT LISTA -- FRISSÍTÉSNÉL MÁSIK PHP-BÓL HOZZUK BE!-->
    <div id="list">
    <form id='form' method='post'>
        <?php 
        include('getReceptList.php'); 
        foreach($recept_all as $recept){ ?>
            <div class='loadRecept'>
            <input type='button' class='elnevezes' value='<?php echo iconv("UTF-8", "ISO-8859-2", htmlspecialchars($recept['elnevezes'])) . ' (' . $recept['id'] . ')' ; ?>'>
            <input type='hidden' class='id' value='<?php echo ($recept['id']); ?>'>
            </div>
        <?php }?> 
    </form>
    </div> 
</div>
</body>
</html>
