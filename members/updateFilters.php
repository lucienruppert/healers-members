<?php   

include('functions_new.php');

$etelId = ($_POST['etelId']);
$etelId = intval($etelId);
$filterId = ($_POST['filterId']);
$filterId = intval($filterId);
$filterState = ($_POST['filterState']);
$filterState = intval($filterState);

// AZ �J INF� BEILLESZT�SE
$sql = "INSERT INTO filtersData (id, etelId, filterId, filterState) VALUES ('','$etelId','$filterId','$filterState')";
$result = mysqli_query($conn,$sql);
if(!$result){
    print mysqli_error(); 
    exit("Nem siker�lt az �j bejegyz�s updatel�se!");
}

//HA AZ �TELNEK M�R VAN L�TEZ� (UGYANILYEN ST�TUSZ�) BEJEGYZ�SE AZON A FILTEREN KOR�BBI SESSIONB�L, AKKOR AZT T�R�LJ�K
$sql = "DELETE FROM filtersData WHERE etelId = $etelId && filterId = $filterId && filterState = $filterState && id != LAST_INSERT_ID()";
$result = mysqli_query($conn,$sql);
if(!$result){
    print mysqli_error(); 
    exit("Nem siker�lt az �j bejegyz�s updatel�se!");
}

mysqli_close($conn);

?>