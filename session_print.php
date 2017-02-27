<?php

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

require 'php/php_func.php';
session_start();
$collection = fMongoDB();
$cursor     = $collection->find();

foreach($cursor as $diadema){
    //pprint($diadema['_id']);
    if(end($diadema["resumen"])['campaign'] != '6118'){
        //pprint(end($diadema["resumen"]));
    }
}

pprint(getListaCoordinadores());

?>