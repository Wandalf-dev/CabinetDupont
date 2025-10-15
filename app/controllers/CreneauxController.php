<?php
namespace App\Controllers;

use App\Models\CreneauModel;
use App\Models\AgendaModel;

class CreneauxController {
    private $creneauModel;
    private $agendaModel;

    public function toggleIndisponible() {
        // Vérifier les droits d'accès (médecin uniquement)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès non autorisé']);
            exit();
        }

        // Récupérer l'ID du créneau depuis la requête POST
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $id = $data['id'] ?? 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID de créneau invalide']);
            exit();
        }

        // Vérifier que le créneau existe et n'est pas réservé
        $success = $this->creneauModel->toggleIndisponible($id);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Statut du créneau modifié avec succès']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Impossible de modifier le statut du créneau']);
        }
        exit();
    }

    public function __construct() {
        $this->creneauModel = new CreneauModel();
        $this->agendaModel = new AgendaModel();
    }

    public function deleteMultiple() {
        // Vérifier les droits d'accès (médecin uniquement)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'MEDECIN') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            exit();
        }

        // Récupérer et décoder les données JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['ids']) || !is_array($data['ids'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données invalides']);
            exit();
        }

        // Nettoyer et valider les IDs
        $creneauxIds = array_map('intval', $data['ids']);
        
        if (empty($creneauxIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'Aucun créneau sélectionné']);
            exit();
        }

        // Supprimer les créneaux
        $supprimés = 0;
        $erreurs = [];

        foreach ($creneauxIds as $id) {
            $creneau = $this->creneauModel->getCreneauById($id);
            
            if (!$creneau) {
                $erreurs[] = "Créneau $id introuvable";
                continue;
            }

            if ($creneau['est_reserve']) {
                $erreurs[] = "Créneau $id est déjà réservé";
                continue;
            }

            if ($this->creneauModel->deleteCreneau($id)) {
                $supprimés++;
            } else {
                $erreurs[] = "Erreur lors de la suppression du créneau $id";
            }
        }

        // Préparer la réponse
        $response = [
            'success' => $supprimés > 0,
            'message' => $supprimés > 0 ? "$supprimés créneau(x) supprimé(s) avec succès" : "Aucun créneau n'a pu être supprimé",
            'deletedCount' => $supprimés
        ];

        if (!empty($erreurs)) {
            $response['errors'] = $erreurs;
        }

        // Envoyer la réponse JSON
        header('Content-Type: application/json');
        echo json_encode($response);
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
            error_log("=== DEBUG: Début de la génération des créneaux ===");
            error_log("POST reçu : " . print_r($_POST, true));
            
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            $confirmedDateDebut = $_POST['confirmed_date_debut'] ?? '';
            $confirmedDateFin = $_POST['confirmed_date_fin'] ?? '';
            
            error_log("Dates reçues - Début: $dateDebut, Fin: $dateFin");
            error_log("Dates confirmées - Début: $confirmedDateDebut, Fin: $confirmedDateFin");
            
            if (empty($dateDebut) || empty($dateFin) || empty($confirmedDateDebut) || empty($confirmedDateFin)) {
                error_log("ERREUR: Dates manquantes dans le formulaire");
                $_SESSION['error'] = "Les dates sont obligatoires";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }
            
            // Vérifier que les dates correspondent
            if ($dateDebut !== $confirmedDateDebut || $dateFin !== $confirmedDateFin) {
                error_log("ERREUR: Les dates ne correspondent pas aux dates confirmées");
                $_SESSION['error'] = "Une erreur est survenue lors de la validation des dates";
                header('Location: index.php?page=creneaux&action=generer');
                exit();
            }

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

            // On affiche juste une information sur le nombre de créneaux qui seront générés
            $_SESSION['info'] = "Vous allez générer environ {$nombreCreneauxEstime} créneaux.";

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
            error_log("Tentative de génération des créneaux avec les paramètres suivants :");
            error_log("Agenda ID : " . $agenda['id']);
            error_log("Date début : " . $dateDebut);
            error_log("Date fin : " . $dateFin);

            try {
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
            } catch (\Exception $e) {
                error_log("Exception lors de la génération des créneaux : " . $e->getMessage());
                $_SESSION['error'] = "Une erreur inattendue est survenue";
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