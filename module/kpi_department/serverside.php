<?php
class kpi_department extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  private $id_kpidept_check = null;
  private $month_kpidept_check = null;
  private function checkFilterMonth($id_kpidept, $month_kpidept) {
    return $id_kpidept == $this->id_kpidept_check && $month_kpidept == $this->month_kpidept_check;
  }

  public function getKpiDepartment(){
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
      $department_kpi = $cleanWord->textCk(@$_POST["department_kpi"], false, 'normal');
      $cekup_kpi = "WITH $with_sql SELECT distinct a.*,
      g.target_kpidept baseline_1, STRING_TO_ARRAY(a.index_kpidept, '.')::INT[] arr_index,
      bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_department a
      left join kpi_department f
        on f.id_sobject = a.id_sobject
        and f.name_kpidept = a.name_kpidept
        and f.compkpi_id = a.compkpi_id
        and f.deptkpi_id = a.deptkpi_id
        and f.year_kpidept = (a.year_kpidept - 1)
      left join kpi_depttarget g on g.id_kpidept = f.id_kpidept
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpidept = $year_kpi
      and a.compkpi_id = {$company_kpi}
      and a.deptkpi_id = {$department_kpi}
      order by index_perspective asc, arr_index asc";
      $query_kpi = $this->sendQuery($this->konek_sita_db(), $cekup_kpi);
      $response_kpi = pg_fetch_all($query_kpi);
      
      if (empty($response_kpi)) {
        $cek = "WITH $with_sql SELECT distinct
        'kpi_department_corps'::varchar status_kpi,
        null::varchar id_kpidept,
        a.id_kpibunit,
        a.name_kpibunit AS name_kpidept, a.index_kpibunit AS index_kpidept, a.define_kpibunit AS define_kpidept,
        a.control_cek_kpibunit AS control_cek_kpidept, a.polaritas_kpibunit AS polaritas_kpidept, a.year_kpibunit AS year_kpidept,
        null::boolean terbit_kpidept, null::varchar cascade_kpidept, a.month_kpibunit AS month_kpidept,
        a.target_kpibunit, a.target_kpicorp,
        a.data_avail_id_usersetup, a.data_avail_fullname_users, a.data_avail_id_department, a.data_avail_name_department,
        a.compkpi_id, a.compkpi_name,
        a.id_sobject, a.name_sobject, a.index_sobject,
        a.id_perspective, a.name_perspective, a.alias_perspective, a.index_perspective,
        a.id_satuan, a.name_satuan,
        a.id_formula, a.name_formula,
        a.index_parent,
        a.text_sobject,
        a.text_perspective,
        null::integer date_cutoff,
        null::varchar deptkpi_id, null::varchar deptkpi_name,
        g.target_kpidept baseline_1, STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] arr_index,
        null::varchar user_entry, null::timestamp last_update, null::varchar nickname_entry, null::varchar fullname_entry
        from kpi_bisnis_unit a
        left join kpi_department f
          on f.id_sobject = a.id_sobject
          and f.name_kpidept = a.name_kpibunit
          and f.compkpi_id = {$company_kpi}::varchar
          and f.deptkpi_id = {$department_kpi}::varchar
          and f.year_kpidept = (a.year_kpibunit - 1)
        left join kpi_depttarget g on g.id_kpidept = f.id_kpidept
        where a.year_kpibunit = $year_kpi and a.compkpi_id = {$company_kpi} and a.terbit_kpibunit is true
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
          array_push($arrId, $response_kpi[$key]['id_kpidept']);
        }
        $compileId = array_map(function($val) {
          return "'$val'";
        }, $arrId);
        $compileId = implode(',', $compileId);

        $cek_target = "SELECT * from kpi_department_target where id_kpidept in ($compileId)";
        $query_target = $this->sendQuery($this->konek_sita_db(), $cek_target);
        $response_target = pg_fetch_all($query_target);

        foreach ($response_kpi as $key => $value) {
          $response_kpi[$key]['target_month'] = [];
          foreach ($response_target as $keyTarget => $valueTarget) {
            if ($valueTarget['id_kpidept'] == $value['id_kpidept']) {
              $response_kpi[$key]['target_month'][$valueTarget['month_kpidept']] = $valueTarget['target_kpidept'];
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
        $count_valid_month++;
      }
    }
    return array(
      'calc' => $calc,
      'count_month' => $count_valid_month
    );
  }

  private function checkUpTarget($index, $cascade, $month_choice, $target_month, $target_bunit){
    global $cleanWord;
    $counting_month = 0;
    foreach ($index as $key => $value) {

      if (intval($target_bunit[$key]) == 0) {
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
        $new_target = $cleanWord->numberCk(@$target_bunit[$key], true, 'double', true);
      } else {
        $new_target = $cleanWord->numberCk(@$target_bunit[$key], false, 'double', true);
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
      $counting_month = $counting_month + $new_target_month['count_month'];

    }
    return $counting_month;
  }

  private function sqlInsertTarget ($id_kpidept_target, $id_kpidept, $target_kpidept, $month_kpidept, $user_entry, $last_update, $status = 'new') {
    $id_kpidept = $status == 'new' ? "'$id_kpidept'" : "{$id_kpidept}";
    return "INSERT INTO kpi_department_target (
      id_kpidept_target, id_kpidept,
      target_kpidept, month_kpidept,
      user_entry, last_update, lock_status
    ) values (
      '$id_kpidept_target', $id_kpidept,
      $target_kpidept, $month_kpidept,
      '$user_entry', '$last_update', false
    );
    ";
  }

  public function editKpiDepartment(){
    global $cleanWord, $upload;

    $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
    $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');
    $department_kpi = $cleanWord->textCk(@$_POST["department_kpi"], true, 'normal');
    $sql = "";
    $keyCountCode = 0;
    $keyCountTarget = 0;

    if (!empty($_POST['index_kpidept'])) {

      $count_valid_month = $this->checkUpTarget(@$_POST['index_kpidept'], @$_POST['cascade_kpidept'], @$_POST['month_kpidept'], @$_POST['target_month'], @$_POST['target_kpibunit']);
      $countNewCode = count(array_map(function($value) {
        return empty($value);
      }, $_POST['id_kpidept']));
      $arrayUpdate = array_filter($_POST['id_kpidept'], function($value) {
        return !empty($value);
      });
      $arrayUpdate = array_map(function($val) {
        return "'$val'";
      }, $arrayUpdate);
      $checkUpdate = implode(",", $arrayUpdate);
      $getPkey = $this->getSeriesPkey('kpi_department', 'id_kpidept', 1, 99999, $countNewCode, "SELECT split_part(id_kpidept, '-', 3)::integer from kpi_department where split_part(id_kpidept, '-', 2)::varchar = '". $this->year ."'");
      $getPkeyTarget = $this->getSeriesPkey('kpi_department_target', 'id_kpidept_target', 1, 99999, $count_valid_month, "SELECT split_part(id_kpidept_target, '-', 3)::integer from kpi_department_target where split_part(id_kpidept_target, '-', 2)::varchar = '". $this->year ."'");
      if (!empty($checkUpdate)) {
        $sql_check = "SELECT distinct id_kpidept_target, id_kpidept, month_kpidept from kpi_department_target where id_kpidept in ($checkUpdate)";
        $query_check = $this->sendQuery($this->konek_sita_db(), $sql_check);
        $response_check = pg_fetch_all($query_check);
      } else {
        $response_check = [];
      }

      foreach ($_POST['index_kpidept'] as $key => $value) {

        $target_kpibunit = $cleanWord->numberCk(@$_POST["target_kpibunit"][$key], true, 'integer', true);
        if ($target_kpibunit == 0) {
          $cascade_kpidept = empty($_POST["cascade_kpidept"][$key]) ? 'full-round' : $cleanWord->textCk(@$_POST["cascade_kpidept"][$key], true, 'trim');
        } else {
          $cascade_kpidept = $cleanWord->textCk(@$_POST["cascade_kpidept"][$key], true, 'trim');
        }

        $status_kpi = $cleanWord->textCk(@$_POST["status_kpi"][$key], false, 'trim');
        $date_cutoff = $cleanWord->numberCk(@$_POST["date_cutoff"][$key], true, 'integer');
        $name_kpidept = $cleanWord->textCk(@$_POST["name_kpidept"][$key], true, 'normal');
        $define_kpidept = $cleanWord->textCk(@$_POST["define_kpidept"][$key], true, 'normal');
        $control_cek_kpidept = $cleanWord->textCk(@$_POST["control_cek_kpidept"][$key], true, 'normal');
        $index_kpidept = $cleanWord->textCk(@$_POST["index_kpidept"][$key], true, 'normal');
        $id_kpi = '';

        if ($date_cutoff < 1 || $date_cutoff > 31) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>"Tanggal <span class=\"fst-italic\">cut off</span> tidak boleh kurang dari 1 dan lebih dari 31"
            )
          );
        }

        if (!empty($_POST['id_kpidept'][$key])) {
          
          $id_kpi = $cleanWord->textCk(@$_POST["id_kpidept"][$key], true, 'normal');
          if ($status_kpi == 'kpi_department_corps') {
            $sql .= "UPDATE kpi_department_corps SET
            name_kpidept = {$name_kpidept},
            define_kpidept = {$define_kpidept},
            control_cek_kpidept = {$control_cek_kpidept},
            index_kpidept = {$index_kpidept},
            cascade_kpidept = '$cascade_kpidept',
            date_cutoff = $date_cutoff,
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            where id_kpidept = {$id_kpi};
            ";
          } else {
            $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
            $satuan_kpidept = $cleanWord->textCk(@$_POST["satuan_kpidept"][$key], true, 'normal');
            $formula_kpidept = $cleanWord->textCk(@$_POST["formula_kpidept"][$key], true, 'normal');
            $polaritas_kpidept = $cleanWord->textCk(@$_POST["polaritas_kpidept"][$key], true, 'normal');
            $userpic_kpidept = $cleanWord->textCk(@$_POST["userpic_kpidept"][$key], true, 'normal');

            $sql .= "UPDATE kpi_department_support SET
            id_sobject = {$id_sobject},
            index_kpidept = {$index_kpidept},
            name_kpidept = {$name_kpidept},
            define_kpidept = {$define_kpidept},
            control_cek_kpidept = {$control_cek_kpidept},
            id_satuan = {$satuan_kpidept},
            id_formula = {$formula_kpidept},
            polaritas_kpidept = {$polaritas_kpidept},
            cascade_kpidept = '$cascade_kpidept',
            id_usersetup = {$userpic_kpidept},
            date_cutoff = $date_cutoff,
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            where id_kpidept = {$id_kpi};
            ";
          }

          $arrayTargetMonth = array_filter($_POST['target_month'][$key], function($valMonth) {
            return $valMonth !== null && $valMonth !== '';
          });
          $this->id_kpidept_check = $cleanWord->textCk(@$_POST["id_kpidept"][$key], true, 'trim');
          foreach ($arrayTargetMonth as $keyTarget => $valueTarget) {
            $this->month_kpidept_check = $keyTarget;
            $arrTargetValidMonth = array_filter($response_check, function($item) {
              return $this->checkFilterMonth($item['id_kpidept'], $item['month_kpidept']);
            });
            $monthTarget = $cleanWord->numberCk(@$valueTarget, false, 'text', true);
            $monthValue = $cleanWord->numberCk(@$keyTarget, false, 'integer', true);
            $countingValidMonth = count($arrTargetValidMonth);
            
            if ($countingValidMonth > 0) {
              $getValidId = reset($arrTargetValidMonth);
              $id_kpidept_target = $cleanWord->textCk(@$getValidId['id_kpidept_target'], true, 'normal');
              $sql .= "UPDATE kpi_department_target SET
              id_kpidept = {$id_kpi},
              target_kpidept = $monthTarget,
              month_kpidept = $monthValue,
              user_entry = '$_SESSION[setupuser_kpi_askara]',
              last_update = '".$this->last_update."',
              flag = 'u'
              where id_kpidept_target = {$id_kpidept_target};
              ";
            } else {
              $id_kpi_target = "KDPTARGET-" . $this->year . "-" . str_pad($getPkeyTarget[$keyCountTarget]['code'], 5, '0', STR_PAD_LEFT);
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

        } elseif (empty($_POST['id_kpidept'][$key])) {
          $id_kpi = "KDP-" . $this->year . "-" . str_pad($getPkey[$keyCountCode]['code'], 5, '0', STR_PAD_LEFT);
          if (!empty($_POST["id_kpibunit"][$key])) {
            $id_kpibunit = $cleanWord->textCk(@$_POST["id_kpibunit"][$key], true, 'normal');
            $sql .= "INSERT INTO kpi_department_corps (
              id_kpidept, id_kpibunit, index_kpidept,
              name_kpidept, define_kpidept, control_cek_kpidept,
              cascade_kpidept, date_cutoff, id_department,
              user_entry, last_update
            ) values (
              '$id_kpi', {$id_kpibunit}, {$index_kpidept},
              {$name_kpidept}, {$define_kpidept}, {$control_cek_kpidept},
              '$cascade_kpidept', $date_cutoff, {$department_kpi},
              '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
            );
            ";
          } else {
            $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
            $satuan_kpidept = $cleanWord->textCk(@$_POST["satuan_kpidept"][$key], true, 'normal');
            $formula_kpidept = $cleanWord->textCk(@$_POST["formula_kpidept"][$key], true, 'normal');
            $polaritas_kpidept = $cleanWord->textCk(@$_POST["polaritas_kpidept"][$key], true, 'normal');
            $userpic_kpidept = $cleanWord->textCk(@$_POST["userpic_kpidept"][$key], true, 'normal');
  
            $sql .= "INSERT INTO kpi_department_support (
              id_kpidept, name_kpidept, index_kpidept, define_kpidept, control_cek_kpidept,
              id_sobject, id_satuan, id_formula, polaritas_kpidept,
              year_kpidept, cascade_kpidept, date_cutoff,
              id_usersetup, id_company, id_department,
              user_entry, last_update
            ) values (
              '$id_kpi', {$name_kpidept}, {$index_kpidept}, {$define_kpidept}, {$control_cek_kpidept},
              {$id_sobject}, {$satuan_kpidept}, {$formula_kpidept}, {$polaritas_kpidept},
              $year_kpi, '$cascade_kpidept', $date_cutoff,
              {$userpic_kpidept}, {$company_kpi}, {$department_kpi},
              '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
            );
            ";
          }

          $arrayTargetMonth = array_filter($_POST['target_month'][$key], function($valMonth) {
            return $valMonth !== null && $valMonth !== '';
          });
          foreach ($arrayTargetMonth as $keyTarget => $valueTarget) {
            $id_kpi_target = "KDPTARGET-" . $this->year . "-" . str_pad($getPkeyTarget[$keyCountTarget]['code'], 5, '0', STR_PAD_LEFT);
            $sql .= $this->sqlInsertTarget(
              $id_kpi_target,
              $id_kpi,
              $cleanWord->numberCk(@$valueTarget, false, 'text', true),
              $cleanWord->numberCk(@$keyTarget, false, 'integer', true),
              $_SESSION['setupuser_kpi_askara'],
              $this->last_update
            );
            $keyCountTarget++;
          }
          $keyCountCode++;
        }
      }
    }

    $sql_delete = '';
    if (!empty($_POST['deleteDataKPI'])) {
      foreach (json_decode($_POST['deleteDataKPI'], true) as $key => $value) {
        $status_kpi = $cleanWord->textCk(@$value['status_kpi'], true, 'trim');
        if (!empty($value['id_kpidept'])) {
          $id_kpi = $cleanWord->textCk(@$value['id_kpidept'], true, 'normal');
          $sql_delete .= $status_kpi == 'kpi_department_corps' ? "DELETE FROM kpi_department_corps where id_kpidept = {$id_kpi};" : "DELETE FROM kpi_department_support where id_kpidept = {$id_kpi};";
          $sql_delete .= "DELETE FROM kpi_department_target where id_kpidept = {$id_kpi};";
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
      $this->sendQuery($this->konek_sita_db(), $sql_delete. $sql);
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

  public function changePIC(){
    global $cleanWordPDO;
    $sendArrayTemplate = array(
      ':userEntry' => $_SESSION['setupuser_kpi_askara'],
      ':lastUpdate' => $this->last_update,
    );
    $beginConnect = $this->konek_kpi_pdo();
    $beginConnect->beginTransaction();
    try {
      foreach ($_POST['id_changeuser'] as $key => $value) {
        $sendArray = array_merge($sendArrayTemplate, [
          ':id_' . $key => $cleanWordPDO->textCk(@$_POST["id_changeuser"][$key], true),
          ':user_' . $key => $cleanWordPDO->textCk(@$_POST["userpic_changeuser"][$key], true),
        ]);
        $sql = "UPDATE kpi_department_support SET
        id_usersetup = :user_$key,
        user_entry = :userEntry,
        last_update = :lastUpdate,
        flag = 'u'
        where id_kpidept = :id_$key;
        ";
        $this->sendQueryPDO($beginConnect, trim($sql), $sendArray);
      }
      $beginConnect->commit();
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data Berhasil diubah!"
        )
      );
    } catch (Exception $e) {
      $beginConnect->rollBack();
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan! ❌',
          'error'=>$e
        )
      );
    }
  }

  public function publishKpiDepartment(){
    global $cleanWord;
    try {

      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');
      $department_kpi = $cleanWord->textCk(@$_POST["department_kpi"], true, 'normal');

      $cek = "SELECT count(*) from kpi_department where compkpi_id = {$company_kpi} and year_kpidept = $year_kpi and deptkpi_id = {$department_kpi}";
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

      $cek = "UPDATE kpi_department_support SET terbit_kpidept = true where year_kpidept = $year_kpi and id_company = {$company_kpi} and id_department = {$department_kpi};
      UPDATE kpi_department_corps SET terbit_kpidept = true where id_department = {$department_kpi} and id_kpibunit in (
        SELECT id_kpibunit from kpi_bisnis_unit where year_kpibunit = $year_kpi and compkpi_id = {$company_kpi}
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