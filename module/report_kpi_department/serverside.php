<?php
class kpi_department extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getKpiDepartement(){
    global $cleanWord;
    try {
      $with_sql = "kpi_depttarget AS (
        SELECT distinct id_kpidept, sum(target_kpidept) target_kpidept
        from kpi_department_target
        group by id_kpidept
      )
      ";
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');

      $cek = "SELECT distinct c.id_department, c.name_department
      from company_detail a
      inner join section_master b on b.id_section = a.id_section
      inner join department_master c on c.id_department = b.id_department
      where a.id_company = {$company_kpi}
      order by name_department asc";

      $result = $this->sendQuery($this->konek_sita_db(), $cek);

      $cekup_kpi = "WITH $with_sql SELECT DISTINCT STRING_TO_ARRAY(a.index_kpidept, '.')::INT[] arr_index, 
      a.name_kpidept, a.index_kpidept, a.define_kpidept,
      a.control_cek_kpidept, a.polaritas_kpidept,
      a.target_kpibunit, 
      a.compkpi_id, a.compkpi_name,
      a.id_perspective, a.name_perspective, a.alias_perspective,
      a.id_satuan, a.name_satuan,
      a.id_formula, a.name_formula,
      a.index_parent,
      a.text_sobject,
      a.text_perspective,
      CASE
          WHEN a.id_kpibunit IS NULL THEN a.id_kpidept
          ELSE a.id_kpibunit
      END AS id_kpibunit";
      $join = '';
      $i = 1;
      while ($row = pg_fetch_assoc($result)) {
          $dept_id = $row['id_department'];
          $cekup_kpi .= ", b$i.cascade_kpidept AS ck_$i, c$i.target_kpidept AS tk_$i";
          $join .= " LEFT JOIN kpi_department b$i ON b$i.deptkpi_id = '$dept_id' AND (b$i.id_kpibunit = a.id_kpibunit OR b$i.id_kpidept = a.id_kpidept) and b$i.terbit_kpidept is true";
          $join .= " LEFT JOIN kpi_depttarget c$i ON c$i.id_kpidept = b$i.id_kpibunit OR c$i.id_kpidept = b$i.id_kpidept";
          $i++;
      }
      $cekup_kpi .= " FROM kpi_department a
         $join
         WHERE a.year_kpidept = $year_kpi
         AND a.compkpi_id = {$company_kpi}
         ORDER BY a.alias_perspective ASC, STRING_TO_ARRAY(a.index_kpidept, '.')::INT[] ASC";
      // echo $cekup_kpi;
      // die;
      $query_kpi = $this->sendQuery($this->konek_sita_db(), $cekup_kpi);
      $response_kpi = pg_fetch_all($query_kpi);
      
      if (empty($response_kpi)) {
        $cek = "WITH $with_sql SELECT distinct STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] arr_index,
        a.id_kpibunit,
        a.name_kpibunit AS name_kpidept, a.index_kpibunit AS index_kpidept, a.define_kpibunit AS define_kpidept,
        a.control_cek_kpibunit AS control_cek_kpidept, a.polaritas_kpibunit AS polaritas_kpidept,
        a.target_kpibunit, 
        a.compkpi_id, a.compkpi_name,
        a.id_perspective, a.name_perspective, a.alias_perspective,
        a.id_satuan, a.name_satuan,
        a.id_formula, a.name_formula,
        a.index_parent,
        a.text_sobject,
        a.text_perspective
        from kpi_bisnis_unit a
        where a.year_kpibunit = $year_kpi and a.compkpi_id = {$company_kpi} and a.terbit_kpibunit is true
        order by a.alias_perspective asc, STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] asc";
        $query = $this->sendQuery($this->konek_sita_db(), $cek);
        $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
        foreach ($response as $key => $value) {
          $response[$key]['target_month'] = [];
        }
        return json_encode($response);
      } else {
        return json_encode($response_kpi);
      }
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function getHeadDepartement(){
    global $cleanWord;
    try {
      $id_company = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');
      $cek = "SELECT distinct c.id_department, c.name_department, d.id_company, d.name_company
      from company_detail a
      inner join section_master b on b.id_section = a.id_section
      inner join department_master c on c.id_department = b.id_department
      inner join company_master d on d.id_company = a.id_company
      where a.id_company = {$id_company}
      order by name_department asc";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      return json_encode($response);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  private function newArrayForTarget($valueTarget, $text_target_sql, $month_target_sql) {
    $newArray = array(
      'target' => $valueTarget[$text_target_sql],
      'month' => $valueTarget[$month_target_sql]
    );
    return $newArray;
  }

  public function getDataDepartement(){
    global $cleanWord;
    try {
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');
      $department_kpi = $cleanWord->textCk(@$_POST["department_kpi"], false, 'normal');
      
      $with_sql = "kpi_depttarget AS (
        SELECT distinct id_kpidept, sum(target_kpidept) target_kpidept
        from kpi_department_target
        group by id_kpidept
      ), kpi_casecade AS (
        SELECT id_kpidept, cascade_kpidept from kpi_department 
      )";
      $cek = "WITH $with_sql SELECT a.*, b.target_kpidept, c.cascade_kpidept from kpi_realization_dept a
      LEFT JOIN kpi_depttarget b on b.id_kpidept = a.id_realization
      LEFT JOIN kpi_casecade c on c.id_kpidept = a.id_realization
      where year_kpi_realization = $year_kpi and deptkpi_id = {$department_kpi} 
      and compkpi_id = {$company_kpi} and (status_kpi = 'kpi_department_corps' or status_kpi = 'kpi_department_support') 
      order by id_realization asc";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = pg_fetch_all($query);
      if (empty($response)) {
        return json_encode(
          array(
            'response'=>'error',
            'alert'=>'Data kosong, hubungi QMS! ❌'
          )
        );
      } else {
        $cek_target = "SELECT * from kpi_department_target
        where id_kpidept in (select id_kpidept from kpi_department where year_kpidept = $year_kpi and compkpi_id = {$company_kpi} and deptkpi_id = {$department_kpi})";
        $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
        $response_target = pg_fetch_all($query_target);
        if (empty($response_target)) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Data target kosong, hubungi QMS! ❌'
            )
          );
        } else {
          foreach ($response as $key => $value) {
            $response[$key]['target_kpi'] = [];
            foreach ($response_target as $keyTarget => $valueTarget) {
              if ($value['id_realization'] == $valueTarget['id_kpidept']) {
                array_push($response[$key]['target_kpi'], $this->newArrayForTarget($valueTarget, 'target_kpidept', 'month_kpidept'));
              }
            }
          }
        }
      }
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