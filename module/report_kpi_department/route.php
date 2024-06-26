<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
switch ($request) {
  case 'getKpiDepartement':
    $classRoute = new kpi_department();
    echo $classRoute->getKpiDepartement();
    break;

  case 'getHeadDepartement':
    $classRoute = new kpi_department();
    echo $classRoute->getHeadDepartement();
    break;

  case 'getDataDepartement':
    $classRoute = new kpi_department();
    echo $classRoute->getDataDepartement();
    break;

  case 'jsonTahun':
    $classRoute = new kpi_department();
    echo $classRoute->jsonTahun();
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