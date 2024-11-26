<?php
require_once '../../Config/Database.php';
require_once '../../Controllers/UserController.php';
require_once '../../Models/User.php';

use Config\Database;
use Controllers\UserController;

session_start();

$db = new Database();
$conn = $db->connect();
$userController = new UserController($conn);

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $email = $_POST['email'];
   $password = $_POST['password'];
   $cpassword = $_POST['cpassword'];

   if ($password == $cpassword) {
      $userData = [
         'name' => $name,
         'email' => $email,
         'password' => $password
      ];

      $result = $userController->register($userData);

      if ($result['success']) {
         header('Location: login.php');
         exit();
      } else {
         $message[] = $result['message'];
      }
   } else {
      $message[] = 'Las contraseñas no coinciden!';
   }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registro</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
   <?php if (isset($message)): ?>
      <?php foreach ($message as $msg): ?>
         <div class="message">
            <span><?= $msg ?></span>
            <button class="close-btn" aria-label="Cerrar mensaje"
               onclick="this.parentElement.remove();"
               onkeydown="if (event.key === 'Enter' || event.key === ' ') this.parentElement.remove();">
               <i class="fas fa-times"></i>
            </button>
         </div>

      <?php endforeach; ?>
   <?php endif; ?>

   <div class="form-container">
      <form action="" method="post">
         <h3>Regístrate ahora</h3>
         <input type="text" name="name" placeholder="Nombre" required class="box">
         <input type="email" name="email" placeholder="Email" required class="box">
         <input type="password" name="password" placeholder="Contraseña" required class="box">
         <input type="password" name="cpassword" placeholder="Confirmar contraseña" required class="box">
         <input type="submit" name="submit" value="Registrarse" class="btn">
         <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión ahora</a></p>
      </form>
   </div>
</body>

</html>