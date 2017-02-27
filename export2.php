<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once 'php/PHPExcel.php';
include 'php/php_func.php';

header ('Pragma: public');
header ('Content-Disposition: attachment;filename="reporte_diademas_'.date('m_d_Y').'.xlsx"');
header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header ('Cache-Control: cache, must-revalidate');
header ('Cache-Control: max-age=0');
header ('Cache-Control: max-age=1');

$flag             = 2;
$objPHPExcel      = new PHPExcel();
$diademas         = array();
$diademas2        = array();
$collection       = fMongoDB();
$cursor           = $collection->find();
$coordinadores    = getListaCoordinadores();
$cantReparaciones = getCantidadReparaciones();
$query            = $collection->find();
$campname         = getListaCampaigns();
$alfabeto1        = range("A", "Z");
$alfabeto2        = array("B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Z", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ", "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ");
$campaigns        = array_values(getListaCampaigns());
$archivo          = "reporte_diademas_".date('m_d_Y').".xls";
$centrado         = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
$izquierda        = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));



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

$objPHPExcel->getActiveSheet()->getStyle("A1:P1")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(26);
$objPHPExcel->getActiveSheet()->getStyle('A')    ->applyFromArray($izquierda);
$objPHPExcel->getActiveSheet()->getStyle('B')    ->applyFromArray($centrado);
$objPHPExcel->getActiveSheet()->getStyle('C')    ->applyFromArray($izquierda);
$objPHPExcel->getActiveSheet()->getStyle('D')    ->applyFromArray($centrado);
$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($centrado);
$objPHPExcel->getActiveSheet()->setAutoFilter('A1:H1');
$objPHPExcel->getActiveSheet()->freezePane('A2');

$diademas  = getCantidadDiademasPorCampaign();
$campaigns = getListaCampaigns();
$campid    = array_keys($campaigns);

for($i = 0; $i < count($campid); $i++){
    for($j = 0; $j < count($diademas[$campid[$i]]); $j++){
        $camp = "";
        $diademaptemp = $diademas[$campid[$i]][$j];
        $resumentemp  = end($diadematemp['resumen']);
        if(isset($resumentemp['coordinador_id'])){
            $coorid   = 'coordinador_id';
            $coor     = $coordinadores[$coorid]['nombre'];
            $camp     = $coordinadores[$coorid]['nombrecamp'];
            $id       = $diadematemp['_id'];
            $marca    = $diadematemp['Marca'];
            $serial   = $diadematemp['serial'];
        }
        
        $esta = $resumentemp['estado'];
        if($camp === ""){
            $camp = "En reparación";
        }elseif($camp === "6118" || $esta == "0"){
            $camp = "Tecnología";
            $coor = "Mesa mantenimiento";
        }elseif($esta == "3" || $esta == "2"){
            break(1);
        }
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$flag, $id)
                ->setCellValue('B'.$flag, $marca)
                ->setCellValue('C'.$flag, $serial)
                ->setCellValue('D'.$flag, $camp)
                ->setCellValue('E'.$flag, $coor)
                ->setCellValue('F'.$flag, $cantReparaciones[$id])
                ->setCellValue('G'.$flag, '=IF(B'.($flag).'=N1,N2,IF(B'.($flag).'=O1,O2, IF(B'.($flag).'=P1,P2)))')
                ->setCellValue('H'.$flag, "=F$flag*14000");
        $flag++;
    }
}

/*foreach($cursor as $document){
    if(isset(end($document['resumen'])['coordinador_id'])){
        $coorid = end($document['resumen'])['coordinador_id'];
        $coor   = htmlentities($coordinadores[$coorid]['nombre'], ENT_HTML5,'UTF-8');
        $camp   = $coordinadores[$coorid]['nombrecamp'];
    }
    $esta = end($document['resumen'])['estado'];
    if($camp == ""){
        $camp = "En reparación";
    }elseif($camp === "6118" || $esta === "0"){
        $camp = "Tecnología";
        $coor = "Mesa Mantenimiento";
    }elseif($esta == "3" || $esta == "2"){
        break(1);
    }
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$flag, $document['_id'])
                ->setCellValue('B'.$flag, ucfirst(strtolower($document['Marca'])))
                ->setCellValue('C'.$flag, $document['serial'])
                ->setCellValue('D'.$flag, $camp)
                ->setCellValue('E'.$flag, $coor)
                ->setCellValue('F'.$flag, $cantReparaciones[$document['_id']])
                ->setCellValue('G'.$flag, '=IF(B'.($flag).'=N1,N2,IF(B'.($flag).'=O1,O2, IF(B'.($flag).'=P1,P2)))')
                ->setCellValue('H'.$flag, "=F$flag*14000");
    $flag++;
}*/

 ///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1)
            ->setTitle('Hoja de vida de diademas')
            ->setCellValue('A1', 'ID de diadema')
            ->setCellValue('B1', 'Movimientos');

