-- Ajoute les colonnes avatar et date_inscription à la table utilisateur
ALTER TABLE utilisateur
  ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER telephone,
  ADD COLUMN date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER password_hash;
