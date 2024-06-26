<?php
class kpi_corporate extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getKpiCorporate(){
    global $cleanWord;
    try {
      $year_kpi_corp = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $copy_kpi = $cleanWord->textCk(@$_POST["copy_kpi"], true, 'trim');
      $sql_copy = $copy_kpi == 'copy' ? "and a.terbit_kpicorp = true" : '';
      $year_baseline = $cleanWord->numberCk(@$_POST["year_baseline"], true, 'integer');
      $cek = "SELECT distinct a.*, '$copy_kpi'::varchar status_copy,
      reverse(substring(reverse(a.index_kpicorp), position('.' in reverse(a.index_kpicorp)) + 1)) index_parent,
      c.name_perspective, c.alias_perspective, d.name_satuan, e.name_formula, b.index_sobject, a.year_kpicorp,
      (c.alias_perspective || b.index_sobject || '. ' || b.name_sobject)::varchar text_sobject,
      ('(' || c.alias_perspective || ') ' || c.name_perspective)::varchar text_perspective,
      f.target_kpicorp baseline_1, g.target_kpicorp baseline_2, h.target_kpicorp baseline_3,
      STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] arr_index
      from kpi_corporate a
      left join strategic_objective b on b.id_sobject = a.id_sobject
      left join perspective c on c.id_perspective = b.id_perspective
      left join satuan_master d on d.id_satuan = a.id_satuan
      left join formula_master e on e.id_formula = a.id_formula
      left join kpi_corporate f on f.id_sobject = a.id_sobject and f.name_kpicorp = a.name_kpicorp and f.year_kpicorp = ($year_baseline - 1)
      left join kpi_corporate g on g.id_sobject = a.id_sobject and g.name_kpicorp = a.name_kpicorp and g.year_kpicorp = ($year_baseline - 2)
      left join kpi_corporate h on h.id_sobject = a.id_sobject and h.name_kpicorp = a.name_kpicorp and h.year_kpicorp = ($year_baseline - 3)
      where a.year_kpicorp = $year_kpi_corp $sql_copy
      order by STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] asc";
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