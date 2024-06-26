<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
switch ($request) {
  case 'getKpiDivisi':
    $classRoute = new kpi_bisnis_unit();
    echo $classRoute->getKpiDivisi();
    break;

  case 'getHeadDivisi':
    $classRoute = new kpi_bisnis_unit();
    echo $classRoute->getHeadDivisi();
    break;

  case 'getDataDivisi':
    $classRoute = new kpi_bisnis_unit();
    echo $classRoute->getDataDivisi();
    break;

  case 'jsonTahun':
    $classRoute = new kpi_bisnis_unit();
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