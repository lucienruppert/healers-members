<?php   

//AZ EREDETI MEGOLDÁS - CSAK COUNT
// include('functions_new.php');
// $sql = 'SELECT id FROM etrend_tervezo';
// $result = mysqli_query($conn, $sql);
// $totalcount = mysqli_num_rows($result);
// mysqli_free_result($result);
// mysqli_close($conn);

// FÓKUSZÁLTABB MEGOLDÁS - CSAK COUNT
// include('functions_new.php');
// $sql = 'SELECT COUNT(id) FROM etrend_tervezo';
// $result = mysqli_query($conn, $sql);
// $totalcount = mysqli_fetch_row($result);
// mysqli_free_result($result);
// mysqli_close($conn);

//print_r($totalcount);

// $count = sizeof($receptIdList);
// print $count;
// print "@";

include('functions_new.php');
$query = "SELECT id FROM etrend_tervezo";
$result = mysqli_query($conn, $query);
$receptIdList = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);

$IdList = '';
$list = '';
foreach ($receptIdList as $IdList) {
    $list .= $IdList[0];
    $list .= '@';
}

print ($list);

?>