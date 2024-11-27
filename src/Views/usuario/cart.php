<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/ProductController.php';
require_once '../../Models/Product.php';

use Config\Database;
use Controllers\ProductController;

session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$productController = new ProductController($conn);

$user_id = $_SESSION['user_id'];

if(isset($_POST['update_cart'])) {
    $result = $productController->updateCartQuantity(
        $_POST['cart_id'],
        $_POST['cart_quantity']
    );
    $message[] = $result['message'];
}

if(isset($_GET['delete'])) {
    if($productController->deleteCartItem($_GET['delete'])) {
        header('location: cart.php');
        exit();
    }
}

if(isset($_GET['delete_all'])) {
    if($productController->deleteAllCartItems($user_id)) {
        header('location: cart.php');
        exit();
    }
}

$cartItems = $productController->getCartItems($user_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Carrito</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
   
<?php include_once '../components/header.php'; ?>

<div class="heading">
   <h3>Carrito de Compras</h3>
   <p><a href="home.php">Inicio</a> / Carrito</p>
</div>

<section class="shopping-cart">
   <h1 class="title">Productos Añadidos</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         if(!empty($cartItems)){
            foreach($cartItems as $item){   
               $sub_total = ($item['quantity'] * $item['price']);
               $grand_total += $sub_total;
      ?>
      <div class="box">
         <a href="cart.php?delete=<?php echo $item['id']; ?>" 
            class="fas fa-times" 
            onclick="return confirm('¿Eliminar este producto del carrito?');">
         </a>
         <img src="../../uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" alt="">
         <div class="name"><?php echo htmlspecialchars($item['name']); ?></div>
         <div class="price">S/. <?php echo htmlspecialchars($item['price']); ?> Soles</div>
         <form action="" method="post">
            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
            <input type="number" min="1" name="cart_quantity" value="<?php echo $item['quantity']; ?>">
            <input type="submit" name="update_cart" value="actualizar" class="option-btn">
         </form>
         <div class="sub-total">
            Sub total: <span>S/. <?php echo $sub_total; ?> Soles</span>
         </div>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">Tu carrito está vacío</p>';
         }
      ?>
   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="cart.php?delete_all" 
         class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>" 
         onclick="return confirm('¿Borrar todo del carrito?');">
         Borrar Todo
      </a>
   </div>

   <div class="cart-total">
      <p>Total General: <span>S/. <?php echo $grand_total; ?> Soles</span></p>
      <div class="flex">
         <a href="shop.php" class="option-btn">Seguir Comprando</a>
         <a href="checkout.php" class="btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">
            Proceder al Pago
         </a>
      </div>
   </div>

</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 