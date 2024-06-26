<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_lock_unlock();
switch ($request) {
  case 'selectKpiLockUnlock':
    echo $classRoute->selectKpiLockUnlock();
    break;

  case 'getKpiLockUnlock':
    echo $classRoute->getKpiLockUnlock();
    break;

  case 'sendKpiLockUnlock':
    echo $classRoute->sendKpiLockUnlock();
    break;

  case 'updateLockCutOff':
    echo $classRoute->updateLockCutOff();
    break;

  default:
    echo json_encode(
      array(
        'response'=>'error',
        'alert'=>'Permintaan tidak ditemukan! Silahkan lapor IT!'
      )
    );
}
?>