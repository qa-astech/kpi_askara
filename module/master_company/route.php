<?php
// header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new company_master();
switch ($request) {
  case 'getCompany':
    echo $classRoute->getCompany();
    break;

  case 'jsonCompany':
    echo $classRoute->jsonCompany();
    break;

  case 'searchSection':
    echo $classRoute->searchSection();
    break;

  case 'searchPosition':
    echo $classRoute->searchPosition();
    break;

  case 'searchPlant':
    echo $classRoute->searchPlant();
    break;
    
  case 'searchGolongan':
    echo $classRoute->searchGolongan();
    break;

  case 'addCompany':
    echo $classRoute->addCompany();
    break;

  case 'editCompany':
    echo $classRoute->editCompany();
    break;

  case 'deleteCompany':
    echo $classRoute->deleteCompany();
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