<?php   

    include('functions_new.php');
    $clientId = $_GET['clientId'];
    $nowProtocols = ($_GET['nowProtocols']);
    $laterProtocols = ($_GET['laterProtocols']);
    $nowProtocolSteps = ($_GET['nowProtocolSteps']);
    $protocolSupplementIds = ($_GET['protocolSupplementIds']);

    $sql = "DELETE FROM clientProtocolData WHERE clientId = '$clientId'";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerult!");
    }

    $sql = "INSERT INTO clientProtocolData (id, clientId, protocol_Now, protocol_Later, protocol_Recomm, protocol_Supp) 
    VALUES ('', '$clientId', '$nowProtocols','$laterProtocols','$nowProtocolSteps','$protocolSupplementIds')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerült!");
    }

    mysqli_free_result($result);
    mysqli_close($conn);

?>