<?php

namespace App\Models;

use App\Core\Model;

class ActuModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function getAllActus($limit = null) {
        $sql = "SELECT id, titre, contenu, date_publication, statut 
                FROM actualite 
                WHERE statut = 'PUBLIE'
                ORDER BY date_publication DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    public function getFeaturedActus($limit = 3) {
        $limit = (int)$limit; // Conversion en entier pour sécurité
        $sql = "SELECT id, titre, contenu, date_publication, statut 
                FROM actualite 
                WHERE statut = 'PUBLIE'
                ORDER BY date_publication DESC 
                LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActuById($id) {
        $sql = "SELECT id, titre, contenu, date_publication, statut 
                FROM actualite 
                WHERE id = ? AND statut = 'PUBLIE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function searchActus($keyword) {
        $sql = "SELECT id, titre, contenu, date_publication, featured 
                FROM actualite 
                WHERE titre LIKE ? OR contenu LIKE ? 
                ORDER BY date_publication DESC";
        $searchTerm = "%$keyword%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    public function createActu($data) {
        $sql = "INSERT INTO actualite (auteur_id, titre, contenu, date_publication, statut) 
                VALUES (?, ?, ?, NOW(), ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $_SESSION['user_id'], // L'auteur est l'utilisateur connecté
            $data['titre'],
            $data['contenu'],
            $data['statut'] ?? 'BROUILLON' // Par défaut en brouillon
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateActu($id, $data) {
        $sql = "UPDATE actualite 
                SET titre = ?, 
                    contenu = ?, 
                    statut = ?
                WHERE id = ? AND auteur_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['titre'],
            $data['contenu'],
            $data['statut'] ?? 'BROUILLON',
            $id,
            $_SESSION['user_id'] // Sécurité : vérifier que l'utilisateur est l'auteur
        ]);
    }

    public function deleteActu($id) {
        $sql = "DELETE FROM actualite WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}