<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AgendaModel;

class AgendaController extends Controller {
    private $agendaModel;

    public function __construct() {
        parent::__construct();
        $this->agendaModel = new AgendaModel();
    }

    /**
     * Affiche la vue du planning
     */
    public function planning() {
        // Vérifie si l'utilisateur est connecté et est un médecin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'MEDECIN') {
            $this->redirect('auth', 'login');
            return;
        }

        // Récupère les horaires d'ouverture du cabinet
        $horaireModel = new \App\Models\HoraireModel();
        $horaires = $horaireModel->getHoraires();

        // Récupère la liste des services pour la légende
        $serviceModel = new \App\Models\ServiceModel();
        $services = $serviceModel->getAllServices();

        // Prépare les données pour la vue
        $data = [
            'horaires' => $horaires,
            'services' => $services, // Liste des services avec leurs couleurs
            'jours_ouverture' => [], // Liste des jours où le cabinet est ouvert
            'heure_min' => 23, // Sera mis à jour avec l'heure d'ouverture la plus tôt
            'heure_max' => 0   // Sera mis à jour avec l'heure de fermeture la plus tard
        ];

        // Traite les horaires pour déterminer les jours d'ouverture et les heures
        foreach ($horaires as $horaire) {
            // Vérifie si le cabinet est ouvert ce jour-là (au moins un créneau défini)
            $has_morning = !empty($horaire['ouverture_matin']) && !empty($horaire['fermeture_matin']);
            $has_afternoon = !empty($horaire['ouverture_apresmidi']) && !empty($horaire['fermeture_apresmidi']);

            if ($has_morning || $has_afternoon) {
                // Ajoute le jour à la liste des jours d'ouverture
                $data['jours_ouverture'][] = strtolower($horaire['jour']);

                // Traite les horaires du matin
                if ($has_morning) {
                    $heure_debut = (int)substr($horaire['ouverture_matin'], 0, 2);
                    $heure_fin = (int)substr($horaire['fermeture_matin'], 0, 2);
                    if ($heure_debut > 0) { // Ignore les heures à 00:00
                        $data['heure_min'] = min($data['heure_min'], $heure_debut);
                    }
                    if ($heure_fin > 0) {
                        $data['heure_max'] = max($data['heure_max'], $heure_fin);
                    }
                }

                // Traite les horaires de l'après-midi
                if ($has_afternoon) {
                    $heure_debut = (int)substr($horaire['ouverture_apresmidi'], 0, 2);
                    $heure_fin = (int)substr($horaire['fermeture_apresmidi'], 0, 2);
                    if ($heure_debut > 0) { // Ignore les heures à 00:00
                        $data['heure_min'] = min($data['heure_min'], $heure_debut);
                    }
                    if ($heure_fin > 0) {
                        $data['heure_max'] = max($data['heure_max'], $heure_fin);
                    }
                }
            }
        }

        $this->view('agenda/planning', $data); // Retour à l'ancienne vue
    }

    /**
     * Récupère les rendez-vous pour une période donnée (appel AJAX)
     */
    public function getAppointments() {
        // Vérifie si l'utilisateur est connecté et est un médecin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'MEDECIN') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            return;
        }

        // Récupère les dates de début et de fin depuis la requête
        $dateDebut = $_GET['start'] ?? date('Y-m-d');
        $dateFin = $_GET['end'] ?? date('Y-m-d', strtotime('+7 days'));

        // Récupère l'agenda du médecin
        $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
        if (!$agenda) {
            http_response_code(404);
            echo json_encode(['error' => 'Agenda non trouvé']);
            return;
        }

        // Récupère les rendez-vous
        $appointments = $this->agendaModel->getRendezVousByPeriod($dateDebut, $dateFin, $agenda['id']);
        
        // Formater les rendez-vous pour le JavaScript
        $formattedAppointments = [];
        foreach ($appointments as $rdv) {
            error_log("Traitement du rendez-vous : " . print_r($rdv, true));
            error_log("Données du rendez-vous avant formatage : " . print_r($rdv, true));
            
            // Récupérer la durée du service
            $service_duree = isset($rdv['service_duree']) ? intval($rdv['service_duree']) : 30;
            $debut = new \DateTime($rdv['debut']);
            $fin = clone $debut;
            $fin->modify("+{$service_duree} minutes");

            error_log("Durée du service : {$service_duree} minutes");
            error_log("Début : {$rdv['debut']}, Fin calculée : {$fin->format('Y-m-d H:i:s')}");

            $formattedAppointments[] = [
                'id' => $rdv['rdv_id'],
                'start' => $debut->format('Y-m-d H:i:s'),
                'end' => $fin->format('Y-m-d H:i:s'),
                'title' => sprintf(
                    '%s %s - %s',
                    $rdv['patient_prenom'],
                    $rdv['patient_nom'],
                    $rdv['service_titre']
                ),
                'status' => $rdv['rdv_statut'],
                'couleur' => $rdv['service_couleur'] ?? '#4CAF50',
                'duree' => $service_duree,
                'patient' => [
                    'nom' => $rdv['patient_nom'],
                    'prenom' => $rdv['patient_prenom']
                ],
                'service' => [
                    'titre' => $rdv['service_titre'],
                    'couleur' => $rdv['service_couleur'] ?? '#4CAF50',
                    'duree' => $service_duree
                ]
            ];
        }
        
        error_log("Rendez-vous formatés : " . print_r($formattedAppointments, true));
        
        // Retourner les rendez-vous formatés
        header('Content-Type: application/json');
        echo json_encode($formattedAppointments);
    }

    /**
     * Met à jour le statut d'un rendez-vous (appel AJAX)
     */
    public function updateStatus() {
        // Vérifie si l'utilisateur est connecté et est un médecin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'MEDECIN') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $statut = $_POST['statut'] ?? null;

        if (!$id || !$statut) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants']);
            return;
        }

        $success = $this->agendaModel->updateRendezVousStatus($id, $statut);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Reprogramme un rendez-vous (appel AJAX)
     */
    public function reschedule() {
        // Vérifie si l'utilisateur est connecté et est un médecin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'MEDECIN') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $date = $_POST['date'] ?? null;
        $heureDebut = $_POST['heure_debut'] ?? null;
        $heureFin = $_POST['heure_fin'] ?? null;

        if (!$id || !$date || !$heureDebut || !$heureFin) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants']);
            return;
        }

        // Vérifie la disponibilité
        if (!$this->agendaModel->verifierDisponibilite($date, $heureDebut, $heureFin)) {
            http_response_code(409);
            echo json_encode(['error' => 'Créneau déjà occupé']);
            return;
        }

        $success = $this->agendaModel->reprogrammerRendezVous($id, $date, $heureDebut, $heureFin);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Annule un rendez-vous (appel AJAX)
     */
    public function cancel() {
        // Vérifie si l'utilisateur est connecté et est un docteur
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            return;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID du rendez-vous manquant']);
            return;
        }

        $success = $this->agendaModel->annulerRendezVous($id);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Ajoute une note à un rendez-vous (appel AJAX)
     */
    public function addNote() {
        // Vérifie si l'utilisateur est connecté et est un docteur
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $note = $_POST['note'] ?? null;

        if (!$id || !$note) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants']);
            return;
        }

        $success = $this->agendaModel->ajouterNote($id, $note);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }
}