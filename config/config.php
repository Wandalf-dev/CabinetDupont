<?php
/**
 * Fichier de configuration principal
 * Cabinet Dupont
 */

// Démarrage de la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir la timezone
date_default_timezone_set('Europe/Paris');

// Configuration de l'environnement
define('ENVIRONMENT', 'production');

// Chemins de base
define('BASE_PATH', __DIR__ . '/..');
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// URL de base (détection automatique)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Détection de l'environnement (local vs production)
$isLocal = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false || strpos($host, '.local') !== false);

if ($isLocal) {
    // En local (XAMPP)
    define('BASE_URL', $protocol . '://' . $host . '/cabinetdupont-1');
} else {
    // En production (InfinityFree)
    define('BASE_URL', $protocol . '://' . $host);
}

// Configuration des erreurs selon l'environnement
if ($isLocal) {
    // Environnement LOCAL : Afficher les erreurs
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Environnement PRODUCTION : Masquer les erreurs
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/error.log');
}

// Chargement de la configuration de la base de données
$dbConfig = require APP_PATH . '/config/database.php';

// Rendre la config DB accessible globalement (si nécessaire)
define('DB_HOST', $dbConfig['host']);
define('DB_NAME', $dbConfig['dbname']);
define('DB_USER', $dbConfig['username']);
define('DB_PASS', $dbConfig['password']);
define('DB_CHARSET', $dbConfig['charset']);

// Configuration de sécurité
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 heure

// Configuration des uploads
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);

// Autres configurations
define('SITE_NAME', 'Cabinet Dupont');
define('SITE_EMAIL', 'contact@cabinetdupont.fr');
define('ITEMS_PER_PAGE', 10);
