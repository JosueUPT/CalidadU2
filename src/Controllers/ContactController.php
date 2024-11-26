<?php
namespace Controllers;

require_once __DIR__ . '/../Models/Message.php';
require_once __DIR__ . '/../Models/User.php';
use Models\Message;
use Models\User;

class ContactController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function sendMessage($userData) {
        try {
            // Validar que todos los campos requeridos existan
            if (!isset($userData['user_id']) || !isset($userData['name']) || 
                !isset($userData['email']) || !isset($userData['message'])) {
                return ['success' => false, 'message' => 'Faltan campos requeridos'];
            }

            // Usar el modelo User para validar
            $user = new User();
            $user->setId($userData['user_id']);
            if (!$user->exists($this->conn)) {  // Asumiendo que existe un método exists() en User
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            $message = new Message();
            $message->setUserId($userData['user_id']);
            $message->setName($userData['name']);
            $message->setEmail($userData['email']);
            $message->setNumber($userData['number']);
            $message->setMessage($userData['message']);

            if ($message->exists($this->conn)) {  // Asumiendo que existe un método exists() en Message
                return ['success' => false, 'message' => '¡Mensaje ya enviado!'];
            }

            if ($message->save($this->conn)) {
                return ['success' => true, 'message' => '¡Mensaje enviado exitosamente!'];
            }

            // Añadir log específico si save() falla
            error_log("Error en save(): Falló al guardar el mensaje para usuario ID: " . $userData['user_id']);
            return ['success' => false, 'message' => 'Error al enviar mensaje'];
        } catch (\Exception $e) {
            error_log("Error al enviar mensaje: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al enviar mensaje: ' . $e->getMessage()];
        }
    }
}