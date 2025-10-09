<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActuModel;
use App\Models\ServiceModel;
use App\Models\HoraireModel;

// Contrôleur pour la page d'accueil du cabinet
class HomeController extends Controller {
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

    // Méthode principale pour afficher la page d'accueil
    public function index() {
        // Récupère les actualités mises en avant
        $featuredActus = $this->actuModel->getFeaturedActus();
        // Récupère la liste des services proposés par le cabinet
        $services = $this->serviceModel->getAllServices();
        // Récupère les horaires d'ouverture du cabinet
        $horaires = $this->horaireModel->getHoraires();
        // Charge la vue de la page d'accueil avec toutes les données récupérées
        require_once 'app/views/home.php';
    }
}
