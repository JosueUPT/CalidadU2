<?php
if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}

try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_rows_number = $stmt->fetchColumn();
} catch (\PDOException $e) {
    error_log("Error al obtener items del carrito: " . $e->getMessage());
    $cart_rows_number = 0;
}
?>

<header class="header">
   <div class="header-1">
      <div class="flex">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <p>
            <?php if(isset($_SESSION['user_name'])): ?>
                Bienvenido, <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <?php else: ?>
                nuevo <a href="../auth/login.php">ingresar</a> | <a href="../auth/register.php">registrar</a>
            <?php endif; ?>
         </p>
      </div>
   </div>

   <div class="header-2">
      <div class="flex">
         <a href="home.php" class="logo">Cinemas</a>

         <nav class="navbar">
            <a href="home.php">Inicio</a>
            <a href="about.php">Nosotros</a>
            <a href="shop.php">Tienda</a>
            <a href="contact.php">Contactanos</a>
            <a href="orders.php">Pedidos</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <div id="user-btn" class="fas fa-user"></div>
            <a href="cart.php"> 
                <i class="fas fa-shopping-cart"></i> 
                <span>(<?php echo $cart_rows_number; ?>)</span> 
            </a>
         </div>

         <div class="user-box">
            <?php if(isset($_SESSION['user_name'])): ?>
                <p>nombre de usuario: <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></p>
                <p>email: <span><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></span></p>
                <a href="../auth/logout.php" class="delete-btn">cerrar sesión</a>
            <?php endif; ?>
         </div>
      </div>
   </div>
</header>

<style>
.user-box {
    position: absolute;
    top: 120%;
    right: 2rem;
    background-color: white;
    border-radius: .5rem;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
    border: .1rem solid #333;
    padding: 2rem;
    text-align: center;
    width: 30rem;
    display: none;
}

.user-box.active {
    display: block;
}

.icons div {
    cursor: pointer;
}

.header .icons > * {
    font-size: 2.5rem;
    margin-left: 1.5rem;
    cursor: pointer;
}

.header .icons span {
    font-size: 1.5rem;
}
</style>

<script>
document.querySelector('#user-btn').onclick = () => {
    document.querySelector('.user-box').classList.toggle('active');
    document.querySelector('.shopping-cart').classList.remove('active');
}

document.querySelector('#menu-btn').onclick = () => {
    document.querySelector('.navbar').classList.toggle('active');
    document.querySelector('.user-box').classList.remove('active');
    document.querySelector('.shopping-cart').classList.remove('active');
}
</script>
