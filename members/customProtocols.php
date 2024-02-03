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
<title>PROTOCOL-CUSTOM</title>
<?php include_once('headLinks.php'); ?>
<link rel="stylesheet" href="customProtocols.css">
<style>
.color {
    border: 2px solid <?php echo $color ?>;
}
.generateButton, .saveButton {
    background-color: <?php echo $color ?>;
} 

</style>
<script type='text/javascript' src='customProtocols.js' defer></script>
<script type='text/javascript' src='customProtocolsSep.js' defer></script>
</head>
<body>
<?php include("adminmenu.php"); ?>
<div class='page1'>
    <div class='jegyzet'>Jegyzetek
    </div>
    <div>
        <div class='container target' id='nowProtocols'>
            <span>Kezelendõ</span>
        </div>
        <div class='container target' id='laterProtocols'>
            <span>Kivizsgálandó</span>
        </div>
        <div class='saveDiv'>
            <input type="button" class="saveButton" value='MENTÉS'>
        </div>
    </div>
    <div id='list' class='container'></div> 
</div>
<div class='page1_overlay'></div>
<div><hr></div>
<div class='page2'>
    <div class='bottomDivs target' id='recommProtocolSteps'>Táplálkozási és életmód ajánlások</div>
    <div class='bottomDivs target' id='suppProtocolSteps'>Javasolt étrend-kiegészítõk--</div>
</div>
<div class='generateDiv'>
            <input type="button" class="generateButton" value='AJÁNLÁS GENERÁLÁSA'>
</div>
</body>
</html>
