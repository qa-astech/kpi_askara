<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_department();
switch ($request) {
  case 'getKpiDivisionCorps':
    echo $classRoute->getKpiDivisionCorps();
    break;

  case 'addKpiDivisionCorps':
    echo $classRoute->addKpiDivisionCorps();
    break;

  case 'editKpiDivisionCorps':
    echo $classRoute->editKpiDivisionCorps();
    break;

  case 'publishKpiDivisionCorps':
    echo $classRoute->publishKpiDivisionCorps();
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