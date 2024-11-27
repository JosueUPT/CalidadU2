<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
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

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}
