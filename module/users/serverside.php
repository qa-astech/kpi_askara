<?php
class users extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }
  
  public function getUsers(){
    global $cleanWord;
    try {
      // View Column
      $viewColumn = array(
        'nik_users' => 'a',
        'username_users' => 'a',
        'fullname_users' => 'a',
        'fullname_entry' => 'bb',
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
          if (!empty($column)) {
            if ($column === 'fullname_entry') {
              $searching .= "bb.fullname_users::text ilike '%$searchValueBox%' or ";
            } else {
              $searching .= $viewColumn[$column] . ".$column::text ilike '%$searchValueBox%' or ";
            }
          }
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
        if (!empty($column)) {
          if ($column === 'fullname_entry') {
            $filtering .= "and bb.fullname_users::text ilike '%$searchValue%' ";
          } else {
            $filtering .= "and " . $viewColumn[$column] . ".$column::text ilike '%$searchValue%' ";
          }
        }
      }
      // Total
      $totalRecordsQuery = "SELECT COUNT(*)
      from users a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.nik_users is not null $filtering $searching";
      $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);

      $cek = "SELECT a.*, bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from users a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.nik_users is not null $filtering $searching
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

  public function addUsers(){
    global $cleanWord;
    $users_nik = $cleanWord->textCk(@$_POST["users_nik"], true, 'normal');
    $users_username = $cleanWord->textCk(@$_POST["users_username"], true, 'normal');
    $users_fullname = $cleanWord->textCk(@$_POST["users_fullname"], true, 'camel');
    $users_password = $cleanWord->textCk(@$_POST["users_password"], true, 'plain', "", true);
    $users_confirm_password = $cleanWord->textCk(@$_POST["users_confirm_password"], true, 'plain', "", true);
    if ($users_password !== $users_confirm_password) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Konfirmasi password tidak sama, periksa ulang!'
        )
      );
    }
    $users_password = password_hash($users_password, PASSWORD_DEFAULT);
    try {
      $sql = "INSERT INTO users
      (nik_users, username_users, fullname_users, password_users, user_entry, last_update)
      values
      ({$users_nik}, {$users_username}, {$users_fullname}, '$users_password', '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editUsers(){
    global $cleanWord;
    $users_nik_before = $cleanWord->textCk(@$_POST["users_nik_before"], true, 'normal');
    $users_nik = $cleanWord->textCk(@$_POST["users_nik"], true, 'normal');
    $users_username = $cleanWord->textCk(@$_POST["users_username"], true, 'normal');
    $users_fullname = $cleanWord->textCk(@$_POST["users_fullname"], true, 'camel');
    try {
      $sql = "UPDATE users SET
      nik_users = {$users_nik},
      username_users = {$users_username},
      fullname_users = {$users_fullname},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE nik_users = {$users_nik_before};
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

  public function resetUsers(){
    global $cleanWord;
    $users_nik = $cleanWord->textCk(@$_POST["users_nik"], true, 'normal');
    $users_password = password_hash('123456', PASSWORD_DEFAULT);
    try {
      $sql = "UPDATE users SET
      password_users = '$users_password',
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE nik_users = {$users_nik};
      ";
      $this->sendQuery($this->konek_sita_db(), $sql);
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Kata sandi akun berhasil diatur ulang!"
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

  // public function deleteUsers(){
  //   global $cleanWord;
  //   $users_nik = $cleanWord->textCk(@$_POST["users_nik"], true, 'normal');
  //   try {
  //     $sql = "UPDATE users SET flag = 'd' WHERE nik_users = {$users_nik};";
  //     $this->sendQuery($this->konek_sita_db(), $sql);
  //     return json_encode(
  //       array(
  //         'response'=>'success',
  //         'alert'=>"Data Berhasil ditambahkan!"
  //       )
  //     );
  //   } catch (Exception $e) {
  //     return json_encode(
  //       array(
  //         'response'=>'error',
  //         'alert'=>'Terjadi kesalahan! ❌'
  //       )
  //     );
  //   }
  // }

  public function jsonUsers(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct nik_users as id, fullname_users as text from users where fullname_users ilike '%$q%'";
      $cek_main = $cek . " order by nik_users asc offset $offset limit $records_per_page";
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
}
?>