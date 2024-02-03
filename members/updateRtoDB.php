<?php   

include('functions_new.php');

$elnevezes = ($_POST['elnevezes']);
$hozzavalok = ($_POST['hozzavalok']);
$elkeszites = ($_POST['elkeszites']);
$alkoto = ($_POST['alkoto']);
$adag = ($_POST['adag']);
$id = ($_POST['id']);
$id = intval($id);

$sql = "UPDATE etrend_tervezo SET elnevezes='$elnevezes',hozzavalok='$hozzavalok',elkeszites='$elkeszites',alkoto='$alkoto',adag='$adag' WHERE id = $id";
$result = mysqli_query($conn,$sql);
if(!$result){
    print mysqli_error(); 
    exit("Nem sikerült az új bejegyzés updatelése!");
}

mysqli_close($conn);

?>