<?php

namespace App\Controllers;
use DateTime;

use App\Models\ActuModel;
use App\Models\ServiceModel;
use App\Models\HoraireModel;
use App\Models\PatientModel;
use App\Models\CreneauModel;

// Contrôleur pour le panneau d'administration du cabinet
class AdminController extends \App\Core\Controller {
    // Propriétés pour accéder aux modèles des actualités, services et horaires
    private $actuModel;
    private $serviceModel;
    private $horaireModel;
    private $patientModel;
    private $creneauModel;

    // Constructeur : instancie les modèles nécessaires
    public function __construct() {
        parent::__construct();
        $this->actuModel = new ActuModel();
        $this->serviceModel = new ServiceModel();
        $this->horaireModel = new HoraireModel();
        $this->patientModel = new PatientModel();
        $this->creneauModel = new CreneauModel();
    }

    // === Helpers internes ===
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

    private function checkAdminRights(): bool {
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $this->jsonResponse(['success' => false, 'error' => 'Accès non autorisé'], 403);
            return false; // unreachable, mais clair
        }
        return true;
    }

    // Méthode principale pour afficher le panneau d'administration
    public function index() {
        // Debug

        // Vérifie que l'utilisateur est bien un administrateur (médecin ou secrétaire)
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        // Récupérations
        $actusAdmin    = $this->actuModel->getAllActusAdmin();
        $servicesAdmin = $this->serviceModel->getAllServicesAdmin();
        $horaires      = $this->horaireModel->getHoraires();
        $patientsAdmin = $this->patientModel->getAllPatientsAdmin();

        // Créneaux du jour
        $date = date('Y-m-d');
        $creneaux = $this->creneauModel->getCreneauxPourDate(1, $date);

        // Formatage
        foreach ($creneaux as &$creneau) {
            $creneau['debut'] = date('H:i', strtotime($creneau['debut']));
            $creneau['est_reserve'] = (bool)$creneau['est_reserve'] || ($creneau['statut'] === 'indisponible');
            $creneau['est_indisponible'] = $creneau['statut'] === 'indisponible';
        }

        // Token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = \App\Core\Csrf::generateToken();
        }

        require_once 'app/views/admin-combined.php';
    }

    // Méthodes Patients (inchangé sauf style)
    public function addPatient() {
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validatePatientData($_POST);
            if (empty($data['errors'])) {
                if ($this->patientModel->emailExists($data['email'])) {
                    $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
                } else {
                    if ($this->patientModel->createPatient($data)) {
                        $_SESSION['success'] = "Le patient a été ajouté avec succès.";
                        header('Location: index.php?page=admin');
                        exit();
                    } else {
                        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout du patient.";
                    }
                }
            } else {
                $_SESSION['error'] = implode("<br>", $data['errors']);
            }
        }
        require_once 'app/views/patient/patient-create.php';
    }

    public function editPatient() {
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $patient = $this->patientModel->getPatientById($id);

        if (!$patient) {
            $_SESSION['error'] = "Patient non trouvé.";
            header('Location: index.php?page=admin');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validatePatientData($_POST, true);
            if (empty($data['errors'])) {
                if ($this->patientModel->emailExists($data['email'], $id)) {
                    $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
                } else {
                    if ($this->patientModel->updatePatient($id, $data)) {
                        $_SESSION['success'] = "Les informations du patient ont été mises à jour.";
                        header('Location: index.php?page=admin');
                        exit();
                    } else {
                        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour.";
                    }
                }
            } else {
                $_SESSION['error'] = implode("<br>", $data['errors']);
            }
        }

        require_once 'app/views/patient/patient-update.php';
    }

    public function deletePatient() {
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($this->patientModel->deletePatient($id)) {
            $_SESSION['success'] = "Le patient a été supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression.";
        }

        header('Location: index.php?page=admin');
        exit();
    }

    // =======================
    //       CRÉNEAUX
    // =======================

    public function getAllCreneaux() {
        if (!$this->checkAdminRights()) return;

        try {
            $creneaux = $this->creneauModel->getAllCreneaux();
            foreach ($creneaux as &$creneau) {
                $creneau['debut_format'] = date('d/m/Y H:i', strtotime($creneau['debut']));
                $creneau['fin_format']   = date('H:i', strtotime($creneau['fin']));
                $creneau['est_indisponible'] = $creneau['statut'] === 'indisponible';
            }
            $this->jsonResponse(['success' => true, 'creneaux' => $creneaux], 200);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la récupération des créneaux'], 500);
        }
    }

    public function loadCreneaux() {
        if (!$this->checkAdminRights()) return;

        $date = $_GET['date'] ?? date('Y-m-d');
        try {
            $creneaux = $this->creneauModel->getCreneauxPourDate(1, $date);
            require_once 'app/views/admin/sections/creneaux-section.php';
            // (vue partielle HTML retournée)
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => 'Erreur lors du chargement des créneaux'], 500);
        }
    }

    public function toggleIndisponible() {
        if (!$this->checkAdminRights()) return;

        // Lecture JSON ou POST
        $data = $this->jsonInput();
        $id   = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de créneau invalide'], 400);
        }

        try {
            $result  = $this->creneauModel->toggleIndisponible($id);
            $creneau = $this->creneauModel->getCreneauById($id);

            $this->jsonResponse([
                'success'         => $result !== false,
                'estIndisponible' => isset($creneau['statut']) && $creneau['statut'] === 'indisponible',
                'message'         => $result ? 'Statut mis à jour' : 'Erreur lors de la mise à jour'
            ], $result !== false ? 200 : 400);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la mise à jour du statut'], 500);
        }
    }

    public function genererCreneaux() {
        if (!$this->checkAdminRights()) return;

        // Le front envoie { date_debut, date_fin } (snake_case)
        $data      = $this->jsonInput();
        $dateDebut = $data['date_debut'] ?? ($_POST['date_debut'] ?? null);
        $dateFin   = $data['date_fin']   ?? ($_POST['date_fin']   ?? null);
        $agendaId  = isset($data['agendaId']) ? (int)$data['agendaId'] : 1;

        if (empty($dateDebut) || empty($dateFin)) {
            $this->jsonResponse(['success' => false, 'error' => 'Dates invalides'], 400);
        }

        try {
            $result = $this->creneauModel->genererCreneaux($agendaId, $dateDebut, $dateFin);
            $this->jsonResponse([
                'success' => (bool)$result,
                'message' => $result ? 'Créneaux générés avec succès' : 'Erreur lors de la génération'
            ], $result ? 200 : 500);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la génération des créneaux'], 500);
        }
    }

    public function getCreneauxPourDate() {
        if (!$this->checkAdminRights()) return;

        $date     = $_GET['date'] ?? date('Y-m-d');
        $agendaId = isset($_GET['agendaId']) ? (int)$_GET['agendaId'] : 1;

        try {
            $creneaux = $this->creneauModel->getCreneauxPourDate($agendaId, $date);
            foreach ($creneaux as &$creneau) {
                $creneau['debut_format'] = date('H:i', strtotime($creneau['debut']));
                $creneau['fin_format']   = date('H:i', strtotime($creneau['fin']));
                $creneau['est_indisponible'] = $creneau['statut'] === 'indisponible';
            }
            $this->jsonResponse(['success' => true, 'creneaux' => $creneaux], 200);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la récupération des créneaux'], 500);
        }
    }

    public function cleanupInconsistentSlots() {
        if (!$this->checkAdminRights()) return;

        try {
            $result = $this->creneauModel->cleanupInconsistentSlots();
            $this->jsonResponse([
                'success' => (bool)$result,
                'message' => $result ? 'Nettoyage effectué avec succès' : 'Erreur lors du nettoyage'
            ], $result ? 200 : 500);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors du nettoyage des créneaux'], 500);
        }
    }

    public function deleteCreneau() {
        if (!$this->checkAdminRights()) return;

        $data = $this->jsonInput();
        $id   = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de créneau invalide'], 400);
        }

        try {
            $result = $this->creneauModel->deleteCreneau($id);
            $this->jsonResponse([
                'success' => (bool)$result,
                'message' => $result ? 'Créneau supprimé' : 'Créneau introuvable ou non supprimé'
            ], $result ? 200 : 400);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
        }
    }

    public function markUnavailableBulk() {
        if (!$this->checkAdminRights()) return;

        $data = $this->jsonInput();
        $ids  = $data['ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            $this->jsonResponse(['success' => false, 'error' => 'Aucun créneau sélectionné'], 400);
        }

        try {
            $success = true;
            foreach ($ids as $id) {
                if (!$this->creneauModel->toggleIndisponible((int)$id)) {
                    $success = false;
                    break;
                }
            }
            $this->jsonResponse(['success' => $success], $success ? 200 : 500);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la modification des créneaux'], 500);
        }
    }

    public function deleteCreneauxBulk() {
        if (!$this->checkAdminRights()) return;

        $data = $this->jsonInput();
        $ids  = $data['ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            $this->jsonResponse(['success' => false, 'error' => 'Aucun créneau sélectionné'], 400);
        }

        try {
            $success = true;
            foreach ($ids as $id) {
                if (!$this->creneauModel->deleteCreneau((int)$id)) {
                    $success = false;
                    break;
                }
            }
            $this->jsonResponse(['success' => $success], $success ? 200 : 500);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la suppression des créneaux'], 500);
        }
    }

    private function validatePatientData($data, $isUpdate = false) {
        $errors = [];
        $validated = [];

        if (empty($data['nom'])) {
            $errors[] = "Le nom est obligatoire.";
        } else {
            $validated['nom'] = trim($data['nom']);
        }

        if (empty($data['prenom'])) {
            $errors[] = "Le prénom est obligatoire.";
        } else {
            $validated['prenom'] = trim($data['prenom']);
        }

        if (empty($data['email'])) {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } else {
            $validated['email'] = trim($data['email']);
        }

        if (empty($data['telephone'])) {
            $errors[] = "Le numéro de téléphone est obligatoire.";
        } else {
            $validated['telephone'] = trim($data['telephone']);
        }

        if (empty($data['date_naissance'])) {
            $errors[] = "La date de naissance est obligatoire.";
        } else {
            $date = trim($data['date_naissance']);
            $finalDate = null;
            $dateObject = null;
            
            // Essayer le format dd/mm/yyyy (saisie manuelle ou formaté par JS)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                $dt = DateTime::createFromFormat('d/m/Y', $date);
                if ($dt && $dt->format('d/m/Y') === $date) {
                    $dateObject = $dt;
                    $finalDate = $dt->format('Y-m-d');
                }
            }
            // Essayer le format yyyy-mm-dd (Flatpickr ou format MySQL)
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $dt = DateTime::createFromFormat('Y-m-d', $date);
                if ($dt && $dt->format('Y-m-d') === $date) {
                    $dateObject = $dt;
                    $finalDate = $date; // Déjà au bon format
                }
            }
            
            // Si aucun format valide
            if ($finalDate === null) {
                $errors[] = "La date de naissance n'est pas au bon format (attendu : jj/mm/aaaa ou aaaa-mm-jj).";
            } else {
                // Validation de l'âge (entre 3 et 120 ans)
                $today = new DateTime();
                $age = $today->diff($dateObject)->y;
                
                if ($age < 3) {
                    $errors[] = "Le patient doit avoir au moins 3 ans.";
                } elseif ($age > 120) {
                    $errors[] = "La date de naissance ne peut pas dépasser 120 ans.";
                } elseif ($dateObject > $today) {
                    $errors[] = "La date de naissance ne peut pas être dans le futur.";
                } else {
                    $validated['date_naissance'] = $finalDate;
                }
            }
        }

        if (!$isUpdate && empty($data['password'])) {
            $errors[] = "Le mot de passe est obligatoire.";
        } elseif (!empty($data['password'])) {
            // Validation de la force du mot de passe
            $passwordValidation = \App\Core\Security::validatePasswordStrength($data['password']);
            if (!$passwordValidation['valid']) {
                $errors[] = "Mot de passe trop faible. Il doit contenir : au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (!@#$%^&*...).";
            } else {
                $validated['password'] = $data['password'];
            }
        }

        $validated['errors'] = $errors;
        return $validated;
    }
}
