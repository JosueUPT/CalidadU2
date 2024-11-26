<?php
namespace Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Config\Database;
use Exceptions\DatabaseException;

class DatabaseTest extends TestCase
{
    private $envBackup;

    protected function setUp(): void
    {
        $this->envBackup = [
            'DB_HOST' => getenv('DB_HOST'),
            'DB_USER' => getenv('DB_USER'),
            'DB_PASSWORD' => getenv('DB_PASSWORD'),
            'DB_NAME' => getenv('DB_NAME')
        ];
    }

    protected function tearDown(): void
    {
        foreach ($this->envBackup as $key => $value) {
            if ($value === false) {
                putenv($key);
            } else {
                putenv("$key=$value");
            }
        }
    }

    /** @test */
    public function verifica_valores_por_defecto(): void
    {
        putenv('DB_HOST');
        putenv('DB_USER');
        putenv('DB_PASSWORD');
        putenv('DB_NAME');

        $database = new Database();
        
        $reflection = new \ReflectionClass($database);
        $hostProperty = $reflection->getProperty('host');
        $hostProperty->setAccessible(true);
        
        $this->assertEquals('db', $hostProperty->getValue($database));
    }

    /** @test */
    public function verifica_carga_variables_entorno(): void
    {
        putenv('DB_HOST=test_host');
        putenv('DB_USER=test_user');
        putenv('DB_PASSWORD=test_pass');
        putenv('DB_NAME=test_db');

        $database = new Database();
        
        $reflection = new \ReflectionClass($database);
        $hostProperty = $reflection->getProperty('host');
        $hostProperty->setAccessible(true);
        
        $this->assertEquals('test_host', $hostProperty->getValue($database));
    }

    /** @test */
    public function verifica_carga_env_file(): void
    {
        $envContent = "DB_HOST=localhost\n" .
                     "DB_USER=test_user\n" .
                     "DB_PASSWORD=test_pass\n" .
                     "DB_NAME=test_db";
        
        $envPath = __DIR__ . '/../../../.env';
        file_put_contents($envPath, $envContent);

        $database = new Database();
        
        $reflection = new \ReflectionClass($database);
        $hostProperty = $reflection->getProperty('host');
        $hostProperty->setAccessible(true);
        
        $this->assertEquals('localhost', $hostProperty->getValue($database));

        unlink($envPath);
    }
} 