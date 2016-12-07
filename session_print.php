<?php

require 'php/php_func.php';
$conn = fSesion();
session_start();

$cantdiademascamp   = getCantidadDiademasPorCampaign();
$campcantdiademas   = array_keys($cantdiademascamp);
pprint($cantdiademascamp[$campcantdiademas[0]]);

?>