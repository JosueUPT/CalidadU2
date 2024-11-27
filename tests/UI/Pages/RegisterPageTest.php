<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class RegisterPageTest extends TestCase
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
        
        $videoFileName = 'user_register_test_' . date('Y-m-d_H-i-s') . '.mp4';
        echo "Nombre del archivo de video: " . $videoFileName . "\n";
        
        $capabilities->setCapability('selenoid:options', [
            'enableVideo' => true,
            'videoName' => $videoFileName,
            'enableVNC' => true,
            'name' => 'User Register Test Recording'
        ]);
        
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testUserRegisterSuccess()
    {
        try {
            echo "Intentando cargar la página de registro...\n";
            $this->driver->get($this->baseUrl . '/views/auth/register.php');
            sleep(2);
            
            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('form-container')),
                'No se encontró el contenedor del formulario'
            );
            
            $uniqueEmail = 'test_' . time() . '@hotmail.com';
            
            echo "Ingresando datos de registro...\n";
            $this->driver->findElement(WebDriverBy::name('name'))
                ->sendKeys('Usuario Test');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys($uniqueEmail);
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('123456');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('cpassword'))
                ->sendKeys('123456');
            sleep(1);
            
            echo "Haciendo clic en el botón de registro...\n";
            $submitButton = $this->driver->findElement(WebDriverBy::name('submit'));
            $submitButton->click();
            
            sleep(3);
            
            $currentUrl = $this->driver->getCurrentURL();
            echo "URL después del registro: " . $currentUrl . "\n";
            
            $this->assertStringContainsString(
                '/views/auth/login.php',
                $currentUrl,
                'La redirección a la página de login no fue exitosa'
            );
            
        } catch (\Exception $e) {
            echo "Error durante la prueba de registro: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function testRegisterFailure()
    {
        try {
            echo "Probando registro con contraseñas que no coinciden...\n";
            $this->driver->get($this->baseUrl . '/views/auth/register.php');
            sleep(2);
            
            $this->driver->findElement(WebDriverBy::name('name'))
                ->sendKeys('Usuario Test');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys('test@hotmail.com');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('123456');
            sleep(1);
            
            $this->driver->findElement(WebDriverBy::name('cpassword'))
                ->sendKeys('654321');
            sleep(1);
            
            $submitButton = $this->driver->findElement(WebDriverBy::name('submit'));
            $submitButton->click();
            
            sleep(2);
            
            // Verificar mensaje de error
            $errorMessage = $this->driver->findElement(WebDriverBy::className('message'));
            $this->assertNotNull($errorMessage, 'No se mostró mensaje de error');
            $this->assertStringContainsString(
                'Las contraseñas no coinciden!', 
                $errorMessage->getText(), 
                'El mensaje de error no es el esperado'
            );
                
        } catch (\Exception $e) {
            echo "Error durante la prueba de registro fallido: " . $e->getMessage() . "\n";
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