<?php
//200.93.165.20
//172.29.106.225
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
        if (time() - $_SESSION['horaAcceso'] > 1200){
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
    $conn    = fSesion();
    $camps   = getCantidadDiademasPorCampaign();
    $campids = array_keys($camps);
    
    echo '<script type="text/javascript">'."\xA";
    echo indent(04)."google.charts.load('current', {'packages':['corechart']});\xA";
    echo indent(04)."google.charts.setOnLoadCallback(drawChart);\xA";
    echo indent(04)."function drawChart() {\xA";
    echo indent(08)."var data = google.visualization.arrayToDataTable([\xA";
    echo indent(12)."['Campaña', '% de diademas'],\xA";
    
    for($i = 0; $i<count($campids); $i++){
        $cant   = count($camps[$campids[$i]]);
        $nombre = getListaCampaigns()[$campids[$i]]['nombre'];
        echo indent(12)."['".$nombre." (".$cant.")', " .$cant."],\xA";
    }

    echo indent(12)."['Tecnología (".count(getDiademasEnStock()).")', ".count(getDiademasEnStock())."],\xA";
    echo indent(09)."]);\xA";
    echo indent(09)."var options = {\xA";
    echo indent(13)."title: '',\xA";
    echo indent(13)."pieHole: 0.45,\xA";
    echo indent(13)."width: ".$width.",\xA";
    echo indent(13)."height: ".$height.",\xA";
  //echo indent(13)."pieStartAngle: 100,\xA";
    echo indent(13)."chartArea:{left:0,top:40,width:'80%',height:'70%'},\xA";
    echo indent(13)."backgroundColor: { fill:'transparent' },\xA";
    echo indent(13)."is3D: false\xA";
    echo indent(09)."};\xA";
    echo indent(09)."var chart = new google.visualization.PieChart(document.getElementById('tortaoperaciones'));\xA";
    echo indent(09)."chart.draw(data, options);\xA";
    echo indent(05)."}\xA";
    echo "</script>\xA";
}
function llamarAreaChart($width, $height)
{
    echo '<script type="text/javascript">'."\xA";
    echo indent(04)."google.charts.load('current', {'packages':['corechart']});\xA";
    echo indent(04)."google.charts.setOnLoadCallback(drawChart);\xA";
    echo indent(04)."function drawChart() {\xA";
    echo indent(08)."var data = google.visualization.arrayToDataTable([\xA";
    echo indent(12)."['Mes',         'Entrada',       'Salida'],\xA";
    echo indent(12)."['Junio',      110,                 540],\xA";
    echo indent(12)."['Julio',      465,                 301],\xA";
    echo indent(12)."['Agosto',      1030,                 540],\xA";
    echo indent(12)."['Septiembre',  1000,                 400],\xA";
    echo indent(12)."['Octubre',     1170,                 460],\xA";
    echo indent(12)."['Noviembre',   660,                 1120],\xA";
    echo indent(08)."]);\xA";
    echo indent(08)."var options = {\xA";
    echo indent(12)."title: '',\xA";
    echo indent(12)."hAxis: {title: 'últimos meses',  titleTextStyle: {color: '#333'}},\xA";
    echo indent(12)."vAxis: {minValue: 0},\xA";
    echo indent(12)."width: ".$width.",\xA";
    echo indent(12)."height: ".$height.",\xA";
    echo indent(12)."chartArea:{left:35,top:30,width:'60%',height:'60%'},\xA";
    echo indent(12)."backgroundColor: { fill: 'transparent' },\xA";
    echo indent(12)."is3D: false\xA";
    echo indent(08)."};\xA";
    echo indent(08)."var chart = new google.visualization.AreaChart(document.getElementById('movimientos'));\xA";
    echo indent(08)."chart.draw(data, options);\xA";
    echo indent(04)."}\xA";
    echo "</script>\xA";
}
function ultimosMovimientos()
{
    $collection = fMongoDB();
    $query      = $collection->find();
    
    $movimientos = array("0" => "Entra a stock ",
                         "1" => "Entregada a ",
                         "2" => "Sale a reparación ",
                         "3" => "Dada de baja ");
    
    $campaigns  = getListaCampaigns();
    $diademas   = array();
    $diademas2  = array();
    $topdiez    = array();
    $flag       = 10;


    foreach ($query as $diadema){
        
        $id      = $diadema["_id"];
        $resumen = end($diadema['resumen']);
        $fecha   = $resumen['fechaMov'];

        $diademas2[$id] = array("id"       => $id,
                                "fecha"    => $fecha,
                                "resumen"  => $resumen);

        array_push($diademas, $diadema);
    }
    
    usort($diademas2, function($a1, $a2) {
        $d1 = strtotime($a1['fecha']);
        $d2 = strtotime($a2['fecha']);
        return $d2 - $d1;
    });

    for($i = 0; $i<$flag; $i++){
        array_push($topdiez, $diademas2[$i]);
    }
    echo '<ol>';
    echo "<table>";
    echo "<tr>";
    for($i = 0; $i < count($topdiez); $i++){
        $id     = $topdiez[$i]['id'];
        $estado = $topdiez[$i]['resumen']['estado'];
        $camp   = $topdiez[$i]['resumen']['campaign'];
        $tecid  = $topdiez[$i]['resumen']['tecnico_id'];
        
        switch($estado){
            case 0:
                $ech = indent(36)."<td width='45%'><li><strong>".$id."</strong></td><td>".$movimientos[$estado]."</li></td></tr>\xA";
                break;
            case 1:
                $ech = indent(36)."<td width='45%'><li><strong>".$diademas[$i]['_id']."</strong></td><td>".$movimientos[$estado]." ".$campaigns[$camp]['nombre']."</li></td></tr>\xA";
                break;
            case 2:
                $ehc = indent(36)."<td width='45%'><li><strong>".$diademas[$i]['_id']."</strong></td><td>".$movimientos[$estado]."</li></td></tr>\xA";
        }
        echo $ech;
    }
    

        //pprint($diademas[$i]['_id']);
        //pprint($ultimoresumen);
    echo indent(32)."</ol>\xA";
    echo "</table>";
}
//db.diademas.find().sort({$natural:-1}).limit(10).pretty()
function validaEstadoLogin()
{
    session_start();
    if(isset($_SESSION['ns'])){
        if($_SESSION['ns'] == 1){
            unset($_SESSION['ns']);
            echo indent(07).'<label class="alert alert-danger col-md-4 col-md-offset-6 text-center">'."\xA";
            echo indent(34).'<strong>Usuario o clave incorrectos</strong>'."\xA";
            echo indent(30).'</label>'."\xA";
        }
        else if($_SESSION['ns'] == 0){
            if($_SESSION['rol'] == 0)       header('location: default.php');
            else if($_SESSION['rol'] == 1)  header('location: defaultcoord.php');
        }
        else if($_SESSION['ns'] == 3){
            echo indent(07).'<label class="alert alert-danger col-md-4 col-md-offset-6 text-center">'."\xA";
            echo indent(34).'<strong>Error al iniciar sesion (codigo 0x8160)</strong>'."\xA";
            echo indent(30).'</label>';
        }
        else if($_SESSION['ns'] == 2){
            echo indent(07).'<label class="alert alert-warning col-md-4 col-md-offset-6 text-center">'."\xA";
            echo indent(34).'<strong>Sesión cerrada por inactividad</strong>'."\xA";
            echo indent(30).'</label>'."\xA";
            }
        else if($_SESSION['ns'] == 4){
            echo indent(07).'<label class="alert alert-warning col-md-4 col-md-offset-6 text-center">';
            echo indent(34).'<strong>Sesión finalizada</strong>';
            echo indent(30).'</label>';
        }
        else if($_SESSION['ns'] == 5){
            echo indent(07).'<label class="alert alert-warning col-md-4 col-md-offset-6 text-center">';
            echo indent(34).'<strong>Sesión no iniciada</strong>';
            echo indent(30).'</label>';
        }
        else echo '';
        $_SESSION   = array();
    }
}
function navbar()
{
    echo indent(00).'<nav class="navbar navbar-default nav-center" role="navigation">'."\xA";
    echo indent(20).'<div class="navbar-header">'."\xA";
    echo indent(24).'<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">'."\xA";
    echo indent(28).'<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>'."\xA";
    echo indent(24).'</button>'."\xA";
    echo indent(24).'<a class="navbar-brand" href="default.php">ADMin</a>'."\xA";
    echo indent(20).'</div>'."\xA";
    echo indent(20).'<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'."\xA";
    echo indent(24).'<ul class="nav navbar-nav">'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="device.php">Ver todas las diademas en campaña</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=4">Ver diademas en stock</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=6">Ver diademas en reparación</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=10">Ver diademas dadas de baja</a></li>'."\xA";
    echo indent(36).'<li role="separator" class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=1">Crear diadema</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=2">Realizar cambio</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=3">Recoger diademas</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=5">Envío a reparación</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=7">Recibir de reparación</a></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=8">Entregar a campaña</a></li>'."\xA";
    echo indent(36).'<li role="separator" class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="device.php?ic=9">Dar de baja</a></li>'."\xA";
    echo indent(36).'<li><a href="consecutivo.php">Ver consecutivo de marcación</a></li>'."\xA";
    
    if($_SESSION['id'] == "9002"){
        echo indent(36).'<li role="separator" class="divider"></li>'."\xA";
        echo indent(36).'<li><a href="cargardiademas.php">Cargar diademas</a></li>'."\xA";
    }
    
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">Coordinadores<span class="caret"></span></a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="default-opman.php?ic=0">Ver coordinadores</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="default-opman.php?ic=1">Crear</a></li>'."\xA";
    echo indent(36).'<li><a href="default-opman.php?ic=2">Modificar</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">Campañas<span class="caret"></span></a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="defaultcamp.php?ic=0">Ver campañas</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="defaultcamp.php?ic=1">Crear</a></li>'."\xA";
    echo indent(36).'<li><a href="defaultcamp.php?ic=3">Modificar</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">Técnicos<span class="caret"></span></a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="#">Ver técnicos</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="tecnico.php?ic=1">Crear</a></li>'."\xA";
    echo indent(36).'<li><a href="#">Modificar</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(24).'</ul>'."\xA";
    echo indent(24).'<form class="navbar-form navbar-left" role="search">'."\xA";
    echo indent(28).'<div class="form-group">'."\xA";
    echo indent(32).'<input type="text" class="form-control">'."\xA";
    echo indent(28).'</div>'."\xA";
    echo indent(28).'<button type="submit" class="btn btn-default">'."\xA";
    echo indent(32).'Buscar coordinador'."\xA";
    echo indent(28).'</button>'."\xA";
    echo indent(24).'</form>'."\xA";
    echo indent(24).'<ul class="nav navbar-nav navbar-right">'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">'."\xA";
    echo indent(36).'<span class="caret"></span>'."\xA";
    echo indent(36).'<span class="glyphicon glyphicon-user"></span> '.$_SESSION['nombres'].' '.$_SESSION['apellidos']."\xA";
    echo indent(32).'</a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="infopersonal.php"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(24).'</ul>'."\xA";
    echo indent(20).'</div>'."\xA";
    echo indent(16).'</nav>'."\xA";
}
function navbarCoordinadores()
{
    echo '<nav class="navbar navbar-default" role="navigation">'."\xA";
    echo indent(20).'<div class="navbar-header">'."\xA";
    echo indent(24).'<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">'."\xA";
    echo indent(28).'<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>'."\xA";
    echo indent(24).'</button><a class="navbar-brand" href="default.php">COOrd</a>'."\xA";
    echo indent(20).'</div>'."\xA";
    echo indent(20).'<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'."\xA";
    echo indent(24).'<ul class="nav navbar-nav">'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="defaultdevice.php?ic=0">Ver lista de diademas</a></li>'."\xA";
    //echo '                                    <li class="divider"></li>'."\xA";
    //echo '                                    <li><a href="defaultdevice.php?ic=1">Crear diadema</a></li>'."\xA";
    //echo '                                    <li><a href="cambios.php">Solicitud de cambio</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(24).'</ul>'."\xA";
    echo indent(24).'<form class="navbar-form navbar-left" role="search">'."\xA";
    echo indent(28).'<div class="form-group">'."\xA";
    echo indent(32).'<input type="text" class="form-control">'."\xA";
    echo indent(28).'</div>'."\xA";
    echo indent(28).'<button type="submit" class="btn btn-default">'."\xA";
    echo indent(32).'Buscar diadema'."\xA";
    echo indent(28).'</button>'."\xA";
    echo indent(24).'</form>'."\xA";
    echo indent(24).'<ul class="nav navbar-nav navbar-right">'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">'."\xA";
    echo indent(36).'<span class="caret"></span>'."\xA";
    echo indent(36).'<span class="glyphicon glyphicon-user"></span>'.$_SESSION['nombres'].' '.$_SESSION['apellidos']."\xA";
    echo indent(32).'</a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(24).'</ul>'."\xA";
    echo indent(20).'</div>'."\xA";
    echo indent(16).'</nav>'."\xA";
}
function navbarTecnico()
{
    echo '<nav class="navbar navbar-default" role="navigation">'."\xA";
    echo indent(20).'<div class="navbar-header">'."\xA";
    echo indent(24).'<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">'."\xA";
    echo indent(28).'<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>'."\xA";
    echo indent(24).'</button><a class="navbar-brand" href="default.php">TEcn</a>'."\xA";
    echo indent(20).'</div>'."\xA";
    echo indent(20).'<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'."\xA";
    echo indent(24).'<ul class="nav navbar-nav">'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">Diademas<span class="caret"></span></a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=0">Ver lista de diademas en stock</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=1">Crear diadema</a></li>'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=2">Cambiar diadema</a></li>'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=3">Recoger diademas</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=4">Enviar a reparación</a></li>'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=5">Recibir de reparación</a></li>'."\xA";
    echo indent(36).'<li><a href="tecdev.php?ic=6">Entregar a campaña</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(24).'</ul>'."\xA";
    echo indent(24).'<form class="navbar-form navbar-left" role="search">'."\xA";
    echo indent(28).'<div class="form-group">'."\xA";
    echo indent(32).'<input type="text" class="form-control">'."\xA";
    echo indent(28).'</div>'."\xA";
    echo indent(28).'<button type="submit" class="btn btn-default">'."\xA";
    echo indent(32).'Buscar diadema'."\xA";
    echo indent(28).'</button>'."\xA";
    echo indent(24).'</form>'."\xA";
    echo indent(24).'<ul class="nav navbar-nav navbar-right">'."\xA";
    echo indent(28).'<li class="dropdown">'."\xA";
    echo indent(32).'<a class="dropdown-toggle" data-toggle="dropdown" href="#">'."\xA";
    echo indent(36).'<span class="caret"></span>'."\xA";
    echo indent(36).'<span class="glyphicon glyphicon-user"></span> '.$_SESSION['nombres'].' '.$_SESSION['apellidos']."\xA";
    echo indent(32).'</a>'."\xA";
    echo indent(32).'<ul class="dropdown-menu">'."\xA";
    echo indent(36).'<li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>'."\xA";
    echo indent(36).'<li class="divider"></li>'."\xA";
    echo indent(36).'<li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>'."\xA";
    echo indent(32).'</ul>'."\xA";
    echo indent(28).'</li>'."\xA";
    echo indent(24).'</ul>'."\xA";
    echo indent(20).'</div>'."\xA";
    echo indent(16).'</nav>'."\xA";
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
    echo            '<form class="form-horizontal" role="form" action="crear_diadema.php" method="post">'."\xA";
    echo indent(48).'<div align="center">'."\xA";
    echo indent(52).'<p class="text-left"><small>Utilice esta opción para crear dispositivos.</small></p>'."\xA";
    echo indent(52).'<!--Formulario-->'."\xA";
    echo indent(52).'<div class="form-group">'."\xA";
    echo indent(56).'<label for="serial" class="col-md-4 control-label">Serial:</label>'."\xA";
    echo indent(56).'<div class="col-md-6">'."\xA";
    echo indent(60).'<input type="text" data-toggle="tooltip" title="<br><img src='.$diadema.'><br><br>Verifique el consecutivo grabado en la bocina de la diadema.<br><br>" class="form-control back-tooltips" rel="serial" id="serial" name="serial" autocomplete="off" placeholder="Consecutivo grabado en el auricular" required autofocus>'."\xA";
    echo indent(56).'</div>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(52).'<div class="form-group">'."\xA";
    echo indent(56).'<label for="nombres" class="col-md-4 control-label">Nombre(s) y apellidos o Dirección IP: </label>'."\xA";
    echo indent(56).'<div class="col-md-6">'."\xA";
    echo indent(60).'<input type="text" data-toggle="tooltip" title="Si la diadema es fija, ingrese la dirección IP. Si fue asignada a un agente, ingrese los nombres y apellidos de este." class="form-control back-tooltip" id="nombres" name="nombres" placeholder="Nombres o dirección IP" required autocomplete="off">'."\xA";
    echo indent(56).'</div>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(52).'<div class="form-group">'."\xA";
    echo indent(56).'<label for="marca" class="col-md-4 control-label">Marca:</label>'."\xA";
    echo indent(56).'<div class="col-md-4">'."\xA";
    echo indent(60).'<select id="marca" name="marca" class="selectpicker" data-live-search="true" title="Seleccione una marca" data-width="355px" required>'."\xA";
    echo indent(64).'<option value="Jabra">Jabra</option>'."\xA";
    echo indent(64).'<option value="Plantronics">Plantronics</option>'."\xA";
    echo indent(64).'<option value="China">China</option>'."\xA";
    echo indent(60).'</select>'."\xA";
    echo indent(56).'</div>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(52).'<div class="form-group">'."\xA";
    echo indent(56).'<label for="serialnumber" class="col-sm-4 control-label">S/N: </label>'."\xA";
    echo indent(56).'<div class="col-md-6">'."\xA";
    echo indent(60).'<input type="text" data-toggle="tooltip" title="<br><img src='.$jabra.'><br><br>Si la diadema es marca Jabra, ubique el S/N e ingreselo.<br><br>" class="form-control bfh-number back-tooltip" name="serialnumber" id="serialnumber" autocomplete="off" disabled required>'."\xA";
    echo indent(56).'</div>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(52).'<div class="form-group">'."\xA";
    echo indent(56).'<div class="col-md-10" align="right">'."\xA";
    echo indent(60).'<fieldset>'."\xA";

    if(isset($_GET['ag'])){
        $padding = 25;
        if($_GET['ag'] == 0){
            echo indent(64).'<label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
            echo indent(68).'<strong>Error: Serial '.$_GET["sd"].' duplicado</strong>'."\xA";
            echo indent(64).'</label>'."\xA";
        }
        else if($_GET['ag'] == 1){
            echo indent(64).'<label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
            echo indent(68).'<strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>'."\xA";
            echo indent(64).'</label>'."\xA";
        }
    }

    echo indent(64).'<button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>'."\xA";
    echo indent(60).'</fieldset>'."\xA";
    echo indent(56).'</div>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(44).'</form>'."\xA";
    echo indent(44).'<script>'."\xA";
    echo indent(48).'$("#marca").on("changed.bs.select", function(e) {'."\xA";
    echo indent(52).'var val = $("#marca").val();'."\xA";
    echo indent(52).'if (val == "Jabra") $("#serialnumber").prop("disabled", false);'."\xA";
    echo indent(52).'else $("#serialnumber").prop("disabled", true);'."\xA";
    echo indent(48).'});'."\xA";
    echo indent(44).'</script>'."\xA";
}
function verDiadema($opcion)
{
    ini_set('display_errors', FALSE);
    ini_set('display_startup_errors', FALSE);
    
    $listaCampaigns                       = getListaCampaigns();
    $cant                                 = getCantidadDiademasPorCampaign();
    $listaCoordinadores                   = getListaCoordinadores();
    $idcamps                              = array_keys($listaCampaigns);
    if($opcion == "1")      $cant['6118'] = getDiademasEnStock();
    else if($opcion == "2") $cant['6118'] = getDiademasEnReparacion();
    
    $inner = '<center><a href="exportardiademas.php" class="text-success">Descargar en formato .xls<span class="glyphicon glyphicon-download-alt"></span></a></center><br>';
    echo indent(4).'<form class="form-horizontal" role="form">'."\xA";
    
    if($_SESSION['rol'] == 0 && $opcion != "1" && $opcion != "2"){
        echo indent(48)."<script>\xA";
        echo indent(52)."document.getElementById('exportar').innerHTML = '".$inner."';\xA";
        echo indent(48)."</script>\xA";
        echo indent(48).'<div class="form-group">'."\xA";
        echo indent(52).'<label for="selectorCampaign" class="col-md-4 control-label">Listar por campaña:</label>'."\xA";
        echo indent(52).'<div class="col-md-4">'."\xA";
        
        // ------ Opciones selector de campaña ------- //    
        
        echo indent(56).'<select data-size="7" id="selectorCampaign" name="selectorCampaign" class="selectpicker form-control" data-live-search="true" title="Seleccione una campaña" required autocomplete="off" data-width="355px">'."\xA";
        echo indent(60).'<option value="Todas las campañas">Todas las campañas</option>'."\xA";

        for($i = 0; $i < count($idcamps); $i++){
            $idcamp     = $idcamps[$i];
            if($idcamp != "6118"){
                $nombrecamp = $listaCampaigns[$idcamp]['nombre'];
                echo indent(60).'<option value="'.$idcamp.'">'.$nombrecamp.'</option>'."\xA";
            }
        }
        echo indent(56).'</select>'."\xA";
        echo indent(52).'</div>'."\xA";
        echo indent(48).'</div>'."\xA";
    }
    
        echo indent(48).'<h4 id="campseleccionada">&nbsp;</h4>';
        
    for($i = 0; $i < count($listaCampaigns); $i++){
        if($_SESSION['rol'] == "0"){
            if($opcion == "1" || $opcion == "2") $camp = "6118";
            else{
                if(!isset($_GET['camplist']))    $camp = $idcamps[$i];
                else                             $camp = $_GET['camplist'];
            }
        }else{
            if($opcion == "1" || $opcion == "2") $camp = "6118";
            else if($opcion == "100")            $camp = $_SESSION['campid'];
        }
        for($j = 0; $j < count($cant[$camp]); $j++){
            $diadema     = $cant[$camp][$j];
            $idcoord     = end($diadema['resumen'])['coordinador_id'];
            $nombreAg    = end($diadema['resumen'])['nombresAg'];
            $ip          = end($diadema['resumen'])['ipequipo'];
            $estado      = end($diadema['resumen'])['estado'];
            $nombrecoord = $listaCoordinadores[$idcoord]['nombre'];
            $nombrecamp  = $listaCampaigns[$camp]['nombre'];
            $marca       = $diadema['Marca'];
            $id          = $diadema['_id'];
            if($estado == "1" || ($opcion == "1" || $opcion == "2" || $opcion == "100")){
                echo '<div class="form-group">'."\xA";
                echo indent(52).'<div>'."\xA";
                echo indent(56).'<label for="iddiadema" class="col-md-3 control-label">Consecutivo:</label>'."\xA";
                echo indent(56).'<div class="col-md-9 ">'."\xA";
                echo indent(60).'<input type="text" class="form-control" rel="iddiadema" id="iddiadema" name="iddiadema" value="'.$id.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                echo indent(56).'</div>'."\xA";

                if($opcion != "1" && $opcion != "2"){
                    echo indent(56).'<label for="ip" class="col-md-3 control-label">IP del equipo:</label>'."\xA";
                    echo indent(56).'<div class="col-md-9">'."\xA";
                    echo indent(60).'<input type="text" class="form-control" rel="ip" id="ip" name="ip" value="'.$ip.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                    echo indent(56).'</div>'."\xA";
                }

                echo indent(56).'<label for="marca" class="col-md-3 control-label">Marca:</label>'."\xA";
                echo indent(56).'<div class="col-md-9">'."\xA";
                echo indent(60).'<input type="text" class="form-control" rel="marca" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                echo indent(56).'</div>'."\xA";

                if($_SESSION['rol'] == 0){
                    if(!isset($_GET['camplist'])){
                        echo indent(56).'<label for="campaign" class="col-md-3 control-label">Campaña:</label>'."\xA";
                        echo indent(56).'<div class="col-md-9">'."\xA";
                        echo indent(60).'<input type="text" class=" form-control" rel="campaign" id="campaign" name="campaign" value="'.$nombrecamp.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                        echo indent(56).'</div>'."\xA";
                    }
                    if($opcion != "1" && $opcion != "2"){
                        echo indent(56).'<label for="nombrecoord" class="col-md-3 control-label">Nombre del coordinador:</label>'."\xA";
                        echo indent(56).'<div class="col-md-9">'."\xA";
                        echo indent(60).'<input type="text" class=" form-control" rel="nombrecoord" id="nombrecoord" name="nombrecoord" value="'.$nombrecoord.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
                        echo indent(56).'</div>'."\xA";
                    }
                }
                echo indent(52).'</div>'."\xA";
                echo indent(48).'</div>'."\xA";
            }
        }
        if(isset($_GET['camplist']))
            break 1;
        elseif(isset($_SESSION['campid']) || $opcion == "1" || $opcion == "2")
            break;
    }
    if($opcion == "0"){
        echo indent(48).'<script>'."\xA";
        echo indent(52).'$("#selectorCampaign").on("changed.bs.select", function (e) {'."\xA";
        echo indent(56).'var val = $("#selectorCampaign").val();'."\xA";
        echo indent(56).'if(val == "Todas las campañas")'."\xA";
        echo indent(56).'{'."\xA";
        echo indent(60).'window.location.href = "device.php?ic=0";'."\xA";
        echo indent(56).'}'."\xA";
        echo indent(56).'else'."\xA";
        echo indent(56).'{'."\xA";
        echo indent(60).'window.location.href = "device.php?ic=0&camplist="+val;'."\xA";
        echo indent(56).'}'."\xA";
        echo indent(52).'});'."\xA";
        echo indent(48).'</script>'."\xA";   
        echo indent(44).'</form>' . "\xA";
    }
    if(isset($_GET['camplist'])) echo indent(40).'<script>document.getElementById("campseleccionada").innerHTML = "'.$listaCampaigns[$_GET['camplist']]['nombre'].' ('.count( $cant[$_GET['camplist']] ).')"; </script>'."\xA";
    else{
        if($opcion == "0")       echo indent(40).'<script>document.getElementById("campseleccionada").innerHTML = "Todas las campañas ('.getTotalDiademas().')"; </script>'."\xA";
        else if($opcion == "1")  echo indent(40).'<script>document.getElementById("campseleccionada").innerHTML = "Cantidad de diademas que se encuentran en la oficina de Mantenimiento: '.count($cant['6118']).'"; </script>'."\xA";
        else if($opcion == "2")  echo indent(40).'<script>document.getElementById("campseleccionada").innerHTML = "En reparación ('.count($cant['6118']).')"; </script>'."\xA";
        else                     echo indent(40).'<script>document.getElementById("campseleccionada").innerHTML = "'.$listaCampaigns[$camp]['nombre'].' ('.count(getCantidadDiademasPorCampaign()[$camp]).')"; </script>'."\xA";
    }
}
function crearCamp()
{
    $padding = 10;
    $conn = fSesion();
    $sql = "select id_ubicacion, nombre_ubicacion from ubicaciones order by nombre_ubicacion asc";
    $stmt = sqlsrv_query($conn, $sql);
    
    echo            '<form class="form-horizontal" role="form" action="crear_camp.php" method="post">' . "\xA";
    echo indent(02).'<div align="center">' . "\xA";
    echo indent(20).'<!--Formulario-->';
    echo indent(04).'<div class="form-group">' . "\xA";
    echo indent(06).'<label for="idcamp" class="col-md-4 control-label">ID:</label>' . "\xA";
    echo indent(08).'<div class="col-md-6">' . "\xA";
    echo indent(10).'<input type="text" class="form-control back-tooltips" id="idcamp" name="idcamp" autocomplete="off" placeholder="Ingrese el identificador de la campaña" required autofocus>' . "\xA";
    echo indent(08).'</div>' . "\xA";
    echo indent(04).'</div>' . "\xA";
    echo indent(04).'<div class="form-group">' . "\xA";
    echo indent(06).'<label for="nombre" class="col-md-4 control-label">Nombre: </label>';
    echo indent(08).'<div class="col-md-6">' . "\xA";
    echo indent(10).'<input type="text" class="form-control back-tooltip" id="nombre" name="nombre" placeholder="Nombre de la campaña" autocomplete="off" required>' . "\xA";
    echo indent(08).'</div>' . "\xA";
    echo indent(04).'</div>' . "\xA";
    echo indent(04).'<div class="form-group">' . "\xA";
    echo indent(06).'<label for="sede" class="col-md-4 control-label">Sede:</label>' . "\xA";
    echo indent(08).'<div class="col-md-4">' . "\xA";
    echo indent(10).'<select id="sede" name="sede" class="selectpicker" data-live-search="true" title="Sede en donde se encuentra la campaña" data-width="355px" required>' . "\xA";
    
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        echo indent(14).'<option value="'.$row['id_ubicacion'].'">'.$row['nombre_ubicacion'].'</option>';
    }
    
    echo indent(10).'</select>' . "\xA";
    echo indent(08).'</div>' . "\xA";
    echo indent(04).'</div>' . "\xA";
    echo indent(04).'<div class="form-group">' . "\xA";
    echo indent(06).'<div class="col-md-10" align="right">' . "\xA";
    echo indent(08).'<fieldset>' . "\xA";
    
    if(isset($_GET['ag'])){
        $padding = 25;
        if($_GET['ag'] == 0){
            echo indent(16).'<label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo indent(20).'<strong>Error al crear. Valide la información</strong>';
            echo indent(16).'</label>';
        }
        else if($_GET['ag'] == 1){
            echo indent(16).'<label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo indent(20).'<strong>Campaña agregada correctamente</strong>';
            echo indent(16).'</label>';
        }
    }
    
    echo indent(12).'<button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>' . "\xA";
    echo indent(14).'</fieldset>' . "\xA";
    echo indent(12).'</div>' . "\xA";
    echo indent(8).'</form>' . "\xA";
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
            echo indent(08).'<div class="form-group">' . "\xA";
            echo indent(12).'<label for="nombre'.$index.'" class="col-md-4 control-label">Nombre de la campaña:</label>' . "\xA";
            echo indent(12).'<div class="col-md-6">' . "\xA";
            echo indent(16).'<input type="text" class="form-control back-tooltips" id="nombre'.$index.'" name="nombre'.$index.'" value="'.$campaigns["nombre"].'" disabled>' . "\xA";
            echo indent(12).'</div>' . "\xA";
            echo indent(12).'<label for="cantagentes'.$index.'" class="col-md-4 control-label">Cantidad de agentes:</label>' . "\xA";
            echo indent(12).'<div class="col-md-6">' . "\xA";
            echo indent(16).'<input type="text" class="form-control back-tooltips" id="cant'.$index.'" name="cant'.$index.'" value="'.$cant['total'].'" disabled>' . "\xA";
            echo indent(12).'</div>' . "\xA";
            echo indent(12).'<label for="cantdiademas'.$index.'" class="col-md-4 control-label">Cantidad de diademas:</label>' . "\xA";
            echo indent(12).'<div class="col-md-6">' . "\xA";
            echo indent(16).'<input type="text" class="form-control back-tooltips" id="cant'.$index.'" name="cant'.$index.'" value="'.$cant['total'].'" disabled>' . "\xA";
            echo indent(12).'</div>' . "\xA";
            echo indent(08).'</div>' . "\xA";
        }
    }
    echo indent(10).'</div>' . "\xA";
    echo indent(06).'</div>' . "\xA";
    echo indent(04).'</div>' . "\xA";
    echo '</form>' . "\xA";
    echo '<script>' . "\xA";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_free_stmt($stmt2);
}
function adminCrearDiadema()
{
    $diadema = "'img/diadema1.jpg' width='150px' height='150px'";
    $jabra   = "'img/jabrasn.jpg'  width='150px' height='100px'";
    $padding = 10;
    $index   = 0;

    echo            '<form class="form-horizontal" role="form" action="admin_crear_diadema.php" method="post">'."\xA";
    echo indent(44).'<div align="center">'."\xA";
    echo indent(52).'<p class="text-left"><small>Utilice esta opción para crear dispositivos. Quedarán almacenados como dispositivos en stock.</small></p>'."\xA";
    echo indent(48).'<!--Formulario-->'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<label for="serial" class="col-md-4 control-label">Serial:</label>'."\xA";
    echo indent(52).'<div class="col-md-6">'."\xA";
    echo indent(56).'<input type="text" data-toggle="tooltip" title="<br><img src='.$diadema.'>'."\xA";
    echo indent(57).'<br><br>Verifique el consecutivo grabado en la bocina de la diadema.<br><br>"'."\xA";
    echo indent(57).'class="form-control back-tooltips" rel="serial" id="serial" name="serial" autocomplete="off" placeholder="Consecutivo grabado en el auricular" required autofocus>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<label for="marca" class="col-md-4 control-label">Marca:</label>'."\xA";
    echo indent(52).'<div class="col-md-4">'."\xA";
    echo indent(56).'<select id="marca" name="marca" class="selectpicker" data-live-search="true" title="Seleccione una marca" data-width="355px" required>'."\xA";
    echo indent(60).'<option value="Jabra">Jabra</option>'."\xA";
    echo indent(60).'<option value="Plantronics">Plantronics</option>'."\xA";
    echo indent(60).'<option value="China">China</option>'."\xA";
    echo indent(56).'</select>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<label for="serialnumber" class="col-sm-4 control-label">S/N: </label>'."\xA";
    echo indent(52).'<div class="col-md-6">'."\xA";
    echo indent(56).'<input type="text" data-toggle="tooltip" title="<br><img src='.$jabra.'>'."\xA";
    echo indent(57).'<br><br>Si la diadema es marca Jabra, ubique el S/N e ingreselo.<br><br>"'."\xA";
    echo indent(57).'class="form-control bfh-number back-tooltip" name="serialnumber" id="serialnumber" autocomplete="off" disabled required>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<div class="col-md-10" align="right">'."\xA";
    echo indent(56).'<fieldset>'."\xA";
    
    if(isset($_GET['ag'])){
        $padding = 25;
        if($_GET['ag'] == "0"){
            echo indent(60).'<label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
            echo indent(64).'<strong>Error: Serial '.$_GET["sd"].' duplicado</strong>'."\xA";
            echo indent(60).'</label>'."\xA";
        } else if($_GET['ag'] == 1) {
            echo indent(60).'<label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center" style="padding: 15px;">'."\xA";
            echo indent(64).'<strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>'."\xA";
            echo indent(60).'</label>'."\xA";
        }
    }

    echo indent(60).'<button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Agregar</button>'."\xA";
    echo indent(56).'</fieldset>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(44).'</div>'."\xA";
    echo indent(40).'</form>'."\xA";
    echo indent(40).'<script>'."\xA";
    echo indent(44).'$("#marca").on("changed.bs.select", function(e) {'."\xA";
    echo indent(48).'var val = $("#marca").val();'."\xA";
    echo indent(48).'if (val == "Jabra") $("#serialnumber").prop("disabled", false);'."\xA";
    echo indent(48).'else $("#serialnumber").prop("disabled", true);'."\xA";
    echo indent(44).'});'."\xA";
    echo indent(40).''."\xA";
    echo indent(40).'</script>'."\xA";
                                            
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
            "nombre"     => correccionTexto($ppl['nombres']." ".$ppl['apellidos']),
            "idcamp"     => $ppl['idcamp'],
            "nombrecamp" => $ppl['nombrecamp']
        );
    }
    return $coordinadores;
}
function getListaCampaigns()
{
    $conn = fSesion();
    $sql1 = "select * from campaigns order by nombre_campaign";
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
    echo            '<form class="form-horizontal" role="form" action="creartecnico.php" method="post">'."\xA";
    echo indent(28).'<div>'."\xA";
    echo indent(28).'<!--Formulario-->'."\xA";
    echo indent(32).'<br>'."\xA";
    echo indent(32).'<div class="form-group">'."\xA";
    echo indent(36).'<label for="nombres" class="col-md-4 control-label">Nombres:</label>'."\xA";
    echo indent(36).'<div class="col-md-6">'."\xA";
    echo indent(40).'<input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" placeholder="Nombre(s) del técnico" required autofocus>'."\xA";
    echo indent(36).'</div>'."\xA";
    echo indent(32).'</div>'."\xA";
    echo indent(32).'<div class="form-group">'."\xA";
    echo indent(36).'<label for="apellidos" class="col-md-4 control-label">Apellidos:</label>'."\xA";
    echo indent(36).'<div class="col-md-6">'."\xA";
    echo indent(40).'<input type="text" class="form-control" rel="apellidos" id="apellidos" name="apellidos" autocomplete="off" placeholder="Apellidos del técnico" required autofocus>'."\xA";
    echo indent(36).'</div>'."\xA";
    echo indent(32).'</div>'."\xA";
    echo indent(32).'<div class="form-group">'."\xA";
    echo indent(36).'<label for="lider" class="col-md-4 control-label">Líder:</label>'."\xA";
    echo indent(36).'<div class="col-md-6">'."\xA";
    echo indent(40).'<select id="lider" name="lider" class="selectpicker" data-live-search="true" title="Seleccione el líder del técnico que está creando" data-width="400px" required>'."\xA";
    
    llamarLideres();
    
    echo indent(40).'</select>'."\xA";
    echo indent(36).'</div>'."\xA";
    echo indent(32).'</div>'."\xA";
    echo indent(32).'<div class="form-group">'."\xA";
    echo indent(36).'<label for="ubicacion" class="col-md-4 control-label">Ubicación:</label>'."\xA";
    echo indent(36).'<div class="col-md-6">'."\xA";
    echo indent(40).'<select id="ubicacion" name="ubicacion" class="selectpicker" data-live-search="true" title="Seleccione la sede en donde se encuentra el técnico" data-width="400px" required>'."\xA";

    llamarUbicaciones();
    
    echo indent(40).'</select>'."\xA";
    echo indent(36).'</div>'."\xA";
    echo indent(32).'</div>'."\xA";
    echo indent(32).'<div class="form-group">'."\xA";
    echo indent(36).'<div class="col-md-10" align="right">'."\xA";
    echo indent(40).'<fieldset>'."\xA";
    echo indent(44).'<button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Crear</button>'."\xA";
    echo indent(40).'</fieldset>'."\xA";
    echo indent(36).'</div>'."\xA";
    echo indent(32).'</div>'."\xA";
    echo indent(28).'</div>'."\xA";
    echo indent(28).'</form>'."\xA";
}
function llamarLideres()
{
    $conn = fSesion();
    $sql = "select id_admin, nombres_admin, apellidos_admin, rol_admin from admins order by nombres_admin";
    $stmt= sqlsrv_query($conn, $sql);
    while($ppl = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        if($ppl['rol_admin'] == "1"){
            $nombrelider = $ppl['nombres_admin']." ".$ppl['apellidos_admin'];
            echo indent(44).'<option value="'.$ppl['id_admin'].'">'.$nombrelider.'</option>'."\xA";
        }
    }
}
function llamarUbicaciones()
{
    $conn = fSesion();
    $sql = "select id_ubicacion, nombre_ubicacion from ubicaciones order by nombre_ubicacion";
    $stmt= sqlsrv_query($conn, $sql);
    while($ppl = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        echo indent(44).'<option value="'.$ppl['id_ubicacion'].'">'.$ppl['nombre_ubicacion'].'</option>'."\xA";
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

    foreach($cursor as $doc)
        array_push($resbod, $doc);

    for($i = 0; $i < count($resbod); $i++){
        if(end($resbod[$i]['resumen'])['estado'] == "0"){
            array_push($diademasbod, str_replace(" ", "", $resbod[$i]['_id']));
        }
    }
    echo indent(04).'<form class="form-horizontal" role="form" action="cambiodiadema.php" method="post">' . "\xA";
    echo            '<small><p>Utilice este formulario para realizar cambio de diademas.</small></p>';
    echo indent(40).'<div align="center">' . "\xA";
    echo indent(44).'<div class="form-group">'."\xA";
    echo indent(48).'<label for="campid" class="col-md-4 control-label">Seleccione la campaña:</label>'."\xA";
    echo indent(48).'<div class="col-md-4">'."\xA";
    echo indent(52).'<select data-size="7" id="campid" name="campid" class="selectpicker form-control" data-live-search="true" title="Seleccione una campaña" required autocomplete="off" data-width="355px">'."\xA";

    for($i = 0; $i < count($idcamps); $i++){
        if($idcamps[$i] != "6118")
        echo indent(56).'<option value="'.$idcamps[$i].'">'.$campaigns[$idcamps[$i]]['nombre'].'</option>'."\xA";
    }
    
    sqlsrv_free_stmt($stmt);

    echo indent(52).'</select>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(44).'</div>'."\xA";
    echo indent(44).'<!--Formulario-->'."\xA";
    echo indent(44).'<div class="form-group">' . "\xA";
    echo indent(48).'<label for="coordid" class="col-md-4 control-label">Seleccione un coordinador:</label>' . "\xA";
    echo indent(48).'<div class="col-md-4">' . "\xA";
    echo indent(52).'<select id="coordid" name="coordid" class="selectpicker" data-live-search="true" title="Seleccione un coordinador" data-width="355px" required>' . "\xA";

    for($i = 0; $i < count($idcoords); $i++){
        $idcoord = $idcoords[$i];
        echo indent(56).'<option value="'.$idcoord.'" data-tokens="'.$coords[$idcoord]['nombrecamp'].' '.ucwords(mb_strtolower($coords[$idcoord]['nombre'])).'">'.ucwords(mb_strtolower($coords[$idcoord]['nombre'])).'</option>'."\xA";
    }

    echo indent(52).'</select>' . "\xA";
    echo indent(48).'</div>' . "\xA";
    echo indent(44).'</div>' . "\xA";
    echo indent(44).'<div class="form-group">' . "\xA";
    echo indent(48).'<label for="diademaentrante" class="col-md-4 control-label">Serial de la diadema recogida:</label>' . "\xA";
    echo indent(48).'<div class="col-md-4">' . "\xA";
    echo indent(52).'<select id="diademaentrante" name="diademaentrante" class="selectpicker" data-live-search="true" title="Seleccione el serial de la diadema recogida" data-width="355px" required>' . "\xA";
    
    for($i = 0; $i < count($diademascamp); $i++){
        $idcamp = end($rescamp[$i]['resumen'])['campaign'];
        echo indent(56).'<option value="'.$diademascamp[$i].'" data-tokens="'.$campaigns[$idcamp]['nombre'].' '.$diademascamp[$i].'">'.$diademascamp[$i].'</option>'."\xA";
    }
    
    echo indent(52).'</select>' . "\xA";
    echo indent(48).'</div>' . "\xA";
    echo indent(44).'</div>' . "\xA";
    echo indent(44).'<div class="form-group">' . "\xA";
    echo indent(48).'<label for="diademasaliente" class="col-md-4 control-label">Serial de la diadema entregada:</label>' . "\xA";
    echo indent(52).'<div class="col-md-4">' . "\xA";
    echo indent(56).'<select id="diademasaliente" name="diademasaliente" class="selectpicker" data-live-search="true" title="Seleccione el serial de la diadema entregada" data-width="355px" required>' . "\xA";
    
    for($i = 0; $i < count($diademasbod); $i++){
        echo indent(60).'<option value="'.$diademasbod[$i].'">'.$diademasbod[$i].'</option>';
    }
    
    echo indent(56).'</select>' . "\xA";
    echo indent(52).'</div>' . "\xA";
    echo indent(48).'</div>' . "\xA";
    echo indent(44).'<div class="form-group">' . "\xA";
    echo indent(48).'<div class="col-md-10" align="right">' . "\xA";
    echo indent(52).'<fieldset>' . "\xA";
    
    if(isset($_GET['ag'])){
        $padding = 15;
        if($_GET['ag'] == 0){
            echo indent(56).'<label for="agregar" class="alert alert-danger col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo indent(60).'<strong>Error: Serial '.$_GET["sd"].' duplicado</strong>' . "\xA";
            echo indent(56).'</label>' . "\xA";
        }
        else if($_GET['ag'] == 1){
            echo indent(56).'<label for="agregar" class="alert alert-success col-md-4 col-md-offset-5 text-center"  style="padding: 15px;">' . "\xA";
            echo indent(60).'<strong>Diadema '.$_GET["sd"].' agregada correctamente</strong>' . "\xA";
            echo indent(56).'</label>' . "\xA";
        }
    }
    echo indent(52).'<button type="submit" class="btn btn-success" id="agregar" name="agregar" style="padding: '.$padding.'px;">Realizar cambio</button>' . "\xA";
    echo indent(48).'</fieldset>' . "\xA";
    echo indent(44).'</div>' . "\xA";
    echo indent(40).'</form>' . "\xA";
    echo indent(40).'<script>' . "\xA";
    echo indent(44).'$("#marca").on("changed.bs.select", function (e) {' . "\xA";
    echo indent(48).'var val = $("#marca").val();' . "\xA";
    echo indent(48).'if(val == "Jabra") $( "#serialnumber" ).prop( "disabled", false );' . "\xA";
    echo indent(48).'else $( "#serialnumber" ).prop( "disabled", true );' . "\xA";
    echo indent(44).'});' . "\xA";
    echo indent(40).'</script>' . "\xA";
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
        echo indent(28).'<div>'."\xA";
        echo indent(28).'<!--Formulario-->'."\xA";
        echo indent(32).'<br>'."\xA";
        echo indent(32).'<div class="form-group">'."\xA";
        echo indent(36).'<label for="idtecnico" class="col-md-4 control-label">ID:</label>'."\xA";
        echo indent(36).'<div class="col-md-6">'."\xA";
        echo indent(40).'<input type="text" class="form-control" id="idtecnico" name="idtecnico" autocomplete="off" value="'.$idtecnico.'" disabled>'."\xA";
        echo indent(36).'</div>'."\xA";
        echo indent(36).'<label for="nombres" class="col-md-4 control-label">Nombres:</label>'."\xA";
        echo indent(36).'<div class="col-md-6">'."\xA";
        echo indent(40).'<input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" value="'.$nombreTecnico.'" disabled>'."\xA";
        echo indent(36).'</div>'."\xA";
        echo indent(36).'<label for="lider" class="col-md-4 control-label">Líder:</label>'."\xA";
        echo indent(36).'<div class="col-md-6">'."\xA";
        echo indent(40).'<input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" value="'.$lider.'" disabled>'."\xA";
        echo indent(36).'</div>'."\xA";
        echo indent(36).'<label for="ubicacion" class="col-md-4 control-label">Ubicación:</label>'."\xA";
        echo indent(36).'<div class="col-md-6">'."\xA";
        echo indent(40).'<input type="text" class="form-control" id="nombres" name="nombres" autocomplete="off" value="'.$ubicacion.'" disabled>'."\xA";
        echo indent(36).'</div>'."\xA";
        echo indent(32).'</div>'."\xA";
        echo indent(28).'</div>'."\xA";
        echo indent(24).'</form>'."\xA";
        echo indent(24).'<script>document.getElementById("tecnicoheader").innerHTML = "Ver técnicos";</script>';
    }
}
function getDiademasEnStock()
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
function getDiademasEnReparacion()
{
    $collection = fMongoDB();
    $cursor = $collection->find();
    $diademasenreparacion = array();
    
    foreach($cursor as $document){
        if(end($document['resumen'])['estado'] == "2"){
            array_push($diademasenreparacion, $document);
        }
    }
    return $diademasenreparacion;
}
function getDiademasEnCamp()
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
    $diademas = getDiademasEnCamp();
    $campaigns = getListaCampaigns();

    echo '<form action="recoger.php" method="post" class="form-horizontal">'."\xA";
    echo indent(04).'<p class="text-left"><small>Utilice esta opción para seleccionar una o varias diademas que se deban recoger.</small></p>';
    echo indent(04).'<fieldset>'."\xA";
    echo indent(08).'<div class="form-group">'."\xA";
    echo indent(12).'<label class="col-md-2 control-label" for="id" name="id"></label>'."\xA";
    echo indent(12).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo indent(16).'<span class="input-group-addon"></span>'."\xA";
    echo indent(16).'<select id="diademas[]" name="diademas[]" class="selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Seleccione las diademas a recoger" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($diademas); $i++){
        $idcamp = end($diademas[$i]['resumen'])['campaign'];
        $nombrecamp = $campaigns[$idcamp]['nombre'];
        echo indent(24).'<option data-tokens="'.$nombrecamp.' '.$diademas[$i]['_id'].'">'.$diademas[$i]['_id'].'</option>'."\xA";
    }
    
    echo indent(16).'</select>'."\xA";
    echo indent(12).'</div>'."\xA";
    echo indent(08).'</div>'."\xA";
    echo indent(08).'<div class="form-group">'."\xA";
    echo indent(12).'<div class="col-md-10 input-group" style="outline: 0px" align="right">'."\xA";
    echo indent(16).'<fieldset>'."\xA";
    echo indent(20).'<button id="ingresar" name="ingresar" class="btn btn-primary">Recoger</button>'."\xA";
    echo indent(16).'</fieldset>'."\xA";
    echo indent(12).'</div>'."\xA";
    echo indent(08).'</div>'."\xA";
    echo indent(04).'</fieldset>'."\xA";
    echo '</form>'."\xA";
    
}
function getCantidadReparaciones()
{
    $collection = fMongoDB();
    $cursor     = $collection->find();
    $mants      = array();
    $diademas   = array();
    
    foreach($cursor as $document){
        array_push($diademas, $document);
    }
    
    for($i = 0; $i < count($diademas); $i++){
        $diadematemp    = $diademas[$i];
        $mant           = 0;
        for($j = 0; $j < count($diadematemp['resumen']); $j++){
            $restemp    = $diadematemp['resumen'][$j];
            if($restemp['estado'] == "2"){
                $mant++;
            }
            $mants[$diadematemp['_id']] = $mant;
        }
    }
    return $mants;
}
function repararDiademas()
{
    $diademas = getDiademasEnStock();

    echo '<form class="form-horizontal" role="form" action="reparar.php" method="post">'."\xA";
    echo '<p class="text-left"><small>Utilice esta opción para seleccionar las diademas que se encuentren en la oficina de Mantenimiento y deban ser reparadas.</small></p>';
    echo indent(44).'<fieldset>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<label class="col-md-2 control-label" for="id" name="id"></label>'."\xA";
    echo indent(52).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo indent(56).'<span class="input-group-addon"></span>'."\xA";
    echo indent(56).'<select id="diademas[]" name="diademas[]" class="selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Seleccione las diademas para reparar" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($diademas); $i++){
        echo indent(60).'<option>'.$diademas[$i]['_id'].'</option>'."\xA";
    }
    
    echo indent(56).'</select>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<div class="col-md-10 input-group" style="outline: 0px" align="right">'."\xA";
    echo indent(56).'<fieldset>'."\xA";
    echo indent(60).'<button id="ingresar" name="ingresar" class="btn btn-success">Enviar a reparación</button>'."\xA";
    echo indent(56).'</fieldset>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(44).'</fieldset>'."\xA";
    echo indent(40).'</form>'."\xA";
    
}
function recibirDiademasDeReparacion()
{
    $diademas = getDiademasEnReparacion();

    echo '<form class="form-horizontal" role="form" action="recoger.php" method="post">'."\xA";
    echo '<p class="text-left"><small>Use esta opción para recibir diademas de reparación. Si alguna de las diademas que se están recibiendo no cuentan con el consecutivo grabado en el auricular, esta debe ser marcada y creada desde la opción "crear diadema".</small></p>';
    echo indent(44).'<fieldset>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<label class="col-md-2 control-label" for="id" name="id"></label>'."\xA";
    echo indent(52).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo indent(56).'<span class="input-group-addon"></span>'."\xA";
    echo indent(56).'<select id="diademas[]" name="diademas[]" class="selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Seleccione las diademas a recoger" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($diademas); $i++){
        echo indent(60).'<option>'.$diademas[$i]['_id'].'</option>'."\xA";
    }
    
    echo indent(56).'</select>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<div class="col-md-10 input-group" style="outline: 0px" align="right">'."\xA";
    echo indent(56).'<fieldset>'."\xA";
    echo indent(60).'<button id="ingresar" name="ingresar" class="btn btn-primary">Recoger</button>'."\xA";
    echo indent(56).'</fieldset>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(44).'</fieldset>'."\xA";
    echo indent(40).'</form>'."\xA";
    
}
function verDiademasEnReparacion()
{
    $cant  = getDiademasEnReparacion();
    $inner = '<center><a href="exportardiademas.php" class="text-success">Descargar en formato .xls<span class="glyphicon glyphicon-download-alt"></span></a></center><br>';
    echo indent(04).'<form class="form-horizontal" role="form">'."\xA";
    echo indent(48).'<h4 id="campseleccionada">&nbsp;</h4>';
                                        
    for($i = 0; $i < count($cant); $i++){
        
        $id     = $cant[$i]['_id'];
        $marca  = $cant[$i]['Marca'];

        echo '<div class="form-group">'."\xA";
        echo indent(52).'<div>'."\xA";
        echo indent(56).'<label for="iddiadema" class="col-md-3 control-label">Consecutivo:</label>'."\xA";
        echo indent(56).'<div class="col-md-9 ">'."\xA";
        echo indent(60).'<input type="text" class="form-control" rel="iddiadema" id="iddiadema" name="iddiadema" value="'.$id.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
        echo indent(56).'</div>'."\xA";
        echo indent(56).'<label for="marca" class="col-md-3 control-label">Marca:</label>'."\xA";
        echo indent(56).'<div class="col-md-9">'."\xA";
        echo indent(60).'<input type="text" class="form-control" rel="marca" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
        echo indent(56).'</div>'."\xA";
        echo indent(52).'</div>'."\xA";
        echo indent(48).'</div>'."\xA";
    }
    echo indent(44).'<script>document.getElementById("campseleccionada").innerHTML = "Cantidad de diademas que se encuentran en reparación: '.count($cant).'"; </script>'."\xA";
}
function entregarDiademas()
{
    $diademas   = getDiademasEnStock();
    $campaigns  = getListaCampaigns();
    $coords     = getListaCoordinadores();
    $idcamps    = array_keys($campaigns);
    $idcoords   = array_keys($coords);

    echo indent(00).'<form action="entregar.php" method="post" class="form-horizontal">'."\xA";
    echo indent(00).'<p class="text-center"><small>Utilice esta opción para seleccionar una o varias diademas que se van a entregar.</small></p>';
    echo indent(04).'<fieldset>'."\xA";
    echo indent(08).'<div class="form-group">'."\xA";
    echo indent(12).'<label class="col-md-2 control-label" for="campid" name="campid"></label>'."\xA";
    echo indent(12).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo indent(16).'<span class="input-group-addon"></span>'."\xA";
    echo indent(16).'<select data-size="7" id="campid" name="campid" class="selectpicker" data-live-search="true" data-selected-text-format="count" title="Seleccione una campaña" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($idcamps); $i++){
        if($idcamps[$i] != "6118"){
            echo indent(56).'<option value="'.$idcamps[$i].'">'.$campaigns[$idcamps[$i]]['nombre'].'</option>'."\xA";
        }
    }
    
    sqlsrv_free_stmt($stmt);
    
    echo indent(16).'</select>'."\xA";
    echo indent(12).'</div>'."\xA";
    echo indent(08).'</div>'."\xA";
    
    echo indent(08).'<div class="form-group">'."\xA";
    echo indent(12).'<label class="col-md-2 control-label" for="coordid" name="coordid"></label>'."\xA";
    echo indent(12).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo indent(16).'<span class="input-group-addon"></span>'."\xA";
    echo indent(16).'<select data-size="7" id="coordid" name="coordid" class="selectpicker" data-live-search="true" data-selected-text-format="count" title="Seleccione un coordinador" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($idcoords); $i++){
        $idcoord = $idcoords[$i];
        echo indent(56).'<option value="'.$idcoord.'" data-tokens="'.$coords[$idcoord]['nombrecamp'].' '.ucwords(mb_strtolower($coords[$idcoord]['nombre'])).'">'.ucwords(mb_strtolower($coords[$idcoord]['nombre'])).'</option>'."\xA";
    }
    
    echo indent(16).'</select>'."\xA";
    echo indent(12).'</div>'."\xA";
    echo indent(08).'</div>'."\xA";
    
    echo indent(08).'<div class="form-group">'."\xA";
    echo indent(12).'<label class="col-md-2 control-label" for="diademas[]" name="diademas[]"></label>'."\xA";
    echo indent(12).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA";
    echo indent(16).'<span class="input-group-addon"></span>'."\xA";
    echo indent(16).'<select id="diademas[]" name="diademas[]" class="selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Seleccione las diademas a entregar" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($diademas); $i++){
        $idcamp = end($diademas[$i]['resumen'])['campaign'];
        $nombrecamp = $campaigns[$idcamp]['nombre'];
        echo indent(24).'<option data-tokens="'.$diademas[$i]['_id'].'">'.$diademas[$i]['_id'].'</option>'."\xA";
    }
    
    echo indent(16).'</select>'."\xA";
    echo indent(12).'</div>'."\xA";
    echo indent(08).'</div>'."\xA";
    
    echo indent(08).'<div class="form-group">'."\xA";
    echo indent(12).'<div class="col-md-10 input-group" style="outline: 0px" align="right">'."\xA";
    echo indent(16).'<fieldset>'."\xA";
    echo indent(20).'<button id="entregar" name="entregar" class="btn btn-primary">Entregar</button>'."\xA";
    echo indent(16).'</fieldset>'."\xA";
    echo indent(12).'</div>'."\xA";
    echo indent(08).'</div>'."\xA";
    echo indent(04).'</fieldset>'."\xA";
    echo indent(00).'</form>'."\xA";
    
}
function indent($contador)
{
    for($i = 0; $i<$contador; $i++) echo " ";
}
function bajaDiademas()
{
    $diademas = getDiademasEnStock();

    echo '<form class="form-horizontal" role="form" action="baja.php" method="post">'."\xA";
    echo indent(44).'<p class="text-center"><small>Utilice esta opción para seleccionar las diademas que se deban dar de baja.</small></p>'."\xA";
    echo indent(44).'<fieldset>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<label class="col-md-2 control-label" for="id" name="id"></label>'."\xA";
    echo indent(52).'<div class="col-md-8 input-group" style="outline: 0px>'."\xA" ;
    echo indent(56).'<span class="input-group-addon"></span>'."\xA";
    echo indent(56).'<select id="diademas[]" name="diademas[]" class="selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Seleccione las diademas para reparar" data-width="100%">'."\xA";
    
    for($i = 0; $i < count($diademas); $i++) echo indent(60).'<option>'.$diademas[$i]['_id'].'</option>'."\xA";
    
    echo indent(56).'</select>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(48).'<div class="form-group">'."\xA";
    echo indent(52).'<div class="col-md-8 input-group col-md-offset-2" align="right">'."\xA";
    echo indent(56).'<fieldset>'."\xA";
    echo indent(60).'<button id="ingresar" name="ingresar" class="btn btn-success">Dar de baja</button>'."\xA";
    if(isset($_GET['m'])){
        echo indent(60).'<label class="alert alert-success col-md-5 text-center">'."\xA";
        echo indent(64).'<strong>Diademas dadas de baja correctamente</strong>'."\xA";
        echo indent(60).'</label>'."\xA";
    }
    echo indent(56).'</fieldset>'."\xA";
    echo indent(52).'</div>'."\xA";
    echo indent(48).'</div>'."\xA";
    echo indent(44).'</fieldset>'."\xA";
    echo indent(40).'</form>'."\xA";
    
}
function getDiademasEnBaja()
{
    $collection = fMongoDB();
    $cursor = $collection->find();
    $diademasenreparacion = array();
    
    foreach($cursor as $document){
        if(end($document['resumen'])['estado'] == "3"){
            array_push($diademasenreparacion, $document);
        }
    }
    return $diademasenreparacion;
}
function verDiademasEnBaja()
{
    $cant  = getDiademasEnBaja();
    //$inner = '<center><a href="exportardiademas.php" class="text-success">Descargar en formato .xls<span class="glyphicon glyphicon-download-alt"></span></a></center><br>';
    echo indent(04).'<form class="form-horizontal" role="form">'."\xA";
    echo indent(48).'<h4 id="campseleccionada">&nbsp;</h4>';
    echo ''."\xA";
    for($i = 0; $i < count($cant); $i++){
        
        $id    = $cant[$i]['_id'];
        $marca = $cant[$i]['Marca'];
        $fecha = end($cant[$i]['resumen'])['fechaMov'];

        echo indent(44).'<div class="form-group">'."\xA";
        echo indent(48).'<div>'."\xA";
        echo indent(52).'<label for="iddiadema" class="col-md-3 control-label">Consecutivo:</label>'."\xA";
        echo indent(52).'<div class="col-md-9 ">'."\xA";
        echo indent(56).'<input type="text" class="form-control" rel="iddiadema" id="iddiadema" name="iddiadema" value="'.$id.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
        echo indent(52).'</div>'."\xA";
        echo indent(48).'<div>'."\xA";
        echo indent(52).'<label for="fecha" class="col-md-3 control-label">Fecha:</label>'."\xA";
        echo indent(52).'<div class="col-md-9 ">'."\xA";
        echo indent(56).'<input type="text" class="form-control" rel="fecha" id="fecha" name="fecha" value="'.$fecha.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
        echo indent(52).'</div>'."\xA";
        echo indent(52).'<label for="marca" class="col-md-3 control-label">Marca:</label>'."\xA";
        echo indent(52).'<div class="col-md-9">'."\xA";
        echo indent(56).'<input type="text" class="form-control" rel="marca" id="marca" name="marca" value="'.$marca.'" data-toggle="tooltip" autocomplete="off" disabled>'."\xA";
        echo indent(52).'</div>'."\xA";
        echo indent(48).'</div>'."\xA";
        echo indent(48).'</div>'."\xA";
        echo indent(44).'</div>'."\xA";
    }
    echo indent(44).'<script>document.getElementById("campseleccionada").innerHTML = "Cantidad de diademas que se encuentran en baja: '.count($cant).'"; </script>'."\xA";
}
function getUltimoConsecutivo()
{
    $conexion = fSesion();    
    $sql = "SELECT top 1 * FROM consecutivo ORDER BY id_consecutivo DESC";
    $qry = sqlsrv_query($conexion, $sql);
    $resultado = sqlsrv_fetch_array($qry);
    return $resultado;
}