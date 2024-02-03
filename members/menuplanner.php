<?php
    session_start();
    include_once('functions.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }   
?>
<html>
<head>
    <title>Étrendtervezõ</title>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="menuplanner.css">
<style>
<?php include ('baseStyle2.css'); ?>
.buttonWhite {
    color:  <?php echo $color; ?>;
    cursor: pointer;
    border-radius: 5px;
    background-color: white;
    border: 1px solid gray;
}
.xDays {
    color:  <?php echo $color; ?>;
}
.buttonGreen {
    color:  white;
    cursor: pointer;
    border-radius: 5px;
    background-color: gray;
    border: 1px solid  gray;
}
.buttonGen {
    color:  white;
    cursor: pointer;
    border-radius: 5px;
    background-color: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}
.filterButton {
    padding: 5px 20px 5px 20px;
    margin: 10px;
    font-size: 0.8rem;
}
</style>
<script type='text/javascript' src='menuplanner.js' defer></script>
</head>
<body> 
<?php include("adminmenu.php"); ?>
<div class='column'>
    <div class='filters'>
        <div class='leftDiv'>
            <div class='filter' alt='tojas'>
                <input type='button' id='tojas' class='buttonGreen filterButton' value='Tojás'>
            </div>
            <div class='filter' alt='zab'>
                <input type='button' id='zab' class='buttonGreen filterButton' value='Zab'>        
            </div>
            <div class='filter' alt='hal'>
                <input type='button' id='hal' class='buttonGreen filterButton' value='Hal'>
            </div>
            <div class='filter' alt='maj'>
                <input type='button' id='maj' class='buttonGreen filterButton' value='Máj'>
            </div>
            <div class='filter' alt='koles'>
                <input type='button' id='koles' class='buttonGreen filterButton' value='Köles'>
            </div>
        </div>
        <div class='rightDiv'>
            <div class='meal' alt='reggeli'>
                <input type='button' id='meal' class='buttonGreen filterButton' value='Reggeli'>
            </div>
            <div class='meal' alt='koztes1' >
                <input type='button' id='meal' class='buttonGreen filterButton' value='Köztes1'>
            </div>
            <div class='meal' alt='ebed'>
                <input type='button' id='meal' class='buttonGreen filterButton' value='Ebéd'>
            </div>   
            <div class='meal' alt='koztes2'>
                <input type='button' id='meal' class='buttonGreen filterButton' value='Köztes2'>
            </div>           
            <div class='meal' alt='vacsora' >
                <input type='button' id='meal' class='buttonGreen filterButton' value='Vacsora'>
            </div> 
            <div>
                <input type='button' id='generate1' class='buttonGen generateButton filterButton' value='Vázlat'>
            </div>
            <div>
                <input type='button' id='generate2' class='buttonGen generateButton filterButton' value='Végsõ'>
            </div>
        </div>
    </div>
    <div class='menu' id='menu'></div>
    <div id='overlay'></div>
    <div id='searchBoxLayer'></div>
</div>
</body>
</html>
<!-- <div class='leftDiv'>
    <div id='osszes' class='osszes'></div>.    
    <div id='foetkezes' class='numberOf'></div>.
    <div id='reggeli' class='numberOf'></div>.
    <div id='koztes' class='numberOf'></div>      
</div> -->