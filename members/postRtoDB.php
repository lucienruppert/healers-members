<?php   

include('functions_new.php');

// ÚJ RECORD Létrehozása és az új ADATOK mentése a DB-be
    $elnevezes = ($_POST['elnevezes']);
    $hozzavalok = ($_POST['hozzavalok']);
    $elkeszites = ($_POST['elkeszites']);
    $alkoto = ($_POST['alkoto']);
    $adag = ($_POST['adag']);

    $sql = "INSERT INTO etrend_tervezo (id,elnevezes,hozzavalok,elkeszites,alkoto,kep,adag) VALUES ('null','$elnevezes','$hozzavalok','$elkeszites','$alkoto','', '$adag')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error(); 
        exit("Nem sikerï¿½lt az ï¿½j bejegyzï¿½s lï¿½trehozï¿½sa!");
    }

    mysqli_close($conn);

?>