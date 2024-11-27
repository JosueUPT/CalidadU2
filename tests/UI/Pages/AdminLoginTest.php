<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class AdminLoginTest extends TestCase
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
            '--use-fake-ui-for-media-stream',
            '--use-fake-device-for-media-stream',
            '--allow-file-access-from-files',
            '--disable-popup-blocking',
            '--disable-notifications'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $capabilities->setCapability('name', 'Admin Login Test');
        $capabilities->setCapability('video', true);
        
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testAdminLoginVisual()
    {
        try {
            echo "Intentando cargar la página de login del administrador...\n";
            $this->driver->get($this->baseUrl . '/views/auth/login.php');
            sleep(2);
            
            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";
            
            // Credenciales de administrador
            echo "Ingresando credenciales de administrador...\n";
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys('admin@hotmail.com');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('123456');
            sleep(1);
            
            echo "Haciendo clic en el botón de login...\n";
            $submitButton = $this->driver->findElement(WebDriverBy::name('submit'));
            $submitButton->click();
            
            sleep(3);
            
            // Verificar redirección al panel de administrador
            $currentUrl = $this->driver->getCurrentURL();
            echo "URL después del login: " . $currentUrl . "\n";
            
            // Verificar solo la URL primero
            $this->assertStringContainsString(
                '/views/admin/admin_page.php',
                $currentUrl,
                'La redirección al panel de administrador no fue exitosa'
            );
            
            // Agregar un tiempo de espera explícito
            sleep(2);
            
            // Verificar la existencia de cualquier elemento que sepamos que existe en admin_page.php
            // Por ejemplo, si sabemos que hay un div con clase 'admin-container':
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('dashboard')),
                'No se encontró el dashboard del administrador'
            );
            
            // Verificar la URL y el título
            $this->assertStringContainsString('/views/admin/admin_page.php', $currentUrl);
            
            // Esperar a que la página cargue completamente
            sleep(2);
            
            // Verificar elementos que sí existen en el HTML real
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('dashboard')),
                'No se encontró el dashboard del administrador'
            );
            
            // Verificar el título del panel
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('title')),
                'No se encontró el título del panel de control'
            );
            
            // Opcional: Verificar que el texto del título sea correcto
            $titleText = $this->driver->findElement(WebDriverBy::className('title'))->getText();
            $this->assertEquals('PANEL DE CONTROL', $titleText);
            
        } catch (\Exception $e) {
            echo "Error durante la prueba de administrador: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    protected function tearDown(): void
    {
        sleep(3);
        if ($this->driver) {
            $this->driver->quit();
        }
    }
} 