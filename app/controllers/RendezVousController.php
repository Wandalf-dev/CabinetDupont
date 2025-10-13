<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ServiceModel;
use App\Models\CreneauModel;

error_log("Chargement de RendezVousController");

class RendezVousController extends Controller {
    private $serviceModel;
    private $creneauModel;

    public function __construct() {
        parent::__construct();
        $this->serviceModel = new ServiceModel();
        $this->creneauModel = new CreneauModel();
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
        if (!isset($_SESSION['user_id']) || !isset($_GET['service_id'])) {
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        $serviceId = $_GET['service_id'];
        $service = $this->serviceModel->getServiceById($serviceId);

        if (!$service) {
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        $this->view('rendezvous/select-date', [
            'service' => $service
        ]);
    }

    public function selectTime() {
        error_log("Méthode selectTime appelée");
        if (!isset($_SESSION['user_id']) || !isset($_GET['service_id']) || !isset($_GET['date'])) {
            header('Location: index.php?page=rendezvous&action=selectConsultation');
            exit;
        }

        $serviceId = $_GET['service_id'];
        $date = $_GET['date'];
        
        // Récupérer les créneaux disponibles pour cette date
        $availableSlots = $this->creneauModel->getAvailableSlots($date, $serviceId);

        $this->view('rendezvous/select-time', [
            'availableSlots' => $availableSlots
        ]);
    }
}