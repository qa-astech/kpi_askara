<?php
class report_kpi_division_corporate extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonTahun(){
    try {
      global $cleanWordPDO;
      $q = $cleanWordPDO->textCk(@$_POST["q"], false);
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;
      $sendArray = array(
        ':search' => '%'.$q.'%',
        ':offset' => $offset,
        ':recordsPerPage' => $records_per_page
      );

      $cek = "SELECT distinct year_kpidivcorp as id, year_kpidivcorp as text from kpi_divcorp where year_kpidivcorp::text ilike :search";
      $cek_main = $cek . " order by year_kpidivcorp asc offset :offset limit :recordsPerPage";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_main, $sendArray);
      $items = $query_main->fetchAll();

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_count, array_filter($sendArray, function ($v, $k) {
        return in_array($k, [':search'], true);
      }, ARRAY_FILTER_USE_BOTH));
      $total_count = $query_count->fetchAll();

      $response = array(
        "items" => $items,
        "pagination" => array(
          "more" => ($page * $records_per_page) < $total_count[0]['count']
        )
      );
      return json_encode($response);
    } catch (Exception $e) {
      $response = array(
        'status' => 'error',
        'message' => $e
      );
      return json_encode($response);
    }
  }

  public function jsonDepartment(){
    try {
      global $cleanWordPDO;
      $year = $cleanWordPDO->textCk(@$_POST["year"], true);
      $q = $cleanWordPDO->textCk(@$_POST["q"], false);
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;
      $sendArray = array(
        ':yearKpi' => $year,
        ':search' => '%'.$q.'%',
        ':offset' => $offset,
        ':recordsPerPage' => $records_per_page
      );

      $cek = "SELECT distinct deptkpi_id as id, deptkpi_name as text
      from kpi_divcorp
      where year_kpidivcorp = :yearKpi
      and deptkpi_name::text ilike :search";
      $cek_main = $cek . " order by deptkpi_name asc offset :offset limit :recordsPerPage";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_main, $sendArray);
      $items = $query_main->fetchAll();

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_count, array_filter($sendArray, function ($v, $k) {
        return in_array($k, [':search', ':yearKpi', ':companyKpi'], true);
      }, ARRAY_FILTER_USE_BOTH));
      $total_count = $query_count->fetchAll();

      $response = array(
        "items" => $items,
        "pagination" => array(
          "more" => ($page * $records_per_page) < $total_count[0]['count']
        )
      );
      return json_encode($response);
    } catch (Exception $e) {
      $response = array(
        'status' => 'error',
        'message' => $e
      );
      return json_encode($response);
    }
  }

  public function getKpiDivisionCorporate(){
    global $cleanWordPDO;
    try {

      $sendDataArray = array(
        ':year' => $cleanWordPDO->numberCk(@$_POST["year_kpi"], true, 'normal', null, "year_kpi"),
        ':department' => $cleanWordPDO->textCk(@$_POST["department_kpi"], true, 'normal', null, "company_kpi"),
      );
      $cek = "WITH kpi_divcorptarget AS (
        SELECT distinct id_kpidivcorp, sum(target_kpidivcorp) target_kpidivcorp
        from kpi_divcorp_target
        group by id_kpidivcorp
      )
      SELECT distinct a.*,
      g.target_kpidivcorp baseline_1, e.target_kpidivcorp, STRING_TO_ARRAY(a.index_kpidivcorp, '.')::INT[] arr_index,
      bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_divcorp a
      LEFT JOIN kpi_divcorp f
        on f.id_sobject = a.id_sobject
        and f.name_kpidivcorp = a.name_kpidivcorp
        and f.deptkpi_id = a.deptkpi_id
        and f.year_kpidivcorp = (a.year_kpidivcorp - 1)
      LEFT JOIN kpi_divcorptarget e on e.id_kpidivcorp = a.id_kpidivcorp
      LEFT JOIN kpi_divcorptarget g on g.id_kpidivcorp = f.id_kpidivcorp
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpidivcorp = :year
      and a.deptkpi_id = :department
      order by index_perspective asc, arr_index asc";
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, $sendDataArray);
      $main_data = $query->fetchAll();

      $cek_month = "SELECT distinct id_kpidivcorp, month_kpidivcorp as month_realisasi, value_kpidivcorp_real as realisasi, target_kpidivcorp as target
      from target_distinct_kpidivcorp
      where id_kpidivcorp IN (
        SELECT distinct id_kpidivcorp
        from kpi_divcorp
        where year_kpidivcorp = :year
        and deptkpi_id = :department
        and terbit_kpidivcorp = true
      )
      order by month_realisasi asc";
      $query_month = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_month, $sendDataArray);
      $data_month = $query_month->fetchAll();

      $cek_total_realisasi = "SELECT distinct id_kpidivcorp, sum(value_kpidivcorp_real) as total_realisasi
      from target_distinct_kpidivcorp
      where id_kpidivcorp IN (
        SELECT distinct id_kpidivcorp
        from kpi_divcorp
        where year_kpidivcorp = :year
        and deptkpi_id = :department
        and terbit_kpidivcorp = true
      )
      group by id_kpidivcorp";
      $query_total_realisasi = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_total_realisasi, $sendDataArray);
      $data_total_realisasi = $query_total_realisasi->fetchAll();

      foreach ($main_data as $key => $value) {
        $main_data[$key]['month'] = array_filter($data_month, function($filterVal) use ($value) {
          return $filterVal['id_kpidivcorp'] === $value['id_kpidivcorp'];
        });
        $arrayTotalRealisasi = array_column(array_filter($data_total_realisasi, function($filterVal) use ($value) {
          return $filterVal['id_kpicorp'] === $value['id_kpicorp'];
        }), 'total_realisasi');
        $main_data[$key]['totalRealisasi'] = $arrayTotalRealisasi[0] ?? null;
      }
      return json_encode($main_data);

    } catch (Exception $e) {
      $response = array(
        'status' => 'error',
        'message' => $e
      );
      return json_encode($response);
    }
  }

}
?>