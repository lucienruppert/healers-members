<?php   

include('functions_new.php');

//AZ ÖSSZES RECORD LEHOZÁSA DB-BÕL
$sql = 'SELECT * FROM etrend_tervezo ORDER BY elnevezes ASC';
$result = mysqli_query($conn, $sql);
$recept_all = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($conn);

?>