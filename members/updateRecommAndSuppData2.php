<?php   

    session_start();
    include('functions_new.php');
    include('functions.php');
    $consultantId = ($userObject['ID']);
    $diseaseId = ($_GET['diseaseId']);
    $supplements = ($_GET['supplements']);
    $recommendations = ($_GET['recommendations']);

    $sql = "DELETE FROM diseaseConnections WHERE diseaseId = '$diseaseId'";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerult!");
    }

    $sql = "INSERT INTO diseaseConnections (id, diseaseId, consultantId, recommendationArray, supplementArray) 
    VALUES ('','$diseaseId','$consultantId','$recommendations','$supplements')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerült!");
    }

    mysqli_free_result($result);
    mysqli_close($conn);

?>