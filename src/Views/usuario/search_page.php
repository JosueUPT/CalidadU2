<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/SearchController.php';
require_once '../../Controllers/ProductController.php';

use Config\Database;
use Controllers\SearchController;
use Controllers\ProductController;

session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$searchController = new SearchController($conn);
$productController = new ProductController($conn);

$user_id = $_SESSION['user_id'];
$message = [];
$products = [];

// Procesar búsqueda
if(isset($_POST['submit'])) {
    $search_term = $_POST['search'];
    $products = $searchController->searchProducts($search_term);
}

// Procesar agregar al carrito
if(isset($_POST['add_to_cart'])) {
    $result = $productController->addToCart(
        $user_id,
        [
            'product_name' => $_POST['product_name'],
            'product_price' => $_POST['product_price'],
            'product_quantity' => $_POST['product_quantity'],
            'product_image' => $_POST['product_image']
        ]
    );
    $message[] = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Búsqueda</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
   
<?php include_once '../components/header.php'; ?>

<div class="heading">
   <h3>Busca tus series o películas</h3>
   <p><a href="home.php">Inicio</a> / Buscar</p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="buscar productos..." class="box">
      <input type="submit" name="submit" value="Buscar" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">
   <div class="box-container">
   <?php
   if(isset($_POST['submit'])) {
      if(!empty($products)) {
         foreach($products as $product) {
   ?>
   <form action="" method="post" class="box">
      <img src="../../uploaded_img/<?php echo htmlspecialchars($product['image']); ?>" alt="" class="image">
      <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
      <div class="price">S/. <?php echo htmlspecialchars($product['price']); ?> Soles</div>
      <input type="number" class="qty" name="product_quantity" min="1" value="1">
      <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
      <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>">
      <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
      <input type="submit" class="btn" value="Agregar al carrito" name="add_to_cart">
   </form>
   <?php
         }
      } else {
         echo '<p class="empty">¡No se han encontrado resultados!</p>';
      }
   } else {
      echo '<p class="empty">¡Busca algo!</p>';
   }
   ?>
   </div>
</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 