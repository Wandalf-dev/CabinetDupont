<?php

namespace App\Controllers;

use App\Models\ActuModel;
use App\Models\ServiceModel;

class AdminController extends \App\Core\Controller {
    private $actuModel;
    private $serviceModel;

    public function __construct() {
        parent::__construct();
        $this->actuModel = new ActuModel();
        $this->serviceModel = new ServiceModel();
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

        // Charger la vue
        require_once 'app/views/admin-combined.php';
    }
}