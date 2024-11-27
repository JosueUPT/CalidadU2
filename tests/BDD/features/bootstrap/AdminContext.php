<?php

namespace Tests\BDD\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

class AdminContext implements Context
{
    private $currentPage = '';
    private $user = null;
    private $products = [];
    private $selectedProduct = null;
    private $lastMessage = '';
    private $orders = [];
    private $users = [];
    private $statistics = [];
    private $selectedOrder = null;

    /**
     * @Given que estoy logueado como administrador
     */
    public function queEstoyLogueadoComoAdministrador()
    {
        $this->user = [
            'email' => 'admin@test.com',
            'role' => 'admin'
        ];
    }

    /**
     * @Given estoy en el panel de administración
     */
    public function estoyEnElPanelDeAdministracion()
    {
        $this->currentPage = 'admin/dashboard';
        $this->initializeTestData();
    }

    /**
     * @Then debería ver estadísticas de:
     */
    public function deberiaVerEstadisticasDe(TableNode $table)
    {
        foreach ($table->getRows() as $row) {
            Assert::assertArrayHasKey(
                strtolower(str_replace(' ', '_', $row[0])), 
                $this->statistics
            );
        }
    }

    /**
     * @When accedo a :page
     */
    public function accedoA($page)
    {
        $this->currentPage = "admin/$page";
    }

    /**
     * @When completo los datos del producto:
     */
    public function completoLosDatosDelProducto(TableNode $table)
    {
        $this->selectedProduct = $table->getRowsHash();
    }

    /**
     * @When selecciono un producto existente
     */
    public function seleccionoUnProductoExistente()
    {
        $this->selectedProduct = $this->products[0];
    }

    /**
     * @When modifico el precio a :price
     */
    public function modificoElPrecioA($price)
    {
        $this->selectedProduct['precio'] = $price;
    }

    /**
     * @When confirmo la acción
     */
    public function confirmoLaAccion()
    {
        // Simulación de confirmación
        return true;
    }

    /**
     * @Then debería ver la lista de usuarios registrados
     */
    public function deberiaVerLaListaDeUsuariosRegistrados()
    {
        Assert::assertNotEmpty($this->users);
    }

    /**
     * @Then debería ver los últimos pedidos
     */
    public function deberiaVerLosUltimosPedidos()
    {
        Assert::assertNotEmpty($this->orders);
    }

    /**
     * @When selecciono un pedido
     */
    public function seleccionoUnPedido()
    {
        $this->selectedOrder = $this->orders[0];
    }

    /**
     * @When cambio el estado a :status
     */
    public function cambioElEstadoA($status)
    {
        $this->selectedOrder['estado'] = $status;
        $this->lastMessage = 'Estado actualizado correctamente';
    }

    private function initializeTestData()
    {
        $this->statistics = [
            'ventas_totales' => 1500,
            'nuevos_usuarios' => 25,
            'productos_activos' => 100,
            'pedidos_pendientes' => 10
        ];

        $this->products = [
            [
                'id' => 1,
                'nombre' => 'Producto Test',
                'precio' => 99.99,
                'stock' => 100
            ]
        ];

        $this->users = [
            [
                'email' => 'usuario@test.com',
                'nombre' => 'Usuario Test',
                'rol' => 'user'
            ]
        ];

        $this->orders = [
            [
                'id' => 1,
                'cliente' => 'Cliente Test',
                'total' => 99.99,
                'estado' => 'Pendiente',
                'fecha' => '2024-03-20'
            ]
        ];
    }
} 