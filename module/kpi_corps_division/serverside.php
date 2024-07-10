<?php
class kpi_department extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  private $id_kpidivcorp_check = null;
  private $month_kpidivcorp_check = null;
  private function checkFilterMonth($id_kpidivcorp, $month_kpidivcorp) {
    return $id_kpidivcorp == $this->id_kpidivcorp_check && $month_kpidivcorp == $this->month_kpidivcorp_check;
  }

  public function getKpiDivisionCorps(){
    global $cleanWord;
    try {
      $with_sql = "kpi_divcorptarget AS (
        SELECT distinct id_kpidivcorp, sum(target_kpidivcorp) target_kpidivcorp
        from kpi_divcorp_target
        group by id_kpidivcorp
      )
      ";
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer', true);
      $divisionCorps_kpi = $cleanWord->textCk(@$_POST["divisionCorps_kpi"], true, 'normal');
      $cekup_kpi = "WITH $with_sql SELECT distinct a.*,
      g.target_kpidivcorp baseline_1, STRING_TO_ARRAY(a.index_kpidivcorp, '.')::INT[] arr_index,
      bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_divcorp a
      LEFT JOIN kpi_divcorp f
        on f.id_sobject = a.id_sobject
        and f.name_kpidivcorp = a.name_kpidivcorp
        and f.deptkpi_id = a.deptkpi_id
        and f.year_kpidivcorp = (a.year_kpidivcorp - 1)
      LEFT JOIN kpi_divcorptarget g on g.id_kpidivcorp = f.id_kpidivcorp
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpidivcorp = $year_kpi
      and a.deptkpi_id = {$divisionCorps_kpi}
      order by index_perspective asc, arr_index asc;";
      $query_kpi = $this->sendQuery($this->konek_sita_db(), $cekup_kpi);
      $response_kpi = pg_fetch_all($query_kpi);

      if (empty($response_kpi)) {
        $cek = "WITH $with_sql SELECT
        'kpi_divcorp_corps'::varchar status_kpi,
        null::varchar id_kpidivcorp,
        a.id_kpicorp,
        a.name_kpicorp AS name_kpidivcorp, a.index_kpicorp AS index_kpidivcorp, a.define_kpicorp AS define_kpidivcorp,
        a.control_cek_kpicorp AS control_cek_kpidivcorp, a.polaritas_kpicorp AS polaritas_kpidivcorp, a.year_kpicorp AS year_kpidivcorp,
        null::boolean terbit_kpidivcorp, null::varchar cascade_kpidivcorp,
        null::double precision target_kpidivcorp,
        a.target_kpicorp,
        null::varchar data_avail_id_usersetup, null::varchar data_avail_fullname_users, null::varchar data_avail_id_department, null::varchar data_avail_name_department,
        null::varchar deptkpi_id, null::varchar deptkpi_name,
        i.id_sobject, i.name_sobject, i.index_sobject,
        j.id_perspective, j.name_perspective, j.alias_perspective, j.index_perspective,
        k.id_satuan, k.name_satuan,
        l.id_formula, l.name_formula,
        reverse(substring(reverse(a.index_kpicorp::text), POSITION('.'::text in reverse(a.index_kpicorp::text)) + 1)) AS index_parent,
        (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
        ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
        null::integer date_cutoff,
        null::varchar deptkpi_id, null::varchar deptkpi_name,
        g.target_kpidivcorp baseline_1, STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] arr_index,
        null::varchar user_entry, null::timestamp last_update
        from kpi_corporate a
        LEFT JOIN kpi_divcorp f
          on f.id_sobject = a.id_sobject
          and f.name_kpidivcorp = a.name_kpicorp
          and f.deptkpi_id = {$divisionCorps_kpi}::varchar
          and f.year_kpidivcorp = ($year_kpi - 1)
        LEFT JOIN kpi_divcorptarget g on g.id_kpidivcorp = f.id_kpidivcorp
        LEFT JOIN strategic_objective i ON i.id_sobject::text = a.id_sobject::text
        LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
        LEFT JOIN satuan_master k ON k.id_satuan::text = a.id_satuan::text
        LEFT JOIN formula_master l ON l.id_formula::text = a.id_formula::text
        where a.year_kpicorp = $year_kpi and a.terbit_kpicorp is true
        order by index_perspective asc, arr_index asc";

        $query = $this->sendQuery($this->konek_sita_db(), $cek);
        $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
        foreach ($response as $key => $value) {
          $response[$key]['target_month'] = [];
        }
        return json_encode($response);

      } else {
        $arrId = [];
        foreach ($response_kpi as $key => $value) {
          array_push($arrId, $response_kpi[$key]['id_kpidivcorp']);
        }
        $compileId = array_map(function($val) {
          return "'$val'";
        }, $arrId);
        $compileId = implode(',', $compileId);

        $cek_target = "SELECT * from kpi_divcorp_target where id_kpidivcorp in ($compileId)";
        $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
        $response_target = pg_fetch_all($query_target);
        
        foreach ($response_kpi as $key => $value) {
          $response_kpi[$key]['target_month'] = [];
          foreach ($response_target as $keyTarget => $valueTarget) {
            if ($valueTarget['id_kpidivcorp'] == $value['id_kpidivcorp']) {
              $response_kpi[$key]['target_month'][$valueTarget['month_kpidivcorp']] = $valueTarget['target_kpidivcorp'];
            }
          }
        }
        return json_encode($response_kpi);
      }

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  private function targetCalcMonth($target_month, $month_valid = null){
    global $cleanWord;
    $calc = 0;
    $count_valid_month = 0;
    if ($month_valid !== null) {
      foreach ($month_valid as $key => $value) {
        $month_valid[$key] = $this->monthArr[$month_valid[$key]];
      }
      foreach ($target_month as $key => $value) {
        if (in_array($key, $month_valid)) {
          $calc = $calc + $cleanWord->numberCk(@$value, true, 'double', true);
          $count_valid_month++;
        } else {
          echo json_encode(
            array(
              'response'=>'error',
              'alert'=>"Terjadi anomali pengisian target, kontak IT!"
            )
          );
          die();
        }
      }
    } else {
      foreach ($target_month as $key => $value) {
        $calc = $calc + $cleanWord->numberCk(@$value, false, 'double', true);
        $testup = $cleanWord->numberCk(@$value, false, 'text', true);
        if ($testup !== null && $testup !== '') {
          $count_valid_month++;
        }
      }
    }
    return array(
      'calc' => $calc,
      'count_month' => $count_valid_month
    );
  }

  private function checkUpTarget($index, $cascade, $month_choice, $target_month, $target_corp){
    global $cleanWord;
    $counting_month = 0;
    foreach ($index as $key => $value) {
      
      if (intval($target_corp[$key]) == 0) {
        $new_cascade = empty($cascade[$key]) ? 'full-round' : $cascade[$key];
      } else {
        if (empty($cascade[$key])) {
          echo json_encode(
            array(
              'response'=>'error',
              'alert'=>"Silahkan cek index '$value', cascade belum dipilih ❌"
            )
          );
          die();
        }
        $new_cascade = $cascade[$key];
      }

      if (!empty($month_choice[$key]) && $month_choice[$key] !== '{}') {
        $new_month_choice = rtrim($month_choice[$key], '}');
        $new_month_choice = ltrim($new_month_choice, '{');
        $new_month_choice = explode(',', $new_month_choice);
        $new_target_month = $this->targetCalcMonth($target_month[$key], $new_month_choice);
      } else {
        $new_target_month = $this->targetCalcMonth($target_month[$key]);
      }
      if ($new_cascade == 'full-round' || $new_cascade == 'half-round') {
        $new_target = $cleanWord->numberCk(@$target_corp[$key], true, 'double', true);
      } else {
        $new_target = $cleanWord->numberCk(@$target_corp[$key], false, 'double', true);
      }
      if ($new_cascade == 'full-round' && $new_target !== $new_target_month['calc']) {
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>"Silahkan cek index '$value' dengan cascade '$new_cascade', jumlah target perbulannya tidak sama dengan kriteria cascade ❌"
          )
        );
        die();
      } elseif ($new_cascade == 'half-round' && $new_target_month['calc'] == $new_target) {
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>"Silahkan cek index '$value' dengan cascade '$new_cascade', jumlah target perbulannya tidak sama dengan kriteria cascade ❌"
          )
        );
        die();
      }
      if (empty($new_target_month['count_month'])) {
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>"Pastikan ada target yang diisi!"
          )
        );
        die();
      }
      $counting_month = $counting_month + $new_target_month['count_month'];

    }
    return $counting_month;
  }

  private function sqlInsertTarget ($id_kpidivcorp_target, $id_kpidivcorp, $target_kpidivcorp, $month_kpidivcorp, $user_entry, $last_update, $status = 'new') {
    $id_kpidivcorp = $status == 'new' ? "'$id_kpidivcorp'" : "{$id_kpidivcorp}";
    return "INSERT INTO kpi_divcorp_target (
      id_kpidivcorp_target, id_kpidivcorp,
      target_kpidivcorp, month_kpidivcorp,
      user_entry, last_update, lock_status
    ) values (
      '$id_kpidivcorp_target', $id_kpidivcorp,
      $target_kpidivcorp, $month_kpidivcorp,
      '$user_entry', '$last_update', false
    );
    ";
  }

  public function editKpiDivisionCorps(){
    global $cleanWord, $upload;

    $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
    $divisionCorps_kpi = $cleanWord->textCk(@$_POST["divisionCorps_kpi"], true, 'normal');
    $sql = "";
    $keyCountCode = 0;
    $keyCountTarget = 0;

    if (!empty($_POST['index_kpidivcorp'])) {

      $count_valid_month = $this->checkUpTarget(@$_POST['index_kpidivcorp'], @$_POST['cascade_kpidivcorp'], @$_POST['month_kpidivcorp'], @$_POST['target_month'], @$_POST['target_kpicorp']);
      if (empty($count_valid_month)) {
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>"Pastikan ada target yang diisi!"
          )
        );
        die();
      }
      $countNewCode = count(array_map(function($value) {
        return empty($value);
      }, $_POST['id_kpidivcorp']));
      $arrayUpdate = array_filter($_POST['id_kpidivcorp'], function($value) {
        return !empty($value);
      });
      $arrayUpdate = array_map(function($val) {
        return "'$val'";
      }, $arrayUpdate);
      $checkUpdate = implode(",", $arrayUpdate);
  
      $getPkey = $this->getSeriesPkey('kpi_divcorp', 'id_kpidivcorp', 1, 99999, $countNewCode, "SELECT split_part(id_kpidivcorp, '-', 3)::integer from kpi_divcorp where split_part(id_kpidivcorp, '-', 2)::varchar = '". $this->year ."'");
      $getPkeyTarget = $this->getSeriesPkey('kpi_divcorp_target', 'id_kpidivcorp_target', 1, 99999, $count_valid_month, "SELECT split_part(id_kpidivcorp_target, '-', 3)::integer from kpi_divcorp_target where split_part(id_kpidivcorp_target, '-', 2)::varchar = '". $this->year ."'");
  
      if (!empty($checkUpdate)) {
        $sql_check = "SELECT distinct id_kpidivcorp_target, id_kpidivcorp, month_kpidivcorp from kpi_divcorp_target where id_kpidivcorp in ($checkUpdate)";
        $query_check = $this->sendQuery($this->konek_sita_db(), $sql_check);
        $response_check = pg_fetch_all($query_check);
      } else {
        $response_check = [];
      }

      $test_quo = 0;
      foreach ($_POST['index_kpidivcorp'] as $key => $value) {

        $target_kpicorp = $cleanWord->numberCk(@$_POST["target_kpicorp"][$key], true, 'integer', true);
        if ($target_kpicorp == 0) {
          $cascade_kpidivcorp = empty($_POST["cascade_kpidivcorp"][$key]) ? 'full-round' : $cleanWord->textCk(@$_POST["cascade_kpidivcorp"][$key], true, 'trim');
        } else {
          $cascade_kpidivcorp = $cleanWord->textCk(@$_POST["cascade_kpidivcorp"][$key], true, 'trim');
        }
        $status_kpi = $cleanWord->textCk(@$_POST["status_kpi"][$key], false, 'trim');
        $date_cutoff = $cleanWord->numberCk(@$_POST["date_cutoff"][$key], true, 'integer');
        $userpic_kpidivcorp = $cleanWord->textCk(@$_POST["userpic_kpidivcorp"][$key], true, 'normal');
        $index_kpidivcorp = $cleanWord->textCk(@$_POST["index_kpidivcorp"][$key], true, 'normal');
        $name_kpidivcorp = $cleanWord->textCk(@$_POST["name_kpidivcorp"][$key], true, 'normal');
        $define_kpidivcorp = $cleanWord->textCk(@$_POST["define_kpidivcorp"][$key], true, 'normal');
        $control_cek_kpidivcorp = $cleanWord->textCk(@$_POST["control_cek_kpidivcorp"][$key], true, 'normal');
        $id_kpi = '';

        if ($date_cutoff > 31 || $date_cutoff < 1) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>"Tanggal cutoff hanya boleh dari rentang nomor 1 - 31, sesuai dengan minimal dan maksimal hari perbulannya!"
            )
          );
        }

        if (!empty($_POST['id_kpidivcorp'][$key])) {
          
          $id_kpi = $cleanWord->textCk(@$_POST["id_kpidivcorp"][$key], true, 'normal');
          if ($status_kpi == 'kpi_divcorp_corps') {
            $sql .= "UPDATE kpi_divcorp_corps SET
            index_kpidivcorp = {$index_kpidivcorp},
            name_kpidivcorp = {$name_kpidivcorp},
            define_kpidivcorp = {$define_kpidivcorp},
            control_cek_kpidivcorp = {$control_cek_kpidivcorp},
            cascade_kpidivcorp = '$cascade_kpidivcorp',
            date_cutoff = $date_cutoff,
            id_usersetup = {$userpic_kpidivcorp},
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            where id_kpidivcorp = {$id_kpi};
            ";
          } else {
            $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
            $satuan_kpidivcorp = $cleanWord->textCk(@$_POST["satuan_kpidivcorp"][$key], true, 'normal');
            $formula_kpidivcorp = $cleanWord->textCk(@$_POST["formula_kpidivcorp"][$key], true, 'normal');
            $polaritas_kpidivcorp = $cleanWord->textCk(@$_POST["polaritas_kpidivcorp"][$key], true, 'normal');

            $sql .= "UPDATE kpi_divcorp_support SET
            id_sobject = {$id_sobject},
            index_kpidivcorp = {$index_kpidivcorp},
            name_kpidivcorp = {$name_kpidivcorp},
            define_kpidivcorp = {$define_kpidivcorp},
            control_cek_kpidivcorp = {$control_cek_kpidivcorp},
            id_satuan = {$satuan_kpidivcorp},
            id_formula = {$formula_kpidivcorp},
            polaritas_kpidivcorp = {$polaritas_kpidivcorp},
            cascade_kpidivcorp = '$cascade_kpidivcorp',
            id_usersetup = {$userpic_kpidivcorp},
            date_cutoff = $date_cutoff,
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            where id_kpidivcorp = {$id_kpi};
            ";
          }

          $arrayTargetMonth = array_filter($_POST['target_month'][$key], function($valMonth) {
            return $valMonth !== null && $valMonth !== '';
          });
          $this->id_kpidivcorp_check = $cleanWord->textCk(@$_POST["id_kpidivcorp"][$key], true, 'trim');
          foreach ($arrayTargetMonth as $keyTarget => $valueTarget) {
            $this->month_kpidivcorp_check = $keyTarget;
            $arrTargetValidMonth = array_filter($response_check, function($item) {
              return $this->checkFilterMonth($item['id_kpidivcorp'], $item['month_kpidivcorp']);
            });
            $monthTarget = $cleanWord->numberCk(@$valueTarget, false, 'text', true);
            $monthValue = $cleanWord->numberCk(@$keyTarget, false, 'integer', true);
            $countingValidMonth = count($arrTargetValidMonth);
            
            if ($countingValidMonth > 0) {
              $getValidId = reset($arrTargetValidMonth);
              $id_kpidivcorp_target = $cleanWord->textCk(@$getValidId['id_kpidivcorp_target'], true, 'normal');
              $sql .= "UPDATE kpi_divcorp_target SET
              id_kpidivcorp = {$id_kpi},
              target_kpidivcorp = $monthTarget,
              month_kpidivcorp = $monthValue,
              user_entry = '$_SESSION[setupuser_kpi_askara]',
              last_update = '".$this->last_update."',
              flag = 'u'
              where id_kpidivcorp_target = {$id_kpidivcorp_target};
              ";
            } else {
              $id_kpi_target = "KDCTARGET-" . $this->year . "-" . str_pad($getPkeyTarget[$keyCountTarget]['code'], 5, '0', STR_PAD_LEFT);
              $sql .= $this->sqlInsertTarget(
                $id_kpi_target,
                $id_kpi,
                $monthTarget,
                $monthValue,
                $_SESSION['setupuser_kpi_askara'],
                $this->last_update,
                'old'
              );
              $keyCountTarget++;
            }
          }

        } elseif (empty($_POST['id_kpidivcorp'][$key])) {
          $id_kpi = "KDC-" . $this->year . "-" . str_pad($getPkey[$keyCountCode]['code'], 5, '0', STR_PAD_LEFT);
          if (!empty($_POST["id_kpicorp"][$key])) {
            $id_kpicorp = $cleanWord->textCk(@$_POST["id_kpicorp"][$key], true, 'normal');
            $sql .= "INSERT INTO kpi_divcorp_corps (
              id_kpidivcorp, id_kpicorp, index_kpidivcorp,
              name_kpidivcorp, define_kpidivcorp, control_cek_kpidivcorp,
              cascade_kpidivcorp, date_cutoff, id_department, id_usersetup,
              user_entry, last_update
            ) values (
              '$id_kpi', {$id_kpicorp}, {$index_kpidivcorp},
              {$name_kpidivcorp}, {$define_kpidivcorp}, {$control_cek_kpidivcorp},
              '$cascade_kpidivcorp', $date_cutoff, {$divisionCorps_kpi}, {$userpic_kpidivcorp},
              '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
            );
            ";
          } else {
            $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
            $satuan_kpidivcorp = $cleanWord->textCk(@$_POST["satuan_kpidivcorp"][$key], true, 'normal');
            $formula_kpidivcorp = $cleanWord->textCk(@$_POST["formula_kpidivcorp"][$key], true, 'normal');
            $polaritas_kpidivcorp = $cleanWord->textCk(@$_POST["polaritas_kpidivcorp"][$key], true, 'normal');

            $sql .= "INSERT INTO kpi_divcorp_support (
              id_kpidivcorp, name_kpidivcorp, index_kpidivcorp, define_kpidivcorp, control_cek_kpidivcorp,
              id_sobject, id_satuan, id_formula, polaritas_kpidivcorp,
              year_kpidivcorp, cascade_kpidivcorp, date_cutoff,
              id_usersetup, id_department,
              user_entry, last_update
            ) values (
              '$id_kpi', {$name_kpidivcorp}, {$index_kpidivcorp}, {$define_kpidivcorp}, {$control_cek_kpidivcorp},
              {$id_sobject}, {$satuan_kpidivcorp}, {$formula_kpidivcorp}, {$polaritas_kpidivcorp},
              $year_kpi, '$cascade_kpidivcorp', $date_cutoff,
              {$userpic_kpidivcorp}, {$divisionCorps_kpi},
              '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
            );
            ";
          }

          $arrayTargetMonth = array_filter($_POST['target_month'][$key], function($valMonth) {
            return $valMonth !== null && $valMonth !== '';
          });
          foreach ($arrayTargetMonth as $keyTarget => $valueTarget) {
            $id_kpi_target = "KDCTARGET-" . $this->year . "-" . str_pad($getPkeyTarget[$keyCountTarget]['code'], 5, '0', STR_PAD_LEFT);
            $sql .= $this->sqlInsertTarget(
              $id_kpi_target,
              $id_kpi,
              $cleanWord->numberCk(@$valueTarget, false, 'text', true),
              $cleanWord->numberCk(@$keyTarget, false, 'integer', true),
              $_SESSION['setupuser_kpi_askara'],
              $this->last_update
            );
            $keyCountTarget++;
            $test_quo++;
          }
          $keyCountCode++;
        }
      }
    }

    $sql_delete = "";
    if (!empty($_POST['deleteDataKPI'])) {
      foreach (json_decode($_POST['deleteDataKPI'], true) as $key => $value) {
        $status_kpi = $cleanWord->textCk(@$value['status_kpi'], true, 'trim');
        if (!empty($value['id_kpidivcorp'])) {
          $id_kpi = $cleanWord->textCk(@$value['id_kpidivcorp'], true, 'normal');
          $sql_delete .= $status_kpi == 'kpi_divcorp_corps' ? "DELETE FROM kpi_divcorp_corps where id_kpidivcorp = {$id_kpi};
          " : "DELETE FROM kpi_divcorp_support where id_kpidivcorp = {$id_kpi};
          ";
          $sql_delete .= "DELETE FROM kpi_divcorp_target where id_kpidivcorp = {$id_kpi};";
        }
      }
    }

    if (empty(trim($sql_delete . $sql))) {
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data tidak ada perubahan!"
        )
      );
    }

    try {
      $this->sendQuery($this->konek_sita_db(), $sql_delete . $sql);
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data Berhasil diubah!"
        )
      );
    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan! ❌'
        )
      );
    }
    
  }

  public function publishKpiDivisionCorps(){
    global $cleanWord;
    try {

      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $divisionCorps_kpi = $cleanWord->textCk(@$_POST["divisionCorps_kpi"], true, 'normal');

      $cek = "SELECT count(*) from kpi_divcorp where year_kpidivcorp = $year_kpi and deptkpi_id = {$divisionCorps_kpi}";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = pg_fetch_all($query);
      if (empty($response) || empty(@$response[0]['count'])) {
        return json_encode(
          array(
            'response'=>'error',
            'alert'=>'Silahkan isi data KPI terlebih dahulu! ❌'
          )
        );
      }

      $cek = "UPDATE kpi_divcorp_support SET terbit_kpidivcorp = true where year_kpidivcorp = $year_kpi and id_department = {$divisionCorps_kpi};
      UPDATE kpi_divcorp_corps SET terbit_kpidivcorp = true where id_department = {$divisionCorps_kpi} and id_kpicorp in (
        SELECT id_kpicorp from kpi_corporate where year_kpicorp = $year_kpi
      );";

      $this->sendQuery($this->konek_sita_db(), $cek);
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"KPI Berhasil diterbitkan!"
        )
      );

    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan! ❌'
        )
      );
    }
  }
}
?>