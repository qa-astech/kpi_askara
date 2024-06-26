<?php
class menu_access extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getMenuAccess(){
    global $cleanWord;
    try {
      // View Column
      $viewColumn = array(
        'code_maccess' => 'a',
        'code_menu' => 'a',
        'name_maccess' => 'a',
        'user_entry' => 'a',
        'last_update' => 'a'
      );
      $code_menu = $cleanWord->textCk(@$_POST["code_menu"], true, 'normal');
      // Total
      $totalRecordsQuery = "SELECT COUNT(*) FROM menu_access";
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
          $searching .= $viewColumn[$column] . ".$column::text ilike '%$searchValueBox%' or ";
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

      $cek = "SELECT a.*, c.username_users nickname_entry, c.fullname_users fullname_entry
      from menu_access a
      left join all_users_setup b on b.id_usersetup = a.user_entry
      left join users c on c.nik_users = b.nik
      where a.last_update is not null and a.code_menu = $code_menu $filtering $searching
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

  public function addMenuAccess(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('menu_access', 'code_maccess', 1, 99999, 1, "SELECT split_part(code_maccess, '-', 2)::integer from menu_access");
    $code_maccess = "MNA-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $code_menu = $cleanWord->textCk(@$_POST["code_menu"], true, 'normal');
    $name_maccess = $cleanWord->textCk(@$_POST["name_maccess"], true, 'normal');
    try {
      $sql = "INSERT INTO menu_access
      (code_maccess, code_menu, name_maccess, user_entry, last_update)
      values
      ('$code_maccess', {$code_menu}, {$name_maccess}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editMenuAccess(){
    global $cleanWord;
    $code_maccess = $cleanWord->textCk(@$_POST["code_maccess"], true, 'normal');
    $name_maccess = $cleanWord->textCk(@$_POST["name_maccess"], true, 'normal');
    try {
      $sql = "UPDATE menu_access SET
      name_maccess = {$name_maccess},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE code_maccess = {$code_maccess}
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

  public function deleteMenuAccess(){
    global $cleanWord;
    $code_maccess = $cleanWord->textCk(@$_POST["code_maccess"], true, 'normal');
    try {
      $sql = "UPDATE menu_access SET flag = 'd' WHERE code_maccess = {$code_maccess};
      DELETE FROM menu_access WHERE code_maccess = {$code_maccess}";
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