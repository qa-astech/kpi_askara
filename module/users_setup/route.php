<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../koneksi.php');
require_once('serverside.php');

$request = @$_REQUEST["act"];
$classRoute = new users_setup();
switch ($request) {
  
  case 'getUsersSetup':
    echo $classRoute->getUsersSetup();
    break;

  case 'searchDeptComp':
    echo $classRoute->searchDeptComp();
    break;
    
  case 'searchDeptCompCorps':
    echo $classRoute->searchDeptCompCorps();
    break;

  case 'addUsersSetup':
    echo $classRoute->addUsersSetup();
    break;

  case 'editUsersSetup':
    echo $classRoute->editUsersSetup();
    break;

  case 'deleteUsersSetup':
    echo $classRoute->deleteUsersSetup();
    break;

  case 'jsonUsersSetup':
    echo $classRoute->jsonUsersSetup();
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