<?php   

    session_start();
    include('functions_new.php');
    include('functions.php');
    $consultantId = $userObject['ID'];
    $protocolList = $_GET['protocolList'];
    
    $sql = "SELECT * FROM diseaseConnections WHERE diseaseId IN ($protocolList) AND consultantId = $consultantId";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);
    echo json_encode($data);

?>