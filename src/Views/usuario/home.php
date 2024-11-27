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

if(isset($_POST['add_to_cart'])) {
    $result = $productController->addToCart($_SESSION['user_id'], $_POST);
    $message[] = $result['message'];
}

$products = $productController->getLatestProducts();
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
   
<?php include_once '../components/header.php'; ?>

<section class="home">
   <div class="content">
      <h3>Peliculas y series en tus manos</h3>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, quod? Reiciendis ut porro iste totam.</p>
      <a href="about.php" class="white-btn">Descubrir Más</a>
   </div>
</section>

<section class="products">
   <h1 class="title">ÚLTIMOS productos</h1>
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
            <input type="submit" value="agregar al carrito" name="add_to_cart" class="btn">
         </form>
         <?php endforeach; ?>
      <?php else: ?>
         <p class="empty">¡Aún no hay productos añadidos!</p>
      <?php endif; ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="../shop.php" class="option-btn">Cargar mas</a>
   </div>
</section>

<section class="about">
   <div class="flex">
      <div class="image">
         <img src="../../images/about-img.jpg" alt="img">
      </div>
      <div class="content">
         <h3>Sobre nosotros</h3>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Impedit quos enim minima ipsa dicta officia corporis ratione saepe sed adipisci?</p>
         <a href="about.php" class="btn">Leer más</a>
      </div>
   </div>
</section>

<section class="home-contact">
   <div class="content">
      <h3>Tienes alguna pregunta?</h3>
      <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Atque cumque exercitationem repellendus, amet ullam voluptatibus?</p>
      <a href="contact.php" class="white-btn">Contáctanos</a>
   </div>
</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 