<?php
/*
{
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
		estado: '1',
		nombresAg: 'Gina',
		apellidosAg: 'Saenz',
		coordinador_id: '1025',
		fechaMov: '25-10-2016 11:33'
	 }
]
}
*/
    $conn = new MongoClient();
    $db = $conn->ctrltest;
    $collection = $db->diademas;
    session_start();

    /*$diadema = array(
        "_id"=>"avion",
        "letras"=>["primera" => "a",
                   "segunda" => "v",
                   "tercera" => "i",
                   "cuarta" => "o",
                   "quinta" => "n"]
    );*/
    $diadema = array(
        "_id"    => $_POST['serial'],
        "Marca"  => $_POST['marca'],
        "serial" => $_POST['serialnumber'],
        "resumen"=> [
            "estado" => "1",
            "nombresAg" => $_POST['nombres'],
            "coordinador_id" => $_SESSION['id'],
            "fechaMov" => date("d-m-Y H:i")
        ]
    );
    echo '<pre>';
    var_dump($diadema);
    echo '</pre>';
    echo '<pre>id coordinador: '.$_SESSION['id'].'</pre>';

    try {
        $collection->insert($diadema);
        header( 'Location: defaultdevice.php?ic=1');
    }
    catch (MongoCursorException $e) {
        echo "error message: ".$e->getMessage()."\n";
        echo "error code: ".$e->getCode()."\n";
        header( "refresh:5; defaultdevice.php?ic=1&err=1" );
    }




















?>