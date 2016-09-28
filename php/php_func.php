<?php
    function fSesion($usuario, $clave){
        $db_srv = '172.27.48.125';
        $db_usr = 'merhengo';
        $db_psw = '10ceroun0';
        $db_name = 'abps_control';

        $connection_options = array('Database'=>'abps_control', 'UID'=>'merhengo', 'PWD'=>'10ceroun0');
        $conn = sqlsrv_connect($db_srv, $connection_options);

        if(!is_resource($conn)){
            session_start();
            $_SESSION['ns'] = 3;
            header('Location: index.php'); var_dump(sqlsrv_errors(SQLSRV_ERR_ALL)); exit(0);
        }
        
        return $conn;
    }
    
    function fTimeStamp($rol){
        if(isset($_SESSION['horaAcceso'])){
            if  ($_SESSION['horaAcceso']) unset(
                                                $_SESSION['nombres'],
                                                $_SESSION['apellidos'],
                                                $_SESSION['horaAcceso'],
                                                $_SESSION['ns']
                                                );
            else $_SESSION['horaAcceso'] = time();
        }
    }

    function initHTML($environment){
        $tipoUsuario = '';
        
        if($environment == 0){
            $tipoUsuario = 'Administrador de coordinadores';
        }else if($environment == 1){
            $tipoUsuario = 'Coordinador';
        }
        return '
        
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <title>'.$tipoUsuario.' - Inicio de sesión</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
            <script src="js/jquery.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
        </head>
        
        ';
    }
?>