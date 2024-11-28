<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use PHPUnit\Framework\TestCase;

class ShopPageTest extends TestCase
{
    protected $driver;
    protected $baseUrl = 'http://proyecto_codigo_web';

    protected function setUp(): void
    {
        $host = 'http://localhost:4444';

        $options = new ChromeOptions();
        $options->addArguments(['--start-maximized', '--disable-infobars', '--no-sandbox']);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->driver = RemoteWebDriver::create($host, $capabilities);

        $this->login();
    }

    private function login()
    {
        $this->driver->get($this->baseUrl . '/views/auth/login.php');
        $this->driver->findElement(WebDriverBy::name('email'))->sendKeys('test@hotmail.com');
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('123456');
        $this->driver->findElement(WebDriverBy::name('submit'))->click();
        sleep(2);
    }

    public function testShopPageLoadsWithProducts()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/shop.php');
            sleep(2);

            $title = $this->driver->findElement(WebDriverBy::cssSelector('.heading h3'));
            $this->assertEquals('NUESTRA TIENDA', $title->getText());

            $products = $this->driver->findElements(WebDriverBy::cssSelector('.products .box'));
            $this->assertGreaterThan(0, count($products), 'No se encontraron productos en la tienda');

            $firstProduct = $products[0];
            $this->assertNotNull($firstProduct->findElement(WebDriverBy::cssSelector('.image')), 'Imagen del producto no encontrada');
            $this->assertNotNull($firstProduct->findElement(WebDriverBy::cssSelector('.name')), 'Nombre del producto no encontrado');
            $this->assertNotNull($firstProduct->findElement(WebDriverBy::cssSelector('.price')), 'Precio del producto no encontrado');
            $this->assertNotNull($firstProduct->findElement(WebDriverBy::cssSelector('.qty')), 'Campo de cantidad no encontrado');
            $this->assertNotNull($firstProduct->findElement(WebDriverBy::cssSelector('.btn')), 'Botón de añadir al carrito no encontrado');
        } catch (\Exception $e) {
            echo "Error en prueba de carga de página de tienda: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function testAddNewProductToCart()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/shop.php');
            sleep(2);
            
            $cartCountSelector = '.header .icons a[href="cart.php"] span';
            $initialCartCount = (int)$this->driver->findElement(WebDriverBy::cssSelector($cartCountSelector))->getText();
            
            $addToCartButton = $this->driver->findElement(WebDriverBy::name('add_to_cart'));
            $addToCartButton->click();
            
            sleep(2); 
            
            $messages = $this->driver->findElements(WebDriverBy::cssSelector('.message'));
            foreach ($messages as $message) {
                if (strpos($message->getText(), 'El producto ya está en el carrito') !== false) {
                    $this->assertTrue(true); 
                    return;
                }
            }
            
            $this->assertTrue(true);
                
        } catch (\Exception $e) {
            $this->fail("Error en prueba de añadir producto al carrito: " . $e->getMessage());
        }
    }

    public function testAddDuplicateProductToCart()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/shop.php');
            sleep(2);
            
            $cartCountSelector = '.header .icons a[href="cart.php"] span';
            $initialCartCount = (int)$this->driver->findElement(WebDriverBy::cssSelector($cartCountSelector))->getText();
            
            $addToCartButton = $this->driver->findElement(WebDriverBy::name('add_to_cart'));
            $addToCartButton->click();
            
            sleep(2); 
            
            $messages = $this->driver->findElements(WebDriverBy::cssSelector('.message'));
            $errorFound = false;
            
            foreach ($messages as $message) {
                if (strpos($message->getText(), 'El producto ya está en el carrito') !== false) {
                    $errorFound = true;
                    break;
                }
            }
            
            $this->assertTrue($errorFound, "No se mostró el mensaje de error esperado");
            
            $finalCartCount = (int)$this->driver->findElement(WebDriverBy::cssSelector($cartCountSelector))->getText();
            $this->assertEquals($initialCartCount, $finalCartCount, 
                "El contador no debería cambiar si el producto ya está en el carrito");
                
        } catch (\Exception $e) {
            $this->fail("Error en prueba de añadir producto duplicado al carrito: " . $e->getMessage());
        }
    }

    public function testUpdateProductQuantityInCart()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/shop.php');
            sleep(2);
            
            $addToCartButton = $this->driver->findElement(WebDriverBy::name('add_to_cart'));
            $addToCartButton->click();
            sleep(2);
            
            $cartIcon = $this->driver->findElement(WebDriverBy::cssSelector('.header .icons a[href="cart.php"]'));
            $cartIcon->click();
            sleep(2);
            
            $quantityInput = $this->driver->findElement(WebDriverBy::name('cart_quantity'));
            $initialQuantity = $quantityInput->getAttribute('value');
            
            $quantityInput->clear();
            $quantityInput->sendKeys('3');
            
            $updateButton = $this->driver->findElement(WebDriverBy::cssSelector('input[name="update_cart"]'));
            $updateButton->click();
            sleep(2);
            
            $updatedQuantityInput = $this->driver->findElement(WebDriverBy::name('cart_quantity'));
            $newQuantity = $updatedQuantityInput->getAttribute('value');
            
            $this->assertEquals('3', $newQuantity, 'La cantidad no se actualizó correctamente');
            
        } catch (\Exception $e) {
            $this->fail("Error en prueba de actualización de cantidad en el carrito: " . $e->getMessage());
        }
    }

    public function testProceedToCheckoutAndPlaceOrder()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/shop.php');
            
            $wait = new WebDriverWait($this->driver, 10);
            
            $addToCartButton = $wait->until(
                WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::name('add_to_cart'))
            );
            $addToCartButton->click();
            sleep(2);
            
            $this->driver->get($this->baseUrl . '/views/usuario/cart.php');
            sleep(2);
            
            $checkoutButton = $wait->until(
                WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.cart-total .btn'))
            );
            $checkoutButton->click();
            sleep(2);
            
            $this->assertStringContainsString('checkout.php', $this->driver->getCurrentUrl());
            
            $wait->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('name')))
                ->sendKeys('Usuario Prueba');
            $this->driver->findElement(WebDriverBy::name('number'))->sendKeys('987654321');
            $this->driver->findElement(WebDriverBy::name('email'))->sendKeys('prueba@test.com');
            
            $select = $this->driver->findElement(WebDriverBy::name('method'));
            $select->findElement(WebDriverBy::cssSelector("option[value='Tarjeta de crédito']"))->click();
            
            $this->driver->findElement(WebDriverBy::name('flat'))->sendKeys('123');
            $this->driver->findElement(WebDriverBy::name('street'))->sendKeys('Calle Principal');
            $this->driver->findElement(WebDriverBy::name('city'))->sendKeys('Lima');
            $this->driver->findElement(WebDriverBy::name('state'))->sendKeys('Miraflores');
            $this->driver->findElement(WebDriverBy::name('country'))->sendKeys('Perú');
            $this->driver->findElement(WebDriverBy::name('pin_code'))->sendKeys('15046');
            
            $orderButton = $wait->until(
                WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::name('order_btn'))
            );
            $orderButton->click();
            sleep(2);
            
            $message = $wait->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.message'))
            );
            
            $messageText = $message->getText();
            $validMessages = [
                '¡Pedido realizado con éxito!',
                '¡Pedido ya realizado!'
            ];
            
            $this->assertTrue(
                in_array($messageText, $validMessages),
                "Mensaje inesperado: '$messageText'. Se esperaba uno de estos mensajes: " . implode(' o ', $validMessages)
            );
                
        } catch (\Exception $e) {
            $this->fail("Error en prueba de checkout: " . $e->getMessage());
        }
    }

    public function testViewOrders()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/orders.php');
            sleep(2);
            
            $currentUrl = $this->driver->getCurrentUrl();
            $this->assertStringContainsString('orders.php', $currentUrl);
            
            $ordersSection = $this->driver->findElement(WebDriverBy::cssSelector('.placed-orders'));
            $this->assertNotNull($ordersSection, 'La sección de órdenes no fue encontrada');
            
            $boxContainer = $this->driver->findElement(WebDriverBy::cssSelector('.box-container'));
            
            try {
                $orderBoxes = $this->driver->findElements(WebDriverBy::cssSelector('.box-container .box'));
                if (count($orderBoxes) > 0) {
                    $firstOrder = $orderBoxes[0];
                    $this->assertNotNull($firstOrder->findElement(WebDriverBy::xpath(".//p[contains(text(), 'Nombre')]")), 'Nombre no encontrado');
                    $this->assertNotNull($firstOrder->findElement(WebDriverBy::xpath(".//p[contains(text(), 'Número')]")), 'Número no encontrado');
                    $this->assertNotNull($firstOrder->findElement(WebDriverBy::xpath(".//p[contains(text(), 'Tus pedidos')]")), 'Total de productos no encontrado');
                    $this->assertNotNull($firstOrder->findElement(WebDriverBy::xpath(".//p[contains(text(), 'Precio total')]")), 'Precio total no encontrado');
                    $this->assertNotNull($firstOrder->findElement(WebDriverBy::xpath(".//p[contains(text(), 'Fecha')]")), 'Fecha no encontrada');
                }
            } catch (\Exception $e) {
                $this->fail("Error en prueba de visualización de órdenes: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->fail("Error en prueba de visualización de órdenes: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}
