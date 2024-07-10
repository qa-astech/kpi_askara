<?php
class perspective extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonPerspective(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct id_perspective as id, '(' || alias_perspective || ') ' || name_perspective as text from perspective where name_perspective ilike '%$q%'";
      $cek_main = $cek . " order by id_perspective asc offset $offset limit $records_per_page";
      $query = $this->sendQuery($this->konek_sita_db(), $cek_main);
      $items = empty(pg_fetch_all($query)) ? array() : $cleanWord->cleaningArrayHtml(pg_fetch_all($query));

      $cek_count = "SELECT count(*) from ($cek) tbl";
      $query_count = $this->sendQuery($this->konek_sita_db(), $cek_count);
      $total_count = pg_fetch_all($query_count);

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

  public function getPerspective(){
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
        'id_perspective' => 'a',
        'name_perspective' => 'a',
        'index_perspective' => 'a',
        'alias_perspective' => 'a',
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
          $searching .= !empty($column) ? $viewColumn[$column] . ".$column::text ilike '%$searchValueBox%' or " : '';
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
      from perspective a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.last_update is not null $filtering $searching";
      $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);

      $cek = "SELECT a.*, bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from perspective a
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

  public function addPerspective(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('perspective', 'id_perspective', 1, 99999, 1, "SELECT split_part(id_perspective, '-', 2)::integer from perspective");
    $perspective_id = "PERS-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $perspective_name = $cleanWord->textCk(@$_POST["perspective_name"], true, 'upper');
    $perspective_index = $cleanWord->numberCk(@$_POST["perspective_index"], true, 'integer');
    $perspective_alias = $cleanWord->textCk(@$_POST["perspective_alias"], true, 'upper');
    try {
      $sql = "INSERT INTO perspective
      (id_perspective, name_perspective, index_perspective, alias_perspective, user_entry, last_update)
      values
      ('$perspective_id', {$perspective_name}, $perspective_index, {$perspective_alias}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editPerspective(){
    global $cleanWord;
    $perspective_id = $cleanWord->textCk(@$_POST["perspective_id"], true, 'normal');
    $perspective_name = $cleanWord->textCk(@$_POST["perspective_name"], true, 'upper');
    $perspective_index = $cleanWord->numberCk(@$_POST["perspective_index"], true, 'integer');
    $perspective_alias = $cleanWord->textCk(@$_POST["perspective_alias"], true, 'upper');
    try {
      $sql = "UPDATE perspective SET
      name_perspective = {$perspective_name},
      index_perspective = $perspective_index,
      alias_perspective = {$perspective_alias},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_perspective = {$perspective_id}
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

  public function deletePerspective(){
    global $cleanWord;
    $perspective_id = $cleanWord->textCk(@$_POST["perspective_id"], true, 'normal');
    try {
      $sql = "UPDATE perspective SET flag = 'd' WHERE id_perspective = {$perspective_id};
      DELETE FROM perspective WHERE id_perspective = {$perspective_id}";
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