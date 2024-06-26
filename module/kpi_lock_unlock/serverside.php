<?php
class kpi_lock_unlock extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function selectKpiLockUnlock(){
    global $cleanWord;
    try {

      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $monthFrom_kpi = $cleanWord->numberCk(@$_POST["monthFrom_kpi"], true, 'integer');
      $monthTo_kpi = $cleanWord->numberCk(@$_POST["monthTo_kpi"], true, 'integer');

      $cek = "SELECT distinct compkpi_id, compkpi_name, deptkpi_id, deptkpi_name,
      CASE
        WHEN status_kpi = 'kpi_department_support' or status_kpi = 'kpi_department_corps' then 'KPI Department'::varchar
        WHEN status_kpi = 'kpi_divcorp_support' or status_kpi = 'kpi_divcorp_corps' then 'KPI Division Korporat'::varchar
      END status_kpi
      from kpi_realization_dept where year_kpi_realization = $year_kpi ";
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
        $response_dept = array_filter($response, function($value) {
          return $value['status_kpi'] == "KPI Department";
        });
        $response_divcorp = array_filter($response, function($value) {
          return $value['status_kpi'] == "KPI Division Korporat";
        });
        $response_target1 = array();
        $response_target2 = array();
        if (!empty($response_dept)) {
          $cek_target1 = "SELECT * from kpi_department_target
          where id_kpidept in (select id_kpidept from kpi_department where year_kpidept = $year_kpi)
          and month_kpidept >= $monthFrom_kpi and month_kpidept <= $monthTo_kpi";
          $query_target1 = $this->sendQuery($this->konek_sita_db(), $cek_target1);
          $response_target1 = pg_fetch_all($query_target1);
        }
        if (!empty($response_divcorp)) {
          $cek_target2 = "SELECT * from kpi_divcorp_target
          where id_kpidivcorp in (select id_kpidivcorp from kpi_divcorp where year_kpidivcorp = $year_kpi)
          and month_kpidivcorp >= $monthFrom_kpi and month_kpidivcorp <= $monthTo_kpi";
          $query_target2 = $this->sendQuery($this->konek_sita_db(), $cek_target2);
          $response_target2 = pg_fetch_all($query_target2);
        }
        $merge_target = array_merge($response_target1, $response_target2);
        
        if (empty($merge_target)) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Data target kosong, hubungi QMS! ❌'
            )
          );
        }
      }
      return json_encode($response);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  private function newArrayForTarget($valueTarget, $type_target) {
    if ($type_target == "KPI Department") {
      $newArray = array(
        'month' => $valueTarget['month_kpidept'],
        'tblTarget' => $valueTarget['tbl_target'],
        'lockStatus' => $valueTarget['lock_status'],
        'idTargetMonth' => $valueTarget['id_kpidept_target']
      );
    } elseif ($type_target == "KPI Division Korporat") {
      $newArray = array(
        'month' => $valueTarget['month_kpidivcorp'],
        'tblTarget' => $valueTarget['tbl_target'],
        'lockStatus' => $valueTarget['lock_status'],
        'idTargetMonth' => $valueTarget['id_kpidivcorp_target']
      );
    }
    return $newArray;
  }

  public function getKpiLockUnlock(){
    global $cleanWord;
    try {

      // print_r($_POST);
      // die();

      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $monthFrom_kpi = $cleanWord->numberCk(@$_POST["monthFrom_kpi"], true, 'integer');
      $monthTo_kpi = $cleanWord->numberCk(@$_POST["monthTo_kpi"], true, 'integer');
      $indexKpi = $cleanWord->numberCk(@$_POST["statusKpi"], true, 'integer');

      $typeKPI = $cleanWord->textCk(@$_POST["typeKPI"][$indexKpi], true, 'trim');
      $companyKpi = $cleanWord->textCk(@$_POST["companyKpi"][$indexKpi], $typeKPI == "KPI Department", 'normal');
      $departmentKpi = $cleanWord->textCk(@$_POST["departmentKpi"][$indexKpi], true, 'normal');

      $cek = "SELECT a.*, cc.fullname_users fullname_entry, cc.nik_users nik_entry
      FROM kpi_realization_dept a
      LEFT JOIN all_users_setup bb on bb.id_usersetup = a.user_entry
      LEFT JOIN users cc on cc.nik_users = bb.nik
      where a.year_kpi_realization = $year_kpi and a.deptkpi_id = {$departmentKpi} ";
      $cek .= $typeKPI == "KPI Department" ? "and a.compkpi_id = {$companyKpi} and (a.status_kpi = 'kpi_department_corps' or a.status_kpi = 'kpi_department_support') " : "";
      $cek .= $typeKPI == "KPI Division Korporat" ? "and (a.status_kpi = 'kpi_divcorp_corps' or a.status_kpi = 'kpi_divcorp_support') " : "";
      $cek .= "ORDER BY a.index_perspective, STRING_TO_ARRAY(a.index_kpi_realization, '.')::INT[] asc";
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
        $response = $cleanWord->cleaningArrayHtml($response);
        if ($typeKPI == "KPI Department") {
          $cek_target = "SELECT a.id_kpidept_target, a.id_kpidept, a.month_kpidept, 'kpi_department_target'::varchar tbl_target, a.lock_status
          FROM kpi_department_target a
          LEFT JOIN kpi_department_realization b on b.id_kpidept_target = a.id_kpidept_target
          LEFT JOIN all_users_setup c on c.id_usersetup = b.user_entry
          LEFT JOIN users d on d.nik_users = c.nik
          where a.id_kpidept in (
            select id_kpidept
            from kpi_department
            where year_kpidept = $year_kpi
            and compkpi_id = {$companyKpi}
            and deptkpi_id = {$departmentKpi}
          )
          and a.month_kpidept >= $monthFrom_kpi and a.month_kpidept <= $monthTo_kpi";
          $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
          $response_target = pg_fetch_all($query_target);

        } elseif ($typeKPI == "KPI Division Korporat") {
          $cek_target = "SELECT a.id_kpidivcorp_target, a.id_kpidivcorp, a.month_kpidivcorp, 'kpi_divcorp_target'::varchar tbl_target, a.lock_status
          FROM kpi_divcorp_target a
          LEFT JOIN kpi_divcorp_realization b on b.id_kpidivcorp_target = a.id_kpidivcorp_target
          LEFT JOIN all_users_setup c on c.id_usersetup = b.user_entry
          LEFT JOIN users d on d.nik_users = c.nik
          where a.id_kpidivcorp in (
            SELECT id_kpidivcorp
            FROM kpi_divcorp
            WHERE year_kpidivcorp = $year_kpi
            and deptkpi_id = {$departmentKpi}
          )
          and a.month_kpidivcorp >= $monthFrom_kpi and a.month_kpidivcorp <= $monthTo_kpi";
          $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
          $response_target = pg_fetch_all($query_target);
        }

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
              // echo $typeKPI . "\n";
              // print_r($valueTarget);
              if (
                ($typeKPI == "KPI Department" && $value['id_realization'] == $valueTarget['id_kpidept']) ||
                ($typeKPI == "KPI Division Korporat" && $value['id_realization'] == $valueTarget['id_kpidivcorp'])
              ) {
                array_push($response[$key]['target_kpi'], $this->newArrayForTarget($valueTarget, $typeKPI));
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

  public function sendKpiLockUnlock() {
    global $cleanWord;
    $code_maccess = $cleanWord->textCk(@$_POST["code_maccess"], true, 'normal');
    $check_menu = $cleanWord->textCk(@$_POST["check_menu"], true, 'trim');
    $target_table = $cleanWord->textCk(@$_POST["target_table"], true, 'trim');

    $idOfTable = [];
    $idOfTable['kpi_department_target'] = 'id_kpidept_target';
    $idOfTable['kpi_divcorp_target'] = 'id_kpidivcorp_target';
    try {
      $sql_update = "UPDATE $target_table SET lock_status = $check_menu WHERE $idOfTable[$target_table] = $code_maccess;";
      $this->sendQuery($this->konek_sita_db(), $sql_update);
      return json_encode(
        array('response'=>'success')
      );
    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'statBefore'=> $check_menu == 'true' ? false : true
        )
      );
    }
  }

  public function updateLockCutOff() {
    $month = $this->monthNumber;
    $day = $this->day;
    $year = $this->year;
    // $monthBefore = intval($this->monthNumber) - 1;
    $sql_update = "UPDATE kpi_department_target SET lock_status = true where id_kpidept_target in (  
      select distinct b.id_kpidept_target
      from kpi_realization_dept a
      left join kpi_department_target b on b.id_kpidept = a.id_realization
      where a.year_kpi_realization = $year
      and b.month_kpidept = $month
      and a.date_cutoff = $day
    );
    UPDATE kpi_divcorp_target SET lock_status = true where id_kpidivcorp_target in (
      select distinct b.id_kpidivcorp_target
      from kpi_realization_dept a
      left join kpi_divcorp_target b on b.id_kpidivcorp = a.id_realization
      where a.year_kpi_realization = $year
      and b.month_kpidivcorp = $month
      and a.date_cutoff = $day
    );";
    // and (b.month_kpidept::varchar || a.date_cutoff::varchar)::int <= ('$month'::varchar || '$day'::varchar)::int
    // and (b.month_kpidept::varchar || a.date_cutoff::varchar)::int <= ('$month'::varchar || '$day'::varchar)::int
    try {
      $this->sendQuery($this->konek_sita_db(), $sql_update);
      return json_encode(
        array(
          'response'=>'success',
          'msg'=>'lock sukses!'
        )
      );
    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'msg'=>'lock gagal!'
        )
      );
    }
  }

}
?>