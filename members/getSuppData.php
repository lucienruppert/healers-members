<?php   

session_start();
include('functions_new.php');
include('functions.php');
$consultantId = $userObject['ID'];
$id = $_POST['id'];

$sql = "SELECT * FROM SuppData WHERE tapkiegId = $id AND consultantId = $consultantId";
$result = mysqli_query($conn, $sql);
$suppData = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($conn);

echo json_encode($suppData)

?>

