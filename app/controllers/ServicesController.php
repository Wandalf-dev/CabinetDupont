<?php

namespace App\Controllers;

use App\Models\ServiceModel;

// Contrôleur pour la gestion des services du cabinet
class ServicesController {
    private $serviceModel;

    // Constructeur : instancie le modèle des services
    public function __construct() {
        $this->serviceModel = new ServiceModel();
    }

    // Vérifie si l'utilisateur a les droits d'accès admin (médecin ou secrétaire)
    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?page=services');
            exit();
        }
    }

    // Affiche la liste des services
    public function index() {
        // Récupère tous les services publics
        $services = $this->serviceModel->getAllServices();
        
        // Si l'utilisateur est admin, affiche la vue de gestion
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && 
            ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')) {
            $servicesAdmin = $this->serviceModel->getAllServicesAdmin();
            require_once 'app/views/service/service-posts.php';
        } else {
            // Pour les visiteurs, affiche seulement les services publiés
            require_once 'app/views/service/service.php';
        }
    }

    // Crée un nouveau service (formulaire + traitement)
    public function create() {
        // Vérifie les droits d'accès
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!\App\Core\Csrf::checkToken($csrf_token)) {
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=services&action=create');
                exit();
            }
            // Récupère et valide les données du formulaire
            $data = [
                'titre' => trim($_POST['titre']),
                'description' => trim($_POST['description']),
                'statut' => 'PUBLIE',
                'couleur' => trim($_POST['couleur']),
                'duree' => intval($_POST['duree'])
            ];

            // Validation de la couleur
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $data['couleur'])) {
                $data['couleur'] = '#4CAF50'; // Couleur par défaut si invalide
            }

            // Vérifie que le titre et la description sont remplis
            if (!$data['titre'] || !$data['description']) {
                $_SESSION['error'] = "Le titre et la description sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=services&action=create');
                exit();
            }

            // Gestion de l'upload d'image (SÉCURISÉE)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'C:/xampp/htdocs/CabinetDupont/public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true); // Permissions plus sécurisées (0755 au lieu de 0777)
                }
                
                // SÉCURITÉ 1 : Vérifier l'extension du fichier
                $originalFileName = $_FILES['image']['name'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (!\App\Core\Security::isAllowedFileExtension($originalFileName, $allowedExtensions)) {
                    $_SESSION['error'] = "Extension de fichier non autorisée. Formats acceptés : jpg, jpeg, png, gif, webp";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=create');
                    exit();
                }
                
                // SÉCURITÉ 2 : Vérifier le type MIME réel du fichier (pas juste l'extension)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!\App\Core\Security::isAllowedMimeType($_FILES['image']['tmp_name'], $allowedMimes)) {
                    \App\Core\Security::logSecurityEvent('UPLOAD_BLOCKED', "Tentative d'upload avec MIME invalide : $originalFileName");
                    $_SESSION['error'] = "Le fichier n'est pas une image valide.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=create');
                    exit();
                }
                
                // SÉCURITÉ 3 : Vérifier la taille du fichier (max 5MB)
                $maxSize = 5 * 1024 * 1024; // 5 MB
                if ($_FILES['image']['size'] > $maxSize) {
                    $_SESSION['error'] = "Le fichier est trop volumineux. Taille maximale : 5 MB";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=create');
                    exit();
                }
                
                // SÉCURITÉ 4 : Générer un nom de fichier sécurisé (évite les injections)
                $fileName = \App\Core\Security::generateSecureFilename($originalFileName);
                $uploadFile = $uploadDir . $fileName;
                
                // Upload du fichier
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // SÉCURITÉ 5 : Définir les permissions du fichier (lecture seule pour les autres)
                    chmod($uploadFile, 0644);
                    $data['image'] = $fileName;
                } else {
                    $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
                    $_SESSION['form_data'] = $data;
                    header('Location: index.php?page=services&action=create');
                    exit();
                }
            } else {
                $data['image'] = null;
            }
            
            // Crée le service en base de données
            if ($this->serviceModel->createService($data)) {
                $_SESSION['success'] = "Le service a été créé avec succès";
                header('Location: index.php?page=admin');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la création du service";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=admin&action=addService');
                exit();
            }
        }

        // Affiche le formulaire de création (avec les données précédentes en cas d'erreur)
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        require_once 'app/views/service/service-create.php';
    }

    // Modifie un service existant
    public function edit($id) {
        // Vérifie les droits d'accès admin
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
                'statut' => $_POST['statut'] ?? 'BROUILLON',
                'couleur' => trim($_POST['couleur']),
                'duree' => intval($_POST['duree'])
            ];

            // Validation de la couleur
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $data['couleur'])) {
                error_log("Couleur invalide reçue : " . $data['couleur']);
                $data['couleur'] = '#4CAF50'; // Couleur par défaut si invalide
            }

            if (empty($data['titre']) || empty($data['description'])) {
                $_SESSION['error'] = "Le titre et la description sont obligatoires";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=services&action=edit&id=' . $id);
                exit();
            }

            // Gestion de l'upload d'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $maxSize = 5 * 1024 * 1024; // 5 Mo
                if ($_FILES['image']['size'] > $maxSize) {
                    $_SESSION['error'] = "L'image ne doit pas dépasser 5 Mo.";
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
            } else {
                // Si aucune nouvelle image n'est téléchargée, on garde l'image existante
                $data['image'] = isset($_POST['current_image']) ? $_POST['current_image'] : $service['image'];
            }

            // Met à jour le service en base de données
            if ($this->serviceModel->updateService($id, $data)) {
                $_SESSION['success'] = "Le service a été modifié avec succès";
                header('Location: index.php?page=admin');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la modification du service";
                $_SESSION['form_data'] = $data;
                header('Location: index.php?page=admin&action=editService&id=' . $id);
                exit();
            }
        }

        // Affiche le formulaire d'édition (avec les données précédentes en cas d'erreur)
        $formData = $_SESSION['form_data'] ?? $service;
        unset($_SESSION['form_data']);
        require_once 'app/views/service/service-update.php';
    }

    // Supprime un service
    public function delete($id) {
        $this->checkAdminAccess();

        if ($this->serviceModel->deleteService($id)) {
            $_SESSION['success'] = "Le service a été supprimé avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression du service";
        }

        header('Location: index.php?page=admin');
        exit();
    }

    // Met à jour l'ordre des services (drag & drop côté admin)
    public function updateOrder() {
        $this->checkAdminAccess();
        // Récupère le JSON envoyé
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (!isset($data['order']) || !is_array($data['order'])) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit();
        }
        // Met à jour l'ordre dans la base de données
        foreach ($data['order'] as $index => $id) {
            $this->serviceModel->updateOrdre($id, $index);
        }
        echo json_encode(['success' => true]);
        exit();
    }
}