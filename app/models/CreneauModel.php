<?php
namespace App\Models;
use App\Core\Model;

class CreneauModel extends Model {
    public function __construct() {
        parent::__construct();
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
        $sql = "SELECT c.*, s.titre as service_titre 
                FROM creneau c
                LEFT JOIN service s ON c.service_id = s.id
                WHERE DATE(c.debut) >= CURDATE()
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
                       CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END as est_reserve
                FROM creneau c
                LEFT JOIN rendezvous r ON c.id = r.creneau_id
                WHERE c.agenda_id = ? 
                AND DATE(c.debut) = ?
                ORDER BY c.debut ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $date]);
        return $stmt->fetchAll();
    }

    public function getCreneauById($id) {
        $sql = "SELECT c.*, r.id as reservation_id 
                FROM creneau c
                LEFT JOIN rendezvous r ON r.creneau_id = c.id
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

        // 1. Récupérer la durée du service
        $sql = "SELECT duree FROM service WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch();
        
        if (!$service) {
            error_log("ERREUR : Service non trouvé !");
            return [];
        }
        
        $dureeService = $service['duree'];
        error_log("Durée du service : " . $dureeService . " minutes");

        // 2. Convertir la date en jour de la semaine en français
        $joursSemaine = [
            'Monday' => 'lundi',
            'Tuesday' => 'mardi',
            'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi',
            'Friday' => 'vendredi',
            'Saturday' => 'samedi',
            'Sunday' => 'dimanche'
        ];
        $jourSemaine = $joursSemaine[date('l', strtotime($date))];
        error_log("Jour de la semaine : " . $jourSemaine);
        
        // 3. Récupérer les horaires du cabinet pour ce jour
        $sql = "SELECT * FROM horaire_cabinet WHERE jour = ? AND cabinet_id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jourSemaine]);
        $horaire = $stmt->fetch();
        error_log("Horaires trouvés : " . print_r($horaire, true));

        if (!$horaire) {
            error_log("ERREUR : Aucun horaire trouvé pour ce jour");
            return [];  // Jour non travaillé
        }

        error_log("Horaires trouvés - Matin : " . $horaire['ouverture_matin'] . " - " . $horaire['fermeture_matin']);
        error_log("Horaires trouvés - Après-midi : " . $horaire['ouverture_apresmidi'] . " - " . $horaire['fermeture_apresmidi']);

        // 4. Générer tous les créneaux possibles pour ce jour
        $creneaux = [];
        
        // Créneaux du matin
        if ($horaire['ouverture_matin'] !== '00:00:00') {
            $debut = strtotime($date . ' ' . $horaire['ouverture_matin']);
            $fin = strtotime($date . ' ' . $horaire['fermeture_matin']);
            
            // S'assurer qu'il y a assez de temps pour le service
            $fin = strtotime('-' . ($dureeService - 30) . ' minutes', $fin);
            
            while ($debut <= $fin) {
                $creneaux[] = date('H:i', $debut);
                $debut = strtotime('+30 minutes', $debut);
            }
        }
        
        // Créneaux de l'après-midi
        if ($horaire['ouverture_apresmidi'] !== '00:00:00') {
            $debut = strtotime($date . ' ' . $horaire['ouverture_apresmidi']);
            $fin = strtotime($date . ' ' . $horaire['fermeture_apresmidi']);
            
            // S'assurer qu'il y a assez de temps pour le service
            $fin = strtotime('-' . ($dureeService - 30) . ' minutes', $fin);
            
            while ($debut <= $fin) {
                $creneaux[] = date('H:i', $debut);
                $debut = strtotime('+30 minutes', $debut);
            }
        }

        // 5. Retirer les créneaux déjà réservés et ceux qui chevaucheraient des RDV existants
        $sql = "SELECT c.debut, s.duree 
                FROM creneau c 
                JOIN service s ON c.service_id = s.id 
                JOIN rendezvous r ON r.creneau_id = c.id 
                WHERE DATE(c.debut) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date]);
        $rdvExistants = $stmt->fetchAll();
        
        // Si aucun RDV existant, retourner tous les créneaux disponibles
        if (empty($rdvExistants)) {
            error_log("Aucun rendez-vous existant trouvé pour cette date");
            return $creneaux;
        }

        // Filtrer les créneaux qui chevaucheraient des RDV existants
        $creneauxDisponibles = [];
        foreach ($creneaux as $creneau) {
            $creneauDebut = strtotime($date . ' ' . $creneau);
            $creneauFin = strtotime('+' . $dureeService . ' minutes', $creneauDebut);
            
            $estDisponible = true;
            foreach ($rdvExistants as $rdv) {
                $rdvDebut = strtotime($rdv['debut']);
                $rdvFin = strtotime('+' . $rdv['duree'] . ' minutes', $rdvDebut);
                
                // Vérifier si le créneau chevauche un RDV existant
                if (
                    ($creneauDebut >= $rdvDebut && $creneauDebut < $rdvFin) ||
                    ($creneauFin > $rdvDebut && $creneauFin <= $rdvFin) ||
                    ($creneauDebut <= $rdvDebut && $creneauFin >= $rdvFin)
                ) {
                    $estDisponible = false;
                    break;
                }
            }
            
            if ($estDisponible) {
                $creneauxDisponibles[] = $creneau;
            }
        }

        return $creneauxDisponibles;
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
                $dateDebut = new \DateTime($dateDebut);
                $dateFin = new \DateTime($dateFin);
                $interval = new \DateInterval('P1D'); // Intervalle d'un jour
            } catch (\Exception $e) {
                error_log("Erreur lors de la conversion des dates : " . $e->getMessage());
                return false;
            }
            
            // Pour chaque jour entre dateDebut et dateFin
            while ($dateDebut <= $dateFin) {
                // Convertir le jour en français
                $joursEN = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $joursFR = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                $jourSemaine = str_replace($joursEN, $joursFR, strtolower($dateDebut->format('l')));
                
                error_log("Génération des créneaux pour le jour : " . $jourSemaine);
                
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
            
            return true;
        } catch (\Exception $e) {
            // Log l'erreur ou la gérer selon les besoins
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
        $debut = new \DateTime($date . ' ' . $heureDebut);
        $fin = new \DateTime($date . ' ' . $heureFin);
        
        // Durée par défaut d'un créneau (30 minutes)
        $dureeMinutes = 30;
        $interval = new \DateInterval('PT' . $dureeMinutes . 'M');
        
        error_log("Création des créneaux pour le " . $date . " de " . $heureDebut . " à " . $heureFin);
        
        while ($debut < $fin) {
            $finCreneau = clone $debut;
            $finCreneau->add($interval);
            
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
            
            // Avancer au prochain créneau
            $debut->add($interval);
            
            // Si le créneau ne dépasse pas l'heure de fin
            if ($finCreneau <= $fin) {
                $sql = "INSERT INTO creneau (agenda_id, debut, fin, est_reserve) 
                        VALUES (?, ?, ?, 0)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $agendaId,
                    $debut->format('Y-m-d H:i:s'),
                    $finCreneau->format('Y-m-d H:i:s')
                ]);
            }
            
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