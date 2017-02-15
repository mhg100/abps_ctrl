<?php

require 'php/php_func.php';
session_start();

$abc = range("A", "Z");
$collection = fMongoDB();
$query      = $collection->find();
$diademas = array();

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
    for($j = 0; $j < count($diademas2['resumen']); $j++){
        
    }
}

?>