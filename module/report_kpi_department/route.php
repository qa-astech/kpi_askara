<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new report_kpi_department();
switch ($request) {
  case 'getKpiDepartment':
    echo $classRoute->getKpiDepartment();
    break;

  case 'jsonTahun':
    echo $classRoute->jsonTahun();
    break;

  case 'jsonCompany':
    echo $classRoute->jsonCompany();
    break;

  case 'jsonDepartment':
    echo $classRoute->jsonDepartment();
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