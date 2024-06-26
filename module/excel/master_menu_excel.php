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
$sql = "SELECT m.*, ma.* from menu m 
left join menu_access ma on ma.code_menu = m.code_menu
where ma.last_update is not null
order by m.code_menu asc, ma.code_maccess asc";


$query = $db->sendQuery($db->konek_sita_db(), $sql);
$items = pg_fetch_all($query);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getStyle('A4:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '')
            ->setCellValue('G1', 'Bekasi, ' . $date)
            ->setCellValue('G2', 'Print By : ' . $_SESSION["user_kpi_askara"])
            ->setCellValue('A4', 'Master Menu')
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'Kode Menu')
            ->setCellValue('C5', 'Nama Menu')
            ->setCellValue('D5', 'Link')
            ->setCellValue('E5', 'Kode Access')
            ->setCellValue('F5', 'Nama Access')
            ->setCellValue('G5', 'User Entry')
            ->setCellValue('H5', 'Last Update')
            ;
$no = 6;
$max = 0;
$num = 1;
if (!empty($items)) {
	foreach ($items as $key => $value) {
        $id =  $value['code_menu'];
        $name =  $value['title_menu'];
        $alias =  $value['link_menu'];
        $id_det =  $value['code_maccess'];
        $name_det =  $value['name_maccess'];
        $user_entry =  $value['user_entry'];
        $last_update =  $value['last_update'];
    
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$no, $num)
                    ->setCellValue('B'.$no, $id)
                    ->setCellValue('C'.$no, $name)
                    ->setCellValue('D'.$no, $alias)
                    ->setCellValue('E'.$no, $id_det)
                    ->setCellValue('F'.$no, $name_det)
                    ->setCellValue('G'.$no, $user_entry)
                    ->setCellValue('H'.$no, $last_update)
                    ;
        $num = $num + 1;
        $no = $no + 1;
    }
}
$sheet->mergeCells('G1:H1');
$sheet->mergeCells('G2:H2');
$sheet->mergeCells('A4:H4');
for ($col2 = 'A'; $col2 <= 'H'; $col2++) {
    $sheet->getColumnDimension($col2)->setAutoSize(true);
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Master Menu');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Master Menu.xlsx"');
$objWriter->save('php://output');
?>