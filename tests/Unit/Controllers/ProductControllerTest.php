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

    #[Test]
    public function get_latest_products(): void
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

    #[Test]
    public function add_to_cart(): void
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

    #[Test]
    public function get_cart_items(): void
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
} 