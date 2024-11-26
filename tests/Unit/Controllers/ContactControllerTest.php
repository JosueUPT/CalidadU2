<?php
namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\ContactController;
use Models\Message;
use Models\User;
use PDO;
use PDOStatement;

class ContactControllerTest extends TestCase
{
    private $conn;
    private $contactController;
    private $pdoStatement;
    private $user;
    private $message;

    protected function setUp(): void
    {
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        
        // Mockear User y Message
        $this->user = $this->createMock(User::class);
        $this->message = $this->createMock(Message::class);
        
        $this->contactController = $this->getMockBuilder(ContactController::class)
            ->setConstructorArgs([$this->conn])
            ->onlyMethods(['createUser', 'createMessage'])
            ->getMock();
            
        $this->contactController->method('createUser')
            ->willReturn($this->user);
        $this->contactController->method('createMessage')
            ->willReturn($this->message);
    }

    #[Test]
    public function send_message_success(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        // Configurar comportamiento esperado
        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willReturn(true);

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(true, $result['success']);
        $this->assertSame('¡Mensaje enviado exitosamente!', $result['message']);
    }

    #[Test]
    public function send_message_missing_fields(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez'
            // Faltan campos requeridos
        ];

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('Faltan campos requeridos', $result['message']);
    }

    #[Test]
    public function send_message_user_not_found(): void
    {
        $userData = [
            'user_id' => 999,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        // Mock para User::exists retornando false
        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(false);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('Usuario no encontrado', $result['message']);
    }

    #[Test]
    public function send_message_already_sent(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        // Configurar comportamiento esperado
        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(true); // Mensaje ya existe
        
        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('¡Mensaje ya enviado!', $result['message']);
    }

    #[Test]
    public function send_message_save_error(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        // Configurar comportamiento esperado
        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willReturn(false);

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('Error al enviar mensaje', $result['message']);
    }

    #[Test]
    public function send_message_throws_exception(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        // Simular una excepción al verificar si el usuario existe
        $this->user->method('exists')
            ->willThrowException(new \Exception('Error de conexión'));

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertStringContainsString('Error al enviar mensaje', $result['message']);
    }
} 