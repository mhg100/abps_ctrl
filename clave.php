<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
include "php/php_func.php";
session_start();
fTimeStamp();

$comilladoble = '"';
$prohibidos = array("'", $comilladoble, ";", "=");
$conn    = fSesion();
$id      = $_SESSION['id'];
$newpass = $_POST['clave'];
$newpass = str_replace($prohibidos, "-", $newpass);
$newpass2= $newpass;
$flag    = true;

for($i = 0; $i < strlen($newpass); $i++){
    for($j = 0; $j < count($prohibidos); $j++){
        if($newpass[$i] == $prohibidos[$j]){
            $flag = false;
        }else{
            $newpass2[$i] = $newpass[$i];
        }
    }
}
$identificador = $id[0];

if($identificador == 9)     $sql = "update admins set pass_admin = '$newpass' where id_admin = '$id'";
elseif($identificador == 8) $sql = "update tecnicos set pass_tecnico = '$newpass' where id_tecnico = '$id'";
else                        $sql = "update coordinadores set pass_coordinador = '$newpass' where id_coordinador = '$id'";


$query = sqlsrv_query($conn, $sql);

if(!$query){
    if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
        }
    }
}else{
    header("Location: infopersonal.php?ic=2");
}

?>