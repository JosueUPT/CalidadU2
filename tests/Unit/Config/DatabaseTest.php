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
        $database->setCredentials('host_invalido', 'usuario_invalido', 'pass_invalida', 'db_invalida');
        
        $this->expectException(DatabaseException::class);
        $database->connect();
    }

    /** @test */
    public function test_constructor_usa_env_cuando_existe(): void
    {
        // Simular variables de entorno para la prueba
        putenv("DB_HOST=test_host");
        putenv("DB_USER=test_user");
        putenv("DB_PASSWORD=test_pass");
        putenv("DB_NAME=test_db");

        $database = new Database(false); // Usar false para evitar leer .env

        $reflection = new \ReflectionClass($database);
        
        $hostProp = $reflection->getProperty('host');
        $hostProp->setAccessible(true);
        $this->assertEquals('test_host', $hostProp->getValue($database));

        // Limpiar el ambiente
        putenv("DB_HOST");
        putenv("DB_USER");
        putenv("DB_PASSWORD");
        putenv("DB_NAME");
    }

    /** @test */
    public function test_constructor_usa_valores_por_defecto_cuando_no_hay_env(): void
    {
        $database = new Database(false);
        
        $reflection = new \ReflectionClass($database);
        
        $hostProp = $reflection->getProperty('host');
        $hostProp->setAccessible(true);
        $this->assertEquals('db', $hostProp->getValue($database));

        $userProp = $reflection->getProperty('user');
        $userProp->setAccessible(true);
        $this->assertEquals('root', $userProp->getValue($database));
    }

    /** @test */
    public function test_set_credentials_establece_valores_correctamente(): void
    {
        $database = new Database(false);
        $database->setCredentials('nuevo_host', 'nuevo_user', 'nueva_pass', 'nueva_db');
        
        $reflection = new \ReflectionClass($database);
        
        $hostProp = $reflection->getProperty('host');
        $hostProp->setAccessible(true);
        $this->assertEquals('nuevo_host', $hostProp->getValue($database));
    }

    /** @test */
    public function test_constructor_cuando_use_env_es_false(): void
    {
        $database = new Database(false);
        
        $reflection = new \ReflectionClass($database);
        
        $hostProp = $reflection->getProperty('host');
        $hostProp->setAccessible(true);
        $this->assertEquals('db', $hostProp->getValue($database));
        
        $userProp = $reflection->getProperty('user');
        $userProp->setAccessible(true);
        $this->assertEquals('root', $userProp->getValue($database));
    }

    /** @test */
    public function test_connect_usa_opciones_pdo_correctamente(): void
    {
        $database = new Database(false);
        $database->setCredentials('localhost', 'test_user', 'test_pass', 'test_db');

        try {
            $conn = $database->connect();
            
            $this->assertInstanceOf(\PDO::class, $conn);
            
            $stmt = $conn->query("SHOW VARIABLES LIKE 'character_set_client'");
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->assertEquals('utf8', $result['Value']);
            
        } catch (DatabaseException $e) {
            $this->markTestSkipped('No se pudo conectar a la base de datos de prueba');
        }
    }

    /** @test */
    public function test_connect_con_diferentes_opciones_pdo(): void
    {
        $database = new Database(false);
        $database->setCredentials('localhost', 'test_user', 'test_pass', 'test_db');

        try {
            $conn = $database->connect();
            
            $this->assertInstanceOf(\PDO::class, $conn);
            
            $this->assertEquals(
                \PDO::ERRMODE_EXCEPTION, 
                $conn->getAttribute(\PDO::ATTR_ERRMODE)
            );
            
            $stmt = $conn->query("SHOW VARIABLES LIKE 'character_set_client'");
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->assertEquals('utf8', $result['Value']);
            
        } catch (DatabaseException $e) {
            $this->markTestSkipped('No se pudo conectar a la base de datos de prueba');
        }
    }
} 