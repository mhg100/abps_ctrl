<?php
include 'php/php_func.php';
header('Content-Type: text/html; charset=UTF-8');
session_start();
fTimeStamp();
echo initHTML($_SESSION['rol']);
if($_SESSION['rol'] == 1)
comprobarAdmin();

$conn = fSesion();
$collection = fMongoDB();
$inforgral = array();
$sql = "select * from inventariodiademas order by fecha";
$stmt = sqlsrv_query($conn, $sql);
$char = '"';
$chars= array("'", $char, "=");

while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $_id            = $row['consecutivo'];
    $marca          = $row['marca'];
    $serial         = $row['serial'];
    $ipequipo       = $row['ip_equipo'];
    $nombresAg      = $row['nombre'];
    $coordinador_id = $row['coordinador'];
    $campaign       = $row['campaign'];
    $fechaMov       = $row['fecha'] -> format('Y-m-d H:i');
    
    if($_id == 'No tiene'){
        $_id = $_id." ".$ipequipo;
    }
    $inforgral = array(
        '_id'=>$_id,
        'Marca'=>$marca,
        'serial'=>$serial,
        'resumen'=>[array(
            '_id'=>"001",
            'estado'=>"1",
            'ipequipo'=>$ipequipo,
            'nombresAg'=>$nombresAg,
            'coordinador_id'=>$coordinador_id,
            'campaign'=>$campaign,
            'fechaMov'=>$fechaMov
        )]
    );
    echo '<pre>';
        print_r($inforgral);
    echo '</pre>';
    try {
        $collection->insert($inforgral);
        //header("Location: default.php");
    }
    catch (MongoCursorException $e) {
        echo "error message: ".$e->getMessage()."\n";
        //header("Location: default.php");
    }
    $inforgral = array();
}
?>