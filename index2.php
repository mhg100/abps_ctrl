<?php
    include 'php/php_func.php';
    session_start();
    $_SESSION['rol'] = '0';
    echo initHTML($_SESSION['rol']);
?>
<body>
    <div class="container">
        <form class="form-horizontal" role="form" action="login.php" method="post">
            <fieldset class="">
                <legend>Administrador de coordinadores - inicio de sesión</legend>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="index.php" class="btn btn-success" role="button">Cambiar a coordinador</a>
                        <p></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="id" name="id"></label>
                    <div class="col-md-4 input-group">
                        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user"></span></span>
                        <input id="id" name="id" type="text" placeholder="ID de campaña" class="form-control input-md" autocomplete="off" required autofocus>
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
                    <div class="col-md-8 input-group" align="right">
                        <fieldset>
                            <button id="ingresar" name="ingresar" class="btn btn-primary" style="padding:15px">Ingresar</button>
                        <?php
                            validaEstadoLogin();
                        ?>
                        </fieldset>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</body>
</html>