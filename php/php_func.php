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
    function fMongoDB()
    {
            $conn = new MongoClient();
            $db = $conn->ctrltest;
            return $db->diademas;
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
        echo '<nav class="navbar navbar-default nav-center" role="navigation">';
        echo '            <div class="navbar-header">';
        echo '                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">';
        echo '                    <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>';
        echo '                </button><a class="navbar-brand" href="default.php">ADMin</a>';
        echo '            </div>';
        echo '            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
        echo '                <ul class="nav navbar-nav">';
        echo '                    <li class="dropdown">';
        echo '                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>';
        echo '                        <ul class="dropdown-menu">';
        echo '                            <li><a href="device.php?ic=0">Ver todas las diademas</a></li>';
        echo '                            <li><a href="device.php?ic=0">Ver diademas por coordinador</a></li>';
        echo '                            <li><a href="device.php?ic=0">Ver diademas por campaña</a></li>';
        echo '                            <li><a href="cambios.php">Realizar cambio</a></li>';
        echo '                        </ul>';
        echo '                        ';
        echo '                        ';
        echo '                    </li>';
        echo '                    <li class="dropdown">';
        echo '                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Coordinadores<span class="caret"></span></a>';
        echo '                        <ul class="dropdown-menu">';
        echo '                            <li><a href="default-opman.php?ic=0">Ver coordinadores</a></li>';
        echo '                            <li class="divider"></li>';
        echo '                            <li><a href="default-opman.php?ic=1">Crear</a></li>';
        echo '                            <li><a href="default-opman.php?ic=2">Modificar</a></li>';
        echo '                        </ul>';
        echo '                    </li>';
        echo '                </ul>';
        echo '                <form class="navbar-form navbar-left" role="search">';
        echo '                    <div class="form-group">';
        echo '                        <input type="text" class="form-control">';
        echo '                    </div>';
        echo '                    <button type="submit" class="btn btn-default">';
        echo '                        Buscar coordinador';
        echo '                    </button>';
        echo '                </form>';
        echo '                <ul class="nav navbar-nav navbar-right">';
        echo '                    <li class="dropdown">';
        echo '                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">';
        echo '                            <span class="caret"></span>';
        echo '                            <span class="glyphicon glyphicon-user"></span> ';
                                          echo $_SESSION['nombres'].' '.$_SESSION['apellidos'];
        echo '                        </a>';
        echo '                        <ul class="dropdown-menu">';
        echo '                            <li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>';
        echo '                            <li class="divider"></li>';
        echo '                            <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>';
        echo '                        </ul>';
        echo '                    </li>';
        echo '                </ul>';
        echo '            </div>';
        echo '        </nav>';
    }
    function navbarCoordinadores()
    {
        echo '<nav class="navbar navbar-default" role="navigation">';
        echo '    <div class="navbar-header">';
        echo '        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">';
        echo '                    <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>';
        echo '                </button><a class="navbar-brand" href="default.php">COOrd</a>';
        echo '            </div>';
        echo '            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
        echo '                <ul class="nav navbar-nav">';
        echo '                    <li class="dropdown">';
        echo '                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>';
        echo '                        <ul class="dropdown-menu">';
        echo '                            <li><a href="defaultdevice.php?ic=0">Ver lista de diademas</a></li>';
        echo '                            <li class="divider"></li>';
        echo '                            <li><a href="defaultdevice.php?ic=1">Crear diadema</a></li>';
        echo '                            <li><a href="cambios.php">Solicitud de cambio</a></li>';
        echo '                        </ul>';
        echo '                    </li>';
        echo '                </ul>';
        echo '                <form class="navbar-form navbar-left" role="search">';
        echo '                    <div class="form-group">';
        echo '                        <input type="text" class="form-control">';
        echo '                    </div>';
        echo '                    <button type="submit" class="btn btn-default">';
        echo '                        Buscar diadema';
        echo '                    </button>';
        echo '                </form>';
        echo '                <ul class="nav navbar-nav navbar-right">';
        echo '                    <li class="dropdown">';
        echo '                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">';
        echo '                            <span class="caret"></span>';
        echo '                            <span class="glyphicon glyphicon-user"></span> ';
        echo ''                               .$_SESSION['nombres'].' '.$_SESSION['apellidos'];
        echo '                        </a>';
        echo '                        <ul class="dropdown-menu">';
        echo '                            <li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>';
        echo '                            <li class="divider"></li>';
        echo '                            <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>';
        echo '                        </ul>';
        echo '                    </li>';
        echo '                </ul>';
        echo '            </div>';
        echo '        </nav>';
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
    function verCoordinadores($edit, $camp)
    {
        $conn = fSesion();
        $sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
        $sql2 = "select nombre_campaign, id_campaign from campaigns order by nombre_campaign asc";
        $stmt2 = sqlsrv_query($conn, $sql2);
        $inject = "' or ''='";
        
        echo '<form class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="listarcampaign" class="col-sm-3 control-label">Listar por campaña:</label>
                    <div class="col-md-3">
                        <select id="selectorCampaign" name="selectorCampaign" class="selectpicker" data-live-search="true" title="Seleccione una campaña" required autocomplete="off">
                            <option value="'.$inject.'">Todas las campañas</option>
                            ';
                            while($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC))
                            {
                                echo '<option value="'.$row['id_campaign'].'">'.$row['nombre_campaign'].'</option>
                                ';
                            }
                            sqlsrv_free_stmt($stmt);
                  echo '</select>
                    </div>
                </div>
        ';
        
        $index = 0;
        
        if(isset($camp))
        {
            $sql1 = $sql1." where campaign_coordinador = '".$camp."'";
        }
        $stmt1 = sqlsrv_query($conn, $sql1);
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
        echo '
    </form>
        <script>
            $("#selectorCampaign").on("changed.bs.select", function (e) {
                var val = $("#selectorCampaign").val();
                if(val == "Todas las campañas")
                {
                    window.location.href = "default-opman.php?ic=0";
                }
                window.location.href = "default-opman.php?ic=0&camplist="+val;
            });
        </script>';
        sqlsrv_free_stmt($stmt1);
        sqlsrv_free_stmt($stmt2);
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
        $index = 0;
        echo '<form class="form-horizontal" role="form" action="crear_diadema.php" method="post">' . "\xA";
        echo '  <div align="center">' . "\xA";
        echo '                    <!--Formulario-->';
        echo '    <div class="form-group">' . "\xA";
        echo '      <label for="serial" class="col-md-4 control-label">Serial:</label>' . "\xA";
        echo '        <div class="col-md-6">' . "\xA";
        echo '          <input type="text" class="form-control" rel="serial" id="serial" name="serial" data-toggle="tooltip" autocomplete="off" placeholder="Si no tiene serial, digite 00" required autofocus>' . "\xA";
        echo '        </div>' . "\xA";
        echo '    </div>' . "\xA";
        echo '    <div class="form-group">' . "\xA";
        echo '      <label for="nombres" class="col-md-4 control-label">Nombre(s) y apellidos o Dirección IP: </label>';
        echo '        <div class="col-md-6">' . "\xA";
        echo '          <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres o dirección IP" required autocomplete="off">' . "\xA";
        echo '        </div>' . "\xA";
        echo '    </div>' . "\xA";
        echo '    <div class="form-group">' . "\xA";
        echo '      <label for="marca" class="col-md-4 control-label">Marca:</label>' . "\xA";
        echo '        <div class="col-md-4">' . "\xA";
        echo '          <select id="marca" name="marca" class="selectpicker" data-live-search="true" title="Seleccione una marca" width="200px" required>' . "\xA";
        echo '            <option value="Jabra">Jabra</option>' . "\xA";
        echo '            <option value="Plantronics">Plantronics</option>' . "\xA";
        echo '            <option value="China">China</option>' . "\xA";
        echo '          </select>' . "\xA";
        echo '        </div>' . "\xA";
        echo '    </div>' . "\xA";
        echo '    <div class="form-group">' . "\xA";
        echo '      <label for="serialnumber" class="col-sm-4 control-label">S/N: </label>' . "\xA";
        echo '        <div class="col-md-6">' . "\xA";
        echo '          <input type="text" class="form-control bfh-number" name="serialnumber" id="serialnumber" autocomplete="off" disabled required>' . "\xA";
        echo '        </div>' . "\xA";
        echo '    </div>' . "\xA";
        echo '    <div class="form-group">' . "\xA";
        echo '      <div class="col-md-10" align="right">' . "\xA";
        echo '        <fieldset>' . "\xA";
        $padding = 10;
        if(isset($_GET['ag']))
        {
            $padding = 25;
            if($_GET['ag'] == 0)
            {
                echo '        <label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
                echo '          <strong>Error: Serial '.$_GET["sd"].' duplicado</strong>' . "\xA";
                echo '        </label>' . "\xA";
            }
            else if($_GET['ag'] == 1)
            {
                echo '        <label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
                echo '          <strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>' . "\xA";
                echo '        </label>' . "\xA";
            }
        }
        echo '            <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>' . "\xA";
        echo '              </fieldset>' . "\xA";
        echo '            </div>' . "\xA";
        echo '         </form>' . "\xA";
        echo '         <script>' . "\xA";
        echo '             $("#marca").on("changed.bs.select", function (e) {' . "\xA";
        echo '                 var val = $("#marca").val();' . "\xA";
        echo '                 if(val == "Jabra") $( "#serialnumber" ).prop( "disabled", false );' . "\xA";
        echo '                 else $( "#serialnumber" ).prop( "disabled", true );' . "\xA";
        echo '             });' . "\xA";
        echo '         </script>' . "\xA";
    }
    function verDiadema()
    {
        $index = 0;
        $collection = fMongoDB();
        $res = array();
        $cursor = $collection->find(array('resumen.coordinador_id' => $_SESSION['id']));
        foreach ($cursor as $document)
            {   
                $temp = array($document["_id"], $document["Marca"], $document["serial"], $document["resumen"]);
                array_push($res, $temp);
            }

        for($i = 0; $i<count($res); $i++)
        {
            $serial = $res[$i][0];
            $marca = $res[$i][1];
            $sn = $res[$i][2];
            $id = $res[$i][3][0]['_id'];
            $estado = $res[$i][3][0]['estado'];
            $agente = $res[$i][3][0]['nombresAg'];
            
            $resumenTemp = end($res[$i][3]);
            $coordinador = $resumenTemp['coordinador_id'];
            $estado = $resumenTemp['estado'];
            
            $fecha = $resumenTemp['fechaMov'];
            
            if($coordinador == $_SESSION['id'] && $estado == "1")
            {
                echo '<form class="form-horizontal" role="form">' . "\xA";
                echo '  <div class="form-group">' . "\xA";
                echo '      <label for="serial" class="col-md-4 control-label">Serial:</label>' . "\xA";
                echo '      <div class="col-md-6">' . "\xA";
                echo '          <input type="text" class="form-control" rel="serial" id="serial" name="serial" value="'.$serial.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '      </div>' . "\xA";
                echo '      <label for="serial" class="col-md-4 control-label">Agente o IP del equipo:</label>' . "\xA";
                echo '      <div class="col-md-6">' . "\xA";
                echo '          <input type="text" class="form-control" rel="serial" id="serial" name="nombreag" value="'.$agente.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '      </div>' . "\xA";
                echo '      <label for="serial" class="col-md-4 control-label">Marca:</label>' . "\xA";
                echo '      <div class="col-md-6">' . "\xA";
                echo '          <input type="text" class="form-control" rel="serial" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '      </div>' . "\xA";
                if($res[$i][2] != NULL)
                {
                    echo '      <label for="serial" class="col-md-4 control-label">S/N:</label>' . "\xA";
                    echo '      <div class="col-md-6">' . "\xA";
                    echo '          <input type="text" class="form-control" rel="sn" id="sn" name="sn" value="'.$sn.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                    echo '      </div>' . "\xA";
                }
                echo '      <label for="serial" class="col-md-4 control-label">Fecha de ingreso:</label>' . "\xA";
                echo '      <div class="col-md-6">' . "\xA";
                echo '          <input type="text" class="form-control" rel="serial" id="ingreso" name="ingreso" value="'.$fecha.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '      </div>' . "\xA";
                echo '      </div>' . "\xA";
            }
        }
        echo '  </div>' . "\xA";
        echo '      </div>' . "\xA";
        echo '</form>' . "\xA";
    }
?>
















