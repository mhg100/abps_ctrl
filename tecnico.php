<?php
include 'php/php_func.php';
header('Content-Type: text/html; charset=UTF-8');
session_start();
fTimeStamp();
echo initHTML($_SESSION['rol']);
if($_SESSION['rol'] == 1)
comprobarAdmin();
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <?php
                navbar();
            ?>
            <div></div>
                <div class="jumbotron">
                    <h2 class="text-center" id="tecnicoheader">Crear técnico</h2>
                    <br />
                    <div class="row" style="background-color: #F8F8F8;">
                    <?php
                        if($_GET['ic'] == "1")      crearTecnico();
                        else if($_GET['ic'] == "0") verTecnicos();
                        else if($_GET['ic'] == "2") editarTecnico();
                    ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <h2>Operación de coordinadores</h2>
                    <p></p>
                    <p>
                        <a class="btn" href="#">View details »</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <h2>Operación de coordinadores</h2>
                    <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
                    <p>
                        <a class="btn" href="#">View details »</a>
                    </p>
                </div>
                <div class="col-md-4">
                <h2>Operación de coordinadores</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
                <p>
                    <a class="btn" href="#">View details »</a>
                </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>