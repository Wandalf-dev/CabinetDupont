<?php
namespace App\Controllers;

use App\Models\CreneauModel;
use App\Models\AgendaModel;

class CreneauxController {
    private $creneauModel;
    private $agendaModel;

    public function __construct() {
        $this->creneauModel = new CreneauModel();
        $this->agendaModel = new AgendaModel();
    }

    public function generer() {
        // Vérifier les droits d'accès (médecin uniquement)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer l'agenda du médecin
            $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
            
            if (!$agenda) {
                $_SESSION['error'] = "Aucun agenda trouvé pour ce médecin";
                header('Location: index.php?page=admin');
                exit();
            }

            // Récupérer les dates depuis le formulaire
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';

            // Valider les dates
            if (empty($dateDebut) || empty($dateFin)) {
                $_SESSION['error'] = "Les dates sont obligatoires";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            // Générer les créneaux
            $success = $this->creneauModel->genererCreneaux(
                $agenda['id'],
                $dateDebut,
                $dateFin
            );

            if ($success) {
                $_SESSION['success'] = "Les créneaux ont été générés avec succès";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la génération des créneaux";
            }

            header('Location: index.php?page=admin');
            exit();
        }

        // Afficher le formulaire de génération
        require_once 'app/views/creneaux/generer.php';
    }

    public function liste() {
        // Vérifier les droits d'accès
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        // Récupérer l'agenda du médecin
        $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
        
        if (!$agenda) {
            $_SESSION['error'] = "Aucun agenda trouvé pour ce médecin";
            header('Location: index.php?page=admin');
            exit();
        }

        // Récupérer les créneaux pour la semaine en cours
        $dateDebut = date('Y-m-d');
        $dateFin = date('Y-m-d', strtotime('+7 days'));
        
        $creneaux = $this->creneauModel->getCreneauxDisponibles(
            $agenda['id'],
            $dateDebut,
            $dateFin
        );

        // Afficher la liste des créneaux
        require_once 'app/views/creneaux/liste.php';
    }
}