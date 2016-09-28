<?php
    include 'php/php_func.php';
    session_start();
    $_SESSION['rol'] = '1';
    fTimeStamp($_SESSION['rol']);
    echo initHTML($_SESSION['rol']);
?>
<body>
    <div class="container">
        <form class="form-horizontal" role="form" action="login.php" method="post">
            <fieldset class="">
                <legend>Control diademas - inicio de sesión</legend>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="id" name="id"></label>
                    <div class="col-md-4 input-group">
                        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user"></span></span>
                        <input id="id" name="id" type="text" placeholder="ID de campaña" class="form-control input-md" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="password"></label>
                    <div class="col-md-4 input-group">
                        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-lock"></span></span>
                        <input id="password" name="password" type="password" placeholder="Clave" class="form-control input-md" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for=""></label>
                    <div class="col-md-4 input-group">
                        <div class="col-md-4">
                            <button id="" name="" class="btn btn-primary">Ingresar</button>
                        </div>
                        
                        <?php
                        
                            if(isset($_SESSION['ns']))
                            {
                                if($_SESSION['ns'] == 1)
                                {
                                    echo '
                        <label class="alert alert-danger col-md-8">
                            <strong>Usuario o clave incorrectos</strong>
                        </label>
                                    ';
                                }
                                else if($_SESSION['ns'] == 0)
                                {
                                    echo '
                        <label class="alert alert-success col-md-8">
                            <strong>logueado</strong>
                        </label>
                            ';
                                }
                                else if($_SESSION['ns'] == 2)
                                {
                                    echo '
                        <label class="alert alert-warning col-md-8">
                            <strong>Sesión cerrada por inactividad</strong>
                        </label>
                                    ';
                                }
                                else if($_SESSION['ns'] == 3)
                                {
                                    echo '
                        <label class="alert alert-danger col-md-8">
                            <strong>Error al iniciar sesion (codigo 0x8160)</strong>
                        </label>
                                    ';
                                }
                                else echo '';
                            }
                        
                        ?>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</body>

</html>