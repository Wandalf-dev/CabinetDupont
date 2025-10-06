<?php

namespace App\Core;

class App {
    public function run() {
        session_start();
        
        // Récupère la page demandée, par défaut 'home'
        $page = $_GET['page'] ?? 'home';
        $action = $_GET['action'] ?? 'index';
        
        // Construction du nom du contrôleur et de la méthode
        $controllerName = ucfirst($page);
        $method = $action;
        $params = [];
        
        // Construction du nom complet du contrôleur
        $controllerClass = "App\\Controllers\\{$controllerName}Controller";
        
        try {
            error_log("Tentative de chargement du contrôleur: " . $controllerClass);
            
            if (!class_exists($controllerClass)) {
                error_log("Erreur: Le contrôleur n'existe pas");
                throw new \Exception("Le contrôleur '$controllerClass' n'existe pas.");
            }

            error_log("Création d'une instance du contrôleur");
            $controller = new $controllerClass();
            
            error_log("Vérification de la méthode: " . $method);
            if (!method_exists($controller, $method)) {
                error_log("Erreur: La méthode n'existe pas");
                throw new \Exception("La méthode '$method' n'existe pas dans le contrôleur.");
            }

            error_log("Méthode trouvée, tentative d'exécution");

            // Appel de la méthode avec les paramètres si présents
            call_user_func_array([$controller, $method], $params);

        } catch (\Exception $e) {
            // Log l'erreur détaillée
            error_log("Exception détaillée: " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . " ligne " . $e->getLine());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Afficher une page d'erreur
            http_response_code(404);
            include __DIR__ . '/../views/error/404.php';
        }
    }
}