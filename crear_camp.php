<?php

    include 'php/php_func.php';
    fTimeStamp();
    comprobarAdmin();
    $id = $_POST['costos'];
    $nombre = $_POST['nombre'];
    $sede = $_POST['sede'];

    $comilladoble = '"';
    $prohibidos = array("'", $comilladoble, ";", "=");

    $id = str_replace($prohibidos, "-", $id);
    $nombre = str_replace($prohibidos, "-", $nombre);
    $sede = str_replace($prohibidos, "-", $sede);
    
    $conn = fSesion();
    $sql = "insert into campaigns (id_campaign, nombre_campaign, ubicacion_campaign) values ('".$id."', '".$nombre."', '".$sede."')";
    $stmt = sqlsrv_query($conn, $sql);
    if(!$stmt)
    {
        header("refresh:10; defaultcamp.php?ag=0");
    }
    else
    {
        header("refresh:10; defaultcamp.php?ag=1");
    }
?> 