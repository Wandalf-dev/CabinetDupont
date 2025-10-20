<?php

// Détection automatique de l'environnement
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || 
            strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false ||
            strpos($_SERVER['SERVER_NAME'], '.local') !== false);

if ($isLocal) {
    // Configuration XAMPP Local
    return [
        'host' => 'localhost',
        'dbname' => 'bdd_dupont',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
} else {
    // Configuration InfinityFree
    return [
        'host' => 'sql210.infinityfree.com',
        'dbname' => 'if0_40207543_bdd_dupont',
        'username' => 'if0_40207543',
        'password' => 'Q8fzhdFdbU4KtEZ',
        'charset' => 'utf8mb4'
    ];
}