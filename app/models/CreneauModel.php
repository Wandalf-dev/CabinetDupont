<?php
namespace App\Models;
use App\Core\Model;

class CreneauModel extends Model {
    public function __construct() {
        parent::__construct();
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
            // 1. Récupérer les horaires du cabinet
            $sql = "SELECT * FROM horaire_cabinet WHERE cabinet_id = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $horaires = $stmt->fetchAll();
            
            // Convertir les dates en objets DateTime
            $dateDebut = new \DateTime($dateDebut);
            $dateFin = new \DateTime($dateFin);
            $interval = new \DateInterval('P1D'); // Intervalle d'un jour
            
            // Pour chaque jour entre dateDebut et dateFin
            while ($dateDebut <= $dateFin) {
                $jourSemaine = strtolower($dateDebut->format('l')); // jour en anglais
                
                // Trouver les horaires pour ce jour
                foreach ($horaires as $horaire) {
                    if ($horaire['jour'] === $jourSemaine) {
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
        
        while ($debut < $fin) {
            $finCreneau = clone $debut;
            $finCreneau->add($interval);
            
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
        $sql = "SELECT * FROM creneau 
                WHERE agenda_id = ? 
                AND debut BETWEEN ? AND ? 
                AND est_reserve = 0 
                ORDER BY debut ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$agendaId, $dateDebut, $dateFin]);
        return $stmt->fetchAll();
    }
}