<?php
class company_detail extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function searchDepartmentFromCompany(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct c.id_department as id, c.name_department as text
      from company_detail a
      inner join section_master b on b.id_section = a.id_section
      inner join department_master c on c.id_department = b.id_department
      where a.id_company = {$id_company}
      and c.name_department ilike '%$q%'
      ";
      $cek_main = $cek . " order by c.name_department asc offset $offset limit $records_per_page";
      $query = $this->sendQuery($this->konek_sita_db(), $cek_main);
      $items = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQuery($this->konek_sita_db(), $cek_count);
      $total_count = pg_fetch_all($query_count);

      $response = array(
        "results" => $items,
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

  public function getDetailCompany(){
    global $cleanWord;
    $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
    try {
      // View Column
      $viewColumn = array(
        'id_det_company' => 'a',
        'name_department' => 'c',
        'name_section' => 'b',
        'name_position' => 'e',
        'name_plant' => 'd',
        'golongan' => 'a',
        'user_entry' => 'a',
        'last_update' => 'a'
      );
      // Total
      $totalRecordsQuery = "SELECT COUNT(*) FROM company_detail";
      $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);
      // Offset
      $start = $_POST['start'];
      // Limit
      $length = $_POST['length'];
      // Search Box
      $searchValueBox = $cleanWord->textCk(@$_POST['search']['value'], false, 'trim');
      $searching = '';
      if (!empty($searchValueBox)) {
        foreach ($_POST['columns'] as $key => $value) {
          $column = $value['data'];
          $searching .= !empty($column) ? $viewColumn[$column] . ".$column::text ilike '%$searchValueBox%' or " : "";
        }
        $valueOfSearch = rtrim($searching, ' or ');
        $searching = !empty($valueOfSearch) ? "and ($valueOfSearch)" : "";
      }
      // Ordering
      $ordering = '';
      foreach ($_POST['order'] as $key => $value) {
        $idxColumn = $value['column'];
        $dir = $value['dir'];
        $columnOrder = $_POST['columns'][$idxColumn]['data'];
        $ordering .= "$columnOrder $dir, ";
      }
      $ordering = rtrim($ordering, ', ');
      // Filtering Column
      $filtering = '';
      foreach ($_POST['columns'] as $key => $value) {
        $column = $value['data'];
        $searchValue = $cleanWord->textCk($value['search']['value'], false, 'trim');
        $filtering .= !empty($searchValue) ? "and " . $viewColumn[$column] . ".$column::text ilike '%$searchValue%' " : "";
      }

      $cek = "SELECT a.user_entry, a.last_update, a.id_det_company, a.golongan,
      b.id_section, b.name_section,
      c.id_department, c.name_department,
      d.id_plant, d.name_plant,
      e.id_position, e.name_position
      from company_detail a
      left join section_master b on b.id_section = a.id_section
      left join department_master c on c.id_department = b.id_department
      left join plant_master d on d.id_plant = a.id_plant
      left join position_master e on e.id_position = a.id_position
      where a.id_company = {$id_company} $filtering $searching
      order by $ordering
      offset $start limit $length
      ";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $items = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      $response = array(
        'draw' => $_POST['draw'],
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $items
      );
      return json_encode($response);
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function addDetailCompany(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('company_detail', 'id_det_company', 1, 99999, 1, "SELECT split_part(id_det_company, '-', 2)::integer from company_detail");
    $company_det_id = "COMDET-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $company_id = $cleanWord->textCk(@$_POST["company_id"], true, 'normal');
    $position_id = $cleanWord->textCk(@$_POST["position_id"], true, 'normal');
    $section_id = $cleanWord->textCk(@$_POST["section_id"], true, 'normal');
    $plant_id = $cleanWord->textCk(@$_POST["plant_id"], true, 'normal');
    $golongan = $cleanWord->numberCk(@$_POST["golongan"], true, 'integer', true);
    if ($golongan > 5 || $golongan < 1) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Maksimal golongan hanya boleh 1 - 5, silahkan koreksi kembali!'
        )
      );
    }
    try {
      $sql = "INSERT INTO company_detail
      (id_det_company, id_company, id_position, id_section, id_plant, golongan, user_entry, last_update)
      values
      ('$company_det_id', {$company_id}, {$position_id}, {$section_id}, {$plant_id}, {$golongan}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editDetailCompany(){
    global $cleanWord;
    $company_det_id = $cleanWord->textCk(@$_POST["det_company_id"], true, 'normal');
    // $company_id = $cleanWord->textCk(@$_POST["company_id"], false, 'normal');
    $position_id = $cleanWord->textCk(@$_POST["position_id"], true, 'normal');
    $section_id = $cleanWord->textCk(@$_POST["section_id"], true, 'normal');
    $plant_id = $cleanWord->textCk(@$_POST["plant_id"], true, 'normal');
    $golongan = $cleanWord->numberCk(@$_POST["golongan"], true, 'integer', true);
    if ($golongan > 5 || $golongan < 1) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Maksimal golongan hanya boleh 1 - 5, silahkan koreksi kembali!'
        )
      );
    }
    try {
      $sql = "UPDATE company_detail SET
      id_position = {$position_id},
      id_section = {$section_id},
      id_plant = {$plant_id},
      golongan = {$golongan},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_det_company = {$company_det_id}
      ";
      $this->sendQuery($this->konek_sita_db(), $sql);
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

  public function deleteDetailCompany(){
    global $cleanWord;
    $company_det_id = $cleanWord->textCk(@$_POST["det_company_id"], true, 'normal');
    try {
      $sql = "UPDATE company_detail SET flag = 'd' WHERE id_det_company = {$company_det_id};
      DELETE FROM company_detail WHERE id_det_company = {$company_det_id}";
      $this->sendQuery($this->konek_sita_db(), $sql);
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data Berhasil dihapus!"
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