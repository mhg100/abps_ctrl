<?php
    include 'php/php_func.php';
    session_start();

    $usuario        = $_POST['id'];
    $password       = $_POST['password'];
    $conexion       = fSesion();
    $comilladoble   = '"';
    $prohibidos     = array("'", $comilladoble, ";", "=");
    $usuario        = str_replace($prohibidos, "-", $usuario);
    $password       = str_replace($prohibidos, "-", $password);
    $dirip          = $_SERVER['REMOTE_HOST'];
    $tabla          = 
    $columna        = 
    $passw          = 
    $ip             = '';
    

    /*if($_SESSION['rol'] == '0'){
        $tabla      = 'dbo.admins';
        $columna    = 'id_admin';
        $passw      = 'pass_admin';
        $apellido   = 'apellidos_admin';
        $fecha      = 'ultimoacceso_admin';
        $ip         = 'ultimaip_admin';
    }
    else if($_SESSION['rol'] == '1'){
        $tabla      = 'dbo.coordinadores';
        $columna    = 'id_coordinador';
        $passw      = 'pass_coordinador';
        $apellido   = 'apellidos_coordinador';
        $fecha      = 'ultimoacceso_coordinador';
        $ip         = 'ultimaip_coordinador';
    }*/

    ///////--------------------Nuevo login--------------------//////

    if($usuario[0] == "9"){
        $_SESSION['rol'] = '0';
        $tabla           = 'dbo.admins';
        $columna         = 'id_admin';
        $passw           = 'pass_admin';
        $apellido        = 'apellidos_admin';
        $fecha           = 'ultimoacceso_admin';
        $ip              = 'ultimaip_admin';
    }
    else if($usuario[0] == "1"){
        $_SESSION['rol'] = '1';
        $tabla           = 'dbo.coordinadores';
        $columna         = 'id_coordinador';
        $passw           = 'pass_coordinador';
        $apellido        = 'apellidos_coordinador';
        $fecha           = 'ultimoacceso_coordinador';
        $ip              = 'ultimaip_coordinador';
    }
    else if($usuario[0] == "8"){
        $_SESSION['rol'] = '2';
        $tabla           = 'dbo.tecnicos';
        $columna         = 'id_tecnico';
        $passw           = 'pass_tecnico';
        $apellido        = 'apellidos_tecnico';
    }


    $sql = "select * from ".$tabla." where ".$columna." = '".$usuario."' and ".$passw." COLLATE Modern_Spanish_CS_AS = '".$password."' order by ".$apellido;
    $qry = sqlsrv_query($conexion, $sql, array(), array( "Scrollable" => 'static' ));
    $resultado = sqlsrv_fetch_array($qry);

    if($resultado[0] == $usuario){//-------------------------------acceso autorizado
        if($usuario[0] != "8"){
            $sql = "update $tabla set $fecha = GETDATE(), $ip = '$dirip' where $columna = '$usuario'";
            $qry = sqlsrv_query($conexion, $sql, array(), array( "Scrollable" => 'static' ));
            if($usuario[0] == "1"){
                $_SESSION['nombres']    = ucwords(mb_strtolower($resultado['nombres_coordinador'],  'UTF-8'));
                $_SESSION['apellidos']  = ucwords(mb_strtolower($resultado['apellidos_coordinador'],'UTF-8'));
                $_SESSION['campid']     = $resultado['campaign_coordinador'];
                $_SESSION['id']         = $usuario;
                header('Location: defaultcoord.php');
            }
            else if ($usuario[0] == "9"){
                $_SESSION['id']         = $usuario;
                $_SESSION['tipo']       = $resultado['rol_admin'];
                $_SESSION['nombres']    = ucwords(mb_strtolower($resultado['nombres_admin'],  'UTF-8'));
                $_SESSION['apellidos']  = ucwords(mb_strtolower($resultado['apellidos_admin'],'UTF-8'));
                $_SESSION['horaAcceso'] = time();
                header('Location: default.php');
            }
        }
        else if ($usuario[0] == "8"){
            $_SESSION['nombres']    = ucwords(mb_strtolower($resultado['nombres_tecnico'],  'UTF-8'));
            $_SESSION['apellidos']  = ucwords(mb_strtolower($resultado['apellidos_tecnico'],'UTF-8'));
            header('Location: tecdefault.php');
        }
        $_SESSION['ns'] = 0;
        $_SESSION['horaAcceso'] = time();
    }
    else
    {
        $_SESSION['ns'] = 1;//-------------------------------------acceso restringido
        header('Location: index.php');
    }
    sqlsrv_close($conn);
?>