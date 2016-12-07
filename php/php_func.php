<?php
//200.93.165.20
function fSesion()
{
    $db_srv = '172.27.48.125';
    $db_usr = 'merhengo';
    $db_psw = '10ceroun0';
    $db_name = 'abps_control';
    $connection_options = array('Database'=>''.$db_name.'', 'UID'=>''.$db_usr.'', 'PWD'=>''.$db_psw.'', 'CharacterSet' => 'UTF-8');
    $conn = sqlsrv_connect($db_srv, $connection_options);
    if(!is_resource($conn)){
        session_start();
        $_SESSION['ns'] = 3;
        header('Location: index.php'); var_dump(sqlsrv_errors(SQLSRV_ERR_ALL));
        exit(0);
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
    if(isset($_SESSION['horaAcceso'])){
        if (time() - $_SESSION['horaAcceso'] > 600){
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
    else if($_SESSION['nombres'] == '' || !isset($_SESSION['nombre']))  header('Location: logout.php?mtv=2'); 
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
    return '<!DOCTYPE html>
<html lang="es">
<head>
    <title>'.$tipoUsuario.' - Inicio de sesión</title>
    <meta http-equiv="Content-type" content="text/html; utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="js/charts.loader.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.custom.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-select.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.custom.css">
</head>'."\xA";
}
function llamarPieChart($width, $height)
{
    $conn       = fSesion();
    $camps      = getCantidadDiademasPorCampaign();
    $campids    = array_keys($camps);
    
    echo '<script type="text/javascript">'."\xA";
    echo "    google.charts.load('current', {'packages':['corechart']});\xA";
    echo "    google.charts.setOnLoadCallback(drawChart);\xA";
    echo "    function drawChart() {\xA";
    echo "        var data = google.visualization.arrayToDataTable([\xA";
    echo "            ['Campaña', '% de diademas'],\xA";
    
    for($i = 0; $i<count($campids); $i++){
        
        $cant   = count($camps[$campids[$i]]);
        $nombre = getListaCampaigns()[$campids[$i]]['nombre'];
        
        echo "            ['".$nombre." (".$cant.")', " .$cant."],\xA";
    }

    echo "            ['', '']\xA";
    echo "         ]);\xA";
    echo "         var options = {\xA";
    echo "             title: '',\xA";
    echo "             pieHole: 0.45,\xA";
    echo "             width: ".$width.",\xA";
    echo "             height: ".$height.",\xA";
    echo "             pieStartAngle: 100,\xA";
    echo "             chartArea:{left:20,top:40,width:'90%',height:'70%'},\xA";
    echo "             backgroundColor: { fill:'transparent' },\xA";
    echo "             is3D: false\xA";
    echo "         };\xA";
    echo "         var chart = new google.visualization.PieChart(document.getElementById('tortaOperaciones'));\xA";
    echo "         chart.draw(data, options);\xA";
    echo "     }\xA";
    echo "</script>\xA";
}
function llamarAreaChart($width, $height)
{
    echo '<script type="text/javascript">'."\xA";
    echo "    google.charts.load('current', {'packages':['corechart']});\xA";
    echo "    google.charts.setOnLoadCallback(drawChart);\xA";
    echo "    function drawChart() {\xA";
    echo "        var data = google.visualization.arrayToDataTable([\xA";
    echo "            ['Mes',         'Entrada',       'Salida'],\xA";
    echo "            ['Junio',      110,                 540],\xA";
    echo "            ['Julio',      465,                 301],\xA";
    echo "            ['Agosto',      1030,                 540],\xA";
    echo "            ['Septiembre',  1000,                 400],\xA";
    echo "            ['Octubre',     1170,                 460],\xA";
    echo "            ['Noviembre',   660,                 1120],\xA";
    echo "        ]);\xA";
    echo "        var options = {\xA";
    echo "            title: '',\xA";
    echo "            hAxis: {title: 'últimos meses',  titleTextStyle: {color: '#333'}},\xA";
    echo "            vAxis: {minValue: 0},\xA";
    echo "            width: ".$width.",\xA";
    echo "            height: ".$height.",\xA";
    echo "             chartArea:{left:35,top:30,width:'68%',height:'60%'},\xA";
    echo "            backgroundColor: { fill: 'transparent' },\xA";
    echo "            is3D: false\xA";
    echo "        };\xA";
    echo "        var chart = new google.visualization.AreaChart(document.getElementById('movimientos'));\xA";
    echo "        chart.draw(data, options);\xA";
    echo "    }\xA";
    echo "</script>\xA";
}
function validaEstadoLogin()
{
    if(isset($_SESSION['ns'])){
        if($_SESSION['ns'] == 1){
            unset($_SESSION['ns']);
            echo '       <label class="alert alert-danger col-md-4 col-md-offset-6 text-center">'."\xA";
            echo '                                  <strong>Usuario o clave incorrectos</strong>'."\xA";
            echo '                              </label>'."\xA";
        }
        else if($_SESSION['ns'] == 0){
            if($_SESSION['rol'] == 0)       header('location: default.php');
            else if($_SESSION['rol'] == 1)  header('location: defaultcoord.php');
        }
        else if($_SESSION['ns'] == 3){
            echo '       <label class="alert alert-danger col-md-4 col-md-offset-6 text-center">'."\xA";
            echo '                                  <strong>Error al iniciar sesion (codigo 0x8160)</strong>'."\xA";
            echo '                              </label>';
        }
        else echo '';
    }
    else if($_GET['ns'] == 2){
        echo '       <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">'."\xA";
        echo '                                  <strong>Sesión cerrada por inactividad</strong>'."\xA";
        echo '                              </label>'."\xA";
        }
    else if($_GET['ns'] == 4){
        echo '       <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">';
        echo '                                  <strong>Sesión finalizada</strong>';
        echo '                              </label>';
    }
    else if($_GET['ns'] == 5){
        echo '       <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">';
        echo '                                  <strong>Sesión no iniciada</strong>';
        echo '                              </label>';
    }
}
function navbar()
{
    echo '<nav class="navbar navbar-default nav-center" role="navigation">'."\xA";
    echo '                    <div class="navbar-header">'."\xA";
    echo '                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">'."\xA";
    echo '                            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>'."\xA";
    echo '                        </button><a class="navbar-brand" href="default.php">ADMin</a>'."\xA";
    echo '                    </div>'."\xA";
    echo '                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'."\xA";
    echo '                        <ul class="nav navbar-nav">'."\xA";
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="device.php">Ver todas las diademas</a></li>'."\xA";
    echo '                                    <li role="separator" class="divider"></li>'."\xA";
    echo '                                    <li><a href="device.php?ic=1">Crear diadema</a></li>'."\xA";
    echo '                                    <li><a href="device.php?ic=2">Realizar cambio</a></li>'."\xA";
    echo '                                    <li><a href="device.php?ic=2">Envío a mantenimiento</a></li>'."\xA";
    if($_SESSION['id'] == "9002"){
        echo '                                    <li role="separator" class="divider"></li>'."\xA";
        echo '                                    <li><a href="cargardiademas.php">Cargar diademas</a></li>'."\xA";
    }
    echo '                                </ul>'."\xA";
    echo '                            </li>'."\xA";
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Coordinadores<span class="caret"></span></a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="default-opman.php?ic=0">Ver coordinadores</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="default-opman.php?ic=1">Crear</a></li>'."\xA";
    echo '                                    <li><a href="default-opman.php?ic=2">Modificar</a></li>'."\xA";
    echo '                                </ul>'."\xA";
    echo '                            </li>'."\xA";
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Campañas<span class="caret"></span></a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="defaultcamp.php?ic=0">Ver campañas</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="defaultcamp.php?ic=1">Crear</a></li>'."\xA";
    echo '                                    <li><a href="defaultcamp.php?ic=3">Modificar</a></li>'."\xA";
    echo '                                </ul>'."\xA";
    echo '                            </li>'."\xA";
    echo '                        </ul>'."\xA";
    echo '                        <form class="navbar-form navbar-left" role="search">'."\xA";
    echo '                            <div class="form-group">'."\xA";
    echo '                                <input type="text" class="form-control">'."\xA";
    echo '                            </div>'."\xA";
    echo '                            <button type="submit" class="btn btn-default">'."\xA";
    echo '                                Buscar coordinador'."\xA";
    echo '                            </button>'."\xA";
    echo '                        </form>'."\xA";
    echo '                        <ul class="nav navbar-nav navbar-right">'."\xA";
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">'."\xA";
    echo '                                    <span class="caret"></span>'."\xA";
    echo '                                    <span class="glyphicon glyphicon-user"></span> '.$_SESSION['nombres'].' '.$_SESSION['apellidos']."\xA";
    echo '                                </a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="infopersonal.php"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>'."\xA";
    echo '                                </ul>'."\xA";
    echo '                            </li>'."\xA";
    echo '                        </ul>'."\xA";
    echo '                    </div>'."\xA";
    echo '                </nav>'."\xA";
}
function navbarCoordinadores()
{
    echo '<nav class="navbar navbar-default" role="navigation">'."\xA";
    echo '                    <div class="navbar-header">'."\xA";
    echo '                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">'."\xA";
    echo '                            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>'."\xA";
    echo '                        </button><a class="navbar-brand" href="default.php">COOrd</a>'."\xA";
    echo '                    </div>'."\xA";
    echo '                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'."\xA";
    echo '                        <ul class="nav navbar-nav">'."\xA";
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="defaultdevice.php?ic=0">Ver lista de diademas</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="defaultdevice.php?ic=1">Crear diadema</a></li>'."\xA";
    echo '                                    <li><a href="cambios.php">Solicitud de cambio</a></li>'."\xA";
    echo '                                </ul>'."\xA";
    echo '                            </li>'."\xA";
    echo '                        </ul>'."\xA";
    echo '                        <form class="navbar-form navbar-left" role="search">'."\xA";
    echo '                            <div class="form-group">'."\xA";
    echo '                                <input type="text" class="form-control">'."\xA";
    echo '                            </div>'."\xA";
    echo '                            <button type="submit" class="btn btn-default">'."\xA";
    echo '                                Buscar diadema'."\xA";
    echo '                            </button>'."\xA";
    echo '                        </form>'."\xA";
    echo '                        <ul class="nav navbar-nav navbar-right">'."\xA";
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">'."\xA";
    echo '                                    <span class="caret"></span>'."\xA";
    echo '                                    <span class="glyphicon glyphicon-user"></span>'.$_SESSION['nombres'].' '.$_SESSION['apellidos']."\xA";
    echo '                                </a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>'."\xA";
    echo '                                </ul>'."\xA";
    echo '                            </li>'."\xA";
    echo '                        </ul>'."\xA";
    echo '                    </div>'."\xA";
    echo '                </nav>'."\xA";
}
function crearCoordinadores()
{
    $conn = fSesion();
    $sql = "select nombre_campaign, id_campaign from campaigns order by nombre_campaign asc";
    $stmt = sqlsrv_query($conn, $sql);
    
    echo '<form class="form-horizontal" role="form" action="crearCoordinador.php" method="post">'."\xA";
    echo '                                        <div class="col-md-10 text-left col-md-offset-2">'."\xA";
    echo '                                            <p>&nbsp;</p>'."\xA";
    echo '                                            <!--Formulario-->'."\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <label for="nombres" class="col-sm-2 control-label">Nombre(s):</label>'."\xA";
    echo '                                                <div class="col-md-8">'."\xA";
    echo '                                                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required autofocus autocomplete="off">'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <label for="apellidos" class="col-sm-2 control-label">Apellidos: </label>'."\xA";
    echo '                                                <div class="col-md-8">'."\xA";
    echo '                                                    <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos" required autocomplete="off">'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <label for="cedula" class="col-sm-2 control-label">Cédula: </label>'."\xA";
    echo '                                                <div class="col-md-8">'."\xA";
    echo '                                                    <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula" required autocomplete="off">'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <label for="campa" class="col-sm-2 control-label">Campaña:</label>'."\xA";
    echo '                                                <div class="col-md-4">'."\xA";
    echo '                                                    <select id="selectorCampaign" name="selectorCampaign" class="selectpicker" data-live-search="true" title="Seleccione una campaña" required autocomplete="off">'."\xA";
                                                        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
    echo '                                                        <option value="'.$row['id_campaign'].'">'.$row['nombre_campaign'].'</option>'."\xA";
                                                        }
                                                        sqlsrv_free_stmt($stmt);
    echo '                                                    </select>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <label for="cant_agentes" class="col-sm-2 control-label">Cantidad de agentes: </label>'."\xA";
    echo '                                                <div class="col-md-8">'."\xA";
    echo '                                                    <input type="text" class="form-control bfh-number" name="cantagentes" id="cantagentes" autocomplete="off">'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <div class="col-sm-offset-2 col-sm-10">'."\xA";
    echo '                                                    <button type="submit" class="btn btn-default" id="cant_agentes" name="cant_agentes">'."\xA";
    echo '                                                        Agregar'."\xA";
    echo '                                                    </button>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                    </form>'."\xA";
}
function verCoordinadores($edit, $camp)
{
    $conn = fSesion();
    $sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
    $sql2 = "select nombre_campaign, id_campaign from campaigns order by nombre_campaign asc";
    $stmt2 = sqlsrv_query($conn, $sql2);
    $inject = "' or ''='";
    
    echo '<form class="form-horizontal" role="form">'."\xA";
    echo '                                    <div class="form-group">'."\xA";
    echo '                                        <label for="listarcampaign" class="col-sm-3 control-label">Listar por campaña:</label>'."\xA";
    echo '                                        <div class="col-md-3">'."\xA";
    echo '                                            <select id="selectorCampaign" name="selectorCampaign" class="selectpicker" data-live-search="true" title="Seleccione una campaña" required autocomplete="off">'."\xA";
    echo '                                                <option value="'.$inject.'">Todas las campañas</option>'."\xA";
                                                while($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
    echo '                                                <option value="'.$row['id_campaign'].'">'.$row['nombre_campaign'].'</option>'."\xA";
                                                }
                                                sqlsrv_free_stmt($stmt);
    echo '                                            </select>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                    </div>'."\xA";
                                        $index = 0;
                                        if(isset($camp)){
                                            $sql1 = $sql1." where campaign_coordinador = '".$camp."'";
                                        }
                                        $stmt1 = sqlsrv_query($conn, $sql1);
                                        while($ppl = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)){
                                            $id = $ppl['id'];
                                            $nombres = ucwords($ppl['nombres']);
                                            $apellidos = ucwords($ppl['apellidos']);
                                            $campaign = ucwords($ppl['idcampa']);
                                            $cantagentes=ucwords($ppl['cantagentes']);
                                            $nombreCampa=' ';
                                            $sql2 = "select nombre_campaign as nc from campaigns where id_campaign = ".$campaign."";
                                            $stmt2 = sqlsrv_query($conn, $sql2);
                                            while($cmpgn = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
                                                $nombreCampa = ucwords($cmpgn['nc']);
                                            }
                                            $deshabilitar = '';
                                            if($edit == 1)      $deshabilitar = '';
                                            else if($edit == 0) $deshabilitar = 'disabled';

    echo '                                    <div class="form-group">'."\xA";
    echo '                                        <p>&nbsp;</p>'."\xA";
    echo '                                        <label for="nombres'.$index.'" class="col-sm-3 control-label">ID:</label>'."\xA";
    echo '                                        <div class="col-md-8">'."\xA";
    echo '                                            <input type="text" class="form-control" id="id'.$index.'" placeholder="ID del agente" required value="'.$id.'" '.$deshabilitar.'>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                        <label for="nombres'.$index.'" class="col-sm-3 control-label">Nombre(s):</label>'."\xA";
    echo '                                        <div class="col-md-8">'."\xA";
    echo '                                            <input type="text" class="form-control" id="nombres'.$index.'" placeholder="Nombre(s)" required value="'.$nombres.'" '.$deshabilitar.'>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                        <label for="apellidos'.$index.'" class="col-sm-3 control-label">Apellidos:</label>'."\xA";
    echo '                                        <div class="col-md-8">'."\xA";
    echo '                                            <input type="text" class="form-control" id="apellidos'.$index.'" placeholder="Apellidos" required value="'.$apellidos.'" '.$deshabilitar.'>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                        <label for="campaign'.$index.'" class="col-sm-3 control-label">Campaña:</label>'."\xA";
    echo '                                        <div class="col-md-8">'."\xA";
    echo '                                            <input type="text" class="form-control" id="campaign'.$index.'" placeholder="Campaña" required value="'.$nombreCampa.'" '.$deshabilitar.'>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                        <label for="cantagentes'.$index.'" class="col-sm-3 control-label">Cantidad de agentes: </label>'."\xA";
    echo '                                        <div class="col-md-8">'."\xA";
    echo '                                            <input type="text" class="form-control" id="cantagentes'.$index.'" placeholder="Cantidad de agentes" required value="'.$cantagentes.'" '.$deshabilitar.'>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                        <label for="cantdiademas'.$index.'" class="col-sm-3 control-label">Diademas registradas:</label>'."\xA";
    echo '                                        <div class="col-md-8">'."\xA";
    echo '                                            <input type="text" class="form-control" id="cantdiademas'.$index.'" placeholder="ID del agente" required value="'.$id.'" '.$deshabilitar.'>'."\xA";
    echo '                                        </div>'."\xA";
    echo '                                    </div>'."\xA";
                                            $index++;
                                        }
    echo '                                </form>'."\xA";
    echo '                                <script>'."\xA";
    echo '                                    $("#selectorCampaign").on("changed.bs.select", function(e) {'."\xA";
    echo '                                        var val = $("#selectorCampaign").val();'."\xA";
    echo '                                        if (val == "Todas las campañas") {'."\xA";
    echo '                                            window.location.href = "default-opman.php?ic=0";'."\xA";
    echo '                                        }'."\xA";
    echo '                                        window.location.href = "default-opman.php?ic=0&camplist=" + val;'."\xA";
    echo '                                    });'."\xA";
    echo '                                </script>'."\xA";
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
    echo '<form class="form-horizontal" role="form" action="crear_diadema.php" method="post">'."\xA";
    echo '                                                <div align="center">'."\xA";
    echo '                                                    <!--Formulario-->'."\xA";
    echo '                                                    <div class="form-group">'."\xA";
    echo '                                                        <label for="serial" class="col-md-4 control-label">Serial:</label>'."\xA";
    echo '                                                        <div class="col-md-6">'."\xA";
    echo '                                                            <input type="text" data-toggle="tooltip" title="<br><img src='.$diadema.'><br><br>Verifique el consecutivo grabado en la bocina de la diadema.<br><br>" class="form-control back-tooltips" rel="serial" id="serial" name="serial" autocomplete="off" placeholder="Consecutivo grabado en el auricular" required autofocus>'."\xA";
    echo '                                                        </div>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                    <div class="form-group">'."\xA";
    echo '                                                        <label for="nombres" class="col-md-4 control-label">Nombre(s) y apellidos o Dirección IP: </label>'."\xA";
    echo '                                                        <div class="col-md-6">'."\xA";
    echo '                                                            <input type="text" data-toggle="tooltip" title="Si la diadema es fija, ingrese la dirección IP. Si fue asignada a un agente, ingrese los nombres y apellidos de este." class="form-control back-tooltip" id="nombres" name="nombres" placeholder="Nombres o dirección IP" required autocomplete="off">'."\xA";
    echo '                                                        </div>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                    <div class="form-group">'."\xA";
    echo '                                                        <label for="marca" class="col-md-4 control-label">Marca:</label>'."\xA";
    echo '                                                        <div class="col-md-4">'."\xA";
    echo '                                                            <select id="marca" name="marca" class="selectpicker" data-live-search="true" title="Seleccione una marca" data-width="355px" required>'."\xA";
    echo '                                                                <option value="Jabra">Jabra</option>'."\xA";
    echo '                                                                <option value="Plantronics">Plantronics</option>'."\xA";
    echo '                                                                <option value="China">China</option>'."\xA";
    echo '                                                            </select>'."\xA";
    echo '                                                        </div>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                    <div class="form-group">'."\xA";
    echo '                                                        <label for="serialnumber" class="col-sm-4 control-label">S/N: </label>'."\xA";
    echo '                                                        <div class="col-md-6">'."\xA";
    echo '                                                            <input type="text" data-toggle="tooltip" title="<br><img src='.$jabra.'><br><br>Si la diadema es marca Jabra, ubique el S/N e ingreselo.<br><br>" class="form-control bfh-number back-tooltip" name="serialnumber" id="serialnumber" autocomplete="off" disabled required>'."\xA";
    echo '                                                        </div>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                    <div class="form-group">'."\xA";
    echo '                                                        <div class="col-md-10" align="right">'."\xA";
    echo '                                                            <fieldset>'."\xA";
                                                                      if(isset($_GET['ag'])){
                                                                          $padding = 25;
                                                                          if($_GET['ag'] == 0){
    echo '                                                                <label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
    echo '                                                                    <strong>Error: Serial '.$_GET["sd"].' duplicado</strong>'."\xA";
    echo '                                                                </label>'."\xA";
                                                                          }else if($_GET['ag'] == 1){
    echo '                                                                <label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
    echo '                                                                    <strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>'."\xA";
    echo '                                                                </label>'."\xA";
                                                                          }
                                                                      }
    echo '                                                                <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>'."\xA";
    echo '                                                            </fieldset>'."\xA";
    echo '                                                        </div>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </form>'."\xA";
    echo '                                            <script>'."\xA";
    echo '                                                $("#marca").on("changed.bs.select", function(e) {'."\xA";
    echo '                                                    var val = $("#marca").val();'."\xA";
    echo '                                                    if (val == "Jabra") $("#serialnumber").prop("disabled", false);'."\xA";
    echo '                                                    else $("#serialnumber").prop("disabled", true);'."\xA";
    echo '                                                });'."\xA";
    echo '                                            </script>'."\xA";
}
function verDiadema()
{
    $index = 0;
    $collection = fMongoDB();
    $res = array();
    $campaigns = array();
    $cant = 0;
    
    if(!$_SESSION['rol'] == 0){
        $cursor = $collection->find(array('resumen.coordinador_id' => $_SESSION['id']));
    } else {
        $cursor = $collection->find();
        
        $conn = fSesion();
        $sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
        $sql2 = "select nombre_campaign, id_campaign from campaigns order by nombre_campaign asc";
        $stmt2 = sqlsrv_query($conn, $sql2);
        $inner = '<center><a href="exportardiademas.php" class="text-success">Descargar en formato .xls<span class="glyphicon glyphicon-download-alt"></span></a></center><br>';
        echo "<script>\xA \t\t\t\t\t\t\t\t\t\t\t\tdocument.getElementById('exportar').innerHTML = '".$inner."';
    </script>\xA                                            ";
        
        echo '<form class="form-horizontal" role="form">'."\xA";
        echo '                                                <div class="form-group">'."\xA";
        echo '                                                    <label for="listarcampaign" class="col-md-4 control-label">Listar por campaña:</label>'."\xA";
        echo '                                                    <div class="col-md-4">'."\xA";
        echo '                                                        <select data-size="7" id="selectorCampaign" name="selectorCampaign" class="selectpicker form-control" data-live-search="true" title="Seleccione una campaña" required autocomplete="off" data-width="355px">'."\xA";
        echo '                                                            <option value="Todas las campañas">Todas las campañas</option>'."\xA";
                                  while( $row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC) ){
                                      $campaigns[$row['id_campaign']] = $row['nombre_campaign'];
        echo '                                                            <option value="'.$row['id_campaign'].'">'.$row['nombre_campaign'].'</option>'."\xA";
                                  }
                                  sqlsrv_free_stmt($stmt);
        echo '                                                        </select>'."\xA";
        echo '                                                    </div>'."\xA";
        echo '                                                </div>'."\xA";
        echo '                                            <h4 id="campseleccionada">&nbsp;</h4>';
        echo '                                                <script>'."\xA";
        echo '                                                    $("#selectorCampaign").on("changed.bs.select", function (e) {'."\xA";
        echo '                                                        var val = $("#selectorCampaign").val();'."\xA";
        echo '                                                        if(val == "Todas las campañas")'."\xA";
        echo '                                                        {'."\xA";
        echo '                                                            window.location.href = "device.php?ic=0";'."\xA";
        echo '                                                        }'."\xA";
        echo '                                                        else'."\xA";
        echo '                                                        {'."\xA";
        echo '                                                            window.location.href = "device.php?ic=0&camplist="+val;'."\xA";
        echo '                                                        }'."\xA";
        echo '                                                    });'."\xA";
    }
    
    foreach ($cursor as $document){   
        $temp = array($document["_id"], $document["Marca"], $document["serial"], $document["resumen"]);
        array_push($res, $temp);
    }
    echo '                                                </script>'."\xA";
    for($i = 0; $i < count($res); $i++){
        $serial = $res[$i][0];
        $marca = $res[$i][1];
        $sn = $res[$i][2];
        $id = $res[$i][3][0]['_id'];
        $agente = $res[$i][3][0]['nombresAg'];
        $coord = $res[$i][3][0]['coordinador_id'];
        $campa = $res[$i][3][0]['campaign'];
        $resumenTemp = end($res[$i][3]);
        $coordinador = $resumenTemp['coordinador_id'];
        $coordinadores = getListaCoordinadores();
        $coord = $coordinadores[$coord]['nombre'];
        $estado = $resumenTemp['estado'];
        $fecha = $resumenTemp['fechaMov'];
        
        if($_SESSION['rol'] == 1){
            if($coordinador == $_SESSION['id'] && $estado == "1"){
                echo '                                          <form class="form-horizontal" role="form">' . "\xA";
                echo '                                              <div class="form-group">' . "\xA";
                echo '                                                  <label for="serial" class="col-md-4 control-label">Serial:</label>' . "\xA";
                echo '                                                  <div class="col-md-6">' . "\xA";
                echo '                                                      <input type="text" class="form-control" rel="serial" id="serial" name="serial" value="'.$serial.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                  </div>' . "\xA";
                echo '                                                  <label for="nombreag" class="col-md-4 control-label">Agente o IP del equipo:</label>' . "\xA";
                echo '                                                  <div class="col-md-6">' . "\xA";
                echo '                                                      <input type="text" class="form-control" rel="nombreag" id="nombreag" name="nombreag" value="'.$agente.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                  </div>' . "\xA";
                echo '                                                  <label for="marca" class="col-md-4 control-label">Marca:</label>' . "\xA";
                echo '                                                  <div class="col-md-6">' . "\xA";
                echo '                                                      <input type="text" class="form-control" rel="marca" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                  </div>' . "\xA";
                if($res[$i][2] != NULL){
                    echo '                                              <label for="sn" class="col-md-4 control-label">S/N:</label>' . "\xA";
                    echo '                                              <div class="col-md-6">' . "\xA";
                    echo '                                                  <input type="text" class="form-control" rel="sn" id="sn" name="sn" value="'.$sn.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                    echo '                                              </div>' . "\xA";
                }
                echo '                                              <label for="serial" class="col-md-4 control-label">Fecha de ingreso:</label>' . "\xA";
                echo '                                              <div class="col-md-6">' . "\xA";
                echo '                                                  <input type="text" class="form-control" rel="serial" id="ingreso" name="ingreso" value="'.$fecha.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                              </div>' . "\xA";
                echo '                                              </div>' . "\xA";
            }
        }else{
            if(isset($_GET['camplist'])){
                if($_GET['camplist'] == $campa){
                    if($estado == "1"){
                        $cant = $cant + 1;
                        echo '                                          <form class="form-horizontal" role="form">' . "\xA";
                        echo '                                              <div class="form-group">' . "\xA";
                        echo '                                                  <label for="serial" class="col-md-4 control-label">Serial:</label>' . "\xA";
                        echo '                                                  <div class="col-md-6">' . "\xA";
                        echo '                                                      <input type="text" class="form-control" rel="serial" id="serial" name="serial" value="'.$serial.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                        echo '                                                  </div>' . "\xA";
                        echo '                                                  <label for="serial" class="col-md-4 control-label">Agente o IP del equipo:</label>' . "\xA";
                        echo '                                                  <div class="col-md-6">' . "\xA";
                        echo '                                                      <input type="text" class="form-control" rel="serial" id="serial" name="nombreag" value="'.$agente.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                        echo '                                                  </div>' . "\xA";
                        echo '                                                  <label for="serial" class="col-md-4 control-label">Marca:</label>' . "\xA";
                        echo '                                                  <div class="col-md-6">' . "\xA";
                        echo '                                                      <input type="text" class="form-control" rel="serial" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                        echo '                                                  </div>' . "\xA";
                        echo '                                                  <label for="campa" class="col-md-4 control-label">Campaña:</label>' . "\xA";
                        echo '                                                  <div class="col-md-6">' . "\xA";
                        echo '                                                      <input type="text" class="form-control" rel="campa" id="camap" name="campa" value="'.$campaigns[$campa].'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                        echo '                                                  </div>' . "\xA";
                        echo '                                                  <label for="serial" class="col-md-4 control-label">Coordinador:</label>' . "\xA";
                        echo '                                                  <div class="col-md-6">' . "\xA";
                        echo '                                                      <input type="text" class="form-control" rel="serial" id="coordinador" name="coordinador" value="'.$coord.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                        echo '                                                  </div>' . "\xA";
                        if($res[$i][2] != NULL){
                            echo '  <label for="serial" class="col-md-4 control-label">S/N:</label>' . "\xA";
                            echo '  <div class="col-md-6">' . "\xA";
                            echo '      <input type="text" class="form-control" rel="sn" id="sn" name="sn" value="'.$sn.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                            echo '  </div>' . "\xA";
                        }
                        echo '  <label for="serial" class="col-md-4 control-label">Fecha de ingreso:</label>' . "\xA";
                        echo '  <div class="col-md-6">' . "\xA";
                        echo '      <input type="text" class="form-control" rel="serial" id="ingreso" name="ingreso" value="'.$fecha.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                        echo '  </div>' . "\xA";
                        echo '  </div>' . "\xA";


                    }
                }
            }else{
                echo '                                                <div class="form-group">' . "\xA";
                echo '                                                    <label for="serial" class="col-md-4 control-label">Serial:</label>' . "\xA";
                echo '                                                    <div class="col-md-6">' . "\xA";
                echo '                                                        <input type="text" class="form-control" rel="serial" id="serial" name="serial" value="'.$serial.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                    </div>' . "\xA";
                echo '                                                    <label for="nombreag" class="col-md-4 control-label">Agente o IP del equipo:</label>' . "\xA";
                echo '                                                    <div class="col-md-6">' . "\xA";
                echo '                                                        <input type="text" class="form-control" rel="nombreag" id="nombreag" name="nombreag" value="'.$agente.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                    </div>' . "\xA";
                echo '                                                    <label for="marca" class="col-md-4 control-label">Marca:</label>' . "\xA";
                echo '                                                    <div class="col-md-6">' . "\xA";
                echo '                                                        <input type="text" class="form-control" rel="marca" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                    </div>' . "\xA";
                echo '                                                    <label for="campa" class="col-md-4 control-label">Campaña:</label>' . "\xA";
                echo '                                                    <div class="col-md-6">' . "\xA";
                echo '                                                        <input type="text" class="form-control" rel="campa" id="campa" name="campa" value="'.$campaigns[$campa].'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                    </div>' . "\xA";
                echo '                                                    <label for="coordinador" class="col-md-4 control-label">Coordinador:</label>' . "\xA";
                echo '                                                    <div class="col-md-6">' . "\xA";
                echo '                                                        <input type="text" class="form-control" rel="coordinador" id="coordinador" name="coordinador" value="'.$coord.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                    </div>' . "\xA";
                if($res[$i][2] != NULL){
                    echo '                                                    <label for="sn" class="col-md-4 control-label">S/N:</label>' . "\xA";
                    echo '                                                    <div class="col-md-6">' . "\xA";
                    echo '                                                        <input type="text" class="form-control" rel="sn" id="sn" name="sn" value="'.$sn.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                    echo '                                                    </div>' . "\xA";
                }
                echo '                                                    <label for="ingreso" class="col-md-4 control-label">Fecha de ingreso:</label>' . "\xA";
                echo '                                                    <div class="col-md-6">' . "\xA";
                echo '                                                        <input type="text" class="form-control" rel="ingreso" id="ingreso" name="ingreso" value="'.$fecha.'" data-toggle="tooltip" autocomplete="off" disabled>' . "\xA";
                echo '                                                    </div>' . "\xA";
                echo '                                                </div>' . "\xA";
            }
        }
    }
    echo '                                            </form>' . "\xA";
    if( isset($_GET['camplist']) )  echo '                                        <script>document.getElementById("campseleccionada").innerHTML = "'.$campaigns[$_GET['camplist']].' ('.$cant.')"; </script>';
    else                            echo '                                        <script>document.getElementById("campseleccionada").innerHTML = "Todas las campañas ('.getTotalDiademas().')"; </script>';
}
function crearCamp()
{
    $padding = 10;
    $conn = fSesion();
    $sql = "select id_ubicacion, nombre_ubicacion from ubicaciones order by nombre_ubicacion asc";
    $stmt = sqlsrv_query($conn, $sql);
    echo '<form class="form-horizontal" role="form" action="crear_camp.php" method="post">' . "\xA";
    echo '  <div align="center">' . "\xA";
    echo '                    <!--Formulario-->';
    echo '    <div class="form-group">' . "\xA";
    echo '      <label for="idcamp" class="col-md-4 control-label">ID:</label>' . "\xA";
    echo '        <div class="col-md-6">' . "\xA";
    echo '          <input type="text" class="form-control back-tooltips" id="idcamp" name="idcamp" autocomplete="off" placeholder="Ingrese el identificador de la campaña" required autofocus>' . "\xA";
    echo '        </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '    <div class="form-group">' . "\xA";
    echo '      <label for="nombre" class="col-md-4 control-label">Nombre: </label>';
    echo '        <div class="col-md-6">' . "\xA";
    echo '          <input type="text" class="form-control back-tooltip" id="nombre" name="nombre" placeholder="Nombre de la campaña" autocomplete="off" required>' . "\xA";
    echo '        </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '    <div class="form-group">' . "\xA";
    echo '      <label for="sede" class="col-md-4 control-label">Sede:</label>' . "\xA";
    echo '        <div class="col-md-4">' . "\xA";
    echo '          <select id="sede" name="sede" class="selectpicker" data-live-search="true" title="Sede en donde se encuentra la campaña" data-width="355px" required>' . "\xA";
                        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
    echo '              <option value="'.$row['id_ubicacion'].'">'.$row['nombre_ubicacion'].'</option>';
                        }
    echo '          </select>' . "\xA";
    echo '        </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '    <div class="form-group">' . "\xA";
    echo '      <div class="col-md-10" align="right">' . "\xA";
    echo '        <fieldset>' . "\xA";
    if(isset($_GET['ag'])){
        $padding = 25;
        if($_GET['ag'] == 0){
            echo '                <label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo '                    <strong>Error al crear. Valide la información</strong>';
            echo '                </label>';
        }
        else if($_GET['ag'] == 1){
            echo '                <label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo '                    <strong>Campaña agregada correctamente</strong>';
            echo '                </label>';
        }
    }
    echo '            <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>' . "\xA";
    echo '              </fieldset>' . "\xA";
    echo '            </div>' . "\xA";
    echo '         </form>' . "\xA";
}
function verCamp()
{
    $padding            = 10;
    $index              = 0;
    $conn               = fSesion();
    $cantdiademascamp   = getCantidadDiademasPorCampaign();
    $campcantdiademas   = array_keys($cantdiademascamp);
    
    $sql                = "select nombre_campaign as nombre, ubicacion_campaign from campaigns";
    $stmt               = sqlsrv_query($conn, $sql);
    
    echo '<form class="form-horizontal" role="form" method="post">' . "\xA";
    echo '    <div align="center">' . "\xA";
    while($campaigns = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $stmt2 = sqlsrv_query($conn, fetchCantCampaign($campaigns["nombre"]));
        while($cant = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
            if($cant['total'] == NULL){
                $cant['total'] = 0;
            }
            echo '        <div class="form-group">' . "\xA";
            echo '            <label for="nombre'.$index.'" class="col-md-4 control-label">Nombre de la campaña:</label>' . "\xA";
            echo '            <div class="col-md-6">' . "\xA";
            echo '                <input type="text" class="form-control back-tooltips" id="nombre'.$index.'" name="nombre'.$index.'" value="'.$campaigns["nombre"].'" disabled>' . "\xA";
            echo '            </div>' . "\xA";
            echo '            <label for="cantagentes'.$index.'" class="col-md-4 control-label">Cantidad de agentes:</label>' . "\xA";
            echo '            <div class="col-md-6">' . "\xA";
            echo '                <input type="text" class="form-control back-tooltips" id="cant'.$index.'" name="cant'.$index.'" value="'.$cant['total'].'" disabled>' . "\xA";
            echo '            </div>' . "\xA";
            echo '            <label for="cantdiademas'.$index.'" class="col-md-4 control-label">Cantidad de diademas:</label>' . "\xA";
            echo '            <div class="col-md-6">' . "\xA";
            echo '                <input type="text" class="form-control back-tooltips" id="cant'.$index.'" name="cant'.$index.'" value="'.$cant['total'].'" disabled>' . "\xA";
            echo '            </div>' . "\xA";
            echo '        </div>' . "\xA";
        }
    }
    echo '          </div>' . "\xA";
    echo '      </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '</form>' . "\xA";
    echo '<script>' . "\xA";
    sqlsrv_free_stmt($stmt);
    sqlsrv_free_stmt($stmt2);
}
function adminCrearDiadema()
{
    $diadema = "'img/diadema1.jpg' width='150px' height='150px'";
    $jabra = "'img/jabrasn.jpg' width='150px' height='100px'";
    $padding = 10;
    $index = 0;
    echo '<form class="form-horizontal" role="form" action="admin_crear_diadema.php" method="post">'."\xA";
    echo '                                            <div align="center">'."\xA";
    echo '                                                <!--Formulario-->'."\xA";
    echo '                                                <div class="form-group">'."\xA";
    echo '                                                    <label for="serial" class="col-md-4 control-label">Serial:</label>'."\xA";
    echo '                                                    <div class="col-md-6">'."\xA";
    echo '                                                        <input type="text" data-toggle="tooltip" title="<br><img src='.$diadema.'><br><br>Verifique el consecutivo grabado en la bocina de la diadema.<br><br>" class="form-control back-tooltips" rel="serial" id="serial" name="serial" autocomplete="off" placeholder="Consecutivo grabado en el auricular" required autofocus>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                                <div class="form-group">'."\xA";
    echo '                                                    <label for="marca" class="col-md-4 control-label">Marca:</label>'."\xA";
    echo '                                                    <div class="col-md-4">'."\xA";
    echo '                                                        <select id="marca" name="marca" class="selectpicker" data-live-search="true" title="Seleccione una marca" data-width="355px" required>'."\xA";
    echo '                                                            <option value="Jabra">Jabra</option>'."\xA";
    echo '                                                            <option value="Plantronics">Plantronics</option>'."\xA";
    echo '                                                            <option value="China">China</option>'."\xA";
    echo '                                                        </select>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                                <div class="form-group">'."\xA";
    echo '                                                    <label for="serialnumber" class="col-sm-4 control-label">S/N: </label>'."\xA";
    echo '                                                    <div class="col-md-6">'."\xA";
    echo '                                                        <input type="text" data-toggle="tooltip" title="<br><img src='.$jabra.'><br><br>Si la diadema es marca Jabra, ubique el S/N e ingreselo.<br><br>" class="form-control bfh-number back-tooltip" name="serialnumber" id="serialnumber" autocomplete="off" disabled required>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                                <div class="form-group">'."\xA";
    echo '                                                    <div class="col-md-10" align="right">'."\xA";
    echo '                                                        <fieldset>'."\xA";
                                                                   if(isset($_GET['ag'])){
                                                                       $padding = 25;
                                                                       if($_GET['ag'] == 0){
    echo '                                                            <label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
    echo '                                                                <strong>Error: Serial '.$_GET["sd"].' duplicado</strong>'."\xA";
    echo '                                                            </label>'."\xA";
                                                                      } else if($_GET['ag'] == 1) {
    echo '                                                            <label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
    echo '                                                                <strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>'."\xA";
    echo '                                                            </label>'."\xA";
                                                                      }
                                                                   }
    echo '                                                            <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>'."\xA";
    echo '                                                        </fieldset>'."\xA";
    echo '                                                    </div>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                        </form>'."\xA";
    echo '                                        <script>'."\xA";
    echo '                                            $("#marca").on("changed.bs.select", function(e) {'."\xA";
    echo '                                                var val = $("#marca").val();'."\xA";
    echo '                                                if (val == "Jabra") $("#serialnumber").prop("disabled", false);'."\xA";
    echo '                                                else $("#serialnumber").prop("disabled", true);'."\xA";
    echo '                                            });'."\xA";
    echo '                                        '."\xA";
    echo '                                        </script>'."\xA";
                                            
}
function getListaCoordinadores()
{
    $conn = fSesion();
    $sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
    $stmt1 = sqlsrv_query($conn, $sql1);
    $coordinadores = array();
    while($ppl = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)){
        $coordinadores[$ppl['id']] = array(
            "nombre"=>$ppl['nombres']." ".$ppl['apellidos'],
        );
    }
    return $coordinadores;
}
function cambioDiadema()
{
    $diadema = "'img/diadema1.jpg' width='150px' height='150px'";
    $jabra = "'img/jabrasn.jpg' width='150px' height='100px'";
    $padding = 10;
    $index = 0;
    
    $index = 0;
    $collection = fMongoDB();
    $res = array();
    $campaigns = array();
    $cursor = $collection->find();
        
    $conn = fSesion();
    $sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
    $sql2 = "select nombre_campaign, id_campaign from campaigns order by nombre_campaign asc";
    $stmt2 = sqlsrv_query($conn, $sql2);
    echo '<form class="form-horizontal" role="form" action="" method="post">' . "\xA";
    echo '    <div align="center">' . "\xA";
        echo '                                                <div class="form-group">'."\xA";
        echo '                                                    <label for="listarcampaign" class="col-md-4 control-label">Seleccione la campaña:</label>'."\xA";
        echo '                                                    <div class="col-md-4">'."\xA";
        echo '                                                        <select data-size="7" id="selectorCampaign" name="selectorCampaign" class="selectpicker form-control" data-live-search="true" title="Seleccione una campaña" required autocomplete="off" data-width="355px">'."\xA";
        echo '                                                            <option value="Todas las campañas">Todas las campañas</option>'."\xA";
                                  while($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)){
                                      $campaigns[$row['id_campaign']] = $row['nombre_campaign'];
        echo '                                                            <option value="'.$row['id_campaign'].'">'.$row['nombre_campaign'].'</option>'."\xA";
                                  }
                                  sqlsrv_free_stmt($stmt);
        echo '                                                        </select>'."\xA";
        echo '                                                    </div>'."\xA";
        echo '                                                </div>'."\xA";
        echo '                                                <script>'."\xA";
        echo '                                                    $("#selectorCampaign").on("changed.bs.select", function (e) {'."\xA";
        echo '                                                        var val = $("#selectorCampaign").val();'."\xA";
        echo '                                                        if(val == "Todas las campañas")'."\xA";
        echo '                                                        {'."\xA";
        echo '                                                            window.location.href = "device.php?ic=0";'."\xA";
        echo '                                                        }'."\xA";
        echo '                                                        else'."\xA";
        echo '                                                        {'."\xA";
        echo '                                                            window.location.href = "device.php?ic=0&camplist="+val;'."\xA";
        echo '                                                        }'."\xA";
        echo '                                                    });'."\xA";
        echo '                                                </script>'."\xA";
    echo '<script>';
    echo '    $("#selectorCampaign").on("changed.bs.select", function (e) {';
    echo '        var val = $("#selectorCampaign").val();';
    echo '        if(val == "Todas las campañas")';
    echo '        {';
    echo '            window.location.href = "device.php?ic=0";';
    echo '        }';
    echo '        else';
    echo '        {';
    echo '            window.location.href = "device.php?ic=0&camplist="+val;';
    echo '        }';
    echo '    });';
    echo '</script>';
    echo '                    <!--Formulario-->';
    echo '    <div class="form-group">' . "\xA";
    echo '      <label for="camp" class="col-md-4 control-label">Campaña:</label>' . "\xA";
    echo '        <div class="col-md-4">' . "\xA";
    echo '          <select id="camp" name="camp" class="selectpicker" data-live-search="true" title="Seleccione la campaña" data-width="355px" required>' . "\xA";
    echo '            <option value="Jabra">Jabra</option>' . "\xA";
    echo '            <option value="Plantronics">Plantronics</option>' . "\xA";
    echo '            <option value="China">China</option>' . "\xA";
    echo '          </select>' . "\xA";
    echo '        </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '    <div class="form-group">' . "\xA";
    echo '      <label for="serialrecogida" class="col-md-4 control-label">Serial de la diadema recogida:</label>' . "\xA";
    echo '        <div class="col-md-6">' . "\xA";
    echo '          <select id="serialrecogida" name="serialrecogida" class="selectpicker" data-live-search="true" title="Seleccione el serial de la diadema recogida" data-width="355px" required>' . "\xA";
    echo '            <option value="">ABPS001</option>' . "\xA";
    echo '            <option value="">ABPS002</option>' . "\xA";
    echo '            <option value="">ABPS003</option>' . "\xA";
    echo '          </select>' . "\xA";
    echo '        </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '    <div class="form-group">' . "\xA";
    echo '      <label for="serialentregada" class="col-md-4 control-label">Serial de la diadema recogida:</label>' . "\xA";
    echo '        <div class="col-md-6">' . "\xA";
    echo '          <select id="serialentregada" name="serialentregada" class="selectpicker" data-live-search="true" title="Seleccione el serial de la diadema entregada" data-width="355px" required>' . "\xA";
    echo '            <option value="">ABPS001</option>' . "\xA";
    echo '            <option value="">ABPS002</option>' . "\xA";
    echo '            <option value="">ABPS003</option>' . "\xA";
    echo '          </select>' . "\xA";
    echo '        </div>' . "\xA";
    echo '    </div>' . "\xA";
    echo '    <div class="form-group">' . "\xA";
    echo '      <div class="col-md-10" align="right">' . "\xA";
    echo '        <fieldset>' . "\xA";
    if(isset($_GET['ag']))
    {
        $padding = 25;
        if($_GET['ag'] == 0){
            echo '        <label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo '          <strong>Error: Serial '.$_GET["sd"].' duplicado</strong>' . "\xA";
            echo '        </label>' . "\xA";
        }
        else if($_GET['ag'] == 1){
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
function correccionTexto($texto)
{
    $comilladoble = '"';
    $prohibidos = array("'", $comilladoble, ";", "=");
    $texto = str_replace($prohibidos, "-", $texto);
    $texto = mb_convert_case($texto, MB_CASE_TITLE, "ISO-8859-1");
    return $texto;
}
function getListaCampaigns()
{
    $conn = fSesion();
    $sql1 = "select * from campaigns";
    $stmt1 = sqlsrv_query($conn, $sql1);
    $campaigns = array();
    while($camps = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)){
        $campaigns[$camps['id_campaign']] = array(
            "nombre"   =>$camps['nombre_campaign'],
            "ubicacion"=>$camps['ubicacion_campaign']
        );
    }
    return $campaigns;
}
function getCantidadDiademasPorCampaign()
{
    $collection = fMongoDB();
    $camps = getListaCampaigns();
    $campsKeys = array_keys($camps);
    $diademas = array();
    $camp = array();

    for( $i = 0; $i < count( $campsKeys ); $i++ ) {
        $camp = array();
        $cursor = $collection->find(array('resumen.campaign'=>"$campsKeys[$i]"));
        foreach($cursor as $lista){
            if($lista['resumen'][count($lista['resumen'])-1]['estado'] == '1'){
                array_push($camp, $lista);
            }
        }
        if($camp != array()) {
            $diademas[$campsKeys[$i]] = $camp;
        }
    }
    return $diademas;
}
function getTotalDiademas()
{
    $campaigns = getListaCampaigns();
    $diademas  = getCantidadDiademasPorCampaign();
    $campkeys  = array_keys($campaigns);
    $cantotal  = 0;

    for($i = 0; $i < count($campaigns); $i++){
        $cantotal += count($diademas[$campkeys[$i]]);
    }
    return $cantotal;
}
function pprint($arreglo)
{
    echo '<pre>';
        print_r($arreglo);
    echo '</pre>';
}















