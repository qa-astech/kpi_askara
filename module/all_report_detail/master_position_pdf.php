<?php 
include_once(__DIR__ . '/../../koneksi.php');

// *notes* Ini html2pdf 4
// include_once(__DIR__ . '/../../../third-party/html2pdf-4/html2pdf.class.php');

// *notes* Ini html2pdf 5
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();
$db->konek_sita_db();

$sql_w = "SELECT a.* from position_master a
where a.last_update is not null
order by a.id_position asc";
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


// *notes* Ada catatan disini untuk page_footer / page_header, jangan ada html tag lain dibawahnya, kalo mau styling kasih class aja
$content .= "
    <div style='margin-top:0px;margin-left:570px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

	  <div>
      <h3 align='center'>Master Posisi</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Posisi</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Posisi</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
        </tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['id_position'];
		$name = $value['name_position'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
        <tr>
          <td valign='middle' align='center'>$nourut</td>
          <td valign='middle' align='center'>$id</td>
          <td valign='middle' style=''>$name</td>
          <td valign='middle' align='center'>$user_entry</td>
          <td valign='middle' align='center'>$last_update</td>
        </tr>
		";
		$nourut++;
	}
}
$content .= "</table></div></page>";

// *notes* Ini setup untuk html2pdf yang ke 4
// $html2pdf = new HTML2PDF('P','A4','en');
// $html2pdf->WriteHTML($content);
// $html2pdf->Output('Master Posisi.pdf');

// *notes* Testup content htmlnya
// echo trim($content);
// die();

// *notes* Ini setup untuk html2pdf yang ke 5
$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
$html2pdf->writeHTML(trim($content));
$html2pdf->output();
?>