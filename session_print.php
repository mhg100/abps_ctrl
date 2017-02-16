<?php

/*error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);*/

require 'php/php_func.php';
session_start();

$abc = range("A", "Z");
$collection = fMongoDB();
$query      = $collection->find();
$campname   = getListaCampaigns();
$diademas = array();

$movimientos = array("0" => "Entra a stock",
                     "1" => "Entregada a",
                     "2" => "Sale a reparación",
                     "3" => "Dada de baja");

$num = (51 / 26);

//echo floor($num);

foreach($query as $diadema){
    array_push($diademas, $diadema);
}

$diademas2 = $diademas;

usort($diademas2, function($a1, $a2) {
    $d1 = count($a1['resumen']);
    $d2 = count($a2['resumen']);
    return $d2 - $d1;
});

for($i = 0; $i < count($diademas2); $i++){
    $diadematemp = $diademas2[$i];
    $id = $diadematemp['_id'];
    
    echo "<p>ID: $id - ";
    
    for($j = 0; $j < count($diadematemp['resumen']); $j++){
        $resumentemp = $diadematemp['resumen'][$j];
        
        $diademaAnterior = $resumentemp['idDiademaAnterior'];
        $tecnico = $resumentemp['tecnico_id'];
        $fecha = $resumentemp['fechaMov'];
        $estado = $resumentemp['estado'];
        $camp = $resumentemp['campaign'];
        
        $fecha = date("d/m/Y", strtotime($fecha));
        
        switch($estado){
            case 0:
                $motivo = "$movimientos[0] ";
                break;
            case 1:
                $motivo = $movimientos[1]." ".$campname[$camp]['nombre'];
                break;
            case 2:
                $motivo = $movimientos[2];
                break;
            case 3:
                $motivo = $movimientos[3];
                break;
        }
                                        
        echo "$motivo el día $fecha </br></br>";
    }
    echo "</p>";
}

?>