<?php
include 'php/php_func.php';
session_start();

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
		_id: '001',
		estado: '0',
		fechaMov: '25-10-2016 11:33'
	 }
]
}

Agregar dato:

    la que entra a stock
	db.diademas.update(
        {_id: "349"},
        {$addToSet:
            {resumen:
                {
                    _id:'002',
                    estado: "0",
                    coordinador_id: "1025",
                    fechaMov: "25-10-2016 11:33",
                    tecnico_id: "12",
                    idDiademaNueva: ""
                }
            }
        }
    ):
    
    la que sale a campaña
    db.diademas.update(
        {_id: "349"},
        {$addToSet:
            {resumen:
                {
                    _id: "002",
                    estado: "1",
                    coordinador_id: "1025",
                    campaign: "1",
                    fechaMov: "25-10-2016 11:33",
                    tecnico_id: "12",
                    idDiademaAnterior: ""
                }
            }
        }
    ):

*/
if(!isset($_GET['ic'])){
    
    $collection      = fMongoDB();
    $diademasaliente = $_POST['diademas'];
    $campid          = $_POST['campid'];
    $coordid         = $_POST['coordid'];
    $tecnicoid       = $_SESSION['id'];
    
    pprint($diademasaliente);
    
    for($i = 0; $i < count($diademasaliente); $i++){
        $diademacamp = $collection->find(array("_id" => $diademasaliente[$i]));

        foreach($diademacamp as $d1){
            $diademacamp = $d1;
        }

        $id1 = end($diademacamp['resumen'])['_id'];
        $id1 ++;
        $resumen1 = array ("_id"            => $id1,
                           "estado"         => "1",
                           "coordinador_id" => $coordid,
                           "campaign"       => $campid,
                           "fechaMov"       => date ("Y-m-d H:i"),
                           "tecnico_id"     => $tecnicoid
        );
        array_push($diademacamp['resumen'], $resumen1);
        echo "diadema ".($bandera+1);
        pprint($diademacamp);
        $collection->update(array('_id'=>$diademasaliente[$i]), array('$addToSet'=>array("resumen"=>$resumen1)));
    }
    header('Location: device.php?ic=8&sd=1');
}
?>
