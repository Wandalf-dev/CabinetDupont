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

    public function deleteMultiple() {
        // Vérifier les droits d'accès (médecin uniquement)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Token de sécurité invalide";
            header('Location: index.php?page=admin');
            exit();
        }

        // Récupérer les IDs des créneaux à supprimer
        $creneauxIds = isset($_POST['creneaux']) ? array_map('intval', $_POST['creneaux']) : [];
        
        if (empty($creneauxIds)) {
            $_SESSION['error'] = "Aucun créneau sélectionné";
            header('Location: index.php?page=admin');
            exit();
        }

        // Supprimer les créneaux
        $supprimés = 0;
        foreach ($creneauxIds as $id) {
            $creneau = $this->creneauModel->getCreneauById($id);
            if ($creneau && !$creneau['est_reserve'] && $this->creneauModel->deleteCreneau($id)) {
                $supprimés++;
            }
        }

        if ($supprimés > 0) {
            $_SESSION['success'] = "$supprimés créneau(x) supprimé(s) avec succès";
        } else {
            $_SESSION['error'] = "Aucun créneau n'a pu être supprimé";
        }

        header('Location: index.php?page=admin');
        exit();
    }

    public function delete() {
        // Vérifier les droits d'accès (médecin uniquement)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id > 0) {
            // Vérifier que le créneau existe et n'est pas réservé
            $creneau = $this->creneauModel->getCreneauById($id);
            if (!$creneau) {
                $_SESSION['error'] = "Ce créneau n'existe pas";
                header('Location: index.php?page=admin');
                exit();
            }

            if ($creneau['est_reserve']) {
                $_SESSION['error'] = "Impossible de supprimer un créneau réservé";
                header('Location: index.php?page=admin');
                exit();
            }

            // Supprimer le créneau
            if ($this->creneauModel->deleteCreneau($id)) {
                $_SESSION['success'] = "Le créneau a été supprimé avec succès";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la suppression du créneau";
            }
        } else {
            $_SESSION['error'] = "ID de créneau invalide";
        }

        header('Location: index.php?page=admin');
        exit();
    }

    public function generer() {
        // Vérifier les droits d'accès (médecin uniquement)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Demande de génération de créneaux reçue");
            error_log("Utilisateur ID: " . $_SESSION['user_id']);

            // Récupérer l'agenda du médecin
            $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
            
            if (!$agenda) {
                error_log("Erreur: Aucun agenda trouvé pour l'utilisateur " . $_SESSION['user_id']);
                $_SESSION['error'] = "Aucun agenda trouvé pour ce médecin";
                header('Location: index.php?page=admin');
                exit();
            }
            
            error_log("Agenda trouvé : " . print_r($agenda, true));

            // Récupérer et valider les dates depuis le formulaire
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            
            error_log("Dates reçues - Début: $dateDebut, Fin: $dateFin");

            // Vérifier si des créneaux existent déjà pour cette période
            $creneauxExistants = $this->creneauModel->getCreneauxPourPeriode($dateDebut, $dateFin, $agenda['id']);
            if (!empty($creneauxExistants)) {
                $_SESSION['error'] = "Des créneaux existent déjà pour cette période. Veuillez d'abord les supprimer ou choisir une autre période.";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            // Calculer le nombre approximatif de créneaux qui seront générés
            $dateDebutObj = new \DateTime($dateDebut);
            $dateFinObj = new \DateTime($dateFin);
            $interval = $dateDebutObj->diff($dateFinObj);
            $nombreJours = $interval->days + 1;
            
            // 20 créneaux par jour (8 le matin + 12 l'après-midi)
            $nombreCreneauxEstime = $nombreJours * 20;

            if ($nombreCreneauxEstime > 200) { // Limite arbitraire de 200 créneaux
                $_SESSION['warning'] = "Attention : Vous allez générer environ {$nombreCreneauxEstime} créneaux. 
                    Veuillez réduire la période pour une meilleure gestion.";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            // Valider les dates
            if (empty($dateDebut) || empty($dateFin)) {
                $_SESSION['error'] = "Les dates sont obligatoires";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

            error_log("Tentative de génération des créneaux pour l'agenda " . $agenda['id']);
            error_log("Date de début : " . $dateDebut);
            error_log("Date de fin : " . $dateFin);

            // Générer les créneaux
            $success = $this->creneauModel->genererCreneaux(
                $agenda['id'],
                $dateDebut,
                $dateFin
            );

            if ($success) {
                $_SESSION['success'] = "Les créneaux ont été générés avec succès";
                error_log("Génération des créneaux réussie");
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la génération des créneaux";
                error_log("Échec de la génération des créneaux");
            }

            header('Location: index.php?page=admin');
            exit();
        }

        // Afficher le formulaire de génération
        require_once 'app/views/creneaux/generer.php';
    }

    public function liste() {
        error_log("=== Début méthode liste() ===");
        // Vérifier les droits d'accès
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            error_log("Accès non autorisé - user_id: " . ($_SESSION['user_id'] ?? 'non défini') . ", role: " . ($_SESSION['user_role'] ?? 'non défini'));
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php');
            exit();
        }

        error_log("Utilisateur connecté ID: " . $_SESSION['user_id']);

        // Récupérer l'agenda du médecin
        $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
        error_log("Agenda récupéré : " . print_r($agenda, true));
        
        if (!$agenda) {
            error_log("Aucun agenda trouvé pour le médecin");
            $_SESSION['error'] = "Aucun agenda trouvé pour ce médecin";
            header('Location: index.php?page=admin');
            exit();
        }

        // Récupérer la date du filtre ou utiliser la date du jour
        $dateFiltre = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        error_log("Date filtre : " . $dateFiltre);
        
        // Récupérer tous les créneaux pour la date spécifiée
        $creneaux = $this->creneauModel->getCreneauxPourDate(
            $agenda['id'],
            $dateFiltre
        );
        
        error_log("Nombre de créneaux trouvés : " . count($creneaux));
        if (!empty($creneaux)) {
            error_log("Premier créneau : " . print_r($creneaux[0], true));
        }

        // Afficher la liste des créneaux
        require_once 'app/views/creneaux/liste.php';
    }
}