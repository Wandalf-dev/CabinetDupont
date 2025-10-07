<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model {

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createUser($data) {
        $sql = "INSERT INTO utilisateur (role, nom, prenom, email, password_hash, telephone, date_naissance) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['role'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['telephone'] ?? null,
            $data['date_naissance'] ?? null
        ]);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE utilisateur SET 
                nom = ?, 
                prenom = ?, 
                email = ?, 
                telephone = ?,
                date_naissance = ?
                WHERE id = ?";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['date_naissance'],
            $userId
        ]);
    }

    public function updatePassword($userId, $newPassword) {
        $sql = "UPDATE utilisateur SET password_hash = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $userId
        ]);
    }
}