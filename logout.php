<?php
    session_start();
    $rol = $_SESSION['rol'];
    $_SESSION = array();
    unset($_SESSION);
    session_destroy();

    if($rol == 1)
    {
        if($_GET['mtv'] == 0)      header('Location: index.php?ns=4');
        else if($_GET['mtv'] == 1) header('Location: index.php?ns=2');
        else if($_GET['mtv'] == 2) header('Location: index.php?ns=5');
    }
    else
    {
        if($_GET['mtv'] == 0)      header('Location: index2.php?ns=4');
        else if($_GET['mtv'] == 1) header('Location: index2.php?ns=2');
        else if($_GET['mtv'] == 2) header('Location: index2.php?ns=5');
    }

    sqlsrv_close($conn);

?>