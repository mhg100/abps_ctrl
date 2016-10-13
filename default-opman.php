<?php
    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    $_SESSION['rol'] = '0';
    echo initHTML($_SESSION['rol']);
    llamarPieChart(' ', 400, 250);
    llamarAreaChart('', 400, 250);
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
                                <div class="col-md-10 text-left col-md-offset-2">
                                    <p>&nbsp;</p>
                                                <!--Formulario-->
                                    <form class="form-horizontal" role="form">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Nombre(s):</label>
                                            <div class="col-md-8">
                                                <input type="email" class="form-control" id="inputEmail3" placeholder="nombres" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPassword3" class="col-sm-2 control-label">Apellidos: </label>
                                            <div class="col-md-8">
                                                <input type="password" class="form-control" id="inputPassword3" placeholder="Apellidos" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Campaña:</label>
                                            <div class="col-md-8">
                                                <select class="form-control" data-live-search="true">
                                                  <option>--Seleccione--</option>
                                                  <option>Acueducto</option>
                                                  <option>Alcaldía</option>
                                                  <option>Bancoldex</option>
                                                  <option>Cafam</option>
                                                  <option>Colsanitas</option>
                                                  <option>DPS</option>
                                                  <option>ETB</option>
                                                  <option>Familias en Acción</option>
                                                  <option>Sodimac</option>
                                                  <option>UARIV</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--<div class="form-group">
                                            <label for="inputPassword3" class="col-sm-2 control-label">Apellidos: </label>
                                            <div class="col-md-8">
                                                <input type="password" class="form-control" id="inputPassword3" placeholder="Apellidos" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" /> Remember me
                                                    </label>
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">

                                                <button type="submit" class="btn btn-default">
                                                    Agregar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
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
