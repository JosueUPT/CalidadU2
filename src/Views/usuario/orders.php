<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/OrderController.php';

use Config\Database;
use Controllers\OrderController;

session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$orderController = new OrderController($conn);

$user_id = $_SESSION['user_id'];

$orders = $orderController->getUserOrders($user_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pedidos</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
   
<?php include_once '../components/header.php'; ?>

<div class="heading">
   <h3>Pedidos</h3>
   <p><a href="home.php">Inicio</a> / Pedidos</p>
</div>

<section class="placed-orders">
   <h1 class="title">Pedidos realizados</h1>
   <div class="box-container">
      <?php
      if(!empty($orders)){
         foreach($orders as $order){
      ?>
      <div class="box">
         <p> Fecha : <span><?php echo $order->getPlacedOn(); ?></span> </p>
         <p> Nombre : <span><?php echo $order->getName(); ?></span> </p>
         <p> Número : <span><?php echo $order->getNumber(); ?></span> </p>
         <p> Email : <span><?php echo $order->getEmail(); ?></span> </p>
         <p> Dirección : <span><?php echo $order->getAddress(); ?></span> </p>
         <p> Método de pago : <span><?php echo $order->getMethod(); ?></span> </p>
         <p> Tus pedidos : <span><?php echo $order->getTotalProducts(); ?></span> </p>
         <p> Precio total : <span>S/. <?php echo $order->getTotalPrice(); ?> Soles</span> </p>
         <p> Estado del pago : 
            <span style="color:<?php echo ($order->getPaymentStatus() == 'pendiente') ? 'red' : 'green'; ?>;">
               <?php echo $order->getPaymentStatus(); ?>
            </span>
         </p>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">¡Aún no se han realizado pedidos!</p>';
      }
      ?>
   </div>
</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 