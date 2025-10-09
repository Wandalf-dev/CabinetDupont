<?php

namespace App\Models;

use App\Core\Model;

class AuthModel extends Model {

    // Méthode pour connecter un utilisateur
    public function login($email, $password) {
        $sql = "SELECT id, nom, prenom, email, password_hash, avatar, date_inscription 
                FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Vérifie le mot de passe avec password_verify
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

    // Récupère un utilisateur par son id
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?"; // Attention : la table devrait être 'utilisateur' pour la cohérence
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Méthode pour inscrire un nouvel utilisateur
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
            password_hash($data['password'], PASSWORD_DEFAULT), // Hash sécurisé du mot de passe
            $data['telephone'],
            $data['adresse']
        ]);

        if ($success) {
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
    }
}