<?php   

include('db_connect.php');

// //�J RECORD L�TREHOZ�SA AZ ADATB�ZISBAN
function letrehoz($conn){
    $sql = 'INSERT INTO etrend_tervezo (id,elnevezes,hozzavalok,elkeszites,alkoto) VALUES ("NULL","","","","");';
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error(); 
        exit("Nem siker�lt az �j bejegyz�s l�trehoz�sa!");
    }
}

letrehoz($conn);
mysqli_close($conn);

?>