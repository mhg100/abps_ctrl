<?php
    include 'php/php_func.php';

    $usuario = $_POST['id'];
    $password = $_POST['password'];
    $conexion = fSesion();
    
    $comilladoble = '"';
    $prohibidos = array("'", $comilladoble, ";", "=");

    $usuario = str_replace($prohibidos, "-", $usuario);
    $password = str_replace($prohibidos, "-", $password);
    
    session_start();

    $tabla = '';
    $columna='';
    $passw = '';

    if($_SESSION['rol'] == '0')
    {
        $tabla    = 'dbo.admins';
        $columna  = 'id_admin';
        $passw    = 'pass_admin';
        $apellido = 'apellidos_admin';
        $fecha    = 'ultimoacceso_admin';
    }
    else if($_SESSION['rol'] == '1')
    {
        $tabla = 'dbo.coordinadores';
        $columna='id_coordinador';
        $passw = 'pass_coordinador';
        $apellido = 'apellidos_coordinador';
        $fecha = 'ultimoacceso_coordinador';
    }
    $sql = "select * from ".$tabla." where ".$columna." = '".$usuario."' and ".$passw." COLLATE Modern_Spanish_CS_AS = '".$password."' order by ".$apellido;
    $qry = sqlsrv_query($conexion, $sql, array(), array( "Scrollable" => 'static' ));
    $resultado = sqlsrv_fetch_array($qry);

    if($resultado[0] == $usuario)//--------------------------------acceso autorizado
    {
        $sql = "update ".$tabla." set ".$fecha." = GETDATE() where ".$columna." = '".$usuario."'";
        $qry = sqlsrv_query($conexion, $sql, array(), array( "Scrollable" => 'static' ));
        if($_SESSION['rol'] == '1')
        {
            $_SESSION['nombres']  = ucwords(mb_strtolower($resultado['nombres_coordinador'],  'UTF-8'));
            $_SESSION['apellidos']= ucwords(mb_strtolower($resultado['apellidos_coordinador'],'UTF-8'));
            $_SESSION['id']= $usuario;
            $_SESSION['horaAcceso'] = time();
            header('Location: defaultcoord.php');
        }
        else if ($_SESSION['rol'] == '0')
        {
            $_SESSION['nombres']  = ucwords(mb_strtolower($resultado['nombres_admin'],  'UTF-8'));
            $_SESSION['apellidos']= ucwords(mb_strtolower($resultado['apellidos_admin'],'UTF-8'));
            $_SESSION['horaAcceso'] = time();
            header('Location: default.php');
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