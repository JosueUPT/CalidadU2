<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use PHPUnit\Framework\TestCase;

class AdminProductTest extends TestCase
{
    protected $driver;
    protected $baseUrl = 'http://proyecto_codigo_web';

    protected function setUp(): void
    {
        $host = 'http://localhost:4444';
        
        $options = new ChromeOptions();
        $options->addArguments([
            '--start-maximized',
            '--disable-infobars',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1920,1080'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        
        $this->driver = RemoteWebDriver::create($host, $capabilities);
        
        $this->adminLogin();
    }

    private function adminLogin()
    {
        $this->driver->get($this->baseUrl . '/views/auth/login.php');
        $this->driver->findElement(WebDriverBy::name('email'))->sendKeys('admin@hotmail.com');
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('123456');
        $this->driver->findElement(WebDriverBy::name('submit'))->click();
        sleep(2);
    }

    public function testAddAndUpdateProduct()
    {
        try {
            // Navegar a la página de productos
            $this->driver->get($this->baseUrl . '/views/admin/admin_products.php');
            
            // Esperar explícitamente a que la página se cargue
            $wait = new WebDriverWait($this->driver, 10);
            
            // Esperar a que el formulario esté visible
            $form = $wait->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::cssSelector('.add-products form')
                )
            );
            
            // Agregar nuevo producto
            $productName = 'Producto UI Test';
            
            // Llenar solo los campos obligatorios
            $nameInput = $form->findElement(WebDriverBy::cssSelector('input[name="name"]'));
            $nameInput->clear();
            $nameInput->sendKeys($productName);
            
            $priceInput = $form->findElement(WebDriverBy::cssSelector('input[name="price"]'));
            $priceInput->clear();
            $priceInput->sendKeys('15');
            
            // Enviar el formulario
            $form->findElement(WebDriverBy::cssSelector('input[name="add_product"]'))->click();
            
            // Esperar a que el producto aparezca en la lista
            $wait = new WebDriverWait($this->driver, 10);
            $productElement = $wait->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::xpath("//div[contains(@class, 'name') and contains(text(), '$productName')]")
                )
            );
            
            // Verificar que el texto del producto está presente
            $this->assertStringContainsString(
                $productName,
                $productElement->getText(),
                'El producto no se encontró en la lista después de agregarlo'
            );

            // Actualizar el producto
            $newProductName = 'Producto UI Test Actualizado';
            
            // Hacer clic en el botón actualizar del producto
            $updateButton = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class, 'name') and contains(text(), '$productName')]/..//a[contains(@class, 'option-btn')]")
            );
            $updateButton->click();
            
            // Esperar a que el formulario de actualización sea visible
            $wait = new WebDriverWait($this->driver, 10);
            $updateForm = $wait->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::cssSelector('.edit-product-form form')
                )
            );
            
            // Actualizar el nombre del producto
            $updateNameInput = $updateForm->findElement(WebDriverBy::cssSelector('input[name="update_name"]'));
            $updateNameInput->clear();
            $updateNameInput->sendKeys($newProductName);
            
            // Actualizar el precio
            $updatePriceInput = $updateForm->findElement(WebDriverBy::cssSelector('input[name="update_price"]'));
            $updatePriceInput->clear();
            $updatePriceInput->sendKeys('25');
            
            // Enviar el formulario de actualización
            $updateForm->findElement(WebDriverBy::cssSelector('input[name="update_product"]'))->click();
            
            // Esperar a que el producto actualizado aparezca en la lista
            $updatedProductElement = $wait->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::xpath("//div[contains(@class, 'name') and contains(text(), '$newProductName')]")
                )
            );
            
            // Verificar que el producto se actualizó correctamente
            $this->assertStringContainsString(
                $newProductName,
                $updatedProductElement->getText(),
                'El producto no se actualizó correctamente'
            );
            
            // Verificar el nuevo precio
            $updatedPriceElement = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class, 'name') and contains(text(), '$newProductName')]/..//div[contains(@class, 'price')]")
            );
            $this->assertStringContainsString(
                '25',
                $updatedPriceElement->getText(),
                'El precio del producto no se actualizó correctamente'
            );

        } catch (\Exception $e) {
            echo "Error en prueba de productos: " . $e->getMessage() . "\n";
            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";
            echo "HTML de la página: " . $this->driver->getPageSource() . "\n";
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