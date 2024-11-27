<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AutoloadTest extends TestCase
{
    public function test_puede_cargar_clase_existente(): void
    {
        $className = 'Exceptions\DatabaseException';
        
        $this->assertFalse(class_exists($className, false));
        
        $this->assertTrue(class_exists($className, true));
    }

    public function test_maneja_clase_inexistente(): void
    {
        $className = 'Exceptions\ClaseQueNoExiste';
        
        $this->assertFalse(class_exists($className, true));
    }
} 