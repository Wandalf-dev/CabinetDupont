<?php
namespace App\Controllers;

use App\Models\CreneauModel;
use App\Models\AgendaModel;

class CreneauxController {
    private $creneauModel;
    private $agendaModel;

    public function __construct() {
        $this->creneauModel = new CreneauModel();
        $this->agendaModel  = new AgendaModel();
    }

    private function jsonInput(): array {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $data = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $data;
            }
        }
        return $_POST ?? [];
    }

    private function jsonResponse(array $payload, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit();
    }

    public function toggleIndisponible() {
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->jsonResponse(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        // CSRF check - vérifier plusieurs sources pour le token
        $token = '';
        
        // 1. Essayer getallheaders() si disponible
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? '';
        }
        
        // 2. Fallback: vérifier $_SERVER
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        // 3. Dernière option: vérifier le POST
        if (empty($token)) {
            $data = $this->jsonInput();
            $token = $data['csrf_token'] ?? '';
        }
        
        if (empty($token) || !\App\Core\Csrf::checkToken($token)) {
            $this->jsonResponse(['success' => false, 'error' => 'Token CSRF invalide ou manquant'], 403);
        }

        $data = $this->jsonInput();
        $id   = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de créneau invalide'], 400);
        }

        try {
            $nouveauStatut = $this->creneauModel->toggleIndisponible($id);

            if ($nouveauStatut === true) {
                $response = [
                    'success' => true,
                    'message' => 'Le créneau a été marqué comme indisponible.',
                    'estIndisponible' => true
                ];
                $this->jsonResponse($response, 200);
            } elseif ($nouveauStatut === false) {
                $response = [
                    'success' => true,
                    'message' => 'Le créneau a été rendu disponible.',
                    'estIndisponible' => false
                ];
                $this->jsonResponse($response, 200);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Impossible de modifier le statut du créneau'], 400);
            }
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function markUnavailableBulk() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->jsonResponse(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        // CSRF check - vérifier plusieurs sources pour le token
        $token = '';
        
        // 1. Essayer getallheaders() si disponible
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? '';
        }
        
        // 2. Fallback: vérifier $_SERVER
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        // 3. Dernière option: vérifier le POST
        if (empty($token)) {
            $data = $this->jsonInput();
            $token = $data['csrf_token'] ?? '';
        }
        
        if (empty($token) || !\App\Core\Csrf::checkToken($token)) {
            $this->jsonResponse(['success' => false, 'error' => 'Token CSRF invalide ou manquant'], 403);
        }

        $data = $this->jsonInput();
        if (!isset($data['ids']) || !is_array($data['ids'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Données invalides'], 400);
        }

        $creneauxIds = array_map('intval', $data['ids']);
        if (empty($creneauxIds)) {
            $this->jsonResponse(['success' => false, 'error' => 'Aucun créneau sélectionné'], 400);
        }

        $modifies = 0;
        $erreurs = [];

        foreach ($creneauxIds as $id) {
            try {
                $creneau = $this->creneauModel->getCreneauById($id);
                if (!$creneau) {
                    $erreurs[] = "Créneau $id introuvable";
                    continue;
                }
                if (!empty($creneau['est_reserve'])) {
                    $erreurs[] = "Créneau $id est déjà réservé";
                    continue;
                }
                
                // Vérifier si le créneau est déjà indisponible
                $isIndisponible = ($creneau['statut'] ?? '') === 'indisponible';
                
                // Si déjà indisponible, on compte quand même comme succès
                if ($isIndisponible) {
                    $modifies++;
                } else {
                    // Sinon, le basculer vers indisponible
                    $result = $this->creneauModel->toggleIndisponible($id);
                    if ($result === true) { // true signifie maintenant indisponible
                        $modifies++;
                    } else {
                        $erreurs[] = "Erreur lors de la modification du créneau $id";
                    }
                }
            } catch (\Exception $e) {
                $erreurs[] = "Erreur créneau $id: " . $e->getMessage();
            }
        }

        $response = [
            'success' => $modifies > 0,
            'message' => $modifies > 0 ? "$modifies créneau(x) marqué(s) comme indisponible(s)" : "Aucun créneau n'a pu être modifié",
            'modifiedCount' => $modifies
        ];
        if (!empty($erreurs)) {
            $response['errors'] = $erreurs;
        }

        $this->jsonResponse($response, 200);
    }

    public function markAvailableBulk() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->jsonResponse(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        // CSRF check - vérifier plusieurs sources pour le token
        $token = '';
        
        // 1. Essayer getallheaders() si disponible
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? '';
        }
        
        // 2. Fallback: vérifier $_SERVER
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        // 3. Dernière option: vérifier le POST
        if (empty($token)) {
            $data = $this->jsonInput();
            $token = $data['csrf_token'] ?? '';
        }
        
        if (empty($token) || !\App\Core\Csrf::checkToken($token)) {
            $this->jsonResponse(['success' => false, 'error' => 'Token CSRF invalide ou manquant'], 403);
        }

        $data = $this->jsonInput();
        if (!isset($data['ids']) || !is_array($data['ids'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Données invalides'], 400);
        }

        $creneauxIds = array_map('intval', $data['ids']);
        if (empty($creneauxIds)) {
            $this->jsonResponse(['success' => false, 'error' => 'Aucun créneau sélectionné'], 400);
        }

        $modifies = 0;
        $erreurs = [];

        foreach ($creneauxIds as $id) {
            try {
                $creneau = $this->creneauModel->getCreneauById($id);
                if (!$creneau) {
                    $erreurs[] = "Créneau $id introuvable";
                    continue;
                }
                
                // Vérifier si le créneau est indisponible
                $isIndisponible = ($creneau['statut'] ?? '') === 'indisponible';
                
                // Si déjà disponible (pas indisponible), on compte quand même comme succès
                if (!$isIndisponible) {
                    $modifies++;
                } else {
                    // Sinon, le basculer vers disponible
                    $result = $this->creneauModel->toggleIndisponible($id);
                    if ($result === false) { // false signifie maintenant disponible
                        $modifies++;
                    } else {
                        $erreurs[] = "Erreur lors de la modification du créneau $id";
                    }
                }
            } catch (\Exception $e) {
                $erreurs[] = "Erreur créneau $id: " . $e->getMessage();
            }
        }

        $response = [
            'success' => $modifies > 0,
            'message' => $modifies > 0 ? "$modifies créneau(x) marqué(s) comme disponible(s)" : "Aucun créneau n'a pu être modifié",
            'modifiedCount' => $modifies
        ];
        if (!empty($erreurs)) {
            $response['errors'] = $erreurs;
        }

        $this->jsonResponse($response, 200);
    }

    public function deleteCreneauxBulk() {
        // Alias pour deleteMultiple() pour compatibilité avec le JavaScript
        $this->deleteMultiple();
    }

    public function deleteMultiple() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->jsonResponse(['error' => 'Accès non autorisé'], 403);
        }

        // CSRF check - vérifier plusieurs sources pour le token
        $token = '';
        
        // 1. Essayer getallheaders() si disponible
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? '';
        }
        
        // 2. Fallback: vérifier $_SERVER
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        // 3. Dernière option: vérifier le POST
        if (empty($token)) {
            $data = $this->jsonInput();
            $token = $data['csrf_token'] ?? '';
        }
        
        if (empty($token) || !\App\Core\Csrf::checkToken($token)) {
            $this->jsonResponse(['success' => false, 'error' => 'Token CSRF invalide ou manquant'], 403);
        }

        $data = $this->jsonInput();
        if (!isset($data['ids']) || !is_array($data['ids'])) {
            $this->jsonResponse(['error' => 'Données invalides'], 400);
        }

        $creneauxIds = array_map('intval', $data['ids']);
        if (empty($creneauxIds)) {
            $this->jsonResponse(['error' => 'Aucun créneau sélectionné'], 400);
        }

        $supprimes = 0;
        $erreurs   = [];

        foreach ($creneauxIds as $id) {
            $creneau = $this->creneauModel->getCreneauById($id);
            if (!$creneau) {
                $erreurs[] = "Créneau $id introuvable";
                continue;
            }
            if (!empty($creneau['est_reserve'])) {
                $erreurs[] = "Créneau $id est déjà réservé";
                continue;
            }
            if ($this->creneauModel->deleteCreneau($id)) {
                $supprimes++;
            } else {
                $erreurs[] = "Erreur lors de la suppression du créneau $id";
            }
        }

        $response = [
            'success'      => $supprimes > 0,
            'message'      => $supprimes > 0 ? "$supprimes créneau(x) supprimé(s) avec succès" : "Aucun créneau n'a pu être supprimé",
            'deletedCount' => $supprimes
        ];
        if (!empty($erreurs)) $response['errors'] = $erreurs;

        $this->jsonResponse($response, 200);
    }

    public function delete() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            $creneau = $this->creneauModel->getCreneauById($id);
            if (!$creneau) {
                $_SESSION['error'] = "Ce créneau n'existe pas";
            } elseif (!empty($creneau['est_reserve'])) {
                $_SESSION['error'] = "Impossible de supprimer un créneau réservé";
            } else {
                if ($this->creneauModel->deleteCreneau($id)) {
                    $_SESSION['success'] = "Le créneau a été supprimé avec succès";
                } else {
                    $_SESSION['error'] = "Une erreur est survenue lors de la suppression du créneau";
                }
            }
        } else {
            $_SESSION['error'] = "ID de créneau invalide";
        }

        header('Location: index.php?page=admin');
        exit();
    }

    public function generer() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
            if (!$agenda) {
                $_SESSION['error'] = "Aucun agenda trouvé pour ce médecin";
                header('Location: index.php?page=admin');
                exit();
            }

            $dateDebut          = $_POST['date_debut'] ?? '';
            $dateFin            = $_POST['date_fin'] ?? '';
            $confirmedDateDebut = $_POST['confirmed_date_debut'] ?? '';
            $confirmedDateFin   = $_POST['confirmed_date_fin'] ?? '';

            if (empty($dateDebut) || empty($dateFin) || empty($confirmedDateDebut) || empty($confirmedDateFin)) {
                $_SESSION['error'] = "Les dates sont obligatoires";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            if ($dateDebut !== $confirmedDateDebut || $dateFin !== $confirmedDateFin) {
                $_SESSION['error'] = "Une erreur est survenue lors de la validation des dates";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            $creneauxExistants = $this->creneauModel->getCreneauxPourPeriode($dateDebut, $dateFin, $agenda['id']);
            if (!empty($creneauxExistants)) {
                $_SESSION['error'] = "Des créneaux existent déjà pour cette période. Veuillez d'abord les supprimer ou choisir une autre période.";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            $dateDebutObj = new \DateTime($dateDebut);
            $dateFinObj   = new \DateTime($dateFin);
            $interval     = $dateDebutObj->diff($dateFinObj);
            $nombreJours  = $interval->days + 1;
            $nombreCreneauxEstime = $nombreJours * 20;
            $_SESSION['info'] = "Vous allez générer environ {$nombreCreneauxEstime} créneaux.";

            try {
                $success = $this->creneauModel->genererCreneaux($agenda['id'], $dateDebut, $dateFin);
                $_SESSION[$success ? 'success' : 'error'] = $success
                    ? "Les créneaux ont été générés avec succès"
                    : "Une erreur est survenue lors de la génération des créneaux";
            } catch (\Exception $e) {
                $_SESSION['error'] = "Une erreur inattendue est survenue";
            }

            header('Location: index.php?page=admin');
            exit();
        }

        require_once 'app/views/creneaux/generer.php';
    }

    public function genererCreneaux() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->jsonResponse(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        // CSRF check - vérifier plusieurs sources pour le token
        $token = '';
        
        // 1. Essayer getallheaders() si disponible
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? '';
        }
        
        // 2. Fallback: vérifier $_SERVER
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        // 3. Dernière option: vérifier le POST
        if (empty($token)) {
            $data = $this->jsonInput();
            $token = $data['csrf_token'] ?? '';
        }
        
        if (empty($token) || !\App\Core\Csrf::checkToken($token)) {
            $this->jsonResponse(['success' => false, 'error' => 'Token CSRF invalide ou manquant'], 403);
        }

        $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
        if (!$agenda) {
            $this->jsonResponse(['success' => false, 'error' => 'Aucun agenda trouvé'], 404);
        }

        $data = $this->jsonInput();
        $dateDebut = $data['date_debut'] ?? '';
        $dateFin = $data['date_fin'] ?? '';

        if (empty($dateDebut) || empty($dateFin)) {
            $this->jsonResponse(['success' => false, 'error' => 'Les dates sont obligatoires'], 400);
        }

        // Vérifier que les dates sont valides
        $dateDebutObj = \DateTime::createFromFormat('Y-m-d', $dateDebut);
        $dateFinObj = \DateTime::createFromFormat('Y-m-d', $dateFin);
        
        if (!$dateDebutObj || !$dateFinObj) {
            $this->jsonResponse(['success' => false, 'error' => 'Format de date invalide'], 400);
        }

        if ($dateDebutObj > $dateFinObj) {
            $this->jsonResponse(['success' => false, 'error' => 'La date de début doit être antérieure à la date de fin'], 400);
        }

        // Vérifier s'il existe déjà des créneaux pour cette période
        $creneauxExistants = $this->creneauModel->getCreneauxPourPeriode($dateDebut, $dateFin, $agenda['id']);
        if (!empty($creneauxExistants)) {
            $this->jsonResponse([
                'success' => false, 
                'error' => 'Des créneaux existent déjà pour cette période. Veuillez d\'abord les supprimer ou choisir une autre période.'
            ], 400);
        }

        try {
            $success = $this->creneauModel->genererCreneaux($agenda['id'], $dateDebut, $dateFin);
            
            if ($success) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Les créneaux ont été générés avec succès'
                ], 200);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Une erreur est survenue lors de la génération des créneaux'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Une erreur inattendue est survenue : ' . $e->getMessage()
            ], 500);
        }
    }

    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
        if (!$agenda) {
            $_SESSION['error'] = "Aucun agenda trouvé";
            header('Location: index.php');
            exit();
        }

        $dateFiltre = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $creneaux   = $this->creneauModel->getCreneauxPourDate($agenda['id'], $dateFiltre);

        require_once 'app/views/admin/sections/creneaux-section.php';
    }

    public function loadCreneaux() {
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->jsonResponse(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
        if (!$agenda) {
            $this->jsonResponse(['success' => false, 'error' => 'Aucun agenda trouvé'], 404);
        }

        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

        try {
            $creneaux = $this->creneauModel->getCreneauxPourDate($agenda['id'], $date);
            
            ob_start();
            require_once 'app/views/admin/sections/creneaux-section.php';
            $html = ob_get_clean();

            $this->jsonResponse([
                'success' => true,
                'html' => $html
            ], 200);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false, 
                'error' => 'Erreur lors du chargement des créneaux'
            ], 500);
        }
    }

    public function liste() {
        header('Location: index.php?page=creneaux');
        exit();
    }
}
