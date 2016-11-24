<?php
require 'php/php_func.php';
$listacoordinadores = getListaCoordinadores();
echo '<pre>';
    print_r($listacoordinadores);
echo '</pre>';
$listaids = array_values($listacoordinadores);
?>