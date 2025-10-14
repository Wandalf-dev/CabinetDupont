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
            $query = "SELECT r.*, r.patient_id as patient_user_id 
                     FROM rendezvous r 
                     WHERE r.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du rendez-vous : " . $e->getMessage());
            throw $e;
        }
    }

    public function annulerRendezVous($rdvId) {
        // Commencer une transaction
        $this->db->beginTransaction();
        
        try {
            // 1. Mettre à jour le statut du rendez-vous
            $sql = "UPDATE rendezvous SET statut = 'ANNULE' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$rdvId]);

            if (!$success) {
                throw new \Exception("Erreur lors de l'annulation du rendez-vous");
            }

            // 2. Récupérer le créneau_id associé
            $sql = "SELECT creneau_id FROM rendezvous WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId]);
            $creneau = $stmt->fetch();

            if (!$creneau) {
                throw new \Exception("Créneau introuvable");
            }

            // 3. Libérer le créneau en le marquant comme non réservé
            $sql = "UPDATE creneau SET est_reserve = 0, service_id = NULL WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$creneau['creneau_id']]);

            if (!$success) {
                throw new \Exception("Erreur lors de la libération du créneau");
            }

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