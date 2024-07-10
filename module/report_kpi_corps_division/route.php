<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new report_kpi_division_corporate();
switch ($request) {
  case 'getKpiDivisionCorporate':
    echo $classRoute->getKpiDivisionCorporate();
    break;

  case 'jsonTahun':
    echo $classRoute->jsonTahun();
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