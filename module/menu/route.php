<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new menu();
switch ($request) {
  case 'getMenu':
    echo $classRoute->getMenu();
    break;

  case 'searchIndexMenu':
    echo $classRoute->searchIndexMenu();
    break;

  case 'addMenu':
    echo $classRoute->addMenu();
    break;

  case 'editMenu':
    echo $classRoute->editMenu();
    break;

  case 'deleteMenu':
    echo $classRoute->deleteMenu();
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