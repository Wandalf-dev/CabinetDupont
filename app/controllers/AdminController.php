<?php

namespace App\Controllers;

use App\Models\ActuModel;
use App\Models\ServiceModel;
use App\Models\HoraireModel;

// Contrôleur pour le panneau d'administration du cabinet
class AdminController extends \App\Core\Controller {
    // Propriétés pour accéder aux modèles des actualités, services et horaires
    private $actuModel;
    private $serviceModel;
    private $horaireModel;

    // Constructeur : instancie les modèles nécessaires
    public function __construct() {
        parent::__construct();
        $this->actuModel = new ActuModel();
        $this->serviceModel = new ServiceModel();
        $this->horaireModel = new HoraireModel();
    }

    // Méthode principale pour afficher le panneau d'administration
    public function index() {
        // Vérifie que l'utilisateur est bien un administrateur (médecin ou secrétaire)
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            // Si ce n'est pas le cas, redirige vers la page d'accueil
            header('Location: index.php?page=home');
            exit();
        }

        // Récupère toutes les actualités pour l'administration
        $actusAdmin = $this->actuModel->getAllActusAdmin();
        // Récupère tous les services pour l'administration
        $servicesAdmin = $this->serviceModel->getAllServicesAdmin();
        // Récupère les horaires du cabinet
        $horaires = $this->horaireModel->getHoraires();

        // Charge la vue combinée de l'administration (onglets actu, services, horaires)
        require_once 'app/views/admin-combined.php';
    }
}