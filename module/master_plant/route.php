<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new plant_master();
switch ($request) {
  case 'getPlant':
    echo $classRoute->getPlant();
    break;

  case 'jsonPlant':
    echo $classRoute->jsonPlant();
    break;

  case 'addPlant':
    echo $classRoute->addPlant();
    break;

  case 'editPlant':
    echo $classRoute->editPlant();
    break;

  case 'deletePlant':
    echo $classRoute->deletePlant();
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