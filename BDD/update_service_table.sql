-- Suppression des contraintes existantes si elles existent
SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `service` DROP FOREIGN KEY IF EXISTS `fk_service_cabinet`;
SET FOREIGN_KEY_CHECKS=1;

-- Modification de la structure de la table
ALTER TABLE `service` 
  DROP COLUMN IF EXISTS `cabinet_id`,
  DROP COLUMN IF EXISTS `libelle`,
  DROP COLUMN IF EXISTS `duree_minutes`,
  DROP COLUMN IF EXISTS `tarif`;

-- Renommage de la colonne description existante
ALTER TABLE `service` CHANGE `description` `description` mediumtext NOT NULL;

-- Ajout des nouvelles colonnes
ALTER TABLE `service` 
  ADD COLUMN IF NOT EXISTS `titre` varchar(200) NOT NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `statut` enum('BROUILLON','PUBLIE','ARCHIVE') NOT NULL DEFAULT 'BROUILLON',
  ADD COLUMN IF NOT EXISTS `image` varchar(255) DEFAULT NULL;

-- Insertion de quelques services de démonstration
INSERT INTO `service` (`titre`, `description`, `statut`) VALUES
('Consultation générale', 'Examen complet de la santé bucco-dentaire, diagnostic et plan de traitement personnalisé.', 'PUBLIE'),
('Détartrage', 'Nettoyage professionnel des dents pour éliminer la plaque et le tartre.', 'PUBLIE'),
('Implantologie', 'Remplacement des dents manquantes par des implants dentaires.', 'PUBLIE'),
('Orthodontie', 'Correction de l''alignement des dents et des problèmes d''occlusion.', 'PUBLIE'),
('Blanchiment dentaire', 'Procédure esthétique pour éclaircir la couleur des dents.', 'PUBLIE');