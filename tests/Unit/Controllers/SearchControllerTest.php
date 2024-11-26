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

    /** @test */
    public function buscar_productos_devuelve_resultados(): void
    {
        $resultadosEsperados = [
            ['id' => 1, 'name' => 'Producto 1'],
            ['id' => 2, 'name' => 'Producto 2']
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn($resultadosEsperados);
        
        $this->conn->method('prepare')
            ->willReturn($this->pdoStatement);

        $resultados = $this->searchController->searchProducts('Producto');

        $this->assertEquals($resultadosEsperados, $resultados);
    }

    /** @test */
    public function buscar_productos_devuelve_array_vacio_cuando_no_hay_resultados(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn([]);
        
        $this->conn->method('prepare')
            ->willReturn($this->pdoStatement);

        $resultados = $this->searchController->searchProducts('NoExiste');

        $this->assertEmpty($resultados);
    }

    /** @test */
    public function buscar_productos_devuelve_array_vacio_cuando_hay_excepcion(): void
    {
        $this->conn->method('prepare')
            ->willThrowException(new \Exception('Error de conexión'));

        $resultados = $this->searchController->searchProducts('Producto');

        $this->assertEmpty($resultados);
    }
} 