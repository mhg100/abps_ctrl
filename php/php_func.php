<?php
//200.93.165.20
//uso, secuencia, clases, actividadas
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
        if (time() - $_SESSION['horaAcceso'] > 10000){
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
function initHTML()
{
    //172.27.32.134 IP control
    return '<!DOCTYPE html>
<html lang="es">
<head>
    <title>Administración de diademas</title>
    <meta http-equiv="Content-type" content="text/html; ISO-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="js/charts.loader.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.custom.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
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
    
    
    echo "            ['Tecnología (".count(verDiademasEnStock()).")', ".count(verDiademasEnStock())."],\xA";
    echo "         ]);\xA";
    echo "         var options = {\xA";
    echo "             title: '',\xA";
    echo "             pieHole: 0.45,\xA";
    echo "             width: ".$width.",\xA";
    echo "             height: ".$height.",\xA";
  //echo "             pieStartAngle: 100,\xA";
    echo "             chartArea:{left:0,top:40,width:'85%',height:'70%'},\xA";
    echo "             backgroundColor: { fill:'transparent' },\xA";
    echo "             is3D: false\xA";
  //echo "             is3D: true\xA";
    echo "         };\xA";
    echo "         var chart = new google.visualization.PieChart(document.getElementById('tortaoperaciones'));\xA";
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
    echo "             chartArea:{left:35,top:30,width:'60%',height:'60%'},\xA";
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
    session_start();
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
        else if($_SESSION['ns'] == 2){
            echo '       <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">'."\xA";
            echo '                                  <strong>Sesión cerrada por inactividad</strong>'."\xA";
            echo '                              </label>'."\xA";
            }
        else if($_SESSION['ns'] == 4){
            echo '       <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">';
            echo '                                  <strong>Sesión finalizada</strong>';
            echo '                              </label>';
        }
        else if($_SESSION['ns'] == 5){
            echo '       <label class="alert alert-warning col-md-4 col-md-offset-6 text-center">';
            echo '                                  <strong>Sesión no iniciada</strong>';
            echo '                              </label>';
        }
        else echo '';
        $_SESSION   = array();
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
    echo '                                    <li><a href="device.php">Ver diademas en stock</a></li>'."\xA";
    echo '                                    <li><a href="device.php">Ver diademas en reparación</a></li>'."\xA";
    echo '                                    <li role="separator" class="divider"></li>'."\xA";
    echo '                                    <li><a href="device.php?ic=1">Crear diadema</a></li>'."\xA";
    echo '                                    <li><a href="device.php?ic=2">Realizar cambio</a></li>'."\xA";
    echo '                                    <li><a href="device.php?ic=3">Recoger diademas</a></li>'."\xA";
    echo '                                    <li><a href="#">Envío a reparación</a></li>'."\xA";
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
    echo '                            <li class="dropdown">'."\xA";
    echo '                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Técnicos<span class="caret"></span></a>'."\xA";
    echo '                                <ul class="dropdown-menu">'."\xA";
    echo '                                    <li><a href="#">Ver técnicos</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="tecnico.php?ic=1">Crear</a></li>'."\xA";
    echo '                                    <li><a href="#">Modificar</a></li>'."\xA";
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
function navbarTecnico()
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
    echo '                                    <li><a href="defaultdevice.php?ic=0">Ver lista de diademas en stock</a></li>'."\xA";
    echo '                                    <li class="divider"></li>'."\xA";
    echo '                                    <li><a href="tcrear.php">Crear diadema</a></li>'."\xA";
    echo '                                    <li><a href="tcambiar.php">Cambiar diadema</a></li>'."\xA";
    echo '                                    <li><a href="trecoger.php">Recoger diademas</a></li>'."\xA";
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
    echo '                                    <span class="glyphicon glyphicon-user"></span> '.$_SESSION['nombres'].' '.$_SESSION['apellidos']."\xA";
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
    $inject = "all";
    
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
    echo '                                    <h3 id="cantidad">&nbsp;</h3>'."\xA";
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
    echo '                                        if (val == "all") {'."\xA";
    echo '                                            window.location.href = "default-opman.php?ic=0";'."\xA";
    echo '                                        }'."\xA";
    echo '                                        else{'."\xA";
    echo '                                            window.location.href = "default-opman.php?ic=0&camplist=" + val;'."\xA";
    echo '                                        }'."\xA";
    echo '                                    });'."\xA";
    echo '                                </script>'."\xA";
}
function fetchCantCampaign($nombreCampaign)
{
    return "select sum(cantidad_agentes_coordinador) as total from coordinadores where campaign_coordinador = (select id_campaign from campaigns where nombre_campaign = '".$nombreCampaign."')";
}
function comprobarAdmin()
{
    if($_SESSION['rol'] == 1)       header('Location: defaultcoord.php');
    elseif($_SESSION['rol'] == 2)   header('Location: tecdefault.php');
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
    $listaCampaigns     = getListaCampaigns();
    $listaCoordinadores = getListaCoordinadores();
    $idcamps            = array_keys($listaCampaigns);
    $idcoords           = array_keys($listaCoordinadores);
    $cant               = getCantidadDiademasPorCampaign();
    $cantTotal          = getTotalDiademas();
    
    $inner = '<center><a href="exportardiademas.php" class="text-success">Descargar en formato .xls<span class="glyphicon glyphicon-download-alt"></span></a></center><br>';
    echo '    <form class="form-horizontal" role="form">'."\xA";
    if($_SESSION['rol'] == 0){
        echo "                                                <script>\xA";
        echo"                                                    document.getElementById('exportar').innerHTML = '".$inner."';\xA";
        echo "                                                </script>\xA";
        echo '                                                <div class="form-group">'."\xA";
        echo '                                                    <label for="selectorCampaign" class="col-md-4 control-label">Listar por campaña:</label>'."\xA";
        echo '                                                    <div class="col-md-4">'."\xA";
        // ------ Opciones selector de campaña ------- //    
        echo '                                                        <select data-size="7" id="selectorCampaign" name="selectorCampaign" class="selectpicker form-control" data-live-search="true" title="Seleccione una campaña" required autocomplete="off" data-width="355px">'."\xA";
        echo '                                                            <option value="Todas las campañas">Todas las campañas</option>'."\xA";

        for($i = 0; $i < count($idcamps); $i++){
            $idcamp     = $idcamps[$i];
            if($idcamp != "6118"){
                $nombrecamp = $listaCampaigns[$idcamp]['nombre'];
                echo '                                                            <option value="'.$idcamp.'">'.$nombrecamp.'</option>'."\xA";
            }
        }
        echo '                                                        </select>'."\xA";
        echo '                                                    </div>'."\xA";
        echo '                                                </div>'."\xA";
    }
        echo '                                                <h4 id="campseleccionada">&nbsp;</h4>';
        
    for($i = 0; $i < count($listaCampaigns); $i++){
        if($_SESSION['rol'] == 0){
            if(!isset($_GET['camplist']))   $camp = $idcamps[$i];
            else                            $camp = $_GET['camplist'];
        }else{
            $camp = $_SESSION['campid'];
        }
        for($j = 0; $j < count($cant[$camp]); $j++){
            $diadema                  = $cant[$camp][$j];
            $idcoord                  = end($diadema['resumen'])['coordinador_id'];
            $nombrecoord              = $listaCoordinadores[$idcoord]['nombre'];
            $nombrecamp               = $listaCampaigns[$camp]['nombre'];
            $nombreAg                 = end($diadema['resumen'])['nombresAg'];
            $ip                       = end($diadema['resumen'])['ipequipo'];
            $marca                    = $diadema['Marca'];
            $id                       = $diadema['_id'];
            $estado                   = end($diadema['resumen'])['estado'];
            
            if($estado == "1"){
                echo '<div class="form-group">'."\xA";
                echo '    <div>'."\xA";
                echo '        <label for="iddiadema" class="col-md-3 control-label">Consecutivo:</label>'."\xA";
                echo '        <div class="col-md-9 ">'."\xA";
                echo '            <input type="text" class="form-control" rel="iddiadema" id="iddiadema" name="iddiadema" value="'.$id.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                echo '        </div>'."\xA";

                echo '        <label for="ip" class="col-md-3 control-label">IP del equipo:</label>'."\xA";
                echo '        <div class="col-md-9">'."\xA";
                echo '            <input type="text" class="form-control" rel="ip" id="ip" name="ip" value="'.$ip.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                echo '        </div>'."\xA";


                echo '        <label for="marca" class="col-md-3 control-label">Marca:</label>'."\xA";
                echo '        <div class="col-md-9">'."\xA";
                echo '            <input type="text" class="form-control" rel="marca" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                echo '        </div>'."\xA";

                if($_SESSION['rol'] == 0){
                    if(!isset($_GET['camplist'])){
                        echo '        <label for="campaign" class="col-md-3 control-label">Campaña:</label>'."\xA";
                        echo '        <div class="col-md-9">'."\xA";
                        echo '            <input type="text" class=" form-control" rel="campaign" id="campaign" name="campaign" value="'.$nombrecamp.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                        echo '        </div>'."\xA";
                    }
                    echo '        <label for="nombrecoord" class="col-md-3 control-label">Nombre del coordinador:</label>'."\xA";
                    echo '        <div class="col-md-9">'."\xA";
                    echo '            <input type="text" class=" form-control" rel="nombrecoord" id="nombrecoord" name="nombrecoord" value="'.$nombrecoord.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                    echo '        </div>'."\xA";
                }
                echo '    </div>'."\xA";
                echo '</div>'."\xA";
            }
        }
        if(isset($_GET['camplist']))        break 1;
        elseif(isset($_SESSION['campid']))  break 2;
    }        
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
    echo '                                            </form>' . "\xA";
    if( isset($_GET['camplist']) )  echo '                                        <script>document.getElementById("campseleccionada").innerHTML = "'.$listaCampaigns[$_GET['camplist']]['nombre'].' ('.count( $cant[$_GET['camplist']] ).')"; </script>';
    else                            echo '                                        <script>document.getElementById("campseleccionada").innerHTML = "Todas las campañas ('.getTotalDiademas().')"; </script>'."\xA";
}
function verDiademaStock($donde)
{
    // $donde = 1 => Stock
    // $donde = 2 => Reparacion
    
    $collection = fMongoDB();
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
    $camps              = getCantidadDiademasPorCampaign();
    $campids            = array_keys($camps);
    $cant               = count($camps[$campids[$i]]);
    $nombre             = getListaCampaigns()[$campids[$i]]['nombre'];
    
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
function correccionTexto($texto)
{
    $comilladoble = '"';
    $prohibidos = array("'", $comilladoble, ";", "=");
    $texto = str_replace($prohibidos, "-", $texto);
    $texto = mb_convert_case($texto, MB_CASE_TITLE, "ISO-8859-1");
    return $texto;
}
function getListaCoordinadores()
{
    $conn           = fSesion();
    $sql1           = "select
                           coordinadores.id_coordinador as idcoord,
                           coordinadores.nombres_coordinador as nombres, 
                           coordinadores.apellidos_coordinador as apellidos, 
                           coordinadores.campaign_coordinador as idcamp,
                           campaigns.nombre_campaign as nombrecamp, 
                           coordinadores.cantidad_agentes_coordinador as cantagentes
                       from coordinadores, campaigns
                       where
                           campaign_coordinador = campaigns.id_campaign
                       order by nombres";
    $stmt1          = sqlsrv_query($conn, $sql1);
    $coordinadores  = array();
    
    while($ppl = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)){
        $coordinadores[$ppl['idcoord']] = array(
            "nombre"     => $ppl['nombres']." ".$ppl['apellidos'],
            "idcamp"     => $ppl['idcamp'],
            "nombrecamp" => $ppl['nombrecamp']
        );
    }
    return $coordinadores;
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
    $camps      = getListaCampaigns();
    $campsKeys  = array_keys($camps);
    $diademas   = array();
    $camp       = array();

    for( $i = 0; $i < count($campsKeys); $i++ ) {
        $camp   = array();
        $cursor = $collection->find(array('resumen.campaign'=>"$campsKeys[$i]"));
        foreach($cursor as $lista){
            if(end($lista['resumen'])['estado'] == '1' && end($lista['resumen'])['campaign'] == $campsKeys[$i]){
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
function crearTecnico()
{
    echo '<form class="form-horizontal" role="form" action="creartecnico.php" method="post">'."\xA";
    echo '                            <div>'."\xA";
    echo '                            <!--Formulario-->'."\xA";
    echo '                                <br>'."\xA";
    echo '                                <div class="form-group">'."\xA";
    echo '                                    <label for="nombres" class="col-md-4 control-label">Nombres:</label>'."\xA";
    echo '                                    <div class="col-md-6">'."\xA";
    echo '                                        <input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" placeholder="Nombre(s) del técnico" required autofocus>'."\xA";
    echo '                                    </div>'."\xA";
    echo '                                </div>'."\xA";
    echo '                                <div class="form-group">'."\xA";
    echo '                                    <label for="apellidos" class="col-md-4 control-label">Apellidos:</label>'."\xA";
    echo '                                    <div class="col-md-6">'."\xA";
    echo '                                        <input type="text" class="form-control" rel="apellidos" id="apellidos" name="apellidos" autocomplete="off" placeholder="Apellidos del técnico" required autofocus>'."\xA";
    echo '                                    </div>'."\xA";
    echo '                                </div>'."\xA";
    echo '                                <div class="form-group">'."\xA";
    echo '                                    <label for="lider" class="col-md-4 control-label">Líder:</label>'."\xA";
    echo '                                    <div class="col-md-6">'."\xA";
    echo '                                        <select id="lider" name="lider" class="selectpicker" data-live-search="true" title="Seleccione el líder del técnico que está creando" data-width="400px" required>'."\xA";
                                                  llamarLideres();
    echo '                                        </select>'."\xA";
    echo '                                    </div>'."\xA";
    echo '                                </div>'."\xA";
    echo '                                <div class="form-group">'."\xA";
    echo '                                    <label for="ubicacion" class="col-md-4 control-label">Ubicación:</label>'."\xA";
    echo '                                    <div class="col-md-6">'."\xA";
    echo '                                        <select id="ubicacion" name="ubicacion" class="selectpicker" data-live-search="true" title="Seleccione la sede en donde se encuentra el técnico" data-width="400px" required>'."\xA";
                                                  llamarUbicaciones();
    echo '                                        </select>'."\xA";
    echo '                                    </div>'."\xA";
    echo '                                </div>'."\xA";
    echo '                                <div class="form-group">'."\xA";
    echo '                                    <div class="col-md-10" align="right">'."\xA";
    echo '                                        <fieldset>'."\xA";
    echo '                                            <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Crear</button>'."\xA";
    echo '                                        </fieldset>'."\xA";
    echo '                                    </div>'."\xA";
    echo '                                </div>'."\xA";
    echo '                            </div>'."\xA";
echo '                        </form>'."\xA";
}
function llamarLideres()
{
    $conn = fSesion();
    $sql = "select id_admin, nombres_admin, apellidos_admin, rol_admin from admins order by nombres_admin";
    $stmt= sqlsrv_query($conn, $sql);
    while($ppl = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        if($ppl['rol_admin'] == "1"){
            $nombrelider = $ppl['nombres_admin']." ".$ppl['apellidos_admin'];
            echo '                                            <option value="'.$ppl['id_admin'].'">'.$nombrelider.'</option>'."\xA";
        }
    }
}
function llamarUbicaciones()
{
    $conn = fSesion();
    $sql = "select id_ubicacion, nombre_ubicacion from ubicaciones order by nombre_ubicacion";
    $stmt= sqlsrv_query($conn, $sql);
    while($ppl = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        echo '                                            <option value="'.$ppl['id_ubicacion'].'">'.$ppl['nombre_ubicacion'].'</option>'."\xA";
    }
}
function cambioDiadema()
{
    $padding    = "";
    $index      = 0;
    $index      = 0;
    $collection = fMongoDB();
    $cursor     = $collection->find();
    $conn       = fSesion();
    $coords     = getListaCoordinadores();
    $campaigns  = getListaCampaigns();
    $idcoords   = array_keys($coords);
    $idcamps    = array_keys($campaigns);
    
    //---en campaña---//
    
    $cursor         = $collection->find();
    $diademascamp   = array();
    $rescamp        = array();

    foreach($cursor as $doc){
        array_push($rescamp, $doc);
    }

    for($i = 0; $i < count($rescamp); $i++){
        if(end($rescamp[$i]['resumen'])['estado'] == "1"){
            array_push($diademascamp, str_replace(" ", "", $rescamp[$i]['_id']));
        }
    }
    
    //---en bodega---//
    
    $cursor      = $collection->find();
    $diademasbod = array();
    $resbod      = array();

    foreach($cursor as $doc)    array_push($resbod, $doc);

    for($i = 0; $i < count($resbod); $i++){
        if(end($resbod[$i]['resumen'])['estado'] == "0"){
            array_push($diademasbod, str_replace(" ", "", $resbod[$i]['_id']));
        }
    }
    echo '    <form class="form-horizontal" role="form" action="cambiodiadema.php" method="post">' . "\xA";
    echo '                                        <div align="center">' . "\xA";
    echo '                                            <div class="form-group">'."\xA";
    echo '                                                <label for="campid" class="col-md-4 control-label">Seleccione la campaña:</label>'."\xA";
    echo '                                                <div class="col-md-4">'."\xA";
    echo '                                                    <select data-size="7" id="campid" name="campid" class="selectpicker form-control" data-live-search="true" title="Seleccione una campaña" required autocomplete="off" data-width="355px">'."\xA";

    for($i = 0; $i < count($idcamps); $i++){
        if($idcamps[$i] != "6118")
        echo '                                                        <option value="'.$idcamps[$i].'">'.$campaigns[$idcamps[$i]]['nombre'].'</option>'."\xA";
    }
    
    sqlsrv_free_stmt($stmt);

    echo '                                                    </select>'."\xA";
    echo '                                                </div>'."\xA";
    echo '                                            </div>'."\xA";
    echo '                                            <!--Formulario-->'."\xA";
    echo '                                            <div class="form-group">' . "\xA";
    echo '                                                <label for="coordid" class="col-md-4 control-label">Seleccione un coordinador:</label>' . "\xA";
    echo '                                                <div class="col-md-4">' . "\xA";
    echo '                                                    <select id="coordid" name="coordid" class="selectpicker" data-live-search="true" title="Seleccione un coordinador" data-width="355px" required>' . "\xA";

    for($i = 0; $i < count($idcoords); $i++){
        $idcoord = $idcoords[$i];
        echo '                                                        <option value="'.$idcoord.'" data-tokens="'.$coords[$idcoord]['nombrecamp'].' '.ucwords(mb_strtolower($coords[$idcoord]['nombre'])).'">'.ucwords(mb_strtolower($coords[$idcoord]['nombre'])).'</option>'."\xA";
    }

    echo '                                                    </select>' . "\xA";
    echo '                                                </div>' . "\xA";
    echo '                                            </div>' . "\xA";
    echo '                                            <div class="form-group">' . "\xA";
    echo '                                                <label for="diademaentrante" class="col-md-4 control-label">Serial de la diadema recogida:</label>' . "\xA";
    echo '                                                <div class="col-md-4">' . "\xA";
    echo '                                                    <select id="diademaentrante" name="diademaentrante" class="selectpicker" data-live-search="true" title="Seleccione el serial de la diadema recogida" data-width="355px" required>' . "\xA";
    
    for($i = 0; $i < count($diademascamp); $i++){
        $idcamp = end($rescamp[$i]['resumen'])['campaign'];
        echo '                                                        <option value="'.$diademascamp[$i].'" data-tokens="'.$campaigns[$idcamp]['nombre'].' '.$diademascamp[$i].'">'.$diademascamp[$i].'</option>'."\xA";
    }
    
    echo '                                                    </select>' . "\xA";
    echo '                                                </div>' . "\xA";
    echo '                                            </div>' . "\xA";
    echo '                                            <div class="form-group">' . "\xA";
    echo '                                                <label for="diademasaliente" class="col-md-4 control-label">Serial de la diadema entregada:</label>' . "\xA";
    echo '                                                    <div class="col-md-4">' . "\xA";
    echo '                                                        <select id="diademasaliente" name="diademasaliente" class="selectpicker" data-live-search="true" title="Seleccione el serial de la diadema entregada" data-width="355px" required>' . "\xA";
    
    for($i = 0; $i < count($diademasbod); $i++){
        echo '                                                            <option value="'.$diademasbod[$i].'">'.$diademasbod[$i].'</option>';
    }
    
    echo '                                                        </select>' . "\xA";
    echo '                                                    </div>' . "\xA";
    echo '                                                </div>' . "\xA";
    echo '                                            <div class="form-group">' . "\xA";
    echo '                                                <div class="col-md-10" align="right">' . "\xA";
    echo '                                                    <fieldset>' . "\xA";
    
    if(isset($_GET['ag'])){
        $padding = 15;
        if($_GET['ag'] == 0){
            echo '                                                        <label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo '                                                            <strong>Error: Serial '.$_GET["sd"].' duplicado</strong>' . "\xA";
            echo '                                                        </label>' . "\xA";
        }
        else if($_GET['ag'] == 1){
            echo '                                                        <label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo '                                                            <strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>' . "\xA";
            echo '                                                        </label>' . "\xA";
        }
    }
    echo '                                                    <button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Realizar cambio</button>' . "\xA";
    echo '                                                </fieldset>' . "\xA";
    echo '                                            </div>' . "\xA";
    echo '                                        </form>' . "\xA";
    echo '                                        <script>' . "\xA";
    echo '                                            $("#marca").on("changed.bs.select", function (e) {' . "\xA";
    echo '                                                var val = $("#marca").val();' . "\xA";
    echo '                                                if(val == "Jabra") $( "#serialnumber" ).prop( "disabled", false );' . "\xA";
    echo '                                                else $( "#serialnumber" ).prop( "disabled", true );' . "\xA";
    echo '                                            });' . "\xA";
    echo '                                        </script>' . "\xA";
}
function verTecnicos()
{
    $conn = fSesion();
    $sql = "select tecnicos.id_tecnico, tecnicos.nombres_tecnico, tecnicos.apellidos_tecnico, tecnicos.lider_tecnico, admins.nombres_admin, admins.apellidos_admin, tecnicos.ubicacion_tecnico, ubicaciones.nombre_ubicacion from tecnicos, admins, ubicaciones where ubicacion_tecnico = ubicaciones.id_ubicacion and admins.id_admin = tecnicos.lider_tecnico order by nombres_tecnico";
    $stmt= sqlsrv_query($conn, $sql);
    
    while($ppl = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $idtecnico      = $ppl['id_tecnico'];
        $nombreTecnico  = $ppl['nombres_tecnico']." ".$ppl['apellidos_tecnico'];
        $lider          = $ppl['nombres_admin']." ".$ppl['apellidos_admin'];
        $ubicacion      = $ppl['nombre_ubicacion'];
        
        echo '<form class="form-horizontal" role="form" action="creartecnico.php" method="post">'."\xA";
        echo '                            <div>'."\xA";
        echo '                            <!--Formulario-->'."\xA";
        echo '                                <br>'."\xA";
        echo '                                <div class="form-group">'."\xA";
        echo '                                    <label for="idtecnico" class="col-md-4 control-label">ID:</label>'."\xA";
        echo '                                    <div class="col-md-6">'."\xA";
        echo '                                        <input type="text" class="form-control" id="idtecnico" name="idtecnico" autocomplete="off" value="'.$idtecnico.'" disabled>'."\xA";
        echo '                                    </div>'."\xA";
        echo '                                    <label for="nombres" class="col-md-4 control-label">Nombres:</label>'."\xA";
        echo '                                    <div class="col-md-6">'."\xA";
        echo '                                        <input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" value="'.$nombreTecnico.'" disabled>'."\xA";
        echo '                                    </div>'."\xA";
        echo '                                    <label for="lider" class="col-md-4 control-label">Líder:</label>'."\xA";
        echo '                                    <div class="col-md-6">'."\xA";
        echo '                                        <input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" value="'.$lider.'" disabled>'."\xA";
        echo '                                    </div>'."\xA";
        echo '                                    <label for="ubicacion" class="col-md-4 control-label">Ubicación:</label>'."\xA";
        echo '                                    <div class="col-md-6">'."\xA";
        echo '                                        <input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" value="'.$ubicacion.'" disabled>'."\xA";
        echo '                                    </div>'."\xA";
        echo '                                </div>'."\xA";
        echo '                            </div>'."\xA";
        echo '                        </form>'."\xA";
        echo '                        <script>document.getElementById("tecnicoheader").innerHTML = "Ver técnicos";</script>';
    }
}
function verDiademasEnStock()
{
    $collection = fMongoDB();
    $cursor = $collection->find();
    $diademasenstock = array();
    
    foreach($cursor as $document){
        if(end($document['resumen'])['estado'] == "0"){
            array_push($diademasenstock, $document);
        }
    }
    return $diademasenstock;
}
function verDiademasEnCamp()
{
    $collection = fMongoDB();
    $cursor = $collection->find();
    $diademasEnCamp = array();
    
    foreach($cursor as $document){
        if(end($document['resumen'])['estado'] == "1"){
            array_push($diademasEnCamp, $document);
        }
    }
    return $diademasEnCamp;
}
function recogerDiademas()
{
    $diademas = verDiademasEnCamp();
    $campaigns = getListaCampaigns();

    echo '<form action="recoger.php" method="post">'."\xA";
    echo '    <form class="form-horizontal" role="form" action="login.php" method="post">'."\xA";
    echo '        <fieldset>'."\xA";
    echo '            <div class="form-group">'."\xA";
    echo '                <label class="col-md-2 control-label" for="id" name="id"></label>'."\xA";
    echo '                <div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo '                    <span class="input-group-addon"></span>'."\xA";
    echo '                    <select id="diademas[]" name="diademas[]" class="selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Seleccione las diademas a recoger" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($diademas); $i++){
        $idcamp = end($diademas[$i]['resumen'])['campaign'];
        $nombrecamp = $campaigns[$idcamp]['nombre'];
        echo '                        <option data-tokens="'.$nombrecamp.'">'.$diademas[$i]['_id'].'</option>'."\xA";
    }
    
    echo '                    </select>'."\xA";
    echo '                </div>'."\xA";
    echo '            </div>'."\xA";
    echo '            <div class="form-group">'."\xA";
    echo '                <div class="col-md-10 input-group" style="outline: 0px" align="right">'."\xA";
    echo '                    <fieldset>'."\xA";
    echo '                        <button id="ingresar" name="ingresar" class="btn btn-primary">Ingresar</button>'."\xA";
    echo '                    </fieldset>'."\xA";
    echo '                </div>'."\xA";
    echo '            </div>'."\xA";
    echo '        </fieldset>'."\xA";
    echo '    </form>'."\xA";
    echo '</form>'."\xA";
    
}


