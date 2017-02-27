<?php

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

require 'php/php_func.php';
session_start();
$collection = fMongoDB();
$cursor     = $collection->find();
$camps = array_keys(getCantidadDiademasPorCampaign());

foreach($cursor as $diadema){
    //pprint($diadema['_id']);
    if(end($diadema["resumen"])['campaign'] != '6118'){
        //pprint(end($diadema["resumen"]));
    }
}

usort($camps, function($a1, $a2) {
    $d1 = $a1;
    $d2 = $a2;
    return $d2 - $d1;
});

pprint(array_values($camps));

?>