<?php

require 'php/php_func.php';
session_start();

echo time() - $_SESSION['horaAcceso'];

?>