<?php

include 'php/php_func.php';
session_start();
$collection = fMongoDB();

$recoger    = $_POST['diademas'];
$cant       =  count($recoger);

if($cant > 0){
    for($i = 0; $i < $cant; $i++){
        $diademamant = $collection->find(array('_id'=>$recoger[$i]));

        foreach($diademamant as $document){
            $id = end($document['resumen'])['_id'];
            $id ++;
            $resumen = array("_id"          => $id,
                             "estado"       => "0",
                             "fechaMov"     => date('Y-m-d H:i'),
                             "tecnico_id"    => $_SESSION['id']
            );
            try{
                $collection->update(array('_id'=>$recoger[$i]), array('$addToSet'=>array("resumen"=>$resumen)));
                header('Location: device.php?ic=3');
            }catch(Exception $e) {
                echo "Exception: ", $e->getMessage(), "\n";
            }
        }
    }
}


/*for($i = 0; count($recoger); $i++){
    $diademamant = $collection->find(['_id'=>$$recoger[$i]]);
    
    foreach($diademamant as $document)  $diademamant = $document;
    $id = end($diademamant['resumen'])['_id'];
    $id ++;
    $resumen2 = array ("_id"                 => $id,
                       "estado"              => "0",
                       "fechaMov"            => date ("Y-m-d H:i"),
                       "tecnico_id"          => $tecnicoid
    );
    array_push($diademamant['resumen'], $resumen2);
    $collection->update(array('_id'=>$diademamant), array('$addToSet'=>array("resumen"=>$resumen2)));
}*/



?>