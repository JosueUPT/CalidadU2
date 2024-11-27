<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class SearchPageTest extends TestCase
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
        
        // Login antes de las pruebas
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

    public function testAddToCartFromSearchWithResults()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/search_page.php');
            sleep(2);

            $searchInput = $this->driver->findElement(WebDriverBy::name('search'));
            $searchInput->sendKeys('test'); 
            
            $submitButton = $this->driver->findElement(
                WebDriverBy::cssSelector('.search-form input[type="submit"]')
            );
            $submitButton->click();
            sleep(2);

            $products = $this->driver->findElements(WebDriverBy::className('box'));
            $this->assertGreaterThan(0, count($products), 'No se encontraron productos');

            $addToCartButton = $this->driver->findElement(
                WebDriverBy::cssSelector('.box input[type="submit"][value="Agregar al carrito"]')
            );
            
            sleep(2);
            $addToCartButton->click();
            
            $cartCount = $this->driver->findElement(
                WebDriverBy::cssSelector('a[href="cart.php"] span')
            );
            $this->assertNotEquals('(0)', $cartCount->getText(), 'El contador del carrito no se actualizó');
        } catch (\Exception $e) {
            echo "Error en prueba de agregar al carrito con resultados: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function testSearchWithNoResults()
    {
        try {
            $this->driver->get($this->baseUrl . '/views/usuario/search_page.php');
            sleep(2);

            $searchInput = $this->driver->findElement(WebDriverBy::name('search'));
            $searchInput->sendKeys('xxxxxxxxxxx123456789');
            
            $submitButton = $this->driver->findElement(
                WebDriverBy::cssSelector('.search-form input[type="submit"]')
            );
            $submitButton->click();
            
            sleep(4);
            
            $currentUrl = $this->driver->getCurrentUrl();
            echo "\nURL actual: " . $currentUrl . "\n";
            
            $products = $this->driver->findElements(
                WebDriverBy::cssSelector('.search-results .box')
            );
            echo "\nNúmero de productos encontrados: " . count($products) . "\n";
            
            foreach ($products as $index => $product) {
                echo "Producto " . ($index + 1) . ": " . $product->getText() . "\n";
            }

            $this->assertEquals(0, count($products), 'Se encontraron productos cuando no debería haberlos');

            $emptyMessage = $this->driver->findElement(WebDriverBy::className('empty'));
            $this->assertEquals(
                '¡No se han encontrado resultados!',
                $emptyMessage->getText(),
                'Mensaje de búsqueda vacía incorrecto'
            );
        } catch (\Exception $e) {
            echo "Error en prueba de búsqueda sin resultados: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
} 