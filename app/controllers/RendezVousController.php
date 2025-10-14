<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ServiceModel;
use App\Models\CreneauModel;
use App\Models\PatientModel;

error_log("Chargement de RendezVousController");

class RendezVousController extends Controller {
    private $serviceModel;
    private $creneauModel;
    private $patientModel;

    public function __construct() {
        parent::__construct();
        $this->serviceModel = new ServiceModel();
        $this->creneauModel = new CreneauModel();
        $this->patientModel = new PatientModel();
    }

    public function index() {
        // Redirige vers selectConsultation par défaut
        $this->selectConsultation();
    }

    public function selectConsultation() {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = "Vous devez être connecté pour prendre un rendez-vous.";
            header('Location: index.php?page=auth&action=login');
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
        
        if (!isset($_SESSION['user_id']) || !isset($_GET['service_id'])) {
            header('Location: index.php?page=rendezvous&action=selectConsultation');
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
        error_log("=== Début méthode selectTime() ===");
        if (!isset($_SESSION['user_id']) || !isset($_GET['service_id']) || !isset($_GET['date'])) {
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        // S'assurer que la date est dans le bon fuseau horaire
        date_default_timezone_set('Europe/Paris');
        
        $serviceId = (int)$_GET['service_id'];
        $date = $_GET['date'];
        
        // Convertir la date dans le fuseau horaire correct si nécessaire
        $dateObj = new \DateTime($date, new \DateTimeZone('Europe/Paris'));
        $date = $dateObj->format('Y-m-d');
        
        error_log("Paramètres reçus - serviceId: $serviceId, date: $date");

        // Récupérer le service
        $service = $this->serviceModel->getServiceById($serviceId);
        if (!$service) {
            $_SESSION['error'] = "Le service demandé n'existe pas.";
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }
        
        // Récupérer les créneaux disponibles pour cette date
        $availableSlots = $this->creneauModel->getAvailableSlots($date, $serviceId);
        error_log("Créneaux disponibles trouvés : " . print_r($availableSlots, true));

        if (empty($availableSlots)) {
            $_SESSION['error'] = "Aucun créneau disponible pour cette date.";
            header('Location: index.php?page=rendezvous&action=selectDate&service_id=' . $serviceId);
            exit;
        }

        $this->view('rendezvous/select-time', [
            'availableSlots' => $availableSlots,
            'service' => $service,
            'date' => $date
        ]);
    }

    public function confirmation() {
        error_log("=== Début méthode confirmation() ===");
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
                $_SESSION['error'] = "Veuillez compléter votre profil patient.";
                header('Location: index.php?page=user&action=profile');
                exit;
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
            $_SESSION['success'] = "Votre rendez-vous a été confirmé avec succès !";
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

    public function cancel() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header('Location: index.php?page=rendezvous&action=list');
            exit;
        }

        try {
            $rdvId = (int)$_GET['id'];
            $userId = $_SESSION['user_id'];

            if ($this->creneauModel->cancelRendezVous($rdvId, $userId)) {
                $_SESSION['success'] = "Le rendez-vous a été annulé avec succès.";
            } else {
                $_SESSION['error'] = "Impossible d'annuler ce rendez-vous.";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Une erreur est survenue lors de l'annulation du rendez-vous.";
        }

        header('Location: index.php?page=rendezvous&action=list');
        exit;
    }
}