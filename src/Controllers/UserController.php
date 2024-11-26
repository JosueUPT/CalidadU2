<?php
namespace Controllers;

use Models\User;

class UserController {
    private $conn;
    private const HASH_COST = 12; // Costo de hash para password_hash

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => self::HASH_COST]);
    }

    private function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function registerUser($userData) {
        try {
            $user = new User();
            $user->setName($userData['name']);
            $user->setEmail($userData['email']);
            $user->setUserType($userData['user_type'] ?? 'user');

            // Verificar si el correo ya existe
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$user->getEmail()]);
            if($stmt->fetch()) {
                return ['success' => false, 'message' => 'El correo ya está registrado'];
            }

            // Usar password_hash en lugar de md5
            $stmt = $this->conn->prepare(
                "INSERT INTO users (name, email, password, user_type) 
                 VALUES (?, ?, ?, ?)"
            );
            
            $stmt->execute([
                $user->getName(),
                $user->getEmail(),
                $this->hashPassword($userData['password']),
                $user->getUserType()
            ]);
            
            return ['success' => true, 'message' => 'Registro exitoso!'];
        } catch (\Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en el registro'];
        }
    }

    public function loginUser($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user && $this->verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['user_email'] = $user['email'];
                return ['success' => true, 'user_type' => $user['user_type']];
            }

            return ['success' => false, 'message' => 'Correo o contraseña incorrectos'];
        } catch (\Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en el inicio de sesión'];
        }
    }

    public function logout() {
        try {
            // Asegurarse de que la sesión está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Limpiar todas las variables de sesión
            $_SESSION = array();
            
            // Destruir la sesión
            session_destroy();
            
            // Redireccionar al login
            header('location: ../auth/login.php');
            exit();
        } catch (\Exception $e) {
            error_log("Error en logout: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cerrar sesión'];
        }
    }

    public function getUserById($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, email, user_type FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return null;
        }
    }

    // Alias para mantener compatibilidad
    public function register($userData) {
        return $this->registerUser($userData);
    }
} 