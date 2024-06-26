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
$sql = "SELECT dm.*, sm.* from department_master dm 
left join section_master sm on sm.id_department = dm.id_department
where sm.last_update is not null
order by dm.id_department asc, sm.id_section asc";


$query = $db->sendQuery($db->konek_sita_db(), $sql);
$items = pg_fetch_all($query);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getStyle('A4:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '')
            ->setCellValue('G1', 'Bekasi, ' . $date)
            ->setCellValue('G2', 'Print By : ' . $_SESSION["user_kpi_askara"])
            ->setCellValue('A4', 'Master Department')
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'Kode Department')
            ->setCellValue('C5', 'Nama Department')
            ->setCellValue('D5', 'Singkatan')
            ->setCellValue('E5', 'Kode Divisi')
            ->setCellValue('F5', 'Nama Divisi')
            ->setCellValue('G5', 'User Entry')
            ->setCellValue('H5', 'Last Update')
            ;
$no = 6;
$max = 0;
$num = 1;
if (!empty($items)) {
	foreach ($items as $key => $value) {
        $id =  $value['id_department'];
        $name =  $value['name_department'];
        $alias =  $value['alias_department'];
        $id_det =  $value['id_section'];
        $name_det =  $value['name_section'];
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
$objPHPExcel->getActiveSheet()->setTitle('Master Department');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Master Department.xlsx"');
$objWriter->save('php://output');
?>