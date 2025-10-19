<?php

namespace App\Core;

/**
 * Classe de sécurité pour gérer les tentatives de connexion
 * Protection contre les attaques par force brute
 */
class Security {
    
    /**
     * Vérifie si l'utilisateur a dépassé le nombre de tentatives autorisées
     * 
     * @param string $identifier Identifiant (email ou IP)
     * @param int $maxAttempts Nombre maximum de tentatives (par défaut 5)
     * @param int $timeWindow Fenêtre de temps en secondes (par défaut 15 minutes)
     * @return array ['allowed' => bool, 'remaining' => int, 'wait_time' => int]
     */
    public static function checkLoginAttempts($identifier, $maxAttempts = 5, $timeWindow = 900) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_attempts_' . md5($identifier);
        $now = time();
        
        // Initialiser si pas encore défini
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'first_attempt' => $now,
                'last_attempt' => $now
            ];
        }
        
        $attempts = $_SESSION[$key];
        
        // Vérifier si la fenêtre de temps est expirée
        if (($now - $attempts['first_attempt']) > $timeWindow) {
            // Réinitialiser les tentatives
            $_SESSION[$key] = [
                'attempts' => 0,
                'first_attempt' => $now,
                'last_attempt' => $now
            ];
            return [
                'allowed' => true,
                'remaining' => $maxAttempts,
                'wait_time' => 0
            ];
        }
        
        // Vérifier si le nombre de tentatives est dépassé
        if ($attempts['attempts'] >= $maxAttempts) {
            $waitTime = $timeWindow - ($now - $attempts['first_attempt']);
            return [
                'allowed' => false,
                'remaining' => 0,
                'wait_time' => $waitTime
            ];
        }
        
        return [
            'allowed' => true,
            'remaining' => $maxAttempts - $attempts['attempts'],
            'wait_time' => 0
        ];
    }
    
    /**
     * Enregistre une tentative de connexion
     * 
     * @param string $identifier Identifiant (email ou IP)
     */
    public static function recordLoginAttempt($identifier) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_attempts_' . md5($identifier);
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 1,
                'first_attempt' => $now,
                'last_attempt' => $now
            ];
        } else {
            $_SESSION[$key]['attempts']++;
            $_SESSION[$key]['last_attempt'] = $now;
        }
    }
    
    /**
     * Réinitialise les tentatives de connexion (après connexion réussie)
     * 
     * @param string $identifier Identifiant (email ou IP)
     */
    public static function resetLoginAttempts($identifier) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_attempts_' . md5($identifier);
        unset($_SESSION[$key]);
    }
    
    /**
     * Récupère l'adresse IP réelle du client
     * 
     * @return string Adresse IP
     */
    public static function getClientIp() {
        $ip = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Si plusieurs IPs, prendre la première
        if (strpos($ip, ',') !== false) {
            $ip = explode(',', $ip)[0];
        }
        
        return trim($ip);
    }
    
    /**
     * Valide la force d'un mot de passe
     * 
     * @param string $password Mot de passe à valider
     * @return array ['valid' => bool, 'errors' => array, 'strength' => string]
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        $strength = 'weak';
        
        // Longueur minimale
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        
        // Au moins une majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        }
        
        // Au moins une minuscule
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        }
        
        // Au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }
        
        // Au moins un caractère spécial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*...)";
        }
        
        // Calculer la force du mot de passe
        if (empty($errors)) {
            if (strlen($password) >= 12) {
                $strength = 'strong';
            } elseif (strlen($password) >= 10) {
                $strength = 'medium';
            } else {
                $strength = 'acceptable';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => $strength
        ];
    }
    
    /**
     * Nettoie une entrée utilisateur pour éviter les injections XSS
     * 
     * @param mixed $data Données à nettoyer
     * @return mixed Données nettoyées
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitizeInput($value);
            }
        } else {
            $data = htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    /**
     * Valide un email
     * 
     * @param string $email Email à valider
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Génère un token sécurisé
     * 
     * @param int $length Longueur du token
     * @return string Token généré
     */
    public static function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Vérifie si une extension de fichier est autorisée
     * 
     * @param string $filename Nom du fichier
     * @param array $allowedExtensions Extensions autorisées
     * @return bool
     */
    public static function isAllowedFileExtension($filename, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions);
    }
    
    /**
     * Vérifie le type MIME réel d'un fichier
     * 
     * @param string $filepath Chemin du fichier
     * @param array $allowedMimes Types MIME autorisés
     * @return bool
     */
    public static function isAllowedMimeType($filepath, $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']) {
        if (!file_exists($filepath)) {
            return false;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        return in_array($mimeType, $allowedMimes);
    }
    
    /**
     * Génère un nom de fichier sécurisé
     * 
     * @param string $filename Nom original du fichier
     * @return string Nom sécurisé
     */
    public static function generateSecureFilename($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        
        // Nettoyer le nom de base
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $basename = substr($basename, 0, 50); // Limiter la longueur
        
        // Ajouter un identifiant unique
        $unique = uniqid('', true);
        
        return $basename . '_' . $unique . '.' . $extension;
    }
    
    /**
     * Log une tentative de sécurité suspecte
     * 
     * @param string $type Type d'événement
     * @param string $details Détails
     */
    public static function logSecurityEvent($type, $details) {
        $logFile = __DIR__ . '/../../logs/security_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        $ip = self::getClientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $logEntry = sprintf(
            "[%s] %s | IP: %s | Details: %s | User-Agent: %s\n",
            $timestamp,
            $type,
            $ip,
            $details,
            $userAgent
        );
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
