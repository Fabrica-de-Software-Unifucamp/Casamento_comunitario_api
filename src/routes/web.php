<?php

require_once '../config/database.php';
require_once '../app/models/UserModel.php';
require_once '../app/controllers/UserController.php';
require_once '../app/views/UserView.php';

$db = (new Database())->connect();
$userModel = new UserModel($db);
$userView = new UserView(); 
$userController = new UserController($userModel, $userView); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['route'] == 'users') {
    $userController->createUser();
}
?>
