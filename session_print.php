<?php
    require 'php/php_func.php';
    $conn = fSesion();
    $sql_ObtenerNombres  = "select nombre_campaign as nombre from campaigns";
    $stmt_ObtenerNombres = sqlsrv_query($conn, $sql_ObtenerNombres);
    session_start();
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';
    echo '<pre>'.time()-$_SESSION['horaAcceso'].'</pre>';
    
    $string = "tengo 'estos valores' y 'estos otros' ";
    echo "<pre>".$string."</pre>";
    $string = str_replace("'", "-", $string); // Replaces all spaces with hyphens.
    echo "<pre>".$string."</pre>";
    
    echo $_POST['agregar'];

    /*while($campaigns = sqlsrv_fetch_array($stmt_ObtenerNombres, SQLSRV_FETCH_ASSOC))
    {
        $stmt_ObtenerCantCampaign = sqlsrv_query($conn, fetchCantCampaign($campaigns["nombre"]));
        while($cant = sqlsrv_fetch_array($stmt_ObtenerCantCampaign, SQLSRV_FETCH_ASSOC))
        {
            if($cant['total'] == NULL){
                $cant['total'] = 0;
            }
            //echo "['".$campaigns["nombre"]."', " .$cant['total']."],";
            echo '<pre>'.$campaigns["nombre"].' '.$cant['total'].'</pre>';
        }
    }

    $campaign = '6244';
    $sql2 = "select nombre_campaign as nc from campaigns where id_campaign = '6244'";
    $stmt2 = sqlsrv_query($conn, $sql2);
 
    while($cmpgn = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC))
    {
        $nombreCampa = $cmpgn['nc'];
        echo $nombreCampa;
    }

    if(extension_loaded("mongodb")){
        echo "Cargada";
    }else
    {
        echo "No cargada";
    }
    //echo extension_loaded("mongodb") ? "loaded\n" : "not loaded\n";*/
?>