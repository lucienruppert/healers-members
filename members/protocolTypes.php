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
<title>Protokoll összerakó</title>
<?php include_once('headLinks.php'); ?>
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
    .CategoryRow {
        display: grid;
        grid-auto-flow: column; 
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }
    .button {
        background-color: <?php echo $color ?>;
        color: white;
        font-size: 1rem;
        padding: 5px 20px 5px 20px;
        border-radius: 5px;
        cursor: pointer;
        border: none;
    }
</style>
<script type="text/javascript" src="defaultProtocols.js"></script>
</head>
<body>
<?php include("adminmenu.php") ?>
<div class='CategoryRow'>
    <input type='button' class='button' value='Összes'</div>
    <?php include_once('getAllAjanlasKategoria.php'); 
    foreach ($all_ajanlasKat as $Kategoria) {
        print "<input type='button' class='button' value='" . iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($Kategoria[1])) . "'>";
    }
    ?>
</div>
</body>
</html>
