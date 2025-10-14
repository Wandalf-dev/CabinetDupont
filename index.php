<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chargement de la configuration
require_once __DIR__ . '/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    // Conversion du namespace en chemin de fichier
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Core\App;

// Démarrage de l'application
$app = new App();
$app->run();