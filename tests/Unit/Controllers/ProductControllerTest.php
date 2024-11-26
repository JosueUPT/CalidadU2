<?php
namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\ProductController;
use Models\Product;
use PDO;
use PDOStatement;

class ProductControllerTest extends TestCase
{
    private $conn;
    private $productController;
    private $pdoStatement;

    protected function setUp(): void
    {
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        $this->productController = new ProductController($this->conn);
    }

    /** @test */
    public function obtener_productos_recientes(): void
    {
        $expectedProducts = [
            [
                'id' => 1,
                'name' => 'Producto Test',
                'price' => 99.99,
                'image' => 'test.jpg'
            ]
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute');

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedProducts);

        $result = $this->productController->getLatestProducts(1);

        $this->assertIsArray($result);
        $this->assertInstanceOf(Product::class, $result[0]);
        $this->assertSame('Producto Test', $result[0]->getName());
    }

    /** @test */
    public function agregar_al_carrito(): void
    {
        $userId = 1;
        $productData = [
            'product_name' => 'Producto Test',
            'product_price' => 99.99,
            'product_quantity' => 1,
            'product_image' => 'test.jpg'
        ];

        $this->pdoStatement->method('rowCount')->willReturn(0);
        
        $this->conn->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        $result = $this->productController->addToCart($userId, $productData);

        $this->assertSame(true, $result['success']);
        $this->assertSame('Producto añadido al carrito', $result['message']);
    }

    /** @test */
    public function obtener_items_carrito(): void
    {
        $userId = 1;
        $expectedItems = [
            [
                'id' => 1,
                'name' => 'Producto Test',
                'price' => 99.99,
                'quantity' => 1,
                'image' => 'test.jpg'
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
            ->willReturn($expectedItems);

        $result = $this->productController->getCartItems($userId);

        $this->assertSame($expectedItems, $result);
    }

    /** @test */
    public function actualizar_cantidad_carrito_exitoso(): void
    {
        $cartId = 1;
        $quantity = 2;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$quantity, $cartId])
            ->willReturn(true);

        $result = $this->productController->updateCartQuantity($cartId, $quantity);

        $this->assertTrue($result['success']);
        $this->assertEquals('¡Cantidad actualizada!', $result['message']);
    }

    /** @test */
    public function eliminar_item_carrito_exitoso(): void
    {
        $cartId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$cartId])
            ->willReturn(true);

        $result = $this->productController->deleteCartItem($cartId);

        $this->assertTrue($result);
    }

    /** @test */
    public function eliminar_todos_items_carrito_exitoso(): void
    {
        $userId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$userId])
            ->willReturn(true);

        $result = $this->productController->deleteAllCartItems($userId);

        $this->assertTrue($result);
    }

    /** @test */
    public function obtener_todos_productos(): void
    {
        $expectedProducts = [
            [
                'id' => 1,
                'name' => 'Producto 1',
                'price' => 99.99,
                'image' => 'test1.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Producto 2',
                'price' => 149.99,
                'image' => 'test2.jpg'
            ]
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute');

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedProducts);

        $result = $this->productController->getAllProducts();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Product::class, $result[0]);
        $this->assertEquals('Producto 1', $result[0]->getName());
        $this->assertEquals('Producto 2', $result[1]->getName());
    }

    /** @test */
    public function agregar_producto_duplicado_al_carrito(): void
    {
        $userId = 1;
        $productData = [
            'product_name' => 'Producto Test',
            'product_price' => 99.99,
            'product_quantity' => 1,
            'product_image' => 'test.jpg'
        ];

        $this->pdoStatement->method('rowCount')->willReturn(1);
        
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $result = $this->productController->addToCart($userId, $productData);

        $this->assertFalse($result['success']);
        $this->assertEquals('El producto ya está en el carrito', $result['message']);
    }

    /** @test */
    public function manejo_error_al_actualizar_cantidad(): void
    {
        $cartId = 1;
        $quantity = 2;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->productController->updateCartQuantity($cartId, $quantity);

        $this->assertFalse($result['success']);
        $this->assertEquals('Error al actualizar cantidad', $result['message']);
    }

    /** @test */
    public function manejo_error_al_obtener_productos_recientes(): void
    {
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de conexión'));

        $result = $this->productController->getLatestProducts();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function manejo_error_al_obtener_todos_productos(): void
    {
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->productController->getAllProducts();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function manejo_error_al_obtener_items_carrito(): void
    {
        $userId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de conexión'));

        $result = $this->productController->getCartItems($userId);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function manejo_error_al_eliminar_item_carrito(): void
    {
        $cartId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->productController->deleteCartItem($cartId);

        $this->assertFalse($result);
    }

    /** @test */
    public function manejo_error_al_eliminar_todos_items_carrito(): void
    {
        $userId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->productController->deleteAllCartItems($userId);

        $this->assertFalse($result);
    }

    /** @test */
    public function manejo_error_al_agregar_al_carrito(): void
    {
        $userId = 1;
        $productData = [
            'product_name' => 'Producto Test',
            'product_price' => 99.99,
            'product_quantity' => 1,
            'product_image' => 'test.jpg'
        ];

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willThrowException(new \Exception('Error de base de datos'));

        $result = $this->productController->addToCart($userId, $productData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Error al añadir al carrito', $result['message']);
    }

    /** @test */
    public function verificar_carrito_vacio(): void
    {
        $userId = 1;

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$userId]);

        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $result = $this->productController->getCartItems($userId);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
} 