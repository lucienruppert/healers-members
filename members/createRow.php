<?php   

//include('db_connect.php');
$conn = mysqli_connect('mysql.luciendelmar.com','luciendelmar','9CUiNwYzV3','luciendelmar');
if(!$conn){ echo 'Kapcsolati hiba: ' . mysqli_connect_error(); }

//echo $conn;

// //ÚJ ROW LÉTREHOZÁSA AZ ADATBÁZISBAN
function letrehoz($conn){
    $sql = 'INSERT INTO etrend_tervezo VALUES ()';
    //$sql = 'INSERT INTO etrend_tervezo (id,elnevezes,hozzavalok,elkeszites,alkoto) VALUES ("NULL","","","","");';
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error(); 
        exit("Nem sikerült az új bejegyzés létrehozása!");
    }
}

letrehoz($conn);
mysqli_close($conn);


?>