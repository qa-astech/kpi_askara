<?php
class kpi_bisnis_unit extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getKpiBisnisUnit(){
    global $cleanWord;
    try {
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');

      $cek = "SELECT * from company_master
      order by name_company asc";

      $result = $this->sendQuery($this->konek_sita_db(), $cek);

      $cekup_kpi = "SELECT DISTINCT STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] arr_index, 
      a.name_kpibunit, a.index_kpibunit, a.define_kpibunit,
      a.control_cek_kpibunit, a.polaritas_kpibunit,
      a.target_kpibunit, 
      a.compkpi_id, a.compkpi_name,
      a.id_perspective, a.name_perspective, a.alias_perspective,
      a.id_satuan, a.name_satuan,
      a.id_formula, a.name_formula,
      a.index_parent,
      a.text_sobject,
      a.text_perspective,
      CASE
          WHEN a.id_kpicorp IS NULL THEN a.id_kpibunit
          ELSE a.id_kpicorp
      END AS id_kpicorp";
      $join = '';
      $i = 1;
      while ($row = pg_fetch_assoc($result)) {
          $comp_id = $row['id_company'];
          $cekup_kpi .= ", b$i.cascade_kpibunit AS ck_$i, b$i.target_kpibunit AS tk_$i";
          $join .= " LEFT JOIN kpi_bisnis_unit b$i ON b$i.compkpi_id = '$comp_id' AND b$i.id_kpibunit = a.id_kpibunit";
          $i++;
      }
      $cekup_kpi .= " FROM kpi_bisnis_unit a
         $join
         WHERE a.year_kpibunit = $year_kpi
         ORDER BY a.alias_perspective ASC, STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] ASC";
      // echo $cekup_kpi;
      // die;
      $query = $this->sendQuery($this->konek_sita_db(), $cekup_kpi);
      $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      return json_encode($response);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function getDataBisnisUnit(){
    try {
      $cek = "SELECT * from company_master
      order by name_company asc";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      return json_encode($response);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function jsonTahun(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct year_kpicorp as id, year_kpicorp as text from kpi_corporate where year_kpicorp::text ilike '%$q%'";
      $cek_main = $cek . " order by year_kpicorp asc offset $offset limit $records_per_page";
      $query = $this->sendQuery($this->konek_sita_db(), $cek_main);
      $items = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQuery($this->konek_sita_db(), $cek_count);
      $total_count = pg_fetch_all($query_count);

      $response = array(
        "items" => $items,
        "pagination" => array(
          "more" => ($page * $records_per_page) < $total_count[0]['count']
        )
      );
      return json_encode($response);
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }
}
?>