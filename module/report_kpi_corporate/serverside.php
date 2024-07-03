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
      $year_kpi_corp = $cleanWord->numberCk(@$_POST["year_kpi"], true);
      $year_baseline1 = $year_kpi_corp - 1;
      $year_baseline2 = $year_kpi_corp - 2;
      $year_baseline3 = $year_kpi_corp - 3;


      $cek = "SELECT distinct a.*,
      reverse(substring(reverse(a.index_kpicorp), position('.' in reverse(a.index_kpicorp)) + 1)) index_parent,
      c.name_perspective, c.index_perspective, c.alias_perspective, ('(' || c.alias_perspective || ') ' || c.name_perspective)::varchar text_perspective,
      d.name_satuan,
      e.name_formula,
      b.index_sobject, (c.alias_perspective || b.index_sobject || '. ' || b.name_sobject)::varchar text_sobject,
      f.target_kpicorp baseline_1, g.target_kpicorp baseline_2, h.target_kpicorp baseline_3,
      STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] arr_index,
      bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_corporate a
      left join strategic_objective b on b.id_sobject = a.id_sobject
      left join perspective c on c.id_perspective = b.id_perspective
      left join satuan_master d on d.id_satuan = a.id_satuan
      left join formula_master e on e.id_formula = a.id_formula
      left join kpi_corporate f on f.id_sobject = a.id_sobject and f.name_kpicorp = a.name_kpicorp and f.year_kpicorp = :yearBaseline1
      left join kpi_corporate g on g.id_sobject = a.id_sobject and g.name_kpicorp = a.name_kpicorp and g.year_kpicorp = :yearBaseline2
      left join kpi_corporate h on h.id_sobject = a.id_sobject and h.name_kpicorp = a.name_kpicorp and h.year_kpicorp = :yearBaseline3
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpicorp = :yearKpiCorp and a.terbit_kpicorp = true
      order by c.index_perspective asc, STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] asc";
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, array(
        ':yearBaseline1' => $year_baseline1,
        ':yearBaseline2' => $year_baseline2,
        ':yearBaseline3' => $year_baseline3,
        ':yearKpiCorp' => $year_kpi_corp
      ));
      $main_data = $query->fetchAll();

      $cek_month = "SELECT id_kpicorp, month_realisasi, sum(realisasi) realisasi
      from (
        SELECT distinct id_kpicorp, month_kpidept as month_realisasi, sum(value_kpidept_real) as realisasi
        from target_distinct_kpicorps_dept
        where id_kpicorp IN (SELECT distinct id_kpicorp from kpi_corporate where year_kpicorp = :yearKpiCorp and terbit_kpicorp = true)
        group by id_kpicorp, month_kpidept
        UNION ALL
        SELECT distinct id_kpicorp, month_kpidivcorp as month_realisasi, sum(value_kpidivcorp_real) as realisasi
        from target_distinct_kpicorps_divcorp
        where id_kpicorp IN (SELECT distinct id_kpicorp from kpi_corporate where year_kpicorp = :yearKpiCorp and terbit_kpicorp = true)
        group by id_kpicorp, month_kpidivcorp
      ) tbl
      group by id_kpicorp, month_realisasi
      order by month_realisasi asc";
      $query_month = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_month, array(
        ':yearKpiCorp' => $year_kpi_corp,
        ':yearKpiCorp' => $year_kpi_corp
      ));
      $data_month = $query_month->fetchAll();

      $cek_year = "SELECT distinct id_kpicorp, id_company as compkpi_id, name_company as compkpi_name, target_kpibunit as target_bisnis, sum(value_kpidept_real) as realisasi
      from target_distinct_kpicorps_dept
      where id_kpicorp IN (SELECT distinct id_kpicorp from kpi_corporate where year_kpicorp = :yearKpiCorp and terbit_kpicorp = true)
      group by id_kpicorp, id_company, name_company, target_kpibunit
      UNION ALL
      SELECT distinct id_kpicorp, id_department as compkpi_id, name_department as compkpi_name, sum(target_kpidivcorp) as target_bisnis, sum(value_kpidivcorp_real) as realisasi
      from target_distinct_kpicorps_divcorp
      where id_kpicorp IN (SELECT distinct id_kpicorp from kpi_corporate where year_kpicorp = :yearKpiCorp and terbit_kpicorp = true)
      group by id_kpicorp, id_department, name_department";
      $query_year = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_year, array(
        ':yearKpiCorp' => $year_kpi_corp,
        ':yearKpiCorp' => $year_kpi_corp
      ));
      $data_year = $query_year->fetchAll();

      $cek_total_realisasi = "SELECT id_kpicorp, sum(realisasi) total_realisasi
      from (
        SELECT distinct id_kpicorp, sum(value_kpidept_real) as realisasi
        from target_distinct_kpicorps_dept
        where id_kpicorp IN (SELECT distinct id_kpicorp from kpi_corporate where year_kpicorp = :yearKpiCorp and terbit_kpicorp = true)
        group by id_kpicorp
        UNION ALL
        SELECT distinct id_kpicorp, sum(value_kpidivcorp_real) as realisasi
        from target_distinct_kpicorps_divcorp
        where id_kpicorp IN (SELECT distinct id_kpicorp from kpi_corporate where year_kpicorp = :yearKpiCorp and terbit_kpicorp = true)
        group by id_kpicorp
      ) tbl
      group by id_kpicorp";
      $query_total_realisasi = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_total_realisasi, array(
        ':yearKpiCorp' => $year_kpi_corp,
        ':yearKpiCorp' => $year_kpi_corp
      ));
      $data_total_realisasi = $query_total_realisasi->fetchAll();

      foreach ($main_data as $key => $value) {
        $main_data[$key]['month'] = array_filter($data_month, function($filterVal) use ($value) {
          return $filterVal['id_kpicorp'] === $value['id_kpicorp'];
        });
        $main_data[$key]['year'] = array_filter($data_year, function($filterVal) use ($value) {
          return $filterVal['id_kpicorp'] === $value['id_kpicorp'];
        });
        $main_data[$key]['totalRealisasi'] = array_column(array_filter($data_total_realisasi, function($filterVal) use ($value) {
          return $filterVal['id_kpicorp'] === $value['id_kpicorp'];
        }), 'total_realisasi')[0];
      }
      return json_encode($main_data);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function jsonTahun(){
    try {
      global $cleanWordPDO;
      $q = $cleanWordPDO->textCk(@$_POST["q"], false, 'normal');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;


      $cek = "SELECT distinct year_kpicorp as id, year_kpicorp as text from kpi_corporate where year_kpicorp::text ilike :search";
      $cek_main = $cek . " order by year_kpicorp asc offset :offset limit :recordsPerPage";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_main, array(
        ':search' => '%'.$q.'%',
        ':offset' => $offset,
        ':recordsPerPage' => $records_per_page
      ));
      $items = $query_main->fetchAll();


      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_count, array(
        ':search' => '%'.$q.'%'
      ));
      $total_count = $query_count->fetchAll();

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

  public function getDataBisnisUnit(){
    try {
      $cek = "SELECT * FROM (
        SELECT id_company, name_company, 1::integer ordering_data from company_master
        UNION ALL
        SELECT id_department as id_company, 'DIVISI KORPORAT ' || name_department as name_company, 2::integer ordering_data from department_master
      ) tbl order by ordering_data asc, name_company asc";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      return json_encode($response);
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }
}
?>