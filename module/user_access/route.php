<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new user_access();
switch ($request) {
  case 'getUserAccess':
    echo $classRoute->getUserAccess();
    break;

  case 'getMenuAccess':
    echo $classRoute->getMenuAccess();
    break;

  case 'editUserAccess':
    echo $classRoute->editUserAccess();
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