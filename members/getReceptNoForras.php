<?php   

include('functions_new.php');
$sql = "SELECT * FROM etrend_tervezo WHERE alkoto = '' ORDER BY elnevezes ASC";
$result = mysqli_query($conn, $sql);
$receptFilterData = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);
?>