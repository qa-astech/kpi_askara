<?php 
include_once(__DIR__ . '/../../koneksi.php');
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();

$sql_w = "SELECT a.*, b.*, a.user_entry as user_entry_a, a.last_update as last_update_a from users a
left join all_users_setup b on b.nik = a.nik_users
where a.last_update is not null
order by a.nik_users asc";
$query = $db->sendQuery($db->konek_sita_db(), $sql_w);
$items = pg_fetch_all($query);

$date=date("d M y / h:i:s",time());
$nourut = 1;
$content = "
	<style> 
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		th {
			background-color: #4bd2fe;
			color: black;
		}
	</style>
	<page>
    <page_footer>
      page [[page_cu]] of [[page_nb]]
    </page_footer>
	";
    // <div style='position:absolute;margin-top:10px;'>
		//   <img src='../../image/(file)' alt='#' style='height:45px;'/>
    // </div>
$content .= "
    <div style='margin-top:0px;margin-left:1310px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

    <div>
      <h3 align='center'>Users</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>NIK</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Panggilan (Username)</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Lengkap</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Detail</th>
          <th valign='middle' align='center' style='height:30px;'>Perusahaan</th>
          <th valign='middle' align='center' style='height:30px;'>Departemen</th>
          <th valign='middle' align='center' style='height:30px;'>Divisi</th>
          <th valign='middle' align='center' style='height:30px;'>Posisi</th>
          <th valign='middle' align='center' style='height:30px;'>Plan</th>
          <th valign='middle' align='center' style='height:30px;'>Golongan</th>
          <th valign='middle' align='center' style='height:30px;'>Status Aktif</th>
          <th valign='middle' align='center' style='height:30px;'>Peran Kerja Utama</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
        </tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['nik_users'];
		$name = $value['username_users'];
		$full = $value['fullname_users'];
		$id_usersetup = $value['id_usersetup'];
		$name_company = $value['name_company'];
		$name_department = $value['name_department'];
		$name_section = $value['name_section'];
		$name_position = $value['name_position'];
		$name_plant = $value['name_plant'];
		$golongan = $value['golongan'];
		$status_active = ($value['status_active']=='t')? "<img src='../../image/asset-pdf/check-solid.png' style='width:10%;'/>" : (($value['status_active']=='f')? "<img src='../../image/asset-pdf/xmark-solid.png' style='width:10%;'/>" : "");
		$role_utama = ($value['role_utama']=='t')? "<img src='../../image/asset-pdf/check-solid.png' style='width:10%;'/>" : (($value['role_utama']=='f')? "<img src='../../image/asset-pdf/xmark-solid.png' style='width:10%;'/>" : "");
		$user_entry = ($value['user_entry']==null)? $value['user_entry_a'] : $value['user_entry'];
		$last_update = ($value['last_update']==null)? $value['last_update_a'] : $value['last_update'];
		$content .= "
      <tr>
        <td valign='middle' align='center'>$nourut</td>
        <td valign='middle'>$id</td>
        <td valign='middle'>$name</td>
        <td valign='middle'>$full</td>
        <td valign='middle'>$id_usersetup</td>
        <td valign='middle'>$name_company</td>
        <td valign='middle'>$name_department</td>
        <td valign='middle'>$name_section</td>
        <td valign='middle'>$name_position</td>
        <td valign='middle'>$name_plant</td>
        <td valign='middle'>$golongan</td>
        <td valign='middle' align='center' style='width: 50px;'>$status_active</td>
        <td valign='middle' align='center' style='width: 50px;'>$role_utama</td>
        <td valign='middle'>$user_entry</td>
        <td valign='middle'>$last_update</td>
      </tr>
		";
		$nourut++;
	}
}
$content .= "</table></div></page>";

// echo trim($content);

$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('L', 'A3', 'en');
$html2pdf->writeHTML(trim($content));
$html2pdf->output();
?>

