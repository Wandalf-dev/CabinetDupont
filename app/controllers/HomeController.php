<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActuModel;
use App\Models\ServiceModel;

class HomeController extends Controller {
    private $actuModel;
    private $serviceModel;

    public function __construct() {
        parent::__construct();
        $this->actuModel = new ActuModel();
        $this->serviceModel = new ServiceModel();
    }

    public function index() {
        $featuredActus = $this->actuModel->getFeaturedActus();
        $services = $this->serviceModel->getAllServices();
        require_once 'app/views/home.php';
    }
}
