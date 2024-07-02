<?php 
include_once(__DIR__ . '/../../koneksi.php');
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();

$sql_w = "SELECT a.* from plant_master a
where a.last_update is not null
order by a.id_plant asc";
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
    <page_footer class='page-footer'>
      page [[page_cu]] of [[page_nb]]
    </page_footer>
  ";
    // <div style='position:absolute;margin-top:10px;'>
		//   <img src='../../image/(file)' alt='#' style='height:45px;'/>
    // </div>
$content .= "
    <div style='margin-top:0px;margin-left:570px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

	  <div>
      <h3 align='center'>Master Plant</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Plant</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Plant</th>
          <th valign='middle' align='center' style='height:30px;'>Singkatan</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
        </tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['id_plant'];
		$name = $value['name_plant'];
		$alias = $value['alias_plant'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
        <tr>
          <td valign='middle' align='center'>$nourut</td>
          <td valign='middle' style='width:90px;height:30px;'>$id</td>
          <td valign='middle' style='width:130px;height:30px;'>$name</td>
          <td valign='middle' style='width:70px;height:30px;'>$alias</td>
          <td valign='middle' style=''>$user_entry</td>
          <td valign='middle' style='width:100px'>$last_update</td>
        </tr>
		";
		$nourut++;
	}
}

$content .= "</table></div></page>";

$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
$html2pdf->writeHTML(trim($content));
$html2pdf->output();
?>