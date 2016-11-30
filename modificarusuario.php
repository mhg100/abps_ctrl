<?php
include 'php/php_func.php';
session_start();
validaEstadoLogin();

$conn = fSesion();

if($_SESSION['rol'] == 0){
    //modificar datos personales
    $sql = "update from admins set nombres_admin = $_POST[nombres] and apellidos_admin = $_POST[apellidos] and pass_admin = $_POST[clave] ";
}

?>