<?php

namespace App\Controllers;

use App\Models\UserModel;
use DateTime;

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

        // Formater la valeur existante pour l'affichage dans le formulaire
        if (!empty($user['telephone'])) {
            $formData['telephone'] = $user['telephone'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du numéro de téléphone
            $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
            if ($telephone) {
                // Nettoyer pour ne garder que les chiffres (et le +)
                $digits = preg_replace('/[^0-9+]/', '', $telephone);
                
                // Si le numéro commence par +33, enlever le +33
                if (strpos($digits, '+33') === 0) {
                    $digits = substr($digits, 3);
                }
                
                // S'assurer qu'on a au moins 9 chiffres
                if (strlen($digits) >= 9) {
                    // Formater en +33-X-XX-XX-XX-XX
                    $telephone = sprintf('+33-%s-%s-%s-%s-%s',
                        substr($digits, 0, 1),
                        substr($digits, 1, 2),
                        substr($digits, 3, 2),
                        substr($digits, 5, 2),
                        substr($digits, 7, 2)
                    );
                }
            }

            $data = [
                'nom' => filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING),
                'prenom' => filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'telephone' => $telephone
            ];

            // Conversion de la date du format FR au format SQL
            $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
            if ($date_naissance) {
                $date = DateTime::createFromFormat('d/m/Y', $date_naissance);
                if ($date) {
                    $data['date_naissance'] = $date->format('Y-m-d');
                }
            }

            // Vérifier si une modification de mot de passe est demandée
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validation des données du profil
            if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
                $_SESSION['error'] = "Les champs nom, prénom et email sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=user&action=edit');
                exit();
            }

            // Debug des données avant mise à jour
            error_log("Données avant mise à jour : " . print_r($data, true));

            // Mettre à jour le profil
            $updateSuccess = $this->userModel->updateProfile($_SESSION['user_id'], $data);
            error_log("Résultat de la mise à jour : " . ($updateSuccess ? 'succès' : 'échec'));

            // Gérer la modification du mot de passe si demandée
            if ($current_password && $new_password) {
                if ($new_password !== $confirm_password) {
                    $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=user&action=edit');
                    exit();
                }

                // Vérifier l'ancien mot de passe
                if (!password_verify($current_password, $user['password_hash'])) {
                    $_SESSION['error'] = "Le mot de passe actuel est incorrect";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=user&action=edit');
                    exit();
                }

                // Mettre à jour le mot de passe
                $passwordSuccess = $this->userModel->updatePassword($_SESSION['user_id'], $new_password);
                if (!$passwordSuccess) {
                    $_SESSION['error'] = "Erreur lors de la modification du mot de passe";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=user&action=edit');
                    exit();
                }
            }

            if ($updateSuccess) {
                // Mettre à jour la session pour refléter les nouveaux nom/prénom
                $_SESSION['user_prenom'] = $data['prenom'];
                $_SESSION['user_nom'] = $data['nom'];
                $_SESSION['success'] = "Profil mis à jour avec succès" . 
                    ($current_password ? " et mot de passe modifié" : "");
                header('Location: index.php?page=user&action=profile');
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du profil";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=user&action=edit');
                exit();
            }
        }

        // Afficher le formulaire d'édition
        // Si on a des données de formulaire en session, les utiliser
        if (isset($_SESSION['form_data'])) {
            $formData = $_SESSION['form_data'];
            unset($_SESSION['form_data']);
        } else {
            // Sinon, utiliser les données de l'utilisateur
            $formData = [
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'telephone' => $user['telephone'],
                'date_naissance' => $user['date_naissance']
            ];
        }
        require_once 'app/views/user/edit.php';
    }
}