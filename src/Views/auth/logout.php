<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/UserController.php';

use Config\Database;
use Controllers\UserController;

$db = new Database();
$conn = $db->connect();
$userController = new UserController($conn);
$userController->logout(); 