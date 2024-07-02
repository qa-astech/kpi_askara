<?php
// error_reporting(0);
require_once('koneksi.php');
class logAttempt extends database {

  protected $pdo_connect;

  public function __construct(){
    parent::__construct();
    $this->konek_sita_db();
  }

  public function login(){
    global $cleanWordPDO;
    $username = $cleanWordPDO->textCk(@$_POST["username"], true, 'normal');
    $password = $cleanWordPDO->textCk(@$_POST["password"], true, 'plain');
    $host = !empty($_SERVER['HTTP_HOST']) ? $cleanWordPDO->textCk(@$_SERVER['HTTP_HOST'], false, 'trim') : 'Not Detected';
    $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $cleanWordPDO->textCk(@$_SERVER['HTTP_USER_AGENT'], false, 'trim') : 'Not Detected';

    try {
      
      $cek_user = "SELECT a.username_users, a.password_users, b.id_usersetup
      FROM users a
      INNER JOIN all_users_setup b on b.nik = a.nik_users and b.role_utama = true
      where a.username_users = :username;";
      $query_user = $this->sendQueryPDO($this->konek_kpi_pdo(), $cek_user, array(
        ':username' => $username
      ));
      $row_user = $query_user->fetchAll();

      if (empty($row_user)) {
        return json_encode(
          array(
            'response'=>'error',
            'alert'=>'Nama kamu tidak ditemukan, apa kamu yakin mempunyai akun tersebut? Jika sudah ada, pastikan ada role utama yang aktif!'
          )
        );

      } else {

        if (password_verify($password, $row_user[0]['password_users'])) {
          $_SESSION['user_kpi_askara'] = $row_user[0]['username_users'];
          $_SESSION['setupuser_kpi_askara'] = $row_user[0]['id_usersetup'];
          $_SESSION["expires_kpi_askara"] = time() + 32400;
          $send_sql = "INSERT INTO last_log (
            username, status_log, host, user_agent, time_log
          ) VALUES (
            :username, 'login', :host, :userAgent, :lastUpdate
          );";
          // {$username}, 'login', '$host', '$user_agent', '".$this->last_update."'
          // $this->sendQuery($this->konek_sita_db(), $send_sql);

          $query_user = $this->sendQueryPDO($this->konek_kpi_pdo(), $send_sql, array(
            ':username' => $username,
            ':host' => $host,
            ':userAgent' => $user_agent,
            ':lastUpdate' => $this->last_update
          ));
          return json_encode(
            array(
              'response'=>'success',
              'alert'=>'Sukses! Selamat datang!'
            )
          );
          
        } else {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Kata sandi atau username salah, silahkan coba kembali!'
            )
          );
        }
      }

    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Proses ditolak, akun tidak ditemukan! ❌'
        )
      );
    }

  }

  public function logout(){
    session_destroy();
    return json_encode(
      array(
        'response'=>'success',
        'alert'=>'Terimakasih sudah bekerja keras hari ini ❤️'
      )
    );
  }

  public function resetpass(){
    global $cleanWord, $classSessionCheck;
    $classSessionCheck->checkSession();
    $old_password = $cleanWord->textCk(@$_POST["old_password"], true, 'plain');
    $new_password = $cleanWord->textCk(@$_POST["new_password"], true, 'plain');
    $confirm_password = $cleanWord->textCk(@$_POST["confirm_password"], true, 'plain');

    try {

      $cek_user = "SELECT * from users where username_users = '$_SESSION[user_kpi_askara]';";
      $query_user = $this->sendQuery($this->konek_sita_db(), $cek_user);
      $row_user = pg_fetch_all($query_user);
  
      if (password_verify($old_password, $row_user[0]['password_users'])) {

        if ($new_password == $confirm_password) {
          $password = password_hash($new_password , PASSWORD_DEFAULT);
          $insert = "UPDATE users set
          password_users = '$password'
          WHERE username_users = '$_SESSION[user_kpi_askara]';";
          $this->sendQuery($this->konek_sita_db(), $insert);
          return json_encode(
            array(
              'response'=>'success',
              'alert'=>"Password berhasil diubah!"
            )
          );

        } else {
          return json_encode(
            array(
              'response'=>'error',
              'alert'=>'Kata sandi tidak sama, silahkan cek kembali kata sandi baru! ❌'
            )
          );
        }

      } else {
        return json_encode(
          array(
            'response'=>'error',
            'alert'=>'Kata sandi yang lama salah, silahkan coba kembali! ❌'
          )
        );
      }

    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Jaringan error, silahkan coba kembali beberapa saat! ❌'
        )
      );
    }
  }

  public function changeDept(){
    global $cleanWord, $classSessionCheck;
    $classSessionCheck->checkSession();
    $id_usersetup = $cleanWord->textCk(@$_POST["id_usersetup"], true, 'normal');
    try {
      $cek_user = "SELECT a.id_usersetup
      FROM all_users_setup a
      inner join users b on b.nik_users = a.nik
      where a.id_usersetup = {$id_usersetup} and b.username_users = '$_SESSION[user_kpi_askara]';";
      $query_user = $this->sendQuery($this->konek_sita_db(), $cek_user);
      $row_user = pg_fetch_all($query_user);
      if (empty($row_user)) {
        return json_encode(
          array(
            'response'=>'error',
            'alert'=>'Data tidak ditemukan, silahkan pilih opsi lain! ❌'
          )
        );
      } else {
        $_SESSION['setupuser_kpi_askara'] = $row_user[0]['id_usersetup'];
        return json_encode(
          array(
            'response'=>'success',
            'alert'=>"Role berhasil diubah!"
          )
        );
      }
    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>'Jaringan error, silahkan coba kembali beberapa saat! ❌'
        )
      );
    }
  }

  public function getSessionSimple(){
    global $classSessionCheck;
    $classSessionCheck->checkSession();

    try {
      $cek_user = "SELECT b.nik_users, b.username_users, b.fullname_users,
      a.id_company, a.name_company,
      a.id_section, a.name_section,
      a.id_department, a.name_department,
      a.id_position, a.name_position,
      a.golongan
      from all_users_setup a
      INNER JOIN users b on b.nik_users = a.nik
      where a.id_usersetup = '$_SESSION[setupuser_kpi_askara]';";
      $query_user = $this->sendQuery($this->konek_sita_db(), $cek_user);
      $row_user = pg_fetch_all($query_user);
      return json_encode(
        array(
          'response'=>'success',
          'data' => $row_user[0]
        )
      );

    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>"Data gagal dimuat, silahkan coba beberapa saat lagi! ❌"
        )
      );
    }

  }

  public function getAllRoles(){
    global $classSessionCheck;
    $classSessionCheck->checkSession();
    try {

      $cek_user = "SELECT distinct a.id_usersetup, b.nik_users, b.username_users, b.fullname_users,
      a.id_company, a.name_company,
      a.id_section, a.name_section,
      a.id_department, a.name_department,
      a.id_position, a.name_position,
      a.golongan
      FROM users b
      INNER JOIN all_users_setup a on a.nik = b.nik_users
      where b.username_users = '$_SESSION[user_kpi_askara]';";
      $query_user = $this->sendQuery($this->konek_sita_db(), $cek_user);
      $row_user = pg_fetch_all($query_user);
      return json_encode(
        array(
          'response'=>'success',
          'data' => $row_user
        )
      );

    } catch (Exception $e) {
      return json_encode(
        array(
          'response'=>'error',
          'alert'=>"Data gagal dimuat, silahkan coba beberapa saat lagi! ❌"
        )
      );
    }

  }

}

$logAttempt = new logAttempt();
if (!empty($_REQUEST["act"])) {
  if ($_REQUEST["act"] == "login") {
    echo $logAttempt->login();
  } elseif ($_REQUEST["act"] == "logout") {
    echo $logAttempt->logout();
  } elseif ($_REQUEST["act"] == "resetpass") {
    echo $logAttempt->resetpass();
  } elseif ($_REQUEST["act"] == "changeDept") {
    echo $logAttempt->changeDept();
  } elseif ($_REQUEST["act"] == "getSessionSimple") {
    echo $logAttempt->getSessionSimple();
  } elseif ($_REQUEST["act"] == "getAllRoles") {
    echo $logAttempt->getAllRoles();
  } else {
    echo json_encode(
      array(
        'response'=>'error',
        'alert'=>"Action tidak ditemukan!"
      )
    );
  }
}
?>