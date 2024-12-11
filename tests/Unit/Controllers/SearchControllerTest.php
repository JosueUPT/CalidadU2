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

    /** @test */
    public function test_search_products_con_parametro_vacio(): void
    {
        $pdoStatement = $this->createMock(PDOStatement::class);
        $pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['%%'])
            ->willReturn(true);
        $pdoStatement->method('fetchAll')
            ->willReturn([]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')
            ->willReturn($pdoStatement);

        $searchController = new SearchController($pdo);
        $result = $searchController->searchProducts('');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function test_search_products_maneja_error_y_logging(): void
    {
        // Crear un archivo temporal para los logs
        $tempLogFile = tempnam(sys_get_temp_dir(), 'test_error_log');
        $originalErrorLog = ini_get('error_log');
        ini_set('error_log', $tempLogFile);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')
            ->willThrowException(new \Exception('Error de prueba'));

        error_reporting(E_ALL);
        
        set_error_handler(function($errno, $errstr) {
            error_log($errstr);
        });

        $searchController = new SearchController($pdo);
        $result = $searchController->searchProducts('test');

        restore_error_handler();
        
        $this->assertEmpty($result);
        
        // Verificar el log en el archivo temporal
        $errorLog = file_get_contents($tempLogFile);
        $this->assertStringContainsString('Error en búsqueda: Error de prueba', $errorLog);

        // Limpieza
        ini_set('error_log', $originalErrorLog);
        unlink($tempLogFile);
    }
} 