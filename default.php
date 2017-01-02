<?php
include 'php/php_func.php';
header('Content-Type: text/html; charset=UTF-8');
session_start();
fTimeStamp();
echo initHTML($_SESSION['rol']);
llamarPieChart(400, 250);
llamarAreaChart(400, 250);
if($_SESSION['rol'] != 0)
comprobarAdmin();
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                    navbar();
                ?>
                <div></div>
                <div class="jumbotron" style="background-color: #F8F8F8; outline: 1px solid #E7E7E7;">
                    <h2 class="text-center">Información general</h2>
                    <br /><br />
                    <div class="row">
                        <div class="col-md-4" style="outline: 1px solid #E7E7E7;">
                            <h4 class="text-center">Total de diademas registradas: (<?php echo getTotalDiademas()+count(getDiademasEnStock())?>)</h4>
                            <div id="tortaoperaciones"></div>
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
            <div class="col-md-3">
                <h2>Diademas</h2>
                <p>En esta sección se pueden realizar las consultas relacionadas con las diademas. Se pueden crear y ver por campañas, consular el stock (disponible para cambios o entregas), ver cuáles están en reparación o entregar las que necesitan servicio</p>
                <p>
                    <a class="btn" href="#">View details »</a>
                </p>
            </div>
            <div class="col-md-3">
                <h2>Coordinadores</h2>
                <p>Sección el a que se pueden realizar las operaciones con los coordinadores de las campañas. Se puede verificar información, modificar datos, restablecer clave de acceso y listar por campaña.</p>
                <p>
                    <a class="btn" href="#">View details »</a>
                </p>
            </div>
            <div class="col-md-3">
                <h2>Campañas</h2>
                <p>Se pueden ver, crear o modificar las campañas.</p>
                <p>
                    <a class="btn" href="#">View details »</a>
                </p>
            </div>
            <div class="col-md-3">
                <h2>Técnicos</h2>
                <p>Aquí se pueden ver, editar o crear los perfiles para los técnicos, quiene acceden con las credenciales que se generen.</p>
                <p>
                    <a class="btn" href="#">View details »</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
