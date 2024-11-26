<?php
namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/ContactController.php';

use Config\Database;
use Controllers\ContactController;

session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$contactController = new ContactController($conn);

$user_id = $_SESSION['user_id'];

if(isset($_POST['send'])) {
    $_POST['user_id'] = $user_id;
    $result = $contactController->sendMessage($_POST);
    $message[] = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contacto</title>
   <link rel="icon" id="png" href="../../images/icon2.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
   
<?php include_once '../components/header.php'; ?>

<div class="heading">
   <h3>Contáctanos</h3>
   <p><a href="home.php">Inicio</a> / Contáctanos</p>
</div>

<section class="contact">
   <form action="" method="post">
      <h3>Escríbenos...</h3>
      <input type="text" name="name" required placeholder="ingresa tu nombre" class="box">
      <input type="email" name="email" required placeholder="ingresa tu email" class="box">
      <input type="number" name="number" required placeholder="ingresa tu numero" class="box">
      <textarea name="message" class="box" placeholder="ingresa tu mensaje" id="" cols="30" rows="10"></textarea>
      <input type="submit" value="enviar mensaje" name="send" class="btn">
   </form>
</section>

<?php include_once '../components/footer.php'; ?>

<script src="../../js/script.js"></script>

</body>
</html> 