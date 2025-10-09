<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActuModel;
use App\Models\ServiceModel;
use App\Models\HoraireModel;

class HomeController extends Controller {
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
        $featuredActus = $this->actuModel->getFeaturedActus();
        $services = $this->serviceModel->getAllServices();
        $horaires = $this->horaireModel->getHoraires();
        require_once 'app/views/home.php';
    }
}
