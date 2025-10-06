<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model {

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
                telephone = ?
                WHERE id = ?";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
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