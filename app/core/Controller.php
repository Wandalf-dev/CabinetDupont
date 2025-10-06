<?php

namespace App\Core;

class Controller {
    public function __construct() {
        // Constructeur de base pour l'héritage
    }

    protected function view($view, $data = []) {
        error_log("Controller: Chargement de la vue: " . $view);
        error_log("Controller: Données passées: " . print_r($data, true));
        
        extract($data);
        $viewPath = __DIR__ . '/../views/' . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
        
        error_log("Controller: Chemin de la vue: " . $viewPath);
        if (!file_exists($viewPath)) {
            error_log("Controller: La vue n'existe pas à l'emplacement: " . $viewPath);
            throw new \Exception("La vue '$view' n'existe pas à l'emplacement: $viewPath");
        }
        
        error_log("Controller: La vue existe, tentative d'inclusion");
        require_once $viewPath;
        error_log("Controller: Vue chargée avec succès");
    }

    protected function redirect($page) {
        header("Location: index.php?page=$page");
        exit();
    }

    protected function setError($message) {
        $_SESSION['error'] = $message;
    }

    protected function setSuccess($message) {
        $_SESSION['success'] = $message;
    }
}
