<?php
    session_start();
    unset($_SESSION['svy_ser']);
    session_destroy();
    $http_referer = $_SERVER['HTTP_REFERER'];
    header("Location: $http_referer");
?>
