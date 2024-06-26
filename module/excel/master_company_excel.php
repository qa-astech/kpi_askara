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
$sql = "SELECT cm.*, a.user_entry, a.last_update, a.id_det_company, a.golongan,
b.id_section, b.name_section,
c.id_department, c.name_department,
d.id_plant, d.name_plant,
e.id_position, e.name_position from company_master cm 
left join company_detail a on a.id_company = cm.id_company
left join section_master b on b.id_section = a.id_section
left join department_master c on c.id_department = b.id_department
left join plant_master d on d.id_plant = a.id_plant
left join position_master e on e.id_position = a.id_position
where a.last_update is not null
order by cm.id_company asc, a.id_det_company asc";


$query = $db->sendQuery($db->konek_sita_db(), $sql);
$items = pg_fetch_all($query);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getStyle('A4:O5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '')
            ->setCellValue('N1', 'Bekasi, ' . $date)
            ->setCellValue('N2', 'Print By : ' . $_SESSION["user_kpi_askara"])
            ->setCellValue('A4', 'Master Perusahaan')
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'Kode Perusahaan')
            ->setCellValue('C5', 'Nama Perusahaan')
            ->setCellValue('D5', 'Singkatan')
            ->setCellValue('E5', 'Grup')
            ->setCellValue('F5', 'Customer')
            ->setCellValue('G5', 'Suplier')
            ->setCellValue('H5', 'Kode Detail Struktur')
            ->setCellValue('I5', 'Department')
            ->setCellValue('J5', 'Divisi')
            ->setCellValue('K5', 'Posisi')
            ->setCellValue('L5', 'Plant')
            ->setCellValue('M5', 'Golongan')
            ->setCellValue('N5', 'User Entry')
            ->setCellValue('O5', 'Last Update')
            ;
$no = 6;
$max = 0;
$num = 1;
if (!empty($items)) {
	foreach ($items as $key => $value) {
        $id = $value['id_company'];
        $name = $value['name_company'];
        $alias = $value['alias_company'];
        $gp = ($value['stat_group'] == 't') ? "✓" : '-';
        $cs = ($value['stat_customer'] == 't') ? "✓" : '-';
        $sp = ($value['stat_supplier'] == 't') ? "✓" : '-';
        $id_det = $value['id_det_company'];
        $dep = $value['name_department'];
        $sec = $value['name_section'];
        $pos = $value['name_position'];
        $plant = $value['name_plant'];
        $gol = $value['golongan'];
        $user_entry = $value['user_entry'];
        $last_update = $value['last_update'];
    
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$no, $num)
                    ->setCellValue('B'.$no, $id)
                    ->setCellValue('C'.$no, $name)
                    ->setCellValue('D'.$no, $alias)
                    ->setCellValue('E'.$no, $gp)
                    ->setCellValue('F'.$no, $cs)
                    ->setCellValue('G'.$no, $sp)
                    ->setCellValue('H'.$no, $id_det)
                    ->setCellValue('I'.$no, $dep)
                    ->setCellValue('J'.$no, $sec)
                    ->setCellValue('K'.$no, $pos)
                    ->setCellValue('L'.$no, $plant)
                    ->setCellValue('M'.$no, $gol)
                    ->setCellValue('N'.$no, $user_entry)
                    ->setCellValue('O'.$no, $last_update)
                    ;
        $num = $num + 1;
        $no = $no + 1;
    }
}
$sheet->mergeCells('N1:O1');
$sheet->mergeCells('N2:O2');
$sheet->mergeCells('A4:O4');
for ($col2 = 'A'; $col2 <= 'O'; $col2++) {
    $sheet->getColumnDimension($col2)->setAutoSize(true);
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Master Perusahaan');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Master Perusahaan.xlsx"');
$objWriter->save('php://output');
?>