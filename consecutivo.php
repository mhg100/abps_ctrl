<?php
include 'php/php_func.php';
session_start();
echo initHTML(0);
fTimeStamp();
$infoUltConsec = getUltimoConsecutivo();
?>

<link href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<body>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php navbar(); ?>
        </div>
        </div>
    <div class="container">
        <form class="form-horizontal" role="form" action="grabarconsecutivo.php" method="post">
            <fieldset class="">
                <div class="clearfix">
                    &nbsp;
                </div>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Consecutivo de marcación</h2>
                    <p class="text-center">Consecutivo actual: <strong><?php echo strtoupper($infoUltConsec['numero_consecutivo']) ?></strong></p>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="consecutivonuevo"></label>
                    <div class="col-md-4 input-group">
                        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-pencil"></span></span>
                        <input id="consecutivonuevo" name="consecutivonuevo" type="consecutivonuevo" placeholder="Nuevo consecutivo" maxlength="8" class="form-control input-md" required autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-8 input-group" align="right">
                        <fieldset>
                            <button id="ingresar" name="ingresar" class="btn btn-success">Ingresar consecutivo</button>
                        </fieldset>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</body>
</html>