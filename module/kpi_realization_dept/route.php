<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_realization_dept();
switch ($request) {

  case 'selectKpiRealization':
    echo $classRoute->selectKpiRealization();
    break;

  case 'getKpiRealization':
    echo $classRoute->getKpiRealization();
    break;

  case 'sendKpiRealization':
    echo $classRoute->sendKpiRealization();
    break;
    
  case 'getFileKpiRealization':
    echo $classRoute->getFileKpiRealization();
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