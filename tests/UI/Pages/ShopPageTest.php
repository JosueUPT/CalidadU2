<?php

namespace Tests\UI\Pages;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class ShopPageTest extends TestCase
{
    private $driver;
    
    protected function setUp(): void
    {
        $host = 'http://localhost:4444/wd/hub';
        $options = new ChromeOptions();
        $options->addArguments([
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--headless'
        ]);
        
        try {
            $this->driver = RemoteWebDriver::create($host, $options->toCapabilities());
            $this->driver->manage()->timeouts()->implicitlyWait(10);
        } catch (\Exception $e) {
            $this->fail('Error conectando a Selenium: ' . $e->getMessage());
        }
    }

    public function testVerProductosEnTienda()
    {
        $this->driver->get('http://localhost:8080/views/usuario/shop.php');
        
        $title = $this->driver->findElement(WebDriverBy::cssSelector('.title'));
        $this->assertEquals('Últimos productos', $title->getText());
        
        $productContainer = $this->driver->findElement(WebDriverBy::cssSelector('.box-container'));
        $this->assertTrue($productContainer->isDisplayed());
        
        $products = $this->driver->findElements(WebDriverBy::cssSelector('.box'));
        foreach ($products as $product) {
            $this->assertTrue($product->findElement(WebDriverBy::cssSelector('.name'))->isDisplayed());
            $this->assertTrue($product->findElement(WebDriverBy::cssSelector('.price'))->isDisplayed());
            $this->assertTrue($product->findElement(WebDriverBy::cssSelector('.btn'))->isDisplayed());
        }
    }

    public function testAgregarProductoAlCarrito()
    {
        $this->driver->get('http://localhost:8080/views/usuario/shop.php');
        
        $firstProduct = $this->driver->findElement(WebDriverBy::cssSelector('.box'));
        $addToCartButton = $firstProduct->findElement(WebDriverBy::cssSelector('.btn'));
        $addToCartButton->click();
        
        $message = $this->driver->findElement(WebDriverBy::cssSelector('.message'));
        $this->assertStringContainsString('agregado al carrito', $message->getText());
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
} 