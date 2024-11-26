<?php
namespace Models;

class Order {
    private $id;
    private $userId;
    private $name;
    private $number;
    private $email;
    private $method;
    private $address;
    private $totalProducts;
    private $totalPrice;
    private $placedOn;
    private $paymentStatus;

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getName() { return $this->name; }
    public function getNumber() { return $this->number; }
    public function getEmail() { return $this->email; }
    public function getMethod() { return $this->method; }
    public function getAddress() { return $this->address; }
    public function getTotalProducts() { return $this->totalProducts; }
    public function getTotalPrice() { return $this->totalPrice; }
    public function getPlacedOn() { return $this->placedOn; }
    public function getPaymentStatus() { return $this->paymentStatus; }

    // Setters
    public function setUserId($userId) { $this->userId = $userId; }
    public function setName($name) { $this->name = $name; }
    public function setNumber($number) { $this->number = $number; }
    public function setEmail($email) { $this->email = $email; }
    public function setMethod($method) { $this->method = $method; }
    public function setAddress($address) { $this->address = $address; }
    public function setTotalProducts($totalProducts) { $this->totalProducts = $totalProducts; }
    public function setTotalPrice($totalPrice) { $this->totalPrice = $totalPrice; }
    public function setPaymentStatus($status) { $this->paymentStatus = $status; }
    public function setPlacedOn($placedOn) { $this->placedOn = $placedOn; }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
} 