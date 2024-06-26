<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new strategic_objective();
switch ($request) {
  case 'getStrategicObjective':
    echo $classRoute->getStrategicObjective();
    break;

  case 'jsonStrategicObjective':
    echo $classRoute->jsonStrategicObjective();
    break;

  case 'addStrategicObjective':
    echo $classRoute->addStrategicObjective();
    break;

  case 'editStrategicObjective':
    echo $classRoute->editStrategicObjective();
    break;

  case 'deleteStrategicObjective':
    echo $classRoute->deleteStrategicObjective();
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