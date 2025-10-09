<?php

namespace App\Controllers;

use App\Models\ActuModel;
use App\Models\ServiceModel;
use App\Models\HoraireModel;

class AdminController extends \App\Core\Controller {
    private $actuModel;
    private $serviceModel;
    private $horaireModel;

    public function __construct() {
        parent::__construct();
        $this->actuModel = new ActuModel();
        $this->serviceModel = new ServiceModel();
        $this->horaireModel = new HoraireModel();
    }

    public function index() {
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        // Récupérer les données nécessaires
        $actusAdmin = $this->actuModel->getAllActusAdmin();
        $servicesAdmin = $this->serviceModel->getAllServicesAdmin();
        $horaires = $this->horaireModel->getHoraires();

        // Charger la vue
        require_once 'app/views/admin-combined.php';
    }
}