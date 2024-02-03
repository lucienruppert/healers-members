<?php
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    $language = $_SESSION['language'];
    session_start();
    session_destroy();
    session_start();
    $_SESSION['language'] = $language;
    print "<script>parent.window.location.href='index.php';</script>";
?>