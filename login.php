<?php
    include 'php/php_func.php';

    $usuario = $_POST['id'];
    $password = $_POST['password'];
    $conexion = fSesion($usuario, $password);
    session_start();

    $tabla = '';
    $columna='';
    $passw = '';

    if($_SESSION['rol'] == '0'){
        $tabla = 'dbo.admins';
        $columna='id_admin';
        $passw = 'pass_admin';
    }else
        if($_SESSION['rol'] == '1'){
            $tabla = 'dbo.coordinadores';
            $columna='id_coordinador';
            $passw = 'pass_coordinador';
    }

    $sql = "SELECT * FROM ".$tabla." where ".$columna." = '".$usuario."' and ".$passw." like '".$password."'";
    $qry = sqlsrv_query($conexion, $sql, array(), array( "Scrollable" => 'static' ));
    $resultado = sqlsrv_fetch_array($qry);

    if($resultado[0] == $usuario)//--------------------------------acceso autorizado
    {
        if($_SESSION['rol'] == '1')
        {
            $_SESSION['nombres']  = $resultado['nombres_coordinador'];
            $_SESSION['apellidos']= $resultado['apellidos_coordinador'];
            header('Location: index.php');
        }
        else if($_SESSION['rol'] == '0')
        {
            $_SESSION['nombres']  = $resultado['nombres_admin'];
            $_SESSION['apellidos']= $resultado['apellidos_admin'];
            header('Location: index2.php');
        }
        $_SESSION['ns'] = 0;
    }
    else
    {
        $_SESSION['ns'] = 1;//-------------------------------------acceso restringido
        if($_SESSION['rol'] == '0')
        {
            header('Location: index2.php');
        }
        else if($_SESSION['rol'] == '1')
        {
            header('Location: index.php');
        }
    }
    sqlsrv_close($conn);
?>