<?php
class menu extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function searchIndexMenu(){
    global $cleanWord;
    $q = $cleanWord->textCk(@$_POST["q"], true, 'trim');
    try {
      $cek = "SELECT count(*) from menu where index_menu::varchar like '$q%'";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $response = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      return json_encode(array(
        'status' => $response[0]['count'] > 1 ? 'Sub-Induk' : 'Turunan'
      ));
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function getMenu(){
    global $cleanWord;
    try {
      // View Column
      // $viewColumn = array(
      //   'code_menu' => 'a',
      //   'index_menu' => 'a',
      //   'title_menu' => 'a',
      //   'link_menu' => 'a',
      //   'icon_menu' => 'a',
      //   'user_entry' => 'a',
      //   'last_update' => 'a'
      // );
      // Total
      // $totalRecordsQuery = "SELECT COUNT(*) FROM menu";
      // $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      // $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);
      // Offset
      // $start = $_POST['start'];
      // Limit
      // $length = $_POST['length'];
      // Search Box
      // $searchValueBox = $cleanWord->textCk(@$_POST['search']['value'], false, 'trim');
      // $searching = '';
      // if (!empty($searchValueBox)) {
      //   foreach ($_POST['columns'] as $key => $value) {
      //     $column = $value['data'];
      //     $searching .= $viewColumn[$column] . ".$column::text ilike '%$searchValueBox%' or ";
      //   }
      //   $valueOfSearch = rtrim($searching, ' or ');
      //   $searching = !empty($valueOfSearch) ? "and ($valueOfSearch)" : "";
      // }
      // Ordering
      // $ordering = '';
      // foreach ($_POST['order'] as $key => $value) {
      //   $idxColumn = $value['column'];
      //   $dir = $value['dir'];
      //   $columnOrder = $_POST['columns'][$idxColumn]['data'];
      //   $ordering .= "$columnOrder $dir, ";
      // }
      // $ordering = rtrim($ordering, ', ');
      // Filtering Column
      // $filtering = '';
      // foreach ($_POST['columns'] as $key => $value) {
      //   $column = $value['data'];
      //   $searchValue = $cleanWord->textCk($value['search']['value'], false, 'trim');
      //   $filtering .= !empty($searchValue) ? "and " . $viewColumn[$column] . ".$column::text ilike '%$searchValue%' " : "";
      // }

      // $cek = "SELECT a.* from menu a
      // where a.last_update is not null $filtering $searching
      // order by $ordering
      // offset $start limit $length
      // ";
      $cek = "SELECT a.*, c.username_users nickname_entry, c.fullname_users fullname_entry
      FROM menu a
      left join all_users_setup b on b.id_usersetup = a.user_entry
      left join users c on c.nik_users = b.nik
      where a.last_update is not null
      order by STRING_TO_ARRAY(a.index_menu, '.')::INT[] asc";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $items = empty(pg_fetch_all($query)) ? array() : pg_fetch_all($query);
      // $response = array(
      //   'draw' => $_POST['draw'],
      //   'recordsTotal' => $totalRecords,
      //   'recordsFiltered' => $totalRecords,
      //   'data' => $items
      // );
      $response = array(
        'data' => $items
      );
      return json_encode($response);
    } catch (Exception $e) {
      $response = array();
      return json_encode($response);
    }
  }

  public function addMenu(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('menu', 'code_menu', 1, 99999, 1, "SELECT split_part(code_menu, '-', 2)::integer from menu");
    $getPkeyAccess = $this->getSeriesPkey('menu_access', 'code_maccess', 1, 99999, 4, "SELECT split_part(code_maccess, '-', 2)::integer from menu_access");
    $arrAccessDefault = ['add', 'edit', 'delete', 'view'];
    $code_menu = "MN-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $index_menu = $cleanWord->textCk(@$_POST["index_menu"], true, 'normal');
    $title_menu = $cleanWord->textCk(@$_POST["title_menu"], true, 'normal');
    $link_menu = $cleanWord->textCk(@$_POST["link_menu"], false, 'normal');
    $icon_menu = $cleanWord->textCk(@$_POST["icon_menu"], true, 'normal');
    try {
      $sql = "INSERT INTO menu
      (code_menu, index_menu, title_menu, link_menu, icon_menu, user_entry, last_update)
      values
      ('$code_menu', {$index_menu}, {$title_menu}, {$link_menu}, {$icon_menu}, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."');
      INSERT INTO menu_access
      (code_maccess, code_menu, name_maccess, user_entry, last_update)
      values
      ";
      foreach ($getPkeyAccess as $key => $value) {
        $maccess_code = "MNA-" . str_pad($getPkeyAccess[$key]['code'], 5, '0', STR_PAD_LEFT);
        $sql .= "('$maccess_code', '$code_menu', '".$arrAccessDefault[$key]."', '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'),";
      }
      $sql = rtrim($sql, ',') . ";";
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

  public function editMenu(){
    global $cleanWord;
    $code_menu = $cleanWord->textCk(@$_POST["code_menu"], true, 'normal');
    $index_menu = $cleanWord->textCk(@$_POST["index_menu"], true, 'normal');
    $title_menu = $cleanWord->textCk(@$_POST["title_menu"], true, 'normal');
    $link_menu = $cleanWord->textCk(@$_POST["link_menu"], false, 'normal');
    $icon_menu = $cleanWord->textCk(@$_POST["icon_menu"], true, 'normal');
    try {
      $sql = "UPDATE menu SET
      index_menu = {$index_menu},
      title_menu = {$title_menu},
      link_menu = {$link_menu},
      icon_menu = {$icon_menu},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE code_menu = {$code_menu}
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

  public function deleteMenu(){
    global $cleanWord;
    $code_menu = $cleanWord->textCk(@$_POST["code_menu"], true, 'normal');
    try {
      $sql = "UPDATE menu_access SET flag = 'd' WHERE code_menu = {$code_menu};
      DELETE FROM menu_access WHERE code_menu = {$code_menu};
      UPDATE menu SET flag = 'd' WHERE code_menu = {$code_menu};
      DELETE FROM menu WHERE code_menu = {$code_menu}";
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