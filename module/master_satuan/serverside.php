<?php
class satuan_master extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonSatuan(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct id_satuan as id, name_satuan as text from satuan_master where name_satuan ilike '%$q%'";
      $cek_main = $cek . " order by id_satuan asc offset $offset limit $records_per_page";
      $query = $this->sendQuery($this->konek_sita_db(), $cek_main);
      $items = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);

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

  public function getSatuan(){
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
        'id_satuan' => 'a',
        'name_satuan' => 'a',
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
      from satuan_master a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.last_update is not null $filtering $searching";
      $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);

      $cek = "SELECT a.*, bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from satuan_master a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.last_update is not null $filtering $searching
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

  public function addSatuan(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('satuan_master', 'id_satuan', 1, 99999, 1, "SELECT split_part(id_satuan, '-', 2)::integer from satuan_master");
    $satuan_id = "UOM-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $satuan_name = $cleanWord->textCk(@$_POST["satuan_name"], true, 'upper');
    try {
      $sql = "INSERT INTO satuan_master
      (id_satuan, name_satuan, user_entry, last_update)
      values
      ('$satuan_id', {$satuan_name}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editSatuan(){
    global $cleanWord;
    $satuan_id = $cleanWord->textCk(@$_POST["satuan_id"], true, 'normal');
    $satuan_name = $cleanWord->textCk(@$_POST["satuan_name"], true, 'upper');
    try {
      $sql = "UPDATE satuan_master SET
      name_satuan = {$satuan_name},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_satuan = {$satuan_id}
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

  public function deleteSatuan(){
    global $cleanWord;
    $satuan_id = $cleanWord->textCk(@$_POST["satuan_id"], true, 'normal');
    try {
      $sql = "UPDATE satuan_master SET flag = 'd' WHERE id_satuan = {$satuan_id};
      DELETE FROM satuan_master WHERE id_satuan = {$satuan_id}";
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