<?php 
include_once(__DIR__ . '/../../koneksi.php');
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();

$sql_w = "SELECT cm.*, a.user_entry, a.last_update, a.id_det_company, a.golongan,
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
    td {
      white-space: break-spaces;
      text-wrap: wrap;
    }
    td, th {
      padding-left: 2px;
      padding-right: 2px;
    }
    .page-footer {
      text-align: right;
    }
	</style>
	<page>
    <page_footer class='page-footer'>
      page [[page_cu]] of [[page_nb]]
    </page_footer>";

    // <div style='position:absolute;margin-top:10px;'>
		//   <img src='../../image/(file)' alt='#' style='height:45px;'/>
    // </div>

$content .= "
    <div style='margin-top:0px;margin-left:1310px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

	  <div>
		
      <h3 align='center'>Master Perusahaan</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Perusahaan</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Perusahaan</th>
          <th valign='middle' align='center' style='height:30px;'>Singkatan</th>
          <th valign='middle' align='center' style='height:30px;'>Grup</th>
          <th valign='middle' align='center' style='height:30px;'>Customer</th>
          <th valign='middle' align='center' style='height:30px;'>Suplier</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Detail Struktur</th>
          <th valign='middle' align='center' style='height:30px;'>Department</th>
          <th valign='middle' align='center' style='height:30px;'>Divisi</th>
          <th valign='middle' align='center' style='height:30px;'>Posisi</th>
          <th valign='middle' align='center' style='height:30px;'>Plant</th>
          <th valign='middle' align='center' style='height:30px;'>Golongan</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
        </tr>";
if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['id_company'];
		$comp = $value['name_company'];
		$alias = $value['alias_company'];
		$gp = ($value['stat_group'] == 't') ? "<img src='../../image/asset-pdf/check-solid.png' style='width:20%;'/>" : '-';
		$cs = ($value['stat_customer'] == 't') ? "<img src='../../image/asset-pdf/check-solid.png' style='width:20%;'/>" : '-';
		$sp = ($value['stat_supplier'] == 't') ? "<img src='../../image/asset-pdf/check-solid.png' style='width:20%;'/>" : '-';
		$id_det = $value['id_det_company'];
		$dep = $value['name_department'];
		$sec = $value['name_section'];
		$pos = $value['name_position'];
		$plant = $value['name_plant'];
		$gol = $value['golongan'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
        <tr>
          <td valign='middle' align='center'>$nourut</td>
          <td valign='middle' style='text-align:center;'>$id</td>
          <td valign='middle' style='text-align:center;'>$comp</td>
          <td valign='middle' style='text-align:center;'>$alias</td>
          <td valign='middle' style='width:50px;text-align:center;'>$gp</td>
          <td valign='middle' style='width:50px;text-align:center;'>$cs</td>
          <td valign='middle' style='width:50px;text-align:center;'>$sp</td>
          <td valign='middle' style='text-align:center;'>$id_det</td>
          <td valign='middle' style='width:140px;'>$dep</td>
          <td valign='middle' style='width:120px'>$sec</td>
          <td valign='middle' style='width:100px;text-align:center;'>$pos</td>
          <td valign='middle' style='text-align:center;'>$plant</td>
          <td valign='middle' style='text-align:center;'>$gol</td>
          <td valign='middle' style='text-align:center;'>$user_entry</td>
          <td valign='middle' style='text-align:center;'>$last_update</td>
        </tr>
			";
			$nourut++;
	}
}
$content .= "</table></div></page>";

$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('L', 'A3', 'en');
$html2pdf->writeHTML(trim($content));
$html2pdf->output();
?>

