# language: es
Característica: Funcionalidades de Usuario
  Como usuario del sistema
  Quiero gestionar mis compras y perfil
  Para tener una experiencia de compra satisfactoria

  Antecedentes:
    Dado que estoy logueado como usuario
    Y estoy en la página principal

  Escenario: Ver página de inicio
    Cuando accedo a "home"
    Entonces debería ver la sección de últimos productos
    Y debería ver la sección "Sobre nosotros"

  Escenario: Buscar productos
    Cuando accedo a "search_page"
    Y busco el término "película"
    Entonces debería ver productos relacionados con "película"

  Escenario: Búsqueda sin resultados
    Cuando busco el término "xyzabc123"
    Entonces debería ver el mensaje "¡No se han encontrado resultados!"

  Escenario: Ver todos los productos
    Cuando accedo a "shop"

  Escenario: Agregar producto al carrito
    Dado que estoy en la tienda
    Cuando selecciono un producto
    Y establezco cantidad "2"
    Y presiono "Añadir al carrito"
    Entonces debería ver el mensaje "Producto agregado al carrito"

  Escenario: Proceso de checkout
    Dado que tengo productos en el carrito
    Cuando accedo al checkout
    Y completo los datos de envío:
      | nombre    | Juan Pérez     |
      | email     | juan@email.com |
      | teléfono  | 987654321      |
      | dirección | Calle 123      |
    Y selecciono método de pago "Pago en persona"
    Entonces debería poder finalizar la compra

  Escenario: Ver historial de pedidos
    Cuando accedo a "orders"
    Entonces debería ver mis pedidos anteriores
    Y cada pedido debería mostrar:
      | Fecha          |
      | Total          |
      | Estado         |
      | Método de pago |

  Escenario: Enviar mensaje de contacto
    Cuando accedo a "contact"
    Y completo el formulario:
      | nombre  | Juan Pérez       |
      | email   | juan@email.com   |
      | mensaje | Consulta general |

  Escenario: Validar carrito vacío
    Cuando accedo al checkout
    Y no tengo productos en el carrito

  Escenario: Actualizar cantidad en carrito
    Dado que tengo productos en el carrito
    Cuando actualizo la cantidad de un producto
    Entonces el total debería actualizarse
    Y debería ver el nuevo subtotal

  Escenario: Eliminar producto del carrito
    Dado que tengo productos en el carrito
    Cuando elimino un producto
    Entonces debería ver el mensaje "Producto eliminado del carrito"
    Y el total debería actualizarse

  Escenario: Validar datos de envío
    Cuando intento hacer checkout
    Y no completo todos los campos requeridos
    Entonces debería ver mensajes de validación
    Y no debería poder continuar

  Escenario: Ver detalles de producto
    Cuando selecciono un producto específico
    Entonces debería ver:
      | Nombre completo |
      | Descripción     |
      | Precio          |
      | Disponibilidad  |

  Escenario: Filtrar productos
    Cuando accedo a la tienda
    Y aplico filtros:
      | Categoría | Series |
      | Precio    | 0-100  |
    Entonces debería ver solo productos que cumplan los criterios

  Escenario: Compartir producto
    Cuando veo un producto
    Y presiono "Compartir"
    Entonces debería poder compartir en redes sociales:
      | Facebook |
      | Twitter  |
      | WhatsApp | 