$objPHPExcel->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(19);
$objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray($centrado);
$objPHPExcel->getActiveSheet()->getStyle("A1:P1")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->freezePane('A2');

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
    $d1 = $a1['resumen'][0]['fechaMov'];
    $d2 = $a2['resumen'][0]['fechaMov'];
    return $d2 - $d1;
});

for($i = 0; $i < count($diademas2); $i++){
    $diadematemp = $diademas2[$i];
    $id          = $diadematemp['_id'];
    $fila        = $i+2;
    $objPHPExcel->getActiveSheet()->setCellValue("A$fila", $id);
    
    for($j = 0; $j < count($diadematemp['resumen']); $j++){
            
        $resumentemp = $diadematemp['resumen'][$j];        
        $fecha       = $resumentemp['fechaMov'];
        $estado      = $resumentemp['estado'];
        $fecha       = date("d/m/Y", strtotime($fecha));
        $columna     = $alfabeto2[$j];

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
        if(isset($resumentemp['caso']))
            $caso            = " (caso # ".$resumentemp['caso'].")";
        else
            $caso            = "";

        switch($estado){
            case 0:  $motivo = "$movimientos[0]";
                break; 
            case 1:  $motivo = $movimientos[1]." ".$campname[$camp]['nombre'];
                break; 
            case 2:  $motivo = $movimientos[2];
                break; 
            case 3:  $motivo = $movimientos[3];
                break;
        }
        
        $objRichText = new PHPExcel_RichText();
        
        $objBold = $objRichText->createTextRun($motivo);
        $objBold->getFont()->setBold(true);
        $objRichText->createText(" el día ");
        $objBold = $objRichText->createTextRun($fecha);
        $objBold->getFont()->setBold(true);
        $objRichText->createText(" por ");
        $objBold = $objRichText->createTextRun(getTecnicos()[$tecnico]['nombre']);
        $objBold->getFont()->setBold(true);
        $objRichText->createText("$caso");
        
        $objPHPExcel->getActiveSheet()->setCellValue("$columna$fila", $objRichText);
        $objPHPExcel->getActiveSheet()->getStyle("$columna$fila")->applyFromArray($izquierda);
        $objPHPExcel->getActiveSheet()->getColumnDimension($columna)->setWidth(68);
        $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($izquierda);
        $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($centrado);
        $resumentemp = "";
        $fecha       = "";
        $estado      = "";
        $fecha       = "";
        $motivo      = "";
    }
}

        //////////////////////////// / /////////////////////////////////////
       //////////////////////////// / /////////////////////////////////////
      //////////////////////////// / /////////////////////////////////////

$lista = getDiademasEnBaja();

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(2)
            ->setTitle('Diademas dadas de baja')
            ->setCellValue('A1', 'ID de diadema')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Técnico');

for($i = 0; $i < count($lista); $i++){
    $fila  = $i+2;
    $id    = $lista[$i]['_id'];
    $fecha = end($lista[$i]['resumen'])['fechaMov'];
    $tecid = end($lista[$i]['resumen'])['tecnico_id'];
    $tecn  = getTecnicos()[$tecid]['nombre'];
    $objPHPExcel->getActiveSheet()->setCellValue("A$fila", $id);
    $objPHPExcel->getActiveSheet()->setCellValue("B$fila", $fecha);
    $objPHPExcel->getActiveSheet()->setCellValue("C$fila", $tecn);
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$objPHPExcel->getActiveSheet()->freezePane('A2');
$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->applyFromArray($centrado);
$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold(true);


  //////////////////////////// / /////////////////////////////////////
 //////////////////////////// / /////////////////////////////////////
//////////////////////////// / /////////////////////////////////////


$lista = getDiademasEnReparacion();

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(3)
            ->setTitle('Diademas en reparación')
            ->setCellValue('A1', 'ID de diadema')
            ->setCellValue('B1', 'Fecha');

for($i = 0; $i < count($lista); $i++){
    $fila  = $i+2;
    $id    = $lista[$i]['_id'];
    $fecha = end($lista[$i]['resumen'])['fechaMov'];
    $tecid = end($lista[$i]['resumen'])['tecnico_id'];
    $tecn  = getTecnicos()[$tecid]['nombre'];
    $objPHPExcel->getActiveSheet()->setCellValue("A$fila", $id);
    $objPHPExcel->getActiveSheet()->setCellValue("B$fila", $fecha);
    $objPHPExcel->getActiveSheet()->setCellValue("C$fila", $tecn);
}

$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->applyFromArray($centrado);
$objPHPExcel->getActiveSheet()->getStyle("A2:A10000")->applyFromArray($izquierda);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$objPHPExcel->getActiveSheet()->freezePane('A2');

  //////////////////////////// / /////////////////////////////////////
 //////////////////////////// / /////////////////////////////////////
//////////////////////////// / /////////////////////////////////////

$objPHPExcel->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>