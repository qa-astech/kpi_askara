<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new kpi_bisnis_unit();
switch ($request) {
  case 'getKpiBisnisUnit':
    echo $classRoute->getKpiBisnisUnit();
    break;

  case 'addKpiBisnisUnit':
    echo $classRoute->addKpiBisnisUnit();
    break;

  case 'editKpiBisnisUnit':
    echo $classRoute->editKpiBisnisUnit();
    break;

  case 'publishKpiBisnisUnit':
    echo $classRoute->publishKpiBisnisUnit();
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