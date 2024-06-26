<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new satuan_master();
switch ($request) {
  case 'getSatuan':
    echo $classRoute->getSatuan();
    break;

  case 'jsonSatuan':
    echo $classRoute->jsonSatuan();
    break;

  case 'addSatuan':
    echo $classRoute->addSatuan();
    break;

  case 'editSatuan':
    echo $classRoute->editSatuan();
    break;

  case 'deleteSatuan':
    echo $classRoute->deleteSatuan();
    break;

  default:
    echo json_encode(
      array(
        'response'=>'error',
        'alert'=>'Request Undefined! Report to IT!'
      )
    );
}
?>