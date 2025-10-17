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
            $sql = "SELECT r.id, r.creneau_id, c.debut, c.agenda_id, c.service_id, s.duree 
                   FROM rendezvous r 
                   JOIN creneau c ON r.creneau_id = c.id 
                   LEFT JOIN service s ON c.service_id = s.id 
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
            // Si le service a une durée, on libère tous les créneaux consécutifs
            $duree = $rdv['duree'] ?? 30; // Par défaut 30 minutes si pas de service
            
            $sql = "SELECT id 
                   FROM creneau 
                   WHERE agenda_id = ? 
                   AND debut >= ? 
                   AND debut < DATE_ADD(?, INTERVAL ? MINUTE)
                   AND service_id = ?
                   ORDER BY debut ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $rdv['agenda_id'],
                $rdv['debut'],
                $rdv['debut'],
                $duree,
                $rdv['service_id']
            ]);
            $creneauxALiberer = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($creneauxALiberer)) {
                $placeholders = implode(',', array_fill(0, count($creneauxALiberer), '?'));
                $sql = "UPDATE creneau 
                       SET est_reserve = 0, 
                           service_id = NULL 
                       WHERE id IN ($placeholders)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($creneauxALiberer);
                error_log("Créneaux libérés : " . count($creneauxALiberer));
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
            $sql = "SELECT r.id as rdv_id, r.creneau_id, r.patient_id, c.*, s.duree, s.id as service_id, c.agenda_id
                   FROM rendezvous r
                   INNER JOIN creneau c ON r.creneau_id = c.id 
                   LEFT JOIN service s ON c.service_id = s.id 
                   WHERE r.id = ? AND r.statut != 'ANNULE'";
            error_log("Exécution de la requête: " . $sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId]);
            $rendezvous = $stmt->fetch();

            if (!$rendezvous) {
                throw new \Exception("Rendez-vous introuvable");
            }

            $duree = $rendezvous['duree'] ?? 30;
            $nbCreneauxNecessaires = (int)ceil($duree / 30);
            $nouvelleDateTime = $nouvelleDate . ' ' . $nouvelleHeure . ':00';

            // 2. Trouver le créneau de départ correspondant à la nouvelle heure
            $sql = "SELECT id, debut, fin, statut, est_reserve, agenda_id 
                   FROM creneau 
                   WHERE agenda_id = ? 
                   AND DATE(debut) = ? 
                   AND TIME(debut) = ? 
                   LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rendezvous['agenda_id'], $nouvelleDate, $nouvelleHeure . ':00']);
            $nouveauCreneau = $stmt->fetch();

            if (!$nouveauCreneau) {
                throw new \Exception("Aucun créneau disponible à cette heure. Vérifiez les horaires du cabinet.");
            }

            // 3. Vérifier que le créneau de départ n'est pas indisponible
            if ($nouveauCreneau['statut'] === 'indisponible') {
                throw new \Exception("Ce créneau est marqué comme indisponible");
            }

            // 4. Vérifier qu'il n'est pas déjà réservé (sauf si c'est le même rendez-vous qu'on déplace)
            if ($nouveauCreneau['est_reserve'] == 1 && $nouveauCreneau['id'] != $rendezvous['creneau_id']) {
                throw new \Exception("Ce créneau est déjà réservé");
            }

            // 5. Vérifier la disponibilité de tous les créneaux consécutifs nécessaires
            $sql = "SELECT id, debut, statut, est_reserve 
                   FROM creneau 
                   WHERE agenda_id = ? 
                   AND debut >= ? 
                   AND debut < DATE_ADD(?, INTERVAL ? MINUTE) 
                   ORDER BY debut ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $rendezvous['agenda_id'], 
                $nouvelleDateTime, 
                $nouvelleDateTime, 
                $duree
            ]);
            $creneauxConsecutifs = $stmt->fetchAll();

            if (count($creneauxConsecutifs) < $nbCreneauxNecessaires) {
                throw new \Exception("Créneaux insuffisants pour ce service (durée: {$duree}min). Il manque " . ($nbCreneauxNecessaires - count($creneauxConsecutifs)) . " créneau(x).");
            }

            // Vérifier que tous les créneaux nécessaires sont disponibles
            foreach ($creneauxConsecutifs as $creneau) {
                if ($creneau['statut'] === 'indisponible') {
                    throw new \Exception("Un des créneaux nécessaires est indisponible");
                }
                // Permettre le créneau actuel du rendez-vous, mais pas les autres réservés
                if ($creneau['est_reserve'] == 1 && $creneau['id'] != $rendezvous['creneau_id']) {
                    throw new \Exception("Un des créneaux nécessaires est déjà réservé");
                }
            }

            // 6. Vérifier qu'il n'y a pas de chevauchement avec d'autres rendez-vous
            $sql = "SELECT COUNT(*) 
                   FROM rendezvous r
                   INNER JOIN creneau c ON r.creneau_id = c.id
                   WHERE r.statut != 'ANNULE'
                   AND r.id != ?
                   AND DATE(c.debut) = ?
                   AND (
                       (c.debut >= ? AND c.debut < DATE_ADD(?, INTERVAL ? MINUTE))
                       OR (c.debut < ? AND DATE_ADD(c.debut, INTERVAL ? MINUTE) > ?)
                   )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $rdvId,
                $nouvelleDate,
                $nouvelleDateTime, $nouvelleDateTime, $duree,
                $nouvelleDateTime, $duree, $nouvelleDateTime
            ]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception("Un autre rendez-vous chevauche cet horaire");
            }

            // 7. Libérer l'ancien créneau et tous les créneaux consécutifs associés
            // Récupérer tous les créneaux consécutifs de l'ancien rendez-vous
            $sql = "SELECT id 
                   FROM creneau 
                   WHERE agenda_id = ? 
                   AND debut >= (SELECT debut FROM creneau WHERE id = ?)
                   AND debut < DATE_ADD((SELECT debut FROM creneau WHERE id = ?), INTERVAL ? MINUTE)
                   AND service_id = ?
                   ORDER BY debut ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $rendezvous['agenda_id'], 
                $rendezvous['creneau_id'], 
                $rendezvous['creneau_id'], 
                $duree,
                $rendezvous['service_id']
            ]);
            $anciensCreneaux = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            if (!empty($anciensCreneaux)) {
                $placeholders = implode(',', array_fill(0, count($anciensCreneaux), '?'));
                $sql = "UPDATE creneau 
                       SET est_reserve = 0, 
                           service_id = NULL 
                       WHERE id IN ($placeholders)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($anciensCreneaux);
            }

            // 8. Mettre à jour le rendez-vous avec le nouveau créneau
            $sql = "UPDATE rendezvous 
                   SET creneau_id = ? 
                   WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$nouveauCreneau['id'], $rdvId]);

            if (!$success) {
                throw new \Exception("Erreur lors de la modification du rendez-vous");
            }

            // 9. Marquer tous les créneaux consécutifs nécessaires comme réservés
            // On récupère les IDs de tous les créneaux consécutifs vérifiés en étape 5
            $creneauxIds = array_column($creneauxConsecutifs, 'id');
            $placeholders = implode(',', array_fill(0, count($creneauxIds), '?'));
            
            $sql = "UPDATE creneau 
                   SET est_reserve = 1, 
                       service_id = ? 
                   WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            $params = array_merge([$rendezvous['service_id']], $creneauxIds);
            $stmt->execute($params);

            $this->db->commit();
            error_log("Modification réussie - Ancien créneau: {$rendezvous['creneau_id']}, Nouveau créneau: {$nouveauCreneau['id']}, Service: {$rendezvous['service_id']}, Nb créneaux: " . count($creneauxIds));
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur lors de la modification de l'heure du rendez-vous : " . $e->getMessage());
            throw $e; // Relancer l'exception pour que le contrôleur puisse récupérer le message
        }
    }
}