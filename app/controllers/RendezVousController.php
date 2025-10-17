<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ServiceModel;
use App\Models\CreneauModel;
use App\Models\PatientModel;
use App\Models\RendezVousModel;

class RendezVousController extends Controller {
    private $serviceModel;
    private $creneauModel;
    private $patientModel;
    private $rendezVousModel;

    public function __construct() {
        parent::__construct();
        $this->serviceModel = new ServiceModel();
        $this->creneauModel = new CreneauModel();
        $this->patientModel = new PatientModel();
        $this->rendezVousModel = new RendezVousModel();
    }

    public function index() {
        // Redirige vers selectConsultation par défaut
        $this->selectConsultation();
    }

    public function selectConsultation() {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = "Vous devez être connecté pour prendre un rendez-vous.";
            header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
            exit;
        }

        // Récupère tous les services pour afficher les motifs de consultation
        $services = $this->serviceModel->getAllServices();
        
        // Affiche la vue avec les services
        $this->view('rendezvous/select-consultation', [
            'services' => $services
        ]);
    }

    public function selectDate() {
        error_log("=== Début méthode selectDate() ===");
        error_log("GET params reçus : " . print_r($_GET, true));
        error_log("Session user_id : " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'non défini'));
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour prendre un rendez-vous.";
            header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
            exit;
        }

        if (!isset($_GET['service_id'])) {
            error_log("service_id manquant");
            $_SESSION['error'] = "Veuillez sélectionner un service.";
            header('Location: ' . BASE_URL . '/index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        $serviceId = (int)$_GET['service_id'];
        error_log("Service ID reçu : " . $serviceId);

        // Récupération du service
        $service = $this->serviceModel->getServiceById($serviceId);
        if (!$service) {
            $_SESSION['error'] = "Le service demandé n'existe pas.";
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        // Récupération des dates disponibles (tous services confondus)
        $datesDisponibles = $this->creneauModel->getDatesDisponibles();
        error_log("Dates disponibles trouvées : " . print_r($datesDisponibles, true));
        error_log("Nombre de dates disponibles : " . count($datesDisponibles));

        if (empty($datesDisponibles)) {
            $_SESSION['error'] = "Aucun créneau disponible actuellement.";
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        $this->view('rendezvous/select-date', [
            'service' => $service,
            'datesDisponibles' => $datesDisponibles
        ]);
    }

    public function selectTime() {
        $logFile = __DIR__ . '/../../debug_rdv.log';
        file_put_contents($logFile, "\n=== " . date('Y-m-d H:i:s') . " - Début selectTime() ===\n", FILE_APPEND);
        file_put_contents($logFile, "GET params: " . print_r($_GET, true) . "\n", FILE_APPEND);
        file_put_contents($logFile, "Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

        // Vérifier la session
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour prendre un rendez-vous.";
            header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
            exit;
        }

        // Vérifier les paramètres requis
        if (!isset($_GET['service_id']) || !isset($_GET['date'])) {
            $_SESSION['error'] = "Des informations sont manquantes pour la sélection de l'horaire.";
            header('Location: ' . BASE_URL . '/index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        // S'assurer que la date est dans le bon fuseau horaire
        date_default_timezone_set('Europe/Paris');
        
        $serviceId = (int)$_GET['service_id'];
        $date = $_GET['date'];
        
        // Valider le format de la date
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            $_SESSION['error'] = "Format de date invalide.";
            header('Location: ' . BASE_URL . '/index.php?page=rendezvous&action=selectConsultation');
            exit;
        }
        
        try {
            // Convertir la date dans le fuseau horaire correct
            $dateObj = new \DateTime($date, new \DateTimeZone('Europe/Paris'));
            $date = $dateObj->format('Y-m-d');
            
            // Vérifier que la date n'est pas dans le passé
            $today = new \DateTime('today', new \DateTimeZone('Europe/Paris'));
            if ($dateObj < $today) {
                $_SESSION['error'] = "Impossible de prendre un rendez-vous à une date passée.";
                header('Location: ' . BASE_URL . '/index.php?page=rendezvous&action=selectDate&service_id=' . $serviceId);
                exit;
            }

            // Récupérer le service et sa durée
            $service = $this->serviceModel->getServiceById($serviceId);
            if (!$service) {
                throw new \Exception("Le service demandé n'existe pas.");
            }

            file_put_contents($logFile, "Récupération des créneaux disponibles pour la date " . $date . " et le service " . $serviceId . "\n", FILE_APPEND);
            // Récupérer les créneaux disponibles
            $availableSlots = $this->creneauModel->getAvailableSlots($date, $serviceId);
            file_put_contents($logFile, "Créneaux disponibles trouvés : " . print_r($availableSlots, true) . "\n", FILE_APPEND);
            
            // Afficher la vue
            $this->view('rendezvous/select-time', [
                'service' => $service,
                'date' => $date,
                'availableSlots' => $availableSlots
            ]);
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/index.php?page=rendezvous&action=selectDate&service_id=' . $serviceId);
            exit;
        }
    }

    public function confirmation() {
        $logFile = __DIR__ . '/../../debug_rdv.log';
        file_put_contents($logFile, "\n=== " . date('Y-m-d H:i:s') . " - Début confirmation() ===\n", FILE_APPEND);
        try {
            // Vérification des paramètres requis
            if (!isset($_SESSION['user_id'])) {
                file_put_contents($logFile, "Erreur: Utilisateur non connecté\n", FILE_APPEND);
                throw new \Exception("Vous devez être connecté pour prendre un rendez-vous.");
            }
            if (!isset($_GET['creneau_id'])) {
                file_put_contents($logFile, "Erreur: Créneau non sélectionné\n", FILE_APPEND);
                throw new \Exception("Aucun créneau sélectionné.");
            }
            if (!isset($_GET['service_id'])) {
                file_put_contents($logFile, "Erreur: Service non sélectionné\n", FILE_APPEND);
                throw new \Exception("Aucun service sélectionné.");
            }
            file_put_contents($logFile, "GET params: " . print_r($_GET, true) . "\n", FILE_APPEND);
            file_put_contents($logFile, "Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

            $creneauId = (int)$_GET['creneau_id'];
            $serviceId = (int)$_GET['service_id'];
            $userId = $_SESSION['user_id'];
            
            error_log("Paramètres reçus - creneauId: $creneauId, serviceId: $serviceId, userId: $userId");

            // Récupération et vérification du créneau
            $creneau = $this->creneauModel->getCreneauById($creneauId);
            if (!$creneau) {
                throw new \Exception("Le créneau sélectionné n'existe pas.");
            }
            error_log("Créneau trouvé : " . print_r($creneau, true));

            // Vérification que le créneau n'est pas déjà réservé
            if ($creneau['est_reserve']) {
                throw new \Exception("Ce créneau n'est plus disponible.");
            }

            // Récupération et vérification du service
            $service = $this->serviceModel->getServiceById($serviceId);
            if (!$service) {
                throw new \Exception("Le service demandé n'existe plus.");
            }
            error_log("Service trouvé : " . print_r($service, true));

            // Récupération du profil patient
            $patient = $this->patientModel->getPatientByUserId($userId);
            if (!$patient) {
                error_log("Patient non trouvé pour l'user_id: $userId");
                throw new \Exception("Profil patient introuvable.");
            }

            error_log("Patient trouvé : " . print_r($patient, true));

            // Affiche la vue de confirmation
            $this->view('rendezvous/confirmation-rdv', [
                'creneau' => $creneau,
                'service' => $service,
                'patient' => $patient
            ]);

        } catch (\Exception $e) {
            error_log("ERREUR dans confirmation() : " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }
    }

    public function confirmer() {
        error_log("=== Début méthode confirmer() ===");
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception("Vous devez être connecté pour confirmer un rendez-vous.");
            }

            // Vérifier la présence des données POST
            error_log("POST data: " . print_r($_POST, true));
            
            if (!isset($_POST['creneau_id']) || !isset($_POST['service_id'])) {
                error_log("Données POST manquantes - creneau_id ou service_id non défini");
                throw new \Exception("Informations manquantes pour la confirmation.");
            }

            $creneauId = (int)$_POST['creneau_id'];
            $serviceId = (int)$_POST['service_id'];
            $userId = $_SESSION['user_id'];

            error_log("Données extraites - creneauId: $creneauId, serviceId: $serviceId, userId: $userId");

            // Récupérer le patient
            error_log("Recherche du patient pour userId: " . $userId);
            $patient = $this->patientModel->getPatientByUserId($userId);
            if (!$patient) {
                error_log("Patient non trouvé pour userId: " . $userId);
                throw new \Exception("Profil patient introuvable.");
            }
            error_log("Patient trouvé: " . print_r($patient, true));

            // Vérifier que le créneau est toujours disponible
            error_log("Vérification du créneau: " . $creneauId);
            $creneau = $this->creneauModel->getCreneauById($creneauId);
            if (!$creneau) {
                error_log("Créneau non trouvé: " . $creneauId);
                throw new \Exception("Ce créneau n'existe pas.");
            }
            if ($creneau['est_reserve']) {
                error_log("Créneau déjà réservé: " . $creneauId);
                throw new \Exception("Ce créneau n'est plus disponible.");
            }
            error_log("Créneau disponible: " . print_r($creneau, true));

            // Vérifier le service
            error_log("Vérification du service: " . $serviceId);
            $service = $this->serviceModel->getServiceById($serviceId);
            if (!$service) {
                error_log("Service non trouvé: " . $serviceId);
                throw new \Exception("Le service demandé n'existe pas.");
            }
            error_log("Service trouvé: " . print_r($service, true));

            // Créer le rendez-vous
            error_log("Tentative de création du rendez-vous avec - creneauId: $creneauId, serviceId: $serviceId, patientId: " . $patient['id']);
            $success = $this->creneauModel->createRendezVous($creneauId, $serviceId, $patient['id']);
            
            if (!$success) {
                error_log("Échec de la création du rendez-vous");
                throw new \Exception("Erreur lors de la création du rendez-vous.");
            }
            error_log("Rendez-vous créé avec succès");

            // Rediriger vers la page de succès
            // On ne met plus de message flash de succès pour éviter l'affichage intempestif
            header('Location: index.php?page=rendezvous&action=success');
            exit;

        } catch (\Exception $e) {
            error_log("ERREUR dans confirmer() : " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }
    }

    public function success() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        $this->view('rendezvous/success-rdv');
    }

    public function modifier() {
        header('Content-Type: application/json');
        error_log("=== Début de la méthode modifier() ===");
        error_log("POST data reçu : " . print_r($_POST, true));
        error_log("Session : " . print_r($_SESSION, true));
        
        try {
            if (!isset($_SESSION['user_id'])) {
                error_log("Erreur: Utilisateur non connecté");
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
                return;
            }

            if (!isset($_POST['rdv_id']) || !isset($_POST['nouvelle_heure']) || !isset($_POST['nouvelle_date'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }

            $rdvId = (int)$_POST['rdv_id'];
            $nouvelleDate = $_POST['nouvelle_date'];
            $nouvelleHeure = $_POST['nouvelle_heure'];

            // Vérifier le format de la date
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $nouvelleDate)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Format de date invalide']);
                return;
            }

            // Vérifier le format de l'heure
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $nouvelleHeure)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Format d\'heure invalide']);
                return;
            }

            // Récupérer le rendez-vous
            $rdv = $this->rendezVousModel->getRendezVousById($rdvId);
            if (!$rdv) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Rendez-vous non trouvé']);
                return;
            }

            // Vérifier les droits
            if ($rdv['patient_user_id'] != $_SESSION['user_id'] && 
                (!isset($_SESSION['user_role']) || 
                ($_SESSION['user_role'] !== 'SECRETAIRE' && $_SESSION['user_role'] !== 'MEDECIN'))) {
                error_log("Accès refusé - user_id: " . $_SESSION['user_id'] . ", role: " . $_SESSION['user_role']);
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce rendez-vous']);
                return;
            }

            // Mettre à jour l'heure du rendez-vous
            error_log("Tentative de modification du rendez-vous - ID: {$rdvId}, Date: {$nouvelleDate}, Heure: {$nouvelleHeure}");
            
            try {
                if ($this->rendezVousModel->modifierHeure($rdvId, $nouvelleDate, $nouvelleHeure)) {
                    error_log("Modification réussie");
                    echo json_encode(['success' => true, 'message' => 'Le rendez-vous a été modifié avec succès']);
                }
            } catch (\Exception $e) {
                error_log("Erreur de modification: " . $e->getMessage());
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la modification du rendez-vous : " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification du rendez-vous']);
        }
    }

    public function list() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $rendezvous = $this->creneauModel->getUserRendezVous($userId);

        $this->view('rendezvous/list-rendezvous', [
            'rendezvous' => $rendezvous
        ]);
    }

    public function annuler() {
        header('Content-Type: application/json');
        
        error_log("=== Début annulation rendez-vous (PHP) ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("Session data: " . print_r($_SESSION, true));
        
        try {
            if (!isset($_SESSION['user_id'])) {
                error_log("Erreur: Utilisateur non connecté");
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
                return;
            }

            if (!isset($_POST['rdv_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de rendez-vous manquant']);
                return;
            }

            $rdvId = (int)$_POST['rdv_id'];

        // Vérifier que le rendez-vous existe et que l'utilisateur a les droits
        try {
            $rdv = $this->rendezVousModel->getRendezVousById($rdvId);
            error_log("=== Détails du rendez-vous ===");
            error_log("RDV complet : " . print_r($rdv, true));
            error_log("ID du rendez-vous : " . $rdvId);
            error_log("Session user_id : " . $_SESSION['user_id']);
            error_log("Date et heure de début : " . ($rdv ? $rdv['debut'] : 'non trouvée'));
            error_log("Date et heure de fin : " . ($rdv ? $rdv['fin'] : 'non trouvée'));
            
            if (!$rdv) {
                error_log("ERREUR: Rendez-vous non trouvé pour l'ID: " . $rdvId);
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Rendez-vous non trouvé']);
                return;
            }
            
            error_log("Vérification des droits - patient_id: " . $rdv['patient_id'] . ", medecin_id: " . $rdv['medecin_id'] . ", session_user_id: " . $_SESSION['user_id']);
            
            // Vérifier si l'utilisateur est le patient, le médecin ou un secrétaire
            if ($rdv['patient_id'] == $_SESSION['user_id'] || 
                $rdv['medecin_id'] == $_SESSION['user_id'] || 
                isset($_SESSION['role']) && $_SESSION['role'] === 'secretaire') {
                
                error_log("Accès autorisé pour l'utilisateur: " . $_SESSION['user_id']);
            } else {
                error_log("Accès non autorisé - user_id: " . $_SESSION['user_id']);
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à annuler ce rendez-vous']);
                return;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la vérification du rendez-vous: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la vérification du rendez-vous']);
            return;
        }            // Annuler le rendez-vous
            if ($this->rendezVousModel->annulerRendezVous($rdvId)) {
                echo json_encode(['success' => true, 'message' => 'Rendez-vous annulé avec succès']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'annulation du rendez-vous']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
}