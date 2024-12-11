<?php
namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\OrderController;
use PDO;
use PDOStatement;

class OrderControllerTest extends TestCase
{
    private $conn;
    private $orderController;
    private $pdoStatement;

    protected function setUp(): void
    {
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        $this->orderController = new OrderController($this->conn);
    }

    /** @test */
    public function crear_pedido(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Juan Pérez',
            'number' => '123456789',
            'email' => 'juan@example.com',
            'method' => 'credit card',
            'flat' => '123',
            'street' => 'Calle Principal',
            'city' => 'Lima',
            'country' => 'Perú',
            'pin_code' => '12345'
        ];

        $cartItems = [
            [
                'name' => 'Producto 1',
                'quantity' => 2,
                'price' => 100
            ]
        ];

        $this->conn->expects($this->exactly(4))
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->exactly(4))
            ->method('execute')
            ->willReturn(true);

        $this->pdoStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($cartItems);

        $result = $this->orderController->createOrder($userData, $userId);

        $this->assertSame(true, $result['success']);
        $this->assertSame('¡Pedido realizado con éxito!', $result['message']);
    }

    /** @test */
    public function obtener_pedidos_usuario(): void
    {
        $userId = 1;
        $expectedOrders = [
            [
                'user_id' => 1,
                'name' => 'Juan Pérez',
                'number' => '123456789',
                'email' => 'juan@example.com',
                'method' => 'credit card',
                'address' => 'Dirección de prueba',
                'total_products' => 'Producto 1 (2)',
                'total_price' => 200,
                'payment_status' => 'pending',
                'placed_on' => '20-Mar-2024'
            ]
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$userId]);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedOrders);

        $result = $this->orderController->getUserOrders($userId);

        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
        $this->assertSame($userId, $result[0]->getUserId());
    }

    /** @test */
    public function actualizar_estado_pago(): void
    {
        $orderId = 1;
        $status = 'completed';

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$status, $orderId])
            ->willReturn(true);

        $result = $this->orderController->updatePaymentStatus($orderId, $status);
        $this->assertTrue($result);
    }

    /** @test */
    public function eliminar_pedido(): void
    {
        $orderId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$orderId])
            ->willReturn(true);

        $result = $this->orderController->deleteOrder($orderId);
        $this->assertTrue($result);
    }

    /** @test */
    public function obtener_todos_pedidos(): void
    {
        $expectedOrders = [
            [
                'user_id' => 1,
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'method' => 'credit card',
                'address' => 'Dirección de prueba',
                'total_products' => 'Producto 1 (2)',
                'total_price' => 200
            ]
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute');

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedOrders);

        $result = $this->orderController->getAllOrders();

        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
        $this->assertSame($expectedOrders[0]['user_id'], $result[0]->getUserId());
    }

    /** @test */
    public function obtener_todos_pedidos_cuando_no_hay_datos(): void
    {
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute');

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $result = $this->orderController->getAllOrders();

        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    /** @test */
    public function manejar_error_base_datos_en_obtener_pedidos(): void
    {
        $userId = 1;
        
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Database error'));

        $result = $this->orderController->getOrders($userId);
        $this->assertEmpty($result);
    }

    public function manejar_error_base_datos_en_actualizar_estado(): void
    {
        $orderId = 1;
        $status = 'completed';

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Database error'));

        $result = $this->orderController->updatePaymentStatus($orderId, $status);
        $this->assertFalse($result);
    }

    /** @test */
    public function obtener_todos_productos(): void
    {
        $expectedProducts = [
            [
                'name' => 'Producto 1',
                'price' => 100,
                'image' => 'imagen1.jpg'
            ],
            [
                'name' => 'Producto 2',
                'price' => 200,
                'image' => 'imagen2.jpg'
            ]
        ];

        $this->conn->expects($this->once())
            ->method('query')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedProducts);

        $result = $this->orderController->getAllProducts();

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Producto 1', $result[0]->getName());
    }

    /** @test */
    public function obtener_todos_usuarios(): void
    {
        $expectedUsers = [
            [
                'name' => 'Usuario 1',
                'email' => 'usuario1@test.com',
                'user_type' => 'user'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'user_type' => 'admin'
            ]
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute');

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedUsers);

        $result = $this->orderController->getAllUsers();

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Usuario 1', $result[0]->getName());
    }

    /** @test */
    public function crear_pedido_con_carrito_vacio(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Juan Pérez',
            'number' => '123456789',
            'email' => 'juan@test.com',
            'method' => 'credit card',
            'flat' => '123',
            'street' => 'Calle Principal',
            'city' => 'Lima',
            'country' => 'Perú',
            'pin_code' => '12345'
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $result = $this->orderController->createOrder($userData, $userId);

        $this->assertFalse($result['success']);
        $this->assertEquals('El carrito está vacío', $result['message']);
    }

    /** @test */
    public function crear_pedido_duplicado(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Juan Pérez',
            'number' => '123456789',
            'email' => 'juan@test.com',
            'method' => 'credit card',
            'flat' => '123',
            'street' => 'Calle Principal',
            'city' => 'Lima',
            'country' => 'Perú',
            'pin_code' => '12345'
        ];

        $cartItems = [
            [
                'name' => 'Producto 1',
                'quantity' => 2,
                'price' => 100
            ]
        ];

        $this->conn->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($cartItems);

        $this->pdoStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->orderController->createOrder($userData, $userId);

        $this->assertFalse($result['success']);
        $this->assertEquals('¡Pedido ya realizado!', $result['message']);
    }

    /** @test */
    public function manejar_error_al_crear_pedido(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Juan Pérez',
            'number' => '123456789',
            'email' => 'juan@test.com',
            'method' => 'credit card',
            'flat' => '123',
            'street' => 'Calle Principal',
            'city' => 'Lima',
            'country' => 'Perú',
            'pin_code' => '12345'
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->orderController->createOrder($userData, $userId);

        $this->assertFalse($result['success']);
        $this->assertEquals('Error al procesar el pedido', $result['message']);
    }

    /** @test */
    public function manejar_error_al_obtener_todos_productos(): void
    {
        $this->conn->expects($this->once())
            ->method('query')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->orderController->getAllProducts();

        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    /** @test */
    public function manejar_error_al_obtener_todos_usuarios(): void
    {
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->orderController->getAllUsers();

        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    /** @test */
    public function crear_pedido_con_carrito_items_vacio(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Juan Pérez',
            'number' => '123456789',
            'email' => 'juan@test.com',
            'method' => 'credit card',
            'flat' => '123',
            'street' => 'Calle Principal',
            'city' => 'Lima',
            'country' => 'Perú',
            'pin_code' => '12345'
        ];

        // Simular array vacío de items
        $cartItems = [];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($cartItems);

        $result = $this->orderController->createOrder($userData, $userId);

        $this->assertFalse($result['success']);
        $this->assertEquals('El carrito está vacío', $result['message']);
    }

    /** @test */
    public function crear_pedido_con_foreach_array_vacio(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Juan Pérez',
            'number' => '123456789',
            'email' => 'juan@test.com',
            'method' => 'credit card',
            'flat' => '123',
            'street' => 'Calle Principal',
            'city' => 'Lima',
            'country' => 'Perú',
            'pin_code' => '12345'
        ];

        // Simular un array vacío para el foreach
        $cartItems = array();

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($cartItems);

        $result = $this->orderController->createOrder($userData, $userId);

        $this->assertFalse($result['success']);
        $this->assertEquals('El carrito está vacío', $result['message']);
    }
} 