<?php
namespace Controllers;

require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Product.php';

use Models\Cart;
use Models\Product;

class ProductController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getLatestProducts($limit = 6) {
        try {
            $query = "SELECT * FROM products LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            $products = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $product = new Product();
                $product->setId($row['id']);
                $product->setName($row['name']);
                $product->setPrice($row['price']);
                $product->setImage($row['image']);
                $products[] = $product;
            }
            return $products;
        } catch (\Exception $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }

    public function addToCart($userId, $productData) {
        try {
            $cart = new Cart();
            $cart->setUserId($userId);
            $cart->setName($productData['product_name']);
            $cart->setPrice($productData['product_price']);
            $cart->setQuantity($productData['product_quantity']);
            $cart->setImage($productData['product_image']);
            
            // Verificar si el producto ya está en el carrito
            $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id = ? AND name = ?");
            $stmt->execute([$userId, $cart->getName()]);
            
            if($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'El producto ya está en el carrito'];
            }

            // Añadir al carrito
            $query = "INSERT INTO cart (user_id, name, price, quantity, image) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->execute([
                $cart->getUserId(),
                $cart->getName(),
                $cart->getPrice(),
                $cart->getQuantity(),
                $cart->getImage()
            ]);
            
            return ['success' => true, 'message' => 'Producto añadido al carrito'];
        } catch (\Exception $e) {
            error_log("Error al añadir al carrito: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al añadir al carrito'];
        }
    }

    public function getAllProducts() {
        try {
            $query = "SELECT * FROM products";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $products = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $product = new Product();
                $product->setId($row['id']);
                $product->setName($row['name']);
                $product->setPrice($row['price']);
                $product->setImage($row['image']);
                $products[] = $product;
            }
            return $products;
        } catch (\Exception $e) {
            error_log("Error al obtener todos los productos: " . $e->getMessage());
            return [];
        }
    }

    public function getCartItems($userId) {
        try {
            $query = "SELECT * FROM cart WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error al obtener items del carrito: " . $e->getMessage());
            return [];
        }
    }

    public function updateCartQuantity($cartId, $quantity) {
        try {
            $query = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$quantity, $cartId]);
            return ['success' => true, 'message' => '¡Cantidad actualizada!'];
        } catch (\Exception $e) {
            error_log("Error al actualizar cantidad: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar cantidad'];
        }
    }

    public function deleteCartItem($cartId) {
        try {
            $query = "DELETE FROM cart WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$cartId]);
            return true;
        } catch (\Exception $e) {
            error_log("Error al eliminar item: " . $e->getMessage());
            return false;
        }
    }

    public function deleteAllCartItems($userId) {
        try {
            $query = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            return true;
        } catch (\Exception $e) {
            error_log("Error al eliminar todos los items: " . $e->getMessage());
            return false;
        }
    }
} 