<?php

namespace App\Core;

// Classe utilitaire pour la protection CSRF (Cross-Site Request Forgery)
class Csrf {
    // Génère un token CSRF unique et le stocke en session
    public static function generateToken() {
        // Si aucun token n'existe en session, on en crée un nouveau
        if (empty($_SESSION['csrf_token'])) {
            // random_bytes(32) génère 32 octets aléatoires, bin2hex les convertit en chaîne hexadécimale
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Retourne le token CSRF stocké en session
        return $_SESSION['csrf_token'];
    }

    // Vérifie que le token fourni correspond à celui en session
    public static function checkToken($token) {
        // hash_equals compare les deux chaînes de façon sécurisée (évite les attaques timing)
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
