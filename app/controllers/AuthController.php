<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Core\Csrf;
use App\Core\Security;

// Contrôleur pour la gestion de l'authentification des utilisateurs
class AuthController {
    private $userModel;

    // Constructeur : instancie le modèle utilisateur
    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Méthode pour la connexion des utilisateurs
    public function login() {
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère et sécurise les données du formulaire
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $csrf_token = $_POST['csrf_token'] ?? '';

            // Vérifie le token CSRF pour la sécurité
            if (!Csrf::checkToken($csrf_token)) {
                Security::logSecurityEvent('CSRF_FAILURE', 'Tentative de connexion avec token CSRF invalide');
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=auth&action=login');
                exit();
            }

            // Vérifie que tous les champs sont remplis
            if (!$email || !$password) {
                $_SESSION['error'] = "Veuillez remplir tous les champs";
                header('Location: index.php?page=auth&action=login');
                exit();
            }

            // Vérification anti-bruteforce (5 tentatives max en 15 minutes)
            $clientIp = Security::getClientIp();
            $identifier = $email . '_' . $clientIp;
            $checkAttempts = Security::checkLoginAttempts($identifier, 5, 900);
            
            if (!$checkAttempts['allowed']) {
                $waitMinutes = ceil($checkAttempts['wait_time'] / 60);
                Security::logSecurityEvent('BRUTEFORCE_BLOCKED', "Trop de tentatives pour $email depuis $clientIp");
                $_SESSION['error'] = "Trop de tentatives de connexion. Veuillez réessayer dans $waitMinutes minutes.";
                header('Location: index.php?page=auth&action=login');
                exit();
            }

            // Recherche l'utilisateur par email
            $user = $this->userModel->getUserByEmail($email);

            // Vérifie le mot de passe
            if ($user && password_verify($password, $user['password_hash'])) {
                // Connexion réussie : réinitialiser les tentatives
                Security::resetLoginAttempts($identifier);
                
                session_regenerate_id(true); // Sécurise la session après connexion
                // Stocke les informations importantes en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['success'] = "Connexion réussie !";
                
                Security::logSecurityEvent('LOGIN_SUCCESS', "Connexion réussie pour $email");
                
                header('Location: index.php?page=home');
                exit();
            } else {
                // Échec de connexion : enregistrer la tentative
                Security::recordLoginAttempt($identifier);
                $remaining = $checkAttempts['remaining'] - 1;
                
                Security::logSecurityEvent('LOGIN_FAILURE', "Échec de connexion pour $email depuis $clientIp");
                
                if ($remaining > 0) {
                    $_SESSION['error'] = "Email ou mot de passe incorrect. Il vous reste $remaining tentative(s).";
                } else {
                    $_SESSION['error'] = "Email ou mot de passe incorrect. Votre compte sera temporairement bloqué après la prochaine tentative.";
                }
                
                header('Location: index.php?page=auth&action=login');
                exit();
            }
        }

        // Génère le token CSRF et affiche le formulaire de connexion
        $csrf_token = Csrf::generateToken();
        require 'app/views/auth/login.php';
    }

    // Méthode pour la déconnexion
    public function logout() {
        session_destroy();
        header('Location: index.php?page=home');
        exit();
    }

    // Méthode pour l'inscription des nouveaux utilisateurs
    public function register() {
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            // Vérifie le token CSRF
            if (!\App\Core\Csrf::checkToken($csrf_token)) {
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=auth&action=register');
                exit();
            }
            // Récupère et sécurise les données du formulaire
            $data = [
                'nom' => mb_strtoupper(htmlspecialchars(strip_tags($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8')),
                'prenom' => htmlspecialchars(strip_tags($_POST['prenom'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'password' => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
                'telephone' => htmlspecialchars(strip_tags($_POST['telephone'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'date_naissance' => htmlspecialchars(strip_tags($_POST['date_naissance'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'role' => 'PATIENT' // Par défaut, les nouveaux inscrits sont des patients
            ];

            // Validation des champs obligatoires
            if (!$data['nom'] || !$data['prenom'] || !$data['email'] || !$data['password']) {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }
            
            // Validation de la date de naissance
            if (!empty($data['date_naissance'])) {
                $date = trim($data['date_naissance']);
                $finalDate = null;
                $dateObject = null;
                
                // Essayer le format dd/mm/yyyy (saisie manuelle ou formaté par JS)
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                    $dt = \DateTime::createFromFormat('d/m/Y', $date);
                    if ($dt && $dt->format('d/m/Y') === $date) {
                        $dateObject = $dt;
                        $finalDate = $dt->format('Y-m-d');
                    }
                }
                // Essayer le format yyyy-mm-dd (Flatpickr ou format MySQL)
                elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    $dt = \DateTime::createFromFormat('Y-m-d', $date);
                    if ($dt && $dt->format('Y-m-d') === $date) {
                        $dateObject = $dt;
                        $finalDate = $date; // Déjà au bon format
                    }
                }
                
                // Si aucun format valide
                if ($finalDate === null) {
                    $_SESSION['error'] = "La date de naissance n'est pas au bon format (attendu : jj/mm/aaaa).";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=auth&action=register');
                    exit();
                }
                
                // Validation de l'âge (entre 3 et 120 ans)
                $today = new \DateTime();
                $age = $today->diff($dateObject)->y;
                
                if ($age < 3) {
                    $_SESSION['error'] = "Vous devez avoir au moins 3 ans pour créer un compte.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=auth&action=register');
                    exit();
                } elseif ($age > 120) {
                    $_SESSION['error'] = "La date de naissance ne peut pas dépasser 120 ans.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=auth&action=register');
                    exit();
                } elseif ($dateObject > $today) {
                    $_SESSION['error'] = "La date de naissance ne peut pas être dans le futur.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=auth&action=register');
                    exit();
                }
                
                $data['date_naissance'] = $finalDate;
            }

            // Vérifie que les mots de passe correspondent
            if ($data['password'] !== $data['password_confirm']) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            // Validation de la force du mot de passe
            $passwordValidation = Security::validatePasswordStrength($data['password']);
            if (!$passwordValidation['valid']) {
                // Construire un message plus clair avec des puces
                $errorMessage = "Mot de passe trop faible. Il doit contenir :<br>";
                $errorMessage .= "• Au moins 8 caractères<br>";
                $errorMessage .= "• Une majuscule (A-Z)<br>";
                $errorMessage .= "• Une minuscule (a-z)<br>";
                $errorMessage .= "• Un chiffre (0-9)<br>";
                $errorMessage .= "• Un caractère spécial (!@#$%^&*...)";
                
                $_SESSION['error'] = $errorMessage;
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            // Vérifie si l'email existe déjà
            if ($this->userModel->emailExists($data['email'])) {
                $_SESSION['error'] = "Cette adresse email est déjà utilisée";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            // Création du nouvel utilisateur
            if ($this->userModel->createUser($data)) {
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header('Location: index.php?page=auth&action=login');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'inscription";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }
        }

        // Affiche le formulaire d'inscription
        require_once 'app/views/auth/register.php';
    }
}