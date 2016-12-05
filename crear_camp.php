<?php

    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    comprobarAdmin();
    $id = correccionTexto($_POST['idcamp']);
    $nombre = correccionTexto($_POST['nombre']);
    $sede = strtolower(correccionTexto($_POST['sede']));
    
    echo $id."\xA".$nombre."\xA".$sede."\xA";

    $conn = fSesion();
    //$sql = "insert into campaigns values ('92', 'Sodimac', 'bogdor');";
    $sql = "insert into campaigns values ('".$id."', '".$nombre."', '".$sede."')";
    $stmt = sqlsrv_query($conn, $sql);
    if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
        }
    }
    if(!$stmt)
    {
        header("Location: defaultcamp.php?ic=1&ag=0");
    }
    else
    {
        header("Location: defaultcamp.php?ic=1&ag=1");
    }
?> 