<?php

namespace App\Controllers;

use App\Models\ActuModel;

// Contrôleur pour la gestion des actualités du cabinet
class ActusController {
    private $actuModel;

    // Constructeur : instancie le modèle des actualités
    public function __construct() {
        $this->actuModel = new ActuModel();
    }

    // Vérifie si l'utilisateur a les droits d'accès admin (médecin ou secrétaire)
    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?page=actus');
            exit();
        }
    }

    // Affiche la liste des actualités (vue publique)
    public function index() {
    $actus = $this->actuModel->getAllActus();
    require_once 'app/views/actu/actu.php';
    }

    // Affiche le détail d'une actualité spécifique
    public function show($id) {
        $actu = $this->actuModel->getActuById($id);
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            header('Location: index.php?page=actus');
            exit();
        }
    require_once 'app/views/actu/detail-actu.php';
    }

    // Crée une nouvelle actualité (formulaire + traitement)
    public function create() {
        // Vérifie les droits d'accès
        $this->checkAdminAccess();

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!\App\Core\Csrf::checkToken($csrf_token)) {
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=actus&action=create');
                exit();
            }
            // Récupère et valide les données du formulaire
            $data = [
                'titre' => trim($_POST['titre']),
                'contenu' => trim($_POST['contenu']),
                'auteur_id' => $_SESSION['user_id']
            ];

            // Vérifie que le titre et le contenu sont remplis
            if (!$data['titre'] || !$data['contenu']) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=create');
                exit();
            }

            // Gestion de l'upload d'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'C:/xampp/htdocs/CabinetDupont/public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $data['image'] = $fileName; // Nom du fichier à stocker en BDD
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

            // Création de l'actualité en base de données
            if ($this->actuModel->createActu($data)) {
                $_SESSION['success'] = "L'actualité a été créée avec succès";
                header('Location: index.php?page=admin');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la création de l'actualité";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=create');
                exit();
            }
        }

        // Affiche le formulaire de création (avec les données précédentes en cas d'erreur)
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
    require_once 'app/views/actu/actu-create.php';
    }

    // Recherche d'actualités par mot-clé
    public function search() {
        // Vérifie les droits d'accès
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?page=actus');
            exit();
        }

        // Utilise le token CSRF existant en session (généré dans AdminController)

        // Récupère le mot-clé et effectue la recherche
        $keyword = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
        if ($keyword) {
            $actus = $this->actuModel->searchActus($keyword);
        } else {
            $actus = $this->actuModel->getAllActus();
        }
        
        require_once 'app/views/actu/actu-posts.php';
    }

    // Affiche les actualités mises en avant
    public function featured() {
        $featuredActus = $this->actuModel->getFeaturedActus();
    require_once 'app/views/actu/actu.php';
    }

    // Modifie une actualité existante
    public function edit($id) {
        // Vérifie les droits d'accès admin
        $this->checkAdminAccess();

        // Récupère l'actualité à modifier
        $actu = $this->actuModel->getActuByIdAdmin($id);
        if (!$actu) {
            $_SESSION['error'] = "Cette actualité n'existe pas";
            header('Location: index.php?page=actus');
            exit();
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!\App\Core\Csrf::checkToken($csrf_token)) {
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=actus&action=edit&id=' . $id);
                exit();
            }

            // Récupère et valide les données du formulaire
            $data = [
                'titre' => trim($_POST['titre']),
                'contenu' => trim($_POST['contenu']),
                'statut' => $_POST['statut'] ?? 'BROUILLON'
            ];

            if (empty($data['titre']) || empty($data['contenu'])) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=edit&id=' . $id);
                exit();
            }

            // Gestion de l'upload d'image (optionnel)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $maxSize = 5 * 1024 * 1024; // 5 Mo
                if ($_FILES['image']['size'] > $maxSize) {
                    $_SESSION['error'] = "L'image ne doit pas dépasser 5 Mo.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=actus&action=edit&id=' . $id);
                    exit();
                }
                $uploadDir = 'C:/xampp/htdocs/CabinetDupont/public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $data['image'] = $fileName;
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
                        $_SESSION['form_data'] = $data;
                        header('Location: index.php?page=actus&action=edit&id=' . $id);
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Format d'image non autorisé (jpg, png, gif, webp uniquement).";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=actus&action=edit&id=' . $id);
                    exit();
                }
            }

            // Mise à jour de l'actualité en base de données
            if ($this->actuModel->updateActu($id, $data)) {
                $_SESSION['success'] = "L'actualité a été modifiée avec succès";
                header('Location: index.php?page=admin');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la modification de l'actualité";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=actus&action=edit&id=' . $id);
                exit();
            }
        }

        // Affiche le formulaire d'édition (avec les données précédentes en cas d'erreur)
        $formData = $_SESSION['form_data'] ?? $actu;
        unset($_SESSION['form_data']);
    require_once 'app/views/actu/actu-update.php';
    }

    // Supprime une actualité
    public function delete($id) {
        // Vérifie que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour supprimer une actualité";
            header('Location: index.php?page=login');
            exit();
        }

    // Vérification CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
        if (!\App\Core\Csrf::checkToken($csrf_token)) {
            $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
            header('Location: index.php?page=admin');
            exit();
        }

        // Suppression en base de données
        if ($this->actuModel->deleteActu($id)) {
            $_SESSION['success'] = "L'actualité a été supprimée avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'actualité";
        }

    header('Location: index.php?page=admin');
    exit();
    }
}