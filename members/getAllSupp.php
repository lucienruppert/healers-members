<?php   

include('functions_new.php');
$sql = 'SELECT * FROM Supplements ORDER BY brand ASC';
$result = mysqli_query($conn, $sql);
$suppl_all = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($conn);

echo json_encode($suppl_all)

?>