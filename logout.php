<?php
    session_start();
    $rol        = $_SESSION['rol'];
    $_SESSION   = array();

    /*if($_GET['mtv'] == 0)      header('Location: index.php?ns=4');
    else if($_GET['mtv'] == 1) header('Location: index.php?ns=2');
    else if($_GET['mtv'] == 2) header('Location: index.php?ns=5');*/

/////////////-----------nuevo-------------//////////////

    if(isset($_GET['mtv'])){
        switch($_GET['mtv']){
            case 1:
                $_SESSION['ns'] = 2;
                break;
            case 2:
                $_SESSION['ns'] = 5;
                break;
        }
    }
    else  $_SESSION['ns'] = 4;
    header('Location: index.php');
?>
