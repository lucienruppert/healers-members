<?php   

include('functions_new.php');
$sql = 'SELECT * FROM Questionarie_Root_Causes ORDER BY Name ASC';
$result = mysqli_query($conn, $sql);
$megoldandok_all = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

?>