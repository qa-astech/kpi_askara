<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
switch ($request) {
  case 'getKpiBisnisUnit':
    $classRoute = new kpi_bisnis_unit();
    echo $classRoute->getKpiBisnisUnit();
    break;

  case 'getDataBisnisUnit':
    $classRoute = new kpi_bisnis_unit();
    echo $classRoute->getDataBisnisUnit();
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