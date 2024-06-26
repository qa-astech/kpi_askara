<?php
class strategic_objective extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonStrategicObjective(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 6;
      $offset = ($page - 1) * $records_per_page;
      $sobject_name = "(b.alias_perspective || a.index_sobject || '. ' || a.name_sobject)";

      $cek_pers = "SELECT distinct b.id_perspective, b.name_perspective, b.index_perspective
      from strategic_objective a
      inner join perspective b on b.id_perspective = a.id_perspective
      where (a.name_sobject ilike '%$q%' or $sobject_name ilike '%$q%')
      order by index_perspective asc";
      $cek_pers_main = $cek_pers . " offset $offset limit $records_per_page";
      $query_pers = $this->sendQuery($this->konek_sita_db(), $cek_pers_main);
      $items_opt = empty(pg_fetch_all($query_pers)) ? array() : pg_fetch_all($query_pers);
      $id_pers_search = '';
      foreach ($items_opt as $key => $value) {
        $id_pers_search .= "'" . $value['id_perspective'] . "',";
      }
      $id_pers_search = rtrim($id_pers_search, ",");

      $cek_count = "SELECT count(*) from ($cek_pers) tbl";
      $query_count = $this->sendQuery($this->konek_sita_db(), $cek_count);
      $total_count = pg_fetch_all($query_count);

      $cek_section = "SELECT distinct a.id_sobject as id, $sobject_name as text,
      a.id_perspective, b.name_perspective, b.index_perspective,
      b.alias_perspective, a.index_sobject
      from strategic_objective a
      inner join perspective b on b.id_perspective = a.id_perspective
      where a.id_perspective in ($id_pers_search)
      order by index_perspective asc, index_sobject asc";
      $query_section = $this->sendQuery($this->konek_sita_db(), $cek_section);
      $items_sec = empty(pg_fetch_all($query_section)) ? array() : pg_fetch_all($query_section);
      
      $response = array();
      $response['results'] = [];
      foreach ($items_opt as $key => $value) {
        $data = array();
        $data['text'] = htmlspecialchars_decode($value['name_perspective']);
        $data['children'] = [];
        foreach ($items_sec as $keyChild => $valueChild) {
          if ($value['id_perspective'] == $valueChild['id_perspective']) {
            $array_transfer = $valueChild;
            $array_transfer['text'] = htmlspecialchars_decode($array_transfer['text']);
            $array_transfer['name_perspective'] = htmlspecialchars_decode($array_transfer['name_perspective']);
            array_push($data['children'], $array_transfer);
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

  public function getStrategicObjective(){
    global $cleanWord;
    try {
      // View Column
      $viewColumn = array(
        'id_sobject' => 'a',
        'text_perspective' => "('(' || b.alias_perspective || ') ' || b.name_perspective)::varchar",
        'index_sobject' => 'a',
        'text_sobject' => "(b.alias_perspective || a.index_sobject || '. ' || a.name_sobject)::varchar",
        'user_entry' => 'a',
        'last_update' => 'a'
      );
      $viewCustomColumn = array_filter($viewColumn, function($val, $key) {
        return $key == 'text_perspective' || $key == 'text_sobject';
      }, ARRAY_FILTER_USE_BOTH);
      // Total
      $totalRecordsQuery = "SELECT COUNT(*) FROM strategic_objective";
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
          // $searching .= !empty($viewColumn[$column]) ? $viewColumn[$column] . ".$column::text ilike '%$searchValueBox%' or " : $viewCustomColumn[$column] . " ilike '%$searchValueBox%' or ";
          if (!empty($column)) {
            if (array_key_exists($column, $viewCustomColumn)) {
              $searching .= $viewColumn[$column] . " ilike '%$searchValueBox%' or ";
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
        if (intval($idxColumn) == 2) {
          $ordering .= "b.index_perspective asc, b.alias_perspective asc, a.index_sobject asc ";
        } else {
          $dir = $value['dir'];
          $columnOrder = $_POST['columns'][$idxColumn]['data'];
          $ordering .= "$columnOrder $dir, ";
        }
      }
      $ordering = rtrim($ordering, ', ');
      // Filtering Column
      $filtering = '';
      foreach ($_POST['columns'] as $key => $value) {
        $column = $value['data'];
        $searchValue = $cleanWord->textCk($value['search']['value'], false, 'trim');
        if (!empty($searchValue)) {
          if (array_key_exists($column, $viewCustomColumn)) {
            $filtering .= "and " . $viewColumn[$column] . " ilike '%$searchValue%' ";
          } else {
            $filtering .= "and " . $viewColumn[$column] . ".$column::text ilike '%$searchValue%' ";
          }
        }
      }

      $cek = "SELECT a.*,
      $viewCustomColumn[text_perspective] as text_perspective,
      $viewCustomColumn[text_sobject] as text_sobject,
      bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from strategic_objective a
      left join perspective b on b.id_perspective = a.id_perspective
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

  public function addStrategicObjective(){
    global $cleanWord;
    $getPkey = $this->getSeriesPkey('strategic_objective', 'id_sobject', 1, 99999, 1, "SELECT split_part(id_sobject, '-', 2)::integer from strategic_objective");
    $sobject_id = "SOB-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $perspective_id = $cleanWord->textCk(@$_POST["perspective_id"], true, 'normal');
    $sobject_name = $cleanWord->textCk(@$_POST["sobject_name"], true, 'upper');
    // $sobject_index = $cleanWord->textCk(@$_POST["sobject_index"], false, 'normal');
    // $getIndex = $this->getSeriesPkey('strategic_objective', 'index_sobject', 1, 99999, 1, "SELECT index_sobject from strategic_objective where id_perspective = {$perspective_id}");
    try {
      $cek = "SELECT coalesce(max(index_sobject), 0)::integer max_index from strategic_objective where id_perspective = {$perspective_id}";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $items = pg_fetch_all($query);
      $sobject_index = intval($items[0]['max_index']) + 1;
      if (!isset($items)) {
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>"Data index tidak ditemukan!"
          )
        );
        die();
      }

      $sql = "INSERT INTO strategic_objective
      (id_sobject, id_perspective, name_sobject, index_sobject, user_entry, last_update)
      values
      ('$sobject_id', {$perspective_id}, {$sobject_name}, $sobject_index, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
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

  public function editStrategicObjective(){
    global $cleanWord;
    $sobject_id = $cleanWord->textCk(@$_POST["sobject_id"], true, 'normal');
    $perspective_id = $cleanWord->textCk(@$_POST["perspective_id"], true, 'normal');
    $sobject_name = $cleanWord->textCk(@$_POST["sobject_name"], true, 'upper');
    $sobject_index = $cleanWord->numberCk(@$_POST["sobject_index"], true, 'integer');
    $sobject_index_before = $cleanWord->numberCk(@$_POST["sobject_index_before"], true, 'integer');
    $perspective_id_before = $cleanWord->textCk(@$_POST["perspective_id_before"], true, 'normal');
    $sql = "";
    if ($sobject_index < 1) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>"Index object tidak boleh kurang dari 1!"
        )
      );
    }
    try {
      $cek = "SELECT id_sobject from strategic_objective where index_sobject = $sobject_index and id_perspective = {$perspective_id}";
      $query = $this->sendQuery($this->konek_sita_db(), $cek);
      $items = pg_fetch_all($query);
      if (!empty($items)) {
        if ($perspective_id_before == $perspective_id) {
          $sql .= "UPDATE strategic_objective SET
          index_sobject = $sobject_index_before,
          user_entry = '$_SESSION[setupuser_kpi_askara]',
          last_update = '".$this->last_update."',
          flag = 'u'
          WHERE id_sobject = '".$items[0]['id_sobject']."';
          ";
        } else {
          $cek = "SELECT coalesce(max(index_sobject), 0)::integer max_index from strategic_objective where id_perspective = {$perspective_id}";
          $query = $this->sendQuery($this->konek_sita_db(), $cek);
          $items = pg_fetch_all($query);
          $sobject_index_before = intval($items[0]['max_index']) + 1;
          if (!isset($items)) {
            return json_encode(
              array(
                'response'=>'error',
                'alert'=>"Gagal mengupdate index object yang bersangkutan, silahkan ganti nomor lain atau hubungin IT"
              )
            );
          } else {
            $sql .= "UPDATE strategic_objective SET
            index_sobject = $sobject_index_before,
            user_entry = '$_SESSION[setupuser_kpi_askara]',
            last_update = '".$this->last_update."',
            flag = 'u'
            WHERE id_sobject = '".$items[0]['id_sobject']."';
            ";
          }
        }
      }

      $sql .= "UPDATE strategic_objective SET
      id_perspective = {$perspective_id},
      name_sobject = {$sobject_name},
      index_sobject = {$sobject_index},
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_sobject = {$sobject_id};
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

  public function deleteStrategicObjective(){
    global $cleanWord;
    $sobject_id = $cleanWord->textCk(@$_POST["sobject_id"], true, 'normal');
    try {
      $sql = "UPDATE strategic_objective SET flag = 'd' WHERE id_sobject = {$sobject_id};
      DELETE FROM strategic_objective WHERE id_sobject = {$sobject_id}";
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