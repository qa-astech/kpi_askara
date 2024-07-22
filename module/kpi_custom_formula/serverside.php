<?php
class kpi_realization_dept extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function selectKpi(){
    global $cleanWord;
    try {

      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
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
          where id_kpidept in (select id_kpidept from kpi_department where year_kpidept = $year_kpi)";
          $query_target1 = $this->sendQuery($this->konek_sita_db(), $cek_target1);
          $response_target1 = pg_fetch_all($query_target1);
        }
        if (!empty($response_divcorp)) {
          $cek_target2 = "SELECT * from kpi_divcorp_target
          where id_kpidivcorp in (select id_kpidivcorp from kpi_divcorp where year_kpidivcorp = $year_kpi)";
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
        'target' => $valueTarget['target_kpidept'],
        'realisasi' => $valueTarget['realisasi'],
        'fileRealisasi' => !empty($valueTarget['file_realisasi']) && strval($valueTarget['file_realisasi']) !== "{}" ? base64_encode($valueTarget['file_realisasi']) : '',
        'entryRealisasi' => $valueTarget['entry_realisasi'],
        'fullnameRealisasi' => $valueTarget['fullname_realisasi'],
        'timeRealisasi' => $valueTarget['time_realisasi'],
        'month' => $valueTarget['month_kpidept'],
        'idTargetMonth' => $valueTarget['id_kpidept_target'],
        'idRealisasi' => $valueTarget['id_tbl_realisasi'],
        'lockStatus' => $valueTarget['lock_status'],
        'remarks' => $valueTarget['remarks'],
        'typeTarget' => $type_target
      );
    } elseif ($type_target == "KPI Division Korporat") {
      $newArray = array(
        'target' => $valueTarget['target_kpidivcorp'],
        'realisasi' => $valueTarget['realisasi'],
        'fileRealisasi' => !empty($valueTarget['file_realisasi']) && strval($valueTarget['file_realisasi']) !== "{}" ? base64_encode($valueTarget['file_realisasi']) : '',
        'entryRealisasi' => $valueTarget['entry_realisasi'],
        'fullnameRealisasi' => $valueTarget['fullname_realisasi'],
        'timeRealisasi' => $valueTarget['time_realisasi'],
        'month' => $valueTarget['month_kpidivcorp'],
        'idTargetMonth' => $valueTarget['id_kpidivcorp_target'],
        'idRealisasi' => $valueTarget['id_tbl_realisasi'],
        'lockStatus' => $valueTarget['lock_status'],
        'remarks' => $valueTarget['remarks'],
        'typeTarget' => $type_target
      );
    }
    return $newArray;
  }

  public function getKpi(){
    global $cleanWordPDO;
    try {

      $year_kpi = $cleanWordPDO->numberCk(@$_POST["year_kpi"], true, 'integer');
      $indexKpi = $cleanWordPDO->numberCk(@$_POST["statusKpi"], true, 'integer');
      $typeKPI = $cleanWordPDO->textCk(@$_POST["typeKPI"][$indexKpi], true, 'trim');
      $companyKpi = $cleanWordPDO->textCk(@$_POST["companyKpi"][$indexKpi], $typeKPI == "KPI Department", 'normal');
      $departmentKpi = $cleanWordPDO->textCk(@$_POST["departmentKpi"][$indexKpi], true, 'normal');

      $sendArray = array(
        ':yearKpi' => $year_kpi,
        ':departmentKpi' => $departmentKpi,
        ':companyKpi' => $companyKpi
      );
      
      $sendArrayMain = array();
      $cek = "SELECT * from kpi_realization_dept where year_kpi_realization = :yearKpi and deptkpi_id = :departmentKpi ";
      if ($typeKPI === "KPI Department") {
        $cek .= "and compkpi_id = :companyKpi and (status_kpi = 'kpi_department_corps' or status_kpi = 'kpi_department_support') ";
        $sendArrayMain = $sendArray;
      } elseif ($typeKPI === "KPI Division Korporat") {
        $cek .= "and (status_kpi = 'kpi_divcorp_corps' or status_kpi = 'kpi_divcorp_support') ";
        $sendArrayMain = array_filter($sendArray, function ($v, $k) {
          return !in_array($k, [':companyKpi'], true);
        }, ARRAY_FILTER_USE_BOTH);
      }
      $cek .= "ORDER BY index_perspective, STRING_TO_ARRAY(index_kpi_realization, '.')::INT[] asc";
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, $sendArrayMain);
      $response = $query->fetchAll();
      if (empty($response)) {
        return json_encode(
          array(
            'response'=>'error',
            'alert'=>'Data kosong, hubungi QMS! ❌'
          )
        );
      } else {
        $response = $cleanWordPDO->cleaningArrayHtml($response);
        if ($typeKPI == "KPI Department") {
          $cek_target = "SELECT a.*,
          b.value_kpidept_real realisasi, b.file_kpidept_real file_realisasi, b.id_kpidept_real id_tbl_realisasi, b.remarks,
          b.user_entry entry_realisasi, b.last_update time_realisasi, d.fullname_users fullname_realisasi
          FROM kpi_department_target a
          LEFT JOIN kpi_department_realization b on b.id_kpidept_target = a.id_kpidept_target
          LEFT JOIN all_users_setup c on c.id_usersetup = b.user_entry
          LEFT JOIN users d on d.nik_users = c.nik
          where a.id_kpidept in (
            select id_kpidept
            from kpi_department
            where year_kpidept = :yearKpi
            and compkpi_id = :companyKpi
            and deptkpi_id = :departmentKpi
          )";
          $query_target = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_target, $sendArray);
          $response_target = $query_target->fetchAll();

        } elseif ($typeKPI == "KPI Division Korporat") {
          $cek_target = "SELECT a.*,
          b.value_kpidivcorp_real realisasi, b.file_kpidivcorp_real file_realisasi, b.id_kpidivcorp_real id_tbl_realisasi, b.remarks,
          b.user_entry entry_realisasi, b.last_update time_realisasi, d.fullname_users fullname_realisasi
          FROM kpi_divcorp_target a
          LEFT JOIN kpi_divcorp_realization b on b.id_kpidivcorp_target = a.id_kpidivcorp_target
          LEFT JOIN all_users_setup c on c.id_usersetup = b.user_entry
          LEFT JOIN users d on d.nik_users = c.nik
          where a.id_kpidivcorp in (
            SELECT id_kpidivcorp
            FROM kpi_divcorp
            WHERE year_kpidivcorp = :yearKpi
            and deptkpi_id = :departmentKpi
          )";
          $query_target = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_target, array_filter($sendArray, function ($v, $k) {
            return !in_array($k, [':companyKpi'], true);
          }, ARRAY_FILTER_USE_BOTH));
          $response_target = $query_target->fetchAll();

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
              if ($typeKPI == "KPI Department") {
                if ($value['id_realization'] == $valueTarget['id_kpidept']) {
                  array_push($response[$key]['target_kpi'], $this->newArrayForTarget($valueTarget, $typeKPI));
                }
              } elseif ($typeKPI == "KPI Division Korporat") {
                if ($value['id_realization'] == $valueTarget['id_kpidivcorp']) {
                  array_push($response[$key]['target_kpi'], $this->newArrayForTarget($valueTarget, $typeKPI));
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

}
?>