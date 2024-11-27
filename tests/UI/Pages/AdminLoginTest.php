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
            '--window-size=1920,1080'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $testName = $this->getName();
        $sessionName = match($testName) {
            'testAdminLoginSuccessful' => 'Session_AdminLogin_Exitoso',
            'testAdminLoginWithInvalidCredentials' => 'Session_AdminLogin_Fallido',
            'testAdminLogoutSuccessful' => 'Session_AdminLogout_Exitoso',
            default => 'Session_' . $testName
        };

        echo sprintf(
            "%s\nOS Logo\nBrowser Logo\nv.%s\n",
            $sessionName,
            '130.0.6723.91'
        );

        $videoFileName = sprintf(
            'admin_%s_%s.mp4',
            str_replace('test', '', strtolower($testName)),
            date('Y-m-d_H-i-s')
        );
        
        echo "Nombre del archivo de video: " . $videoFileName . "\n";

        $capabilities->setCapability('selenoid:options', [
            'enableVideo' => true,
            'videoName' => $videoFileName,
            'enableVNC' => true,
            'name' => $sessionName,
            'sessionTimeout' => '15m',
            'timeZone' => 'America/Lima',
            'labels' => [
                'session' => $sessionName,
                'browser' => 'chrome',
                'version' => '130.0.6723.91',
                'test' => str_replace('test', '', $testName)
            ]
        ]);

        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    /**
     * @test
     * @testdox 1. Prueba de inicio de sesión exitoso como administrador
     */
    public function testAdminLoginVisual()
    {
        try {
            echo "Intentando cargar la página de login del administrador...\n";
            $this->driver->get($this->baseUrl . '/views/auth/login.php');
            sleep(2);

            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";

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

            $currentUrl = $this->driver->getCurrentURL();
            echo "URL después del login: " . $currentUrl . "\n";

            $this->assertStringContainsString(
                '/views/admin/admin_page.php',
                $currentUrl,
                'La redirección al panel de administrador no fue exitosa'
            );

            $this->assertNotNull(
                $this->driver->findElement(WebDriverBy::className('dashboard')),
                'No se encontró el dashboard del administrador'
            );

            $titleElement = $this->driver->findElement(WebDriverBy::className('title'));
            $this->assertNotNull($titleElement, 'No se encontró el título del panel');
            $this->assertEquals('PANEL DE CONTROL', $titleElement->getText());
        } catch (\Exception $e) {
            echo "Error durante la prueba de administrador: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * @test
     * @testdox 2. Prueba de inicio de sesión fallido con credenciales incorrectas
     */
    public function testAdminLoginFailure()
    {
        try {
            echo "Probando login de administrador con credenciales incorrectas...\n";
            $this->driver->get($this->baseUrl . '/views/auth/login.php');
            sleep(2);

            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";

            echo "Ingresando credenciales incorrectas...\n";
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys('admin_incorrecto@hotmail.com');
            sleep(1);

            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('contraseña_incorrecta');
            sleep(1);

            echo "Haciendo clic en el botón de login...\n";
            $submitButton = $this->driver->findElement(WebDriverBy::name('submit'));
            $submitButton->click();

            sleep(2);

            $currentUrl = $this->driver->getCurrentURL();
            $this->assertStringContainsString(
                '/views/auth/login.php',
                $currentUrl,
                'La página no permaneció en el login después de credenciales incorrectas'
            );

            $errorMessage = $this->driver->findElement(WebDriverBy::className('message'));
            $this->assertNotNull($errorMessage, 'No se mostró mensaje de error');
            $this->assertStringContainsString(
                'Correo o contraseña incorrectos',
                $errorMessage->getText(),
                'El mensaje de error no es el esperado'
            );

            echo "Prueba de login fallido completada exitosamente\n";
        } catch (\Exception $e) {
            echo "Error durante la prueba de login fallido: " . $e->getMessage() . "\n";
            $screenshot = $this->driver->takeScreenshot();
            $filename = 'error_screenshot_' . date('Y-m-d_H-i-s') . '.png';
            file_put_contents($filename, $screenshot);
            echo "Screenshot guardado como: " . $filename . "\n";
            throw $e;
        }
    }

    /**
     * @test
     * @testdox 3. Prueba de cierre de sesión exitoso del administrador
     */
    public function testAdminLogoutVisual()
    {
        try {
            echo "Preparando prueba de logout - Iniciando sesión primero...\n";
            $this->driver->get($this->baseUrl . '/views/auth/login.php');
            sleep(2);

            echo "Ingresando credenciales de administrador...\n";
            $this->driver->findElement(WebDriverBy::name('email'))
                ->sendKeys('admin@hotmail.com');
            $this->driver->findElement(WebDriverBy::name('password'))
                ->sendKeys('123456');

            echo "Haciendo clic en el botón de login...\n";
            $this->driver->findElement(WebDriverBy::name('submit'))->click();
            sleep(2);

            $currentUrl = $this->driver->getCurrentURL();
            $this->assertStringContainsString(
                '/views/admin/admin_page.php',
                $currentUrl,
                'No se pudo acceder al panel de administrador'
            );

            echo "Abriendo menú de usuario...\n";
            $userBtn = $this->driver->findElement(WebDriverBy::id('user-btn'));
            $userBtn->click();
            sleep(1);

            echo "Haciendo clic en cerrar sesión...\n";
            $logoutButton = $this->driver->findElement(WebDriverBy::cssSelector('.delete-btn'));
            $logoutButton->click();
            sleep(2);

            $currentUrl = $this->driver->getCurrentURL();
            $this->assertStringContainsString(
                '/views/auth/login.php',
                $currentUrl,
                'La redirección al login después del logout no fue exitosa'
            );

            echo "Prueba de logout completada exitosamente\n";
        } catch (\Exception $e) {
            echo "Error durante la prueba de logout: " . $e->getMessage() . "\n";
            $screenshot = $this->driver->takeScreenshot();
            $filename = 'error_screenshot_' . date('Y-m-d_H-i-s') . '.png';
            file_put_contents($filename, $screenshot);
            echo "Screenshot guardado como: " . $filename . "\n";
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
