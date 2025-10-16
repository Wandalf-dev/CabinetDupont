<?php

require_once __DIR__ . '/../../models/database.php';
global $pdo;
if (!$pdo) {
    // À adapter selon ta config, exemple :
    $pdo = new PDO('mysql:host=localhost;dbname=bdd_dupont;charset=utf8', 'root', '');
}

header('Content-Type: application/json');

// Récupère les horaires du cabinet pour tous les jours
try {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM horaire_cabinet WHERE cabinet_id = 1');
    $horaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'horaires' => $horaires]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
