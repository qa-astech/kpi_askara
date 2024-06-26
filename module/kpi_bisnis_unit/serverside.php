<?php
class kpi_bisnis_unit extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getKpiBisnisUnit(){
    global $cleanWord;
    try {
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');
      
      $cekup_kpi = "SELECT distinct a.*,
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
      where a.year_kpibunit = $year_kpi
      and a.compkpi_id = {$company_kpi}
      order by a.index_perspective asc, STRING_TO_ARRAY(a.index_kpibunit, '.')::INT[] asc";
      $query_kpi = $this->sendQuery($this->konek_sita_db(), $cekup_kpi);
      $response_kpi = pg_fetch_all($query_kpi);
      if (empty($response_kpi)) {
        $cek = "SELECT distinct
        'kpi_bisnis_unit_corps'::varchar status_kpi, null::varchar id_kpibunit, a.id_kpicorp, a.name_kpicorp name_kpibunit, a.index_kpicorp index_kpibunit,
        a.define_kpicorp define_kpibunit, a.control_cek_kpicorp control_cek_kpibunit, a.polaritas_kpicorp polaritas_kpibunit,
        a.year_kpicorp year_kpibunit, null::boolean terbit_kpibunit, null::varchar cascade_kpibunit, null::text[] month_kpibunit,
        null::double precision target_kpibunit, a.target_kpicorp,
        null::varchar id_usersetup, null::varchar fullname_users,
        null::varchar id_department, null::varchar name_department,
        null::varchar id_company, null::varchar name_company,
        b.id_sobject, b.name_sobject, b.index_sobject,
        c.id_perspective, c.name_perspective, c.index_perspective, c.alias_perspective,
        d.id_satuan, d.name_satuan,
        e.id_formula, e.name_formula,
        reverse(substring(reverse(a.index_kpicorp), position('.' in reverse(a.index_kpicorp)) + 1)) index_parent,
        (c.alias_perspective || b.index_sobject || '. ' || b.name_sobject)::varchar text_sobject,
        ('(' || c.alias_perspective || ') ' || c.name_perspective)::varchar text_perspective,
        f.target_kpibunit baseline_1, STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] arr_index,
        null::varchar user_entry, null::timestamp last_update, null::varchar nickname_entry, null::varchar fullname_entry
        from kpi_corporate a
        left join strategic_objective b on b.id_sobject = a.id_sobject
        left join perspective c on c.id_perspective = b.id_perspective
        left join satuan_master d on d.id_satuan = a.id_satuan
        left join formula_master e on e.id_formula = a.id_formula
        left join kpi_bisnis_unit f
          on f.id_sobject = a.id_sobject
          and f.name_kpibunit = a.name_kpicorp
          and f.compkpi_id = {$company_kpi}::varchar
          and f.year_kpibunit = (a.year_kpicorp - 1)
        where a.year_kpicorp = $year_kpi and a.terbit_kpicorp is true
        order by c.index_perspective asc, STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] asc";
        $query = $this->sendQuery($this->konek_sita_db(), $cek);
        $response = empty(pg_fetch_all($query)) ? array() : $cleanWord->cleaningArrayHtml(pg_fetch_all($query));
        return json_encode($response);
      } else {
        return json_encode($response_kpi);
      }
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function editKpiBisnisUnit(){
    global $cleanWord, $upload;
    $sql = "";
    $keyCountCode = 0;
    $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
    $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');

    if (!empty($_POST['index_kpibunit'])) {

      $countNewCode = count(array_map(function($value) {
        return empty($value);
      }, $_POST['id_kpibunit']));
      $getPkey = $this->getSeriesPkey(
        'kpi_bisnis_unit', 'id_kpibunit', 1, 99999, $countNewCode, "SELECT split_part(id_kpibunit, '-', 3)::integer from kpi_bisnis_unit where split_part(id_kpibunit, '-', 2)::varchar = '". $this->year ."'"
      );

      foreach ($_POST['index_kpibunit'] as $key => $value) {
        $name_kpibunit = $cleanWord->textCk(@$_POST["name_kpibunit"][$key], true, 'normal');
        $define_kpibunit = $cleanWord->textCk(@$_POST["define_kpibunit"][$key], true, 'normal');
        $control_cek_kpibunit = $cleanWord->textCk(@$_POST["control_cek_kpibunit"][$key], true, 'normal');
        $status_kpi = $cleanWord->textCk(@$_POST["status_kpi"][$key], false, 'trim');
        $userpic_kpibunit = $cleanWord->textCk(@$_POST["userpic_kpibunit"][$key], true, 'normal');
        $index_kpibunit = $cleanWord->textCk(@$_POST["index_kpibunit"][$key], true, 'normal');
        $target_kpicorp = $cleanWord->numberCk(@$_POST["target_kpicorp"][$key], true, 'text', true);

        $month_kpi = array();
        foreach ($_POST["month_kpibunit"][$key] as $keyChild => $valueChild) {
          if ($valueChild == 'true') {
            array_push($month_kpi, $keyChild);
          }
        }
        $month_kpi = implode(',', $month_kpi);

        if (empty($month_kpi)) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Penentuan bulan pengisian harus diisi! silahkan cek terlebih dahulu!'
            )
          );
        }

        if (intval($target_kpicorp) === 0) {
          $cascade_kpibunit = empty($_POST["cascade_kpibunit"][$key]) ? 'full-round' : $cleanWord->textCk(@$_POST["cascade_kpibunit"][$key], true, 'trim');
        } else {
          $cascade_kpibunit = $cleanWord->textCk(@$_POST["cascade_kpibunit"][$key], true, 'trim');
        }

        if ($cascade_kpibunit === 'half-round') {
          $target_kpibunit = $cleanWord->numberCk(@$_POST["target_kpibunit"][$key], true, 'double', true);
          if ($target_kpibunit == floatval($target_kpicorp)) {
            return json_encode(
              array(
                'response'=>'error',
                'alert'=>'Target bisnis unit tidak boleh sama dengan target korporat (untuk half-round)'
              )
            );
          }
        }

        if (!empty($_POST['id_kpibunit'][$key])) {
          $id_kpi = $cleanWord->textCk(@$_POST["id_kpibunit"][$key], true, 'normal');
          if ($status_kpi == 'kpi_bisnis_unit_corps') {
            $target_kpibunit = $cascade_kpibunit == 'half-round' ? $cleanWord->numberCk(@$_POST["target_kpibunit"][$key], true, 'text', true) : 'null';
            $sql .= "UPDATE kpi_bisnis_unit_corps SET
            index_kpibunit = {$index_kpibunit},
            name_kpibunit = {$name_kpibunit},
            define_kpibunit = {$define_kpibunit},
            control_cek_kpibunit = {$control_cek_kpibunit},
            target_kpibunit = $target_kpibunit,
            cascade_kpibunit = '$cascade_kpibunit',
            month_kpibunit = '{{$month_kpi}}',
            id_usersetup = {$userpic_kpibunit},
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            where id_kpibunit = {$id_kpi};
            ";
          } else {
            $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
            $satuan_kpibunit = $cleanWord->textCk(@$_POST["satuan_kpibunit"][$key], true, 'normal');
            $formula_kpibunit = $cleanWord->textCk(@$_POST["formula_kpibunit"][$key], true, 'normal');
            $polaritas_kpibunit = $cleanWord->textCk(@$_POST["polaritas_kpibunit"][$key], true, 'normal');
            $target_kpibunit = $cleanWord->numberCk(@$_POST["target_kpibunit"][$key], true, 'text', true);
            $sql .= "UPDATE kpi_bisnis_unit_support SET
            id_sobject = {$id_sobject},
            index_kpibunit = {$index_kpibunit},
            name_kpibunit = {$name_kpibunit},
            define_kpibunit = {$define_kpibunit},
            control_cek_kpibunit = {$control_cek_kpibunit},
            id_satuan = {$satuan_kpibunit},
            id_formula = {$formula_kpibunit},
            polaritas_kpibunit = {$polaritas_kpibunit},
            target_kpibunit = $target_kpibunit,
            cascade_kpibunit = '$cascade_kpibunit',
            month_kpibunit = '{{$month_kpi}}',
            id_usersetup = {$userpic_kpibunit},
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            where id_kpibunit = {$id_kpi};
            ";
          }
        }
  
        if (empty($_POST['id_kpibunit'][$key])) {
          $id_kpi = "KBU-" . $this->year . "-" . str_pad($getPkey[$keyCountCode]['code'], 5, '0', STR_PAD_LEFT);
          if (!empty($_POST["id_kpicorp"][$key])) {
            $id_kpicorp = $cleanWord->textCk(@$_POST["id_kpicorp"][$key], true, 'normal');
            $target_kpibunit = $cascade_kpibunit == 'half-round' ? $cleanWord->numberCk(@$_POST["target_kpibunit"][$key], true, 'text', true) : 'null';
            $sql .= "INSERT INTO kpi_bisnis_unit_corps (
              id_kpibunit, id_kpicorp, index_kpibunit,
              name_kpibunit, define_kpibunit, control_cek_kpibunit,
              target_kpibunit, cascade_kpibunit, month_kpibunit,
              id_usersetup, id_company,
              user_entry, last_update
            ) values (
              '$id_kpi', {$id_kpicorp}, {$index_kpibunit},
              {$name_kpibunit}, {$define_kpibunit}, {$control_cek_kpibunit},
              $target_kpibunit, '$cascade_kpibunit', '{{$month_kpi}}',
              {$userpic_kpibunit}, {$company_kpi},
              '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
            );
            ";
          } else {
            $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
            $satuan_kpibunit = $cleanWord->textCk(@$_POST["satuan_kpibunit"][$key], true, 'normal');
            $formula_kpibunit = $cleanWord->textCk(@$_POST["formula_kpibunit"][$key], true, 'normal');
            $polaritas_kpibunit = $cleanWord->textCk(@$_POST["polaritas_kpibunit"][$key], true, 'normal');
            $target_kpibunit = $cleanWord->numberCk(@$_POST["target_kpibunit"][$key], true, 'text', true);
            $sql .= "INSERT INTO kpi_bisnis_unit_support (
              id_kpibunit, name_kpibunit, index_kpibunit,
              define_kpibunit, control_cek_kpibunit, target_kpibunit,
              id_sobject, id_satuan, id_formula, polaritas_kpibunit,
              year_kpibunit, cascade_kpibunit, month_kpibunit,
              id_usersetup, id_company,
              user_entry, last_update
            ) values (
              '$id_kpi', {$name_kpibunit}, {$index_kpibunit},
              {$define_kpibunit}, {$control_cek_kpibunit}, $target_kpibunit,
              {$id_sobject}, {$satuan_kpibunit}, {$formula_kpibunit}, {$polaritas_kpibunit},
              $year_kpi, '$cascade_kpibunit', '{{$month_kpi}}',
              {$userpic_kpibunit}, {$company_kpi},
              '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
            );
            ";
          }
  
          $keyCountCode++;
        }
      }
    }

    $sql_delete = '';
    if (!empty($_POST['deleteDataKPI'])) {
      foreach (json_decode($_POST['deleteDataKPI'], true) as $key => $value) {
        $status_kpi = $cleanWord->textCk(@$value['status_kpi'], true, 'trim');
        if (!empty($value['id_kpibunit'])) {
          $id_kpi = $cleanWord->textCk(@$value['id_kpibunit'], true, 'normal');
          $sql_delete .= $status_kpi == 'kpi_bisnis_unit_corps' ? "DELETE FROM kpi_bisnis_unit_corps where id_kpibunit = {$id_kpi};" : "DELETE FROM kpi_bisnis_unit_support where id_kpibunit = {$id_kpi};";
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

  public function publishKpiBisnisUnit(){
    global $cleanWord;
    try {
      $year_kpi = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $company_kpi = $cleanWord->textCk(@$_POST["company_kpi"], true, 'normal');

      $cek = "SELECT count(*) from kpi_bisnis_unit where compkpi_id = {$company_kpi} and year_kpibunit = $year_kpi";
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

      $update = "UPDATE kpi_bisnis_unit_support SET terbit_kpibunit = true where year_kpibunit = $year_kpi and id_company = {$company_kpi};
      UPDATE kpi_bisnis_unit_corps SET terbit_kpibunit = true where id_company = {$company_kpi} and id_kpicorp in (
        SELECT id_kpicorp from kpi_corporate where year_kpicorp = $year_kpi
      );";
      $this->sendQuery($this->konek_sita_db(), $update);

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