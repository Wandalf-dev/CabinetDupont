<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class CreneauModel extends Model
{
    public function cleanupInconsistentSlots(): bool 
    {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE creneau 
                    SET est_reserve = 0, 
                        service_id = NULL, 
                        reservation_id = NULL 
                    WHERE statut = 'disponible' 
                      AND (est_reserve = 1 OR service_id IS NOT NULL OR reservation_id IS NOT NULL)";

            $stmt   = $this->db->prepare($sql);
            $result = $stmt->execute();

            if ($result) {
                $this->db->commit();
                $this->logDebug("Nettoyage des créneaux incohérents effectué");
                return true;
            }

            $this->db->rollBack();
            return false;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            $this->logDebug("Erreur lors du nettoyage des créneaux", $e->getMessage());
            return false;
        }
    }

    // Normalisation des statuts
    private const STATUT_ANNULE      = 'ANNULE';
    private const STATUT_CONFIRME    = 'CONFIRME';
    private const STATUT_HONORE      = 'HONORE';
    private const STATUT_ABSENT      = 'ABSENT';
    private const STATUT_DEMANDE     = 'DEMANDE';
    private const STATUT_DISPONIBLE  = 'disponible';
    private const STATUT_INDISPONIBLE= 'indisponible';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Europe/Paris');
    }

    private function logDebug(string $message, $data = null): void
    {
        $log = "[" . date('Y-m-d H:i:s') . "] " . $message;
        if ($data !== null) {
            $log .= "\n" . print_r($data, true);
        }
        $log .= "\n";
    }

    /**
     * Marque un créneau comme indisponible ou disponible
     * @return bool true si devient INDISPONIBLE, false si devient DISPONIBLE
     */
    public function toggleIndisponible(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $sql = "SELECT c.*, 
                           EXISTS(SELECT 1 FROM rendezvous r 
                                  WHERE r.creneau_id = c.id 
                                    AND r.statut != :annule) as has_active_rdv
                    FROM creneau c 
                    WHERE c.id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':annule' => self::STATUT_ANNULE,
                ':id'     => $id
            ]);
            $creneau = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$creneau) {
                $this->db->rollBack();
                return false;
            }

            if ($creneau['has_active_rdv']) {
                $this->db->rollBack();
                return false;
            }

            $nouveauStatut = ($creneau['statut'] === self::STATUT_INDISPONIBLE)
                ? self::STATUT_DISPONIBLE
                : self::STATUT_INDISPONIBLE;


            $sql = "UPDATE creneau 
                    SET statut = :statut,
                        est_reserve = 0,
                        service_id = CASE WHEN :to_indispo = 1 THEN NULL ELSE service_id END
                    WHERE id = :id";

            $toIndispo = ($nouveauStatut === self::STATUT_INDISPONIBLE) ? 1 : 0;

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':statut'      => $nouveauStatut,
                ':to_indispo'  => $toIndispo,
                ':id'          => $id
            ]);

            if ($success) {
                $this->db->commit();
                return $nouveauStatut === self::STATUT_INDISPONIBLE;
            }

            $this->db->rollBack();
            return false;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /** Vérifie si un agenda existe */
    private function verifierAgenda(int $agendaId): bool
    {
        $sql = "SELECT COUNT(*) FROM agenda WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Récupère tous les créneaux (futurs) */
    public function getAllCreneaux(): array
    {
        try {
            $this->logDebug("=== Début getAllCreneaux ===");

            foreach (['creneau', 'service', 'rendezvous'] as $table) {
                $stmt = $this->db->prepare("SELECT 1 FROM {$table} LIMIT 1");
                $stmt->execute();
            }

            $sql = "SELECT c.*, s.titre AS service_titre
                    FROM creneau c
                    LEFT JOIN service s ON c.service_id = s.id
                    WHERE DATE(c.debut) >= CURDATE()
                      AND c.id = (
                        SELECT MAX(c2.id) FROM creneau c2
                        WHERE c2.debut = c.debut AND c2.fin = c.fin AND c2.agenda_id = c.agenda_id
                      )
                    ORDER BY c.debut ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->logDebug("Nombre de créneaux trouvés", count($creneaux));
            return $creneaux ?: [];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /** Créneaux existants sur une période */
    public function getCreneauxPourPeriode(string $dateDebut, string $dateFin, int $agendaId): array
    {
        $sql = "SELECT c.*, 
                       r.id AS rdv_id,
                       r.duree AS rdv_duree,
                       r.statut AS rdv_statut,
                       s.titre AS service_titre,
                       s.duree AS service_duree
                FROM creneau c
                LEFT JOIN rendezvous r ON c.id = r.creneau_id AND r.statut != 'ANNULE'
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.agenda_id = ? 
                AND DATE(c.debut) BETWEEN ? AND ?
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Créneaux pour une date */
    public function getCreneauxPourDate(int $agendaId, string $date): array
    {
        // D'abord récupérer tous les créneaux
        $sql = "SELECT c.*,
                       s.titre AS service_titre,
                       s.duree AS service_duree,
                       c.statut
                FROM creneau c
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.agenda_id = :agendaId
                  AND DATE(c.debut) = :date
                ORDER BY c.debut ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':agendaId'  => $agendaId,
            ':date'      => $date,
        ]);

        $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Ensuite, récupérer tous les rendez-vous actifs de la date
        $sqlRdv = "SELECT r.id, r.creneau_id, r.duree, r.statut,
                          c2.debut, c2.service_id, s.titre AS service_titre
                   FROM rendezvous r
                   JOIN creneau c2 ON r.creneau_id = c2.id
                   LEFT JOIN service s ON c2.service_id = s.id
                   WHERE r.statut != :annule
                     AND DATE(c2.debut) = :date";

        $stmtRdv = $this->db->prepare($sqlRdv);
        $stmtRdv->execute([
            ':annule' => self::STATUT_ANNULE,
            ':date'   => $date,
        ]);

        $rdvs = $stmtRdv->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Enrichir les créneaux avec les infos de rdv
        foreach ($creneaux as &$creneau) {
            $creneau['est_reserve'] = false;
            $creneau['rdv_id'] = null;
            $creneau['rdv_duree'] = null;
            $creneau['rdv_statut'] = null;

            // Vérifier si indisponible
            if ($creneau['statut'] === 'indisponible') {
                $creneau['est_reserve'] = true;
                continue;
            }

            // Vérifier si un rdv chevauche ce créneau
            $creneauDebut = strtotime($creneau['debut']);
            $creneauFin = strtotime($creneau['fin']);

            foreach ($rdvs as $rdv) {
                $rdvDebut = strtotime($rdv['debut']);
                $rdvFin = $rdvDebut + ($rdv['duree'] * 60);

                // Si le rdv chevauche le créneau
                if ($rdvDebut <= $creneauDebut && $rdvFin > $creneauDebut) {
                    $creneau['est_reserve'] = true;
                    $creneau['rdv_id'] = $rdv['id'];
                    $creneau['rdv_duree'] = $rdv['duree'];
                    $creneau['rdv_statut'] = $rdv['statut'];
                    // Utiliser le service du rdv au lieu du service du créneau
                    if (!empty($rdv['service_titre'])) {
                        $creneau['service_titre'] = $rdv['service_titre'];
                    }
                    break;
                }
            }
        }

        return $creneaux;
    }

    /** Récupère un créneau par ID */
    public function getCreneauById(int $id)
    {
        $sql = "SELECT c.*, s.titre AS service_titre, r.id AS reservation_id
                FROM creneau c
                LEFT JOIN rendezvous r ON r.creneau_id = c.id AND r.statut != :annule
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':annule' => self::STATUT_ANNULE, ':id' => $id]);
        $cr = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cr) {
            $cr['est_reserve'] = !is_null($cr['reservation_id']) || $cr['est_reserve'] == 1;
        }
        return $cr ?: false;
    }

    /** Dates disponibles (14 jours) */
    public function getDatesDisponibles(): array
    {
        $this->logDebug("=== Début getDatesDisponibles ===");
        $sql = "SELECT DISTINCT DATE(c.debut) AS date
                FROM creneau c
                WHERE DATE(c.debut) >= CURRENT_DATE()
                  AND DATE(c.debut) <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)
                  AND c.est_reserve = 0
                  AND c.statut != :indispo
                  AND NOT EXISTS (
                      SELECT 1 FROM rendezvous r
                      WHERE r.creneau_id = c.id AND r.statut != :annule
                  )
                ORDER BY date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':annule'  => self::STATUT_ANNULE,
            ':indispo' => self::STATUT_INDISPONIBLE,
        ]);
        $dates = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        $this->logDebug('Dates trouvées', $dates);
        return $dates;
    }

    /** Supprime un créneau (si non référencé) */
    public function deleteCreneau(int $id): bool
    {
        $sql = "DELETE FROM creneau WHERE id = ? AND id NOT IN (SELECT creneau_id FROM rendezvous)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /** Slots disponibles pour un service (respecte durée) */
    public function getAvailableSlots(string $date, int $serviceId): array
    {
        $this->logDebug("=== Début getAvailableSlots ===", compact('date', 'serviceId'));

        $stmt = $this->db->prepare("SELECT duree FROM service WHERE id = :sid");
        $stmt->execute([':sid' => $serviceId]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        $duree = (int)($service['duree'] ?? 30);

        $stmtVerif = $this->db->prepare(
            "SELECT r.id, r.statut, r.creneau_id, c.debut
             FROM rendezvous r
             JOIN creneau c ON r.creneau_id = c.id
             WHERE DATE(c.debut) = :date
             ORDER BY r.id DESC"
        );
        $stmtVerif->execute([':date' => $date]);
        $this->logDebug('RDV du jour', $stmtVerif->fetchAll(PDO::FETCH_ASSOC));

        // Calculer la limite de temps UNIQUEMENT pour le jour actuel
        $dateActuelle = (new \DateTime())->format('Y-m-d');
        $dateSelectionnee = $date;
        
        // Si c'est le jour actuel, appliquer le délai de 4 heures
        $conditionDelai = '';
        $delaiMinimumStr = null;
        
        if ($dateSelectionnee === $dateActuelle) {
            $delaiMinimum = new \DateTime();
            $delaiMinimum->modify('+4 hours');
            $delaiMinimumStr = $delaiMinimum->format('Y-m-d H:i:s');
            $conditionDelai = 'AND c.debut > :delai_minimum';
        }

        $sql = "SELECT DISTINCT c.id, c.debut, c.fin, c.statut, c.service_id
                FROM creneau c
                WHERE DATE(c.debut) = :d
                  {$conditionDelai}
                  AND c.est_reserve = 0
                  AND c.statut != :indispo
                  AND NOT EXISTS (
                      SELECT 1 FROM rendezvous r
                      WHERE r.creneau_id = c.id AND r.statut != :annule
                  )
                ORDER BY c.debut ASC";

        $params = [
            ':d'              => $date,
            ':indispo'        => self::STATUT_INDISPONIBLE,
            ':annule'         => self::STATUT_ANNULE,
        ];
        
        // Ajouter le paramètre de délai uniquement si c'est le jour actuel
        if ($delaiMinimumStr !== null) {
            $params[':delai_minimum'] = $delaiMinimumStr;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $departSlots = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->logDebug('Créneaux candidats (départ)', count($departSlots));

        $result    = [];
        $needCount = max(1, (int)ceil($duree / 30));

        foreach ($departSlots as $slot) {
            // VÉRIFICATION DE CHEVAUCHEMENT : vérifier qu'aucun RDV existant ne chevauche la période [slot.debut, slot.debut + duree]
            $sqlChev = "SELECT COUNT(*)
                        FROM rendezvous r
                        JOIN creneau c ON r.creneau_id = c.id
                        WHERE r.statut != :annule
                          AND c.agenda_id = (SELECT agenda_id FROM creneau WHERE id = :slot_id)
                          AND DATE(c.debut) = DATE(:slot_debut)
                          AND (
                              (c.debut >= :slot_debut1 AND c.debut < DATE_ADD(:slot_debut2, INTERVAL :duree MINUTE))
                           OR (c.debut < :slot_debut3 AND DATE_ADD(c.debut, INTERVAL r.duree MINUTE) > :slot_debut4)
                          )";
            $stmtChev = $this->db->prepare($sqlChev);
            $stmtChev->execute([
                ':annule' => self::STATUT_ANNULE,
                ':slot_id' => $slot['id'],
                ':slot_debut' => $slot['debut'],
                ':slot_debut1' => $slot['debut'],
                ':slot_debut2' => $slot['debut'],
                ':slot_debut3' => $slot['debut'],
                ':slot_debut4' => $slot['debut'],
                ':duree' => $duree,
            ]);
            $chevauchement = (int)$stmtChev->fetchColumn();
            
            // Si chevauchement détecté, passer au créneau suivant
            if ($chevauchement > 0) {
                continue;
            }
            
            $sqlRange = "SELECT c2.id, c2.debut
                         FROM creneau c2
                         WHERE c2.agenda_id = (SELECT agenda_id FROM creneau WHERE id = :id0)
                           AND c2.debut >= (SELECT debut FROM creneau WHERE id = :id1)
                           AND c2.debut <  DATE_ADD((SELECT debut FROM creneau WHERE id = :id2), INTERVAL :d MINUTE)
                           AND c2.est_reserve = 0
                           AND c2.statut != :indispo
                           AND NOT EXISTS (
                               SELECT 1 FROM rendezvous r
                               WHERE r.creneau_id = c2.id AND r.statut != :annule_range
                           )
                         ORDER BY c2.debut ASC";
            $stmtRange = $this->db->prepare($sqlRange);
            $stmtRange->execute([
                ':id0' => $slot['id'], 
                ':id1' => $slot['id'], 
                ':id2' => $slot['id'], 
                ':d' => $duree,
                ':indispo' => self::STATUT_INDISPONIBLE,
                ':annule_range' => self::STATUT_ANNULE
            ]);
            $range = $stmtRange->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Vérifier que les créneaux sont consécutifs (espacés de 30 minutes)
            if (count($range) >= $needCount) {
                $isConsecutive = true;
                for ($i = 0; $i < $needCount - 1; $i++) {
                    if (!isset($range[$i]) || !isset($range[$i + 1])) {
                        $isConsecutive = false;
                        break;
                    }
                    $time1 = strtotime($range[$i]['debut']);
                    $time2 = strtotime($range[$i + 1]['debut']);
                    // Vérifier que l'écart est exactement 30 minutes (1800 secondes)
                    if ($time2 - $time1 !== 1800) {
                        $isConsecutive = false;
                        break;
                    }
                }
                
                if ($isConsecutive) {
                    $result[] = $slot;
                }
            }
        }

        $this->logDebug('Slots disponibles (final)', count($result));
        return $result;
    }

    /** RDV d'un utilisateur (futurs) */
    public function getUserRendezVous(int $userId): array
    {
        $sql = "SELECT c.*, r.id AS rdv_id, r.statut AS rdv_statut, s.titre AS service_titre
                FROM rendezvous r
                JOIN creneau c ON r.creneau_id = c.id
                LEFT JOIN service s ON c.service_id = s.id
                WHERE r.patient_id = ? 
                AND r.statut != 'ANNULE'
                AND c.debut >= NOW()
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Indisponibles/occupés sur période */
    public function getCreneauxIndisponiblesByPeriod(string $dateDebut, string $dateFin, int $agendaId): array
    {
        $this->logDebug('=== getCreneauxIndisponiblesByPeriod ===', compact('dateDebut', 'dateFin', 'agendaId'));
        $sql = "SELECT DISTINCT c.*
                FROM creneau c
                LEFT JOIN rendezvous r ON c.id = r.creneau_id AND r.statut != :annule
                WHERE c.agenda_id = :agenda
                  AND DATE(c.debut) BETWEEN :d1 AND :d2
                  AND c.statut = :indispo
                  AND r.id IS NULL
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':annule'  => self::STATUT_ANNULE,
            ':agenda'  => $agendaId,
            ':d1'      => $dateDebut,
            ':d2'      => $dateFin,
            ':indispo' => self::STATUT_INDISPONIBLE,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Annule un rendez-vous et libère créneaux si possible */
    public function cancelRendezVous(int $rdvId, int $userId): bool
    {
        $this->logDebug("=== Début cancelRendezVous ===", compact('rdvId', 'userId'));
        try {
            $this->db->beginTransaction();

            $sqlInfo = "SELECT r.*, c.id AS creneau_id, c.debut AS creneau_debut, c.agenda_id,
                               s.duree AS service_duree
                        FROM rendezvous r
                        JOIN creneau c ON r.creneau_id = c.id
                        LEFT JOIN service s ON c.service_id = s.id
                        WHERE r.id = ? AND r.patient_id = ?";
            $stmtInfo = $this->db->prepare($sqlInfo);
            $stmtInfo->execute([$rdvId, $userId]);
            $rdv = $stmtInfo->fetch(PDO::FETCH_ASSOC);
            if (!$rdv) {
                $this->db->rollBack();
                $this->logDebug("[ERROR] RDV non trouvé", compact('rdvId', 'userId'));
                return false;
            }

            $sql = "UPDATE rendezvous SET statut = :annule WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute([':annule' => self::STATUT_ANNULE, ':id' => $rdvId])) {
                $this->db->rollBack();
                $this->logDebug("[ERROR] Échec de l'annulation du RDV", compact('rdvId'));
                return false;
            }

            $duree = (int)($rdv['service_duree'] ?? 30);
            $debut = $rdv['creneau_debut'];

            $sqlCreneaux = "SELECT id FROM creneau
                            WHERE agenda_id = :agenda
                              AND DATE(debut) = DATE(:debut)
                              AND TIME(debut) >= TIME(:start)
                              AND TIME(debut) <  TIME(DATE_ADD(:start2, INTERVAL :duree MINUTE))";
            $stmtC = $this->db->prepare($sqlCreneaux);
            $stmtC->execute([
                ':agenda' => $rdv['agenda_id'],
                ':debut'  => $debut,
                ':start'  => $debut,
                ':start2' => $debut,
                ':duree'  => $duree,
            ]);
            $creneaux = $stmtC->fetchAll(PDO::FETCH_ASSOC) ?: [];

            foreach ($creneaux as $cr) {
                $stmtAll = $this->db->prepare("SELECT id, statut FROM rendezvous WHERE creneau_id = ?");
                $stmtAll->execute([$cr['id']]);
                $rdvs = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
                $nbActifs = 0;
                foreach ($rdvs as $rdvTest) {
                    if ($rdvTest['statut'] !== self::STATUT_ANNULE) $nbActifs++;
                }
                $this->logDebug('[DEBUG CANCEL] Créneau ' . $cr['id'] . ' - RDV liés :', $rdvs);
                if ($nbActifs === 0) {
                    $stmtUpd = $this->db->prepare(
                        "UPDATE creneau SET est_reserve = 0, service_id = NULL, statut = :dispo WHERE id = :id"
                    );
                    $stmtUpd->execute([':dispo' => self::STATUT_DISPONIBLE, ':id' => $cr['id']]);
                    $this->logDebug('[DEBUG CANCEL] Libération du créneau ' . $cr['id']);
                } else {
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /** Crée un RDV (réserve les créneaux nécessaires) */
    public function createRendezVous(int $creneauId, int $serviceId, int $patientId): bool
    {
        $this->logDebug("=== Début createRendezVous ===", compact('creneauId', 'serviceId', 'patientId'));
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id FROM utilisateur WHERE id = ? AND role = 'PATIENT'");
            $stmt->execute([$patientId]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new \Exception('Patient introuvable');
            }


            $stmt = $this->db->prepare("SELECT id, duree FROM service WHERE id = ?");
            $stmt->execute([$serviceId]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$service) {
                throw new \Exception('Service introuvable');
            }
            $duree = (int)$service['duree'];

            $stmt = $this->db->prepare("SELECT id FROM utilisateur WHERE role = 'MEDECIN' LIMIT 1");
            $stmt->execute();
            $medecin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$medecin) {
                throw new \Exception('Aucun médecin disponible');
            }
            $medecinId = (int)$medecin['id'];

            $stmt = $this->db->prepare("SELECT id, debut, agenda_id, est_reserve, statut FROM creneau WHERE id = ?");
            $stmt->execute([$creneauId]);
            $start = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$start) {
                throw new \Exception("Créneau de départ introuvable");
            }

            $sqlChev = "SELECT COUNT(*)
                        FROM rendezvous r
                        JOIN creneau c ON r.creneau_id = c.id
                        WHERE r.statut != :annule
                          AND c.agenda_id = :agenda
                          AND DATE(c.debut) = DATE(:debut)
                          AND (
                              (c.debut >= :debut AND c.debut < DATE_ADD(:debut2, INTERVAL :duree MINUTE))
                           OR (c.debut < :debut3 AND DATE_ADD(c.debut, INTERVAL r.duree MINUTE) > :debut4)
                          )";
            $stmtChev = $this->db->prepare($sqlChev);
            $stmtChev->execute([
                ':annule' => self::STATUT_ANNULE,
                ':agenda' => $start['agenda_id'],
                ':debut'  => $start['debut'],
                ':debut2' => $start['debut'],
                ':debut3' => $start['debut'],
                ':debut4' => $start['debut'],
                ':duree'  => $duree,
            ]);
            $chevauchement = (int)$stmtChev->fetchColumn();
            if ($chevauchement > 0) {
                throw new \Exception("Chevauchement détecté : créneau indisponible");
            }

            $sqlRange = "SELECT id
                         FROM creneau
                         WHERE agenda_id = :agenda
                           AND debut >= :debut
                           AND debut <  DATE_ADD(:debut2, INTERVAL :duree MINUTE)
                           AND est_reserve = 0
                           AND statut != :indispo
                           AND NOT EXISTS (
                               SELECT 1 FROM rendezvous r
                               WHERE r.creneau_id = creneau.id AND r.statut != :annule_range
                           )
                         ORDER BY debut ASC";
            $stmtRange = $this->db->prepare($sqlRange);
            $stmtRange->execute([
                ':agenda' => $start['agenda_id'],
                ':debut'  => $start['debut'],
                ':debut2' => $start['debut'],
                ':duree'  => $duree,
                ':indispo' => self::STATUT_INDISPONIBLE,
                ':annule_range' => self::STATUT_ANNULE
            ]);
            $range = $stmtRange->fetchAll(PDO::FETCH_COLUMN) ?: [];

            $needCount = max(1, (int)ceil($duree / 30));
            if (count($range) < $needCount) {
                throw new \Exception("Pas assez de créneaux consécutifs disponibles (trouvés: " . count($range) . ", requis: " . $needCount . ")");
            }
            
            // Vérifier que les créneaux sont vraiment consécutifs (espacés de 30 minutes)
            if ($needCount > 1) {
                $stmtVerif = $this->db->prepare("SELECT debut FROM creneau WHERE id IN (" . implode(',', array_fill(0, count($range), '?')) . ") ORDER BY debut ASC");
                $stmtVerif->execute($range);
                $times = $stmtVerif->fetchAll(PDO::FETCH_COLUMN);
                
                for ($i = 0; $i < $needCount - 1; $i++) {
                    if (!isset($times[$i]) || !isset($times[$i + 1])) {
                        throw new \Exception("Créneaux manquants pour la vérification de consécutivité");
                    }
                    $time1 = strtotime($times[$i]);
                    $time2 = strtotime($times[$i + 1]);
                    if ($time2 - $time1 !== 1800) {
                        throw new \Exception("Les créneaux ne sont pas consécutifs (écart: " . ($time2 - $time1) . "s au lieu de 1800s)");
                    }
                }
            }

            $stmtCheck = $this->db->prepare("SELECT id FROM rendezvous WHERE creneau_id = ? AND statut = 'ANNULE'");
            $stmtCheck->execute([$creneauId]);
            $existingRdv = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingRdv) {
                $stmtUpd = $this->db->prepare("UPDATE rendezvous SET patient_id = ?, medecin_id = ?, statut = ?, duree = ? WHERE id = ?");
                $stmtUpd->execute([$patientId, $medecinId, self::STATUT_CONFIRME, $duree, $existingRdv['id']]);
            } else {
                $stmtIns = $this->db->prepare("INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut, duree) VALUES (?, ?, ?, ?, ?)");
                $stmtIns->execute([$creneauId, $patientId, $medecinId, self::STATUT_CONFIRME, $duree]);
            }

            $inIds = implode(',', array_fill(0, count($range), '?'));
            $sqlUpd = "UPDATE creneau SET service_id = ?, est_reserve = 1 WHERE id IN ({$inIds})";
            $stmtUpd = $this->db->prepare($sqlUpd);
            $params  = array_merge([$serviceId], $range);
            $stmtUpd->execute($params);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /** Génère des créneaux sur une période d'après les horaires */
    public function genererCreneaux(int $agendaId, string $dateDebut, string $dateFin): bool
    {
        try {
            $this->db->beginTransaction();

            if (!$this->verifierAgenda($agendaId)) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("SELECT * FROM horaire_cabinet WHERE cabinet_id = 1");
            $stmt->execute();
            $horaires = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            if (!$horaires) {
                $this->db->rollBack();
                return false;
            }

            $dStart = new \DateTime($dateDebut);
            $dEnd   = new \DateTime($dateFin);
            $oneDay = new \DateInterval('P1D');

            $joursEN = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
            $joursFR = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];

            while ($dStart <= $dEnd) {
                $jour = str_replace($joursEN, $joursFR, strtolower($dStart->format('l')));
                foreach ($horaires as $h) {
                    if ($h['jour'] === $jour) {
                        if ($h['ouverture_matin'] !== '00:00:00') {
                            $this->creerCreneauxPourPeriode($agendaId, $dStart->format('Y-m-d'), $h['ouverture_matin'], $h['fermeture_matin']);
                        }
                        if ($h['ouverture_apresmidi'] !== '00:00:00') {
                            $this->creerCreneauxPourPeriode($agendaId, $dStart->format('Y-m-d'), $h['ouverture_apresmidi'], $h['fermeture_apresmidi']);
                        }
                        break;
                    }
                }
                $dStart->add($oneDay);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /** Crée les créneaux d'une plage (pas 30 min) */
    private function creerCreneauxPourPeriode(int $agendaId, string $date, string $heureDebut, string $heureFin): void
    {
        $sql = "SELECT COUNT(*) FROM creneau WHERE agenda_id = ? AND DATE(debut) = ? AND TIME(debut) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $date, $heureDebut, $heureFin]);
        if ((int)$stmt->fetchColumn() > 0) {
            return;
        }

        $debut    = new \DateTime($date . ' ' . $heureDebut);
        $fin      = new \DateTime($date . ' ' . $heureFin);
        $interval = new \DateInterval('PT30M');

        while ($debut < $fin) {
            $finCreneau = (clone $debut)->add($interval);
            if ($finCreneau <= $fin) {
                $sql = "INSERT INTO creneau (agenda_id, debut, fin, est_reserve, statut) VALUES (?, ?, ?, 0, ?)";
                try {
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $agendaId,
                        $debut->format('Y-m-d H:i:s'),
                        $finCreneau->format('Y-m-d H:i:s'),
                        self::STATUT_DISPONIBLE,
                    ]);
                } catch (\PDOException $e) {
                }
            }
            $debut->add($interval);
        }
    }

    /** Récupère les créneaux (tous) pour une période */
    public function getCreneauxDisponibles(int $agendaId, string $dateDebut, string $dateFin): array
    {
        $this->logDebug('=== getCreneauxDisponibles ===', compact('agendaId', 'dateDebut', 'dateFin'));

        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM creneau");
        $stmt->execute();
        $tot = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->logDebug('Total creneau table', $tot);

        $sql = "SELECT c.*, s.titre AS service_titre, s.duree AS service_duree
                FROM creneau c
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.agenda_id = ?
                  AND DATE(c.debut) BETWEEN ? AND ?
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $this->logDebug('Creneaux sur période', count($res));
        return $res;
    }
}
