<?php
require_once __DIR__ . '/../../models/database.php';
require_once __DIR__ . '/../../models/RendezVousModel.php';

use App\Models\RendezVousModel;

// Autoriser les requêtes AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Vérifie si la requête est de type POST et si c'est une requête AJAX
$requestData = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($requestData['action'])) {
    $rendezVousModel = new RendezVousModel();
    
    switch ($requestData['action']) {
        case 'cancel':
            if (isset($requestData['id'])) {
                try {
                    // Mettre à jour le statut du rendez-vous à "annulé"
                    $success = $rendezVousModel->updateStatus($requestData['id'], 'annulé');
                    
                    if ($success) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Le rendez-vous a été annulé avec succès.'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Impossible d\'annuler le rendez-vous.'
                        ]);
                    }
                } catch (\Exception $e) {
                    error_log("Erreur lors de l'annulation du rendez-vous: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Une erreur est survenue : ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID du rendez-vous non fourni.'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action non reconnue.'
            ]);
            break;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requête invalide.'
    ]);
}
exit;