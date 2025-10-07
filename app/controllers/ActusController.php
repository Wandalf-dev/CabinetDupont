<?php

namespace App\Controllers;

use App\Models\ActuModel;

class ActusController {
    private $actuModel;

    public function __construct() {
        $this->actuModel = new ActuModel();
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?page=actus');
            exit();
        }
    }

    public function index() {
        // Charger les actualités publiques pour tous les utilisateurs
        $actus = $this->actuModel->getAllActus();
        
        // Si l'utilisateur est admin, charger aussi les actus admin
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && 
            ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')) {
            $actusAdmin = $this->actuModel->getAllActusAdmin();
            require_once 'app/views/actu-combined.php';
        } else {
            // Public : seulement les actus publiées
            $actus = $this->actuModel->getAllActus();
            require_once 'app/views/actu.php';
        }
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

    public function create() {
        // Vérifier les droits d'accès
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données
            $data = [
                'titre' => trim($_POST['titre']),
                'contenu' => trim($_POST['contenu']),
                'auteur_id' => $_SESSION['user_id']
            ];

            // Vérifier que tous les champs sont remplis
            if (!$data['titre'] || !$data['contenu']) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=create');
                exit();
            }

            // Gestion de l'upload d'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $data['image'] = $fileName; // À stocker dans la BDD
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
                        $_SESSION['form_data'] = $data;
                        header('Location: index.php?page=actus&action=create');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Format d'image non autorisé (jpg, png, gif, webp uniquement).";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=actus&action=create');
                    exit();
                }
            } else {
                $data['image'] = null;
            }

            // Créer l'actualité
            if ($this->actuModel->createActu($data)) {
                $_SESSION['success'] = "L'actualité a été créée avec succès";
                header('Location: index.php?page=actus');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la création de l'actualité";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=create');
                exit();
            }
        }

        // Afficher le formulaire de création avec les données précédentes en cas d'erreur
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        require_once 'app/views/actu-create.php';
    }

    public function search() {
        // Vérifier si l'utilisateur est autorisé à accéder à la gestion
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?page=actus');
            exit();
        }

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

    public function edit($id) {
        // Vérifier les droits d'accès admin
        $this->checkAdminAccess();

        // Utiliser getActuByIdAdmin pour récupérer l'actualité quel que soit son statut
        $actu = $this->actuModel->getActuByIdAdmin($id);
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            header('Location: index.php?page=actus');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => trim($_POST['titre']),
                'contenu' => trim($_POST['contenu']),
                'statut' => $_POST['statut'] ?? 'BROUILLON' // Utiliser le statut soumis ou BROUILLON par défaut
            ];

            if (empty($data['titre']) || empty($data['contenu'])) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=edit&id=' . $id);
                exit();
            }

            if ($this->actuModel->updateActu($id, $data)) {
                $_SESSION['success'] = "L'actualité a été modifiée avec succès";
                header('Location: index.php?page=actus'); // Redirection vers la page de gestion
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