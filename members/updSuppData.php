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
        exit("Nem siker�lt az �j bejegyz�s updatel�se!");
    }

    $sql = "INSERT INTO SuppData (id, tapkiegId, consultantId, adagolas, link) 
    VALUES ('','$id','$consId','$adagolas','$link')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        print mysqli_error($conn); 
        exit("Nem siker�lt az �j bejegyz�s updatel�se!");
    }

    mysqli_free_result($result);
    mysqli_close($conn);

?>