<?php
namespace Controllers;

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/User.php';
use Models\Order;
use Models\Product;
use Models\User;

class OrderController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getOrders($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ?");
            $stmt->execute([$userId]);
            $orders = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $order = new Order();
                $order->setId($row['id']);
                $order->setUserId($row['user_id']);
                $order->setName($row['name']);
                $order->setNumber($row['number']);
                $order->setEmail($row['email']);
                $order->setMethod($row['method']);
                $order->setAddress($row['address']);
                $order->setTotalProducts($row['total_products']);
                $order->setTotalPrice($row['total_price']);
                $order->setPaymentStatus($row['payment_status']);
                $order->setPlacedOn($row['placed_on']);
                $orders[] = $order;
            }
            return $orders;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return [];
        }
    }

    public function updatePaymentStatus($orderId, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
            return $stmt->execute([$status, $orderId]);
        } catch (\Exception $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOrder($orderId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM orders WHERE id = ?");
            return $stmt->execute([$orderId]);
        } catch (\Exception $e) {
            error_log("Error al eliminar orden: " . $e->getMessage());
            return false;
        }
    }

    public function getAllOrders() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `orders`");
            $stmt->execute();
            $orders = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $order = new Order();
                $order->setUserId($row['user_id']);
                $order->setName($row['name']);
                $order->setEmail($row['email']);
                $order->setMethod($row['method']);
                $order->setAddress($row['address']);
                $order->setTotalProducts($row['total_products']);
                $order->setTotalPrice($row['total_price']);
                $orders[] = $order;
            }
            return $orders;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return [];
        }
    }

    public function createOrder($userData, $userId) {
        try {
            $order = new Order();
            $order->setUserId($userId);
            $order->setName($userData['name']);
            $order->setNumber($userData['number']);
            $order->setEmail($userData['email']);
            $order->setMethod($userData['method']);
            
            // Obtener productos del carrito
            $cartItems = $this->getCartItems($userId);
            if(empty($cartItems)) {
                return ['success' => false, 'message' => 'El carrito está vacío'];
            }

            // Calcular total y preparar lista de productos
            $cartTotal = 0;
            $products = [];
            foreach($cartItems as $item) {
                $products[] = $item['name'] . ' (' . $item['quantity'] . ')';
                $cartTotal += ($item['price'] * $item['quantity']);
            }
            $totalProducts = implode(', ', $products);

            // Formatear y establecer dirección
            $address = 'flat no. ' . $userData['flat'] . ', ' . 
                      $userData['street'] . ', ' . 
                      $userData['city'] . ', ' . 
                      $userData['country'] . ' - ' . 
                      $userData['pin_code'];
            
            // Establecer valores adicionales en el objeto Order
            $order->setAddress($address);
            $order->setTotalProducts($totalProducts);
            $order->setTotalPrice($cartTotal);
            
            // Verificar si la orden ya existe
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE 
                name = ? AND number = ? AND email = ? AND 
                method = ? AND address = ? AND 
                total_products = ? AND total_price = ?");
            
            $stmt->execute([
                $order->getName(),
                $order->getNumber(),
                $order->getEmail(),
                $order->getMethod(),
                $order->getAddress(),
                $order->getTotalProducts(),
                $order->getTotalPrice()
            ]);

            if($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => '¡Pedido ya realizado!'];
            }

            // Insertar nueva orden
            $stmt = $this->conn->prepare("INSERT INTO orders 
                (user_id, name, number, email, method, address, 
                total_products, total_price, placed_on) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $order->getUserId(),
                $order->getName(),
                $order->getNumber(),
                $order->getEmail(),
                $order->getMethod(),
                $order->getAddress(),
                $order->getTotalProducts(),
                $order->getTotalPrice(),
                date('d-M-Y')
            ]);

            // Limpiar carrito
            $this->clearCart($userId);

            return ['success' => true, 'message' => '¡Pedido realizado con éxito!'];
        } catch (\Exception $e) {
            error_log("Error al crear orden: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar el pedido'];
        }
    }

    private function getCartItems($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function clearCart($userId) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    public function getUserOrders($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ?");
            $stmt->execute([$userId]);
            $orders = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $order = new Order();
                $order->setUserId($row['user_id']);
                $order->setName($row['name']);
                $order->setNumber($row['number']);
                $order->setEmail($row['email']);
                $order->setMethod($row['method']);
                $order->setAddress($row['address']);
                $order->setTotalProducts($row['total_products']);
                $order->setTotalPrice($row['total_price']);
                $order->setPaymentStatus($row['payment_status']);
                $order->setPlacedOn($row['placed_on']);
                $orders[] = $order;
            }
            return $orders;
        } catch (\Exception $e) {
            error_log("Error al obtener órdenes del usuario: " . $e->getMessage());
            return [];
        }
    }

    public function getAllProducts() {
        try {
            $stmt = $this->conn->query("SELECT * FROM `products`");
            $products = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $product = new Product();
                $product->setName($row['name']);
                $product->setPrice($row['price']);
                $product->setImage($row['image']);
                $products[] = $product;
            }
            return $products;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return [];
        }
    }

    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `users`");
            $stmt->execute();
            $users = [];
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $user = new User();
                $user->setName($row['name']);
                $user->setEmail($row['email']);
                $user->setUserType($row['user_type']);
                $users[] = $user;
            }
            return $users;
        } catch (\Exception $e) {
            $this->handleDatabaseError($e);
            return [];
        }
    }

    private function handleDatabaseError(\Exception $e) {
        error_log("Error en la base de datos: " . $e->getMessage());
    }
} 