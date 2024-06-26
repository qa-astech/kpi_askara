<?php
class kpi_corporate extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getKpiCorporate(){
    global $cleanWord;
    try {
      $year_kpi_corp = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $copy_kpi = $cleanWord->textCk(@$_POST["copy_kpi"], true, 'trim');
      $sql_copy = $copy_kpi == 'copy' ? "and a.terbit_kpicorp = true" : '';
      $year_baseline = $cleanWord->numberCk(@$_POST["year_baseline"], true, 'integer');
      $cek = "SELECT distinct a.*, '$copy_kpi'::varchar status_copy,
      reverse(substring(reverse(a.index_kpicorp), position('.' in reverse(a.index_kpicorp)) + 1)) index_parent,
      c.name_perspective, c.index_perspective, c.alias_perspective, d.name_satuan, e.name_formula, b.index_sobject,
      (c.alias_perspective || b.index_sobject || '. ' || b.name_sobject)::varchar text_sobject,
      ('(' || c.alias_perspective || ') ' || c.name_perspective)::varchar text_perspective,
      f.target_kpicorp baseline_1, g.target_kpicorp baseline_2, h.target_kpicorp baseline_3,
      STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] arr_index, bb.username_users nickname_entry, bb.fullname_users fullname_entry
      from kpi_corporate a
      left join strategic_objective b on b.id_sobject = a.id_sobject
      left join perspective c on c.id_perspective = b.id_perspective
      left join satuan_master d on d.id_satuan = a.id_satuan
      left join formula_master e on e.id_formula = a.id_formula
      left join kpi_corporate f on f.id_sobject = a.id_sobject and f.name_kpicorp = a.name_kpicorp and f.year_kpicorp = ($year_baseline - 1)
      left join kpi_corporate g on g.id_sobject = a.id_sobject and g.name_kpicorp = a.name_kpicorp and g.year_kpicorp = ($year_baseline - 2)
      left join kpi_corporate h on h.id_sobject = a.id_sobject and h.name_kpicorp = a.name_kpicorp and h.year_kpicorp = ($year_baseline - 3)
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.year_kpicorp = $year_kpi_corp $sql_copy
      order by c.index_perspective asc, STRING_TO_ARRAY(a.index_kpicorp, '.')::INT[] asc";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = empty(pg_fetch_all($query)) ? array() : $cleanWord->cleaningArrayHtml(pg_fetch_all($query));
      return json_encode($response);

    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function addKpiCorporate(){
    global $cleanWord;
    $countData = count($_POST['index_kpi_corp']);
    $year_kpi_corp = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
    $getPkey = $this->getSeriesPkey('kpi_corporate', 'id_kpicorp', 1, 99999, $countData, "SELECT split_part(id_kpicorp, '-', 3)::integer from kpi_corporate where split_part(id_kpicorp, '-', 2)::varchar = '". $this->year ."'");
    $sql = "INSERT INTO kpi_corporate
    (id_kpicorp, name_kpicorp, index_kpicorp, year_kpicorp, define_kpicorp, control_cek_kpicorp, target_kpicorp, id_sobject, id_satuan, id_formula, polaritas_kpicorp, user_entry, last_update)
    values";
    foreach ($_POST['index_kpi_corp'] as $key => $value) {
      $kpicorp_id = "KCO-" . $this->year . "-" . str_pad($getPkey[$key]['code'], 5, '0', STR_PAD_LEFT);
      $index_kpi_corp = $cleanWord->textCk(@$value, true, 'normal');
      $name_kpi_corp = $cleanWord->textCk(@$_POST["name_kpi_corp"][$key], true, 'normal');
      $define_kpi_corp = $cleanWord->textCk(@$_POST["define_kpi_corp"][$key], true, 'normal');
      $control_cek_kpi_corp = $cleanWord->textCk(@$_POST["control_cek_kpi_corp"][$key], true, 'normal');
      $target_kpi_corp = $cleanWord->numberCk(@$_POST["target_kpi_corp"][$key], true, 'text', true);
      $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
      $satuan_kpi_corp = $cleanWord->textCk(@$_POST["satuan_kpi_corp"][$key], true, 'normal');
      $formula_kpi_corp = $cleanWord->textCk(@$_POST["formula_kpi_corp"][$key], true, 'normal');
      $polaritas_kpi_corp = $cleanWord->textCk(@$_POST["polaritas_kpi_corp"][$key], true, 'normal');

      $sql .= "
      ('$kpicorp_id', {$name_kpi_corp}, {$index_kpi_corp}, $year_kpi_corp, $define_kpi_corp, $control_cek_kpi_corp, $target_kpi_corp, {$id_sobject}, {$satuan_kpi_corp}, {$formula_kpi_corp}, {$polaritas_kpi_corp}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'),";
    }
    $sql = rtrim($sql, ',') . ';';
    try {
      $this->sendQuery($this->konek_sita_db(), $sql);
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data Berhasil ditambahkan!"
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

  public function editKpiCorporate(){
    global $cleanWord, $upload;
    $sql = "";
    $keyCountCode = 0;
    $year_kpi_corp = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');

    if (!empty($_POST['index_kpi_corp'])) {
      $countNewCode = count(array_map(function($value) {
        return empty($value);
      }, $_POST['kpicorp_id']));
      $getPkey = $this->getSeriesPkey(
        'kpi_corporate', 'id_kpicorp', 1, 99999, $countNewCode, "SELECT split_part(id_kpicorp, '-', 3)::integer from kpi_corporate where split_part(id_kpicorp, '-', 2)::varchar = '". $this->year ."'"
      );

      foreach ($_POST['index_kpi_corp'] as $key => $value) {
        if (!empty($_POST['kpicorp_id'][$key])) {
          $kpicorp_id = $cleanWord->textCk(@$_POST["kpicorp_id"][$key], true, 'normal');
          $index_kpi_corp = $cleanWord->textCk(@$value, true, 'normal');
          $name_kpi_corp = $cleanWord->textCk(@$_POST["name_kpi_corp"][$key], true, 'normal');
          $define_kpi_corp = $cleanWord->textCk(@$_POST["define_kpi_corp"][$key], true, 'normal');
          $control_cek_kpi_corp = $cleanWord->textCk(@$_POST["control_cek_kpi_corp"][$key], true, 'normal');
          $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
          $satuan_kpi_corp = $cleanWord->textCk(@$_POST["satuan_kpi_corp"][$key], true, 'normal');
          $target_kpi_corp = $cleanWord->numberCk(@$_POST["target_kpi_corp"][$key], true, 'text', true);
          $formula_kpi_corp = $cleanWord->textCk(@$_POST["formula_kpi_corp"][$key], true, 'normal');
          $polaritas_kpi_corp = $cleanWord->textCk(@$_POST["polaritas_kpi_corp"][$key], true, 'normal');
  
          $sql .= "UPDATE kpi_corporate SET
          name_kpicorp = {$name_kpi_corp},
          index_kpicorp = {$index_kpi_corp},
          year_kpicorp = $year_kpi_corp,
          define_kpicorp = {$define_kpi_corp},
          control_cek_kpicorp = {$control_cek_kpi_corp},
          target_kpicorp = $target_kpi_corp,
          id_sobject = {$id_sobject},
          id_satuan = {$satuan_kpi_corp},
          id_formula = {$formula_kpi_corp},
          polaritas_kpicorp = {$polaritas_kpi_corp},
          user_entry = '$_SESSION[setupuser_kpi_askara]',
          last_update = '".$this->last_update."',
          flag = 'u'
          WHERE id_kpicorp = {$kpicorp_id};
          ";
        }
        if (empty($_POST['kpicorp_id'][$key])) {
          $kpicorp_id = "KCO-" . $this->year . "-" . str_pad($getPkey[$keyCountCode]['code'], 5, '0', STR_PAD_LEFT);
          $index_kpi_corp = $cleanWord->textCk(@$value, true, 'normal');
          $name_kpi_corp = $cleanWord->textCk(@$_POST["name_kpi_corp"][$key], true, 'normal');
          $define_kpi_corp = $cleanWord->textCk(@$_POST["define_kpi_corp"][$key], true, 'normal');
          $control_cek_kpi_corp = $cleanWord->textCk(@$_POST["control_cek_kpi_corp"][$key], true, 'normal');
          $id_sobject = $cleanWord->textCk(@$_POST["id_sobject"][$key], true, 'normal');
          $satuan_kpi_corp = $cleanWord->textCk(@$_POST["satuan_kpi_corp"][$key], true, 'normal');
          $target_kpi_corp = $cleanWord->numberCk(@$_POST["target_kpi_corp"][$key], true, 'text', true);
          $formula_kpi_corp = $cleanWord->textCk(@$_POST["formula_kpi_corp"][$key], true, 'normal');
          $polaritas_kpi_corp = $cleanWord->textCk(@$_POST["polaritas_kpi_corp"][$key], true, 'normal');
          $sql .= "INSERT INTO kpi_corporate (
            id_kpicorp, name_kpicorp, index_kpicorp, year_kpicorp, define_kpicorp, control_cek_kpicorp, target_kpicorp, id_sobject, id_satuan, id_formula, polaritas_kpicorp, user_entry, last_update
          ) values (
            '$kpicorp_id', {$name_kpi_corp}, {$index_kpi_corp}, $year_kpi_corp, {$define_kpi_corp}, {$control_cek_kpi_corp}, $target_kpi_corp, {$id_sobject}, {$satuan_kpi_corp}, {$formula_kpi_corp}, {$polaritas_kpi_corp}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
          );
          ";
          $keyCountCode++;
        }
      }
    }

    $sql_delete = "";
    if (!empty($_POST['deleteDataKPI'])) {
      foreach (json_decode($_POST['deleteDataKPI'], true) as $key => $value) {
        $id_kpi = $cleanWord->textCk(@$value['id_kpicorp'], true, 'normal');
        $sql_delete .= "DELETE FROM kpi_corporate where id_kpicorp = {$id_kpi};";
      }
    }

    // if (empty(trim($sql_delete . $sql))) {
    //   return json_encode(
    //     array(
    //       'response'=>'success',
    //       'alert'=>"Data tidak ada perubahan!"
    //     )
    //   );
    // }

    try {
      $this->sendQuery($this->konek_sita_db(), $sql_delete . $sql);
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data berhasil diubah!"
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

  public function publishKpiCorporate(){
    global $cleanWord;
    try {
      $year_kpi_corp = $cleanWord->numberCk(@$_POST["year_kpi"], true, 'integer');
      $cek = "UPDATE kpi_corporate SET terbit_kpicorp = true where year_kpicorp = $year_kpi_corp";
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