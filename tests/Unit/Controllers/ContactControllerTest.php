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

    /** @test */
    public function enviar_mensaje_exitoso(): void
    {
        $datosUsuario = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willReturn(true);

        $resultado = $this->contactController->sendMessage($datosUsuario);

        $this->assertSame(true, $resultado['success']);
        $this->assertSame('¡Mensaje enviado exitosamente!', $resultado['message']);
    }

    /** @test */
    public function enviar_mensaje_campos_faltantes(): void
    {
        $datosUsuario = [
            'user_id' => 1,
            'name' => 'Juan Pérez'
        ];

        $resultado = $this->contactController->sendMessage($datosUsuario);

        $this->assertSame(false, $resultado['success']);
        $this->assertSame('Faltan campos requeridos', $resultado['message']);
    }

    /** @test */
    public function enviar_mensaje_usuario_no_encontrado(): void
    {
        $userData = [
            'user_id' => 999,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->pdoStatement->method('execute')->willReturn(true);
        $this->pdoStatement->method('fetch')->willReturn(false);
        $this->conn->method('prepare')->willReturn($this->pdoStatement);

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('Usuario no encontrado', $result['message']);
    }

    /** @test */
    public function enviar_mensaje_ya_enviado(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(true);
        
        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('¡Mensaje ya enviado!', $result['message']);
    }

    /** @test */
    public function enviar_mensaje_error_al_guardar(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willReturn(false);

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertSame('Error al enviar mensaje', $result['message']);
    }

    /** @test */
    public function enviar_mensaje_lanza_excepcion(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->user->method('exists')
            ->willThrowException(new \Exception('Error de conexión'));

        $result = $this->contactController->sendMessage($userData);

        $this->assertSame(false, $result['success']);
        $this->assertStringContainsString('Error al enviar mensaje', $result['message']);
    }

    /** @test */
    public function crear_usuario_retorna_instancia_user(): void
    {
        $contactController = new ContactController($this->conn);
        $reflection = new \ReflectionClass($contactController);
        $method = $reflection->getMethod('createUser');
        $method->setAccessible(true);

        $result = $method->invoke($contactController);

        $this->assertInstanceOf(User::class, $result);
    }

    /** @test */
    public function crear_mensaje_retorna_instancia_message(): void
    {
        $contactController = new ContactController($this->conn);
        $reflection = new \ReflectionClass($contactController);
        $method = $reflection->getMethod('createMessage');
        $method->setAccessible(true);

        $result = $method->invoke($contactController);

        $this->assertInstanceOf(Message::class, $result);
    }

    /** @test */
    public function enviar_mensaje_valida_setters(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->message->expects($this->once())->method('setUserId')->with($userData['user_id']);
        $this->message->expects($this->once())->method('setName')->with($userData['name']);
        $this->message->expects($this->once())->method('setEmail')->with($userData['email']);
        $this->message->expects($this->once())->method('setNumber')->with($userData['number']);
        $this->message->expects($this->once())->method('setMessage')->with($userData['message']);

        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willReturn(true);

        $this->contactController->sendMessage($userData);
    }

    /** @test */
    public function constructor_asigna_conexion(): void
    {
        $contactController = new ContactController($this->conn);
        $reflection = new \ReflectionClass($contactController);
        $property = $reflection->getProperty('conn');
        $property->setAccessible(true);

        $this->assertSame($this->conn, $property->getValue($contactController));
    }

    /** @test */
    public function enviar_mensaje_maneja_error_en_save(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willThrowException(new \Exception('Error al guardar'));

        $result = $this->contactController->sendMessage($userData);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al enviar mensaje', $result['message']);
    }

    /** @test */
    public function enviar_mensaje_valida_user_id(): void
    {
        $userData = [
            'user_id' => 1,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'number' => '123456789',
            'message' => 'Mensaje de prueba'
        ];

        $this->user->expects($this->once())
            ->method('setId')
            ->with($userData['user_id']);

        $this->user->method('exists')->willReturn(true);
        $this->message->method('exists')->willReturn(false);
        $this->message->method('save')->willReturn(true);

        $this->contactController->sendMessage($userData);
    }
} 