<?php
    include 'php/php_func.php';
    session_start();
    fTimeStamp();
    echo initHTML($_SESSION['rol']);
?>
<style>
    .txtPassword{
        -webkit-text-security:disc;
    }
</style>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                if($_SESSION['id'][0] == "9")     navbar();
                elseif($_SESSION['id'][0] == "8") navbarTecnico();
                else                              navbarCoordinadores();
                if($_GET["ic"] == 1){
                    $disabled = "";
                }else{
                    $disabled = "disabled";
                }
                ?>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Información personal</h2>
                    <div class="row">
                        <form class="form-horizontal" role="form" id="datospersonales" name="datospersonales" method="post" <?php
                            if($disabled == ""){
                                echo 'action="clave.php" ';
                            }
                            else{
                                echo 'action="#" ';
                            }
                        ?> autocomplete="off">
                            <div class="col-md-10 text-left col-md-offset-2">
                                <div class="form-group">
                                    <label for="nombres" class="col-sm-2 control-label">Nombre(s):</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required autocomplete="off" value="<?php echo $_SESSION['nombres']; ?>" disabled >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="apellidos" class="col-sm-2 control-label">Apellidos: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control has-error" id="apellidos" name="apellidos" placeholder="Apellidos" required autocomplete="off" value="<?php echo $_SESSION['apellidos']; ?>" disabled >
                                    </div>
                                </div>
                                <?php
                                
                                if($disabled != "disabled"){
                                ?>
                                <div class="form-group" id="pass1">
                                    <label for="clave" class="col-sm-2 control-label">Clave: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control txtPassword" id="clave" name="clave" placeholder="Nueva clave" required autocomplete="off" value="" <?php echo $disabled?>>
                                    </div>
                                </div>
                                <div class="form-group" id="pass2">
                                    <label for="clave2" class="col-sm-2 control-label">Repita la nueva clave: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control txtPassword" id="clave2" name="clave2" placeholder="Repita la nueva clave" required autocomplete="off" value="" <?php echo $disabled?>>
                                    </div>
                                </div><?php } ?><div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <?php
                                        if($disabled == ""){
                                            echo indent(40).'<button type="button" id="enviar" name="enviar" onclick="alerta()" class="btn btn-default" id="modificar" name="modificar">Modificar clave</button>'."\xA";
                                        }
                                        elseif($disabled == "disabled"){
                                            echo indent(40).'<button type="button" id="enviar" name="enviar" onclick="location.href='."'infopersonal.php?ic=1'".'" class="btn btn-default">Modificar clave</button>'."\xA";
                                        }
                                        ?></div>
                                </div>
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
    <script>
        function alerta(){
            var a = document.getElementById('clave').value;
            var b = document.getElementById('clave2').value;
            var c = document.getElementById('pass1');
            var d = document.getElementById('pass2');

            if(a.length >= 8){
                if(a === b){
                    c.className = "form-group has-success"; 
                    d.className = "form-group has-success";
                    document.forms['datospersonales'].submit();
                }else{
                    c.className = "form-group has-error";
                    d.className = "form-group has-error";
                    returnToPreviousPage();
                    /*document.getElementById("datospersonales").addEventListener("click", function(event){
                        event.preventDefault()
                    });*/
                }
            }else{
                c.className = "form-group has-error";
                d.className = "form-group has-error";
                returnToPreviousPage();
            }
        }
    </script>
</body>
</html>
