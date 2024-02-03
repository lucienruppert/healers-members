<?php   

    include('functions_new.php');
    $clientId = $_GET['clientId'];
    $clientId = intval($clientId);

    $sql = "SELECT * FROM clientProtocolData WHERE clientId = '$clientId'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_close($conn);
    echo json_encode($data);

?>