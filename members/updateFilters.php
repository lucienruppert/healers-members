<?php   

include('functions_new.php');

$etelId = ($_POST['etelId']);
$etelId = intval($etelId);
$filterId = ($_POST['filterId']);
$filterId = intval($filterId);
$filterState = ($_POST['filterState']);
$filterState = intval($filterState);

// AZ ÚJ INFÓ BEILLESZTÉSE
$sql = "INSERT INTO filtersData (id, etelId, filterId, filterState) VALUES ('','$etelId','$filterId','$filterState')";
$result = mysqli_query($conn,$sql);
if(!$result){
    print mysqli_error(); 
    exit("Nem sikerült az új bejegyzés updatelése!");
}

//HA AZ ÉTELNEK MÁR VAN LÉTEZÕ (UGYANILYEN STÁTUSZÚ) BEJEGYZÉSE AZON A FILTEREN KORÁBBI SESSIONBÕL, AKKOR AZT TÖRÖLJÜK
$sql = "DELETE FROM filtersData WHERE etelId = $etelId && filterId = $filterId && filterState = $filterState && id != LAST_INSERT_ID()";
$result = mysqli_query($conn,$sql);
if(!$result){
    print mysqli_error(); 
    exit("Nem sikerült az új bejegyzés updatelése!");
}

mysqli_close($conn);

?>