<?php   

session_start();
include('functions_new.php');
include('functions.php');
$consultantId = ($userObject['ID']);
$diseaseId = ($_GET['diseaseId']);

$sql = "SELECT * FROM diseaseConnections WHERE diseaseId = $diseaseId AND consultantId = $consultantId";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);
mysqli_free_result($result);
mysqli_close($conn);

if (sizeof($data) != 0) 
	echo json_encode($data);
else 
	echo json_encode([]);

?>

