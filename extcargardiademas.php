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
    $_id            = str_replace($chars, '¡', $row['consecutivo']);
    $marca          = $row['marca'];
    $serial         = $row['serial'];
    $nombresAg      = str_replace($chars, '¡', $row['ip_equipo']);
    $coordinador_id = $row['coordinador'];
    $campaign       = $row['campaign'];
    $fechaMov       = $row['fecha'] -> format('Y-m-d H:i');
    
    if($_id == 'No tiene'){
        $_id = $_id." ".$nombresAg;
    }
    $inforgral = array(
        '_id'=>$_id,
        'Marca'=>$marca,
        'serial'=>$serial,
        'resumen'=>[array(
            '_id'=>"001",
            'estado'=>"1",
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
        header("Location: http://172.27.48.125:81/index.php?do=1");
    }
    catch (MongoCursorException $e) {
        echo "error message: ".$e->getMessage()."\n";
        header("Location: http://172.27.48.125:81/index.php?do=1");
    }
    $inforgral = array();
}
?>