<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
switch ($request) {
  case 'getKpiCorporate':
    $classRoute = new kpi_corporate();
    echo $classRoute->getKpiCorporate();
    break;

  case 'jsonTahun':
    $classRoute = new kpi_corporate();
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