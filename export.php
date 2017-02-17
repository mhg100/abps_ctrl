<?php
/*error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);*/
require_once 'php/PHPExcel.php';
include      'php/php_func.php';

header ('Pragma: public');
header ('Content-Disposition: attachment;filename="reporte_diademas_'.date('m_d_Y').'.xlsx"');
header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header ('Cache-Control: cache, must-revalidate');
header ('Cache-Control: max-age=0');
header ('Cache-Control: max-age=1');

$objPHPExcel      = new PHPExcel();
$collection       = fMongoDB();
$cursor           = $collection->find();
$coordinadores    = getListaCoordinadores();
$campaigns        = getListaCampaigns();
$cantReparaciones = getCantidadReparaciones();
$archivo          = "reporte_diademas_".date('m_d_Y').".xls";
$campaigns        = array_values($campaigns);
$flag             = 2;

$alfabeto   = range("A", "Z");
$diademas   = array();
$collection = fMongoDB();
$query      = $collection->find();
$campname   = getListaCampaigns();

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
    $coor = $coordinadores[end($document['resumen'])['coordinador_id']]['nombre'];
    $camp = $coordinadores[end($document['resumen'])['coordinador_id']]['nombrecamp'];
    $esta = end($document['resumen'])['estado'];

    if($camp == ""){
        $camp = iconv('UTF-8', 'ISO-8859-1', "En reparación");
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
                ->setCellValue('E'.$flag, mb_convert_case($coor, MB_CASE_TITLE, "ISO-8859-1"))
                ->setCellValue('F'.$flag, getCantidadReparaciones()[$document['_id']])
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

$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(19);

 //////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

$movimientos = array("0" => "Entra a stock",
                     "1" => "Entregada a",
                     "2" => "Sale a reparación",
                     "3" => "Dada de baja");

foreach($query as $diadema){
    array_push($diademas, $diadema);
}

$diademas2 = $diademas;

usort($diademas2, function($a1, $a2) {
    $d1 = count($a1['resumen']);
    $d2 = count($a2['resumen']);
    return $d2 - $d1;
});

for($i = 0; $i < count($diademas2); $i++){
    $diadematemp = $diademas2[$i];
    $id = $diadematemp['_id'];
    $fila = $i+2;
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $id);
    
    for($j = 0; $j < count($diadematemp['resumen']); $j++){
        $resumentemp = $diadematemp['resumen'][$j];
        
        $diademaAnterior = $resumentemp['idDiademaAnterior'];
        $tecnico = $resumentemp['tecnico_id'];
        $fecha = $resumentemp['fechaMov'];
        $estado = $resumentemp['estado'];
        $camp = $resumentemp['campaign'];
        
        $fecha = date("d/m/Y", strtotime($fecha));
        
        switch($estado){
            case 0:
                $motivo = "$movimientos[0] ";
                break;
            case 1:
                $motivo = $movimientos[1]." ".$campname[$camp]['nombre'];
                break;
            case 2:
                $motivo = $movimientos[2];
                break;
            case 3:
                $motivo = $movimientos[3];
                break;
        }
        $s1 = "";
        $s2 = "";
        $s3 = "";
        
        for($k = -1; $k < 8; $k++){
            if($k > -1){
                $s1 = $alfabeto[$k];
            }
            for($l = 0; $l < count($alfabeto); $l++){
                $s2 = $alfabeto[$l+1];
                $str = "$s2";
                //pprint($str);
                //$objPHPExcel->getActiveSheet()->setCellValue("$str".$fila, '$Motivo');
            }
        }
        
        //echo "$motivo el día $fecha </br></br>";
    }
}

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

//$objPHPExcel->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

?>















