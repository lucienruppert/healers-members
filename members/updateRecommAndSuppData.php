<?php   

    session_start();
    include('functions_new.php');
    include('functions.php');
    $consultantId = ($userObject['ID']);
    $diseaseId = ($_POST['diseaseId']);
    $diseaseId = intval($diseaseId);

    $supplements = ($_POST['supplements']);
    $supplements = array_map('intval', $_POST['supplements']);
    $supplements_string = implode(',', $supplements);

    $recommendations = ($_POST['recommendations']);
    $recommendations = array_map('intval', $_POST['recommendations']);
    $recommendations_string = implode(',', $recommendations);

    $sql = "DELETE FROM diseaseConnections WHERE diseaseId = '$diseaseId'";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerult!");
    }

    $sql = "INSERT INTO diseaseConnections (id, diseaseId, consultantId, recommendationArray, supplementArray) 
    VALUES ('','$diseaseId','$consultantId','$recommendations_string','$supplements_string')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerult!");
    }

    mysqli_free_result($result);
    mysqli_close($conn);

?>