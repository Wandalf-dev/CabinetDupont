<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use App\Entities\User;

class AuthController
{
    private UserModel $userModel;

    public function __construct(?UserModel $userModel = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->userModel = $userModel ?? new UserModel();
        $_SESSION['_token'] ??= bin2hex(random_bytes(16)); // CSRF token global simple
    }

    /** Helpers */
    private function redirect(string $route): void
    {
        header('Location: index.php?page=' . $route);
        exit();
    }

    private function clean(?string $v): string
    {
        return trim((string)($v ?? ''));
    }

    private function requirePostAndCsrf(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }
        if (isset($_POST['_token']) && hash_equals($_SESSION['_token'] ?? '', (string)$_POST['_token']) === false) {
            $_SESSION['error'] = "Session expirée, veuillez réessayer.";
            $this->redirect('auth&action=login');
        }
    }

    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->requirePostAndCsrf();

            $email    = $this->clean($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $_SESSION['error'] = "Veuillez remplir tous les champs.";
                $_SESSION['form_data'] = ['email' => $email];
                $this->redirect('auth&action=login');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Adresse email invalide.";
                $_SESSION['form_data'] = ['email' => $email];
                $this->redirect('auth&action=login');
            }

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user->getPasswordHash())) {
                // Protection fixation de session
                session_regenerate_id(true);
                $_SESSION['user_id']    = $user->getId();
                $_SESSION['user_role']  = $user->getRole();
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_nom']   = $user->getNom();
                $_SESSION['user_prenom']= $user->getPrenom();
                $_SESSION['success']    = "Connexion réussie !";
                // Nouveau token CSRF pour la suite
                $_SESSION['_token'] = bin2hex(random_bytes(16));
                $this->redirect('user&action=profile');
            }

            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            $_SESSION['form_data'] = ['email' => $email];
            $this->redirect('auth&action=login');
        }

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/auth/login.php';
    }

    public function logout(): void
    {
        // Nettoyage session (cookie + données)
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
        }
        $_SESSION = [];
        session_destroy();
        $this->redirect('home');
    }

    public function register(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->requirePostAndCsrf();

            $data = [
                'nom'       => $this->clean($_POST['nom'] ?? ''),
                'prenom'    => $this->clean($_POST['prenom'] ?? ''),
                'email'     => $this->clean($_POST['email'] ?? ''),
                'password'  => (string)($_POST['password'] ?? ''),
                'confirm'   => (string)($_POST['password_confirm'] ?? ''),
                'telephone' => $this->clean($_POST['telephone'] ?? ''),
                'role'      => 'PATIENT', // défaut
            ];

            // Validations
            if ($data['nom'] === '' || $data['prenom'] === '' || $data['email'] === '' || $data['password'] === '') {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis.";
                $_SESSION['form_data'] = $data;
                $this->redirect('auth&action=register');
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Adresse email invalide.";
                $_SESSION['form_data'] = $data;
                $this->redirect('auth&action=register');
            }
            if ($data['password'] !== $data['confirm']) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                $_SESSION['form_data'] = $data;
                $this->redirect('auth&action=register');
            }
            // (Optionnel) règles minimales de mot de passe
            if (strlen($data['password']) < 8) {
                $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
                $_SESSION['form_data'] = $data;
                $this->redirect('auth&action=register');
            }

            if ($this->userModel->emailExists($data['email'])) {
                $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
                $_SESSION['form_data'] = $data;
                $this->redirect('auth&action=register');
            }

            // Hash du mot de passe côté contrôleur (sécurise même si le modèle oublie)
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            $user = new \App\Entities\User([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'role' => $data['role'],
                'passwordHash' => $passwordHash,
                'dateInscription' => new \DateTime()
            ]);
            
            $created = $this->userModel->createUser($user);

            if ($created) {
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $this->redirect('auth&action=login');
            }

            $_SESSION['error'] = "Une erreur est survenue lors de l'inscription.";
            $_SESSION['form_data'] = $data;
            $this->redirect('auth&action=register');
        }

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/auth/register.php';
    }
}
