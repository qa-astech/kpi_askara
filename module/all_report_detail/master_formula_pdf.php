<?php 
include("../../koneksi.php");
$db = new database();
$db->konek_sita_db();
// print_r($_SESSION);
$sql_w = "SELECT a.* from formula_master a
where a.last_update is not null
order by a.id_formula asc";
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
	<div style='position:absolute;margin-top:10px;'>";
		//<img src='../../image/(file)' alt='#' style='height:45px;'/>
$content .= "
	</div>	
	<page_footer>
		<div style='width:100%;text-align:right;margin-bottom:100%'>page [[page_cu]] of [[page_nb]]</div>
    </page_footer> 
	<div style='margin-top:0px;margin-left:570px'>
		<p align=''>Bekasi, ".$date."<br>Print By : ".$_SESSION["user_kpi_askara"]."</p>
	</div>

	<div>
		
	<h3 align='center'>Master Formula</h3>
		<table align='center' style='font-size:10px;'>
			<tr>
				<th valign='middle' align='center' style='height:30px;'>No.</th>
				<th valign='middle' align='center' style='height:30px;'>Kode Formula</th>
				<th valign='middle' align='center' style='height:30px;'>Nama Formula</th>
				<th valign='middle' align='center' style='height:30px;'>Rumus Formula</th>
				<th valign='middle' align='center' style='height:30px;'>Pengguna Terakhir</th>
				<th valign='middle' align='center' style='height:30px;'>Pembaharuan Terakhir</th>
								
			</tr>";

if (!empty($items)) {
	foreach ($items as $key => $value) {
		$id = $value['id_formula'];
		$name = $value['name_formula'];
		$rumus = $value['rumus_formula'];
		$user_entry = $value['user_entry'];
		$last_update = $value['last_update'];
		$content .= "
		<tr>
			<td valign='middle' align='center'>$nourut</td>
			<td valign='middle' style='width:90px;height:30px;'>$id</td>
			<td valign='middle' style='width:130px;height:30px;'>$name</td>
			<td valign='middle' style='width:70px;height:30px;'>$rumus</td>
			<td valign='middle' style=''>$user_entry</td>
			<td valign='middle' style='width:100px'>$last_update</td>
		</tr>
		";
		$nourut++;
	}
}
$content .= "</table></div></page>";
require_once('../../../third-party/html2pdf-4/html2pdf.class.php');
$html2pdf = new HTML2PDF('P','A4','en');
$html2pdf->WriteHTML($content);
$html2pdf->Output('Master Formula.pdf');
// echo $content;
?>

