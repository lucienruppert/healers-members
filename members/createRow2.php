<?php   

include('db_connect.php');

// //ÚJ RECORD LÉTREHOZÁSA AZ ADATBÁZISBAN
function letrehoz($conn){
    $sql = 'INSERT INTO etrend_tervezo (id,elnevezes,hozzavalok,elkeszites,alkoto) VALUES ("NULL","","","","");';
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error(); 
        exit("Nem sikerült az új bejegyzés létrehozása!");
    }
}

letrehoz($conn);
mysqli_close($conn);

?>