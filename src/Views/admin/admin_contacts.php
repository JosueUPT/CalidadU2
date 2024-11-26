<?php
namespace Views;

require_once __DIR__ . '/../../autoload.php';

use Config\Database;
use Controllers\AdminController;

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$adminController = new AdminController($conn);

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    if($adminController->deleteMessage($delete_id)) {
        header('location:admin_contacts.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mensajes</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
   
<?php include_once '../components/admin_header.php'; ?>

<section class="messages">
   <h1 class="title">mensajes</h1>

   <div class="box-container">
   <?php
      $messages = $adminController->getAllMessages();
      if(!empty($messages)){
         foreach($messages as $message){
   ?>
   <div class="box">
      <p> user id : <span><?php echo $message->getUserId(); ?></span> </p>
      <p> nombre : <span><?php echo $message->getName(); ?></span> </p>
      <p> numero : <span><?php echo $message->getNumber(); ?></span> </p>
      <p> email : <span><?php echo $message->getEmail(); ?></span> </p>
      <p> mensaje : <span><?php echo $message->getMessage(); ?></span> </p>
      <a href="admin_contacts.php?delete=<?php echo $message->getId(); ?>" 
         onclick="return confirm('¿Borrar este mensaje?');" 
         class="delete-btn">eliminar mensaje</a>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">¡No tienes mensajes!</p>';
      }
   ?>
   </div>
</section>

<script src="../../js/admin_script.js"></script>

</body>
</html>