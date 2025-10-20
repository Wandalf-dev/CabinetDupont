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
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
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
            } else {
            }
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function annulerRendezVous($rdvId) {
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


            // 2. Marquer le rendez-vous comme annulé (on garde l'historique)
            $sql = "UPDATE rendezvous SET statut = 'ANNULE' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$rdvId]);
            $rowsAffected = $stmt->rowCount();

            if (!$success) {
                throw new \Exception("Erreur lors de l'annulation du rendez-vous: échec de l'UPDATE");
            }
            
            if ($rowsAffected === 0) {
                throw new \Exception("Aucun rendez-vous n'a été modifié (ID: $rdvId). Vérifiez que le RDV existe.");
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
            }

            // Valider la transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // En cas d'erreur, annuler toutes les modifications
            $this->db->rollBack();
            return false;
        }
    }

    public function modifierHeure($rdvId, $nouvelleDate, $nouvelleHeure) {
        
        // Nettoyer les créneaux orphelins avant toute modification
        $this->nettoyerCreneauxOrphelins();
        
        $this->db->beginTransaction();
        try {
            // 1. Récupérer les informations du rendez-vous actuel
            $sql = "SELECT r.id as rdv_id, r.creneau_id, r.patient_id, c.*, s.duree, s.id as service_id, c.agenda_id
                   FROM rendezvous r
                   INNER JOIN creneau c ON r.creneau_id = c.id 
                   LEFT JOIN service s ON c.service_id = s.id 
                   WHERE r.id = ? AND r.statut != 'ANNULE'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId]);
            $rendezvous = $stmt->fetch();

            if (!$rendezvous) {
                throw new \Exception("Rendez-vous introuvable");
            }

            $duree = $rendezvous['duree'] ?? 30;
            $nbCreneauxNecessaires = (int)ceil($duree / 30);
            $nouvelleDateTime = $nouvelleDate . ' ' . $nouvelleHeure . ':00';

            // 1b. Récupérer tous les créneaux occupés par le RDV actuel pour les exclure de la vérification
            // On récupère les créneaux en se basant sur ceux qui ont le même service_id ET qui sont réservés
            // autour du créneau de départ du RDV, en couvrant la durée du service
            $sql = "SELECT c.id 
                   FROM creneau c
                   WHERE c.agenda_id = ? 
                   AND c.service_id = ?
                   AND c.est_reserve = 1
                   AND c.debut >= (SELECT debut FROM creneau WHERE id = ?)
                   AND c.debut < DATE_ADD((SELECT debut FROM creneau WHERE id = ?), INTERVAL ? MINUTE)
                   ORDER BY c.debut ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $rendezvous['agenda_id'],
                $rendezvous['service_id'], 
                $rendezvous['creneau_id'], 
                $rendezvous['creneau_id'], 
                $duree
            ]);
            $creneauxActuelsRdv = $stmt->fetchAll(\PDO::FETCH_COLUMN);

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

            // 4. Vérifier qu'il n'est pas déjà réservé (sauf si c'est un des créneaux du RDV qu'on déplace)
            if ($nouveauCreneau['est_reserve'] == 1 && !in_array($nouveauCreneau['id'], $creneauxActuelsRdv)) {
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
                // Permettre les créneaux du RDV actuel, mais pas les autres réservés
                if ($creneau['est_reserve'] == 1 && !in_array($creneau['id'], $creneauxActuelsRdv)) {
                    throw new \Exception("Un des créneaux nécessaires est déjà réservé");
                }
            }

            // 6. Vérifier qu'il n'y a pas de chevauchement avec d'autres rendez-vous
            // On doit vérifier si les créneaux nécessaires sont déjà occupés par un autre RDV
            // en excluant les créneaux du RDV qu'on est en train de modifier
            $placeholdersExclus = implode(',', array_fill(0, count($creneauxActuelsRdv), '?'));
            $sql = "SELECT COUNT(*) 
                   FROM creneau c
                   INNER JOIN rendezvous r ON r.creneau_id = c.id
                   WHERE c.agenda_id = ?
                   AND c.est_reserve = 1
                   AND c.debut >= ?
                   AND c.debut < DATE_ADD(?, INTERVAL ? MINUTE)
                   AND r.statut != 'ANNULE'
                   AND c.id NOT IN ($placeholdersExclus)";
            $stmt = $this->db->prepare($sql);
            $params = [
                $rendezvous['agenda_id'],
                $nouvelleDateTime,
                $nouvelleDateTime,
                $duree
            ];
            $params = array_merge($params, $creneauxActuelsRdv);
            $stmt->execute($params);
            
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception("Un autre rendez-vous chevauche cet horaire");
            }

            // 7. D'ABORD : Libérer l'ancien créneau et tous les créneaux consécutifs associés
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

            // 8. ENSUITE : Marquer tous les créneaux consécutifs nécessaires comme réservés
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

            // 9. ENFIN : Mettre à jour le rendez-vous avec le nouveau créneau de départ
            $sql = "UPDATE rendezvous 
                   SET creneau_id = ? 
                   WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$nouveauCreneau['id'], $rdvId]);

            if (!$success) {
                throw new \Exception("Erreur lors de la modification du rendez-vous");
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e; // Relancer l'exception pour que le contrôleur puisse récupérer le message
        }
    }

    /**
     * Nettoie les créneaux orphelins (marqués comme réservés mais sans RDV actif)
     * À appeler périodiquement ou après des opérations critiques
     */
    public function nettoyerCreneauxOrphelins() {
        try {
            $sql = "UPDATE creneau c
                    SET c.est_reserve = 0, c.service_id = NULL
                    WHERE c.est_reserve = 1 
                    AND c.id NOT IN (
                        SELECT r.creneau_id 
                        FROM rendezvous r 
                        WHERE r.statut != 'ANNULE'
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $count = $stmt->rowCount();
            
            if ($count > 0) {
            }
            
            return $count;
        } catch (\Exception $e) {
            return 0;
        }
    }
}