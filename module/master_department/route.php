<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new department_master();
switch ($request) {
  case 'getDepartment':
    echo $classRoute->getDepartment();
    break;

  case 'jsonDepartment':
    echo $classRoute->jsonDepartment();
    break;

  case 'addDepartment':
    echo $classRoute->addDepartment();
    break;

  case 'editDepartment':
    echo $classRoute->editDepartment();
    break;

  case 'deleteDepartment':
    echo $classRoute->deleteDepartment();
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