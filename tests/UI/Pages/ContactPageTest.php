<?php

namespace Tests\UI\Pages;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use PHPUnit\Framework\TestCase;

class ContactPageTest extends TestCase
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

    public function testSendContactForm()
    {
        $this->driver->get($this->baseUrl . '/views/usuario/contact.php');
        
        $this->driver->findElement(WebDriverBy::name('name'))->sendKeys('Usuario Test');
        $this->driver->findElement(WebDriverBy::name('email'))->sendKeys('test@example.com');
        $this->driver->findElement(WebDriverBy::name('number'))->sendKeys('123456789');
        $this->driver->findElement(WebDriverBy::name('message'))->sendKeys('Este es un mensaje de prueba');
        
        $this->driver->findElement(WebDriverBy::name('send'))->click();
        
        $wait = new WebDriverWait($this->driver, 10);
        $messageElement = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.message'))
        );
        
        $actualMessage = $messageElement->getText();
        $this->assertTrue(
            in_array($actualMessage, [
                '¡Mensaje ya enviado!',
                '¡Mensaje enviado exitosamente!'
            ]),
            "El mensaje '$actualMessage' no coincide con ninguno de los mensajes esperados"
        );
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
} 