<?php
include 'php/php_func.php';
session_start();
fTimeStamp();
comprobarAdmin();
fTimeStamp();
$conexion    = fSesion();
$collection  = fMongoDB();
$cursor      = $collection->find();

$consecutivo = strtoupper($_POST['consecutivonuevo']);
$tecid       = $_SESSION['id'];
$ingresar    = true;

$sql = "insert into consecutivo (numero_consecutivo, ultimo_en_actualizar_consecutivo, fecha_consecutivo) values ('$consecutivo', '$tecid', GETDATE());";

foreach($cursor as $diadema){
    if(strtoupper($diadema['_id']) == $consecutivo){
        $ingresar = false;
    }
}
if($ingresar){
    $qry = sqlsrv_query($conexion, $sql);
}else{
    header('Location: consecutivo.php?ic=0');
    die('Diadema duplicada');
}
if(!$qry){
    pprint(sqlsrv_errors());
    header('Location: consecutivo.php?ic=0');
}else{
    header('Location: consecutivo.php?ic=1');
}

?>