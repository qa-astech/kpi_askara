<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new company_detail();
switch ($request) {
  
  case 'getDetailCompany':
    echo $classRoute->getDetailCompany();
    break;

  case 'searchDepartmentFromCompany':
    echo $classRoute->searchDepartmentFromCompany();
    break;

  case 'getAllDepartmentFromCompany':
    echo $classRoute->getAllDepartmentFromCompany();
    break;

  case 'addDetailCompany':
    echo $classRoute->addDetailCompany();
    break;

  case 'editDetailCompany':
    echo $classRoute->editDetailCompany();
    break;

  case 'deleteDetailCompany':
    echo $classRoute->deleteDetailCompany();
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