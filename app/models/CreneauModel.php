<?php
namespace App\Models;
use App\Core\Model;

class CreneauModel extends Model {
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Europe/Paris');
    }

    /**
     * Vérifie si un agenda existe
     * @param int $agendaId ID de l'agenda
     * @return bool True si l'agenda existe, false sinon
     */
    private function verifierAgenda($agendaId) {
        $sql = "SELECT COUNT(*) FROM agenda WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère tous les créneaux pour l'administration
     * @return array Liste de tous les créneaux
     */
    public function getAllCreneaux() {
        try {
            error_log("=== Début getAllCreneaux avec logs détaillés ===");
            
            // 1. Vérifions d'abord la connexion à la base de données
            if (!$this->db) {
                error_log("ERREUR CRITIQUE: Pas de connexion à la base de données");
                throw new \Exception("Pas de connexion à la base de données");
            }
            error_log("Connexion à la base de données OK");
            
            // 2. Vérifions que les tables existent
            try {
                $tables = ['creneau', 'service', 'rendezvous'];
                foreach ($tables as $table) {
                    $stmt = $this->db->prepare("SELECT 1 FROM $table LIMIT 1");
                    $stmt->execute();
                    error_log("Table $table vérifiée avec succès");
                }
            } catch (\PDOException $e) {
                error_log("ERREUR lors de la vérification des tables: " . $e->getMessage());
                throw $e;
            }
            
            // 3. Faisons d'abord une requête simple pour voir les rendez-vous
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rendezvous WHERE statut != 'annulé'");
                $stmt->execute();
                $count = $stmt->fetch();
                error_log("Nombre de rendez-vous actifs trouvés: " . $count['total']);
            } catch (\PDOException $e) {
                error_log("ERREUR lors du comptage des rendez-vous: " . $e->getMessage());
            }
            
            error_log("=== Début de la requête principale ===");
            // Requête qui prend en compte la durée des RDV
            $sql = "SELECT c.*, s.titre as service_titre,
                    EXISTS (
                        SELECT 1 
                        FROM rendezvous r
                        JOIN creneau c_rdv ON r.creneau_id = c_rdv.id
                        JOIN service s_rdv ON c_rdv.service_id = s_rdv.id
                        WHERE r.statut != 'annulé'
                        AND DATE(c_rdv.debut) = DATE(c.debut)
                        AND c.debut >= c_rdv.debut
                        AND c.debut < TIMESTAMPADD(MINUTE, s_rdv.duree, c_rdv.debut)
                    ) as est_reserve
                    FROM creneau c
                    LEFT JOIN service s ON c.service_id = s.id
                    WHERE DATE(c.debut) >= CURDATE()
                    ORDER BY c.debut ASC";
                    
            error_log("Requête SQL: " . str_replace("\n", " ", $sql));
                    
            error_log("Requête SQL à exécuter: " . $sql);
            
            try {
                $stmt = $this->db->prepare($sql);
                error_log("Requête préparée avec succès");
                
                $stmt->execute();
                error_log("Requête exécutée avec succès");
                
                $creneaux = $stmt->fetchAll();
                error_log("Nombre de créneaux trouvés: " . count($creneaux));
                
                // Log des 5 premiers créneaux pour vérification
                $i = 0;
                foreach ($creneaux as $creneau) {
                    if ($i >= 5) break;
                    error_log(sprintf(
                        "Créneau #%d: début=%s, fin=%s, est_reserve=%s",
                        $creneau['id'],
                        $creneau['debut'],
                        $creneau['fin'],
                        $creneau['est_reserve']
                    ));
                    $i++;
                }
                
                return $creneaux;
            } catch (\PDOException $e) {
                error_log("ERREUR SQL: " . $e->getMessage());
                error_log("Code erreur: " . $e->getCode());
                throw $e;
            }
            
        } catch (\Exception $e) {
            error_log("ERREUR dans getAllCreneaux: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            throw $e;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $creneaux = $stmt->fetchAll();
        
        // 2. Récupérer tous les rendez-vous actifs
        $sql_rdv = "SELECT r.date, r.heure_debut, r.service_id, s.duree 
                    FROM rendezvous r
                    JOIN service s ON r.service_id = s.id
                    WHERE r.statut != 'annulé'
                    AND r.date >= ?";
                    
        error_log("Recherche des rendez-vous à partir de : " . $aujourdhui);
        $stmt_rdv = $this->db->prepare($sql_rdv);
        $stmt_rdv->execute([$aujourdhui]);
        $stmt_rdv = $this->db->prepare($sql_rdv);
        $stmt_rdv->execute();
        $rdvs = $stmt_rdv->fetchAll();
        
        // 3. Pour chaque créneau
        foreach ($creneaux as &$creneau) {
            // Si le créneau est hors des horaires d'ouverture, on le marque comme réservé
            if ($creneau['hors_horaires'] == 1) {
                $creneau['est_reserve'] = 1;
                continue;
            }

            $date_creneau = date('Y-m-d', strtotime($creneau['debut']));
            $heure_creneau = date('H:i:s', strtotime($creneau['debut']));
            
            // Vérifier chaque rendez-vous
            foreach ($rdvs as $rdv) {
                if ($rdv['date'] == $date_creneau) {
                    $heure_fin_rdv = date('H:i:s', strtotime($rdv['heure_debut'] . ' + ' . $rdv['duree']));
                    
                    if ($heure_creneau >= $rdv['heure_debut'] && $heure_creneau < $heure_fin_rdv) {
                        $creneau['est_reserve'] = 1;
                        break;
                    }
                }
            }
        }
        
        return $creneaux;
    }

    /**
     * Récupère un créneau par son ID
     * @param int $id ID du créneau
     * @return array|false Le créneau ou false si non trouvé
     */
    /**
     * Récupère les créneaux existants pour une période donnée
     * @param string $dateDebut Date de début au format Y-m-d
     * @param string $dateFin Date de fin au format Y-m-d
     * @param int $agendaId ID de l'agenda
     * @return array Liste des créneaux existants
     */
    public function getCreneauxPourPeriode($dateDebut, $dateFin, $agendaId) {
        $sql = "SELECT * FROM creneau 
                WHERE agenda_id = ? 
                AND DATE(debut) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les créneaux pour une date donnée
     * @param int $agendaId ID de l'agenda
     * @param string $date Date au format Y-m-d
     * @return array Liste des créneaux
     */
    public function getCreneauxPourDate($agendaId, $date) {
        $sql = "SELECT c.*,
                       s.titre as service_titre,
                       CASE WHEN EXISTS (
                           SELECT 1 
                           FROM rendezvous r2
                           JOIN creneau c2 ON r2.creneau_id = c2.id
                           WHERE DATE(c2.debut) = DATE(c.debut)
                           AND c2.debut < c.fin
                           AND c2.fin > c.debut
                       ) THEN 1 ELSE 0 END as est_reserve
                FROM creneau c
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.agenda_id = ?
                AND DATE(c.debut) = ?
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $date]);
        $creneaux = $stmt->fetchAll();
        
        // Debug log pour vérifier les créneaux et leur statut
        $logFile = __DIR__ . '/../../debug_creneaux.log';
        file_put_contents($logFile, "=== Créneaux pour le $date ===\n", FILE_APPEND);
        
        foreach ($creneaux as $creneau) {
            $logMessage = sprintf(
                "Créneau: %s - %s | ID: %d | Est réservé: %s\n",
                date('H:i', strtotime($creneau['debut'])),
                date('H:i', strtotime($creneau['fin'])),
                $creneau['id'],
                $creneau['est_reserve'] ? 'Oui' : 'Non'
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            // Vérifions aussi les rendez-vous qui chevauchent
            $sqlCheck = "SELECT c2.* FROM rendezvous r2
                        JOIN creneau c2 ON r2.creneau_id = c2.id
                        WHERE DATE(c2.debut) = DATE(?)
                        AND c2.debut < ?
                        AND c2.fin > ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([$creneau['debut'], $creneau['fin'], $creneau['debut']]);
            $chevauchements = $stmtCheck->fetchAll();
            
            if (!empty($chevauchements)) {
                file_put_contents($logFile, "  Rendez-vous qui chevauchent:\n", FILE_APPEND);
                foreach ($chevauchements as $rdv) {
                    file_put_contents($logFile, sprintf("    - %s - %s (ID: %d)\n",
                        date('H:i', strtotime($rdv['debut'])),
                        date('H:i', strtotime($rdv['fin'])),
                        $rdv['id']
                    ), FILE_APPEND);
                }
            }
        }
        
        return $creneaux;
    }

    public function getCreneauById($id) {
        $sql = "SELECT c.*, 
                       s.titre as service_titre,
                       r.id as reservation_id 
                FROM creneau c
                LEFT JOIN rendezvous r ON r.creneau_id = c.id
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $creneau = $stmt->fetch();
        if ($creneau) {
            $creneau['est_reserve'] = !is_null($creneau['reservation_id']);
        }
        return $creneau;
    }

    /**
     * Récupère les dates disponibles avec des créneaux non réservés
     * @return array Liste des dates avec des créneaux disponibles
     */
    public function getDatesDisponibles() {
        error_log("=== Début getDatesDisponibles ===");
        
        // Récupérer uniquement les dates avec des créneaux disponibles
        $sql = "SELECT DISTINCT DATE(c.debut) as date
                FROM creneau c
                LEFT JOIN rendezvous r ON c.id = r.creneau_id
                WHERE c.debut >= CURRENT_DATE()
                AND c.debut <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)
                AND r.id IS NULL  -- Créneau non réservé
                ORDER BY date ASC";
        
        error_log("Exécution de la requête : " . $sql);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dates = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        error_log("Dates trouvées : " . print_r($dates, true));
        return $dates;
    }

    /**
     * Supprime un créneau par son ID
     * @param int $id ID du créneau
     * @return bool True si succès, false sinon
     */
    public function deleteCreneau($id) {
        $sql = "DELETE FROM creneau WHERE id = ? AND id NOT IN (SELECT creneau_id FROM rendezvous)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Récupère les créneaux disponibles pour une date et un service donnés
     * @param string $date Date au format YYYY-MM-DD
     * @param int $serviceId ID du service
     * @return array Liste des créneaux disponibles
     */
    public function getAvailableSlots($date, $serviceId) {
        error_log("=== DÉBUT getAvailableSlots ===");
        error_log("Date demandée : " . $date);
        error_log("Service ID demandé : " . $serviceId);

        error_log("Date demandée : " . $date);
        error_log("Service ID demandé : " . $serviceId);

        // Récupérer la durée du service demandé
        $sql = "SELECT duree FROM service WHERE id = :service_id";
        error_log("Requête durée service : " . $sql);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':service_id' => $serviceId]);
        $service = $stmt->fetch();
        $dureeService = $service ? $service['duree'] : 30; // Par défaut 30 minutes
        error_log("Durée du service : " . $dureeService . " minutes");

        $sql = "SELECT c.id, c.debut, c.fin
                FROM creneau c
                WHERE DATE(c.debut) = :date
                AND NOT EXISTS (
                    SELECT 1 
                    FROM rendezvous r2
                    JOIN creneau c2 ON r2.creneau_id = c2.id
                    WHERE DATE(c2.debut) = DATE(c.debut)
                    AND c2.service_id IS NOT NULL
                    AND (
                        /* Vérifie si le nouveau créneau chevauche un rdv existant */
                        (c.debut < DATE_ADD(c2.debut, INTERVAL 
                            (SELECT duree FROM service WHERE id = c2.service_id) MINUTE) 
                        AND DATE_ADD(c.debut, INTERVAL :duree MINUTE) > c2.debut)
                    )
                )
                AND EXISTS (
                    SELECT 1 
                    FROM creneau c4
                    WHERE DATE(c4.debut) = DATE(c.debut)
                    AND c4.debut >= c.debut
                    AND c4.debut < DATE_ADD(c.debut, INTERVAL :duree MINUTE)
                    AND NOT EXISTS (
                        SELECT 1
                        FROM rendezvous r3
                        WHERE r3.creneau_id = c4.id
                    )
                    GROUP BY DATE(c4.debut)
                    HAVING COUNT(*) >= CEIL(:duree/30)
                )
                ORDER BY c.debut ASC";

        error_log("Requête SQL : " . $sql);
        error_log("Paramètres : date = " . $date . ", durée = " . $dureeService);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':date' => $date,
            ':duree' => $dureeService
        ]);

        $slots = $stmt->fetchAll();
        error_log("Créneaux trouvés : " . print_r($slots, true));
        return $slots;
    }

    /**
     * Récupère tous les rendez-vous d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Liste des rendez-vous
     */
    public function getUserRendezVous($userId) {
        $sql = "SELECT c.*, r.id as rdv_id, s.titre as service_titre 
                FROM rendezvous r
                JOIN creneau c ON r.creneau_id = c.id
                JOIN service s ON c.service_id = s.id
                JOIN utilisateur u ON r.patient_id = u.id
                WHERE u.id = ? AND c.debut >= NOW()
                ORDER BY c.debut ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Annule un rendez-vous
     * @param int $rdvId ID du rendez-vous
     * @param int $userId ID de l'utilisateur
     * @return bool True si succès, false sinon
     */
    public function cancelRendezVous($rdvId, $userId) {
        error_log("Tentative d'annulation du rendez-vous : rdvId = " . $rdvId . ", userId = " . $userId);
        try {
            $this->db->beginTransaction();

            // 1. Vérifie que le rendez-vous appartient bien à l'utilisateur
            $sql = "SELECT r.*, c.id as creneau_id 
                   FROM rendezvous r
                   JOIN creneau c ON r.creneau_id = c.id
                   WHERE r.id = ? AND r.patient_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId, $userId]);
            $rdv = $stmt->fetch();

            if (!$rdv) {
                error_log("Rendez-vous non trouvé ou n'appartient pas à l'utilisateur");
                return false;
            }

            // 2. Supprime le rendez-vous
            $sql = "DELETE FROM rendezvous WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdvId]);

            // 3. Réinitialise le créneau
            $sql = "UPDATE creneau 
                   SET est_reserve = 0, service_id = NULL 
                   WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rdv['creneau_id']]);

            $this->db->commit();
            error_log("Rendez-vous annulé avec succès");
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur lors de l'annulation du rendez-vous : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée un nouveau rendez-vous
     * @param int $creneauId ID du créneau
     * @param int $serviceId ID du service
     * @param int $patientId ID du patient
     * @return bool True si succès, false sinon
     */
    public function createRendezVous($creneauId, $serviceId, $patientId) {
        try {
            error_log("=== Début createRendezVous ===");
            error_log("creneauId: " . $creneauId);
            error_log("serviceId: " . $serviceId);
            error_log("patientId: " . $patientId);

            $this->db->beginTransaction();
            error_log("Transaction démarrée");

            // Vérifier que le créneau est toujours disponible
            $sql = "SELECT c.* FROM creneau c 
                    LEFT JOIN rendezvous r ON c.id = r.creneau_id 
                    WHERE c.id = ? AND r.id IS NULL";
            error_log("SQL vérification disponibilité: " . $sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$creneauId]);
            error_log("Paramètres vérification: creneauId = " . $creneauId);
            $creneau = $stmt->fetch();
            error_log("Résultat vérification créneau: " . ($creneau ? "Disponible" : "Non disponible"));
            
            if (!$creneau) {
                throw new \Exception("Ce créneau n'est plus disponible.");
            }

            // Vérifier que le patient existe dans la table utilisateur
            $sql = "SELECT id FROM utilisateur WHERE id = ? AND role = 'PATIENT'";
            error_log("SQL vérification patient: " . $sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$patientId]);
            error_log("Paramètres vérification patient: patientId = " . $patientId);
            if (!$stmt->fetch()) {
                throw new \Exception("Patient introuvable.");
            }

            // Vérifier que le service existe
            $sql = "SELECT id FROM service WHERE id = ?";
            error_log("SQL vérification service: " . $sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$serviceId]);
            error_log("Paramètres vérification service: serviceId = " . $serviceId);
            if (!$stmt->fetch()) {
                throw new \Exception("Service introuvable.");
            }

            // Récupérer l'ID du médecin (on prend le premier médecin trouvé)
            $sql = "SELECT id FROM utilisateur WHERE role = 'MEDECIN' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $medecin = $stmt->fetch();
            if (!$medecin) {
                throw new \Exception("Aucun médecin disponible");
            }
            $medecinId = $medecin['id'];
            error_log("Médecin trouvé avec ID: " . $medecinId);

            // Récupérer la durée du service
            $sql = "SELECT duree FROM service WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$serviceId]);
            $service = $stmt->fetch();
            if (!$service) {
                throw new \Exception("Service introuvable");
            }
            $dureeService = $service['duree'];
            
            // Récupérer tous les créneaux nécessaires pour couvrir la durée du service
            $sql = "SELECT c.id, c.debut 
                   FROM creneau c 
                   WHERE c.id >= ? 
                   AND c.debut < (
                       SELECT DATE_ADD(c2.debut, INTERVAL ? MINUTE) 
                       FROM creneau c2 
                       WHERE c2.id = ?
                   )
                   AND c.est_reserve = 0
                   ORDER BY c.debut ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$creneauId, $dureeService, $creneauId]);
            $creneauxNecessaires = $stmt->fetchAll();
            
            // Vérifier qu'on a tous les créneaux nécessaires
            $totalMinutes = count($creneauxNecessaires) * 30;
            if ($totalMinutes < $dureeService) {
                throw new \Exception("Pas assez de créneaux disponibles pour ce service");
            }
            
            // Créer le rendez-vous
            $sql = "INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
                    VALUES (?, ?, ?, 'DEMANDE')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$creneauId, $patientId, $medecinId]);
            
            // Mettre à jour tous les créneaux nécessaires
            $sql = "UPDATE creneau 
                   SET service_id = ?, est_reserve = 1 
                   WHERE id IN (" . implode(',', array_column($creneauxNecessaires, 'id')) . ")";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$serviceId]);

            $this->db->commit();
            error_log("Transaction validée avec succès");
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur lors de la création du rendez-vous : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère les créneaux pour un agenda et une période donnée
     * @param int $agendaId ID de l'agenda
     * @param string $dateDebut Date de début (YYYY-MM-DD)
     * @param string $dateFin Date de fin (YYYY-MM-DD)
     * @return bool Succès de la génération
     */
    public function genererCreneaux($agendaId, $dateDebut, $dateFin) {
        try {
            $this->db->beginTransaction();

            // Vérifier si l'agenda existe
            if (!$this->verifierAgenda($agendaId)) {
                error_log("Erreur : Agenda $agendaId non trouvé");
                return false;
            }

            // 1. Récupérer les horaires du cabinet
            $sql = "SELECT * FROM horaire_cabinet WHERE cabinet_id = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $horaires = $stmt->fetchAll();
            
            if (empty($horaires)) {
                error_log("Erreur : Aucun horaire trouvé pour le cabinet");
                return false;
            }

            error_log("Horaires récupérés : " . print_r($horaires, true));
            
            // Convertir les dates en objets DateTime
            try {
                error_log("=== DEBUG: Début de génération des créneaux ===");
                error_log("Date début reçue: " . $dateDebut);
                error_log("Date fin reçue: " . $dateFin);
                
                $dateDebut = new \DateTime($dateDebut);
                $dateFin = new \DateTime($dateFin);
                $interval = new \DateInterval('P1D'); // Intervalle d'un jour
                
                error_log("Date début convertie: " . $dateDebut->format('Y-m-d'));
                error_log("Date fin convertie: " . $dateFin->format('Y-m-d'));
            } catch (\Exception $e) {
                error_log("Erreur lors de la conversion des dates : " . $e->getMessage());
                return false;
            }
            
                // Pour chaque jour entre dateDebut et dateFin
                while ($dateDebut <= $dateFin) {
                    error_log("=== Traitement du jour : " . $dateDebut->format('Y-m-d') . " ===");
                    
                    // Convertir le jour en français
                    $joursEN = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $joursFR = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                    $jourSemaine = str_replace($joursEN, $joursFR, strtolower($dateDebut->format('l')));
                    error_log("Jour de la semaine: " . $jourSemaine);                error_log("Génération des créneaux pour le jour : " . $jourSemaine);
                
                // Trouver les horaires pour ce jour
                foreach ($horaires as $horaire) {
                    if ($horaire['jour'] === $jourSemaine) {
                        error_log("Horaires trouvés pour " . $jourSemaine . " : " . print_r($horaire, true));
                        // Matin
                        if ($horaire['ouverture_matin'] !== '00:00:00') {
                            $this->creerCreneauxPourPeriode(
                                $agendaId,
                                $dateDebut->format('Y-m-d'),
                                $horaire['ouverture_matin'],
                                $horaire['fermeture_matin']
                            );
                        }
                        
                        // Après-midi
                        if ($horaire['ouverture_apresmidi'] !== '00:00:00') {
                            $this->creerCreneauxPourPeriode(
                                $agendaId,
                                $dateDebut->format('Y-m-d'),
                                $horaire['ouverture_apresmidi'],
                                $horaire['fermeture_apresmidi']
                            );
                        }
                        break;
                    }
                }
                
                $dateDebut->add($interval); // Passer au jour suivant
            }
            
            $this->db->commit();
            error_log("Génération des créneaux terminée avec succès");
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la génération des créneaux : " . $e->getMessage());
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Crée les créneaux pour une période donnée
     * @param int $agendaId ID de l'agenda
     * @param string $date Date du jour (YYYY-MM-DD)
     * @param string $heureDebut Heure de début (HH:MM:SS)
     * @param string $heureFin Heure de fin (HH:MM:SS)
     */
    private function creerCreneauxPourPeriode($agendaId, $date, $heureDebut, $heureFin) {
        // Vérifier si des créneaux existent déjà pour cette période
        $sql = "SELECT COUNT(*) FROM creneau 
                WHERE agenda_id = ? 
                AND DATE(debut) = ? 
                AND TIME(debut) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $date, $heureDebut, $heureFin]);
        if ($stmt->fetchColumn() > 0) {
            error_log("Des créneaux existent déjà pour le " . $date . " entre " . $heureDebut . " et " . $heureFin);
            return;
        }

        $debut = new \DateTime($date . ' ' . $heureDebut);
        $fin = new \DateTime($date . ' ' . $heureFin);
        
        // Durée par défaut d'un créneau (30 minutes)
        $dureeMinutes = 30;
        $interval = new \DateInterval('PT' . $dureeMinutes . 'M');
        
        error_log("Création des créneaux pour le " . $date . " de " . $heureDebut . " à " . $heureFin);
        
        while ($debut < $fin) {
            $finCreneau = clone $debut;
            $finCreneau->add($interval);
            
            // Ne créer le créneau que s'il ne dépasse pas l'heure de fin
            if ($finCreneau <= $fin) {
                // Insérer le créneau dans la base de données
                $sql = "INSERT INTO creneau (agenda_id, debut, fin, est_reserve) 
                       VALUES (?, ?, ?, 0)";
                try {
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $agendaId,
                        $debut->format('Y-m-d H:i:s'),
                        $finCreneau->format('Y-m-d H:i:s')
                    ]);
                    error_log("Créneau créé : " . $debut->format('Y-m-d H:i:s') . " - " . $finCreneau->format('Y-m-d H:i:s'));
                } catch (\PDOException $e) {
                    error_log("Erreur lors de la création du créneau : " . $e->getMessage());
                }
            }
            
            // Avancer au prochain créneau
            $debut->add($interval);
        }
    }

    /**
     * Récupère les créneaux disponibles pour un agenda et une période donnée
     */
    public function getCreneauxDisponibles($agendaId, $dateDebut, $dateFin) {
        error_log("=== Recherche des créneaux disponibles ===");
        error_log("Agenda ID: " . $agendaId);
        error_log("Date début: " . $dateDebut);
        error_log("Date fin: " . $dateFin);

        // D'abord, vérifions que les créneaux existent bien
        $sql = "SELECT COUNT(*) as total FROM creneau";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $total = $stmt->fetch();
        error_log("Nombre total de créneaux dans la table : " . $total['total']);

        // Maintenant, récupérons les créneaux pour cet agenda et cette période
        $sql = "SELECT c.*, s.titre as service_titre, s.duree as service_duree 
                FROM creneau c 
                LEFT JOIN service s ON c.service_id = s.id
                WHERE c.agenda_id = ? 
                AND DATE(c.debut) BETWEEN ? AND ? 
                ORDER BY c.debut ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        $resultats = $stmt->fetchAll();
        
        error_log("Nombre de créneaux trouvés pour la période : " . count($resultats));
        if (!empty($resultats)) {
            error_log("Exemple de créneau : " . print_r($resultats[0], true));
        }
        
        return $resultats;
    }

}