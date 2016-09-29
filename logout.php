<?php
    session_start();
    $_SESSION = array();
    unset($_SESSION);
    session_destroy();

    if($_GET['rol'] == 1){
        header('Location: index.php?ns=4');
    }else if($_GET['rol'] == 0){
        header('Location: index2.php?ns=4');
    }
    
    sqlsrv_close($conn);
?>