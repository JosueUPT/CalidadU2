<?php

namespace Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Config\Database;
use Exceptions\DatabaseException;

class DatabaseTest extends TestCase
{
    /** @test */
    public function test_conexion_fallida_lanza_excepcion(): void
    {
        $database = new Database(false);
        
        $this->expectException(DatabaseException::class);
        $database->connect();
    }
} 