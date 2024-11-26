<?php
spl_autoload_register(function ($class) {
    // Convertir namespace a ruta de archivo
    $base_dir = __DIR__ . '/';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
}); 