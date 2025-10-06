<?php

namespace App\Controllers;

use App\Models\AuthModel;

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                $_SESSION['error'] = "Veuillez remplir tous les champs";
                header('Location: index.php?page=login?error=missing');
                exit();
            }

            $result = $this->authModel->login($email, $password);
            
            if ($result['success']) {
                $user = $result['user'];
                // Stocker les informations importantes en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_avatar'] = $user['avatar'];
                $_SESSION['success'] = "Connexion réussie !";
                header('Location: index.php?page=profil');
                exit();
            } else {
                $_SESSION['error'] = "Email ou mot de passe incorrect";
                header('Location: index.php?page=login?error=invalid');
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
                'nom' => filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING),
                'prenom' => filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'password' => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
                'telephone' => filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING),
                'adresse' => filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING)
            ];

            // Validation
            if (!$data['email'] || !$data['password']) {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=register');
                exit();
            }

            if ($data['password'] !== $data['password_confirm']) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=register');
                exit();
            }

            // Tentative d'inscription
            $result = $this->authModel->register($data);
            
            if ($result['success']) {
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header('Location: index.php?page=login');
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=register');
                exit();
            }
        }

        // Afficher le formulaire d'inscription
        require_once 'app/views/auth/register.php';
    }
}