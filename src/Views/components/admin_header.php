<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('location: ../auth/login.php');
    exit();
}

if(isset($message)){
    foreach($message as $msg){
        echo '
        <div class="message">
            <span>'.htmlspecialchars($msg).'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">
   <div class="flex">
      <a href="../admin/admin_page.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="../admin/admin_page.php">Inicio</a>
         <a href="../admin/admin_products.php">Productos</a>
         <a href="../admin/admin_orders.php">Ordenes</a>
         <a href="../admin/admin_users.php">Usuarios</a>
         <a href="../admin/admin_contacts.php">Mensajes</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="account-box">
         <p>nombre de usuario : <span><?php 
            echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); 
         ?></span></p>
         <p>email : <span><?php 
            echo htmlspecialchars($_SESSION['user_email'] ?? 'correo@ejemplo.com'); 
         ?></span></p>
         <a href="../auth/logout.php" class="delete-btn">cerrar sesion</a>
      </div>
   </div>
</header>