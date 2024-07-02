<?php 
include_once(__DIR__ . '/../../koneksi.php');
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();

$sql_w ="SELECT dm.*, sm.* from department_master dm 
left join section_master sm on sm.id_department = dm.id_department
where sm.last_update is not null
order by dm.id_department asc, sm.id_section asc";
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
    </page_footer>
	";
    // <div style='position:absolute;margin-top:10px;'>
    //   <img src='../../image/(file)' alt='#' style='height:45px;'/>
    // </div>
$content .= "
    <div style='margin-top:0px;margin-left:910px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

	  <div>
		
      <h3 align='center'>Master Department</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Department</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Department</th>
          <th valign='middle' align='center' style='height:30px;'>Singkatan</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Divisi</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Divisi</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
        </tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['id_department'];
		$dep = $value['name_department'];
		$alias = $value['alias_department'];
		$id_det = $value['id_section'];
		$sec = $value['name_section'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
        <tr>
          <td valign='middle' align='center'>$nourut</td>
          <td valign='middle' align='center'>$id</td>
          <td valign='middle'>$dep</td>
          <td valign='middle' align='center'>$alias</td>
          <td valign='middle' align='center'>$id_det</td>
          <td valign='middle' style='width: 180px;'>$sec</td>
          <td valign='middle' align='center'>$user_entry</td>
          <td valign='middle' align='center'>$last_update</td>
        </tr>
		";
		$nourut++;
	}
}
$content .= "</table></div></page>";

$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('L', 'A4', 'en');
$html2pdf->writeHTML(trim($content));
$html2pdf->output();
?>

