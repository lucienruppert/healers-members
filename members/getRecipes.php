<?php   

    include('functions_new.php');
    $sql = "SELECT elnevezes FROM etrend_tervezo ORDER BY elnevezes ASC";
    $result = mysqli_query($conn, $sql);
    $recipes = mysqli_fetch_all($result);
    mysqli_free_result($result);
    mysqli_close($conn);
    echo json_encode($recipes)

?>