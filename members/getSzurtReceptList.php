<?php   

$szuro = ($_POST['szuro']);

include('functions_new.php');
$query = "SELECT id FROM etrend_tervezo WHERE hozzavalok LIKE '%$szuro%'";
$result = mysqli_query($conn, $query);
$szurt_receptek = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

$IdList = '';
$list = '';
foreach ($szurt_receptek as $IdList) {
    $list .= $IdList[0];
    $list .= '@';
}
print ($list);

?>