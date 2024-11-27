# language: es
Característica: Autenticación de usuarios
  Como usuario del sistema
  Quiero poder registrarme y acceder a mi cuenta
  Para gestionar mis compras y datos personales

  Escenario: Registro exitoso
    Dado que estoy en la página de registro
    Cuando completo el formulario con:
      | nombre     | Juan Pérez      |
      | email      | juan@email.com  |
      | contraseña | password123     |
      | confirmar  | password123     |
    Y presiono "Registrar"
    Entonces debería ver "Registro exitoso"

  Escenario: Login exitoso
    Dado que estoy en la página de login
    Cuando ingreso "usuario@test.com" como email
    Y ingreso "password123" como contraseña
    Y presiono "Iniciar sesión"
    Entonces debería estar logueado
    Y debería ver "Bienvenido"

  Escenario: Login fallido
    Dado que estoy en la página de login
    Cuando ingreso "usuario@test.com" como email
    Y ingreso "wrongpassword" como contraseña
    Y presiono "Iniciar sesión"
    Entonces debería ver "Credenciales inválidas"

  Escenario: Recuperar contraseña
    Dado que estoy en la página de recuperación
    Cuando ingreso "usuario@test.com" como email
    Y presiono "Recuperar contraseña"
    Entonces debería ver "Email de recuperación enviado"

  Escenario: Cerrar sesión
    Dado que estoy logueado
    Cuando presiono "Cerrar sesión"
    Entonces debería estar deslogueado
    Y debería ver "Sesión cerrada correctamente" 