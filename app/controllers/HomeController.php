<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActuModel;

class HomeController extends Controller {
    private $actuModel;

    public function __construct() {
        error_log("HomeController: Début du constructeur");
        parent::__construct();
        error_log("HomeController: Après parent::__construct");
        $this->actuModel = new ActuModel();
        error_log("HomeController: ActuModel créé");
    }

    public function index() {
        error_log("HomeController: Début de la méthode index");
        // Récupérer les actualités mises en avant pour la page d'accueil
        $featuredActus = $this->actuModel->getFeaturedActus();
        error_log("HomeController: Actualités récupérées");
        error_log("HomeController: Chargement de la vue home");
        $this->view('home', ['featuredActus' => $featuredActus]);
    }
}
