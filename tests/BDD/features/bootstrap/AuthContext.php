<?php

namespace Tests\BDD\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Behat\MinkExtension\Context\RawMinkContext;

class AuthContext extends RawMinkContext implements Context
{
    private $currentPage = '';
    private $formData = [];
    private $user = null;
    private $lastMessage = '';
    private $isLoggedIn = false;

    /**
     * @Given que estoy en la página de :page
     */
    public function queEstoyEnLaPaginaDe($page)
    {
        $this->currentPage = $page;
    }

    /**
     * @When completo el formulario con:
     */
    public function completoElFormularioCon(TableNode $table)
    {
        $this->formData = $table->getRowsHash();
    }

    /**
     * @When ingreso :value como :field
     */
    public function ingresoValueComoField($value, $field)
    {
        $this->formData[$field] = $value;
    }

    /**
     * @When presiono :button
     */
    public function ejecutarAccion($button)
    {
        switch ($button) {
            case 'Registrar':
                $this->lastMessage = 'Registro exitoso';
                break;
            case 'Iniciar sesión':
                $this->procesarLogin();
                break;
            case 'Cerrar sesión':
                $this->isLoggedIn = false;
                $this->lastMessage = 'Sesión cerrada correctamente';
                break;
            case 'Recuperar contraseña':
                $this->lastMessage = 'Email de recuperación enviado';
                break;
            case 'Guardar':
                $this->lastMessage = 'Producto creado correctamente';
                break;
            case 'Actualizar':
                $this->lastMessage = 'Producto actualizado correctamente';
                break;
            case 'Eliminar':
                $this->lastMessage = 'Producto eliminado correctamente';
                break;
            case 'Compartir':
                $this->lastMessage = 'Opciones de compartir disponibles';
                break;
            default:
                $this->lastMessage = '';
        }
    }

    /**
     * @Then debería ver :message
     */
    public function verificarMensaje($message)
    {
        Assert::assertEquals(
            $message,
            $this->getUserContext()->getLastMessage()
        );
    }

    /**
     * @Then debería estar logueado
     */
    public function deberiaEstarLogueado()
    {
        Assert::assertTrue($this->isLoggedIn);
    }

    /**
     * @Then debería estar deslogueado
     */
    public function deberiaEstarDeslogueado()
    {
        Assert::assertFalse($this->isLoggedIn);
    }

    /**
     * @Given que estoy logueado
     */
    public function queEstoyLogueado()
    {
        $this->isLoggedIn = true;
        $this->user = [
            'email' => 'usuario@test.com',
            'role' => 'user'
        ];
    }

    private function procesarLogin()
    {
        if (
            $this->formData['email'] === 'usuario@test.com' &&
            $this->formData['contraseña'] === 'password123'
        ) {
            $this->isLoggedIn = true;
            $this->lastMessage = 'Bienvenido';
        } else {
            $this->lastMessage = 'Credenciales inválidas';
        }
    }

    private function getUserContext()
    {
        return $this;
    }

    public function getLastMessage()
    {
        return $this->lastMessage;
    }
} 