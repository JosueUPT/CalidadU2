<?php
// Si el usuario ya está logueado, redirigir según su tipo
if(isset($_SESSION['user_id'])){
   if($_SESSION['user_type'] == 'admin'){
      header('location:views/admin/admin_page.php');
   }else{
      header('location:views/home.php');
   }
}else{
   // Si no está logueado, redirigir al login
   header('location:views/auth/login.php');
}
?>