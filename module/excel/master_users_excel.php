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
$sql = "SELECT a.*, b.*, a.user_entry as user_entry_a, a.last_update as last_update_a from users a
left join all_users_setup b on b.nik = a.nik_users
where a.last_update is not null
order by a.nik_users asc";


$query = $db->sendQuery($db->konek_sita_db(), $sql);
$items = pg_fetch_all($query);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getStyle('A4:O5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '')
            ->setCellValue('N1', 'Bekasi, ' . $date)
            ->setCellValue('N2', 'Print By : ' . $_SESSION["user_kpi_askara"])
            ->setCellValue('A4', 'Master Users')
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'NIK')
            ->setCellValue('C5', 'Nama Panggilan (Username)')
            ->setCellValue('D5', 'Nama Lengkap')
            ->setCellValue('E5', 'Kode Detail')
            ->setCellValue('F5', 'Perusahaan')
            ->setCellValue('G5', 'Departemen')
            ->setCellValue('H5', 'Divisi')
            ->setCellValue('I5', 'Posisi')
            ->setCellValue('J5', 'Plant')
            ->setCellValue('K5', 'Golongan')
            ->setCellValue('L5', 'Status Aktif')
            ->setCellValue('M5', 'Peran Kerja Utama')
            ->setCellValue('N5', 'User Entry')
            ->setCellValue('O5', 'Last Update')
            ;
$no = 6;
$max = 0;
$num = 1;
if (!empty($items)) {
	foreach ($items as $key => $value) {
        $id =  $value['nik_users'];
        $name =  $value['username_users'];
        $full =  $value['fullname_users'];
        $id_usersetup =  $value['id_usersetup'];
        $name_company =  $value['name_company'];
        $name_department =  $value['name_department'];
        $name_section =  $value['name_section'];
        $name_position =  $value['name_position'];
        $name_plant =  $value['name_plant'];
        $golongan =  $value['golongan'];
        $status_active = ( $value['status_active']=='t')? "✓" :(( $value['status_active']=='f')? "X" : "");
        $role_utama = ( $value['role_utama']=='t')? "✓" : (( $value['role_utama']=='f')? "X" : "");
        $user_entry = ( $value['user_entry']==null)?  $value['user_entry_a'] :  $value['user_entry'];
        $last_update = ( $value['last_update']==null)?  $value['last_update_a'] :  $value['last_update'];
        $sheet->getStyle('B'.$no)->getNumberFormat()->setFormatCode('0');
    
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$no, $num)
                    ->setCellValue('B'.$no, $id)
                    ->setCellValue('C'.$no, $name)
                    ->setCellValue('D'.$no, $full)
                    ->setCellValue('E'.$no, $id_usersetup)
                    ->setCellValue('F'.$no, $name_company)
                    ->setCellValue('G'.$no, $name_department)
                    ->setCellValue('H'.$no, $name_section)
                    ->setCellValue('I'.$no, $name_position)
                    ->setCellValue('J'.$no, $name_plant)
                    ->setCellValue('K'.$no, $golongan)
                    ->setCellValue('L'.$no, $status_active)
                    ->setCellValue('M'.$no, $role_utama)
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
$objPHPExcel->getActiveSheet()->setTitle('Master Users');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Master Users.xlsx"');
$objWriter->save('php://output');
?>