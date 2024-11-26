<?php
namespace Models;

class Message {
    private $id;
    private $userId;
    private $name;
    private $email;
    private $number;
    private $message;

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getNumber() { return $this->number; }
    public function getMessage() { return $this->message; }

    // Setters
    public function setUserId($userId) { $this->userId = $userId; }
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setNumber($number) { $this->number = $number; }
    public function setMessage($message) { $this->message = $message; }

    public function exists($conn) {
        $sql = "SELECT id FROM message WHERE user_id = ? AND message = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$this->userId, $this->message]);
        return $stmt->rowCount() > 0;
    }

    public function save($conn) {
        $sql = "INSERT INTO message (user_id, name, email, number, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $this->userId,
            $this->name,
            $this->email,
            $this->number,
            $this->message
        ]);
    }

    public function setId($id) { $this->id = $id; }
} 