<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\AdminController;
use PDO;
use PDOStatement;
use Models\User;

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

    /** @test */
    public function testGetDashboardData(): void
    {
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

    /** @test */
    public function testAddProductSuccessfully(): void
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

    /** @test */
    public function testUpdateOrderStatus(): void
    {
        $orderId = 1;
        $status = 'completado';

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->updateOrderStatus($orderId, $status);

        $this->assertTrue($result);
    }

    /** @test */
    public function testGetAllUsers(): void
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
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertInstanceOf(User::class, $users[1]);
        $this->assertEquals('Admin User', $users[0]->getName());
        $this->assertEquals('user@test.com', $users[1]->getEmail());
    }

    /** @test */
    public function testDeleteProduct(): void
    {
        $productId = 1;

        $this->pdoStatement->method('fetch')->willReturn(['image' => 'test.jpg']);
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->deleteProduct($productId);

        $this->assertSame(true, $result['success']);
        $this->assertSame('Producto eliminado', $result['message']);
    }

    /** @test */
    public function testDeleteOrder(): void
    {
        $orderId = 1;

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->deleteOrder($orderId);

        $this->assertTrue($result);
    }

    /** @test */
    public function testDeleteUser(): void
    {
        $userId = 1;

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->deleteUser($userId);

        $this->assertTrue($result);
    }

    /** @test */
    public function testGetAllMessages(): void
    {
        $expectedMessages = [
            [
                'id' => 1,
                'user_id' => 1,
                'message' => 'Test message',
                'name' => 'User Test',
                'email' => 'test@test.com',
                'number' => '123456789'
            ]
        ];

        $this->pdoStatement->method('fetchAll')->willReturn($expectedMessages);
        $this->conn->method('query')->willReturn($this->pdoStatement);

        $messages = $this->adminController->getAllMessages();

        $this->assertIsArray($messages);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test message', $messages[0]->getMessage());
        $this->assertEquals('test@test.com', $messages[0]->getEmail());
    }

    /** @test */
    public function testDeleteMessage(): void
    {
        $messageId = 1;

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->deleteMessage($messageId);

        $this->assertTrue($result);
    }

    /** @test */
    public function testGetAllProducts(): void
    {
        $expectedProducts = [
            [
                'id' => 1,
                'name' => 'Product 1',
                'price' => 99.99,
                'image' => 'product1.jpg'
            ]
        ];

        $this->pdoStatement->method('fetchAll')->willReturn($expectedProducts);
        $this->conn->method('query')->willReturn($this->pdoStatement);

        $products = $this->adminController->getAllProducts();

        $this->assertIsArray($products);
        $this->assertCount(1, $products);
        $this->assertEquals('Product 1', $products[0]->getName());
        $this->assertEquals(99.99, $products[0]->getPrice());
    }

    /** @test */
    public function testUpdateProduct(): void
    {
        $postData = [
            'update_p_id' => 1,
            'update_name' => 'Updated Product',
            'update_price' => 149.99,
            'update_old_image' => 'old.jpg'
        ];

        $files = [
            'update_image' => [
                'name' => 'new.jpg',
                'size' => 1000000,
                'tmp_name' => 'temp/new.jpg'
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->updateProduct($postData, $files);

        $this->assertTrue($result['success']);
        $this->assertEquals('Producto actualizado exitosamente', $result['message']);
    }

    /** @test */
    public function testGetAllOrders(): void
    {
        $expectedOrders = [
            [
                'id' => 1,
                'user_id' => 1,
                'placed_on' => '2024-03-20',
                'name' => 'Customer 1',
                'number' => '123456789',
                'email' => 'customer@test.com',
                'address' => 'Test Address',
                'total_products' => 2,
                'total_price' => 199.98,
                'method' => 'credit card',
                'payment_status' => 'completed'
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn($expectedOrders);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $orders = $this->adminController->getAllOrders();

        $this->assertIsArray($orders);
        $this->assertCount(1, $orders);
        $this->assertEquals('Customer 1', $orders[0]->getName());
        $this->assertEquals(199.98, $orders[0]->getTotalPrice());
    }

    /** @test */
    public function testHandleDatabaseError(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error al procesar la solicitud');

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('handleDatabaseError');
        $method->setAccessible(true);

        $method->invoke($this->adminController, new \Exception('Test error'));
    }


    /** @test */
    public function testGetTotalPendings(): void
    {
        $expectedOrders = [
            [
                'id' => 1,
                'total_price' => 100.00
            ],
            [
                'id' => 2,
                'total_price' => 150.00
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn($expectedOrders);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getTotalPendings');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(250.00, $result);
    }

    /** @test */
    public function testGetTotalCompleted(): void
    {
        $expectedOrders = [
            [
                'id' => 1,
                'total_price' => 200.00
            ],
            [
                'id' => 2,
                'total_price' => 300.00
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetchAll')->willReturn($expectedOrders);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getTotalCompleted');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(500.00, $result);
    }

    /** @test */
    public function testGetOrdersCount(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 10]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getOrdersCount');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(10, $result);
    }

    /** @test */
    public function testGetProductsCount(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 15]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getProductsCount');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(15, $result);
    }

    /** @test */
    public function testGetUsersCount(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 20]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getUsersCount');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(20, $result);
    }

    /** @test */
    public function testGetAdminsCount(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 5]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getAdminsCount');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(5, $result);
    }

    /** @test */
    public function testGetTotalAccounts(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 25]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getTotalAccounts');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(25, $result);
    }

    /** @test */
    public function testGetMessagesCount(): void
    {
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['count' => 30]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getMessagesCount');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController);

        $this->assertEquals(30, $result);
    }

    /** @test */
    public function testUpdateProductWithoutImage(): void
    {
        $postData = [
            'update_p_id' => 1,
            'update_name' => 'Updated Product',
            'update_price' => 149.99
        ];

        $files = [
            'update_image' => [
                'name' => ''
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->updateProduct($postData, $files);

        $this->assertTrue($result['success']);
        $this->assertEquals('Producto actualizado exitosamente', $result['message']);
    }

    /** @test */
    public function testUpdateProductWithLargeImage(): void
    {
        $postData = [
            'update_p_id' => 1,
            'update_name' => 'Updated Product',
            'update_price' => 149.99
        ];

        $files = [
            'update_image' => [
                'name' => 'large.jpg',
                'size' => 3000000 
            ]
        ];

        $result = $this->adminController->updateProduct($postData, $files);

        $this->assertFalse($result['success']);
        $this->assertEquals('El tamaño de la imagen es demasiado grande', $result['message']);
    }

    /** @test */
    public function testGetSecureImagePath(): void
    {
        $imageName = 'test.jpg';
        
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['image' => $imageName]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getSecureImagePath');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController, $imageName);

        $this->assertNotFalse($result);
        $this->assertStringContainsString($imageName, $result);
    }

    /** @test */
    public function testGetSecureImagePathInvalidExtension(): void
    {
        $imageName = 'test.exe';
        
        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('getSecureImagePath');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController, $imageName);

        $this->assertFalse($result);
    }

    /** @test */
    public function testHandleImageDelete(): void
    {
        $imageName = 'test.jpg';
        
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(['image' => $imageName]);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('handleImageDelete');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController, $imageName);

        $this->assertIsBool($result);
    }

    /** @test */
    public function testHandleImageDeleteInvalidName(): void
    {
        $reflection = new \ReflectionClass($this->adminController);
        $method = $reflection->getMethod('handleImageDelete');
        $method->setAccessible(true);

        $result = $method->invoke($this->adminController, '');

        $this->assertFalse($result);
    }

    /** @test */
    public function testAddProductWithExistingName(): void
    {
        $postData = [
            'name' => 'Producto Existente',
            'price' => 99.99
        ];

        $files = [
            'image' => [
                'name' => 'test.jpg',
                'size' => 1000000,
                'tmp_name' => 'temp/test.jpg'
            ]
        ];

        $this->pdoStatement->method('rowCount')->willReturn(1);
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->addProduct($postData, $files);

        $this->assertFalse($result['success']);
        $this->assertEquals('El producto ya existe', $result['message']);
    }

    /** @test */
    public function testUpdateProductDatabaseError(): void
    {
        $postData = [
            'update_p_id' => 1,
            'update_name' => 'Updated Product',
            'update_price' => 149.99
        ];

        $files = [
            'update_image' => [
                'name' => ''
            ]
        ];

        $this->pdoStatement->method('execute')->willReturn(false);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->updateProduct($postData, $files);

        $this->assertFalse($result['success']);
        $this->assertEquals('Error al actualizar el producto', $result['message']);
    }

    /** @test */
    public function testUpdateOrderStatusDatabaseError(): void
    {
        $orderId = 1;
        $status = 'completado';

        $this->pdoStatement->method('execute')->willReturn(false);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->adminController->updateOrderStatus($orderId, $status);

        $this->assertFalse($result);
    }
}
