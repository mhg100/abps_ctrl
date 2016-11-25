<?php
namespace abps;
include 'php/php_func.php';

$collection = fMongoDB();
$cursor = $collection->find();
$coordinadores = getListaCoordinadores();
$archivo = "reporte_diademas".date('dmY').".xls";
$exportable = array();

function cleanData(&$cadena)
{
    $cadena = preg_replace("/\t/", "\\t", $cadena);
    $cadena = preg_replace("/\r?\n/", "\\n", $cadena);
    if(strstr($cadena, '"')) $cadena = '"' . str_replace('"', '""', $cadena) . '"';
}

header("Content-Disposition: attachment; filename=\"$archivo\"");
header("Content-Type: application/vnd.ms-excel");
header("Content-Type: text/plain");
header('Content-Type: text/html; charset=ISO-8859-1');

foreach($cursor as $document){
    $temp = array(
        "Id"=>"".$document['_id'],
        "Marca"=>"".$document['Marca'],
        "Serial"=>"".$document['serial'],
        "Campana"=>"".$document['resumen'][0]['campaign'],
        "Coordinador"=>"".$coordinadores[$document['resumen'][0]['coordinador_id']]['nombre']);
    
    array_push($exportable, $temp);
    $temp = array();
}

$bandera = false;
foreach($exportable as $row){
    if(!$bandera)
    {
        echo implode("\t", array_keys($row)) . "\r\n";
        $bandera = true;

    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    echo implode("\t", array_values($row)) . "\n";
}

exit;


?>