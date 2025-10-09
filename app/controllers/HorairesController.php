<?php

namespace App\Controllers;

use App\Models\HoraireModel;
use App\Core\Csrf;

// Contrôleur pour la gestion des horaires du cabinet
class HorairesController {
    private $horaireModel;

    // Constructeur : instancie le modèle des horaires
    public function __construct() {
        $this->horaireModel = new HoraireModel();
    }

    // Vérifie si l'utilisateur a les droits d'accès admin (médecin ou secrétaire)
    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }
    }

    // Affiche les horaires du cabinet (vue publique)
    public function index() {
        $horaires = $this->horaireModel->getHoraires();
        require_once 'app/views/horaires/horaires.php';
    }

    // Permet à l'admin de modifier les horaires du cabinet
    public function edit() {
        // Vérifie les droits d'accès admin
        $this->checkAdminAccess();
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF pour la sécurité
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!Csrf::checkToken($csrf_token)) {
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=horaires');
                exit();
            }

            // Récupère les horaires envoyés par le formulaire
            $horaires = $_POST['horaires'] ?? [];

            // Vérifie qu'au moins un horaire est saisi
            $horairesSaisis = false;
            foreach ($horaires as $jour => $creneaux) {
                foreach (['matin', 'apresmidi'] as $periode) {
                    if (isset($creneaux[$periode]) && 
                        (!empty($creneaux[$periode]['ouverture']) || !empty($creneaux[$periode]['fermeture']))) {
                        $horairesSaisis = true;
                        break 2;
                    }
                }
            }
            if (!$horairesSaisis) {
                $_SESSION['error'] = "Veuillez saisir au moins un horaire d'ouverture";
                header('Location: index.php?page=admin&tab=horaires');
                exit();
            }

            // Validation des horaires pour chaque jour et chaque période
            foreach ($horaires as $jour => $creneaux) {
                foreach (['matin', 'apresmidi'] as $periode) {
                    if (isset($creneaux[$periode])) {
                        $ouverture = $creneaux[$periode]['ouverture'] ?? '';
                        $fermeture = $creneaux[$periode]['fermeture'] ?? '';

                        // Normalise le format des heures (ajoute les secondes si besoin)
                        if (!empty($ouverture) && strlen($ouverture) === 5) {
                            $ouverture .= ':00';
                        }
                        if (!empty($fermeture) && strlen($fermeture) === 5) {
                            $fermeture .= ':00';
                        }

                        // Détecte si la période est fermée (00:00 ou vide)
                        $isFerme = (empty($ouverture) && empty($fermeture)) || 
                                   ($ouverture === '00:00:00' && $fermeture === '00:00:00') ||
                                   ($ouverture === '00:00' && $fermeture === '00:00');
                        if ($isFerme) {
                            continue;
                        }

                        // Vérifie que les deux horaires sont remplis ou vides
                        if (empty($ouverture) xor empty($fermeture)) {
                            $periodeLabel = $periode === 'matin' ? 'du matin' : "de l'après-midi";
                            $_SESSION['error'] = "Les horaires " . $periodeLabel . " pour " . ucfirst($jour) . 
                                               " doivent être complets (ouverture ET fermeture)";
                            header('Location: index.php?page=admin&tab=horaires');
                            exit();
                        }

                        // Vérifie que la fermeture est après l'ouverture
                        if (!empty($ouverture) && !empty($fermeture) && !$isFerme) {
                            if (strtotime($fermeture) <= strtotime($ouverture)) {
                                $periodeLabel = $periode === 'matin' ? 'du matin' : "de l'après-midi";
                                $_SESSION['error'] = "L'heure de fermeture " . $periodeLabel . " doit être après " .
                                                   "l'heure d'ouverture pour " . ucfirst($jour);
                                header('Location: index.php?page=admin&tab=horaires');
                                exit();
                            }
                        }
                    }
                }
            }

            // Met à jour les horaires en base de données
            if ($this->horaireModel->updateHoraire($horaires)) {
                $_SESSION['success'] = "Les horaires ont été mis à jour avec succès";
                header('Location: index.php?page=admin&tab=horaires');
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour des horaires";
                header('Location: index.php?page=admin&tab=horaires');
            }
            exit();
        }

        // Affiche le formulaire d'édition des horaires
        $horaires = $this->horaireModel->getHoraires();
        require_once 'app/views/horaires/edit.php';
    }
}