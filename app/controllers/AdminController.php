<?php

namespace App\Controllers;
use DateTime;

use App\Models\ActuModel;
use App\Models\ServiceModel;
use App\Models\HoraireModel;
use App\Models\PatientModel;
use App\Models\CreneauModel;

// Contrôleur pour le panneau d'administration du cabinet
class AdminController extends \App\Core\Controller {
    // Propriétés pour accéder aux modèles des actualités, services et horaires
    private $actuModel;
    private $serviceModel;
    private $horaireModel;
    private $patientModel;
    private $creneauModel;

    // Constructeur : instancie les modèles nécessaires
    public function __construct() {
        parent::__construct();
        $this->actuModel = new ActuModel();
        $this->serviceModel = new ServiceModel();
        $this->horaireModel = new HoraireModel();
        $this->patientModel = new PatientModel();
        $this->creneauModel = new CreneauModel();
    }

    // Méthode principale pour afficher le panneau d'administration
    public function index() {
        // Vérifie que l'utilisateur est bien un administrateur (médecin ou secrétaire)
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            // Si ce n'est pas le cas, redirige vers la page d'accueil
            header('Location: index.php?page=home');
            exit();
        }

        // Récupère toutes les actualités pour l'administration
        $actusAdmin = $this->actuModel->getAllActusAdmin();
        // Récupère tous les services pour l'administration
        $servicesAdmin = $this->serviceModel->getAllServicesAdmin();
        // Récupère les horaires du cabinet
        $horaires = $this->horaireModel->getHoraires();
        // Récupère tous les patients pour l'administration
        $patientsAdmin = $this->patientModel->getAllPatientsAdmin();

        // Récupère tous les créneaux pour l'administration
        $creneaux = $this->creneauModel->getAllCreneaux();

        // Génère le token CSRF une seule fois par session
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = \App\Core\Csrf::generateToken();
        }

        // Charge la vue combinée de l'administration (onglets actu, services, horaires)
        require_once 'app/views/admin-combined.php';
    }

    // Méthodes pour la gestion des patients
    public function addPatient() {
        // Vérification des droits
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données
            $data = $this->validatePatientData($_POST);
            
            if (empty($data['errors'])) {
                // Vérifie si l'email existe déjà
                if ($this->patientModel->emailExists($data['email'])) {
                    $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
                } else {
                    // Crée le patient
                    if ($this->patientModel->createPatient($data)) {
                        $_SESSION['success'] = "Le patient a été ajouté avec succès.";
                        header('Location: index.php?page=admin');
                        exit();
                    } else {
                        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout du patient.";
                    }
                }
            } else {
                $_SESSION['error'] = implode("<br>", $data['errors']);
            }
        }
        
        require_once 'app/views/patient/patient-create.php';
    }

    public function editPatient() {
        // Vérification des droits
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $patient = $this->patientModel->getPatientById($id);

        if (!$patient) {
            $_SESSION['error'] = "Patient non trouvé.";
            header('Location: index.php?page=admin');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données
            $data = $this->validatePatientData($_POST, true);
            
            if (empty($data['errors'])) {
                // Vérifie si l'email existe déjà (en excluant le patient actuel)
                if ($this->patientModel->emailExists($data['email'], $id)) {
                    $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
                } else {
                    // Met à jour le patient
                    if ($this->patientModel->updatePatient($id, $data)) {
                        $_SESSION['success'] = "Les informations du patient ont été mises à jour.";
                        header('Location: index.php?page=admin');
                        exit();
                    } else {
                        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour.";
                    }
                }
            } else {
                $_SESSION['error'] = implode("<br>", $data['errors']);
            }
        }
        
        require_once 'app/views/patient/patient-update.php';
    }

    public function deletePatient() {
        // Vérification des droits
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            header('Location: index.php?page=home');
            exit();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($this->patientModel->deletePatient($id)) {
            $_SESSION['success'] = "Le patient a été supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression.";
        }
        
        header('Location: index.php?page=admin');
        exit();
    }

    private function validatePatientData($data, $isUpdate = false) {
        $errors = [];
        $validated = [];

        // Validation du nom
        if (empty($data['nom'])) {
            $errors[] = "Le nom est obligatoire.";
        } else {
            $validated['nom'] = trim($data['nom']);
        }

        // Validation du prénom
        if (empty($data['prenom'])) {
            $errors[] = "Le prénom est obligatoire.";
        } else {
            $validated['prenom'] = trim($data['prenom']);
        }

        // Validation de l'email
        if (empty($data['email'])) {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } else {
            $validated['email'] = trim($data['email']);
        }

        // Validation du téléphone
        if (empty($data['telephone'])) {
            $errors[] = "Le numéro de téléphone est obligatoire.";
        } else {
            $validated['telephone'] = trim($data['telephone']);
        }

        // Validation de la date de naissance
        if (empty($data['date_naissance'])) {
            $errors[] = "La date de naissance est obligatoire.";
        } else {
            // Conversion d/m/Y vers Y-m-d si nécessaire
            $date = $data['date_naissance'];
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                $dt = DateTime::createFromFormat('d/m/Y', $date);
                if ($dt) {
                    $date = $dt->format('Y-m-d');
                }
            }
            $validated['date_naissance'] = $date;
        }


        // Validation du mot de passe (obligatoire seulement pour la création)
        if (!$isUpdate && empty($data['password'])) {
            $errors[] = "Le mot de passe est obligatoire.";
        } elseif (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            } else {
                $validated['password'] = $data['password'];
            }
        }

        $validated['errors'] = $errors;
        return $validated;
    }
}