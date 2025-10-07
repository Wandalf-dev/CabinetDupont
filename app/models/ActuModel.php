<?php
namespace App\Models;
use App\Core\Model;


class ActuModel extends Model {
    public function __construct() {
        parent::__construct();
    }        // Pour la gestion admin : toutes les actus, tous statuts
        public function getAllActusAdmin($limit = null) {
            $sql = "SELECT a.*, u.nom as auteur_nom, u.prenom as auteur_prenom 
                    FROM actualite a
                    JOIN utilisateur u ON a.auteur_id = u.id 
                    ORDER BY a.date_publication DESC";
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

    public function getAllActus($limit = null) {
        $sql = "SELECT a.*, u.nom as auteur_nom, u.prenom as auteur_prenom 
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id 
                WHERE a.statut = 'PUBLIE'
                ORDER BY a.date_publication DESC";
        
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
        $sql = "SELECT a.*, u.nom as auteur_nom, u.prenom as auteur_prenom 
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id 
                WHERE a.statut = 'PUBLIE'
                ORDER BY a.date_publication DESC 
                LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActuById($id) {
        $sql = "SELECT a.*, u.nom as auteur_nom, u.prenom as auteur_prenom 
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id 
                WHERE a.id = ? AND a.statut = 'PUBLIE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getActuByIdAdmin($id) {
        $sql = "SELECT a.*, u.nom as auteur_nom, u.prenom as auteur_prenom 
                FROM actualite a
                JOIN utilisateur u ON a.auteur_id = u.id 
                WHERE a.id = ?";
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
        // Nettoyage des données avant insertion
        $titre = html_entity_decode(strip_tags($data['titre']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $contenu = html_entity_decode(strip_tags($data['contenu']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $image = isset($data['image']) ? $data['image'] : null;
        $sql = "INSERT INTO actualite (auteur_id, titre, contenu, image, date_publication, statut) 
                VALUES (?, ?, ?, ?, ?, 'PUBLIE')";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['auteur_id'],
            $titre,
            $contenu,
            $image,
            date('Y-m-d H:i:s')
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