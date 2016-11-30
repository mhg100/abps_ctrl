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
                    if ($_GET['ic'] == 0)      $opcion = 'Ver';
                    else if ($_GET['ic'] == 1) $opcion = 'Crear';
                    else if ($_GET['ic'] == 2) $opcion = 'Modificar';
                ?>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Gestor de coordinadores - <?php echo $opcion; ?></h2>
                    <p>&nbsp;</p>
                    <div class="row text-center">
                        <div class="col-md-8 col-lg-offset-2" style="outline: 1px solid #E7E7E7;">
                            <div class="row">
                            <?php
                            if($_GET['ic'] == 0)
                            {
                                verCoordinadores('0', $_GET['camplist']);
                            }
                            else if($_GET['ic'] == 1)
                            {
                                //crear coordinadores
                                crearCoordinadores();
                            }
                            else if($_GET['ic'] == 2){
                                //modificar coordinadores
                                echo verCoordinadores('1');
                            }
                            ?>
                            <p>&nbsp;</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
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
</body>
</html>
