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

    #[Test]
    public function user_can_register(): void 
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

    #[Test]
    public function user_can_login(): void
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

    #[Test]
    public function login_fails_with_wrong_credentials(): void
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

    #[Test]
    public function can_get_user_by_id(): void
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
} 