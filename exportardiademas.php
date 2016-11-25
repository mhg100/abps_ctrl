<?php
namespace abps;
include 'php/php_func.php';

function cleanData(&$cadena)
{
    $cadena = preg_replace("/\t/", "\\t", $cadena);
    $cadena = preg_replace("/\r?\n/", "\\n", $cadena);
    if(strstr($cadena, '"')) $cadena = '"' . str_replace('"', '""', $cadena) . '"';
}

$archivo = "reporte_diademas".date('dmY').".xls";

header("Content-Disposition: attachment; filename=\"$archivo\"");
header("Content-Type: application/vnd.ms-excel");
header("Content-Type: text/plain");
header('Content-Type: text/html; charset=ISO-8859-1');

$collection = fMongoDB();
$cursor = $collection->find();

$exportable = array();

foreach($cursor as $document)
{
    $temp = array(
        "Id"=>"".$document['_id'],
        "Marca"=>"".$document['Marca'],
        "Serial"=>"".$document['serial'],
        "Campana"=>"".$document['resumen'][0]['campaign'],
        "Coordinador"=>"".$document['resumen'][0]['coordinador_id']);
    
    array_push($exportable, $temp);
    $temp = array();
}

$bandera = false;
foreach($exportable as $row) {
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