<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class RendezVousModel extends Model {
    public function updateStatus($id, $status) {
        try {
            $query = "UPDATE rendezvous SET statut = :status WHERE id = :id";
            $stmt = $this->db->prepare($query);
            
            $result = $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);

            if (!$result) {
                error_log("Erreur SQL lors de la mise à jour du statut: " . implode(", ", $stmt->errorInfo()));
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut du rendez-vous : " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getRendezVousById($id) {
        try {
            $query = "SELECT r.*, r.patient_id as patient_user_id,
                            c.debut, c.fin
                     FROM rendezvous r 
                     JOIN creneau c ON r.creneau_id = c.id
                     WHERE r.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                error_log("Données du rendez-vous récupérées: " . print_r($result, true));
            } else {
                error_log("Aucun rendez-vous trouvé avec l'ID: " . $id);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du rendez-vous : " . $e->getMessage());
            throw $e;
        }
    }

    public function annulerRendezVous($rdvId) {
        error_log("=== Début annulation du rendez-vous ===");
        $this->db->beginTransaction();
        
        try {
            // 1. Récupérer les informations du rendez-vous et du créneau
            $sql = "SELECT r.id, c.debut, s.duree 
                   FROM rendezvous r 
                   JOIN creneau c ON r.creneau_id = c.id 
                   JOIN service s ON c.service_id = s.id 
                   WHERE r.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId]);
            $rdv = $stmt->fetch();
            
            if (!$rdv) {
                throw new \Exception("Rendez-vous introuvable");
            }

            error_log("Rendez-vous trouvé : " . print_r($rdv, true));

            // 2. Marquer le rendez-vous comme annulé
            $sql = "UPDATE rendezvous SET statut = 'ANNULE' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$rdvId]);

            if (!$success) {
                throw new \Exception("Erreur lors de l'annulation du rendez-vous");
            }

            // 3. Libérer tous les créneaux associés
            $sql = "UPDATE creneau 
                   SET est_reserve = 0, 
                       service_id = NULL, 
                       statut = 'disponible' 
                   WHERE DATE(debut) = DATE(?) 
                   AND TIME(debut) >= TIME(?) 
                   AND TIME(debut) < TIMESTAMPADD(MINUTE, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                $rdv['debut'],
                $rdv['debut'],
                $rdv['duree'],
                $rdv['debut']
            ]);

            error_log("Mise à jour des créneaux - Succès: " . ($success ? 'true' : 'false'));

            // Valider la transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // En cas d'erreur, annuler toutes les modifications
            $this->db->rollBack();
            error_log("Erreur lors de l'annulation du rendez-vous : " . $e->getMessage());
            return false;
        }
    }

    public function modifierHeure($rdvId, $nouvelleDate, $nouvelleHeure) {
        error_log("=== Début modifierHeure dans le modèle ===");
        error_log("Paramètres reçus - ID: {$rdvId}, Date: {$nouvelleDate}, Heure: {$nouvelleHeure}");
        
        $this->db->beginTransaction();
        try {
            // 1. Récupérer les informations du rendez-vous actuel
            $sql = "SELECT c.*, s.duree 
                   FROM creneau c 
                   INNER JOIN rendezvous r ON c.id = r.creneau_id 
                   LEFT JOIN service s ON c.service_id = s.id 
                   WHERE r.id = ?";
            error_log("Exécution de la requête: " . $sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId]);
            $rendezvous = $stmt->fetch();

            if (!$rendezvous) {
                throw new \Exception("Rendez-vous introuvable");
            }

            // 2. Créer la nouvelle date complète
            $nouvelleDateTime = $nouvelleDate . ' ' . $nouvelleHeure . ':00';

            // 3. Vérifier si le créneau est disponible
            $sql = "SELECT COUNT(*) FROM creneau c 
                   INNER JOIN rendezvous r ON c.id = r.creneau_id 
                   WHERE DATE(c.debut) = ? 
                   AND TIME(c.debut) = ? 
                   AND r.statut != 'ANNULE'
                   AND c.id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$nouvelleDate, $nouvelleHeure . ':00', $rendezvous['id']]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception("Ce créneau est déjà occupé");
            }

            // 4. Mettre à jour le créneau
            $sql = "UPDATE creneau 
                   SET debut = ?, 
                       fin = DATE_ADD(?, INTERVAL ? MINUTE) 
                   WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $duree = $rendezvous['duree'] ?? 30;
            $success = $stmt->execute([
                $nouvelleDateTime, 
                $nouvelleDateTime, 
                $duree,
                $rendezvous['id']
            ]);

            if (!$success) {
                throw new \Exception("Erreur lors de la modification de l'heure");
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur lors de la modification de l'heure du rendez-vous : " . $e->getMessage());
            return false;
        }
    }
}