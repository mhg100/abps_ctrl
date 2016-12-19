<?php

include 'php/php_func.php';

$collection = fMongoDB();
//489000
//2211

$recoger = $_POST['diademas'];
pprint($recoger);

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