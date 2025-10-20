<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chargement de la configuration
require_once __DIR__ . '/config/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    // Conversion du namespace en chemin de fichier
    // App\Core\App devient app/core/App.php
    $classPath = str_replace('\\', '/', $class);
    
    // Séparer les parties du namespace
    $parts = explode('/', $classPath);
    
    // Le premier élément (App) devient 'app' en minuscule
    if (isset($parts[0]) && $parts[0] === 'App') {
        $parts[0] = 'app';
    }
    
    // Le deuxième élément (Core, Controllers, Models) devient minuscule
    if (isset($parts[1])) {
        $parts[1] = strtolower($parts[1]);
    }
    
    // Reconstituer le chemin
    $path = __DIR__ . '/' . implode('/', $parts) . '.php';
    
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Core\App;

// Démarrage de l'application
$app = new App();
$app->run();