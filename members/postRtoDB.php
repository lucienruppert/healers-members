<?php   

include('functions_new.php');

// �J RECORD L�trehoz�sa �s az �j ADATOK ment�se a DB-be
    $elnevezes = ($_POST['elnevezes']);
    $hozzavalok = ($_POST['hozzavalok']);
    $elkeszites = ($_POST['elkeszites']);
    $alkoto = ($_POST['alkoto']);
    $adag = ($_POST['adag']);

    $sql = "INSERT INTO etrend_tervezo (id,elnevezes,hozzavalok,elkeszites,alkoto,kep,adag) VALUES ('null','$elnevezes','$hozzavalok','$elkeszites','$alkoto','', '$adag')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error(); 
        exit("Nem siker�lt az �j bejegyz�s l�trehoz�sa!");
    }

    mysqli_close($conn);

?>