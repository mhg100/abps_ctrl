<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
/** Include PHPExcel */
require_once 'php/PHPExcel.php';
include 'php/php_func.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

// Descripcion de columnas
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

$collection       = fMongoDB();
$cursor           = $collection->find();
$coordinadores    = getListaCoordinadores();
$campaigns        = getListaCampaigns();
$cantReparaciones = getCantidadReparaciones();
$archivo          = "reporte_diademas_".date('m_d_Y').".xls";
$exportable       = array();
$flag             = 2;
$campaigns        = array_values($campaigns);

foreach($cursor as $document){
    $coor = $coordinadores[end($document['resumen'])['coordinador_id']]['nombre'];
    $camp = $coordinadores[end($document['resumen'])['coordinador_id']]['nombrecamp'];

    if($camp == "") $camp = iconv('UTF-8', 'ISO-8859-1', "En reparación");
    if($coor == "") $coor = "Mesa Mantenimiento";

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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(27);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(21);

$objPHPExcel->getActiveSheet()->setTitle('Diademas activas');

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);

$fila   = 1;
$columna = 0;

$alfabeto = range("A", "Z");
foreach($cursor as $diadema){
    $id         = $diadema['_id'];
    $resumen    = $diadema['resumen'];
    $campaign   = "";
    $fecha      = "";
    $tipoMov    = "";

    $objPHPExcel->getActiveSheet()->setCellValue($alfabeto[$columna].''.($fila),   'Diadema');
    $objPHPExcel->getActiveSheet()->setCellValue($alfabeto[$columna].''.($fila+1), $id);
    $columna = $columna + 1;
    
    
    
    
    for($i = 0; $i < count($resumen); $i++){
        $fecha = $resumen[$i]['fechaMov'];
        if(isset($resumen[$i]['campaign'])){
            $campaign = $campaigns[$resumen[$i]['campaign']]['nombre'];
        }
        $tipoMov = $resumen[$i]['estado'];
        switch($tipoMov){
            case 0: $tipoMov = "Entra a stock";     break;
            case 1: $tipoMov = "Sale a campaña";    break;
            case 2: $tipoMov = "Sale a reparación"; break;
            case 3: $tipoMov = "Dada de baja";      break;
        }
        
        $objPHPExcel->getActiveSheet()
                    ->setCellValue($alfabeto[$columna]  .''.($fila),   'Tipo de movimiento')
                    ->setCellValue($alfabeto[$columna]  .''.($fila+1), $tipoMov)
                    ->setCellValue($alfabeto[$columna+1].''.($fila),   'Destino')
                    ->setCellValue($alfabeto[$columna+1].''.($fila+1), $campaign)
                    ->setCellValue($alfabeto[$columna+2].''.($fila),   'Fecha del movimiento')
                    ->setCellValue($alfabeto[$columna+2].''.($fila+1), $fecha);
        
        $columna = $columna + 4;
    }
    
    
    
    
    $fila = $fila + 3;
}








// Rename worksheet
$objPHPExcel->setActiveSheetIndex(1)->setTitle('Hoja de vida de diademas');
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="01simple.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;


?>