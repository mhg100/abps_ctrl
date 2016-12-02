<?php

require 'php/php_func.php';

$camps      = getCantidadDiademasPorCampaign();
$campids    = array_keys($camps);

for($i = 0; $i<count($campids); $i++){
    $cant = count($camps[$campids[$i]]);
    $camps= getListaCampaigns()[$campids[$i]]['nombre'];
    echo "            ['".$cant."', " .$camps."],\xA";
}

?>