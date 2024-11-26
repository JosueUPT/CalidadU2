<?php
namespace Models;

class Cart {
    private $id;
    private $userId;
    private $name;
    private $price;
    private $quantity;
    private $image;

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getName() { return $this->name; }
    public function getPrice() { return $this->price; }
    public function getQuantity() { return $this->quantity; }
    public function getImage() { return $this->image; }

    // Setters
    public function setUserId($userId) { $this->userId = $userId; }
    public function setName($name) { $this->name = $name; }
    public function setPrice($price) { $this->price = $price; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    public function setImage($image) { $this->image = $image; }
} 