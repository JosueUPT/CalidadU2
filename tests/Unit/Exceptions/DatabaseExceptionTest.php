<?php
namespace Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Exceptions\DatabaseException;

class DatabaseExceptionTest extends TestCase
{
    /** @test */
    public function puede_crear_excepcion_con_parametros(): void
    {
        $message = "Error de prueba";
        $code = 123;
        $previous = new \Exception("Excepción previa");

        $exception = new DatabaseException($message, $code, $previous);

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    /** @test */
    public function puede_crear_excepcion_sin_parametros(): void
    {
        $exception = new DatabaseException();

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertEquals("", $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    /** @test */
    public function hereda_de_exception(): void
    {
        $exception = new DatabaseException();
        
        $this->assertInstanceOf(\Exception::class, $exception);
    }
} 