<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use PHPUnit\Framework\TestCase;

class AdminUsersMessagesTest extends TestCase
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

    public function testViewUsersAndMessagesAndLogout()
    {
        try {
            // Verificar sección de Usuarios
            $this->checkUsersSection();
            sleep(1);

            // Verificar sección de Mensajes
            $this->checkMessagesSection();
            sleep(1);

            // Realizar cierre de sesión
            $this->performLogout();

        } catch (\Exception $e) {
            echo "Error en prueba: " . $e->getMessage() . "\n";
            echo "URL actual: " . $this->driver->getCurrentURL() . "\n";
            echo "HTML de la página: " . $this->driver->getPageSource() . "\n";
            throw $e;
        }
    }

    private function checkUsersSection()
    {
        // Navegar a la página de usuarios
        $this->driver->get($this->baseUrl . '/views/admin/admin_users.php');
        sleep(1);

        // Verificar que estamos en la página correcta
        $this->assertStringContainsString(
            'admin_users.php',
            $this->driver->getCurrentURL(),
            'No se pudo acceder a la página de usuarios'
        );

        // Verificar que existe la tabla de usuarios
        $wait = new WebDriverWait($this->driver, 10);
        $usersContainer = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('.box-container')
            )
        );

        // Verificar que hay usuarios listados o mensaje de "no hay usuarios"
        $users = $this->driver->findElements(WebDriverBy::cssSelector('.box'));
        $emptyMessage = $this->driver->findElements(WebDriverBy::cssSelector('.empty'));
        
        $this->assertTrue(
            count($users) > 0 || count($emptyMessage) > 0,
            'No se encontraron usuarios ni mensaje de "no hay usuarios"'
        );
    }

    private function checkMessagesSection()
    {
        // Navegar a la página de mensajes
        $this->driver->get($this->baseUrl . '/views/admin/admin_contacts.php');
        sleep(1);

        // Verificar que estamos en la página correcta
        $this->assertStringContainsString(
            'admin_contacts.php',
            $this->driver->getCurrentURL(),
            'No se pudo acceder a la página de mensajes'
        );

        // Verificar que existe el contenedor de mensajes
        $wait = new WebDriverWait($this->driver, 10);
        $messagesContainer = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('.box-container')
            )
        );

        // Verificar que hay mensajes listados o mensaje de "no hay mensajes"
        $messages = $this->driver->findElements(WebDriverBy::cssSelector('.box'));
        $emptyMessage = $this->driver->findElements(WebDriverBy::cssSelector('.empty'));
        
        $this->assertTrue(
            count($messages) > 0 || count($emptyMessage) > 0,
            'No se encontraron mensajes ni mensaje de "no hay mensajes"'
        );

        // Si hay mensajes, verificar que se pueden eliminar (opcional)
        if (count($messages) > 0) {
            $deleteButtons = $this->driver->findElements(WebDriverBy::cssSelector('.delete-btn'));
            $this->assertGreaterThan(0, count($deleteButtons), 'No se encontraron botones de eliminar');
        }
    }

    private function performLogout()
    {
        // Hacer clic en el icono de usuario para mostrar el menú
        $userBtn = $this->driver->findElement(WebDriverBy::id('user-btn'));
        $userBtn->click();
        sleep(1);

        // Esperar a que aparezca el menú de cuenta
        $wait = new WebDriverWait($this->driver, 10);
        $accountBox = $wait->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::cssSelector('.account-box')
            )
        );

        // Hacer clic en el botón de cerrar sesión
        $logoutBtn = $accountBox->findElement(WebDriverBy::cssSelector('.delete-btn'));
        $logoutBtn->click();
        sleep(1);

        // Verificar que hemos sido redirigidos a la página de login
        $this->assertStringContainsString(
            'login.php',
            $this->driver->getCurrentURL(),
            'No se redirigió correctamente a la página de login después de cerrar sesión'
        );

        // Verificar que ya no podemos acceder a la página de admin
        $this->driver->get($this->baseUrl . '/views/admin/admin_page.php');
        sleep(1);

        // Deberíamos ser redirigidos de nuevo al login
        $this->assertStringContainsString(
            'login.php',
            $this->driver->getCurrentURL(),
            'No se está protegiendo correctamente el acceso a páginas de administrador después del logout'
        );
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            sleep(1);
            $this->driver->quit();
        }
    }
} 