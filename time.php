<?php
    include('php/php_func.php');
    session_start();
    fTimeStamp();
    echo $_SESSION['horaAcceso'];
?>