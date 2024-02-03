<?php   

$consId = intval(($_POST['consId']));

include('functions_new.php');
$query = "SELECT userName FROM Questionarie_Fills WHERE ConsultantId = $consId";
$result = mysqli_query($conn, $query);
$clients = mysqli_fetch_all($result);
//$clients = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($conn);

echo json_encode($clients);

?>