<?php
    session_start();
    include_once('functions.php');   
    include_once('functions_new.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
?>
<!DOCTYPE html>
<head>
<title>Megoldandók</title>
<?php include_once('headLinks.php'); ?>
<link rel="stylesheet" href="megoldandok.css">
<style>
.color {
    border: 2px solid <?php echo $color ?>;
}
.generateButton, .pushedButton {
    background-color: <?php echo $color ?>;
} 

</style>
<script type='text/javascript' src='megoldandok.js' defer>
</script>
</head>
<body>
<?php include("adminmenu.php"); ?>
<div class='page1'>
    <div class='jegyzet'>Jegyzetek
    </div>
    <div>
        <div class='container target' id='kezelendok'>
            <div>Kezelendõ</div>
        </div>
        <div class='container target' id='kivizsgalandok'>
            <div>Kivizsgálandó</div>
        </div>
    </div>
        <div id='list' class='container'>
            <?php 
            include('getAllMegoldandok.php'); 
            foreach($megoldandok_all as $megoldando){ ?>
                <div class='draggable' draggable='true' id='<?php echo ($megoldando[0]); ?>'>
                <input type='button' class='megoldando color' value='<?php echo iconv("UTF-8", "ISO-8859-2", htmlspecialchars($megoldando[1])); ?>'>
                </div>
            <?php }?> 
        </div> 
</div>
<div><hr></div>
<div class='page2'>
    <div class='alsoDivek'>Táplálkozási és életmód ajánlások
    </div>
    <div>
        <div class='alsoDivek tapKieg'>Javasolt étrend-kiegészítõk
        </div>
        <div class='generateDiv'>
            <input type="button" class="generateButton" value='AJÁNLÁS GENERÁLÁSA'>
        </div>
    </div>
    <div class='alsoDivek'>További vizsgálatok és teendõk
</div>
</div>
</body>
</html>
