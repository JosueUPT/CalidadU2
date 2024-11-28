<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use PHPUnit\Framework\TestCase;

class AdminOrderTest extends TestCase
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
        sleep(1);

        $emailInput = $this->driver->findElement(WebDriverBy::name('email'));
        $emailInput->clear();
        $emailInput->sendKeys('admin@hotmail.com');
        sleep(0.5);

        $passwordInput = $this->driver->findElement(WebDriverBy::name('password'));
        $passwordInput->clear();
        $passwordInput->sendKeys('123456');
        sleep(0.5);

        $this->driver->findElement(WebDriverBy::name('submit'))->click();
        sleep(1);
    }

    public function testUpdateOrderStatus()
    {
        try {
            $this->updateOrderStatus('pendiente', 'completado');
            sleep(1);

            $this->updateOrderStatus('completado', 'pendiente');
            
        } catch (\Exception $e) {
            echo "Error en prueba de órdenes: " . $e->getMessage() . "\n";
            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";
            echo "HTML de la página: " . $this->driver->getPageSource() . "\n";
            throw $e;
        }
    }

    private function updateOrderStatus($currentStatus, $newStatus)
    {
        $this->driver->get($this->baseUrl . '/views/admin/admin_orders.php');
        sleep(1);

        $wait = new WebDriverWait($this->driver, 10);
        
        $orderForm = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::xpath("//select[@name='update_payment']/option[@selected and contains(text(), '$currentStatus')]/../..")
            )
        );
        sleep(0.5);
        
        $select = $orderForm->findElement(WebDriverBy::name('update_payment'));
        $select->findElement(WebDriverBy::xpath("//option[@value='$newStatus']"))->click();
        sleep(0.5);
        
        $orderForm->findElement(WebDriverBy::cssSelector('input[name="update_order"]'))->click();
        sleep(1);
        
        $this->driver->get($this->baseUrl . '/views/admin/admin_page.php');
        sleep(1);
        
        $this->assertStringContainsString(
            'admin_page.php',
            $this->driver->getCurrentURL(),
            'No se redirigió correctamente a la página de administración'
        );
        
        sleep(0.5);
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            sleep(1);
            $this->driver->quit();
        }
    }
} 