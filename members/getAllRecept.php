<?php   

include('functions_new.php');
$sql = 'SELECT * FROM etrend_tervezo ORDER BY elnevezes ASC';
$result = mysqli_query($conn, $sql);
$recept_all = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

//print_r($recept_all);

?>