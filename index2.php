<?php
    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    $_SESSION['rol'] = '0';
    echo initHTML($_SESSION['rol']);
?>
<body>
    <div class="container">
        <form class="form-horizontal" role="form" action="login.php" method="post">
            <fieldset class="">
                <legend>Administrador de coordinadores - inicio de sesión</legend>
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
                            validaEstadoLogin();
                        ?>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</body>
</html>