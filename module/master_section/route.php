<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new section_master();
switch ($request) {
  case 'getSection':
    echo $classRoute->getSection();
    break;

  case 'jsonSection':
    echo $classRoute->jsonSection();
    break;

  case 'addSection':
    echo $classRoute->addSection();
    break;

  case 'editSection':
    echo $classRoute->editSection();
    break;

  case 'deleteSection':
    echo $classRoute->deleteSection();
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