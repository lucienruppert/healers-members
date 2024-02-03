<?php   

include('functions_new.php');
$sql = "SELECT etrend_tervezo.id, filtersData.filterId, filtersData.filterState
FROM etrend_tervezo
LEFT JOIN filtersData
ON etrend_tervezo.id = filtersData.etelId
ORDER BY elnevezes ASC";
$result = mysqli_query($conn, $sql);
$receptFilterData = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

$i=0;
$finalArray = '';
$receptData = '';
foreach ($receptFilterData as $receptData) {
    $finalArray .= $receptFilterData[$i][0];
    $finalArray .= '@';
    $finalArray .= $receptFilterData[$i][1];
    $finalArray .= '@';
    $finalArray .= $receptFilterData[$i][2];
    $finalArray .= '~';
    $i++;
}
rtrim($finalArray,'~');
print ($finalArray);

?>

