<?php

namespace App\Models;

use App\Core\Model;

class AuthModel extends Model {

    public function login($email, $password) {
        $sql = "SELECT id, nom, prenom, email, password_hash, avatar, date_inscription 
                FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return [
                'success' => true,
                'user' => $user
            ];
        }
        return [
            'success' => false,
            'error' => 'invalid'
        ];
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function register($data) {
        // Vérifier si l'email existe déjà
        $sql = "SELECT id FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Cette adresse email est déjà utilisée'];
        }

        // Insérer le nouvel utilisateur
        $sql = "INSERT INTO utilisateur (nom, prenom, email, password_hash, telephone, adresse, date_inscription) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['telephone'],
            $data['adresse']
        ]);

        if ($success) {
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
    }
}