<?php
namespace App\Models;
use App\Core\Model;

class AgendaModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Récupère l'agenda d'un utilisateur
     * @param int $utilisateurId ID de l'utilisateur
     * @return array|false Les informations de l'agenda ou false si non trouvé
     */
    public function getAgendaByUtilisateur($utilisateurId) {
        error_log("Recherche de l'agenda pour l'utilisateur " . $utilisateurId);
        $sql = "SELECT * FROM agenda WHERE utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$utilisateurId]);
        $result = $stmt->fetch();
        
        // Si aucun agenda n'existe, on en crée un
        if (!$result) {
            error_log("Aucun agenda trouvé, création d'un nouveau");
            if ($this->creerAgenda($utilisateurId)) {
                $stmt->execute([$utilisateurId]);
                $result = $stmt->fetch();
            }
        }
        
        error_log("Résultat final de la recherche d'agenda : " . print_r($result, true));
        return $result;
    }

    /**
     * Crée un nouvel agenda pour un utilisateur
     * @param int $utilisateurId ID de l'utilisateur
     * @return bool True si succès, false sinon
     */
    private function creerAgenda($utilisateurId) {
        $sql = "INSERT INTO agenda (utilisateur_id, titre) 
                SELECT ?, CONCAT('Agenda Dr. ', u.nom)
                FROM utilisateur u 
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$utilisateurId, $utilisateurId]);
    }

    /**
     * Récupère les rendez-vous pour une période donnée
     */
    public function getRendezVousByPeriod($dateDebut, $dateFin) {
        $sql = "SELECT rdv.*, 
                       p.nom as patient_nom, p.prenom as patient_prenom,
                       s.nom as service
                FROM agenda rdv
                LEFT JOIN patients p ON rdv.patient_id = p.id
                LEFT JOIN services s ON rdv.service_id = s.id
                WHERE rdv.date BETWEEN ? AND ?
                ORDER BY rdv.date ASC, rdv.heure_debut ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dateDebut, $dateFin]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les détails d'un rendez-vous spécifique
     */
    public function getRendezVousById($id) {
        $sql = "SELECT rdv.*, 
                       p.nom as patient_nom, p.prenom as patient_prenom,
                       s.nom as service
                FROM agenda rdv
                LEFT JOIN patients p ON rdv.patient_id = p.id
                LEFT JOIN services s ON rdv.service_id = s.id
                WHERE rdv.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Met à jour le statut d'un rendez-vous
     */
    public function updateRendezVousStatus($id, $statut) {
        $sql = "UPDATE agenda SET statut = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$statut, $id]);
    }

    /**
     * Reprogramme un rendez-vous
     */
    public function reprogrammerRendezVous($id, $date, $heureDebut, $heureFin) {
        $sql = "UPDATE agenda 
                SET date = ?, heure_debut = ?, heure_fin = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$date, $heureDebut, $heureFin, $id]);
    }

    /**
     * Annule un rendez-vous
     */
    public function annulerRendezVous($id) {
        $sql = "UPDATE agenda SET statut = 'annule' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Ajoute une note à un rendez-vous
     */
    public function ajouterNote($id, $note) {
        $sql = "UPDATE agenda SET notes = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$note, $id]);
    }

    /**
     * Vérifie les disponibilités pour une plage horaire
     */
    public function verifierDisponibilite($date, $heureDebut, $heureFin) {
        $sql = "SELECT COUNT(*) FROM agenda 
                WHERE date = ? 
                AND ((heure_debut BETWEEN ? AND ?) 
                     OR (heure_fin BETWEEN ? AND ?))
                AND statut != 'annule'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date, $heureDebut, $heureFin, $heureDebut, $heureFin]);
        return $stmt->fetchColumn() == 0;
    }
}