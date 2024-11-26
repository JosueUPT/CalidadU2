<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\SearchController;
use PDO;
use PDOStatement;

class SearchControllerTest extends TestCase
{
    private $conn;
    private $pdoStatement;
    private $searchController;

    protected function setUp(): void
    {
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        $this->searchController = new SearchController($this->conn);
    }

    #[Test]
    public function search_products_returns_results(): void
    {
        $expectedResults = [
            ['id' => 1, 'name' => 'Producto 1'],
            ['id' => 2, 'name' => 'Producto 2']
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn($expectedResults);
        
        $this->conn->method('prepare')
            ->willReturn($this->pdoStatement);

        $results = $this->searchController->searchProducts('Producto');

        $this->assertEquals($expectedResults, $results);
    }

    #[Test]
    public function search_products_returns_empty_array_when_no_results(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn([]);
        
        $this->conn->method('prepare')
            ->willReturn($this->pdoStatement);

        $results = $this->searchController->searchProducts('NoExiste');

        $this->assertEmpty($results);
    }

    #[Test]
    public function search_products_returns_empty_array_on_exception(): void
    {
        $this->conn->method('prepare')
            ->willThrowException(new \Exception('Error de conexión'));

        $results = $this->searchController->searchProducts('Producto');

        $this->assertEmpty($results);
    }
} 