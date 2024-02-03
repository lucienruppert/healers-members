<?php   

    include('functions_new.php');
    $sql = 'SELECT * FROM Questionarie_Root_Causes ORDER BY Name ASC';
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);
    echo json_encode($data);

?>