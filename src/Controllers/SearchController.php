<?php
namespace Controllers;

class SearchController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function searchProducts($searchTerm) {
        try {
            $searchTerm = "%{$searchTerm}%";
            $stmt = $this->conn->prepare("SELECT * FROM products WHERE name LIKE ?");
            $stmt->execute([$searchTerm]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error en búsqueda: " . $e->getMessage());
            return [];
        }
    }
} 