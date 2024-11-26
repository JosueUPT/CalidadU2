<?php
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Models/User.php';

use Config\Database;
use Controllers\AdminController;

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin'){
   header('location: ../auth/login.php');
   exit();
}

$db = new Database();
$conn = $db->connect();
$adminController = new AdminController($conn);

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   if($adminController->deleteUser($delete_id)) {
       header('location: admin_users.php');
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
   <title>Usuarios</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
   
<?php include_once '../components/admin_header.php'; ?>

<section class="users">
   <h1 class="title">Cuentas de usuario</h1>

   <div class="box-container">
      <?php
         $users = $adminController->getAllUsers();
         foreach($users as $user){
      ?>
      <div class="box">
         <p> user id : <span><?php echo $user->getId(); ?></span> </p>
         <p> nombre de usuario : <span><?php echo $user->getName(); ?></span> </p>
         <p> email : <span><?php echo $user->getEmail(); ?></span> </p>
         <p> tipo de usuario : <span style="color:<?php if($user->getUserType() == 'admin'){ echo 'var(--orange)'; } ?>">
            <?php echo $user->getUserType(); ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $user->getId(); ?>" 
            onclick="return confirm('¿Eliminar este usuario?');" 
            class="delete-btn">eliminar usuario</a>
      </div>
      <?php
         }
      ?>
   </div>
</section>

<script src="../../js/admin_script.js"></script>

</body>
</html>