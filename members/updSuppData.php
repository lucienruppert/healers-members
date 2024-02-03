<?php   

    include('functions_new.php');

    $adagolas = ($_POST['adagolas']);
    $link = ($_POST['link']);
    $consId = ($_POST['consId']);
    $id = ($_POST['id']);
    $id = intval($id);

    $sql = "DELETE FROM SuppData
    WHERE tapkiegId = '$id' AND consultantId = '$consId'";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerült az új bejegyzés updatelése!");
    }

    $sql = "INSERT INTO SuppData (id, tapkiegId, consultantId, adagolas, link) 
    VALUES ('','$id','$consId','$adagolas','$link')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem sikerült az új bejegyzés updatelése!");
    }

    mysqli_free_result($result);
    mysqli_close($conn);

?>