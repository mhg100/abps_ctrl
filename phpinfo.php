<?php
//phpinfo();
//print_r(get_loaded_extensions());
include 'php/php_func.php';

$conn = fSesion();
$sql1 = "select id_coordinador as id, nombres_coordinador as nombres, apellidos_coordinador as apellidos, cantidad_agentes_coordinador as cantagentes, campaign_coordinador as idcampa from coordinadores";
$stmt1 = sqlsrv_query($conn, $sql1);
$coordinadores = array();


while($ppl = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC))
{
    $coordinadores[$ppl['id']] = array(
    
        "nombre"=>$ppl['nombres']." ".$ppl['apellidos'],
    
    );
}

echo '<pre>';
    print_r($coordinadores);
echo '</pre>';

?>