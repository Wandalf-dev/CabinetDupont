<?php

namespace App\Core;

use App\Models\Database;

// Classe abstraite de base pour tous les modèles du projet
abstract class Model {
    // Propriété pour accéder à la base de données
    protected $db;

    // Constructeur : récupère l'instance de connexion à la base de données
    public function __construct() {
        // Utilise le singleton Database pour éviter plusieurs connexions
        $this->db = Database::getInstance();
    }

    // Méthode utilitaire pour préparer une requête SQL
    protected function prepare($sql) {
        // Retourne un objet PDOStatement prêt à être exécuté
        return $this->db->prepare($sql);
    }

    // Méthode utilitaire pour récupérer le dernier ID inséré en base
    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }
}