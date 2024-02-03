<?php   

// ***********************************************************
// ******** NYISD MEG MAGYAR KÓDOLÁSBAN!!! ******************* 
// *********************************************************** 

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

include('functions_new.php');

$id = $_POST['id'];
//AZ UTOLSÓ RECORD LEHOZÁSA DB-BÕL

//HA VAN ID-JA, AKKOR EZ EGY MÁR LÉTEZÕ RECEPT
if ($id != ''){

    $id = intval($id);
    $query = 'WHERE id =';
    $query .= $id;

} else {

    //HA NINCS, AKKOR EZ EGY ÚJ RECEPT
    $query = 'ORDER BY id DESC LIMIT 1';
}
 
$sql = 'SELECT * FROM etrend_tervezo ';
$sql .= $query;
$result = mysqli_query($conn, $sql);
//$recept_last = mysqli_fetch_all($result, MYSQLI_ASSOC);
$recept_last = $result -> fetch_assoc();
mysqli_free_result($result);
mysqli_close($conn);

//AZ UPDATELT_RECEPTBE SZEDTÜK SZÉT AZ EGY SOROS TÖMBNEK KINÉZÕ STRINGET, HOGY A RÉSZEI LEGYENEK EGY JELLEL ELVÁLASZTVA
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

//EZ A SOR MINDENKÉPPEN KELL IDE, PRINTTEL ADJUK ÁT A JS-NEK A RETÖRNÖLT TÖMBNEK KINÉZÕ STRINGET!
print($updatelt_recept);

?>
