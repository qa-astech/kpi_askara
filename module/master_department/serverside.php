<?php
class department_master extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonDepartment(){
    try {
      global $cleanWordPDO;
      $q = $cleanWordPDO->textCk(@$_POST["q"], false);
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct id_department as id, name_department as text from department_master where name_department ilike :search";
      $cek_main = $cek . " order by id_department asc offset :offset limit :recordsPerPage";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_main, array(
        ':search' => '%'.$q.'%',
        ':offset' => $offset,
        ':recordsPerPage' => $records_per_page
      ));
      $items = $query_main->fetchAll();

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_count, array(
        ':search' => '%'.$q.'%'
      ));
      $total_count = $query_count->fetchAll();

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

  public function getAllDepartment(){
    try {
      $cek = "SELECT * from department_master";
      $query_main = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, array());
      return json_encode($query_main->fetchAll());
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function getDepartment(){
    global $cleanWord;
    $_POST['columns'] = array_map(function($v) {
      if ($v['data'] === 'fullname_entry') {
        $v['data'] = 'fullname_users';
      }
      return $v;
    }, $_POST['columns']);
    try {
      // View Column
      $viewColumn = array(
        'id_department' => 'a',
        'name_department' => 'a',
        'alias_department' => 'a',
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
      from department_master a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.last_update is not null $filtering $searching";
      $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);

      $cek = "SELECT a.*, bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from department_master a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.last_update is not null $filtering $searching
      order by $ordering
      offset $start limit $length
      ";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $items = empty(pg_fetch_all($query)) ? array() : $cleanWord->cleaningArrayHtml(pg_fetch_all($query));
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

  public function addDepartment(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('department_master', 'id_department', 1, 99999, 1, "SELECT split_part(id_department, '-', 2)::integer from department_master");
    $department_id = "DEPT-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $department_name = $cleanWord->textCk(@$_POST["department_name"], true, 'upper');
    $department_alias = $cleanWord->textCk(@$_POST["department_alias"], false, 'upper');
    try {
      $sql = "INSERT INTO department_master
      (id_department, name_department, alias_department, user_entry, last_update)
      values
      ('$department_id', {$department_name}, {$department_alias}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editDepartment(){
    global $cleanWord;
    $department_id = $cleanWord->textCk(@$_POST["department_id"], true, 'normal');
    $department_name = $cleanWord->textCk(@$_POST["department_name"], true, 'upper');
    $department_alias = $cleanWord->textCk(@$_POST["department_alias"], false, 'upper');
    try {
      $sql = "UPDATE department_master SET
      name_department = {$department_name},
      alias_department = {$department_alias},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_department = {$department_id}
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

  public function deleteDepartment(){
    global $cleanWord;
    $department_id = $cleanWord->textCk(@$_POST["department_id"], true, 'normal');
    try {
      $sql = "UPDATE section_master SET flag = 'd' WHERE id_department = {$department_id};
      DELETE FROM section_master WHERE id_department = {$department_id};
      UPDATE department_master SET flag = 'd' WHERE id_department = {$department_id};
      DELETE FROM department_master WHERE id_department = {$department_id};";
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