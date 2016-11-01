<?php
    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    echo initHTML($_SESSION['rol']);
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                    navbarCoordinadores();
                ?>
                <div class="jumbotron">
                    <h2 align="center">Coordinador</h2>
                    
                    <p>Aquí podrá encontrar toda la información acerca de los dispositivos creados. Además, desde aquí gestionará la entrega, en caso de que necesite una sustitución por mal funcionamiento.</p>
                    <p>
                        <!--<a class="btn btn-primary btn-large" href="defaultdevice.php?ic=1">Learn more</a>-->
                    </p>
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
</body>
</html>
