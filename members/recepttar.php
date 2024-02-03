<?php
    session_start();
    include_once('functions_new.php');
    include_once('functions.php');
    include_once('getAllRecept.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
?>
<html>
<head>
    <title>All recept</title>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="recepttar.css">
<style>
    <?php include ('baseStyle2.css'); ?>
</style>
<script type="text/javascript" src="recepttar.js"></script>
</head>
<body>
<?php include("adminmenu.php") ?>
<div class='grid'>
    <div class='list'>
        <form id='form' method='post' action=''>
            <?php 
            $i = 0;
            foreach($recept_all as $recept){ 
            $i++;
            ?>
            <div class='listItem'>
                <div class='listRow'><b><?php echo $i . "." ?></b>
                <input type='button' class='elnevezes' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[1])); ?>'>
                </div>
                <div class='idRow'><?php echo ($recept[0]); ?>
                    <input type='hidden' class='id' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[0])); ?>'>
                    <input type='hidden' class='elnevezes2' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[1])); ?>'>
                    <input type='hidden' class='hozzavalok' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars(rtrim($recept[2]))); ?>'>
                    <input type='hidden' class='elkeszites' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars(rtrim($recept[3]))); ?>'>
                    <input type='hidden' class='forras' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[4])); ?>'>
                    <input type='hidden' class='kep' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[5])); ?>'>
                    <input type='hidden' class='adag' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[6])); ?>'>
                </div>
            </div>
            <?php 
            }?> 
        </form>
    </div> 
    <div class='receptview'>
        <div id='elnevezes_cb'></div>
        <div id='kep_cb'></div>
        <div class='label'>Hozzávalók</div>
        <div class='box_style' id='hozzavalok_cb'></div>
        <div class='label'>Elkészítés</div>
        <div class='box_style' id='elkeszites_cb'></div>
        <div class='label'>Adag</div>
        <div class='box_style' id='adag_cb'></div>
        <div  class='box_style' id='forras_cb'></div>
    </div>   
</div>
</body>
</html>