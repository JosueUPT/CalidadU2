<?php
namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\UserController;
use PDO;
use PDOStatement;

class UserControllerTest extends TestCase
{
    private $userController;
    private $mockPDO;

    protected function setUp(): void
    {
        // Crear mock de PDO
        $this->mockPDO = $this->createMock(PDO::class);
        $this->userController = new UserController($this->mockPDO);
    }

    /** @test */
    public function usuario_puede_registrarse(): void 
    {
        // Crear mock para PDOStatement
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn(false); // El email no existe
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->registerUser([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123'
        ]);

        $this->assertSame(true, $result['success']);
        $this->assertSame('Registro exitoso!', $result['message']);
    }

    /** @test */
    public function usuario_puede_iniciar_sesion(): void
    {
        // Crear mock para PDOStatement
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'name' => 'Juan Pérez',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'user_type' => 'user'
        ]);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->loginUser('juan@example.com', 'password123');

        $this->assertSame(true, $result['success']);
        $this->assertSame('user', $result['user_type']);
    }

    /** @test */
    public function inicio_sesion_falla_con_credenciales_incorrectas(): void
    {
        // Simular que no se encuentra el usuario
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn(false);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->loginUser('wrong@email.com', 'wrongpass');

        $this->assertSame(false, $result['success']);
        $this->assertSame('Correo o contraseña incorrectos', $result['message']);
    }

    /** @test */
    public function puede_obtener_usuario_por_id(): void
    {
        // Crear mock para PDOStatement
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'user_type' => 'user'
        ]);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $user = $this->userController->getUserById(1);

        $this->assertSame('Juan Pérez', $user['name']);
        $this->assertSame('juan@example.com', $user['email']);
        $this->assertSame('user', $user['user_type']);
    }

    /** @test */
    public function registro_falla_con_email_existente(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn(['id' => 1]); // Email ya existe
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->registerUser([
            'name' => 'Juan Pérez',
            'email' => 'existente@example.com',
            'password' => 'password123'
        ]);

        $this->assertFalse($result['success']);
        $this->assertEquals('El correo ya está registrado', $result['message']);
    }

    /** @test */
    public function manejo_error_en_registro(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willThrowException(new \Exception('Error de base de datos'));

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->registerUser([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123'
        ]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Error en el registro', $result['message']);
    }

    /** @test */
    public function manejo_error_en_login(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willThrowException(new \Exception('Error de base de datos'));

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->loginUser('juan@example.com', 'password123');

        $this->assertFalse($result['success']);
        $this->assertEquals('Error en el inicio de sesión', $result['message']);
    }

    /** @test */
    public function manejo_error_al_obtener_usuario(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willThrowException(new \Exception('Error de base de datos'));

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->getUserById(1);

        $this->assertNull($result);
    }

    /** @test */
    public function registro_con_tipo_usuario_personalizado(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn(false);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->registerUser([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'user_type' => 'admin'
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Registro exitoso!', $result['message']);
    }

    /** @test */
    public function verificar_alias_register(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn(false);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->register([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123'
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Registro exitoso!', $result['message']);
    }

    /** @test */
    public function verificar_hash_password(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $password = 'password123';
        
        $this->mockPDO->method('prepare')->willReturn($mockStmt);
        
        $result = $this->userController->registerUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password
        ]);

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function verifica_hash_password_con_costo_correcto(): void
    {
        $reflection = new \ReflectionClass($this->userController);
        $method = $reflection->getMethod('hashPassword');
        $method->setAccessible(true);

        $password = 'test123';
        $hashedPassword = $method->invoke($this->userController, $password);

        // Verificar que el hash tiene el costo correcto (12)
        $this->assertTrue(password_verify($password, $hashedPassword));
        $this->assertStringContainsString('$2y$12$', $hashedPassword);
    }

    /** @test */
    public function verifica_password_correcto(): void
    {
        $reflection = new \ReflectionClass($this->userController);
        $method = $reflection->getMethod('verifyPassword');
        $method->setAccessible(true);

        $password = 'test123';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $result = $method->invoke($this->userController, $password, $hashedPassword);
        $this->assertTrue($result);
    }

    /** @test */
    public function verifica_password_incorrecto(): void
    {
        $reflection = new \ReflectionClass($this->userController);
        $method = $reflection->getMethod('verifyPassword');
        $method->setAccessible(true);

        $password = 'test123';
        $wrongPassword = 'wrong123';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $result = $method->invoke($this->userController, $wrongPassword, $hashedPassword);
        $this->assertFalse($result);
    }

    /** @test */
    public function login_falla_con_password_incorrecto(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'password' => password_hash('correctpass', PASSWORD_BCRYPT),
            'user_type' => 'user'
        ]);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->loginUser('test@example.com', 'wrongpass');

        $this->assertFalse($result['success']);
        $this->assertEquals('Correo o contraseña incorrectos', $result['message']);
    }

    /** @test */
    public function login_exitoso_con_admin(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'name' => 'Admin',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'user_type' => 'admin'
        ]);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->loginUser('admin@test.com', 'admin123');

        $this->assertTrue($result['success']);
        $this->assertEquals('admin', $result['user_type']);
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('Admin', $_SESSION['user_name']);
    }

    /** @test */
    public function registro_exitoso_sin_tipo_usuario(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetch')->willReturn(false);
        $mockStmt->method('execute')->willReturn(true);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->registerUser([
            'name' => 'Default User',
            'email' => 'default@test.com',
            'password' => 'pass123'
            // Sin especificar user_type, debería usar 'user' por defecto
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Registro exitoso!', $result['message']);
    }

    /** @test */
    public function obtener_usuario_inexistente(): void
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false);

        $this->mockPDO->method('prepare')->willReturn($mockStmt);

        $result = $this->userController->getUserById(999);
        $this->assertFalse($result);
    }
} 