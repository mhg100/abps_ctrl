<?php
namespace abps;
include   'php/php_func.php';
session_start();
fTimeStamp();
function  cleanData(&$cadena)
{
    $cadena = preg_replace("/\t/", "\\t", $cadena);
    $cadena = preg_replace("/\r?\n/", "\\n", $cadena);
    if(strstr($cadena, '"')) $cadena = '"' . str_replace('"', '""', $cadena) . '"';
}
$collection         = fMongoDB();
$cursor             = $collection->find();
$coordinadores      = getListaCoordinadores();
$campaigns          = getListaCampaigns();
$cantReparaciones   = getCantidadReparaciones();
$archivo            = "reporte_diademas_".date('m_d_Y').".xls";
$exportable         = array();
$flag = 1;

//header('Content-Type: text/html; charset=ISO-8859-1');
header("Content-Type: application/vnd.ms-excel");
header("Content-Type: text/plain");
header('Content-Type: text/html; charset=UTF-8');
header("Content-Disposition: attachment; filename=\"$archivo\"");

foreach($cursor as $document){
    $camp = iconv('UTF-8', 'ISO-8859-1', $campaigns[end($document['resumen'])['campaign']]['nombre']);
    $coor = iconv('UTF-8', 'ISO-8859-1', $coordinadores[end($document['resumen'])['coordinador_id']]['nombre']);
    $txtc = iconv('UTF-8', 'ISO-8859-1', "Campaña");
    if($camp == "") $camp = iconv('UTF-8', 'ISO-8859-1', "En reparación");
    if($coor == "") $coor = "Mesa Mantenimiento";

    $temp = array(
        "ID"                            => "".$document['_id'],
        "Marca"                         => "".$document['Marca'],
        "Serial"                        => "".$document['serial']."",
        $txtc                           => "".$camp,
        "Coordinador"                   => "".mb_convert_case($coor, MB_CASE_TITLE, "ISO-8859-1"),
        "# de reparaciones"             => "".getCantidadReparaciones()[$document['_id']],
        "Valor"                         => '=SI(B'.($flag + 1).'=$N$1,$N$2,SI(B'.($flag + 1).'=$O$1,$O$2, SI(B'.($flag + 1).'=$P$1,$P$2)))',
        "Costo total de reparaciones "  => "" ."=F".($flag + 1)."*15000"
    );
    
    if($flag == 1){
        $temp = array_merge($temp, array(" "=>" ",));
        $temp = array_merge($temp, array("  "=>" ",));
        $temp = array_merge($temp, array("   "=>" ",));
        $temp = array_merge($temp, array("    "=>" ",));
        $temp = array_merge($temp, array("     "=>" ",));
        $temp = array_merge($temp, array("Jabra"=>"65", "Plantronics"=>"50", "China"=>"45"));
    }
    array_push($exportable, $temp);
    $temp = array();
    $flag++;
}
array_push($exportable, $formulaSuma);
$bandera = false;
foreach($exportable as $row){
    if(!$bandera){
        echo implode("\t", array_keys($row)) . "\r\n";
        $bandera = true;
    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    echo implode("\t", array_values($row)) . "\n";
}
exit;
?>