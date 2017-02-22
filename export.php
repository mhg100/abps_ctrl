<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once 'php/PHPExcel.php';
include 'php/php_func.php';

header ('Pragma: public');
header ('Content-Disposition: attachment;filename="reporte_diademas_"'.date('m_d_Y').'".xls"');
header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header ('Cache-Control: cache, must-revalidate');
header ('Cache-Control: max-age=0');
header ('Cache-Control: max-age=1');

$flag               = 2;
$objPHPExcel        = new PHPExcel();
$diademas           = array();
$diademas2          = array();
$collection         = fMongoDB();
$cursor             = $collection->find();
$coordinadores      = getListaCoordinadores();
$cantReparaciones   = getCantidadReparaciones();
$query              = $collection->find();
$campname           = getListaCampaigns();
$alfabeto1          = range("A", "Z");
$alfabeto2          = range("B", "Z");
$campaigns          = array_values(getListaCampaigns());
$archivo            = "reporte_diademas_".date('m_d_Y').".xls";

$objPHPExcel->getProperties()->setCreator("Americas BPS")
							 ->setLastModifiedBy("Americas  BPS")
							 ->setTitle("Reporte de diademas")
							 ->setSubject("")
							 ->setDescription("Reporte de diademas activas y HV general")
							 ->setKeywords("Diademas reporte hoja vida hv")
							 ->setCategory("Reporte");

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Marca')
            ->setCellValue('C1', 'Serial')
            ->setCellValue('D1', 'Campaña')
            ->setCellValue('E1', 'Coordinador')
            ->setCellValue('F1', '# de reparaciones')
            ->setCellValue('G1', 'Valor')
            ->setCellValue('H1', 'Costo total de reparaciones')
            ->setCellValue('N1', 'Jabra')
            ->setCellValue('O1', 'Plantronics')
            ->setCellValue('P1', 'China')
            ->setCellValue('N2', '65')
            ->setCellValue('O2', '50')
            ->setCellValue('P2', '45');

$objPHPExcel->getActiveSheet()->setTitle('Diademas activas');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(27);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(21);

foreach($cursor as $document){
    if(isset(end($document['resumen'])['coordinador_id'])){
        $coorid = end($document['resumen'])['coordinador_id'];
        $coor   = $coordinadores[$coorid]['nombre'];
        $camp   = $coordinadores[$coorid]['nombrecamp'];
    }
    $esta = end($document['resumen'])['estado'];
    if($camp == ""){
        $camp = "En reparación";
    }
    if($esta == "0"){
        $camp = "Tecnología";
        $coor = "Mesa Mantenimiento";
    }
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$flag, $document['_id'])
                ->setCellValue('B'.$flag, $document['Marca'])
                ->setCellValue('C'.$flag, $document['serial'])
                ->setCellValue('D'.$flag, $camp)
                ->setCellValue('E'.$flag, $coor)
                ->setCellValue('F'.$flag, $cantReparaciones[$document['_id']])
                ->setCellValue('G'.$flag, '=IF(B'.($flag).'=N1,N2,IF(B'.($flag).'=O1,O2, IF(B'.($flag).'=P1,P2)))')
                ->setCellValue('H'.$flag, "=F$flag*14000");
    $flag++;
}

 ///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1)
            ->setTitle('Hoja de vida de diademas')
            ->setCellValue('A1', 'ID de diadema')
            ->setCellValue('B1', 'Movimientos');

//$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(19);

 //////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

$movimientos = array("0" => "Entra a stock",
                     "1" => "Entregada a",
                     "2" => "Sale a reparación",
                     "3" => "Dada de baja");

foreach($query as $diadema){
    array_push($diademas, $diadema);
    array_push($diademas2,$diadema);
}

usort($diademas2, function($a1, $a2) {
    $d1 = count($a1['resumen']);
    $d2 = count($a2['resumen']);
    return $d2 - $d1;
});

for($i = 0; $i < count($diademas2); $i++){
    $diadematemp = $diademas2[$i];
    $id          = $diadematemp['_id'];
    $fila        = $i+2;
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $id);
    
    for($j = 0; $j < count($diadematemp['resumen']); $j++){
        $resumentemp = $diadematemp['resumen'][$j];        
        $fecha       = $resumentemp['fechaMov'];
        $estado      = $resumentemp['estado'];
        $fecha       = date("d/m/Y", strtotime($fecha));
        
        if(isset($resumentemp['idDiademaAnterior']))
            $diademaAnterior = $resumentemp['idDiademaAnterior'];
        else
            $diademaAnterior = "";
        if(isset($resumentemp['tecnico_id']))
            $tecnico         = $resumentemp['tecnico_id'];
        else
            $tecnico         = "8000";
        if(isset($resumentemp['campaign']))
            $camp            = $resumentemp['campaign'];
        elseif($estado == 2)
            $camp            = "Reparacion";

        switch($estado){
            case 0:  $motivo = "$movimientos[0] ";
            break; 
            case 1:  $motivo = $movimientos[1]." ".$campname[$camp]['nombre'];
            break; 
            case 2:  $motivo = $movimientos[2];
            break; 
            case 3:  $motivo = $movimientos[3];
            break;
        }

        $s1 = "";
        $s2 = "";

        for($k = -1; $k < 2; $k++){
            if($k > -1){
                $s1 = $alfabeto1[$k];
            }
            for($l = 0; $l < count($alfabeto2); $l++){
                $idresumen   = "";
                $resumentemp = $diadematemp['resumen'][$j];
                if($resumentemp["_id"] === $idresumen){
                    break;
                }else{
                    $idresumen = $resumentemp["_id"];
                }
                $s2 = $alfabeto2[$l];
                $str = "$s1$s2";
                $objPHPExcel->getActiveSheet()->setCellValue("$str$fila", "$motivo el día $fecha");
            }
        }
        
        $resumentemp = "";
        $fecha       = "";
        $estado      = "";
        $fecha       = "";
        $motivo      = "";
    }
}

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

$objPHPExcel->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

?>