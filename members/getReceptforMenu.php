<?php   

$idList = $_POST['idList'];
include('functions_new.php');

$sql = 'SELECT * FROM etrend_tervezo WHERE id =';
for ($i=0; $i <= sizeof($idList)-2; $i++) {
$sql .= $idList[$i] . ' OR id = '; 
}
$sql .= $idList[sizeof($idList)-1];
$sql .= ' ORDER BY elnevezes ASC';
$result = mysqli_query($conn, $sql);
$recept_all = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

$recept = '';
$list = '';
foreach ($recept_all as $recept) {
    $list .= $recept[0];
    $list .= '@';
    $list .= $recept[1];
    $list .= '@';
    $list .= $recept[2];
    $list .= '@';
    $list .= $recept[3];
    $list .= '@';
    $list .= $recept[4];
    $list .= '@';
    $list .= $recept[5];
    $list .= '@';
    $list .= $recept[6];
    $list .= '~';
}
rtrim($list,'~');
print_r($list);

?>