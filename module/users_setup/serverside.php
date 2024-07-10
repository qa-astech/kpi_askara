<?php
class users_setup extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function searchDeptComp(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_company = $cleanWord->textCk(@$_POST["id_company"], true, 'normal');
      $id_department = $cleanWord->textCk(@$_POST["id_department"], true, 'normal');
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct a.id_usersetup as id, b.fullname_users as text
      from users_setup a
      inner join users b on b.nik_users = a.nik
      inner join company_detail c on c.id_det_company = a.id_det_company
      inner join section_master d on d.id_section = c.id_section
      where c.id_company = {$id_company}
      and d.id_department = {$id_department}
      and b.fullname_users ilike '%$q%'
      ";
      $cek_main = $cek . " order by b.fullname_users asc offset $offset limit $records_per_page";
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

  public function searchDeptCompCorps(){
    try {
      global $cleanWord;
      $q = $cleanWord->textCk(@$_POST["q"], false, 'trim');
      $page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
      $id_department = $cleanWord->textCk(@$_POST["id_department"], true, 'normal');
      $records_per_page = 10;
      $offset = ($page - 1) * $records_per_page;

      $cek = "SELECT distinct a.id_usersetup as id, b.fullname_users as text
      from users_setup_corps a
      inner join users b on b.nik_users = a.nik
      inner join section_master d on d.id_section = a.id_section
      where d.id_department = {$id_department}
      and b.fullname_users ilike '%$q%'
      ";
      $cek_main = $cek . " order by b.fullname_users asc offset $offset limit $records_per_page";
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

  public function getUsersSetup(){
    global $cleanWordPDO;
    $_POST['columns'] = array_map(function($v) {
      if ($v['data'] === 'fullname_entry') {
        $v['data'] = 'fullname_users';
      }
      return $v;
    }, $_POST['columns']);
    $nik_users = $cleanWordPDO->textCk(@$_POST["nik_users"], true, 'normal', null, "nik_users");
    $sendVar = array(
      ':nikUsers' => $nik_users,
    );
    try {
      // View Column
      $viewColumn = array(
        'id_usersetup' => 'a',
        'name_company' => 'a',
        'name_department' => 'a',
        'name_section' => 'a',
        'name_position' => 'a',
        'name_plant' => 'a',
        'golongan' => 'a',
        'status_active' => 'a',
        'role_utama' => 'a',
        'fullname_users' => 'bb',
        'last_update' => 'a'
      );
      // Offset
      $start = $_POST['start'];
      // Limit
      $length = $_POST['length'];
      // Search Box
      $searchValueBox = $cleanWordPDO->textCk(@$_POST['search']['value'], false);
      $searching = '';
      if (!empty($searchValueBox)) {
        foreach ($_POST['columns'] as $key => $value) {
          $column = $value['data'];
          if (!empty($column)) {
            $searching .= $viewColumn[$column] . ".$column::text ilike :$column" . "_search or ";
            $sendVar = array_merge($sendVar, [":$column" . "_search" => '%'.$searchValueBox.'%']);
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
        $searchValue = $cleanWordPDO->textCk($value['search']['value'], false);
        if (!empty($searchValue)) {
          $filtering .= "and " . $viewColumn[$column] . ".$column::text ilike :$column" . "_filter ";
          $sendVar = array_merge($sendVar, [":$column" . "_filter" => '%'.$searchValue.'%']);
        }
      }
      // Total
      $totalRecordsQuery = "SELECT COUNT(*)
      from all_users_setup a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.nik = :nikUsers $filtering $searching";
      $totalRecordsResult = $this->sendQueryPDO($this->konek_kpi_pdo(), $totalRecordsQuery, $sendVar);
      // $totalRecordsResult = $this->sendQuery($this->konek_sita_db(), $totalRecordsQuery);
      // $totalRecords = pg_fetch_result($totalRecordsResult, 0, 0);
      $totalRecords = $totalRecordsResult->fetchColumn(0);

      $cek = "SELECT a.*, bb.fullname_users fullname_entry, bb.nik_users nik_entry
      from all_users_setup a
      left join all_users_setup aa on aa.id_usersetup = a.user_entry
      left join users bb on bb.nik_users = aa.nik
      where a.nik = :nikUsers $filtering $searching
      order by $ordering
      offset $start limit $length
      ";
      $query = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek, $sendVar);
      $items = $query->fetchAll();
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

  public function addUsersSetup(){
    global $cleanWord;

    $getPkey = $this->getSeriesPkey(
      'all_users_setup',
      'id_usersetup', 1, 99999, 1,
      "SELECT split_part(id_usersetup, '-', 2)::integer from all_users_setup where split_part(id_usersetup, '-', 1)::varchar = '".$this->year."'"
    );
    $id_usersetup = $this->year . "-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
    $nik = $cleanWord->textCk(@$_POST["nik"], true, 'normal');
    $status_active = $cleanWord->textCk(@$_POST["status_active"], true, 'trim');
    $users_role_utama = !empty($_POST["users_role_utama"]) ? 'true' : 'false';
    $checkDivCorps = !empty($_POST["checkDivCorps"]) ? false : true;
    $sql = '';

    try {
      if ($users_role_utama == 'true') {
        $sql .= "UPDATE users_setup SET role_utama = false where nik = {$nik};
        UPDATE users_setup_corps SET role_utama = false where nik = {$nik};";
      }

      if ($checkDivCorps) {
        $users_company = $cleanWord->textCk(@$_POST["users_company"], true, 'normal');
        $users_position = $cleanWord->textCk(@$_POST["users_position"], true, 'normal');
        $users_section = $cleanWord->textCk(@$_POST["users_section"], true, 'normal');
        $users_plant = $cleanWord->textCk(@$_POST["users_plant"], true, 'normal');
        $users_golongan = $cleanWord->textCk(@$_POST["users_golongan"], true, 'normal');

        $cek_detail_company = "SELECT id_det_company from company_detail
        where id_company = {$users_company}
        and id_position = {$users_position}
        and id_section = {$users_section}
        and id_plant = {$users_plant}
        and golongan = $users_golongan
        ";
        $query_detail_company = $this->sendQuery($this->konek_sita_db(), $cek_detail_company);
        $items_detail_company = pg_fetch_all($query_detail_company);
        if (empty($items_detail_company)) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Kombinasi peran tidak ditemukan, silahkan cek kembali detail struktur perusahaan yang dituju!'
            )
          );
        }
        $sql .= "INSERT INTO users_setup (
          id_usersetup, id_det_company, nik, status_active, role_utama, user_entry, last_update
        ) values (
          '$id_usersetup', '".$items_detail_company[0]['id_det_company']."', {$nik}, $status_active, $users_role_utama, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
        )";

      } else {
        $users_position_corps = $cleanWord->textCk(@$_POST["users_position_corps"], true, 'normal');
        $users_section_corps = $cleanWord->textCk(@$_POST["users_section_corps"], true, 'normal');
        $users_golongan_corps = $cleanWord->numberCk(@$_POST["users_golongan_corps"], true, 'integer');

        if ($users_golongan_corps > 5 || $users_golongan_corps < 1) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Angka golongan lebih dari 5 atau kurang dari 1!'
            )
          );
        }

        $sql .= "INSERT INTO users_setup_corps (
          id_usersetup, id_position, id_section, golongan,
          nik, status_active, role_utama, user_entry, last_update
        ) values (
          '$id_usersetup', {$users_position_corps}, {$users_section_corps}, $users_golongan_corps,
          {$nik}, $status_active, $users_role_utama, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
        )";
      }

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

  public function editUsersSetup(){
    global $cleanWordPDO;

    $id_usersetup = $cleanWordPDO->textCk(@$_POST["id_usersetup"], true, 'normal', null, "id_usersetup");
    $nik = $cleanWordPDO->textCk(@$_POST["nik"], true, 'normal', null, "nik");
    $status_active = $cleanWordPDO->textCk(@$_POST["status_active"], true, 'normal', null, "status_active");
    $users_role_utama = !empty($_POST["users_role_utama"]) ? 'true' : 'false';
    $checkDivCorps = !empty($_POST["checkDivCorps"]) && $_POST["checkDivCorps"] === 'true' ? false : true;
    $beginConnect = $this->konek_kpi_pdo();
    $beginConnect->beginTransaction();

    if ($users_role_utama == 'true') {
      $sql_update = "UPDATE users_setup SET role_utama = false where id_usersetup != :idUsersetup and nik = :nik;";
      $this->sendQueryPDO($beginConnect, $sql_update, array(
        ':nik' => $nik,
        ':idUsersetup' => $id_usersetup
      ));
      $sql_update = "UPDATE users_setup_corps SET role_utama = false where id_usersetup != :idUsersetup and nik = :nik;";
      $this->sendQueryPDO($beginConnect, $sql_update, array(
        ':nik' => $nik,
        ':idUsersetup' => $id_usersetup
      ));
    }
    $sql = "";
    $sendVar = array();

    try {
      if ($checkDivCorps) {
        $users_company = $cleanWordPDO->textCk(@$_POST["users_company"], true, 'normal', null, "users_company");
        $users_section = $cleanWordPDO->textCk(@$_POST["users_section"], true, 'normal', null, "users_section");
        $users_position = $cleanWordPDO->textCk(@$_POST["users_position"], true, 'normal', null, "users_position");
        $users_plant = $cleanWordPDO->textCk(@$_POST["users_plant"], true, 'normal', null, "users_plant");
        $users_golongan = $cleanWordPDO->textCk(@$_POST["users_golongan"], true, 'normal', null, "users_golongan");
    
        $cek_detail_company = "SELECT id_det_company from company_detail
        where id_company = :usersCompany
        and id_position = :usersPosition
        and id_section = :usersSection
        and id_plant = :usersPlant
        and golongan = :usersGolongan
        ";
        $query_detail_company = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_detail_company, array(
          ':usersCompany' => $users_company,
          ':usersPosition' => $users_position,
          ':usersSection' => $users_section,
          ':usersPlant' => $users_plant,
          ':usersGolongan' => $users_golongan
        ));
        $items_detail_company = $query_detail_company->fetchAll();
        if (empty($items_detail_company)) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Kombinasi peran tidak ditemukan, silahkan cek kembali detail struktur perusahaan yang dituju!'
            )
          );
        }
        $sql .= "UPDATE users_setup SET
        id_det_company = :idDetailCompany,
        status_active = :statusActive,
        role_utama = :roleUtama,
        user_entry = :userEntry,
        last_update = :lastUpdate,
        flag = 'u'
        WHERE id_usersetup = :idUsersetup;
        ";
        $sendVar = array_merge($sendVar, [
          ':idDetailCompany' => $items_detail_company[0]['id_det_company'],
          ':statusActive' => $status_active,
          ':roleUtama' => $users_role_utama,
          ':userEntry' => $_SESSION['setupuser_kpi_askara'],
          ':lastUpdate' => $this->last_update,
          ':idUsersetup' => $id_usersetup
        ]);

      } else {
        $users_position_corps = $cleanWordPDO->textCk(@$_POST["users_position_corps"], true, 'normal', "users_position_corps");
        $users_section_corps = $cleanWordPDO->textCk(@$_POST["users_section_corps"], true, 'normal', "users_section_corps");
        $users_golongan_corps = $cleanWordPDO->numberCk(@$_POST["users_golongan_corps"], true, 'normal', "users_golongan_corps");

        if ($users_golongan_corps > 5 || $users_golongan_corps < 1) {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Angka golongan lebih dari 5 atau kurang dari 1!'
            )
          );
        }

        $sql .= "UPDATE users_setup_corps SET
        id_position = :idPosition,
        id_section = :idSection,
        golongan = :golongan,
        status_active = :statusActive,
        role_utama = :roleUtama,
        user_entry = :userEntry,
        last_update = :lastUpdate,
        flag = 'u'
        WHERE id_usersetup = :idUsersetup;
        ";
        $sendVar = array_merge($sendVar, [
          ':idPosition' => $users_position_corps,
          ':idSection' => $users_section_corps,
          ':golongan' => $users_golongan_corps,
          ':statusActive' => $status_active,
          ':roleUtama' => $users_role_utama,
          ':userEntry' => $_SESSION['setupuser_kpi_askara'],
          ':lastUpdate' => $this->last_update,
          ':idUsersetup' => $id_usersetup
        ]);

      }

      $this->sendQueryPDO($beginConnect, $sql, $sendVar);
      $beginConnect->commit();
      return json_encode(
        array(
          'response'=>'success',
          'alert'=>"Data Berhasil diubah!"
        )
      );

    } catch (Exception $e) {
      $beginConnect->rollBack();
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan! ❌'
        )
      );
    }
  }

  // public function deleteUsersSetup(){
  //   global $cleanWord;
  //   $id_usersetup = $cleanWord->textCk(@$_POST["id_usersetup"], true, 'normal');
  //   try {
  //     $sql = "UPDATE users_setup SET flag = 'd' WHERE id_usersetup = {$id_usersetup};";
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

  public function jsonUsersSetup(){
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