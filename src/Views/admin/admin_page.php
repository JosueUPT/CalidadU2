<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/AdminController.php';

use Config\Database;
use Controllers\AdminController;

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$adminController = new AdminController($conn);

// Obtener datos del dashboard
$dashboardData = $adminController->getDashboardData();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
   
<?php include_once '../components/admin_header.php'; ?>

<section class="dashboard">
    <h1 class="title">Panel de Control</h1>

    <div class="box-container">
        <!-- Pedidos Pendientes -->
        <div class="box">
            <h3>S/.<?php echo $dashboardData['total_pendings']; ?> Soles</h3>
            <p>Total Pendientes</p>
        </div>

        <!-- Pagos Completados -->
        <div class="box">
            <h3>S/.<?php echo $dashboardData['total_completed']; ?> Soles</h3>
            <p>Pagos Completados</p>
        </div>

        <!-- Total Pedidos -->
        <div class="box">
            <h3><?php echo $dashboardData['orders_count']; ?></h3>
            <p>Pedidos Realizados</p>
        </div>

        <!-- Productos -->
        <div class="box">
            <h3><?php echo $dashboardData['products_count']; ?></h3>
            <p>Productos Añadidos</p>
        </div>

        <!-- Usuarios Normales -->
        <div class="box">
            <h3><?php echo $dashboardData['users_count']; ?></h3>
            <p>Usuarios Normales</p>
        </div>

        <!-- Administradores -->
        <div class="box">
            <h3><?php echo $dashboardData['admins_count']; ?></h3>
            <p>Administradores</p>
        </div>

        <!-- Total Cuentas -->
        <div class="box">
            <h3><?php echo $dashboardData['total_accounts']; ?></h3>
            <p>Total Cuentas</p>
        </div>

        <!-- Mensajes -->
        <div class="box">
            <h3><?php echo $dashboardData['messages_count']; ?></h3>
            <p>Nuevos Mensajes</p>
        </div>
    </div>
</section>


<script src="../../js/admin_script.js"></script>

</body>
</html>