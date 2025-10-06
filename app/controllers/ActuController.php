<?php

namespace App\Controllers;

use App\Models\ActuModel;

class ActuController {
    private $actuModel;

    public function __construct() {
        $this->actuModel = new ActuModel();
    }

    public function index() {
        // Page principale des actualités
        $actus = $this->actuModel->getAllActus();
        require_once 'app/views/actu-posts.php';
    }

    public function show($id) {
        // Afficher une actualité spécifique
        $actu = $this->actuModel->getActuById($id);
        
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            header('Location: index.php?page=actus');
            exit();
        }
        
        require_once 'app/views/actu.php';
    }

    public function search() {
        $keyword = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
        if ($keyword) {
            $actus = $this->actuModel->searchActus($keyword);
        } else {
            $actus = $this->actuModel->getAllActus();
        }
        
        require_once 'app/views/actu-posts.php';
    }

    public function featured() {
        // Page d'accueil ou section mise en avant
        $featuredActus = $this->actuModel->getFeaturedActus();
        require_once 'app/views/actu.php';
    }

    public function create() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour créer une actualité";
            header('Location: index.php?page=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING),
                'contenu' => filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING),
                'featured' => isset($_POST['featured']) ? 1 : 0
            ];

            if (empty($data['titre']) || empty($data['contenu'])) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=create');
                exit();
            }

            $id = $this->actuModel->createActu($data);
            if ($id) {
                $_SESSION['success'] = "L'actualité a été créée avec succès";
                header('Location: index.php?page=actus&action=show&id=' . $id);
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la création de l'actualité";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=create');
                exit();
            }
        }

        // Afficher le formulaire de création
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        require_once 'app/views/actu-create.php';
    }

    public function edit($id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier une actualité";
            header('Location: index.php?page=login');
            exit();
        }

        $actu = $this->actuModel->getActuById($id);
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            header('Location: index.php?page=actus');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING),
                'contenu' => filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING),
                'featured' => isset($_POST['featured']) ? 1 : 0
            ];

            if (empty($data['titre']) || empty($data['contenu'])) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=edit&id=' . $id);
                exit();
            }

            if ($this->actuModel->updateActu($id, $data)) {
                $_SESSION['success'] = "L'actualité a été modifiée avec succès";
                header('Location: index.php?page=actus&action=show&id=' . $id);
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la modification de l'actualité";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=edit&id=' . $id);
                exit();
            }
        }

        // Afficher le formulaire d'édition
        $formData = $_SESSION['form_data'] ?? $actu;
        unset($_SESSION['form_data']);
        require_once 'app/views/actu-update.php';
    }

    public function delete($id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour supprimer une actualité";
            header('Location: index.php?page=login');
            exit();
        }

        if ($this->actuModel->deleteActu($id)) {
            $_SESSION['success'] = "L'actualité a été supprimée avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'actualité";
        }

        header('Location: index.php?page=actus');
        exit();
    }
}