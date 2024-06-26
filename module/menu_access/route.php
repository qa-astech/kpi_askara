<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new menu_access();
switch ($request) {
  case 'getMenuAccess':
    echo $classRoute->getMenuAccess();
    break;

  case 'addMenuAccess':
    echo $classRoute->addMenuAccess();
    break;

  case 'editMenuAccess':
    echo $classRoute->editMenuAccess();
    break;

  case 'deleteMenuAccess':
    echo $classRoute->deleteMenuAccess();
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