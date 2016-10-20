<?php

    session_start();
    echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';
    echo $_SESSION['horaAcceso'] - time();

?>