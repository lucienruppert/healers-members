<?php   

include('functions_new.php');
$sql = 'SELECT * FROM jelentkezok ORDER BY vezeteknev ASC';
$result = mysqli_query($conn, $sql);
$allProUsers = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($conn);

?>