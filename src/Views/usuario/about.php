<?php

namespace Views;

require_once '../../Config/Database.php';
require_once '../../Controllers/ProductController.php';

use Config\Database;
use Controllers\ProductController;

session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$productController = new ProductController($conn);

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros</title>
    <link rel="icon" id="png" href="../../images/icon2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <?php include_once '../components/header.php'; ?>

    <div class="heading">
        <h3>Nosotros</h3>
        <p><a href="home.php">Inicio</a> / Nosotros</p>
    </div>

    <section class="about">
        <div class="flex">
            <div class="image">
                <img src="../../images/about-img.jpg" alt="img aboutmy">
            </div>
            <div class="content">
                <h3>¿Por qué elegirnos?</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet voluptatibus aut hic molestias, reiciendis natus fuga, cumque excepturi veniam ratione iure.</p>
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Impedit quos enim minima ipsa dicta officia corporis ratione saepe sed adipisci?</p>
                <a href="contact.php" class="btn">Contáctanos</a>
            </div>
        </div>
    </section>

    <section class="reviews">
        <h1 class="title">Reseñas de clientes</h1>
        <div class="box-container">

            <div class="box">
                <img src="../../images/pic-1.png" alt="">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia. Ducimus repudiandae dolore placeat.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Jose Alvarado</h3>
            </div>

            <div class="box">
                <img src="../../images/pic-2.png" alt="">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia. Ducimus repudiandae dolore placeat.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Gustavo Tacs</h3>
            </div>

            <div class="box">
                <img src="../../images/pic-3.png" alt="">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia. Ducimus repudiandae dolore placeat.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Gustavo Res</h3>
            </div>

            <div class="box">
                <img src="../../images/pic-4.png" alt="">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia. Ducimus repudiandae dolore placeat.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Karen Marca</h3>
            </div>

            <div class="box">
                <img src="../../images/pic-5.png" alt="">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia. Ducimus repudiandae dolore placeat.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Olvier Ato</h3>
            </div>

            <div class="box">
                <img src="../../images/pic-6.png" alt="">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia. Ducimus repudiandae dolore placeat.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Tomas Torres </h3>
            </div>

        </div>
    </section>

    <section class="authors">
        <h1 class="title">Grandes autores</h1>
        <div class="box-container">
            <div class="box">
                <img src="../../images/author-1.jpg" alt=""> 
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a> 
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
                <h3>Autor 1</h3>
            </div>

            <div class="box">
                <img src="../../images/author-5.jpg" alt=""> 
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a> 
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
                <h3>Autor 2</h3>
            </div>

            <div class="box">
                <img src="../../images/author-4.jpg" alt=""> 
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a> 
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
                <h3>Autor 3</h3>
            </div>


            <div class="box">
                <img src="../../images/author-6.jpg" alt=""> 
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a> 
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
                <h3>Autor 4</h3>
            </div>


            <div class="box">
                <img src="../../images/author-2.jpg" alt="">
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a> 
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
                <h3>Autor 5</h3>
            </div>

            <div class="box">
                <img src="../../images/author-3.jpg" alt=""> 
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a> 
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
                <h3>Autor 6</h3>
            </div>
        </div>
    </section>

    <?php include_once '../components/footer.php'; ?>

    <script src="../../js/script.js"></script>

</body>

</html>