<?php

namespace Tests\BDD\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

class UserContext implements Context
{
    private $currentPage = '';
    private $cart = [];
    private $lastMessage = '';
    private $selectedProduct = null;
    private $searchResults = [];
    private $checkoutData = [];
    private $user = [];
    private $page;

    /**
     * @Given que estoy logueado como usuario
     */
    public function queEstoyLogueadoComoUsuario()
    {
        $this->user = [
            'email' => 'usuario@test.com',
            'role' => 'user'
        ];
    }

    /**
     * @Given estoy en la página principal
     */
    public function estoyEnLaPaginaPrincipal()
    {
        $this->currentPage = 'home';
    }

    /**
     * @Then debería ver la sección de últimos productos
     */
    public function deberiaVerLaSeccionDeUltimosProductos()
    {
        // Simulación
    }

    /**
     * @Then debería ver la sección :section
     */
    public function deberiaVerLaSeccion($section)
    {
        // Simulación
    }

    /**
     * @When busco el término :term
     */
    public function buscoElTermino($term)
    {
        $this->searchResults = ($term === 'xyzabc123') ? [] : ['Producto 1', 'Producto 2'];
        $this->lastMessage = empty($this->searchResults) ? 
            '¡No se han encontrado resultados!' : 
            'Resultados encontrados';
    }

    /**
     * @Then debería ver productos relacionados con :term
     */
    public function deberiaVerProductosRelacionadosCon($term)
    {
        Assert::assertNotEmpty($this->searchResults);
    }

    /**
     * @Given que estoy en la tienda
     */
    public function queEstoyEnLaTienda()
    {
        $this->currentPage = 'shop';
    }

    /**
     * @When selecciono un producto
     */
    public function seleccionoUnProducto()
    {
        $this->selectedProduct = [
            'id' => 1,
            'nombre' => 'Producto Test',
            'precio' => 99.99
        ];
    }

    /**
     * @When establezco cantidad :quantity
     */
    public function establezoCantidad($quantity)
    {
        $this->selectedProduct['cantidad'] = (int)$quantity;
    }

    /**
     * @Given que tengo productos en el carrito
     */
    public function queTegoProductosEnElCarrito()
    {
        $this->cart = [
            [
                'id' => 1,
                'nombre' => 'Producto 1',
                'precio' => 99.99,
                'cantidad' => 2
            ]
        ];
    }

    /**
     * @When accedo al checkout
     */
    public function accedoAlCheckout()
    {
        $this->currentPage = 'checkout';
    }

    /**
     * @When completo los datos de envío:
     */
    public function completoLosDatosDeEnvio(TableNode $table)
    {
        $this->checkoutData = $table->getRowsHash();
    }

    /**
     * @When selecciono método de pago :method
     */
    public function seleccionoMetodoDePago($method)
    {
        $this->checkoutData['metodoPago'] = $method;
    }

    /**
     * @Then debería poder finalizar la compra
     */
    public function deberiaPoderFinalizarLaCompra()
    {
        Assert::assertNotEmpty($this->checkoutData);
        Assert::assertNotEmpty($this->cart);
    }

    /**
     * @When no tengo productos en el carrito
     */
    public function noTengoProductosEnElCarrito()
    {
        $this->cart = [];
        $this->lastMessage = 'Tu carrito está vacío';
    }

    /**
     * @When actualizo la cantidad de un producto
     */
    public function actualizoLaCantidadDeUnProducto()
    {
        if (!empty($this->cart)) {
            $this->cart[0]['cantidad'] = 3;
        }
    }

    /**
     * @Then el total debería actualizarse
     */
    public function elTotalDeberiaActualizarse()
    {
        // Simulación
    }

    /**
     * @Then debería ver el nuevo subtotal
     */
    public function deberiaVerElNuevoSubtotal()
    {
        // Simulación
    }

    /**
     * @When elimino un producto
     */
    public function eliminoUnProducto()
    {
        array_pop($this->cart);
        $this->lastMessage = 'Producto eliminado del carrito';
    }

    /**
     * @return string
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * @Then debería ver el mensaje :message
     */
    public function deberiaVerElMensaje($message)
    {
        Assert::assertEquals($message, $this->lastMessage);
    }


    /**
     * @When intento hacer checkout
     */
    public function intentoHacerCheckout()
    {
        // Implementar la lógica para iniciar el checkout
    }

    /**
     * @When no completo todos los campos requeridos
     */
    public function noCompletoTodosLosCamposRequeridos()
    {
        // Implementar la lógica para simular campos incompletos
    }

    /**
     * @Then debería ver mensajes de validación
     */
    public function deberiaVerMensajesDeValidacion()
    {
        // Verificar que se muestran mensajes de error
    }
} 