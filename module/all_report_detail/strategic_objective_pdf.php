<?php 
include_once(__DIR__ . '/../../koneksi.php');
require (__DIR__ . '/../../../third-party/html2pdf-5/autoload.php');

$db = new database();

$sql_w = "SELECT a.*, ('(' || b.alias_perspective || ') ' || b.name_perspective)::varchar text_perspective, (b.alias_perspective || a.index_sobject || '. ' || a.name_sobject)::varchar text_sobject
from strategic_objective a
left join perspective b on b.id_perspective = a.id_perspective
where a.last_update is not null
order by a.id_sobject asc
";
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
    <div style='margin-top:0px;margin-left:910px'>
      <p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
    </div>

    <div>
      <h3 align='center'>Strategi Objektif</h3>
      <table align='center' style='font-size:10px;'>
        <tr>
          <th valign='middle' align='center' style='height:30px;'>No.</th>
          <th valign='middle' align='center' style='height:30px;'>Kode Strategic Objektif</th>
          <th valign='middle' align='center' style='height:30px;'>Perspektif</th>
          <th valign='middle' align='center' style='height:30px;'>Strategi Objektif</th>
          <th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
          <th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>					
        </tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['id_sobject'];
		$text_perspective = $value['text_perspective'];
		$text_sobject = $value['text_sobject'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
        <tr>
          <td valign='middle' align='center'>$nourut</td>
          <td valign='middle'>$id</td>
          <td valign='middle'>$text_perspective</td>
          <td valign='middle'>$text_sobject</td>
          <td valign='middle'>$user_entry</td>
          <td valign='middle'>$last_update</td>
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

