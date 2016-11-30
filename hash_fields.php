<?php

include 'php/php_func.php';

$conn = fSesion();

$sql = "select * from coordinadores";
$stmt= sqlsrv_query($conn, $sql);

while($ppl  = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)){
    $id     = $ppl['id_coordinador'];
    $clave  = $ppl['pass_coordinador'];
    $cedula = $ppl['cc_coordinador'];
    
    //$modsql = "update coordinadores set pass_coordinador "
}
?>