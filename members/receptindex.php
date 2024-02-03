<?php
    session_start();
    include_once('functions_new.php');
    include_once('functions.php');
    include_once('getAllFilters.php');
    include_once('getAllReceptFilterData.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }

    if (isset($_POST['kep_filter'])) {
        require_once ('getReceptNoPic.php');
        $hide = "style='display:none'";
    } elseif (isset($_POST['hozzavalok_filter'])) {
        require_once ('getReceptNoHozzavalok.php');
        $hide = "style='display:none'";
    } elseif (isset($_POST['elkeszites_filter'])) {
        require_once ('getReceptNoElkeszites.php');
        $hide = "style='display:none'";
    } elseif (isset($_POST['forras_filter'])) {
        require_once ('getReceptNoForras.php');
        $hide = "style='display:none'";
    } elseif (isset($_POST['all'])) {
        require_once ('getAllReceptFilterData.php');
    } 

    if($userObject['status'] != 9 && $userObject['status'] != 8 ){   
        $hide = "style='display:none'";
    }
?>
<html>
<head>
    <title>Recept indexel�</title>
    <?php include_once('headLinks.php'); ?>
    <link rel="stylesheet" href="receptindex.css">
    <link rel="stylesheet" href="baseStyle2.css">
<script type="text/javascript" src="receptindex.js"></script>
</head>
<body>
<?php include("adminmenu.php") ?>
<div class='grid'>
    <div class='list'>
        <form id='form' method='post' action=''>
            <div class='filters'>
                <input class='filter' name='all' type="submit" Value="All">
                <input class='filter' name='kep_filter' type="submit" Value="No K�p">
                <input class='filter' name='hozzavalok_filter' type="submit" Value="No Hozz�val�k">
                <input class='filter' name='elkeszites_filter' type="submit" Value="No Elk�sz�t�s">
                <input class='filter' name='forras_filter' type="submit" Value="No Forr�s">
            </div>
            <div class="titleRow"  <?php echo ($hide); ?>>
                <div class='firstColumn'></div>
                <div class='buttonTitles'>F��tkez�s</div>
                <div class='buttonTitles'>Reggeli</div>
                <div class='buttonTitles'>K�ztes</div>
                <div class='buttonTitles'>Desszert</div>
                <div class='buttonTitles'>Leves</div>                
                <div class='buttonTitles'>F��tel</div>                
                <div class='buttonTitles'>Smoothie</div>   
                <div class='buttonTitles'>K�ret</div>          
            </div>
    </div>
    <div class="listGrid">
            <?php 
            $i = 0;
            $actualId = 0;
            $tempArray[] = '';
            foreach($receptFilterData as $recept){ 
                //Megvizsg�lom az aktu�lis recept ID-t, �s a nevet csak egyszer �rom ki, a v�ltoz�skor (azaz csak egyszer megy�nk bele az IF-be, ezzel nem engedek duplik�ci�kat
                if ($recept[0] != $actualId) {
                    $i++;
                    //Az el�z� �tel radio gombjait kirakjuk
                    if ($actualId != 0) {
                        print "<div class='filterRow' $hide>";
                        for ($e=0; $e < sizeof($tempArray); $e++ ) {
                        print $tempArray[$e];
                        }
                        print "</div>";
                    }
                    // Kirajzoljuk a gombokat �resen
                    if ($recept[0] != $actualId || $actualId == 0) {
                        $t = 0;
                        foreach($filters_all as $filter){ 
                            $tempArray[$t] = "<div class='filterSelect'" . $style . "' id='" . $filter[0] . "' alt='" . $recept[0] . "'><div id='" . $filter[0] . $recept[0] . "'><input type='radio' " . $checkedIgen . " class='button' name='" . $filter[0] . $recept[0] . "' id='" . $filter[0] . "igen" . $recept[0] . "'>Y-N<input type='radio' " . $checkedNem . " class='button'  name='" . $filter[0] . $recept[0] . "' id='" . $filter[0] . "nem" . $recept[0] . "'></div></div>";
                            $t++;
                        }
                    }
                    $actualId = $recept[0]; 
                    ?>
                    <div class='listItem'><b><?php echo $i . "." ?></b>
                        <input type='button' class='elnevezes' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[1])) . ' (' . $recept[0] . ')' ; ?>'>
                        <input type='hidden' class='id' value='<?php echo ($recept[0]); ?>'>
                        <input type='hidden' class='elnevezes2' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[1])); ?>'>
                        <input type='hidden' class='hozzavalok' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[2])); ?>'>
                        <input type='hidden' class='elkeszites' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[3])); ?>'>
                        <input type='hidden' class='forras' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[4])); ?>'>
                        <input type='hidden' class='kep' value='<?php echo iconv('UTF-8', 'ISO-8859-2', htmlspecialchars($recept[5])); ?>'>
                    </div>
          <?php } 
                        // A r�di�gombok be�ll�t�sa csekkoltra
                        $t = 0;
                        foreach($filters_all as $filter){ 
                            //Itt �ll�tom be, hogy az aktu�lis recept filterei igenek, vagy nemek
                            $checkedIgen = '';
                            $checkedNem = '';
                            if ($recept[6] == $filter[0] && $recept[7] == 1) { 
                                $checkedIgen = 'checked'; 
                            }
                            else if ($recept[6] == $filter[0] && $recept[7] == 0) { 
                                $checkedNem = 'checked'; 
                            }
                            if ($checkedIgen != '' || $checkedNem != '') {
                                $style = "style='background-color:white'";
                                $tempArray[$t] = "<div class='filterSelect'" . $style . "' id='" . $filter[0] . "' alt='" . $recept[0] . "'><div id='" . $filter[0] . $recept[0] . "'><input type='radio' " . $checkedIgen . " class='button' name='" . $filter[0] . $recept[0] . "' id='" . $filter[0] . "igen" . $recept[0] . "'>Y-N<input type='radio' " . $checkedNem . " class='button'  name='" . $filter[0] . $recept[0] . "' id='" . $filter[0] . "nem" . $recept[0] . "'></div></div>";
                            }
                            $style = '';
                            $t++;
                        }
            } 
            //Az utols� recept r�di� gombjainak ki�r�sa a tempArrayben t�rolt �rt�kekkel
            print "<div class='filterRow' $hide>";
                        for ($e=0; $e < sizeof($tempArray); $e++ ) {
                        print $tempArray[$e];
                        }
                        print "</div>";
            ?>
            </div> 
        </form>
    </div> 
</div>
</body>
</html>