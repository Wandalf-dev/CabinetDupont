<?php
namespace App\Models;

use App\Core\Model;
;
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
        error_log($log);
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
                error_log("Créneau $id non trouvé");
                return false;
            }

            if ($creneau['has_active_rdv']) {
                $this->db->rollBack();
                error_log("Créneau $id a un rendez-vous actif");
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
                error_log("Statut du créneau $id changé de {$creneau['statut']} à $nouveauStatut");
                return $nouveauStatut === self::STATUT_INDISPONIBLE;
            }

            $this->db->rollBack();
            return false;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log("Erreur toggleIndisponible: " . $e->getMessage());
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
            error_log("ERREUR getAllCreneaux: " . $e->getMessage());
            throw $e;
        }
    }

    /** Créneaux existants sur une période */
    public function getCreneauxPourPeriode(string $dateDebut, string $dateFin, int $agendaId): array
    {
        $sql = "SELECT * FROM creneau WHERE agenda_id = ? AND DATE(debut) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Créneaux pour une date */
    public function getCreneauxPourDate(int $agendaId, string $date): array
    {
        $sql = "SELECT c.*,
                       s.titre AS service_titre,
                       CASE 
                           WHEN c.statut = 'indisponible' THEN true
                           WHEN EXISTS (
                               SELECT 1 
                               FROM rendezvous r2
                               JOIN creneau c2 ON r2.creneau_id = c2.id
                               WHERE DATE(c2.debut) = DATE(c.debut)
                                 AND c2.debut < c.fin
                                 AND c2.fin > c.debut
                                 AND r2.statut != :annule
                           ) THEN true 
                           ELSE false 
                       END AS est_reserve,
                       c.statut,
                       GROUP_CONCAT(DISTINCT r.id) AS reservations
                FROM creneau c
                LEFT JOIN service s ON c.service_id = s.id
                LEFT JOIN rendezvous r ON c.id = r.creneau_id AND r.statut != :annule2
                WHERE c.agenda_id = :agendaId
                  AND DATE(c.debut) = :date
                GROUP BY c.id
                ORDER BY c.debut ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':annule'    => self::STATUT_ANNULE,
            ':annule2'   => self::STATUT_ANNULE,
            ':agendaId'  => $agendaId,
            ':date'      => $date,
        ]);

        $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $logFile = __DIR__ . '/../../debug_creneaux.log';
        @file_put_contents($logFile, "=== Créneaux pour le {$date} ===\n", FILE_APPEND);
        foreach ($creneaux as $cr) {
            $msg = sprintf(
                "Créneau: %s - %s | ID: %d | Réservé: %s\n",
                date('H:i', strtotime($cr['debut'])),
                date('H:i', strtotime($cr['fin'])),
                (int)$cr['id'],
                !empty($cr['est_reserve']) ? 'Oui' : 'Non'
            );
            @file_put_contents($logFile, $msg, FILE_APPEND);
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
                LEFT JOIN rendezvous r ON c.id = r.creneau_id AND r.statut != :annule
                WHERE c.debut >= CURRENT_DATE()
                  AND c.debut <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)
                  AND r.id IS NULL
                  AND c.statut != :indispo
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

        $sql = "SELECT DISTINCT c.id, c.debut, c.fin, c.statut, c.service_id
                FROM creneau c
                WHERE DATE(c.debut) = :d
                  AND c.est_reserve = 0
                  AND NOT EXISTS (
                      SELECT 1 FROM rendezvous r
                      WHERE r.creneau_id = c.id AND r.statut != :annule
                  )
                  AND NOT EXISTS (
                      SELECT 1
                      FROM rendezvous r2
                      JOIN creneau c2 ON r2.creneau_id = c2.id
                      WHERE r2.statut != :annule2
                        AND DATE(c2.debut) = DATE(c.debut)
                        AND (
                             (c2.debut >= c.debut AND c2.debut < DATE_ADD(c.debut, INTERVAL :d1 MINUTE))
                          OR (c2.debut < c.debut AND DATE_ADD(c2.debut, INTERVAL :d2 MINUTE) > c.debut)
                        )
                  )
                ORDER BY c.debut ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':d'       => $date,
            ':annule'  => self::STATUT_ANNULE,
            ':annule2' => self::STATUT_ANNULE,
            ':d1'      => $duree,
            ':d2'      => $duree,
        ]);
        $departSlots = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->logDebug('Créneaux candidats (départ)', count($departSlots));

        $result    = [];
        $needCount = max(1, (int)ceil($duree / 30));

        foreach ($departSlots as $slot) {
            $sqlRange = "SELECT c2.id, c2.debut
                         FROM creneau c2
                         WHERE c2.agenda_id = (SELECT agenda_id FROM creneau WHERE id = :id0)
                           AND c2.debut >= (SELECT debut FROM creneau WHERE id = :id1)
                           AND c2.debut <  DATE_ADD((SELECT debut FROM creneau WHERE id = :id2), INTERVAL :d MINUTE)
                           AND c2.est_reserve = 0
                         ORDER BY c2.debut ASC";
            $stmtRange = $this->db->prepare($sqlRange);
            $stmtRange->execute([':id0' => $slot['id'], ':id1' => $slot['id'], ':id2' => $slot['id'], ':d' => $duree]);
            $range = $stmtRange->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if (count($range) >= $needCount) {
                $result[] = $slot;
            }
        }

        $this->logDebug('Slots disponibles (final)', count($result));
        return $result;
    }

    /** RDV d'un utilisateur (futurs) */
    public function getUserRendezVous(int $userId): array
    {
        $sql = "SELECT c.*, r.id AS rdv_id, s.titre AS service_titre
                FROM rendezvous r
                JOIN creneau c ON r.creneau_id = c.id
                JOIN service s ON c.service_id = s.id
                JOIN utilisateur u ON r.patient_id = u.id
                WHERE u.id = ? AND c.debut >= NOW()
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
                  AND (c.statut = :indispo OR r.id IS NOT NULL)
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
                    error_log("Créneau #" . $cr['id'] . " non libéré, il reste $nbActifs RDV actifs.");
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log("Erreur cancelRendezVous: " . $e->getMessage());
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
                           OR (c.debut < :debut3 AND DATE_ADD(c.debut, INTERVAL :duree2 MINUTE) > :debut4)
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
                ':duree2' => $duree,
            ]);
            $chevauchement = (int)$stmtChev->fetchColumn();
            if ($chevauchement > 0) {
                error_log("[REFUS RDV] Chevauchement détecté : creneauId=$creneauId, chevauchement=$chevauchement");
                throw new \Exception("Chevauchement détecté : créneau indisponible");
            }

            $sqlRange = "SELECT id
                         FROM creneau
                         WHERE agenda_id = :agenda
                           AND debut >= :debut
                           AND debut <  DATE_ADD(:debut2, INTERVAL :duree MINUTE)
                           AND est_reserve = 0
                         ORDER BY debut ASC";
            $stmtRange = $this->db->prepare($sqlRange);
            $stmtRange->execute([
                ':agenda' => $start['agenda_id'],
                ':debut'  => $start['debut'],
                ':debut2' => $start['debut'],
                ':duree'  => $duree,
            ]);
            $range = $stmtRange->fetchAll(PDO::FETCH_COLUMN) ?: [];

            $needCount = max(1, (int)ceil($duree / 30));
            if (count($range) < $needCount) {
                error_log("[REFUS RDV] Pas assez de créneaux consécutifs : besoin=$needCount, dispo=" . count($range));
                throw new \Exception("Pas assez de créneaux consécutifs disponibles");
            }

            $stmtCheck = $this->db->prepare("SELECT id FROM rendezvous WHERE creneau_id = ? AND statut = 'ANNULE'");
            $stmtCheck->execute([$creneauId]);
            $existingRdv = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingRdv) {
                $stmtUpd = $this->db->prepare("UPDATE rendezvous SET patient_id = ?, medecin_id = ?, statut = ? WHERE id = ?");
                $stmtUpd->execute([$patientId, $medecinId, self::STATUT_DEMANDE, $existingRdv['id']]);
            } else {
                $stmtIns = $this->db->prepare("INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) VALUES (?, ?, ?, ?)");
                $stmtIns->execute([$creneauId, $patientId, $medecinId, self::STATUT_DEMANDE]);
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
            error_log('Erreur createRendezVous: ' . $e->getMessage());
            return false;
        }
    }

    /** Génère des créneaux sur une période d’après les horaires */
    public function genererCreneaux(int $agendaId, string $dateDebut, string $dateFin): bool
    {
        try {
            $this->db->beginTransaction();

            if (!$this->verifierAgenda($agendaId)) {
                error_log("Agenda $agendaId introuvable");
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("SELECT * FROM horaire_cabinet WHERE cabinet_id = 1");
            $stmt->execute();
            $horaires = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            if (!$horaires) {
                error_log('Aucun horaire en base');
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
            error_log('Erreur genererCreneaux: ' . $e->getMessage());
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
            error_log("Créneaux déjà présents le {$date} entre {$heureDebut} et {$heureFin}");
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
                    error_log('Erreur insert creneau: ' . $e->getMessage());
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
