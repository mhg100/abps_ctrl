<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require 'php/php_func.php';
session_start();

$bajas = getDiademasEnBaja();
for($i = 0; $i < count($bajas); $i++){
    $id    = $bajas[$i]['_id'];
    $fecha = end($bajas[$i]['resumen'])['fechaMov'];
    $tecid = end($bajas[$i]['resumen'])['tecnico_id'];
    $tecn  = getTecnicos()[$tecid]['nombre'];
}

?>