<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new position_master();
switch ($request) {
  case 'getPosition':
    echo $classRoute->getPosition();
    break;

  case 'jsonPosition':
    echo $classRoute->jsonPosition();
    break;

  case 'addPosition':
    echo $classRoute->addPosition();
    break;

  case 'editPosition':
    echo $classRoute->editPosition();
    break;

  case 'deletePosition':
    echo $classRoute->deletePosition();
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