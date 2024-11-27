<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class LoginPageTest extends TestCase
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
        
        $videoFileName = 'user_login_test_' . date('Y-m-d_H-i-s') . '.mp4';
        echo "Nombre del archivo de video: " . $videoFileName . "\n";
        
        $capabilities->setCapability('selenoid:options', [
            'enableVideo' => true,
            'videoName' => $videoFileName,
            'enableVNC' => true,
            'name' => 'User Login Test Recording'
        ]);
        
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testUserLoginVisual()
    {
        try {
            echo "Intentando cargar la página de login del usuario...\n";
            $this->driver->get($this->baseUrl . '/views/auth/login.php');
            sleep(2);
            
            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";
            
            // Verificar elementos del formulario de login
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('form-container')),
                'No se encontró el contenedor del formulario'
            );
            
            // Credenciales de usuario
            echo "Ingresando credenciales de usuario...\n";
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys('test@hotmail.com');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('123456');
            sleep(1);
            
            echo "Haciendo clic en el botón de login...\n";
            $submitButton = $this->driver->findElement(WebDriverBy::name('submit'));
            $submitButton->click();
            
            sleep(3);
            
            // Verificar redirección y elementos en home.php
            $currentUrl = $this->driver->getCurrentURL();
            echo "URL después del login: " . $currentUrl . "\n";
            
            $this->assertStringContainsString(
                '/views/usuario/home.php',
                $currentUrl,
                'La redirección a la página de usuario no fue exitosa'
            );
            
            sleep(2);
            
            // Verificar todos los elementos principales de home.php
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('home')),
                'No se encontró la sección home'
            );
            
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('products')),
                'No se encontró la sección de productos'
            );
            
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('about')),
                'No se encontró la sección about'
            );
            
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('home-contact')),
                'No se encontró la sección de contacto'
            );
            
            // Verificar el título de productos
            $titleText = $this->driver->findElement(WebDriverBy::className('title'))->getText();
            $this->assertEquals('ÚLTIMOS PRODUCTOS', $titleText);
            
            // Verificar elementos del header (asumiendo que está incluido)
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::tagName('header')),
                'No se encontró el header'
            );
            
        } catch (\Exception $e) {
            echo "Error durante la prueba de usuario: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function testLoginFailure()
    {
        try {
            echo "Probando login con credenciales incorrectas...\n";
            $this->driver->get($this->baseUrl . '/views/auth/login.php');
            sleep(2);
            
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys('usuario_invalido@hotmail.com');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('contraseña_incorrecta');
            sleep(1);
            
            $submitButton = $this->driver->findElement(WebDriverBy::name('submit'));
            $submitButton->click();
            
            sleep(2);
            
            // Verificar mensaje de error
            $errorMessage = $this->driver->findElement(WebDriverBy::className('message'));
            $this->assertNotNull($errorMessage, 'No se mostró mensaje de error');
            $this->assertStringContainsString(
                'Correo o contraseña incorrectos', 
                $errorMessage->getText(), 
                'El mensaje de error no es el esperado'
            );
                
        } catch (\Exception $e) {
            echo "Error durante la prueba de login fallido: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
} 