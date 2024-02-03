<?php   

    session_start();
    include('functions_new.php');
    include('functions.php');
    $consultantId = ($userObject['ID']);

    include('functions_new.php');
    $sql = "SELECT * FROM lifestyleRecommendations  WHERE consultantId = $consultantId ORDER BY category ASC";
    $result = mysqli_query($conn, $sql);
    $lifestyleRecommendations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);

    echo json_encode($lifestyleRecommendations);

?>