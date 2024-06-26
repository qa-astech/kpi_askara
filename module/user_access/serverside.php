<?php
class user_access extends database {

  public function __construct(){
    parent::__construct();
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    $this->konek_sita_db();
  }

  public function getMenuAccess(){
    try {
      $cek_menu = "SELECT distinct d.*, STRING_TO_ARRAY(d.index_menu, '.')::INT[] arr_index
      FROM users a
      INNER JOIN user_access b on b.nik = a.nik_users
      INNER JOIN menu_access c on c.code_maccess = b.code_maccess
      INNER JOIN menu d on d.code_menu = c.code_menu
      WHERE c.name_maccess = 'view' and a.username_users = '$_SESSION[user_kpi_askara]'
      ORDER BY STRING_TO_ARRAY(d.index_menu, '.')::INT[] asc";
      $query_menu = $this->sendQuery($this->konek_sita_db(), $cek_menu);
      $menu = pg_fetch_all($query_menu);
      if (!empty($menu)) {
        foreach ($menu as $key => $value) {
          $indexMenu = $value['index_menu'];
          if (empty(substr_count($indexMenu, '.'))) {
            $menu[$key]['indexParent'] = null;
          } else {
            $explode = explode('.', $indexMenu);
            array_pop($explode);
            $implode = implode('.', $explode);
            $menu[$key]['indexParent'] = $implode;
          }
          // $menu[$key]['indexParent'] = ;
        }
      }
      return json_encode($menu);
    } catch (Exception $e) {
      return json_encode(array());
    }
	}

  public function getUserAccess(){
    global $cleanWord;
    $nik = $cleanWord->textCk(@$_REQUEST["nik"], true, 'normal');

    try {
      $cek_menu = "SELECT distinct a.*, STRING_TO_ARRAY(a.index_menu, '.')::INT[] arr_index
      FROM menu a
      ORDER BY STRING_TO_ARRAY(a.index_menu, '.')::INT[] asc";
      $query_menu = $this->sendQuery($this->konek_sita_db(), $cek_menu);
      $menu = pg_fetch_all($query_menu);
  
      if (!empty($menu)) {
        $cek_maccess = "SELECT distinct a.*, coalesce(b.access_permission, false) access_permission
        FROM menu_access a
        LEFT JOIN user_access b on b.code_maccess = a.code_maccess and b.nik = {$nik}::varchar
        ORDER BY name_maccess asc";
        $query_maccess = $this->sendQuery($this->konek_sita_db(), $cek_maccess);
        $maccess = pg_fetch_all($query_maccess);
        
        foreach ($menu as $key => $value) {
          $menu[$key]['menu_akses'] = [];
          if (!empty($maccess)) {
            foreach ($maccess as $key_child => $value_child) {
              if ($value['code_menu'] == $value_child['code_menu']) {
                array_push($menu[$key]['menu_akses'], $value_child);
              }
            }
          }
        }
      }
      return json_encode($menu);

    } catch (Exception $e) {
      return json_encode(array());
    }

	}

  public function editUserAccess(){
    global $cleanWord;
    $code_maccess = $cleanWord->textCk(@$_POST["code_maccess"], true, 'normal');
		$check_menu = $cleanWord->textCk(@$_POST["check_menu"], true, 'trim');
		$nik = $cleanWord->textCk(@$_POST["nik"], true, 'normal');

    try {
      $qAkses = "SELECT id_uaccess FROM user_access where nik = {$nik} and code_maccess = {$code_maccess} ";
      $eAkses = $this->sendQuery($this->konek_sita_db(), $qAkses);
      $rAkses = pg_fetch_all($eAkses);

      if (!empty($rAkses)) {
        $sql_run = "UPDATE user_access SET
        access_permission = $check_menu,
        last_update = '". $this->last_update ."',
        flag = 'u'
        WHERE id_uaccess = '". $rAkses[0]['id_uaccess'] ."';";
      } else {
        $getPkey = $this->getSeriesPkey('user_access', 'id_uaccess', 1, 99999, 1, "SELECT split_part(id_uaccess, '-', 2)::integer from user_access");
        $id_uaccess = "UA-" . str_pad($getPkey[0]['code'], 5, '0', STR_PAD_LEFT);
        $sql_run = "INSERT into user_access (
          id_uaccess, nik, code_maccess, access_permission, user_entry, last_update
        ) VALUES ( 
          '$id_uaccess', {$nik}, {$code_maccess}, $check_menu, '$_SESSION[setupuser_kpi_askara]', '".$this->last_update."'
        );";
      }

      $this->sendQuery($this->konek_sita_db(), $sql_run);
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
}
?>