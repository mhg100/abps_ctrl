<?php

    include 'php/php_func.php';
    $nombre    = $_POST['nombres'];
    $apellido  = $_POST['apellidos'];
    $cedula    = $_POST['cedula'];
    $camp      = $_POST['selectorCampaign'];
    $cantagentes=$_POST['cantagentes'];

    $conn = fSesion();
    $sql = "insert into coordinadores (nombres_coordinador, apellidos_coordinador, cc_coordinador, pass_coordinador, campaign_coordinador, ultimoacceso_coordinador, cantidad_agentes_coordinador) values ('".$nombre."', '".$apellido."', '".$cedula."', 'password', '".$camp."', GETDATE(), '".$cantagentes."')";
    $stmt = sqlsrv_query($conn, $sql);

    header('Location: default-opman.php?ic=1');

?>