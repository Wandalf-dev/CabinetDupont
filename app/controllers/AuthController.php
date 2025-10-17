<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Core\Csrf;

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

            // Recherche l'utilisateur par email
            $user = $this->userModel->getUserByEmail($email);

            // Vérifie le mot de passe
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true); // Sécurise la session après connexion
                // Stocke les informations importantes en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['success'] = "Connexion réussie !";
                header('Location: index.php?page=home');
                exit();
            } else {
                $_SESSION['error'] = "Email ou mot de passe incorrect";
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
                'nom' => mb_strtoupper(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING)),
                'prenom' => filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'password' => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
                'telephone' => filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING),
                'date_naissance' => filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING),
                'role' => 'PATIENT' // Par défaut, les nouveaux inscrits sont des patients
            ];

            // Validation des champs obligatoires
            if (!$data['nom'] || !$data['prenom'] || !$data['email'] || !$data['password']) {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            // Vérifie que les mots de passe correspondent
            if ($data['password'] !== $data['password_confirm']) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas";
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