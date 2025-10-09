<?php

namespace App\Models;

use App\Core\Model;

// Modèle pour la gestion des utilisateurs du cabinet
class UserModel extends Model {

    // Récupère un utilisateur par son email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(); // Retourne l'utilisateur trouvé par email
    }

    // Vérifie si un email existe déjà en base
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0; // Retourne vrai si l'email existe déjà
    }

    // Crée un nouvel utilisateur en base
    public function createUser($data) {
        $sql = "INSERT INTO utilisateur (role, nom, prenom, email, password_hash, telephone, date_naissance) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['role'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT), // Hash sécurisé du mot de passe
            $data['telephone'] ?? null,
            $data['date_naissance'] ?? null
        ]);
    }

    // Récupère un utilisateur par son id
    public function getUserById($id) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); // Retourne l'utilisateur trouvé par id
    }

    // Met à jour le profil d'un utilisateur
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

    // Met à jour le mot de passe d'un utilisateur
    public function updatePassword($userId, $newPassword) {
        $sql = "UPDATE utilisateur SET password_hash = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT), // Hash sécurisé du nouveau mot de passe
            $userId
        ]);
    }
}