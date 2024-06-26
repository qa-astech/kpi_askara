<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_department();
switch ($request) {
  case 'getKpiDepartment':
    echo $classRoute->getKpiDepartment();
    break;

  case 'addKpiDepartment':
    echo $classRoute->addKpiDepartment();
    break;

  case 'editKpiDepartment':
    echo $classRoute->editKpiDepartment();
    break;

  case 'publishKpiDepartment':
    echo $classRoute->publishKpiDepartment();
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