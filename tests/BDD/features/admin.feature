# language: es
Característica: Panel de Administración
  Como administrador del sistema
  Quiero gestionar productos y usuarios
  Para mantener la tienda actualizada

  Antecedentes:
    Dado que estoy logueado como administrador
    Y estoy en el panel de administración

  Escenario: Ver dashboard
    Entonces debería ver estadísticas de:
      | Ventas totales    |
      | Nuevos usuarios   |
      | Productos activos |
      | Pedidos pendientes|

  Escenario: Crear nuevo producto
    Cuando accedo a "productos/nuevo"
    Y completo los datos del producto:
      | nombre      | Producto Test     |
      | precio      | 99.99            |
      | stock       | 100              |
      | descripción | Descripción test |
    Y presiono "Guardar"
    Entonces debería ver "Producto creado correctamente"

  Escenario: Editar producto existente
    Cuando selecciono un producto existente
    Y modifico el precio a "149.99"
    Y presiono "Actualizar"
    Entonces debería ver "Producto actualizado correctamente"

  Escenario: Eliminar producto
    Cuando selecciono un producto existente
    Y presiono "Eliminar"
    Y confirmo la acción
    Entonces debería ver "Producto eliminado correctamente"

  Escenario: Ver listado de usuarios
    Cuando accedo a "usuarios"
    Entonces debería ver la lista de usuarios registrados

  Escenario: Ver pedidos recientes
    Cuando accedo a "pedidos"
    Entonces debería ver los últimos pedidos

  Escenario: Cambiar estado de pedido
    Cuando accedo a "pedidos"
    Y selecciono un pedido
    Y cambio el estado a "Enviado" 