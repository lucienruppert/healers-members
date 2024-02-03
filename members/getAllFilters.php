<?php   

include('functions_new.php');
//$sql = 'SELECT * FROM filters WHERE id=1';
$sql = 'SELECT * FROM filters';
$result = mysqli_query($conn, $sql);
$filters_all = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

//print_r($filters_all);

?>