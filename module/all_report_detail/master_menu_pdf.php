<?php 
include_once(__DIR__ . '/../../koneksi.php');
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();

$sql_w = "SELECT m.*, ma.* from menu m 
left join menu_access ma on ma.code_menu = m.code_menu
where ma.last_update is not null
order by m.code_menu asc, ma.code_maccess asc";
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
    <page_footer>
      page [[page_cu]] of [[page_nb]]
    </page_footer>";
    // <div style='position:absolute;margin-top:10px;'>
		//   <img src='../../image/(file)' alt='#' style='height:45px;'/>
    // </div>
$content .= "
    <div style='margin-top:0px;margin-left:910px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

	  <div>
      <h3 align='center'>Master Menu</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Menu</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Menu</th>
          <th valign='middle' align='center' style='height:30px;'>Link</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Access</th>
          <th valign='middle' align='center' style='height:30px;'>Nama Access</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
        </tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['code_menu'];
		$title = $value['title_menu'];
		$link = $value['link_menu'];
		$id_det = $value['code_maccess'];
		$name = $value['name_maccess'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
        <tr>
          <td valign='middle' align='center'>$nourut</td>
          <td valign='middle' align='center'>$id</td>
          <td valign='middle'>$title</td>
          <td valign='middle'>$link</td>
          <td valign='middle'>$id_det</td>
          <td valign='middle'>$name</td>
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

