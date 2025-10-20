<?php
namespace App\Models;

use PDO;
use PDOException;

class Database {
    private static $instance = null; // Singleton : une seule instance partagée
    private $connection;

    private function __construct() {
        // Utiliser les constantes définies dans config.php
        $host = DB_HOST;
        $dbname = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;
        $charset = DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions PDO
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retourne les résultats sous forme de tableau associatif
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset" // Force l'encodage
        ];

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=$charset",
                $user,
                $pass,
                $options
            );
        } catch (PDOException $e) {
            die('Erreur connexion BDD : ' . $e->getMessage()); // Affiche l'erreur et stoppe le script
        }
    }

    public function getConnection() {
        return $this->connection; // Retourne l'objet PDO
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql); // Prépare une requête SQL
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId(); // Retourne le dernier ID inséré
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function inTransaction() {
        return $this->connection->inTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self(); // Crée l'instance si elle n'existe pas
        }
        return self::$instance;
    }

    public static function getPDO() {
        return self::getInstance()->getConnection(); // Accès direct à l'objet PDO
    }
}