<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;

class UserController
{
    private UserModel $userModel;

    public function __construct(?UserModel $userModel = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->userModel = $userModel ?? new UserModel();
    }

    /** Petite aide pour éviter de répéter */
    private function redirect(string $route): void
    {
        header('Location: index.php?page=' . $route);
        exit();
    }

    /** Nettoyage simple (évite FILTER_SANITIZE_STRING déprécié) */
    private function clean(?string $v): string
    {
        return trim((string)($v ?? ''));
    }

    public function profile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
            $this->redirect('login');
        }

        $user = $this->userModel->getUserById((int)$_SESSION['user_id']);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            $this->redirect('login');
        }

        $data = ['user' => $user];

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/user/profile.php';
    }

    public function edit(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier votre profil.";
            $this->redirect('login');
        }

        $user = $this->userModel->getUserById((int)$_SESSION['user_id']);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            $this->redirect('login');
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            // (Optionnel) CSRF
            if (isset($_POST['_token'], $_SESSION['_token']) && $_POST['_token'] !== $_SESSION['_token']) {
                $_SESSION['error'] = "Session expirée, veuillez réessayer.";
                $this->redirect('profile/edit');
            }

            $data = [
                'nom'       => $this->clean($_POST['nom'] ?? null),
                'prenom'    => $this->clean($_POST['prenom'] ?? null),
                'email'     => $this->clean($_POST['email'] ?? null),
                'telephone' => $this->clean($_POST['telephone'] ?? null),
                'adresse'   => $this->clean($_POST['adresse'] ?? null),
            ];

            // validations simples
            if ($data['nom'] === '' || $data['prenom'] === '' || $data['email'] === '') {
                $_SESSION['error'] = "Les champs nom, prénom et email sont obligatoires.";
                $_SESSION['form_data'] = $data;
                $this->redirect('profile/edit');
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email invalide.";
                $_SESSION['form_data'] = $data;
                $this->redirect('profile/edit');
            }

            // mise à jour profil
            $updateSuccess = $this->userModel->updateProfile((int)$_SESSION['user_id'], $data);

            // changement de mot de passe (optionnel)
            $current = (string)($_POST['current_password'] ?? '');
            $new     = (string)($_POST['new_password'] ?? '');
            $confirm = (string)($_POST['confirm_password'] ?? '');

            if ($current !== '' || $new !== '' || $confirm !== '') {
                if ($new === '' || $confirm === '') {
                    $_SESSION['error'] = "Renseignez le nouveau mot de passe et sa confirmation.";
                    $_SESSION['form_data'] = $data;
                    $this->redirect('profile/edit');
                }
                if ($new !== $confirm) {
                    $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas.";
                    $_SESSION['form_data'] = $data;
                    $this->redirect('profile/edit');
                }
                // Vérifier l'ancien mot de passe
                if (!password_verify($current, $user->getPasswordHash())) {
                    $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
                    $_SESSION['form_data'] = $data;
                    $this->redirect('profile/edit');
                }
                // Mettre à jour (le modèle doit hasher en interne, sinon hasher ici)
                $passwordSuccess = $this->userModel->updatePassword((int)$_SESSION['user_id'], $new);
                if (!$passwordSuccess) {
                    $_SESSION['error'] = "Erreur lors de la modification du mot de passe.";
                    $_SESSION['form_data'] = $data;
                    $this->redirect('profile/edit');
                }
            }

            if ($updateSuccess) {
                $_SESSION['success'] = "Profil mis à jour avec succès" . ($new !== '' ? " et mot de passe modifié" : "");
                $this->redirect('profile');
            }

            $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
            $_SESSION['form_data'] = $data;
            $this->redirect('profile/edit');
        }

        // GET : afficher formulaire avec les données de l'utilisateur
        $formData = $_SESSION['form_data'] ?? [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone()
        ];
        unset($_SESSION['form_data']);

        // (Optionnel) régénérer un token CSRF
        $_SESSION['_token'] = bin2hex(random_bytes(16));

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/user/edit.php';
    }
}
