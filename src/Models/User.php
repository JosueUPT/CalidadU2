<?php
namespace Models;

class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $userType;

    public function __construct() {
        $this->userType = 'user';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getUserType() { return $this->userType; }

    // Setters
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { 
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    public function setUserType($userType) { $this->userType = $userType; }
    public function setId($id) { 
        $this->id = $id; 
        return $this;
    }

    public function exists($conn) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }
}