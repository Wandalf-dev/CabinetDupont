<?php

namespace App\Models;

use App\Core\Model;
use App\Entities\User;

class UserModel extends Model {
    /**
     * Convertit un tableau de données en entité User
     */
    private function arrayToEntity(array $data): User {
        if (isset($data['date_inscription'])) {
            $data['dateInscription'] = new \DateTime($data['date_inscription']);
        }
        if (isset($data['date_naissance']) && $data['date_naissance']) {
            $data['dateNaissance'] = new \DateTime($data['date_naissance']);
        }
        
        // Conversion des clés avec underscore en camelCase
        $mappedData = [];
        foreach ($data as $key => $value) {
            $camelKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $mappedData[$camelKey] = $value;
        }

        return new User($mappedData);
    }

    /**
     * Convertit une entité User en tableau pour la base de données
     */
    private function entityToArray(User $user): array {
        return [
            'id' => $user->getId(),
            'role' => $user->getRole(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'avatar' => $user->getAvatar(),
            'password_hash' => $user->getPasswordHash(),
            'date_inscription' => $user->getDateInscription()->format('Y-m-d H:i:s'),
            'date_naissance' => $user->getDateNaissance() ? $user->getDateNaissance()->format('Y-m-d') : null
        ];
    }

    /**
     * Récupère un utilisateur par son email
     */
    public function getUserByEmail(string $email): ?User {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        
        return $data ? $this->arrayToEntity($data) : null;
    }

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function createUser(User $user): int {
        $data = $this->entityToArray($user);
        
        $sql = "INSERT INTO utilisateur (role, nom, prenom, email, password_hash, telephone, 
                                       avatar, date_inscription, date_naissance) 
                VALUES (:role, :nom, :prenom, :email, :password_hash, :telephone, 
                        :avatar, :date_inscription, :date_naissance)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Met à jour un utilisateur existant
     */
    public function updateUser(User $user): bool {
        $data = $this->entityToArray($user);
        
        $sql = "UPDATE utilisateur 
                SET role = :role, nom = :nom, prenom = :prenom, 
                    email = :email, telephone = :telephone, 
                    avatar = :avatar, date_naissance = :date_naissance
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     */
    public function updatePassword(int $userId, string $passwordHash): bool {
        $sql = "UPDATE utilisateur SET password_hash = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$passwordHash, $userId]);
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public function getUserById(int $id): ?User {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        return $data ? $this->arrayToEntity($data) : null;
    }

    /**
     * Récupère tous les utilisateurs par rôle
     */
    public function getUsersByRole(string $role): array {
        $sql = "SELECT * FROM utilisateur WHERE role = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role]);
        $users = [];
        
        while ($data = $stmt->fetch()) {
            $users[] = $this->arrayToEntity($data);
        }
        
        return $users;
    }

    /**
     * Met à jour le profil d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param array $data Données du profil à mettre à jour
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public function updateProfile(int $userId, array $data): bool {
        $sql = "UPDATE utilisateur 
                SET nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    telephone = :telephone
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':id' => $userId
        ]);
    }
}