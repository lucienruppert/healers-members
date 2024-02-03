<?php
    session_start();
    include_once('functions.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
 
?>
<!DOCTYPE html>
<head>
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
</head>
<body>
<?php include("adminmenu.php") ?>
<div class='grid'>
    <!-- BEVITEL -->
    <div id='form'>
        <form id='receptSave' method='post' target='iframe' action='kepfeltoltes.php' enctype='multipart/form-data'>
            <div><input type='text' size='63' id='elnevezes' placeholder='Elnevezés'></div>
            <div><textarea rows='15' cols='62' id='hozzavalok' placeholder='Hozzávalók'></textarea></div>
            <div><textarea rows='15' cols='62' id='elkeszites' placeholder='Elkészítés'></textarea></div>
            <div><input type='text' size='63' id='alkoto' placeholder='Alkotó'></div>
            <div><input type='file' size='63' id='kep' name='kep'></div>
            <div><input type='submit' name='kepfeltoltes' id='kepfeltoltes' style='display:block'></div>
            <div><input type="hidden" name="MAX_FILE_SIZE" value="10000000"></div>
            <div><input type='hidden' name='id' id='id'></div>
        </form>
    </div>
    <iframe id='iframe' name='iframe' style='display:block'></iframe>
</div>
</body>
</html>
