<?php
    session_start();
    $_SESSION = array();
    
    unset($_SESSION);
    header('Location: index.php?ns=1');
    session_destroy();
    sqlsrv_close($conn);
?>