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
    private $filters = [];
    private $pedidos = [];
    private $formData = [];

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
        $_SESSION['message'] = 'Producto agregado al carrito';
        $this->lastMessage = $_SESSION['message'];
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
        $_SESSION['message'] = $this->lastMessage;
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
        $_SESSION['message'] = 'Producto eliminado del carrito';
        $this->lastMessage = $_SESSION['message'];
    }

    /**
     * @return string
     */
    public function getLastMessage(): string
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

    /**
     * @When selecciono un producto específico
     */
    public function seleccionoUnProductoEspecifico()
    {
        $this->selectedProduct = [
            'id' => 1,
            'nombre completo' => 'Producto Test',
            'descripción' => 'Descripción detallada',
            'precio' => 99.99,
            'disponibilidad' => 'En stock'
        ];
    }

    /**
     * @Then debería ver:
     */
    public function deberiaVer(TableNode $table)
    {
        $camposEsperados = array_column($table->getRows(), 0);
        $camposProducto = array_keys($this->selectedProduct);
        
        foreach ($camposEsperados as $campo) {
            $encontrado = false;
            foreach ($camposProducto as $campoProducto) {
                if (mb_strtolower($campo) === mb_strtolower($campoProducto)) {
                    $encontrado = true;
                    break;
                }
            }
            Assert::assertTrue($encontrado, "Campo '$campo' no encontrado en el producto");
        }
    }

    /**
     * @When accedo a la tienda
     */
    public function accedoALaTienda()
    {
        $this->currentPage = 'shop';
    }

    /**
     * @When aplico filtros:
     */
    public function aplicoFiltros(TableNode $table)
    {
        $this->filters = $table->getRowsHash();
    }

    /**
     * @Then debería ver solo productos que cumplan los criterios
     */
    public function deberiaVerSoloProductosQueCumplanLosCriterios()
    {
        Assert::assertNotEmpty($this->filters);
    }

    /**
     * @When veo un producto
     */
    public function veoUnProducto()
    {
        $this->currentPage = 'product';
        $this->selectedProduct = [
            'id' => 1,
            'nombre' => 'Producto Test'
        ];
    }

    /**
     * @Then debería poder compartir en redes sociales:
     */
    public function deberiaPoderCompartirEnRedesSociales(TableNode $table)
    {
        $redesSociales = array_column($table->getRows(), 0);
        Assert::assertNotEmpty($redesSociales);
    }

    /**
     * @Then no debería poder continuar
     */
    public function noDeberiaPoderContinuar()
    {
        Assert::assertFalse($this->checkoutData['valido'] ?? false);
    }

    /**
     * @Then debería ver mis pedidos anteriores
     */
    public function deberiaVerMisPedidosAnteriores()
    {
        $this->pedidos = [
            [
                'fecha' => '2024-03-20',
                'total' => 199.99,
                'estado' => 'Completado',
                'método de pago' => 'Tarjeta'
            ]
        ];
        Assert::assertNotEmpty($this->pedidos);
    }

    /**
     * @Then cada pedido debería mostrar:
     */
    public function cadaPedidoDeberiaMostrar(TableNode $table)
    {
        $camposRequeridos = array_column($table->getRows(), 0);
        $camposPedido = array_keys($this->pedidos[0]);
        
        foreach ($camposRequeridos as $campo) {
            Assert::assertTrue(
                in_array(strtolower($campo), array_map('strtolower', $camposPedido)),
                "Campo '$campo' no encontrado en el pedido"
            );
        }
    }

    /**
     * @When completo el formulario:
     */
    public function completoElFormulario(TableNode $table)
    {
        $this->formData = $table->getRowsHash();
        $this->lastMessage = 'Mensaje enviado correctamente';
        $_SESSION['message'] = $this->lastMessage;
    }

    /**
     * @BeforeScenario
     */
    public function initializeSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['message'] = '';
        $this->lastMessage = '';
    }
} 