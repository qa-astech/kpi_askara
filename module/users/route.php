<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new users();
switch ($request) {
  case 'getUsers':
    echo $classRoute->getUsers();
    break;

  case 'addUsers':
    echo $classRoute->addUsers();
    break;

  case 'editUsers':
    echo $classRoute->editUsers();
    break;

  case 'resetUsers':
    echo $classRoute->resetUsers();
    break;

  case 'deleteUsers':
    echo $classRoute->deleteUsers();
    break;

  case 'jsonUsers':
    echo $classRoute->jsonUsers();
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