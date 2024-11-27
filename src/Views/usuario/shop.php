<?php
namespace Views;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Config/Database.php';
require_once '../../Controllers/ProductController.php';
require_once '../../Models/Product.php';

use Config\Database;
use Controllers\ProductController;

if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$productController = new ProductController($conn);

if(isset($_POST['add_to_cart'])) {
    $result = $productController->addToCart($_SESSION['user_id'], $_POST);
    $message[] = $result['message'];
}

$products = $productController->getAllProducts();
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tienda</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

<?php include_once '../components/header.php'; ?>

<div class="heading">
   <h3>Nuestra tienda</h3>
   <p><a href="home.php">Inicio</a> / Tienda </p>
</div>

<section class="products">
   <h1 class="title">Últimos productos</h1>
   <div class="box-container">
      <?php if(!empty($products)): ?>
         <?php foreach($products as $product): ?>
         <form action="" method="post" class="box">
            <img class="image" src="../../uploaded_img/<?php echo htmlspecialchars($product->getImage()); ?>" alt="">
            <div class="name"><?php echo htmlspecialchars($product->getName()); ?></div>
            <div class="price">S/. <?php echo htmlspecialchars($product->getPrice()); ?> Soles</div>
            <input type="number" min="1" name="product_quantity" value="1" class="qty">
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product->getName()); ?>">
            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product->getPrice()); ?>">
            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product->getImage()); ?>">
            <input type="submit" value="Añadir al carrito" name="add_to_cart" class="btn">
         </form>
         <?php endforeach; ?>
      <?php else: ?>
         <p class="empty">¡Aún no hay productos añadidos!</p>
      <?php endif; ?>
   </div>
</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 