<?php
include 'php/php_func.php';
/*
{
estados:
0: en inventario
1: en campaña
2: en mantenimiento
3: baja (entra una con el mismo consecutivo)

_id:	consecutivo de la diadema
Marca:	Plantronics, Jabra, Chinas
Serial:	Solo para las Jabra.
resumen: [
	 {
		estado:           uno de los cuatro estados
		nombresAg:        nombre del agente que la tiene (ip en caso de ser fija)
		apellidosAg:      apellidos del agente ("FIJA" en caso de ser fija)
		coordinador_id:   id del coordinador
		fechaMov:         fecha del movimiento de la diadema (registro, recepcion)
	 },
	 {
		estado:		uno de los cuatro estados
		tecnicoID:	id del tecnico que realiza el movimiento
		coordinador_id:	id del coordinador que entrega la diadema
		fechaMov.	fecha de la entrega de la diadema)
		idDiademaNueva:	consecutivo de la diadema que se entrega
	 },
	 {...},
	 {...},
	 {...}
]
}
{
_id: 'ABPS1001',
Marca: 'Jabra',
serial: '2393-829-109',
resumen: [
	 {
		_id: '1',
		estado: '0',
		fechaMov: '25-10-2016 11:33'
	 }
]
}
*/
if(!isset($_GET['ic'])){
    $id             = strtoupper(str_replace(array(".", ",", " ", "-"), "", $_POST['serial']));
    $serial         = strtoupper(str_replace(array(".", ",", " ", "-"), "", $_POST['serialnumber']));
    $marca          = strtoupper($_POST['marca']);
    $collection     = fMongoDB();
    session_start();
    $infoDiadema = array(
        "_id"       => $id,
        "Marca"     => $marca,
        "serial"    => $serial
    );
    $resumen = array(
        "_id"       => "1",   
        "estado"    => "0",
        "campaign"  => 6118,
        "tecnico_id"=> $_SESSION['id'],
        "fechaMov"  => date("Y-m-d H:i"),
    );
    $resumenDiadema = array("resumen" => [$resumen]);
    $diadema        = array_merge($infoDiadema, $resumenDiadema);

    //echo '<pre>';
    //var_dump($diadema);
    //echo '</pre>';
    //echo '<pre>id coordinador: '.$_SESSION['id'].'</pre>';
    $redir = "";
    
}

try {
    $collection->insert($diadema);
    if($_SESSION['id'][0] == "8"){
        $redir = "refresh:0; tecdev.php?ic=1&ag=1&sd=".$id;
    }
    elseif($_SESSION['id'][0] == "9"){
        $redir = "refresh:0; device.php?ic=1&ag=1&sd=".$id;
    }
    header($redir);
}
catch (MongoCursorException $e) {
    //echo "error message: ".$e->getMessage()."\n";
    //echo "error code: ".$e->getCode()."\n";
    if($_SESSION['id'][0] == "8"){
        $redir = "refresh:0; tecdev.php?ic=1&ag=0&sd=".$id;
    }
    elseif($_SESSION['id'][0] == "9"){
        $redir = "refresh:0; device.php?ic=1&ag=0&sd=".$id;
    }
    header($redir);
}
?>