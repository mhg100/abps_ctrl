<?php

include 'php/php_func.php';
session_start();
fTimeStamp();
echo initHTML($_SESSION['rol']);
comprobarAdmin();
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                navbar();
                ?>
                <div class="jumbotron" style="position: relative;">
                    <h2 align="center"><?php
                        if($_GET['ic'] == "0" || !isset($_GET['ic']))   echo "Lista de dispositivos";  
                        else if($_GET['ic'] == "1")                     echo "Creación de dispositivo";
                        else if($_GET['ic'] == "2")                     echo "Cambios";
                        else if($_GET['ic'] == "3")                     echo "Recoger diademas";
                        else if($_GET['ic'] == "4")                     echo "Diademas en stock";
                        else if($_GET['ic'] == "5")                     echo "Reparar diademas";
                        else if($_GET['ic'] == "6")                     echo "Diademas en reparación";
                        else if($_GET['ic'] == "7")                     echo "Recibir diademas de reparación";
                    ?></h2>
                        <div class="clearfix" id="exportar">&nbsp;</div>
                            <div class="jumbotron" style="background-color: #F8F8F8; outline: 0px solid #E7E7E7;">
                                <div class="row text-center" style="background-color: #F8F8F8;">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="row">
                                        <?php
                                        if(!isset($_GET['ic']) || $_GET['ic'] == 0) verDiadema("0");
                                        else if($_GET['ic'] == 1)                   adminCrearDiadema();
                                        else if($_GET['ic'] == 2)                   cambioDiadema();
                                        else if($_GET['ic'] == 3)                   recogerDiademas();
                                        else if($_GET['ic'] == 4)                   verDiadema("1");
                                        else if($_GET['ic'] == 5)                   repararDiademas();
                                        else if($_GET['ic'] == 6)                   verDiademasEnReparacion();
                                        else if($_GET['ic'] == 7)                   recibirDiademasDeReparacion();
                                        else if($_GET['ic'] == 8)                   entregarDiademas();
                                        ?>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h2>Crear dispositivo</h2>
                    <p>Utilice esta opción para ingresar los dispositivos de los agentes.</p>
                </div>
                <div class="col-md-4">
                    <h2>Solicitar cambio</h2>
                    <p>
                        Después de crear el caso en <a href="http://172.27.30.103/USDKRC1/" target="_blank">Aranda Service Desk®</a>, utilice esta opción para registrar la solicitud de cambio.
                        El técnico verificará que el serial de la diadema que entregue sea el mismo que está grabado en el dispositivo.
                    </p>
                </div>
                <div class="col-md-4">
                    <h2>Ver dispositivos</h2>
                    <p>En esta sección encontrará un resumen de las diademas de los agentes.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <a class="btn" href="defaultdevice.php?ic=1">Ir a la opción »</a>
                </div>
                <div class="col-md-4">
                    <a class="btn" href="defaultdevice.php?ic=0">Ir a la opción »</a>
                </div>
                <div class="col-md-4">
                    <a class="btn" href="cambios.php">Ir a la opción »</a>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $('[data-toggle="tooltip"]').tooltip({
                        animation: true,
                        animated: 'fade',
                        placement: 'top',
                        html: true,
                        delay: {
                            show: 1000,
                            hide: 100
                        }
                    });
                    $('[data-toggle="tooltip"]').tooltip().off("focusin focusout");
                });

            </script>
        </div>
    </div>
</body>
</html>
