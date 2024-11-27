<?php

namespace Tests\UI\Pages;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class LoginPageTest extends TestCase
{
    private $driver;
    
    protected function setUp(): void
    {
        $host = 'http://localhost:4444';
        $options = new ChromeOptions();
        $options->addArguments([
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--headless',
            '--disable-gpu',
            '--ignore-certificate-errors'
        ]);
        
        try {
            echo "\nIntentando conectar a Selenium en: " . $host . "\n";
            
            $this->driver = RemoteWebDriver::create(
                $host . '/wd/hub',
                $options->toCapabilities()
            );
            
            $this->driver->manage()->timeouts()->implicitlyWait(10);
            
            echo "Intentando cargar la página de login...\n";
            $this->driver->get('http://web/views/auth/login.php');
            
        } catch (\Exception $e) {
            echo "\nError detallado: " . $e->getMessage() . "\n";
            $this->fail('Error accediendo a la página de login: ' . $e->getMessage());
        }
    }

    public function testLoginPageElements()
    {
        try {
            $this->driver->get('http://localhost:8080/views/auth/login.php');
            
            // Verificar elementos del formulario
            $emailInput = $this->driver->findElement(WebDriverBy::name('email'));
            $passwordInput = $this->driver->findElement(WebDriverBy::name('password'));
            $submitButton = $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'));
            
            $this->assertTrue($emailInput->isDisplayed());
            $this->assertTrue($passwordInput->isDisplayed());
            $this->assertTrue($submitButton->isDisplayed());
        } catch (\Exception $e) {
            $this->fail('Error accediendo a la página de login: ' . $e->getMessage());
        }
    }

    public function testLoginAttempt()
    {
        try {
            $this->driver->get('http://localhost:8080/views/auth/login.php');
            
            // Ingresar credenciales
            $emailInput = $this->driver->findElement(WebDriverBy::name('email'));
            $passwordInput = $this->driver->findElement(WebDriverBy::name('password'));
            $submitButton = $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'));
            
            $emailInput->sendKeys('test@example.com');
            $passwordInput->sendKeys('password123');
            $submitButton->click();
            
            // Esperar y verificar resultado (ajusta según tu implementación)
            sleep(2); // Dar tiempo para la respuesta
            
            // Verifica si hay mensaje de error o redirección exitosa
            try {
                $errorMessage = $this->driver->findElement(WebDriverBy::cssSelector('.error'));
                $this->assertStringContainsString('error', $errorMessage->getText());
            } catch (\Exception $e) {
                // Si no hay mensaje de error, debería haber sido redirigido
                $this->assertStringContainsString('/dashboard', $this->driver->getCurrentURL());
            }
        } catch (\Exception $e) {
            $this->fail('Error en el intento de login: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
} 