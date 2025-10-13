<?php

namespace App\Core;

// Classe principale qui gère le routage de l'application
class App {
    // Méthode principale qui lance l'application
    public function run() {
        session_start(); // Démarre la session PHP pour gérer les utilisateurs et les messages

        // Récupère la page demandée dans l'URL, par défaut 'home'
        $page = $_GET['page'] ?? 'home';
        // Récupère l'action demandée, par défaut 'index'
        $action = $_GET['action'] ?? 'index';

        // Prépare le nom du contrôleur et de la méthode à appeler
        // Gestion spéciale pour 'rendezvous' -> 'RendezVous'
        if ($page === 'rendezvous') {
            $controllerName = 'RendezVous';
        } else {
            $controllerName = ucfirst($page); // Exemple : 'home' devient 'Home'
        }
        $method = $action;
        $params = [];

        // Ajoute automatiquement l'id ou d'autres paramètres GET comme arguments à la méthode
        if (isset($_GET['id'])) {
            $params[] = $_GET['id'];
        }

        // Construit le nom complet de la classe du contrôleur
        $controllerClass = "App\\Controllers\\{$controllerName}Controller";

        try {
            // Log technique détaillé pour le débogage
            error_log("=== DÉBUT DEBUG ROUTAGE ===");
            error_log("URL reçue - page: " . $page . ", action: " . $action);
            error_log("Nom du contrôleur avant transformation: " . $controllerName);
            error_log("Tentative de chargement du contrôleur: " . $controllerClass);
            error_log("Vérification de l'existence du fichier: " . __DIR__ . "/../controllers/{$controllerName}Controller.php");
            error_log("GET params reçus: " . print_r($_GET, true));
            error_log("Le fichier existe ? : " . (file_exists(__DIR__ . "/../controllers/{$controllerName}Controller.php") ? "OUI" : "NON"));
            
            // Vérifie si le fichier du contrôleur existe
            if (!file_exists(__DIR__ . "/../controllers/{$controllerName}Controller.php")) {
                error_log("Erreur: Le fichier du contrôleur n'existe pas");
                throw new \Exception("Le fichier du contrôleur '{$controllerName}Controller.php' n'existe pas.");
            }
            
            // Vérifie si la classe du contrôleur existe
            if (!class_exists($controllerClass)) {
                error_log("Erreur: La classe du contrôleur n'existe pas");
                throw new \Exception("La classe '$controllerClass' n'existe pas.");
            }

            // Log technique : création d'une instance du contrôleur
            error_log("Création d'une instance du contrôleur");
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