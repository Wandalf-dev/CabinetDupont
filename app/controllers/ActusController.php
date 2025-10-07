<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActuModel;
use App\Entities\Article;

class ActusController
{
    private ActuModel $actuModel;

    public function __construct(?ActuModel $actuModel = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->actuModel = $actuModel ?? new ActuModel();
        $_SESSION['_token'] ??= bin2hex(random_bytes(16));
    }

    /** ---------- Helpers ---------- */
    private function redirect(string $route): void
    {
        header('Location: index.php?page=' . $route);
        exit();
    }

    private function clean(?string $v): string
    {
        return trim((string)($v ?? ''));
    }

    private function isAdmin(): bool
    {
        $role = $_SESSION['user_role'] ?? null;
        return isset($_SESSION['user_id']) && ($role === 'MEDECIN' || $role === 'SECRETAIRE');
    }

    private function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = "Accès non autorisé";
            $this->redirect('actus');
        }
    }

    private function requirePostAndCsrf(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }
        if (!isset($_POST['_token']) || !hash_equals($_SESSION['_token'] ?? '', (string)$_POST['_token'])) {
            $_SESSION['error'] = "Session expirée, veuillez réessayer.";
            $this->redirect('actus');
        }
    }

    /** ---------- Actions ---------- */

    public function index(): void
    {
        // Actus publiques pour tout le monde
        $actus = $this->actuModel->getAllActus();

        if ($this->isAdmin()) {
            $actusAdmin = $this->actuModel->getAllActusAdmin();
            /** @noinspection PhpIncludeInspection */
            require_once 'app/views/actu-combined.php';
            return;
        }

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/actu.php';
    }

    public function show(int $id): void
    {
        $actu = $this->actuModel->getActuById($id);
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            $this->redirect('actus');
        }

        // Variable disponible pour la vue
        $actus = [$actu]; // ou créer une vue dédiée actu-single.php

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/actu-single.php'; // Vue dédiée pour un article
    }

    public function create(): void
    {
        $this->requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->requirePostAndCsrf();

            $titre   = $this->clean($_POST['titre']   ?? '');
            $contenu = $this->clean($_POST['contenu'] ?? '');
            $statut  = $this->clean($_POST['statut']  ?? 'PUBLIE'); // ou BROUILLON

            $formData = ['titre' => $titre, 'contenu' => $contenu, 'statut' => $statut];

            if ($titre === '' || $contenu === '') {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $formData;
                $this->redirect('actus&action=create');
            }

            // Construire l'entité Article (datePublication = maintenant)
            $article = new Article([
                'auteurId'        => (int)$_SESSION['user_id'],
                'titre'           => $titre,
                'contenu'         => $contenu,
                'datePublication' => new \DateTime('now'),
                'statut'          => $statut,
            ]);

            $id = $this->actuModel->createActu($article);
            if ($id > 0) {
                $_SESSION['success'] = "L'actualité a été créée avec succès";
                $this->redirect('actus');
            }

            $_SESSION['error'] = "Une erreur est survenue lors de la création de l'actualité";
            $_SESSION['form_data'] = $formData;
            $this->redirect('actus&action=create');
        }

        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/actu-create.php';
    }

    public function search(): void
    {
        $q = $this->clean($_GET['q'] ?? '');
        
        // Recherche publique ou admin selon le contexte
        if ($this->isAdmin()) {
            // Admin peut rechercher dans tous les articles (y compris brouillons)
            $actus = $q !== '' ? $this->actuModel->searchActus($q) : $this->actuModel->getAllActusAdmin();
        } else {
            // Public ne voit que les articles publiés
            $actus = $q !== '' ? $this->actuModel->searchActus($q) : $this->actuModel->getAllActus();
        }

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/actu.php';
    }

    public function featured(): void
    {
        $actus = $this->actuModel->getFeaturedActus(3);

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/actu.php';
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();

        // On récupère sans filtrer par statut côté modèle admin
        $actu = $this->actuModel->getActuByIdAdmin($id);
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            $this->redirect('actus');
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->requirePostAndCsrf();

            $titre   = $this->clean($_POST['titre']   ?? '');
            $contenu = $this->clean($_POST['contenu'] ?? '');
            $statut  = $this->clean($_POST['statut']  ?? 'BROUILLON');

            $formData = ['titre' => $titre, 'contenu' => $contenu, 'statut' => $statut];

            if ($titre === '' || $contenu === '') {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $formData;
                $this->redirect('actus&action=edit&id=' . $id);
            }

            // On reconstruit une entité Article avec l'ID à mettre à jour
            $article = new Article([
                'id'              => (int)$id,
                'auteurId'        => $actu->getAuteurId(), // on conserve l'auteur
                'titre'           => $titre,
                'contenu'         => $contenu,
                // si pas de date fournie, on garde l'ancienne; sinon, tu peux décider de la MAJ
                'datePublication' => $actu->getDatePublication(),
                'statut'          => $statut,
            ]);

            if ($this->actuModel->updateActu($article)) {
                $_SESSION['success'] = "L'actualité a été modifiée avec succès";
                $this->redirect('actus');
            }

            $_SESSION['error'] = "Une erreur est survenue lors de la modification de l'actualité";
            $_SESSION['form_data'] = $formData;
            $this->redirect('actus&action=edit&id=' . $id);
        }

        $formData = $_SESSION['form_data'] ?? [
            'titre'   => $actu->getTitre(),
            'contenu' => $actu->getContenu(),
            'statut'  => $actu->getStatut(),
        ];
        unset($_SESSION['form_data']);

        /** @noinspection PhpIncludeInspection */
        require_once 'app/views/actu-update.php';
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();

        if ($this->actuModel->deleteActu($id)) {
            $_SESSION['success'] = "L'actualité a été supprimée avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'actualité";
        }
        $this->redirect('actus');
    }
}