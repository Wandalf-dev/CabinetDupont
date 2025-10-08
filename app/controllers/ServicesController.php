<?php

namespace App\Controllers;

use App\Models\ServiceModel;

class ServicesController {
    private $serviceModel;

    public function __construct() {
        $this->serviceModel = new ServiceModel();
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?page=services');
            exit();
        }
    }

    public function index() {
        // Charger les services publics pour tous les utilisateurs
        $services = $this->serviceModel->getAllServices();
        
        // Si l'utilisateur est admin, charger la vue de gestion
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && 
            ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')) {
            $servicesAdmin = $this->serviceModel->getAllServicesAdmin();
            require_once 'app/views/service/service-posts.php';
        } else {
            // Public : seulement les services publiés
            require_once 'app/views/service/service.php';
        }
    }

    public function create() {
        // Vérifier les droits d'accès
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et valider les données
            $data = [
                'titre' => trim($_POST['titre']),
                'description' => trim($_POST['description']),
                'statut' => $_POST['statut'] ?? 'BROUILLON'
            ];

            // Vérifier que tous les champs sont remplis
            if (!$data['titre'] || !$data['description']) {
                $_SESSION['error'] = "Le titre et la description sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=services&action=create');
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
                        $data['image'] = $fileName;
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
                        $_SESSION['form_data'] = $data;
                        header('Location: index.php?page=services&action=create');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Format d'image non autorisé (jpg, png, gif, webp uniquement).";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=create');
                    exit();
                }
            } else {
                $data['image'] = null;
            }
            
            // Créer le service
            if ($this->serviceModel->createService($data)) {
                $_SESSION['success'] = "Le service a été créé avec succès";
                header('Location: index.php?page=services');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la création du service";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=services&action=create');
                exit();
            }
        }

        // Afficher le formulaire de création avec les données précédentes en cas d'erreur
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        require_once 'app/views/service/service-create.php';
    }

    public function edit($id) {
        // Vérifier les droits d'accès admin
        $this->checkAdminAccess();

        $service = $this->serviceModel->getServiceByIdAdmin($id);
        if (!$service) {
            $_SESSION['error'] = "Ce service n'existe pas";
            header('Location: index.php?page=services');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => trim($_POST['titre']),
                'description' => trim($_POST['description']),
                'statut' => $_POST['statut'] ?? 'BROUILLON'
            ];

            if (empty($data['titre']) || empty($data['description'])) {
                $_SESSION['error'] = "Le titre et la description sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=services&action=edit&id=' . $id);
                exit();
            }

            // Gestion de l'upload d'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $maxSize = 3 * 1024 * 1024; // 3 Mo
                if ($_FILES['image']['size'] > $maxSize) {
                    $_SESSION['error'] = "L'image ne doit pas dépasser 3 Mo.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=edit&id=' . $id);
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
                        header('Location: index.php?page=services&action=edit&id=' . $id);
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Format d'image non autorisé (jpg, png, gif, webp uniquement).";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=edit&id=' . $id);
                    exit();
                }
            }

            if ($this->serviceModel->updateService($id, $data)) {
                $_SESSION['success'] = "Le service a été modifié avec succès";
                header('Location: index.php?page=services');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la modification du service";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=services&action=edit&id=' . $id);
                exit();
            }
        }

        // Afficher le formulaire d'édition
        $formData = $_SESSION['form_data'] ?? $service;
        unset($_SESSION['form_data']);
        require_once 'app/views/service/service-update.php';
    }

    public function delete($id) {
        $this->checkAdminAccess();

        if ($this->serviceModel->deleteService($id)) {
            $_SESSION['success'] = "Le service a été supprimé avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression du service";
        }

        header('Location: index.php?page=services');
        exit();
    }
}