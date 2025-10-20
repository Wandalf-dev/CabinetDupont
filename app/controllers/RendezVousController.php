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
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour prendre un rendez-vous.";
            header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
            exit;
        }

        if (!isset($_GET['service_id'])) {
            $_SESSION['error'] = "Veuillez sélectionner un service.";
            header('Location: ' . BASE_URL . '/index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        $serviceId = (int)$_GET['service_id'];

        // Récupération du service
        $service = $this->serviceModel->getServiceById($serviceId);
        if (!$service) {
            $_SESSION['error'] = "Le service demandé n'existe pas.";
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        // Récupération des dates disponibles (tous services confondus)
        $datesDisponibles = $this->creneauModel->getDatesDisponibles();

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

            // Récupérer les créneaux disponibles
            $availableSlots = $this->creneauModel->getAvailableSlots($date, $serviceId);
            
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
        try {
            // Vérification des paramètres requis
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception("Vous devez être connecté pour prendre un rendez-vous.");
            }
            if (!isset($_GET['creneau_id'])) {
                throw new \Exception("Aucun créneau sélectionné.");
            }
            if (!isset($_GET['service_id'])) {
                throw new \Exception("Aucun service sélectionné.");
            }

            $creneauId = (int)$_GET['creneau_id'];
            $serviceId = (int)$_GET['service_id'];
            $userId = $_SESSION['user_id'];
            

            // Récupération et vérification du créneau
            $creneau = $this->creneauModel->getCreneauById($creneauId);
            if (!$creneau) {
                throw new \Exception("Le créneau sélectionné n'existe pas.");
            }

            // Vérification que le créneau n'est pas déjà réservé
            if ($creneau['est_reserve']) {
                throw new \Exception("Ce créneau n'est plus disponible.");
            }

            // Récupération et vérification du service
            $service = $this->serviceModel->getServiceById($serviceId);
            if (!$service) {
                throw new \Exception("Le service demandé n'existe plus.");
            }

            // Récupération du profil patient
            $patient = $this->patientModel->getPatientByUserId($userId);
            if (!$patient) {
                throw new \Exception("Profil patient introuvable.");
            }


            // Affiche la vue de confirmation
            $this->view('rendezvous/confirmation-rdv', [
                'creneau' => $creneau,
                'service' => $service,
                'patient' => $patient
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }
    }

    public function confirmer() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception("Vous devez être connecté pour confirmer un rendez-vous.");
            }

            // Vérifier la présence des données POST
            
            if (!isset($_POST['creneau_id']) || !isset($_POST['service_id'])) {
                throw new \Exception("Informations manquantes pour la confirmation.");
            }

            $creneauId = (int)$_POST['creneau_id'];
            $serviceId = (int)$_POST['service_id'];
            $userId = $_SESSION['user_id'];


            // Récupérer le patient
            $patient = $this->patientModel->getPatientByUserId($userId);
            if (!$patient) {
                throw new \Exception("Profil patient introuvable.");
            }

            // Vérifier que le créneau est toujours disponible
            $creneau = $this->creneauModel->getCreneauById($creneauId);
            if (!$creneau) {
                throw new \Exception("Ce créneau n'existe pas.");
            }
            if ($creneau['est_reserve']) {
                throw new \Exception("Ce créneau n'est plus disponible.");
            }

            // Vérifier que le créneau n'est pas dans moins de 4 heures (délai minimum)
            $dateHeureCreneau = new \DateTime($creneau['debut']);
            $maintenant = new \DateTime();
            $maintenant->modify('+4 hours'); // Ajoute 4 heures à l'heure actuelle
            
            if ($dateHeureCreneau < $maintenant) {
                throw new \Exception("Les rendez-vous doivent être pris au moins 4 heures à l'avance. Veuillez choisir un autre créneau.");
            }

            // Vérifier le service
            $service = $this->serviceModel->getServiceById($serviceId);
            if (!$service) {
                throw new \Exception("Le service demandé n'existe pas.");
            }

            // Créer le rendez-vous
            $success = $this->creneauModel->createRendezVous($creneauId, $serviceId, $patient['id']);
            
            if (!$success) {
                throw new \Exception("Erreur lors de la création du rendez-vous.");
            }

            // Rediriger vers la page de succès
            // On ne met plus de message flash de succès pour éviter l'affichage intempestif
            header('Location: index.php?page=rendezvous&action=success');
            exit;

        } catch (\Exception $e) {
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
        
        try {
            if (!isset($_SESSION['user_id'])) {
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
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce rendez-vous']);
                return;
            }

            // Mettre à jour l'heure du rendez-vous
            
            try {
                if ($this->rendezVousModel->modifierHeure($rdvId, $nouvelleDate, $nouvelleHeure)) {
                    echo json_encode(['success' => true, 'message' => 'Le rendez-vous a été modifié avec succès']);
                }
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
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
        
        try {
            // Log pour debug
            error_log("=== ANNULATION RDV ===");
            error_log("SESSION user_id: " . ($_SESSION['user_id'] ?? 'NON DÉFINI'));
            error_log("POST data: " . print_r($_POST, true));
            error_log("php://input: " . file_get_contents('php://input'));
            
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
                return;
            }

            // Lire les données JSON si présentes
            $input = json_decode(file_get_contents('php://input'), true);
            $rdvId = null;
            
            // Accepter 'id' ou 'rdv_id' depuis JSON ou POST
            if (isset($input['id'])) {
                $rdvId = (int)$input['id'];
            } elseif (isset($input['rdv_id'])) {
                $rdvId = (int)$input['rdv_id'];
            } elseif (isset($_POST['rdv_id'])) {
                $rdvId = (int)$_POST['rdv_id'];
            } elseif (isset($_POST['id'])) {
                $rdvId = (int)$_POST['id'];
            }

            if (!$rdvId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de rendez-vous manquant']);
                return;
            }

            // Vérifier que le rendez-vous existe et que l'utilisateur a les droits
            $rdv = $this->rendezVousModel->getRendezVousById($rdvId);
            
            if (!$rdv) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Rendez-vous non trouvé']);
                return;
            }
            
            // Vérifier si l'utilisateur est le patient, le médecin ou un secrétaire/médecin
            $isAuthorized = (
                $rdv['patient_id'] == $_SESSION['user_id'] || 
                $rdv['medecin_id'] == $_SESSION['user_id'] || 
                (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'MEDECIN')
            );
            
            if (!$isAuthorized) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à annuler ce rendez-vous']);
                return;
            }
            
            // Annuler le rendez-vous
            $result = $this->rendezVousModel->annulerRendezVous($rdvId);
            
            if ($result) {
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

    /**
     * Change le statut d'un rendez-vous (HONORE, ABSENT, etc.)
     */
    public function changerStatut() {
        // Capturer toute sortie pour éviter de casser le JSON
        ob_start();
        
        header('Content-Type: application/json');
        
        try {
            
            // Vérifier que l'utilisateur est admin ou médecin
            if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['ADMIN', 'MEDECIN'])) {
                ob_end_clean();
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
                return;
            }

            // Récupérer les données
            $rendezvousId = (int)($_POST['rendezvous_id'] ?? 0);
            $nouveauStatut = $_POST['statut'] ?? '';


            // Validation
            if (!$rendezvousId || !$nouveauStatut) {
                ob_end_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }

            // Statuts autorisés
            $statutsAutorises = ['CONFIRME', 'HONORE', 'ABSENT', 'ANNULE'];
            if (!in_array($nouveauStatut, $statutsAutorises)) {
                ob_end_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Statut invalide']);
                return;
            }

            // Vérifier le statut actuel du rendez-vous
            $rdvActuel = $this->rendezVousModel->getRendezVousById($rendezvousId);
            if (!$rdvActuel) {
                ob_end_clean();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Rendez-vous introuvable']);
                return;
            }

            // Vérifier si le statut est déjà celui demandé
            if ($rdvActuel['statut'] === $nouveauStatut) {
                ob_end_clean();
                $messageStatut = [
                    'HONORE' => 'honoré',
                    'ABSENT' => 'absent',
                    'CONFIRME' => 'confirmé',
                    'ANNULE' => 'annulé'
                ];
                $statutTexte = $messageStatut[$nouveauStatut] ?? $nouveauStatut;
                echo json_encode([
                    'success' => false,
                    'message' => "Ce rendez-vous est déjà marqué comme \"$statutTexte\""
                ]);
                return;
            }

            // Mettre à jour le statut
            $success = $this->rendezVousModel->updateStatus($rendezvousId, $nouveauStatut);

            // Nettoyer le buffer et envoyer le JSON
            ob_end_clean();

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du statut'
                ]);
            }

        } catch (\Exception $e) {
            ob_end_clean();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
}