<!DOCTYPE html>
<head>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="baseStyle2.css">
    <link rel="stylesheet" href="receptbevitel.css">
</head>
<body>
<!-- JOBB OLDALI RECEPT LISTA -->
    <div id="list">
    <form id='form' method='post'>
        <?php 
        include('getReceptList.php'); 
        foreach($recept_all as $recept){ ?>
            <div class='loadRecept'>
            <input type='button' class='elnevezes' value='<?php echo htmlspecialchars($recept['elnevezes']) . ' (' . $recept['id'] . ')' ; ?>'>
            <input type='hidden' class='id' value='<?php echo ($recept['id']); ?>'>
            </div>
        <?php }?> 
    </form>
    </div> 
</body>
</html>
