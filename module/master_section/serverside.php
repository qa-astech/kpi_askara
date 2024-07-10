<?php
error_reporting(0);
class section_master extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonSection(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 6;
      $offset = ($page - 1) * $records_per_page;

      $cek_dept = "SELECT distinct b.id_department, b.name_department
      from section_master a
      inner join department_master b on b.id_department = a.id_department
      where (a.name_section ilike '%$q%' or b.name_department ilike '%$q%')";
      $cek_dept_main = $cek_dept . " offset $offset limit $records_per_page";
      $query_dept = $this->sendQuery($this->konek_sita_db(), $cek_dept_main);
      $items_opt = empty(pg_fetch_all($query_dept)) ? array() : pg_fetch_all($query_dept);
      $id_dept_search = '';
      foreach ($items_opt as $key => $value) {
        $id_dept_search .= "'" . $value['id_department'] . "',";
      }
      $id_dept_search = rtrim($id_dept_search, ",");

      $cek_count = "SELECT count(*) from ($cek_dept) tbl";
      $query_count = $this->sendQuery($this->konek_sita_db(), $cek_count);
      $total_count = pg_fetch_all($query_count);

      $cek_section = "SELECT distinct id_section as id, name_section as text, id_department
      from section_master
      where id_department in ($id_dept_search)";
      $query_section = $this->sendQuery($this->konek_sita_db(), $cek_section);
      $items_sec = empty(pg_fetch_all($query_section)) ? array() : pg_fetch_all($query_section);
      
      $response = array();
      $response['results'] = [];
      foreach ($items_opt as $key => $value) {
        $data = array();
        $data['text'] = $value['name_department'];
        $data['children'] = [];
        foreach ($items_sec as $keyChild => $valueChild) {
          if ($value['id_department'] == $valueChild['id_department']) {
            $items_sec[$keyChild]['templateText'] = $value['name_department'] . " - " . $valueChild['text'];
            array_push($data['children'], $items_sec[$keyChild]);
          }
        }
        if (!empty($data['children'])) {
          array_push($response['results'], $data);
        }
      }
      $response['pagination']['more'] = ($page * $records_per_page) < $total_count[0]['count'];
      return json_encode($response);
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function getSection(){
    global $cleanWord;
    $_POST['columns'] = array_map(function($v) {
      if ($v['data'] === 'fullname_entry') {
        $v['data'] = 'fullname_users';
      }
      return $v;
    }, $_POST['columns']);
    try {
      $id_department = $cleanWord->textCk(@$_POST["id_department"], true, 'normal');
      // View Column
      $viewColumn = array(
        'id_section' => 'a',
        'name_section' => 'a',
        'id_department' => 'a',
        'fullname_users' => 'bb',
        'last_update' => 'a'
      );
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

      // Total
      $totalRecordsQuery = "SELECT COUNT(*)
      from section_master a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.id_department = {$id_department} $filtering $searching";
      $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);

      $cek = "SELECT a.*, bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from section_master a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.id_department = {$id_department} $filtering $searching
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

  public function addSection(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('section_master', 'id_section', 1, 99999, 1, "SELECT split_part(id_section, '-', 2)::integer from section_master");
    $section_id = "SEC-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $section_name = $cleanWord->textCk(@$_POST["section_name"], true, 'upper');
    $department_id = $cleanWord->textCk(@$_POST["department_id"], false, 'normal');
    try {
      $sql = "INSERT INTO section_master
      (id_section, name_section, id_department, user_entry, last_update)
      values
      ('$section_id', {$section_name}, {$department_id}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editSection(){
    global $cleanWord;
    $section_id = $cleanWord->textCk(@$_POST["section_id"], true, 'normal');
    $section_name = $cleanWord->textCk(@$_POST["section_name"], true, 'upper');
    $department_id = $cleanWord->textCk(@$_POST["department_id"], false, 'normal');
    try {
      $sql = "UPDATE section_master SET
      name_section = {$section_name},
      id_department = {$department_id},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_section = {$section_id}
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

  public function deleteSection(){
    global $cleanWord;
    $section_id = $cleanWord->textCk(@$_POST["section_id"], true, 'normal');
    try {
      $sql = "UPDATE section_master SET flag = 'd' WHERE id_section = {$section_id};
      DELETE FROM section_master WHERE id_section = {$section_id}";
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