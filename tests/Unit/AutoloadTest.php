<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AutoloadTest extends TestCase
{
    public function test_puede_cargar_clase_existente(): void
    {
        // Cargar el autoloader
        require_once __DIR__ . '/../../src/autoload.php';
        
        // Intentar cargar una clase que sabemos que existe
        $className = 'Models\Cart';
        
        // Verificar que la clase se puede cargar correctamente
        $result = class_exists($className, true);
        
        $this->assertTrue($result, 'El autoloader debería poder cargar la clase Models\Cart');
    }

    public function test_maneja_clase_inexistente(): void
    {
        $className = 'Exceptions\ClaseQueNoExiste';
        
        $this->assertFalse(class_exists($className, true));
    }
} 