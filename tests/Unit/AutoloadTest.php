<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AutoloadTest extends TestCase
{
    public function test_puede_cargar_clase_existente(): void
    {
        // Intentar cargar una clase que sabemos que existe
        $className = 'Exceptions\DatabaseException';
        
        // Verificar que la clase no está cargada inicialmente
        $this->assertFalse(class_exists($className, false));
        
        // Intentar cargar la clase
        $this->assertTrue(class_exists($className, true));
    }

    public function test_maneja_clase_inexistente(): void
    {
        // Intentar cargar una clase que no existe
        $className = 'Exceptions\ClaseQueNoExiste';
        
        // Verificar que retorna false para clases que no existen
        $this->assertFalse(class_exists($className, true));
    }
} 