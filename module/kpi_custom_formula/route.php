<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_realization_dept();
switch ($request) {

  case 'selectKpi':
    echo $classRoute->selectKpi();
    break;

  case 'getKpi':
    echo $classRoute->getKpi();
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