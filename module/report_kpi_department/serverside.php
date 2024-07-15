<?php
class report_kpi_department extends database {

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

      $cek = "SELECT distinct year_kpidept as id, year_kpidept as text from kpi_department where year_kpidept::text ilike :search";
      $cek_main = $cek . " order by year_kpidept asc offset :offset limit :recordsPerPage";
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

  public function jsonCompany(){
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

      $cek = "SELECT distinct compkpi_id as id, compkpi_name as text
      from kpi_department
      where year_kpidept = :yearKpi
      and compkpi_name::text ilike :search";
      $cek_main = $cek . " order by compkpi_name asc offset :offset limit :recordsPerPage";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_main, $sendArray);
      $items = $query_main->fetchAll();

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_count, array_filter($sendArray, function ($v, $k) {
        return in_array($k, [':search', ':yearKpi'], true);
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
      $company = $cleanWordPDO->textCk(@$_POST["company"], true);
      $q = $cleanWordPDO->textCk(@$_POST["q"], false);
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;
      $sendArray = array(
        ':yearKpi' => $year,
        ':companyKpi' => $company,
        ':search' => '%'.$q.'%',
        ':offset' => $offset,
        ':recordsPerPage' => $records_per_page
      );

      $cek = "SELECT distinct deptkpi_id as id, deptkpi_name as text
      from kpi_department
      where year_kpidept = :yearKpi
      and compkpi_id = :companyKpi
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

  public function getKpiDepartment(){
    global $cleanWordPDO;
    try {

      $sendDataArray = array(
        ':year' => $cleanWordPDO->numberCk(@$_POST["year_kpi"], true, 'normal', null, "year_kpi"),
        ':department' => $cleanWordPDO->textCk(@$_POST["department_kpi"], true, 'normal', null, "company_kpi"),
        ':company' => $cleanWordPDO->textCk(@$_POST["company_kpi"], true, 'normal', null, "company_kpi")
      );
      $cek = "WITH kpi_depttarget AS (
        SELECT distinct id_kpidept, sum(target_kpidept) target_kpidept
        from kpi_department_target
        group by id_kpidept
      )
      SELECT distinct a.*,
      g.target_kpidept baseline_1, e.target_kpidept, STRING_TO_ARRAY(a.index_kpidept, '.')::INT[] arr_index,
      bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_department a
      left join kpi_department f
        on f.id_sobject = a.id_sobject
        and f.name_kpidept = a.name_kpidept
        and f.compkpi_id = a.compkpi_id
        and f.deptkpi_id = a.deptkpi_id
        and f.year_kpidept = (a.year_kpidept - 1)
      left join kpi_depttarget g on g.id_kpidept = f.id_kpidept
      left join kpi_depttarget e on e.id_kpidept = a.id_kpidept
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpidept = :year
      and a.compkpi_id = :company
      and a.deptkpi_id = :department
      order by index_perspective asc, arr_index asc";
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, $sendDataArray);
      $main_data = $query->fetchAll();

      $cek_month = "SELECT distinct id_kpidept, month_kpidept as month_realisasi, value_kpidept_real as realisasi, target_kpidept as target
      from target_distinct_kpibunit
      where id_kpidept IN (
        SELECT distinct id_kpidept
        from kpi_department
        where year_kpidept = :year
        and compkpi_id = :company
        and deptkpi_id = :department
        and terbit_kpidept = true
      )
      order by month_realisasi asc";
      $query_month = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_month, $sendDataArray);
      $data_month = $query_month->fetchAll();

      $cek_total_realisasi = "SELECT distinct id_kpidept, sum(value_kpidept_real) as total_realisasi
      from target_distinct_kpibunit
      where id_kpidept IN (
        SELECT distinct id_kpidept
        from kpi_department
        where year_kpidept = :year
        and compkpi_id = :company
        and deptkpi_id = :department
        and terbit_kpidept = true
      )
      group by id_kpidept";
      $query_total_realisasi = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_total_realisasi, $sendDataArray);
      $data_total_realisasi = $query_total_realisasi->fetchAll();

      foreach ($main_data as $key => $value) {
        $main_data[$key]['month'] = array_filter($data_month, function($filterVal) use ($value) {
          return $filterVal['id_kpidept'] === $value['id_kpidept'];
        });
        // $main_data[$key]['totalRealisasi'] = array_column(array_filter($data_total_realisasi, function($filterVal) use ($value) {
        //   return $filterVal['id_kpidept'] === $value['id_kpidept'];
        // }), 'total_realisasi')[0];
        $arrayTotalRealisasi = array_column(array_filter($data_total_realisasi, function($filterVal) use ($value) {
          return $filterVal['id_kpidept'] === $value['id_kpidept'];
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