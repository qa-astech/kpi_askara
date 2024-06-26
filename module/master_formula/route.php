<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new formula_master();
switch ($request) {
  case 'getFormula':
    echo $classRoute->getFormula();
    break;

  case 'jsonFormula':
    echo $classRoute->jsonFormula();
    break;

  case 'jsonFormula':
    echo $classRoute->jsonFormula();
    break;

  case 'addFormula':
    echo $classRoute->addFormula();
    break;

  case 'editFormula':
    echo $classRoute->editFormula();
    break;

  case 'deleteFormula':
    echo $classRoute->deleteFormula();
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