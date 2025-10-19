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
        $sql = "SELECT * FROM agenda WHERE utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$utilisateurId]);
        $result = $stmt->fetch();
        
        // Si aucun agenda n'existe, on en crée un
        if (!$result) {
            if ($this->creerAgenda($utilisateurId)) {
                $stmt->execute([$utilisateurId]);
                $result = $stmt->fetch();
            }
        }
        
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
    public function getRendezVousByPeriod($dateDebut, $dateFin, $agendaId) {
        
        $sql = "SELECT 
                       c.id as creneau_id,
                       c.debut, 
                       c.fin, 
                       c.est_reserve,
                       c.service_id,
                       r.id as rdv_id,
                       r.duree as rdv_duree,
                       s.titre as service_titre, 
                       s.duree as service_duree,
                       s.couleur as service_couleur,
                       u.nom as patient_nom, 
                       u.prenom as patient_prenom,
                       r.statut as rdv_statut,
                       TIMESTAMPDIFF(MINUTE, c.debut, c.fin) as duree_calculee
                FROM creneau c
                INNER JOIN rendezvous r ON c.id = r.creneau_id
                LEFT JOIN service s ON c.service_id = s.id
                LEFT JOIN utilisateur u ON r.patient_id = u.id
                WHERE c.agenda_id = ? 
                AND DATE(c.debut) BETWEEN ? AND ?
                AND r.statut != 'ANNULE'
                ORDER BY c.debut ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        $results = $stmt->fetchAll();
        
        foreach ($results as $result) {
            error_log(sprintf(
                "RDV trouvé - ID: %d, Service: %s, Durée service: %d min, Durée calculée: %d min, Début: %s, Fin: %s",
                $result['rdv_id'],
                $result['service_titre'],
                $result['service_duree'],
                $result['duree_calculee'],
                $result['debut'],
                $result['fin']
            ));
        }
        
        return $results;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        $results = $stmt->fetchAll();
        
        return $results;
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

    /**
     * Récupère les horaires d'ouverture du cabinet
     */
    public function getHorairesCabinet() {
        $sql = "SELECT jour, ouverture_matin, fermeture_matin, ouverture_apresmidi, fermeture_apresmidi 
                FROM horaire_cabinet 
                ORDER BY FIELD(jour, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}