<?php
require 'php/php_func.php';
session_start();
header('Content-Type: text/html; charset=UTF-8');
$listacoordinadores = getListaCoordinadores();
echo '<pre>';
    print_r($_SESSION);
echo '</pre>';
$listaids = array_values($listacoordinadores);

$fecha = "2016-11-29 00:00:00";

echo $fecha;

?>