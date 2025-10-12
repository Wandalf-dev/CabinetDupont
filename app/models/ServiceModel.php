<?php
namespace App\Models;
use App\Core\Model;

class ServiceModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    // Pour la gestion admin : tous les services, tous statuts
    public function getAllServicesAdmin() {
        $sql = "SELECT * FROM service ORDER BY ordre ASC, titre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(); // Retourne tous les services pour l'admin
    }

    public function getAllServices() {
        $sql = "SELECT * FROM service WHERE statut = 'PUBLIE' ORDER BY ordre ASC, titre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(); // Retourne uniquement les services publiés
    }

    public function getServiceById($id) {
        $sql = "SELECT * FROM service WHERE id = ? AND statut = 'PUBLIE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); // Retourne un service publié par son id
    }

    public function getServiceByIdAdmin($id) {
        $sql = "SELECT * FROM service WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); // Retourne un service (tous statuts) par son id
    }

    public function createService($data) {
        // Nettoyage des données avant insertion
        $titre = html_entity_decode(strip_tags($data['titre']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $description = html_entity_decode(strip_tags($data['description']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $image = isset($data['image']) ? $data['image'] : null;
        
        $sql = "INSERT INTO service (titre, description, image, statut, duree) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $titre,
            $description,
            $image,
            $data['statut'] ?? 'BROUILLON',
            intval($data['duree'] ?? 30)
        ]);

        if ($result) {
            return $this->db->lastInsertId(); // Retourne l'id du nouveau service
        }
        return false;
    }

    public function updateService($id, $data) {
        $params = [
            $data['titre'],
            $data['description'],
            $data['statut'] ?? 'BROUILLON',
            intval($data['duree'] ?? 30)
        ];

        $setImage = '';
        if (isset($data['image']) && $data['image']) {
            $setImage = ', image = ?'; // Ajoute l'image si elle existe
            $params[] = $data['image'];
        }
        
        $params[] = $id;
        
        $sql = "UPDATE service 
                SET titre = ?, 
                    description = ?, 
                    statut = ?,
                    duree = ?
                    $setImage
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params); // Met à jour le service
    }

    public function deleteService($id) {
        $sql = "DELETE FROM service WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]); // Supprime le service par son id
    }

    public function updateOrdre($id, $ordre) {
        $sql = "UPDATE service SET ordre = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ordre, $id]); // Met à jour l'ordre d'affichage du service
    }
}