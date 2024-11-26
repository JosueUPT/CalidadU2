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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function manejar_error_base_datos_en_obtener_pedidos(): void
    {
        $userId = 1;
        
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Database error'));

        $result = $this->orderController->getOrders($userId);
        $this->assertEmpty($result);
    }

    #[Test]
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
} 