<?php

namespace App\Controllers;

use App\Models\HoraireModel;
use App\Core\Csrf;

class HorairesController {
    private $horaireModel;

    public function __construct() {
        $this->horaireModel = new HoraireModel();
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')) {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }
    }

    public function index() {
        $horaires = $this->horaireModel->getHoraires();
        require_once 'app/views/horaires/horaires.php';
    }

    public function edit() {
        $this->checkAdminAccess();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug détaillé
            error_log('======= DÉBUT DEBUG HORAIRES =======');
            error_log('POST data brute: ' . print_r($_POST, true));
            error_log('Type de $_POST[\'horaires\']: ' . gettype($_POST['horaires']));
            error_log('Contenu de $_POST[\'horaires\']: ' . print_r($_POST['horaires'], true));
            
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!Csrf::checkToken($csrf_token)) {
                $_SESSION['error'] = "Session expirée ou tentative frauduleuse.";
                header('Location: index.php?page=horaires');
                exit();
            }

            $horaires = $_POST['horaires'] ?? [];

            // Vérifier si au moins un horaire est rempli
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

            // Valider les horaires qui ont été saisis
            foreach ($horaires as $jour => $creneaux) {
                foreach (['matin', 'apresmidi'] as $periode) {
                    if (isset($creneaux[$periode])) {
                        $ouverture = $creneaux[$periode]['ouverture'] ?? '';
                        $fermeture = $creneaux[$periode]['fermeture'] ?? '';

                        error_log("=== Traitement de $jour - $periode ===");
                        error_log("Ouverture: '" . var_export($ouverture, true) . "'");
                        error_log("Fermeture: '" . var_export($fermeture, true) . "'");
                        error_log("empty(ouverture): " . var_export(empty($ouverture), true));
                        error_log("empty(fermeture): " . var_export(empty($fermeture), true));

                        // Pour le débogage, regardons la valeur exacte
                        if ($jour === 'samedi' && $periode === 'apresmidi') {
                            error_log("=== DÉTAIL SAMEDI APRÈS-MIDI ===");
                            error_log("Type ouverture: " . gettype($ouverture));
                            error_log("Type fermeture: " . gettype($fermeture));
                            error_log("Longueur ouverture: " . strlen($ouverture));
                            error_log("Longueur fermeture: " . strlen($fermeture));
                            error_log("Ouverture (hex): " . bin2hex($ouverture));
                            error_log("Fermeture (hex): " . bin2hex($fermeture));
                        }

                        // Normaliser les formats d'heure
                        if (!empty($ouverture) && strlen($ouverture) === 5) {
                            $ouverture .= ':00';
                        }
                        if (!empty($fermeture) && strlen($fermeture) === 5) {
                            $fermeture .= ':00';
                        }

                        // Détecter si c'est une période fermée
                        $isFerme = (empty($ouverture) && empty($fermeture)) || 
                                 ($ouverture === '00:00:00' && $fermeture === '00:00:00') ||
                                 ($ouverture === '00:00' && $fermeture === '00:00');
                        
                        if ($isFerme) {
                            error_log("$jour $periode - Période considérée comme fermée");
                            continue;
                        }

                        // Si un seul horaire est rempli
                        if (empty($ouverture) xor empty($fermeture)) {
                            $periodeLabel = $periode === 'matin' ? 'du matin' : "de l'après-midi";
                            $_SESSION['error'] = "Les horaires " . $periodeLabel . " pour " . ucfirst($jour) . 
                                               " doivent être complets (ouverture ET fermeture)";
                            error_log("Erreur: horaires incomplets pour $jour $periode");
                            header('Location: index.php?page=admin&tab=horaires');
                            exit();
                        }

                        // Si les horaires sont remplis et que ce n'est pas une période fermée
                        if (!empty($ouverture) && !empty($fermeture) && !$isFerme) {
                            // Vérifier que l'heure de fermeture est après l'heure d'ouverture
                            if (strtotime($fermeture) <= strtotime($ouverture)) {
                                $periodeLabel = $periode === 'matin' ? 'du matin' : "de l'après-midi";
                                error_log("Erreur validation horaire: $jour $periode - fermeture ($fermeture) <= ouverture ($ouverture)");
                                $_SESSION['error'] = "L'heure de fermeture " . $periodeLabel . " doit être après " .
                                                   "l'heure d'ouverture pour " . ucfirst($jour);
                                header('Location: index.php?page=admin&tab=horaires');
                                exit();
                            }
                        }
                    }
                }
            }

            if ($this->horaireModel->updateHoraire($horaires)) {
                $_SESSION['success'] = "Les horaires ont été mis à jour avec succès";
                header('Location: index.php?page=admin&tab=horaires');
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour des horaires";
                header('Location: index.php?page=admin&tab=horaires');
            }
            exit();
        }

        $horaires = $this->horaireModel->getHoraires();
        require_once 'app/views/horaires/edit.php';
    }
}