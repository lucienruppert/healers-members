<?php   

include('functions_new.php');
$sql = "SELECT etrend_tervezo.id, etrend_tervezo.elnevezes, etrend_tervezo.hozzavalok, etrend_tervezo.elkeszites, etrend_tervezo.alkoto, etrend_tervezo.kep, filtersData.filterId, filtersData.filterState
FROM etrend_tervezo
LEFT JOIN filtersData
ON etrend_tervezo.id = filtersData.etelId
ORDER BY elnevezes ASC";
$result = mysqli_query($conn, $sql);
$receptFilterData = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

//print_r($receptFilterData);

?>

