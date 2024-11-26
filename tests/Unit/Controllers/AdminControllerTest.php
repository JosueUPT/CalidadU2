<?php
namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\AdminController;
use PDO;
use PDOStatement;

class AdminControllerTest extends TestCase
{
    private $conn;
    private $adminController;
    private $pdoStatement;

    protected function setUp(): void
    {
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        $this->adminController = new AdminController($this->conn);
    }

    #[Test]
    public function obtener_datos_dashboard(): void
    {
        // Configurar mocks para cada consulta
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 5]);
        $this->pdoStatement->method('fetchAll')->willReturn([
            ['id' => 1, 'total_price' => 100],
            ['id' => 2, 'total_price' => 200]
        ]);

        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->getDashboardData();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_pendings', $result);
        $this->assertArrayHasKey('total_completed', $result);
        $this->assertArrayHasKey('orders_count', $result);
        $this->assertArrayHasKey('products_count', $result);
    }

    #[Test]
    public function agregar_producto_exitoso(): void
    {
        $postData = [
            'name' => 'Nuevo Producto',
            'price' => 99.99
        ];

        $files = [
            'image' => [
                'name' => 'test.jpg',
                'size' => 1000000,
                'tmp_name' => 'temp/test.jpg'
            ]
        ];

        $this->pdoStatement->method('rowCount')->willReturn(0);
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->addProduct($postData, $files);

        $this->assertSame(true, $result['success']);
        $this->assertSame('¡Producto añadido exitosamente!', $result['message']);
    }

    #[Test]
    public function actualizar_estado_pedido(): void
    {
        $orderId = 1;
        $status = 'completado';

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->updateOrderStatus($orderId, $status);

        $this->assertTrue($result);
    }

    #[Test]
    public function obtener_todos_usuarios(): void
    {
        $expectedUsers = [
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'user_type' => 'admin'
            ],
            [
                'id' => 2,
                'name' => 'Normal User',
                'email' => 'user@test.com',
                'user_type' => 'user'
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn($expectedUsers);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $users = $this->adminController->getAllUsers();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);
        $this->assertSame('Admin User', $users[0]->getName());
        $this->assertSame('user@test.com', $users[1]->getEmail());
    }

    #[Test]
    public function eliminar_producto(): void
    {
        $productId = 1;
        
        // Mock para obtener la información de la imagen
        $this->pdoStatement->method('fetch')->willReturn(['image' => 'test.jpg']);
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->deleteProduct($productId);

        $this->assertSame(true, $result['success']);
        $this->assertSame('Producto eliminado', $result['message']);
    }
} 