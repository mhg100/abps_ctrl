<?php
    function fSesion()
    {
        $db_srv = '172.27.48.125';
        $db_usr = 'merhengo';
        $db_psw = '10ceroun0';
        $db_name = 'abps_control';

        $connection_options = array('Database'=>''.$db_name.'', 'UID'=>''.$db_usr.'', 'PWD'=>''.$db_psw.'');
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
        else if($_SESSION['nombres'] == '' || !isset($_SESSION['nombre']))
        {
            header('Location: logout.php?mtv=2');
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
            <meta http-equiv="Content-type" content="text/html; utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="css/bootstrap-select.css">
            <script type="text/javascript" src="js/charts.loader.js"></script>
            <script src="js/jquery.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/bootstrap.custom.js"></script>
            <script src="js/bootstrap-select.js"></script>
        </head>
        
        ';
    }
    function llamarPieChart($datos, $width, $height)
    {
        $conn = fSesion();
        $sql_ObtenerNombres  = "select nombre_campaign as nombre from campaigns";
        $stmt_ObtenerNombres = sqlsrv_query($conn, $sql_ObtenerNombres);

        
            //echo             "['".fetchNombreCampaign($row[''])."', " .fetchCantCampaign("13")."],";

        sqlsrv_free_stmt($stmt);
        
        echo '<script type="text/javascript">';
        echo     "google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                          ['Campaña', '% de diademas'],";

        while($campaigns = sqlsrv_fetch_array($stmt_ObtenerNombres, SQLSRV_FETCH_ASSOC))
        {
            $stmt_ObtenerCantCampaign = sqlsrv_query($conn, fetchCantCampaign($campaigns["nombre"]));
            while($cant = sqlsrv_fetch_array($stmt_ObtenerCantCampaign, SQLSRV_FETCH_ASSOC))
            {
                if($cant['total'] == NULL){
                    $cant['total'] = 0;
                }
                echo "
                          ['".$campaigns["nombre"]."', " .$cant['total']."],\r";
            }
        }
        sqlsrv_free_stmt($stmt_ObtenerNombres);
        sqlsrv_free_stmt($stmt_ObtenerCantCampaign);
        
        echo             "\r\n                          ['',                   '']
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
        </script>
                ";

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
    function validaEstadoLogin()
    {
        
        if(isset($_SESSION['ns']))
        {
            if($_SESSION['ns'] == 1)
            {
                unset($_SESSION['ns']);
                echo '
    <label class="alert alert-danger col-md-4 col-md-offset-6 text-center">
        <strong>Usuario o clave incorrectos</strong>
    </label>
                ';
            }
            else if($_SESSION['ns'] == 0)
            {
                if($_SESSION['rol'] == 0) header('location: default.php');
                else if($_SESSION['rol'] == 1) header('location: defaultcoord.php');
                
                /*
                echo '
    <label class="alert alert-success col-md-8">
        <strong>logueado</strong>
    </label>
        ';*/
            }
            else if($_SESSION['ns'] == 3)
            {
                echo '
    <label class="alert alert-danger col-md-4 col-md-offset-6 text-center">
        <strong>Error al iniciar sesion (codigo 0x8160)</strong>
    </label>
                ';
            }
            else echo '';
        }
        else if($_GET['ns'] == 2)
        {
            echo '
    <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">
        <strong>Sesión cerrada por inactividad</strong>
    </label>

            ';
            }
        else if($_GET['ns'] == 4)
        {
            echo '
        <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">
            <strong>Sesión finalizada</strong>
        </label>
            ';
        }
        else if($_GET['ns'] == 5)
        {
            echo '
        <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">
            <strong>Sesión no iniciada</strong>
        </label>
            ';
        }
    }
    function navbar()
    {
        echo '<nav class="navbar navbar-default nav-center" role="navigation">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                        </button><a class="navbar-brand" href="default.php">ADMin</a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="#">Diademas</a>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Coordinadores<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="default-opman.php?ic=0">Ver coordinadores</a></li>
                                    <li class="divider"></li>
                                    <li><a href="default-opman.php?ic=1">Crear</a></li>
                                    <li><a href="default-opman.php?ic=2">Modificar</a></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-default">
                                Buscar coordinador
                            </button>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <span class="caret"></span>
                                    <span class="glyphicon glyphicon-user"></span> ';
        
                                        echo $_SESSION['nombres'].' '.$_SESSION['apellidos'];
                                echo '
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>
                                    <li class="divider"></li>
                                    <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>';
    }
    function navbarCoordinadores()
    {
        echo '<nav class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                        </button><a class="navbar-brand" href="default.php">COOrd</a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="defaultdevice.php?ic=0">Ver lista de diademas</a></li>
                                    <li class="divider"></li>
                                    <li><a href="defaultdevice.php?ic=1">Crear diadema</a></li>
                                    <li><a href="cambios.php">Solicitud de cambio</a></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-default">
                                Buscar diadema
                            </button>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <span class="caret"></span>
                                    <span class="glyphicon glyphicon-user"></span> ';
        
                                        echo $_SESSION['nombres'].' '.$_SESSION['apellidos'];
                                echo '
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>
                                    <li class="divider"></li>
                                    <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>';
    }
    function crearCoordinadores()
    {
        
        echo '<form class="form-horizontal" role="form" action="crearCoordinador.php" method="post">';
        
        $conn = fSesion();
        $sql = "select nombre_campaign, id_campaign from campaigns order by nombre_campaign asc";
        $stmt = sqlsrv_query($conn, $sql);
        //$stmt = sqlsrv_query($conexion, $sql, array(), array( "Scrollable" => 'static'));
        echo '
                                    <div class="col-md-10 text-left col-md-offset-2">
                                    <p>&nbsp;</p>
        
                                                        <!--Formulario-->
                                        <div class="form-group">
                                            <label for="nombres" class="col-sm-2 control-label">Nombre(s):</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required autofocus autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="apellidos" class="col-sm-2 control-label">Apellidos: </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos" required autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cedula" class="col-sm-2 control-label">Cédula: </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula" required autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="campa" class="col-sm-2 control-label">Campaña:</label>
                                            <div class="col-md-4">
                                                <select id="selectorCampaign" name="selectorCampaign" class="selectpicker" data-live-search="true" title="Seleccione una campaña" required autocomplete="off">
                                                    ';
                                                    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
                                                    {
                                                        echo '<option value="'.$row['id_campaign'].'">'.$row['nombre_campaign'].'</option>
                                                        ';
                                                    }
                                                    sqlsrv_free_stmt($stmt);
                                          echo '</select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cant_agentes" class="col-sm-2 control-label">Cantidad de agentes: </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control bfh-number" name="cantagentes" id="cantagentes" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-default" id="cant_agentes" name="cant_agentes">
                                                    Agregar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
        ';
    }
    function verCoordinadores($edit)
    {
        $conn = fSesion();
        $sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
        $stmt1 = sqlsrv_query($conn, $sql1);
        $stmt2 = sqlsrv_query($conn, $sql2);
        if($stmt1 === false)
        {
            die(print_r( sqlsrv_errors(), true));
        }
        echo '<form class="form-horizontal" role="form">';
        //echo '<div class="form-group">';
        $index = 0;
        while($ppl = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC))
        {
            $id = $ppl['id'];
            $nombres   = ucwords($ppl['nombres']);
            $apellidos = ucwords($ppl['apellidos']);
            $campaign  = ucwords($ppl['idcampa']);
            $cantagentes=ucwords($ppl['cantagentes']);
            $nombreCampa=' ';
            $sql2 = "select nombre_campaign as nc from campaigns where id_campaign = ".$campaign."";
            $stmt2 = sqlsrv_query($conn, $sql2);
            while($cmpgn = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC))
            {
                $nombreCampa = ucwords($cmpgn['nc']);
            }
            $deshabilitar = '';
            if($edit == 1) $deshabilitar = '';
            else if($edit == 0) $deshabilitar = 'disabled';
            echo '
                                            <div class="form-group">
                                                <p>&nbsp;</p>
                                                <label for="nombres'.$index.'" class="col-sm-3 control-label">ID:</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="id'.$index.'" placeholder="ID del agente" required value="'.$id.'" '.$deshabilitar.'>
                                                </div>
                                                <label for="nombres'.$index.'" class="col-sm-3 control-label">Nombre(s):</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="nombres'.$index.'" placeholder="Nombre(s)" required value="'.$nombres.'" '.$deshabilitar.'>
                                                </div>
                                                <label for="apellidos'.$index.'" class="col-sm-3 control-label">Apellidos:</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="apellidos'.$index.'" placeholder="Apellidos" required value="'.$apellidos.'" '.$deshabilitar.'>
                                                </div>
                                                <label for="campaign'.$index.'" class="col-sm-3 control-label">Campaña:</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="campaign'.$index.'" placeholder="Campaña" required value="'.$nombreCampa.'" '.$deshabilitar.'>
                                                </div>
                                                <label for="campaign'.$index.'" class="col-sm-3 control-label">Cantidad de agentes: </label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="cantagentes'.$index.'" placeholder="Cantidad de agentes" required value="'.$cantagentes.'" '.$deshabilitar.'>
                                                </div>
                                            </div>
                ';
            $index++;
        }  
        echo '</form>';
        sqlsrv_free_stmt($stmt);
    }
    function fetchCantCampaign($nombreCampaign)
    {
        return "select sum(cantidad_agentes_coordinador) as total from coordinadores where campaign_coordinador = (select id_campaign from campaigns where nombre_campaign = '".$nombreCampaign."')";
    }
    function comprobarAdmin()
    {
        if($_SESSION['rol'] == 1)
        {
            header('Location: defaultcoord.php');
        }
    }
    function crearDiadema()
    {
        echo '<form class="form-horizontal" role="form" action="crear_diadema.php" method="post">';
        //echo '<div class="form-group">';
        $index = 0;
        echo '
                                    <div align="center">
                                    <p>&nbsp;</p>
        
                                                        <!--Formulario-->
                                        <div class="form-group">
                                            <label for="serial" class="col-md-4 control-label">Serial:</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="serial" name="serial" placeholder="Si no tiene serial, digite 00" required autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="nombres" class="col-md-4 control-label">Nombre(s) y apellidos o Dirección IP: </label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres o dirección IP" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="marca" class="col-md-4 control-label">Marca:</label>
                                            <div class="col-md-4">
                                                <select id="marca" name="marca" class="selectpicker" data-live-search="true" title="Seleccione una marca" width="200px" required>
                                                    <option value="Jabra">Jabra</option>
                                                    <option value="Plantronics">Plantronics</option>
                                                    <option value="China">China</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="serialnumber" class="col-sm-4 control-label">S/N: </label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control bfh-number" name="serialnumber" id="serialnumber" disabled required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-10" align="right">
                                                <fieldset>
                                                    <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: 15px;">Agregar</button>
                                                    ';
                                                    if(isset($_GET['err']))
                                                    {
                                                        if($_GET['err'] == 1)
                                                        {
                                                        echo    '<label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center">
                                                        <strong>Error: Serial duplicado</strong>
                                                    </label>';
                                                        }
                                                    }
        echo '
                                                </fieldset>
                                            </div>
                                    </form>
                                    <script>
                                        $("#marca").on("changed.bs.select", function (e) {
                                            var val = $("#marca").val();
                                            if(val == "Jabra") $( "#serialnumber" ).prop( "disabled", false );
                                            else $( "#serialnumber" ).prop( "disabled", true );
                                        });
                                    </script>
        ';
    }
?>