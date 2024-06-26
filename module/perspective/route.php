<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new perspective();
switch ($request) {
  case 'getPerspective':
    echo $classRoute->getPerspective();
    break;

  case 'jsonPerspective':
    echo $classRoute->jsonPerspective();
    break;

  case 'addPerspective':
    echo $classRoute->addPerspective();
    break;

  case 'editPerspective':
    echo $classRoute->editPerspective();
    break;

  case 'deletePerspective':
    echo $classRoute->deletePerspective();
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