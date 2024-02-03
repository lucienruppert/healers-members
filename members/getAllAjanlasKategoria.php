<?php   

include('functions_new.php');
$sql = 'SELECT * FROM AjanlasKategoria';
$result = mysqli_query($conn, $sql);
$all_ajanlasKat = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

?>