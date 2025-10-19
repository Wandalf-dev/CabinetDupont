<?php

namespace App\Core;

// Classe de base pour tous les contrôleurs du projet
class Controller {
    // Constructeur de base, utile pour l'héritage
    public function __construct() {
        // Rien à initialiser ici, mais permet aux contrôleurs enfants d'appeler parent::__construct()
    }

    // Méthode pour charger une vue et lui passer des données
    protected function view($view, $data = []) {
        // Log technique pour le débogage : nom de la vue et données transmises
        
        // Transforme les clés du tableau $data en variables pour la vue
        extract($data);

        // Construit le chemin complet vers le fichier de la vue
        $viewPath = __DIR__ . '/../views/' . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
        
        // Log technique : chemin du fichier de la vue

        // Vérifie que le fichier de la vue existe
        if (!file_exists($viewPath)) {
            throw new \Exception("La vue '$view' n'existe pas à l'emplacement: $viewPath");
        }
        
        // Log technique : la vue existe, on l'inclut
        require_once $viewPath;
    }

    // Méthode pour rediriger vers une autre page
    protected function redirect($page) {
        header("Location: index.php?page=$page");
        exit();
    }

    // Méthode pour définir un message d'erreur en session
    protected function setError($message) {
        $_SESSION['error'] = $message;
    }

    // Méthode pour définir un message de succès en session
    protected function setSuccess($message) {
        $_SESSION['success'] = $message;
    }
}