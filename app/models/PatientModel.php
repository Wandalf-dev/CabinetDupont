<?php

namespace App\Models;

class PatientModel extends \App\Core\Model {
    public function __construct() {
        parent::__construct();
    }

    // Récupérer tous les patients pour l'administration
    public function getAllPatientsAdmin() {
        $sql = "SELECT id, nom, prenom, email, telephone, date_naissance,
                DATE_FORMAT(date_inscription, '%d/%m/%Y') as date_creation 
                FROM utilisateur 
                WHERE role = 'PATIENT' 
                ORDER BY date_inscription DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer un patient par son ID
    public function getPatientById($id) {
        $sql = "SELECT * FROM utilisateur WHERE id = :id AND role = 'PATIENT'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Récupère un patient par son ID utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array|false Données du patient ou false si non trouvé
     */
    public function getPatientByUserId($userId) {
        error_log("Recherche du patient avec userId: " . $userId);
        $sql = "SELECT * FROM utilisateur WHERE id = :userId AND role = 'PATIENT'";
        error_log("SQL: " . $sql);
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        error_log("Résultat de la recherche patient: " . ($result ? "trouvé" : "non trouvé"));
        if ($result) {
            error_log("Données patient: " . print_r($result, true));
        }
        return $result;
    }

    // Créer un nouveau patient
    public function createPatient($data) {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, telephone, password_hash, date_naissance, 
                role, date_inscription) 
                VALUES (:nom, :prenom, :email, :telephone, :password, :date_naissance, 
                'PATIENT', NOW())";
        
        $stmt = $this->db->prepare($sql);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':telephone', $data['telephone']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':date_naissance', $data['date_naissance']);
    // $stmt->bindParam(':adresse', $data['adresse']); (champ supprimé)
        
        return $stmt->execute();
    }

    // Mettre à jour un patient
    public function updatePatient($id, $data) {
        $sql = "UPDATE utilisateur SET 
                nom = :nom,
                prenom = :prenom,
                email = :email,
                telephone = :telephone,
                date_naissance = :date_naissance";
        
        // Ajoute le mot de passe à la requête uniquement s'il est fourni
        if (!empty($data['password'])) {
            $sql .= ", password_hash = :password";
        }
        
        $sql .= " WHERE id = :id AND role = 'PATIENT'";
        
        $stmt = $this->db->prepare($sql);
        
        $params = [
            ':id' => $id,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':date_naissance' => $data['date_naissance']
        ];
        
        if (!empty($data['password'])) {
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        return $stmt->execute();
    }

    // Supprimer un patient
    public function deletePatient($id) {
        $sql = "DELETE FROM utilisateur WHERE id = :id AND role = 'PATIENT'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Vérifier si un email existe déjà (pour éviter les doublons)
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId, \PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}