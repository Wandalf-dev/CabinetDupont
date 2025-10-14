<?php

namespace App\Core;

// Classe principale qui gère le routage de l'application
class App {
    // Méthode principale qui lance l'application
    public function run() {
        session_start(); // Démarre la session PHP pour gérer les utilisateurs et les messages

        error_log("=== DÉBUT DU ROUTAGE ===");
        
        // S'assurer que REQUEST_URI est défini
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        error_log("REQUEST_URI: " . $requestUri);
        error_log("GET params: " . print_r($_GET, true));
        error_log("POST params: " . print_r($_POST, true));

        // Récupère et nettoie la page demandée dans l'URL, par défaut 'home'
        $page = isset($_GET['page']) ? str_replace(' ', '', strtolower($_GET['page'])) : 'home';
        // Récupère l'action demandée, par défaut 'index'
        $action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';
        
        error_log("URL reçue : " . $_SERVER['REQUEST_URI']);
        error_log("Page : " . $page);
        error_log("Action : " . $action);
        
        error_log("=== DEBUG DÉTAILLÉ ===");
        error_log("GET reçu : " . print_r($_GET, true));
        error_log("Page demandée brute : " . $page);
        error_log("Action demandée : " . $action);

        // Liste des contrôleurs spéciaux avec leur casse exacte et leur namespace
        $specialControllers = [
            'rendezvous' => ['name' => 'RendezVous', 'class' => 'App\\Controllers\\RendezVousController'],
            'actus' => ['name' => 'Actus', 'class' => 'App\\Controllers\\ActusController'],
            'auth' => ['name' => 'Auth', 'class' => 'App\\Controllers\\AuthController'],
            'agenda' => ['name' => 'Agenda', 'class' => 'App\\Controllers\\AgendaController'],
            'creneaux' => ['name' => 'Creneaux', 'class' => 'App\\Controllers\\CreneauxController'],
            'horaires' => ['name' => 'Horaires', 'class' => 'App\\Controllers\\HorairesController'],
            'service' => ['name' => 'Service', 'class' => 'App\\Controllers\\ServicesController']
        ];
        
        // Nettoyage et normalisation de la page
        $page = strtolower(trim($page));
        error_log("Page après nettoyage : " . $page);

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
        error_log("Recherche du fichier contrôleur : " . $controllerFile);
        error_log("Le fichier existe ? : " . (file_exists($controllerFile) ? "OUI" : "NON"));

        // Prépare le nom de la méthode et les paramètres
        $method = strtolower(trim($action));
        $params = [];

        error_log("Nom final du contrôleur : " . $controllerName);
        error_log("Méthode à appeler : " . $method);
        error_log("Chemin complet du contrôleur : " . __DIR__ . "/../controllers/{$controllerName}Controller.php");

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
                error_log("Erreur: Le fichier du contrôleur n'existe pas : " . $controllerFile);
                error_log("Dossier des contrôleurs : " . __DIR__ . "/../controllers/");
                error_log("Fichiers disponibles :");
                foreach (glob(__DIR__ . "/../controllers/*.php") as $file) {
                    error_log("- " . basename($file));
                }
                throw new \Exception("Le fichier du contrôleur '{$controllerName}Controller.php' n'existe pas.");
            }

            require_once $controllerFile;
            
            // Vérifie si la classe du contrôleur existe
            if (!class_exists($controllerClass)) {
                error_log("Erreur: La classe du contrôleur n'existe pas : " . $controllerClass);
                throw new \Exception("La classe '$controllerClass' n'existe pas.");
            }

            // Log technique : création d'une instance du contrôleur
            error_log("Création d'une instance du contrôleur");
            
            // Vérification du contrôleur
            $targetFile = __DIR__ . "/../controllers/{$controllerName}Controller.php";
            error_log("Tentative de chargement du contrôleur : {$controllerName}Controller.php");
            error_log("Chemin complet : " . $targetFile);
            
            if (!file_exists($targetFile)) {
                error_log("ERREUR : Contrôleur non trouvé !");
                error_log("Liste des contrôleurs disponibles :");
                foreach (glob(__DIR__ . "/../controllers/*Controller.php") as $file) {
                    error_log("- " . basename($file));
                }
                throw new \Exception("Le contrôleur '{$controllerName}Controller.php' n'existe pas.");
            }

            $controller = new $controllerClass();

            // Vérifie si la méthode demandée existe dans le contrôleur
            error_log("Vérification de la méthode: " . $method);
            if (!method_exists($controller, $method)) {
                error_log("Erreur: La méthode n'existe pas");
                throw new \Exception("La méthode '$method' n'existe pas dans le contrôleur.");
            }

            // Log technique : méthode trouvée, tentative d'exécution
            error_log("Méthode trouvée, tentative d'exécution");

            // Appelle la méthode du contrôleur avec les paramètres (ex : id)
            call_user_func_array([$controller, $method], $params);

        } catch (\Exception $e) {
            // En cas d'erreur, log l'exception détaillée pour le débogage
            error_log("Exception détaillée: " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . " ligne " . $e->getLine());
            error_log("Stack trace: " . $e->getTraceAsString());

            // Affiche une page d'erreur 404 à l'utilisateur
            http_response_code(404);
            include __DIR__ . '/../views/error/404.php';
        }
    }
}