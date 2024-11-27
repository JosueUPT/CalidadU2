<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/OrderController.php';
require_once '../../Controllers/ProductController.php';

use Config\Database;
use Controllers\OrderController;
use Controllers\ProductController;

session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$orderController = new OrderController($conn);
$productController = new ProductController($conn);

$user_id = $_SESSION['user_id'];
$message = [];

if(isset($_POST['order_btn'])) {
    $result = $orderController->createOrder($_POST, $user_id);
    $message[] = $result['message'];
}

$cartItems = $productController->getCartItems($user_id);
$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
   
<?php include_once '../components/header.php'; ?>

<div class="heading">
   <h3>Verificar Pedido</h3>
   <p><a href="home.php">Inicio</a> / Verificar</p>
</div>

<section class="display-order">
   <?php
   if(!empty($cartItems)){
      foreach($cartItems as $item){
         $total_price = ($item['price'] * $item['quantity']);
         $grand_total += $total_price;
   ?>
   <p>
      <?php echo htmlspecialchars($item['name']); ?> 
      <span>(S/. <?php echo $item['price'].' x '.$item['quantity']; ?>)</span>
   </p>
   <?php
      }
   }else{
      echo '<p class="empty">Tu carrito está vacío</p>';
   }
   ?>
   <div class="grand-total">Total a pagar: <span>S/. <?php echo $grand_total; ?> Soles</span></div>
</section>

<section class="checkout">
   <form action="" method="post">
      <h3>Haga su pedido</h3>
      <div class="flex">
         <div class="inputBox">
            <span>Su nombre:</span>
            <input type="text" name="name" required placeholder="Ingrese su nombre">
         </div>
         <div class="inputBox">
            <span>Su número:</span>
            <input type="number" name="number" required placeholder="Ingrese su número">
         </div>
         <div class="inputBox">
            <span>Su email:</span>
            <input type="email" name="email" required placeholder="Ingrese su email">
         </div>
         <div class="inputBox">
            <span>Método de pago:</span>
            <select name="method">
               <option value="Pago en persona">Pago en persona</option>
               <option value="Tarjeta de crédito">Tarjeta de crédito</option>
               <option value="PayPal">PayPal</option>
               <option value="Débito">Débito</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Número de casa:</span>
            <input type="number" min="0" name="flat" required placeholder="Ej. Nro. 123">
         </div>
         <div class="inputBox">
            <span>Dirección:</span>
            <input type="text" name="street" required placeholder="Ej. Nombre de calle">
         </div>
         <div class="inputBox">
            <span>Ciudad:</span>
            <input type="text" name="city" required placeholder="Ej. Lima">
         </div>
         <div class="inputBox">
            <span>Distrito:</span>
            <input type="text" name="state" required placeholder="Ej. Miraflores">
         </div>
         <div class="inputBox">
            <span>País:</span>
            <input type="text" name="country" required placeholder="Ej. Perú">
         </div>
         <div class="inputBox">
            <span>Código postal:</span>
            <input type="number" min="0" name="pin_code" required placeholder="Ej. 123456">
         </div>
      </div>
      <input type="submit" value="Ordenar ahora" class="btn" name="order_btn">
   </form>
</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 