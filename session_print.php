<?php

require 'php/php_func.php';
session_start();

for ($i = 0; $i < 5; $i++){
    $temp = array(
        "ID"                => "ID".$document['_id'],
        "Marca"             => "Marca".$document['Marca'],
        "Serial"            => "Serial".$document['serial'],
        "Camp"              => "Camp".$camp,
        "Coordinador"       => "Coord".mb_convert_case($coor, MB_CASE_TITLE, "ISO-8859-1"),
        "# de reparaciones" => "Reparaciones".getCantidadReparaciones()[$document['_id']]
    );

    if($i < 3){
        $temp = array_merge($temp, array(" "=>" ",));
        $temp = array_merge($temp, array("  "=>" ",));
        $temp = array_merge($temp, array("   "=>" ",));
        $temp = array_merge($temp, array("    "=>" ",));
        $temp = array_merge($temp, array("     "=>" ",));
        $temp = array_merge($temp, array("      "=>" ",));
        $temp = array_merge($temp, array("       "=>" ",));
        $temp = array_merge($temp, array("Jabra"=>"65", "Plantronics"=>"50", "China"=>"45"));
    }
    pprint($temp);
    break;
}

?>