<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                $_SESSION['error'] = "Veuillez remplir tous les champs";
                header('Location: index.php?page=auth&action=login');
                exit();
            }

            $user = $this->userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Stocker les informations importantes en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['success'] = "Connexion réussie !";
                header('Location: index.php?page=user&action=profile');
                exit();
            } else {
                $_SESSION['error'] = "Email ou mot de passe incorrect";
                header('Location: index.php?page=auth&action=login');
                exit();
            }
        }

        // Afficher le formulaire de connexion
        require_once 'app/views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?page=home');
        exit();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            // Validation
            if (!$data['nom'] || !$data['prenom'] || !$data['email'] || !$data['password']) {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            if ($data['password'] !== $data['password_confirm']) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            // Vérifier si l'email existe déjà
            if ($this->userModel->emailExists($data['email'])) {
                $_SESSION['error'] = "Cette adresse email est déjà utilisée";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=auth&action=register');
                exit();
            }

            // Tentative d'inscription
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

        // Afficher le formulaire d'inscription
        require_once 'app/views/auth/register.php';
    }
}