<?php
class kpi_bisnis_unit extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getKpiDivisi(){
    global $cleanWord;
    try {
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');

      $cek = "SELECT * from department_master
      order by name_department asc";

      $result = $this->sendQuery($this->konek_sita_db(), $cek);
      
      $with_sql = "kpi_divcorptarget AS (
        SELECT distinct id_kpidivcorp, sum(target_kpidivcorp) target_kpidivcorp
        from kpi_divcorp_target
        group by id_kpidivcorp
      )
      ";

      $cekup_kpi = "WITH $with_sql SELECT DISTINCT STRING_TO_ARRAY(a.index_kpidivcorp, '.')::INT[] arr_index, 
      a.name_kpidivcorp, a.index_kpidivcorp, a.define_kpidivcorp,
      a.control_cek_kpidivcorp, a.polaritas_kpidivcorp,
      a.target_kpicorp, 
      a.deptkpi_id, a.deptkpi_name,
      a.id_perspective, a.name_perspective, a.alias_perspective,
      a.id_satuan, a.name_satuan,
      a.id_formula, a.name_formula,
      a.index_parent,
      a.text_sobject,
      a.text_perspective,
      CASE
          WHEN a.id_kpicorp IS NULL THEN a.id_kpidivcorp
          ELSE a.id_kpicorp
      END AS id_kpicorp";
      $join = '';
      $i = 1;
      while ($row = pg_fetch_assoc($result)) {
          $dept_id = $row['id_department'];
          $cekup_kpi .= ", b$i.cascade_kpidivcorp AS ck_$i, c$i.target_kpidivcorp AS tk_$i";
          $join .= " LEFT JOIN kpi_divcorp b$i ON b$i.deptkpi_id = '$dept_id' AND b$i.id_kpidivcorp = a.id_kpidivcorp";
          $join .= " LEFT JOIN kpi_divcorptarget c$i ON c$i.id_kpidivcorp = b$i.id_kpidivcorp";
          $i++;
      }
      $cekup_kpi .= " FROM kpi_divcorp a
         $join
         WHERE a.year_kpidivcorp = $year_kpi
         ORDER BY a.alias_perspective ASC, STRING_TO_ARRAY(a.index_kpidivcorp, '.')::INT[] ASC";
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

  public function getHeadDivisi(){
    try {
      $cek = "SELECT * from department_master
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

  public function getDataDivisi(){
    global $cleanWord;
    try {
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $department_kpi = $cleanWord->textCk(@$_POST["department_kpi"], false, 'normal');
      
      $with_sql = "kpi_divcorptarget AS (
        SELECT distinct id_kpidivcorp, sum(target_kpidivcorp) target_kpidivcorp
        from kpi_divcorp_target
        group by id_kpidivcorp
      ), kpi_casecade AS (
        SELECT id_kpidivcorp, cascade_kpidivcorp from kpi_divcorp 
      )";
      $cek = "WITH $with_sql SELECT a.*, b.target_kpidivcorp, c.cascade_kpidivcorp from kpi_realization_dept a
      LEFT JOIN kpi_divcorptarget b on b.id_kpidivcorp = a.id_realization
      LEFT JOIN kpi_casecade c on c.id_kpidivcorp = a.id_realization
      where year_kpi_realization = $year_kpi and deptkpi_id = {$department_kpi} and (status_kpi = 'kpi_divcorp_corps' or status_kpi = 'kpi_divcorp_support')
      order by id_realization asc";
      // echo $cek;
      // die;
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
        $cek_target = "SELECT * from kpi_divcorp_target
        where id_kpidivcorp in (select id_kpidivcorp from kpi_divcorp where year_kpidivcorp = $year_kpi and deptkpi_id = {$department_kpi})";
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
          $cek_real = "SELECT * from kpi_divcorp_realization
          where id_kpidivcorp_target in (select id_kpidivcorp_target from kpi_divcorp_target
          where id_kpidivcorp in (select id_kpidivcorp from kpi_divcorp where year_kpidivcorp = $year_kpi and deptkpi_id = {$department_kpi}))";
          $query_real = $this->sendQuery($this->konek_sita_db(), $cek_real);
          $response_real = pg_fetch_all($query_real);
          if (empty($response_real)) {
            return json_encode(
              array(
                'response'=>'error',
                'alert'=>'Data realisasi kosong, hubungi QMS! ❌'
              )
            );
          } else {
            foreach ($response as $key => $value) {
              $response[$key]['target_kpi'] = [];
              foreach ($response_target as $keyTarget => $valueTarget) {
                if ($value['id_realization'] == $valueTarget['id_kpidivcorp']) {
                  array_push($response[$key]['target_kpi'], $this->newArrayForTarget($valueTarget, 'target_kpidivcorp', 'month_kpidivcorp'));
                  foreach ($response_real as $keyReal => $valueReal) {
                    if ($valueTarget['id_kpidivcorp_target'] == $valueReal['id_kpidivcorp_target']) {
                      array_push($response[$key]['target_kpi'], $this->newArrayForReal($valueReal, 'value_kpidivcorp_real', 'file_kpidivcorp_real'));
                    }
                  }
                }
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