-- Suppression de la table horaire_cabinet si elle existe
DROP TABLE IF EXISTS horaire_cabinet;

-- Création de la nouvelle table horaires
CREATE TABLE horaire_cabinet (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cabinet_id INT UNSIGNED NOT NULL,
    jour ENUM('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche') NOT NULL,
    ouverture_matin TIME,
    fermeture_matin TIME,
    ouverture_apresmidi TIME,
    fermeture_apresmidi TIME,
    FOREIGN KEY (cabinet_id) REFERENCES cabinet(id) ON DELETE CASCADE,
    UNIQUE KEY unique_jour (cabinet_id, jour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion des horaires par défaut pour tous les jours
INSERT INTO horaire_cabinet (cabinet_id, jour)
SELECT 
    (SELECT MIN(id) FROM cabinet),
    jour
FROM (
    SELECT 'lundi' as jour UNION
    SELECT 'mardi' UNION
    SELECT 'mercredi' UNION
    SELECT 'jeudi' UNION
    SELECT 'vendredi' UNION
    SELECT 'samedi' UNION
    SELECT 'dimanche'
) as jours;