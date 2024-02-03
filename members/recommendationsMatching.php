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
<title>Protokoll maker (default)</title>
<?php include_once('headLinks.php'); ?>
<link rel="stylesheet" href="recommendationsMatching.css">
<style>
.color {
    border: 1px solid <?php echo $color ?>;
}
.saveButton {
    background-color: <?php echo $color ?>;
}
</style>
<script type='text/javascript' src='recommendationsMatching.js' defer>
</script>
</head>
<body>
<?php include("adminmenu.php"); ?>
<div class='topDiv'>
    <div class='select'>
        <select id="select" class='diseaseList'>
        <option value="0">Válassz protokollt!</option>
            <?php 
            include('getDiseases.php'); 
            foreach($allDiseases as $disease){ ?>
            <option value=<?php echo ($disease['Id']); ?>><?php echo iconv("UTF-8", "ISO-8859-2", htmlspecialchars($disease['Name'])); ?></option>
            <?php }?> 
        </select>
    </div>
</div>
<div class='grid'>  
    <div class='one'>
        <div class='search'>Keresõmezõ
        </div>
            <div id='recommendationsSource' class='container'>
            </div> 
    </div>
    <div class='two'>
        <div>&nbsp;
        </div>
        <div id='recommendationsTarget' class='container'>
        </div>
    </div>
    <div class='three'>
        <div>&nbsp;
         </div>
        <div id='supplementsTarget' class='container'>
        </div>
    </div>
    <div class='four'>
        <div class='search'>Keresõmezõ
        </div>
        <div id='supplementsSource' class='container'>
        </div> 
    </div>
</div>
<div class='bottomDiv'>        
    <div class='save'>
        <input type="button" class="saveButton" value='Mentés'>
    </div>
</div>
</body>
</html>
