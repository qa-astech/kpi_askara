<?php
require_once("upload.php");
require_once("zipfile.php");
require_once __DIR__ . '/../third-party/utility-yudhi/cleanword.php';
require_once __DIR__ . '/../third-party/utility-yudhi/cleanwordPDO.php';
class session_cek {
  public function checkSession(){
    if ( empty($_SESSION["user_kpi_askara"]) || empty($_SESSION["setupuser_kpi_askara"]) ) {
	    echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Sesi telah berakhir! silahkan login kembali!'
        )
      );
      die();
	  }
  }
}
$classSessionCheck = new session_cek();

class database {

  protected $ip_server_sita;
  protected $last_update;
  protected $dateToday;
  protected $year;
  protected $month;
  protected $day;
  protected $monthNumber;
  protected $monthArr = array(
    'jan' => 1,
    'feb' => 2,
    'mar' => 3,
    'apr' => 4,
    'mei' => 5,
    'jun' => 6,
    'jul' => 7,
    'agu' => 8,
    'sep' => 9,
    'okt' => 10,
    'nov' => 11,
    'des' => 12,
  );
  public $data_insert;

  public function __construct(){
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    $this->data_insert = json_decode(file_get_contents('php://input'), true);
    $this->ip_server_sita["localhost"] = "127.0.0.1";
    $this->ip_server_sita["sita_local"] = "192.168.3.229";
    $this->ip_server_sita["sita_public1"] = "103.134.87.3";
    $this->ip_server_sita["sita_public2"] = "103.165.122.222";
    $this->last_update = date('Y-m-d H:i:s');
    $this->dateToday = date('Y-m-d');
    $this->year = date('Y');
    $this->month = date('m');
    $this->monthNumber = date('n');
    $this->day = date('d');
  }

  protected function returnKoneksi($ip, $dbcon){
    if ($koneksi = pg_connect($ip . $dbcon)) {
      return $koneksi;
    } else {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Koneksi ERROR, tidak dapat tersambung...'
        )
      );
      die();
    }
  }

  public function konek_sita_db(){
    // if ($_SERVER["SERVER_NAME"] == "192.168.3.229" || $_SERVER["SERVER_NAME"] == "127.0.0.1" || $_SERVER["SERVER_NAME"] == "localhost") {
		// 	$ip = "192.168.3.229";
		// } else {
		// 	$ip = "sita.askara-int.com";
		// }
		// $port = "5432";
		// $database = "kpi_askara_test";
		// $user = "postgres";
		// $password = "askara057";

		// $dbcon = "
		// host=$ip
		// port=$port
		// dbname=$database
		// user=$user
		// password=$password";

    $dbcon = "
      host=localhost
      port=5432
      dbname=kpi_askara
      user=postgres
      password=postgres
    ";
    
    if ($koneksi = pg_connect($dbcon)){
      return $koneksi;
    } else {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Koneksi ERROR, tidak dapat tersambung...'
        )
      );
      die();
    }
  }

  public function konek_kpi_pdo() {
    try {
      $host = "localhost";
      $db = "kpi_askara";
      $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
      $user = 'postgres';
      $password = 'postgres';
      return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    } catch (PDOException $e) {
      die($e->getMessage());
    }
  }

  public function sendQuery($koneksi, $insert){
    if (empty($koneksi) || empty($insert)) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan (#Query1)'
        )
      );
      die();
    }
    if (pg_connection_busy($koneksi)) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Sedang ada proses yang berjalan, coba lagi beberapa saat...'
        )
      );
      die();
    }
    $result = @pg_query($koneksi, $insert);
    if ($result === false) {
      echo json_encode(array(
        'response' => 'error',
        'alert' => pg_last_error($koneksi)
      ));
      die();
    } else {
      return $result;
    }
  }

  public function sendQueryPDO($koneksi, $insert, $arrOfInsert){
    if (empty($insert)) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan (#Query1)'
        )
      );
      die();
    }

    $isBusy = $koneksi->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql' && $koneksi->pgsqlGetNotify();
    if ($isBusy) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Sedang ada proses yang berjalan, coba lagi beberapa saat...'
        )
      );
      die();
    }

    // echo 'nanih';

    $prepareSQL = $koneksi->prepare($insert);
    if ($prepareSQL->execute($arrOfInsert)) {
      return $prepareSQL;
    } else {
      echo json_encode(array(
        'response' => 'error',
        'alert' => $prepareSQL->errorInfo()[2]
      ));
      die();
    }
  }

  public function sendQueryWithImg($koneksi, $insert, $imgOld = [], $dir = ''){
    global $upload;
    if (empty($koneksi) || empty($insert)) {
      $upload->revertUpload();
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan (#Query1)'
        )
      );
      die();
    }

    if (pg_connection_busy($koneksi)) {
      $upload->revertUpload();
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Sedang ada proses yang berjalan, coba lagi beberapa saat...'
        )
      );
      die();
    }

    $result = @pg_query($koneksi, $insert);
    if ($result === false) {
      $upload->revertUpload();
      echo json_encode(array(
        'response' => 'error',
        'alert' => pg_last_error($koneksi)
      ));
      die();
    } else {
      if (!empty($imgOld)) {
        $upload->clearImg($imgOld, $dir);
      }
      return $result;
    }
  }

  public function sendQueryWithImgPDO($koneksi, $insert, $arrOfInsert, $imgOld = [], $dir = ''){
    global $upload;
    if (empty($insert)) {
      $upload->revertUpload();
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan (#Query1)'
        )
      );
      die();
    }

    $isBusy = $koneksi->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql' && $koneksi->pgsqlGetNotify();
    if ($isBusy) {
      $upload->revertUpload();
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Sedang ada proses yang berjalan, coba lagi beberapa saat...'
        )
      );
      die();
    }

    $prepareSQL = $koneksi->prepare($insert);
    if (!$prepareSQL->execute($arrOfInsert)) {
      $upload->revertUpload();
      echo json_encode(array(
        'response' => 'error',
        'alert' => $prepareSQL->errorInfo()[2]
      ));
      die();
    } else {
      return $prepareSQL;
    }
  }

  public function getSeriesPkey($table, $column, $start, $end, $limit, $where) {
    $sql = "SELECT EXISTS(SELECT 1 FROM pg_catalog.pg_attribute WHERE attrelid = '$table'::regclass AND attname = '$column')";
    $query = $this->sendQuery($this->konek_sita_db(), $sql);
    $items = pg_fetch_all($query);
    if (!empty($items) && $items[0]['exists'] == 't') {
      $sql_series = "SELECT generate_series code
      FROM generate_series('$start'::integer, '$end'::integer)
      where generate_series not in (
        $where
      )
      limit '$limit'";
      $query_series = $this->sendQuery($this->konek_sita_db(), $sql_series);
      return pg_fetch_all($query_series);
    } else {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Ada error saat mengambil kunci, hubungi IT Software!'
        )
      );
      die();
    }
  }

}
?>