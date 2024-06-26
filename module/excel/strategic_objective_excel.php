<?php
require_once('../../../third-party/phpexcel/PHPExcel.php');
include("../../koneksi.php");
$db = new database();
$db->konek_sita_db();
date_default_timezone_set('Asia/Jakarta');

PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
$objPHPExcel = new PHPExcel(); 
$date=date("d M y / H:i:s",time());

function getAlpha($colNumber) {
    return PHPExcel_Cell::stringFromColumnIndex($colNumber);
}
  
function getCol($alphabet) {
    return PHPExcel_Cell::columnIndexFromString($alphabet)-1;
}
$date=date("d M y / h:i:s",time());
// print_r($where);
// die();
$sql = "SELECT a.*, ('(' || b.alias_perspective || ') ' || b.name_perspective)::varchar text_perspective, (b.alias_perspective || a.index_sobject || '. ' || a.name_sobject)::varchar text_sobject
from strategic_objective a
left join perspective b on b.id_perspective = a.id_perspective
where a.last_update is not null
order by a.id_sobject asc";


$query = $db->sendQuery($db->konek_sita_db(), $sql);
$items = pg_fetch_all($query);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getStyle('A4:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '')
            ->setCellValue('E1', 'Bekasi, ' . $date)
            ->setCellValue('E2', 'Print By : ' . $_SESSION["user_kpi_askara"])
            ->setCellValue('A4', 'Master Strategic Objective')
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'Kode Strategic Objective')
            ->setCellValue('C5', 'Perspektif')
            ->setCellValue('D5', 'Strategic Objective')
            ->setCellValue('E5', 'User Entry')
            ->setCellValue('F5', 'Last Update')
            ;
$no = 6;
$max = 0;
$num = 1;
if (!empty($items)) {
	foreach ($items as $key => $value) {
        $id = $value['id_sobject'];
        $text_perspective = $value['text_perspective'];
        $text_sobject = $value['text_sobject'];
        $user_entry = $value['user_entry'];
        $last_update = $value['last_update'];
    
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$no, $num)
                    ->setCellValue('B'.$no, $id)
                    ->setCellValue('C'.$no, $text_perspective)
                    ->setCellValue('D'.$no, $text_sobject)
                    ->setCellValue('E'.$no, $user_entry)
                    ->setCellValue('F'.$no, $last_update)
                    ;
        $num = $num + 1;
        $no = $no + 1;
    }
}
$sheet->mergeCells('E1:F1');
$sheet->mergeCells('E2:F2');
$sheet->mergeCells('A4:F4');
for ($col2 = 'A'; $col2 <= 'F'; $col2++) {
    $sheet->getColumnDimension($col2)->setAutoSize(true);
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Master Strategic Objective');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Master Strategic Objective.xlsx"');
$objWriter->save('php://output');
?>