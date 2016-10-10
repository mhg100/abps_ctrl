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
                <nav class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                        </button><a class="navbar-brand" href="#">ADMin</a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="#">Diademas</a>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Coordinadores<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Ver coordinadores</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Crear</a></li>
                                    <li><a href="#">Modificar</a></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-default">
                                Buscar coordinador
                            </button>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <span class="caret"></span>
                                    <span class="glyphicon glyphicon-user"></span>
                                    <?php
                                        echo $_SESSION['nombres'].' '.$_SESSION['apellidos'];
                                    ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Ver información personal</a></li>
                                    <li class="divider"></li>
                                    <li><a href="logout.php?rol=0"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesión</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div></div>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Información general</h2>
                    <br /><br />
                    <!--<p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>-->
                    <div class="row">
                        <div class="col-md-4" style="outline: 1px solid #E7E7E7;">
                            <h4 class="text-center">Total diademas</h4>
                            <div id="tortaOperaciones"></div>
                        </div>
                        <div class="col-md-4" style="outline: 1px solid #E7E7E7;">
                            <h4 class="text-center">Movimientos de los tres últimos meses</h4>
                            <div id="movimientos"></div>
                        </div>
                        <div class="col-md-4" style="outline: 1px solid #E7E7E7; height:289px">
                            <h4 class="text-center">Últimos 10 movimientos</h4>
                            <div>
                                <ol type="1">
                                    <li>ABPS1010 - Entregada en DPS</li>
                                    <li>ABPS2025 - Cambio por ABPS0321 en Cafam</li>
                                    <li>ABPS1032 - Entregada en DPS</li>
                                    <li>ABPS1010 - Entregada en UARIV</li>
                                    <li>ABPS2021 - Cambio por ABPS1019 en Colsanitas</li>
                                    <li>ABPS2041 - Cambio por ABPS0485 en MFA</li>
                                    <li>Mov 7</li>
                                    <li>Mov 8</li>
                                    <li>Mov 9</li>
                                    <li>Mov 10</li>
                                </ol>
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
