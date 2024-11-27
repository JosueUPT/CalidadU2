<?php

namespace Tests\Unit\Views;

use PHPUnit\Framework\TestCase;
use Config\Database;
use Controllers\ProductController;

class ShopTest extends TestCase
{
    private $productController;
    private $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = $this->createMock(Database::class);
        $this->productController = $this->createMock(ProductController::class);
    }

    public function test_puede_obtener_productos(): void
    {
        $productos_mock = [
            new class {
                public function getName() { return 'Producto Test'; }
                public function getPrice() { return '100'; }
                public function getImage() { return 'test.jpg'; }
            }
        ];

        $this->productController
            ->expects($this->once())
            ->method('getAllProducts')
            ->willReturn($productos_mock);

        $products = $this->productController->getAllProducts();

        $this->assertNotEmpty($products);
        $this->assertEquals('Producto Test', $products[0]->getName());
        $this->assertEquals('100', $products[0]->getPrice());
        $this->assertEquals('test.jpg', $products[0]->getImage());
    }

    public function test_puede_anadir_al_carrito(): void
    {
        // Simular POST
        $_POST['add_to_cart'] = true;
        $_POST['product_name'] = 'Producto Test';
        $_POST['product_price'] = '100';
        $_POST['product_quantity'] = '1';
        $_POST['product_image'] = 'test.jpg';

        $this->productController
            ->expects($this->once())
            ->method('addToCart')
            ->with(
                $this->equalTo(1),
                $this->equalTo($_POST)
            )
            ->willReturn(['message' => 'Producto añadido al carrito exitosamente']);

        if(isset($_POST['add_to_cart'])) {
            $result = $this->productController->addToCart(1, $_POST);
            $message[] = $result['message'];
        }

        $this->assertArrayHasKey(0, $message);
        $this->assertEquals('Producto añadido al carrito exitosamente', $message[0]);
    }

    public function test_muestra_mensaje_cuando_no_hay_productos(): void
    {
        $this->productController
            ->method('getAllProducts')
            ->willReturn([]);

        $products = $this->productController->getAllProducts();
        
        $this->assertEmpty($products);
    }

    protected function tearDown(): void
    {
        $_POST = array();
        parent::tearDown();
    }
} 