<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function profile() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer les informations de l'utilisateur
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé";
            header('Location: index.php?page=login');
            exit();
        }

        // Charger la vue
        $data = ['user' => $user];
        require_once 'app/views/user/profile.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier votre profil";
            header('Location: index.php?page=login');
            exit();
        }

        // Récupérer les informations actuelles de l'utilisateur
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé";
            header('Location: index.php?page=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING),
                'prenom' => filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'telephone' => filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING),
                'adresse' => filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING)
            ];

            // Vérifier si une modification de mot de passe est demandée
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validation des données du profil
            if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
                $_SESSION['error'] = "Les champs nom, prénom et email sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=profile/edit');
                exit();
            }

            // Mettre à jour le profil
            $updateSuccess = $this->userModel->updateProfile($_SESSION['user_id'], $data);

            // Gérer la modification du mot de passe si demandée
            if ($current_password && $new_password) {
                if ($new_password !== $confirm_password) {
                    $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=profile/edit');
                    exit();
                }

                // Vérifier l'ancien mot de passe
                if (!password_verify($current_password, $user['password_hash'])) {
                    $_SESSION['error'] = "Le mot de passe actuel est incorrect";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=profile/edit');
                    exit();
                }

                // Mettre à jour le mot de passe
                $passwordSuccess = $this->userModel->updatePassword($_SESSION['user_id'], $new_password);
                if (!$passwordSuccess) {
                    $_SESSION['error'] = "Erreur lors de la modification du mot de passe";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=profile/edit');
                    exit();
                }
            }

            if ($updateSuccess) {
                $_SESSION['success'] = "Profil mis à jour avec succès" . 
                    ($current_password ? " et mot de passe modifié" : "");
                header('Location: index.php?page=profile');
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du profil";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=profile/edit');
                exit();
            }
        }

        // Afficher le formulaire d'édition
        $formData = $_SESSION['form_data'] ?? $user;
        unset($_SESSION['form_data']);
        require_once 'app/views/user/edit.php';
    }
}