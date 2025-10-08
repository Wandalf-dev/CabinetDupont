<?php
namespace App\Models;
use App\Core\Model;

class ServiceModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    // Pour la gestion admin : tous les services, tous statuts
    public function getAllServicesAdmin() {
        $sql = "SELECT * FROM service ORDER BY titre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllServices() {
        $sql = "SELECT * FROM service WHERE statut = 'PUBLIE' ORDER BY titre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getServiceById($id) {
        $sql = "SELECT * FROM service WHERE id = ? AND statut = 'PUBLIE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getServiceByIdAdmin($id) {
        $sql = "SELECT * FROM service WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createService($data) {
        // Nettoyage des données avant insertion
        $titre = html_entity_decode(strip_tags($data['titre']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $description = html_entity_decode(strip_tags($data['description']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $image = isset($data['image']) ? $data['image'] : null;
        
        $sql = "INSERT INTO service (titre, description, image, statut) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $titre,
            $description,
            $image,
            $data['statut'] ?? 'BROUILLON'
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateService($id, $data) {
        $params = [
            $data['titre'],
            $data['description'],
            $data['statut'] ?? 'BROUILLON'
        ];

        $setImage = '';
        if (isset($data['image']) && $data['image']) {
            $setImage = ', image = ?';
            $params[] = $data['image'];
        }
        
        $params[] = $id;
        
        $sql = "UPDATE service 
                SET titre = ?, 
                    description = ?, 
                    statut = ?
                    $setImage
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteService($id) {
        $sql = "DELETE FROM service WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}