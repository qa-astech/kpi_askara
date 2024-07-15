<?php
class kpi_bisnis_unit extends database {

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

      $cek = "SELECT distinct year_kpibunit as id, year_kpibunit as text from kpi_bisnis_unit where year_kpibunit::text ilike :search";
      $cek_main = $cek . " order by year_kpibunit asc offset :offset limit :recordsPerPage";
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

  public function jsonCompany(){
    try {
      global $cleanWordPDO;
      $year = $cleanWordPDO->textCk(@$_POST["year"], true);
      $q = $cleanWordPDO->textCk(@$_POST["q"], false);
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct compkpi_id as id, compkpi_name as text
      from kpi_bisnis_unit
      where year_kpibunit = :yearKpi
      and compkpi_name::text ilike :search";
      $cek_main = $cek . " order by compkpi_name asc offset :offset limit :recordsPerPage";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_main, array(
        ':yearKpi' => $year,
        ':search' => '%'.$q.'%',
        ':offset' => $offset,
        ':recordsPerPage' => $records_per_page
      ));
      $items = $query_main->fetchAll();


      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_count, array(
        ':yearKpi' => $year,
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

  public function getKpiBisnisUnit(){
    global $cleanWordPDO;
    try {
      $company_kpibunit = $cleanWordPDO->textCk(@$_POST["company_kpi"], true, 'normal', null, "company_kpi");
      $year_kpibunit = $cleanWordPDO->numberCk(@$_POST["year_kpi"], true, 'normal', null, "year_kpi");

      $cek = "SELECT distinct a.*,
      f.target_kpibunit baseline_1, STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] arr_index,
      bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_bisnis_unit a
      left join kpi_bisnis_unit f
        on f.id_sobject = a.id_sobject
        and f.name_kpibunit = a.name_kpibunit
        and f.compkpi_id = a.compkpi_id
        and f.year_kpibunit = (a.year_kpibunit - 1)
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpibunit = :year
      and a.compkpi_id = :company
      and a.terbit_kpibunit = true
      order by a.index_perspective asc, STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] asc";
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, array(
        ':year' => $year_kpibunit,
        ':company' => $company_kpibunit
      ));
      $main_data = $query->fetchAll();

      $cek_month = "SELECT distinct id_kpibunit, month_kpidept as month_realisasi, sum(value_kpidept_real) as realisasi
      from target_distinct_kpibunit
      where id_kpibunit IN (
        SELECT distinct id_kpibunit
        from kpi_bisnis_unit
        where year_kpibunit = :year
        and compkpi_id = :company
        and terbit_kpibunit = true
      )
      group by id_kpibunit, month_kpidept
      order by month_realisasi asc";
      $query_month = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_month, array(
        ':year' => $year_kpibunit,
        ':company' => $company_kpibunit
      ));
      $data_month = $query_month->fetchAll();

      $cek_year = "SELECT distinct id_kpibunit, deptkpi_id, deptkpi_name, sum(target_kpidept) as target_department, sum(value_kpidept_real) as realisasi
      from target_distinct_kpibunit
      where id_kpibunit IN (
        SELECT distinct id_kpibunit
        from kpi_bisnis_unit
        where year_kpibunit = :year
        and compkpi_id = :company
        and terbit_kpibunit = true
      )
      group by id_kpibunit, deptkpi_id, deptkpi_name, target_kpibunit";
      $query_year = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_year, array(
        ':year' => $year_kpibunit,
        ':company' => $company_kpibunit
      ));
      $data_year = $query_year->fetchAll();

      $cek_total_realisasi = "SELECT distinct id_kpibunit, sum(value_kpidept_real) as total_realisasi
      from target_distinct_kpibunit
      where id_kpibunit IN (
        SELECT distinct id_kpibunit
        from kpi_bisnis_unit
        where year_kpibunit = :year
        and compkpi_id = :company
        and terbit_kpibunit = true
      )
      group by id_kpibunit";
      $query_total_realisasi = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_total_realisasi, array(
        ':year' => $year_kpibunit,
        ':company' => $company_kpibunit
      ));
      $data_total_realisasi = $query_total_realisasi->fetchAll();

      foreach ($main_data as $key => $value) {
        $main_data[$key]['month'] = array_filter($data_month, function($filterVal) use ($value) {
          return $filterVal['id_kpibunit'] === $value['id_kpibunit'];
        });
        $main_data[$key]['year'] = array_filter($data_year, function($filterVal) use ($value) {
          return $filterVal['id_kpibunit'] === $value['id_kpibunit'];
        });
        // $main_data[$key]['totalRealisasi'] = array_column(array_filter($data_total_realisasi, function($filterVal) use ($value) {
        //   return $filterVal['id_kpibunit'] === $value['id_kpibunit'];
        // }), 'total_realisasi')[0];
        $arrayTotalRealisasi = array_column(array_filter($data_total_realisasi, function($filterVal) use ($value) {
          return $filterVal['id_kpibunit'] === $value['id_kpibunit'];
        }), 'total_realisasi');
        $main_data[$key]['totalRealisasi'] = $arrayTotalRealisasi[0] ?? null;
      }
      return json_encode($main_data);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

}
?>