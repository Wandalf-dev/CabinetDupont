<?php

namespace App\Models;

class HoraireModel extends \App\Core\Model {
    public function getHoraires() {
        // Vérifie d'abord si un cabinet existe
        $checkCabinet = $this->db->prepare("SELECT COUNT(*) as count FROM cabinet");
        $checkCabinet->execute();
        $result = $checkCabinet->fetch();
        
        if ($result['count'] == 0) {
            // Si aucun cabinet n'existe, en créer un par défaut
            $insertCabinet = $this->db->prepare("INSERT INTO cabinet (nom, adresse) VALUES ('Cabinet Dupont', '123 rue Example')");
            $insertCabinet->execute();
        }

        $sql = "SELECT jour, 
                       ouverture_matin, fermeture_matin,
                       ouverture_apresmidi, fermeture_apresmidi
                FROM horaire_cabinet 
                WHERE cabinet_id = (SELECT MIN(id) FROM cabinet)
                ORDER BY FIELD(jour, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateHoraire($horaires) {
        $success = true;
        $pdo = $this->db->getConnection();
        
        try {
            $pdo->beginTransaction();

            // D'abord, supprimer tous les horaires existants
            $sql = "DELETE FROM horaire_cabinet WHERE cabinet_id = (SELECT MIN(id) FROM cabinet)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            // Ensuite, insérer les nouveaux horaires
            $sql = "INSERT INTO horaire_cabinet (
                        cabinet_id, jour, 
                        ouverture_matin, fermeture_matin,
                        ouverture_apresmidi, fermeture_apresmidi
                    ) VALUES (
                        (SELECT MIN(id) FROM cabinet), :jour,
                        :ouverture_matin, :fermeture_matin,
                        :ouverture_apresmidi, :fermeture_apresmidi
                    )";
            
            $stmt = $pdo->prepare($sql);
            
            foreach ($horaires as $jour => $creneaux) {
                $success = $success && $stmt->execute([
                    'jour' => $jour,
                    'ouverture_matin' => $creneaux['matin']['ouverture'] ?? null,
                    'fermeture_matin' => $creneaux['matin']['fermeture'] ?? null,
                    'ouverture_apresmidi' => $creneaux['apresmidi']['ouverture'] ?? null,
                    'fermeture_apresmidi' => $creneaux['apresmidi']['fermeture'] ?? null
                ]);
            }

            if ($success) {
                $pdo->commit();
                return true;
            } else {
                $pdo->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erreur lors de la mise à jour des horaires : " . $e->getMessage());
            return false;
        }
    }

    public function deleteHoraire($jour) {
        $sql = "DELETE FROM horaire_cabinet 
                WHERE cabinet_id = (SELECT MIN(id) FROM cabinet) 
                AND jour = :jour";
        
        return $this->db->prepare($sql)->execute(['jour' => $jour]);
    }
}