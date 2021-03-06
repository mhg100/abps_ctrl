<?php
    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    comprobarAdmin();
    echo initHTML($_SESSION['rol']);
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                    navbar();
                ?>
                <div class="jumbotron">
                    <h2 align="center">
                        <?php
                            if($_GET['ic']==0)
                            {
                                echo "Lista de campañas<br><br>";
                            }
                            else if($_GET['ic']==1)
                            {
                                echo "Creación de campañas<br><br>";
                            }
                        ?>
                    </h2>
                    <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <div class="row text-center">
                        <div class="col-md-8 col-lg-offset-2" style="outline: 1px solid #E7E7E7;">
                            <div class="row">
                                <?php
                                    if($_GET['ic'] == 1)        crearCamp();
                                    else if($_GET['ic'] == 0)   verCamp();
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h2>
                    Crear dispositivo
                </h2>
                <p>
                    Utilice esta opción para ingresar los dispositivos de los agentes.
                </p>
            </div>
            <div class="col-md-4">
                <h2>
                    Solicitar cambio
                </h2>
                <p>
                    Después de crear el caso en <a href="http://172.27.30.103/USDKRC1/" target="_blank">Aranda Service Desk®</a>, utilice esta opción para registrar la solicitud de cambio. El técnico verificará que el serial de la diadema que entregue sea el mismo que está grabado en el dispositivo.
                </p>
            </div>
            <div class="col-md-4">
                <h2>
                    Ver dispositivos
                </h2>
                <p>
                    En esta sección encontrará un resumen de las diademas de los agentes.
                </p>
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
    </div>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip({
                animation: true,
                animated: 'fade',
                placement: 'top',
                html: true,
                delay: {show: 1000, hide: 100}
            });
            $('[data-toggle="tooltip"]').tooltip().off("focusin focusout");
        });
    </script>
</body>
</html>
