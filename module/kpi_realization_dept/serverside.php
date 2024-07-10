<?php
class kpi_realization_dept extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function selectKpiRealization(){
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
      from kpi_realization_dept where year_kpi_realization = $year_kpi and data_avail_id_usersetup = '$_SESSION[setupuser_kpi_askara]' ";
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
          where id_kpidept in (select id_kpidept from kpi_department where year_kpidept = $year_kpi and data_avail_id_usersetup = '$_SESSION[setupuser_kpi_askara]')
          and month_kpidept >= $monthFrom_kpi and month_kpidept <= $monthTo_kpi";
          $query_target1 = $this->sendQuery($this->konek_sita_db(), $cek_target1);
          $response_target1 = pg_fetch_all($query_target1);
        }
        if (!empty($response_divcorp)) {
          $cek_target2 = "SELECT * from kpi_divcorp_target
          where id_kpidivcorp in (select id_kpidivcorp from kpi_divcorp where year_kpidivcorp = $year_kpi and data_avail_id_usersetup = '$_SESSION[setupuser_kpi_askara]')
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
        'typeTarget' => $type_target
      );
    }
    return $newArray;
  }

  public function getKpiRealization(){
    global $cleanWordPDO;
    try {

      $year_kpi = $cleanWordPDO->numberCk(@$_POST["year_kpi"], true, 'integer');
      $monthFrom_kpi = $cleanWordPDO->numberCk(@$_POST["monthFrom_kpi"], true, 'integer');
      $monthTo_kpi = $cleanWordPDO->numberCk(@$_POST["monthTo_kpi"], true, 'integer');
      $indexKpi = $cleanWordPDO->numberCk(@$_POST["statusKpi"], true, 'integer');
      $typeKPI = $cleanWordPDO->textCk(@$_POST["typeKPI"][$indexKpi], true, 'trim');
      $companyKpi = $cleanWordPDO->textCk(@$_POST["companyKpi"][$indexKpi], $typeKPI == "KPI Department", 'normal');
      $departmentKpi = $cleanWordPDO->textCk(@$_POST["departmentKpi"][$indexKpi], true, 'normal');

      $cek = "SELECT * from kpi_realization_dept where year_kpi_realization = :yearKpi and deptkpi_id = :departmentKpi and data_avail_id_usersetup = :userSetup ";
      $cek .= $typeKPI == "KPI Department" ? "and compkpi_id = :companyKpi and (status_kpi = 'kpi_department_corps' or status_kpi = 'kpi_department_support') " : "";
      $cek .= $typeKPI == "KPI Division Korporat" ? "and (status_kpi = 'kpi_divcorp_corps' or status_kpi = 'kpi_divcorp_support') " : "";
      $cek .= "ORDER BY index_perspective, STRING_TO_ARRAY(index_kpi_realization, '.')::INT[] asc";
      // $query = $this->sendQuery($this->konek_sita_db(), $cek);
      // $response = pg_fetch_all($query);
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, array(
        ':yearKpi' => $year_kpi,
        ':departmentKpi' => $departmentKpi,
        ':companyKpi' => $companyKpi,
        ':userSetup' => $_SESSION['setupuser_kpi_askara']
      ));
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
          b.value_kpidept_real realisasi, b.file_kpidept_real file_realisasi, b.id_kpidept_real id_tbl_realisasi,
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
            and data_avail_id_usersetup = :userSetup
          )
          and a.month_kpidept >= $monthFrom_kpi and a.month_kpidept <= $monthTo_kpi";
          // $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
          // $response_target = pg_fetch_all($query_target);
          $query_target = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_target, array(
            ':yearKpi' => $year_kpi,
            ':departmentKpi' => $departmentKpi,
            ':companyKpi' => $companyKpi,
            ':userSetup' => $_SESSION['setupuser_kpi_askara']
          ));
          $response_target = $query_target->fetchAll();

        } elseif ($typeKPI == "KPI Division Korporat") {
          $cek_target = "SELECT a.*,
          b.value_kpidivcorp_real realisasi, b.file_kpidivcorp_real file_realisasi, b.id_kpidivcorp_real id_tbl_realisasi,
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
            and data_avail_id_usersetup = :userSetup
          )
          and a.month_kpidivcorp >= $monthFrom_kpi and a.month_kpidivcorp <= $monthTo_kpi";
          // $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
          // $response_target = pg_fetch_all($query_target);
          $query_target = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_target, array(
            ':yearKpi' => $year_kpi,
            ':departmentKpi' => $departmentKpi,
            ':userSetup' => $_SESSION['setupuser_kpi_askara']
          ));
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

  private $columnTable = array(
    'kpi_department_realization' => array('id_kpidept_real', 'id_kpidept_target', 'value_kpidept_real', 'file_kpidept_real'),
    'kpi_divcorp_realization' => array('id_kpidivcorp_real', 'id_kpidivcorp_target', 'value_kpidivcorp_real', 'file_kpidivcorp_real')
  );

  private function destructSendKpi($typeTarget) {
    $filterKpi = array_filter($_POST['type_kpi'], function($value) use ($typeTarget) {
      return $value == $typeTarget;
    });
    $keyFilterKpi = !empty($filterKpi) ? array_keys($filterKpi) : array();

    $arrFilter = !empty($filterKpi) ? array_filter($_POST['realisasi_kpi'], function($value, $key) use ($keyFilterKpi) {
      return isset($value) && $value !== null && strval($value) !== "" && in_array($key, $keyFilterKpi);
    }, ARRAY_FILTER_USE_BOTH) : array();
    $arrKeys = !empty($filterKpi) ? array_keys($arrFilter) : array();
    $arrMapKeys = !empty($arrKeys) ? array_map(function($value) {
      return "'$value'";
    }, $arrKeys) : array();
    $implodeId = !empty($arrMapKeys) ? implode(',', $arrMapKeys) : '';
    return array(
      'arrFilter' => $arrFilter,
      'implodeId' => $implodeId
    );
  }

  private function cleanInputSendKpi($dataReal, $dataTarget, $tableName) {
    $cleanArray = array();
    foreach ($dataReal['arrFilter'] as $key => $value) {
      $makeArray = array();
      $makeArray['type_table'] = $tableName;
      $makeArray['id_target'] = $key;
      $makeArray['realisasi'] = $value;
      if (!empty($dataTarget)) {
        foreach ($dataTarget as $keyChild => $valueChild) {
          if ($valueChild[$this->columnTable[$tableName][1]] == $key) {
            $cleanText = substr($valueChild[$this->columnTable[$tableName][3]], 1, -1);
            $getArray = explode(',', $cleanText);
            $makeArray['id_realisasi'] = $valueChild[$this->columnTable[$tableName][0]];
            $makeArray['file_realisasi'] = $getArray;
          }
        }
      } else {
        $makeArray['id_realisasi'] = '';
      }
      $makeArray['file_name'] = $_FILES['file_kpi']['name'][$key];
      $makeArray['file_temp'] = $_FILES['file_kpi']['tmp_name'][$key];
      $makeArray['file_type'] = $_FILES['file_kpi']['type'][$key];
      $makeArray['file_size'] = $_FILES['file_kpi']['size'][$key];
      array_push($cleanArray, $makeArray);
    }
    return $cleanArray;
  }

  public function sendKpiRealization(){
    global $cleanWord, $upload;
    try {

      $department = $this->destructSendKpi('KPI Department');
      $divisionKorporat = $this->destructSendKpi('KPI Division Korporat');
      $old_file_evidence = array();

      $cleanArrDept = array();
      if (!empty($department['arrFilter'])) {
        $cek_department = "SELECT id_kpidept_real, id_kpidept_target, file_kpidept_real FROM kpi_department_realization where id_kpidept_target in ($department[implodeId]);";
        $query_department = $this->sendQuery($this->konek_sita_db(), $cek_department);
        $response_department = pg_fetch_all($query_department);
        $cleanArrDept = $this->cleanInputSendKpi($department, $response_department, 'kpi_department_realization');
      }

      $cleanArrDivc = array();
      if (!empty($divisionKorporat['arrFilter'])) {
        $cek_divcorp = "SELECT id_kpidivcorp_real, id_kpidivcorp_target, file_divcorp_real FROM kpi_divcorp_realization where id_kpidivcorp_target in ($divisionKorporat[implodeId]);";
        $query_divcorp = $this->sendQuery($this->konek_sita_db(), $cek_divcorp);
        $response_divcorp = pg_fetch_all($query_divcorp);
        $cleanArrDivc = $this->cleanInputSendKpi($divisionKorporat, $response_divcorp, 'kpi_divcorp_realization');
      }
      $mergeClean = array_merge($cleanArrDept, $cleanArrDivc);

      $getPkey_department = !empty($cleanArrDept) ? $this->getSeriesPkey('kpi_department_realization', 'id_kpidept_real', 1, 99999, count($mergeClean), "SELECT split_part(id_kpidept_real, '-', 3)::integer from kpi_department_realization where split_part(id_kpidept_real, '-', 2)::varchar = '". $this->year ."'") : array();
      $count_id_real_department = 0;
      $getPkey_divcorp = !empty($cleanArrDivc) ? $this->getSeriesPkey('kpi_divcorp_realization', 'id_kpidivcorp_real', 1, 99999, count($mergeClean), "SELECT split_part(id_kpidivcorp_real, '-', 3)::integer from kpi_divcorp_realization where split_part(id_kpidivcorp_real, '-', 2)::varchar = '". $this->year ."'") : array();
      $count_id_real_divcorp = 0;

      $upload->setDirFile('evidence/');
      $upload->setTypeFile([
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'xls', 'xlsx', 'word', 'ppt', 'pptx', 'doc', 'docs', 'docx',
        'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'PDF', 'XLS', 'XLSX', 'WORD', 'PPT', 'PPTX', 'DOC', 'DOCS', 'DOCX',
      ]);

      $insert = "";
      foreach ($mergeClean as $key => $value) {

        if (!empty($value['id_realisasi'])) {
          $id_realisasi = $cleanWord->textCk(@$value['id_realisasi'], true, 'trim');
        } elseif ($value['type_table'] == 'kpi_department_realization') {
          $id_realisasi = "KDPREAL-" . $this->year . "-" . str_pad($getPkey_department[$count_id_real_department]['code'], 5, '0', STR_PAD_LEFT);
          $count_id_real_department++;
        } elseif ($value['type_table'] == 'kpi_divcorp_realization') {
          $id_realisasi = "KDCREAL-" . $this->year . "-" . str_pad($getPkey_divcorp[$count_id_real_divcorp]['code'], 5, '0', STR_PAD_LEFT);
          $count_id_real_divcorp++;
        }

        $file_evidence = "";
        if (!empty($value["file_name"])) {
          foreach ($value["file_name"] as $keyFile => $valueFile) {
            if (!empty($valueFile)) {
              $upload->setNameFile($valueFile);
              $upload->setTempFile($value['file_temp'][$keyFile]);
              $upload->setPkeyFile($id_realisasi . "_" . rand());
              $upload->prosesUpload();
              $file_evidence .= "$upload->newNameFile,";
            }
          }
          $file_evidence = '{' . rtrim($file_evidence, ',') . '}';
        }

        $realisasi = $cleanWord->numberCk(@$value['realisasi'], true, 'text', true);
        if (!empty($value['id_realisasi'])) {
          if ($file_evidence !== "{}") {
            $old_file_evidence = array_merge($old_file_evidence, $value['file_realisasi']);
            $sql_file_evidence = $this->columnTable[$value['type_table']][3] . " = '$file_evidence',";
          } else {
            $sql_file_evidence = "";
          }
          $insert .= "UPDATE $value[type_table] SET
          ". $this->columnTable[$value['type_table']][2] ." = $realisasi,
          $sql_file_evidence
          user_entry = $_SESSION[setupuser_kpi_askara],
          last_update = '".$this->last_update."',
          flag = 'u'
          where ".$this->columnTable[$value['type_table']][0]." = '$id_realisasi';
          ";
        } else {
          $id_target = $cleanWord->textCk(@$value['id_target'], true, 'normal');
          // $realisasi = $cleanWord->numberCk(@$value['realisasi'], true, 'double', true);
          $insert .= "INSERT INTO $value[type_table] (
            ".$this->columnTable[$value['type_table']][0].",
            ".$this->columnTable[$value['type_table']][1].",
            ".$this->columnTable[$value['type_table']][2].",
            ".$this->columnTable[$value['type_table']][3].",
            user_entry,
            last_update
          ) values (
            '$id_realisasi', {$id_target}, $realisasi, '$file_evidence', '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
          );
          ";
        }

      }

      $this->sendQueryWithImg($this->konek_sita_db(), $insert, $old_file_evidence, 'evidence/');
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data Berhasil diubah!"
        )
      );

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function getFileKpiRealization() {
    global $cleanWord, $upload, $zipping;
    try {
      $get_file_kpi = base64_decode($cleanWord->textCk(@$_GET["file"], true, 'trim'), true);
      $nama_file_kpi = $cleanWord->textCk(@$_GET["nama_file"], true, 'trim');
      if ($get_file_kpi !== 'undefined' && $get_file_kpi !== 'null') {
        $cleanText = substr($get_file_kpi, 1, -1);
        $getArray = explode(',', $cleanText);
        $zipping->setFolder('evidence/');
        $zipping->setListFile($getArray);
        // $zipping->processZipping('nanih_koreh');
        $zipping->zipFilesAndDownload('EVIDENCE_' . $nama_file_kpi);
      }
      // Tipe KPI_Nama KPI_Bulan Realisasi.zip

    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Zipping file error, coba lagi beberapa saat! ❌'
        )
      );
    }
  }

}
?>