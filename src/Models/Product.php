<?php
namespace Models;

class Product {
    private $id;
    private $name;
    private $price;
    private $image;

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getPrice() { return $this->price; }
    public function getImage() { return $this->image; }

    // Setters
    public function setName($name) { $this->name = $name; }
    public function setPrice($price) { $this->price = $price; }
    public function setImage($image) { $this->image = $image; }

    public function setId($id) {
        $this->id = $id;
    }
}