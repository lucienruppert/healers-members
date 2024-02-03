<?php   

session_start();
include('functions_new.php');
include('functions.php');
$ids = array_map('intval', $_POST['ids']);
$ids_string = implode(',', $ids);

$sql = "SELECT id, elnevezes FROM etrend_tervezo WHERE id IN ($ids_string)
ORDER BY elnevezes ASC";
$result = mysqli_query($conn, $sql);
$mealData = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

echo json_encode($mealData);

?>

