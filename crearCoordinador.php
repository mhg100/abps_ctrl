<?php

    include 'php/php_func.php';
    $nombre   = $_POST['nombres'];
    $apellido = $_POST['apellidos'];
    $camp = $_POST['campa'];
    
    $conn = fSesion();
    $sql = "insert into coordinadores (nombres_coordinador, apellidos_coordinador, pass_coordinador, campaign_coordinador, ultimoacceso_coordinador) values ('".$nombre."', '".$apellido."', 'password', '".$camp."', GETDATE())";
    $stmt = sqlsrv_query($conn, $sql);

    header('Location: default-opman.php')

?>