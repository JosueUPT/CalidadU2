<?php
namespace Views;

require_once __DIR__ . '/../../autoload.php';

use Config\Database;
use Controllers\UserController;

session_start();

$db = new Database();
$conn = $db->connect();
$userController = new UserController($conn);

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $userController->loginUser($email, $password);
    
    if($result['success']){
        if($result['user_type'] == 'admin'){
            header('location:../admin/admin_page.php');
        }else{
            header('location:../usuario/home.php');
        }
        exit();
    } else {
        $message[] = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>'.$msg.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<div class="form-container">
    <form action="" method="post">
        <h3>Ingresa</h3>
        <input type="email" name="email" placeholder="Ingresa tu email" required class="box">
        <input type="password" name="password" placeholder="Ingresa tu contraseña" required class="box">
        <input type="submit" name="submit" value="Ingresar" class="btn">
        <p>No tienes cuenta? <a href="register.php">Regístrate ahora</a></p>
    </form>
</div>

</body>
</html>
