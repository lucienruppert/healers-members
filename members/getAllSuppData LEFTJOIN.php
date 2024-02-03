<?php   

session_start();
include('functions_new.php');
include('functions.php');
$consultantId = $userObject['ID'];

$sql = "SELECT Supplements.id, Supplements.brand, Supplements.name, Supplements_connect.tapkiegId, Supplements_connect.consultantId, Supplements_connect.adagolas, Supplements_connect.link
FROM Supplements
LEFT JOIN Supplements_connect
ON Supplements.id = Supplements_connect.tapkiegId
-- WHERE Supplements_connect.consultantId = $consultantId
ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

$supplements_all = mysqli_fetch_all($result);
// $supplements_all = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($conn);

echo json_encode($supplements_all)

?>

