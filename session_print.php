<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require 'php/php_func.php';
session_start();

$abc = range("B", "Z");
$abc2= range("B", "Z");
$abc3= range("A", "Z");



pprint(getTecnicos()[8000]['nombre']);

?>