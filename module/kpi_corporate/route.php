<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_corporate();
switch ($request) {
  case 'getKpiCorporate':
    echo $classRoute->getKpiCorporate();
    break;

  case 'addKpiCorporate':
    echo $classRoute->addKpiCorporate();
    break;

  case 'editKpiCorporate':
    echo $classRoute->editKpiCorporate();
    break;

  case 'publishKpiCorporate':
    echo $classRoute->publishKpiCorporate();
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