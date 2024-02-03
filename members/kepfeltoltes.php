<?php   

$id = $_POST['id'];
echo 'id: ' . $id;
//FELTÖLTÉS
$target_dir = 'img_uploads/';
$target_file = $target_dir . basename($_FILES['kep']['name']);
if (move_uploaded_file($_FILES['kep']['tmp_name'], $target_file)) {
echo 'sikerült'; }
$kep = basename($_FILES['kep']['name']);
//AZ ELÉRÉS BEÍRÁSA AZ ADATBÁZISBA
include('functions_new.php');
$sql = "UPDATE etrend_tervezo SET kep = '$kep' WHERE id = $id"; 
$result = mysqli_query($conn, $sql);
mysqli_close($conn);

?>