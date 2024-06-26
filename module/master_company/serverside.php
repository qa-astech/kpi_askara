<?php
class company_master extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function jsonCompany(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct id_company as id, name_company as text from company_master where name_company ilike '%$q%'";
      $cek_main = $cek . " order by id_company asc offset $offset limit $records_per_page";
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

  public function searchSection(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
      $records_per_page = 6;
      $offset = ($page - 1) * $records_per_page;

      $cek_dept = "SELECT distinct b.id_department, b.name_department
      from company_detail aa
      inner join section_master a on a.id_section = aa.id_section
      inner join department_master b on b.id_department = a.id_department
      where aa.id_company = {$id_company}
      and (a.name_section ilike '%$q%' or b.name_department ilike '%$q%')";
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

  public function searchPosition(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
      $id_section = $cleanWord->textCk(@$_POST["id_section"], true, 'normal');
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct b.id_position as id, b.name_position as text
      from company_detail a
      inner join position_master b on b.id_position = a.id_position
      where a.id_company = {$id_company}
      and a.id_section = {$id_section}
      and b.name_position ilike '%$q%'";
      $cek_main = $cek . " order by b.id_position asc offset $offset limit $records_per_page";
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

  public function searchPlant(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
      $id_section = $cleanWord->textCk(@$_POST["id_section"], true, 'normal');
      $id_position = $cleanWord->textCk(@$_POST["id_position"], true, 'normal');
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct b.id_plant as id, b.name_plant as text
      from company_detail a
      inner join plant_master b on b.id_plant = a.id_plant
      where a.id_company = {$id_company}
      and a.id_section = {$id_section}
      and a.id_position = {$id_position}
      and b.name_plant ilike '%$q%'";
      $cek_main = $cek . " order by b.id_plant asc offset $offset limit $records_per_page";
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

  public function searchGolongan(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
      $id_section = $cleanWord->textCk(@$_POST["id_section"], true, 'normal');
      $id_position = $cleanWord->textCk(@$_POST["id_position"], true, 'normal');
      $id_plant = $cleanWord->textCk(@$_POST["id_plant"], true, 'normal');
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct golongan as id, golongan as text from company_detail
      where id_company = {$id_company}
      and id_section = {$id_section}
      and id_position = {$id_position}
      and id_plant = {$id_plant}
      and golongan::varchar ilike '%$q%'";
      $cek_main = $cek . " order by golongan asc offset $offset limit $records_per_page";
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

  public function getCompany(){
    global $cleanWord;
    try {
      // View Column
      $viewColumn = array(
        'id_company' => 'a',
        'name_company' => 'a',
        'alias_company' => 'a',
        'stat_group' => 'a',
        'stat_customer' => 'a',
        'stat_supplier' => 'a',
        'user_entry' => 'a',
        'last_update' => 'a'
      );
      // Total
      $totalRecordsQuery = "SELECT COUNT(*) FROM company_master";
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

      $cek = "SELECT a.* from company_master a
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

  public function addCompany(){
    global $cleanWord, $upload;
    $getPkey = $this->getSeriesPkey('company_master', 'id_company', 1, 99999, 1, "SELECT split_part(id_company, '-', 2)::integer from company_master");
    $company_id = "COM-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $company_name = $cleanWord->textCk(@$_POST["company_name"], true, 'upper');
    $company_alias = $cleanWord->textCk(@$_POST["company_alias"], false, 'upper');
    $stat_group = empty($_POST["stat_group"]) ? 'false' : $_POST["stat_group"];
    $stat_customer = empty($_POST["stat_customer"]) ? 'false' : $_POST["stat_customer"];
    $stat_supplier = empty($_POST["stat_supplier"]) ? 'false' : $_POST["stat_supplier"];
    if (!empty($_FILES['logo_perusahaan']['tmp_name'])) {
      $upload->setDirFile('logo/');
      $upload->setTypeFile([
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico',
        'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'ICO'
      ]);
      $upload->setNameFile($_FILES['logo_perusahaan']['name']);
      $upload->setTempFile($_FILES['logo_perusahaan']['tmp_name']);
      $upload->setPkeyFile($company_id);
      $upload->prosesUpload();
    }
    try {
      $sql = "INSERT INTO company_master
      (id_company, name_company, alias_company, stat_group, stat_customer, stat_supplier, logo_perusahaan, user_entry, last_update)
      values
      ('$company_id', {$company_name}, {$company_alias}, $stat_group, $stat_customer, $stat_supplier, '".$upload->newNameFile."', '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."')";
      $this->sendQueryWithImg($this->konek_sita_db(), $sql);
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

  public function editCompany(){
    global $cleanWord, $upload;
    $company_id = $cleanWord->textCk(@$_POST["company_id"], true, 'normal');
    $company_id_img = $cleanWord->textCk(@$_POST["company_id"], true, 'trim');
    $company_name = $cleanWord->textCk(@$_POST["company_name"], true, 'upper');
    $company_alias = $cleanWord->textCk(@$_POST["company_alias"], false, 'upper');
    $stat_group = empty($_POST["stat_group"]) ? 'false' : $_POST["stat_group"];
    $stat_customer = empty($_POST["stat_customer"]) ? 'false' : $_POST["stat_customer"];
    $stat_supplier = empty($_POST["stat_supplier"]) ? 'false' : $_POST["stat_supplier"];
    $image_change = $cleanWord->textCk(@$_POST["image_change"], true, 'trim');
    $imgOld = [];
    $dirOld = 'logo/';
    $imgUpdateSql = "";
    $query = "SELECT * FROM company_master WHERE id_company = {$company_id}";
    $run_query = $this->sendQuery($this->konek_sita_db(), $query);
    $fetch_all = pg_fetch_all($run_query);

    if (!empty($_FILES['logo_perusahaan']['tmp_name']) && $image_change == 'change') {
      array_push($imgOld, $fetch_all[0]['logo_perusahaan']);

      $upload->setDirFile('logo/');
      $upload->setTypeFile([
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico',
        'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'ICO'
      ]);
      $upload->setNameFile($_FILES['logo_perusahaan']['name']);
      $upload->setTempFile($_FILES['logo_perusahaan']['tmp_name']);
      $upload->setPkeyFile($company_id_img);
      $upload->prosesUpload();
      $imgUpdateSql = "logo_perusahaan = '".$upload->newNameFile."',";
    } elseif ($image_change == 'delete') {
      $imgUpdateSql = "logo_perusahaan = null,";
      array_push($imgOld, $fetch_all[0]['logo_perusahaan']);
    }
    try {
      $sql = "UPDATE company_master SET
      name_company = {$company_name},
      alias_company = {$company_alias},
      stat_group = $stat_group,
      stat_customer = $stat_customer,
      stat_supplier = $stat_supplier,
      $imgUpdateSql
      user_entry = '$_SESSION[setupuser_kpi_askara]',
      last_update = '".$this->last_update."',
      flag = 'u'
      WHERE id_company = {$company_id}
      ";
      $this->sendQueryWithImg($this->konek_sita_db(), $sql, $imgOld, $dirOld);
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

  public function deleteCompany(){
    global $cleanWord;
    $company_id = $cleanWord->textCk(@$_POST["company_id"], true, 'normal');
    $imgOld = [];
    $dirOld = 'logo/';

    $query = "SELECT logo_perusahaan FROM company_master WHERE id_company = {$company_id}";
    $run_query = $this->sendQuery($this->konek_sita_db(), $query);
    $fetch_all = pg_fetch_all($run_query);
    if(!empty($fetch_all[0]['logo_perusahaan'])) array_push($imgOld, $fetch_all[0]['logo_perusahaan']);

    try {
      $sql = "UPDATE company_detail SET flag = 'd' WHERE id_company = {$company_id};
      DELETE FROM company_detail WHERE id_company = {$company_id};
      UPDATE company_master SET flag = 'd' WHERE id_company = {$company_id};
      DELETE FROM company_master WHERE id_company = {$company_id}";
      $this->sendQueryWithImg($this->konek_sita_db(), $sql, $imgOld, $dirOld);
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