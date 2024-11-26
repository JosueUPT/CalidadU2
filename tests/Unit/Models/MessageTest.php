<?php
namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Models\Message;
use PDO;
use PDOStatement;

class MessageTest extends TestCase
{
    private $message;
    private $conn;
    private $pdoStatement;

    protected function setUp(): void
    {
        $this->message = new Message();
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
    }

    /** @test */
    public function puede_establecer_y_obtener_id(): void
    {
        $id = 1;
        $this->message->setId($id);
        $this->assertEquals($id, $this->message->getId());
    }

    /** @test */
    public function puede_establecer_y_obtener_user_id(): void
    {
        $userId = 1;
        $this->message->setUserId($userId);
        $this->assertEquals($userId, $this->message->getUserId());
    }

    /** @test */
    public function puede_establecer_y_obtener_name(): void
    {
        $name = "Juan Pérez";
        $this->message->setName($name);
        $this->assertEquals($name, $this->message->getName());
    }

    /** @test */
    public function puede_establecer_y_obtener_email(): void
    {
        $email = "juan@example.com";
        $this->message->setEmail($email);
        $this->assertEquals($email, $this->message->getEmail());
    }

    /** @test */
    public function puede_establecer_y_obtener_number(): void
    {
        $number = "123456789";
        $this->message->setNumber($number);
        $this->assertEquals($number, $this->message->getNumber());
    }

    /** @test */
    public function puede_establecer_y_obtener_message(): void
    {
        $message = "Este es un mensaje de prueba";
        $this->message->setMessage($message);
        $this->assertEquals($message, $this->message->getMessage());
    }

    /** @test */
    public function puede_guardar_mensaje(): void
    {
        $this->message->setUserId(1);
        $this->message->setName("Juan");
        $this->message->setEmail("juan@example.com");
        $this->message->setNumber("123456789");
        $this->message->setMessage("Mensaje de prueba");

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $result = $this->message->save($this->conn);
        $this->assertTrue($result);
    }

    /** @test */
    public function maneja_error_al_guardar(): void
    {
        $this->message->setUserId(1);
        $this->message->setName("Juan");
        $this->message->setEmail("juan@example.com");
        $this->message->setNumber("123456789");
        $this->message->setMessage("Mensaje de prueba");

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $result = $this->message->save($this->conn);
        $this->assertFalse($result);
    }

    /** @test */
    public function maneja_error_al_verificar_existencia(): void
    {
        $this->message->setUserId(1);
        $this->message->setMessage("Mensaje de prueba");

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->willThrowException(new \PDOException("Error de prueba"));

        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        try {
            $result = $this->message->exists($this->conn);
            $this->assertFalse($result);
        } catch (\PDOException $e) {
            $this->assertEquals("Error de prueba", $e->getMessage());
        }
    }
} 