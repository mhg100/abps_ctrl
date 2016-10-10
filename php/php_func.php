<?php
    function fSesion()
    {
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
    function fTimeStamp()
    {
        if(isset($_SESSION['horaAcceso']))
        {
            if (time() - $_SESSION['horaAcceso'] > 600)
            {
                unset(
                    $_SESSION['nombres'],
                    $_SESSION['apellidos'],
                    $_SESSION['horaAcceso'],
                    $_SESSION['ns']
                    );
                header('Location: logout.php?mtv=1');
            }
            else $_SESSION['horaAcceso'] = time();
        }
    }
    function initHTML($environment)
    {
        $tipoUsuario = '';
        
        if($environment == 0){
            $tipoUsuario = 'Administrador de coordinadores';
        }else if($environment == 1){
            $tipoUsuario = 'Coordinador';
        }
        //172.27.32.134 IP control
        return '
        
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <title>'.$tipoUsuario.' - Inicio de sesión</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
            <script type="text/javascript" src="js/charts.loader.js"></script>
            <script src="js/jquery.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
        </head>
        
        ';
    }
    function llamarPieChart($datos, $width, $height)
    {
        echo    '<script type="text/javascript">';
        echo        "google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                          ['Campaña', '% de diademas'],
                          ['Colsanitas',           11],
                          ['ETB',                   2],
                          ['UARIV',                 2],
                          ['Cafam',                 2],
                          ['Acueducto',             2],
                          ['Familias en acción',    1],
                          ['Icetex',                2],
                          ['DPS',                   7]
                        ]);

                        var options = {
                            title: '',
                            width: ".$width.",
                            height: ".$height.",
                            backgroundColor: { fill:'transparent' },
                            is3D: false
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('tortaOperaciones'));
                        chart.draw(data, options);
                    }
                </script>";

    }
    function llamarAreaChart($datos, $width, $height)
    {
        echo    '<script type="text/javascript">';
        echo        "google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Year', 'Sales', 'Expenses'],
                            ['2013',  1000,      400],
                            ['2014',  1170,      460],
                            ['2015',  660,      1120],
                            ['2016',  1030,      540]
                        ]);
                        var options = {
                            title: '',
                            hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
                            vAxis: {minValue: 0},
                            width: ".$width.",
                            height: ".$height.",
                            backgroundColor: { fill: 'transparent' },
                            is3D: false
                        };
                        var chart = new google.visualization.AreaChart(document.getElementById('movimientos'));
                        chart.draw(data, options);
                    }
                </script>";
    }
    function llamarCantidadOperaciones($datos, $width, $height)
    {
        //funcion para tener toda la informacion 
    }
    function consultarOperaciones()
    {
        $conexion = fSesion();
    }
    function validaEstadoLogin()
    {
        
        if(isset($_SESSION['ns']))
        {
            if($_SESSION['ns'] == 1)
            {
                echo '
    <label class="alert alert-danger col-md-8">
        <strong>Usuario o clave incorrectos</strong>
    </label>
                ';
            }
            else if($_SESSION['ns'] == 0)
            {
                echo '
    <label class="alert alert-success col-md-8">
        <strong>logueado</strong>
    </label>
        ';
            }
            else if($_SESSION['ns'] == 3)
            {
                echo '
    <label class="alert alert-danger col-md-8">
        <strong>Error al iniciar sesion (codigo 0x8160)</strong>
    </label>
                ';
            }
            else echo '';
        }
        else if($_GET['ns'] == 2)
        {
            echo '
<label class="alert alert-warning col-md-8">
    <strong>Sesión cerrada por inactividad</strong>
</label>
            ';
            }
        else if($_GET['ns'] == 4)
        {
            echo '
        <label class="alert alert-warning col-md-8">
            <strong>Sesión finalizada</strong>
        </label>
            ';
        }
    }
    function cambioDeUsuario(){
        
    }
?>