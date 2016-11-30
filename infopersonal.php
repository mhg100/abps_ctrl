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
                <?php navbar(); ?>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Información personal</h2>
                    <div class="row">
                        <form class="form-horizontal" role="form" action="crearcoordinador.php" method="post">';
                            <div class="col-md-10 text-left col-md-offset-2">
                                <div class="form-group">
                                    <label for="nombres" class="col-sm-2 control-label">Nombre(s):</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required autocomplete="off" value="<?php echo $_SESSION['nombres']; ?>" <?php if(!isset($_GET["ic"]) || $_GET["ic"] != "1") echo "disabled"; ?> >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="apellidos" class="col-sm-2 control-label">Apellidos: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos" required autocomplete="off" value="<?php echo $_SESSION['apellidos']; ?>" <?php if(!isset($_GET["ic"]) || $_GET["ic"] != "1") echo "disabled"; ?> >
                                    </div>
                                </div>
                                <?php
                                if(isset($_GET["ic"]) && $_GET["ic"] == 1){
                                    echo '                                <div class="form-group">';
                                    echo '                                    <div class="col-sm-offset-2 col-sm-10">';
                                    echo '                                        <button type="submit" class="btn btn-default" id="cant_agentes" name="cant_agentes">';
                                    echo '                                            Modificar';
                                    echo '                                        </button>';
                                    echo '                                    </div>';
                                    echo '                                </div>';
                                }
                                ?>
                            </div>
                        </form>
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
            </div>
            <div class="col-md-4">
                <h2>Operación de coordinadores</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <a class="btn" href="#">View details »</a>
            </div>
            <div class="col-md-4">
                <a class="btn" href="#">View details »</a>
            </div>
            <div class="col-md-4">
                <a class="btn" href="#">View details »</a>
            </div>
        </div>
    </div>
</body>
</html>
