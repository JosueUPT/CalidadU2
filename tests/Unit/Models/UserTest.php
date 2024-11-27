<?php
namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Models\User;
use PDO;
use PDOStatement;

class UserTest extends TestCase
{
    private $user;
    private $conn;
    private $pdoStatement;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->conn = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
    }

    /** @test */
    public function verifica_constructor_inicializa_user_type(): void
    {
        $user = new User();
        $this->assertEquals('user', $user->getUserType());
    }

    /** @test */
    public function verifica_password_es_hasheado(): void
    {
        $password = "123456";
        $this->user->setPassword($password);
        
        $reflection = new \ReflectionClass($this->user);
        $property = $reflection->getProperty('password');
        $property->setAccessible(true);
        $hashedPassword = $property->getValue($this->user);
        
        $this->assertEquals(60, strlen($hashedPassword));
        $this->assertTrue(password_verify($password, $hashedPassword));
    }

    /** @test */
    public function verifica_exists_retorna_true_cuando_existe(): void
    {
        $this->user->setId(1);
        
        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$this->user->getId()])
            ->willReturn(true);
            
        $this->pdoStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);
            
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);
        
        $this->assertTrue($this->user->exists($this->conn));
    }

    /** @test */
    public function verifica_exists_retorna_false_cuando_no_existe(): void
    {
        $this->user->setId(999);
        
        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([$this->user->getId()])
            ->willReturn(true);
            
        $this->pdoStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);
            
        $this->conn->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);
        
        $this->assertFalse($this->user->exists($this->conn));
    }

    /** @test */
    public function verifica_setId_retorna_instancia(): void
    {
        $result = $this->user->setId(1);
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $this->user->getId());
    }
} 