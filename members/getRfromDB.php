<?php   

// ***********************************************************
// ******** NYISD MEG MAGYAR K�DOL�SBAN!!! ******************* 
// *********************************************************** 

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

include('functions_new.php');

$id = $_POST['id'];
//AZ UTOLS� RECORD LEHOZ�SA DB-B�L

//HA VAN ID-JA, AKKOR EZ EGY M�R L�TEZ� RECEPT
if ($id != ''){

    $id = intval($id);
    $query = 'WHERE id =';
    $query .= $id;

} else {

    //HA NINCS, AKKOR EZ EGY �J RECEPT
    $query = 'ORDER BY id DESC LIMIT 1';
}
 
$sql = 'SELECT * FROM etrend_tervezo ';
$sql .= $query;
$result = mysqli_query($conn, $sql);
//$recept_last = mysqli_fetch_all($result, MYSQLI_ASSOC);
$recept_last = $result -> fetch_assoc();
mysqli_free_result($result);
mysqli_close($conn);

//AZ UPDATELT_RECEPTBE SZEDT�K SZ�T AZ EGY SOROS T�MBNEK KIN�Z� STRINGET, HOGY A R�SZEI LEGYENEK EGY JELLEL ELV�LASZTVA
$updatelt_recept = '';
    $updatelt_recept = $recept_last['elnevezes'];
    $updatelt_recept .= '@';
    $updatelt_recept .= $recept_last['hozzavalok'];
    $updatelt_recept .= '@';
    $updatelt_recept .= $recept_last['elkeszites'];
    $updatelt_recept .= '@';
    $updatelt_recept .= $recept_last['alkoto'];
    $updatelt_recept .= '@';
    $updatelt_recept .= $recept_last['id'];
    $updatelt_recept .= '@';
    $updatelt_recept .= $recept_last['kep'];
    $updatelt_recept .= '@';
    $updatelt_recept .= $recept_last['adag'];

//EZ A SOR MINDENK�PPEN KELL IDE, PRINTTEL ADJUK �T A JS-NEK A RET�RN�LT T�MBNEK KIN�Z� STRINGET!
print($updatelt_recept);

?>
