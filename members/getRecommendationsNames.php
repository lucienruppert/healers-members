<?php   

    include('functions_new.php');
    $idList = $_GET['idList'];
    $sql = "SELECT * FROM lifestyleRecommendations WHERE id IN ($idList)";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);
    echo json_encode($data);

?>