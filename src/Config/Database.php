<?php

namespace Config;

use Exceptions\DatabaseException;

class Database {
    private $host;
    private $user;
    private $password;
    private $database;

    public function __construct(bool $useEnv = true) {
        if (!$useEnv) {
            $this->setDefaultCredentials();
            return;
        }

        $envPath = __DIR__ . '/../../.env';
        
        if (file_exists($envPath)) {
            $env = parse_ini_file($envPath);
            if ($env !== false) {
                $this->host = $env['DB_HOST'];
                $this->user = $env['DB_USER'];
                $this->password = $env['DB_PASSWORD'];
                $this->database = $env['DB_NAME'];
                return;
            }
        }

        $this->setDefaultCredentials();
    }

    private function setDefaultCredentials(): void
    {
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '123456';
        $this->database = getenv('DB_NAME') ?: 'tienda_bd';
    }

    public function setCredentials(string $host, string $user, string $password, string $database): void
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    public function connect() {
        try {
            $conn = new \PDO(
                "mysql:host={$this->host};dbname={$this->database}",
                $this->user,
                $this->password,
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(\PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new DatabaseException(
                "Error de conexión a la base de datos: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
} 