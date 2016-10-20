<?php
    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    $_SESSION['rol'] = '0';
    echo initHTML($_SESSION['rol']);
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                    navbar();
                ?>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Gestor de coordinadores - Crear</h2>
                    <p>&nbsp;</p>
                    <div class="row text-center">
                        <div class="col-md-8 col-lg-offset-2" style="outline: 1px solid #E7E7E7;">
                            <div class="row">
                                    <?php
                                        if($_GET['ic'] == 0)
                                        {
                                            //ver coordinadores
                                            echo verCoordinadores('0');
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
