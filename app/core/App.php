<?php

namespace App\Core;

// Classe principale qui gère le routage de l'application
class App {
    // Méthode principale qui lance l'application
    public function run() {
        // Configuration des erreurs
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        // Démarre la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        

        
        // S'assurer que REQUEST_URI est défini
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Récupère et nettoie la page demandée dans l'URL, par défaut 'home'
        $page = isset($_GET['page']) ? str_replace(' ', '', strtolower($_GET['page'])) : 'home';
        // Récupère l'action demandée, par défaut 'index'
        $action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';
        
        

        // Liste des contrôleurs spéciaux avec leur casse exacte et leur namespace
        $specialControllers = [
            'rendezvous' => ['name' => 'RendezVous', 'class' => 'App\\Controllers\\RendezVousController'],
            'actus' => ['name' => 'Actus', 'class' => 'App\\Controllers\\ActusController'],
            'auth' => ['name' => 'Auth', 'class' => 'App\\Controllers\\AuthController'],
            'agenda' => ['name' => 'Agenda', 'class' => 'App\\Controllers\\AgendaController'],
            'creneaux' => ['name' => 'Creneaux', 'class' => 'App\\Controllers\\CreneauxController'],
            'horaires' => ['name' => 'Horaires', 'class' => 'App\\Controllers\\HorairesController'],
            'service' => ['name' => 'Service', 'class' => 'App\\Controllers\\ServicesController'],
            'admin' => ['name' => 'Admin', 'class' => 'App\\Controllers\\AdminController']
        ];
        
        // Nettoyage et normalisation de la page
        $page = strtolower(trim($page));

        // Prépare le nom du contrôleur et sa classe
        
        if (array_key_exists($page, $specialControllers)) {
            $controllerName = $specialControllers[$page]['name'];
            $controllerClass = $specialControllers[$page]['class'];
        } else {
            $controllerName = ucfirst($page);
            $controllerClass = "App\\Controllers\\{$controllerName}Controller";
        }

        // Vérification supplémentaire du contrôleur
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}Controller.php";

        // Prépare le nom de la méthode et les paramètres
        $method = strtolower(trim($action));
        $params = [];


        // Ajoute automatiquement l'id ou d'autres paramètres GET comme arguments à la méthode
        if (isset($_GET['id'])) {
            $params[] = $_GET['id'];
        }

        // Construit le nom complet de la classe du contrôleur
        $controllerClass = "App\\Controllers\\{$controllerName}Controller";

        try {
            // Vérifie si le contrôleur existe
            if (!class_exists($controllerClass)) {
                throw new \Exception("Le contrôleur '$controllerClass' n'existe pas.");
            }

            // Crée une instance du contrôleur
            $controller = new $controllerClass();

            // Vérifie si la méthode demandée existe
            if (!method_exists($controller, $method)) {
                throw new \Exception("L'action '$method' n'existe pas dans le contrôleur.");
            }            // Vérifie si le fichier du contrôleur existe
            $controllerFile = __DIR__ . "/../controllers/{$controllerName}Controller.php";
            if (!file_exists($controllerFile)) {
                foreach (glob(__DIR__ . "/../controllers/*.php") as $file) {
                }
                throw new \Exception("Le fichier du contrôleur '{$controllerName}Controller.php' n'existe pas.");
            }

            require_once $controllerFile;
            
            // Vérifie si la classe du contrôleur existe
            if (!class_exists($controllerClass)) {
                throw new \Exception("La classe '$controllerClass' n'existe pas.");
            }

            // Log technique : création d'une instance du contrôleur
            
            // Vérification du contrôleur
            $targetFile = __DIR__ . "/../controllers/{$controllerName}Controller.php";
            
            if (!file_exists($targetFile)) {
                foreach (glob(__DIR__ . "/../controllers/*Controller.php") as $file) {
                }
                throw new \Exception("Le contrôleur '{$controllerName}Controller.php' n'existe pas.");
            }

            $controller = new $controllerClass();

            // Vérifie si la méthode demandée existe dans le contrôleur
            if (!method_exists($controller, $method)) {
                throw new \Exception("La méthode '$method' n'existe pas dans le contrôleur.");
            }

            // Log technique : méthode trouvée, tentative d'exécution

            // Appelle la méthode du contrôleur avec les paramètres (ex : id)
            call_user_func_array([$controller, $method], $params);

        } catch (\Exception $e) {
            // En cas d'erreur, log l'exception détaillée pour le débogage
            error_log("Exception dans App.php: " . $e->getMessage());

            // Affiche une page d'erreur 404 à l'utilisateur
            http_response_code(404);
            include __DIR__ . '/../views/error/404.php';
        }
    }
}