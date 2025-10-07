-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 07 oct. 2025 à 11:33
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdd_dupont`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualite`
--

CREATE TABLE `actualite` (
  `id` int(10) UNSIGNED NOT NULL,
  `auteur_id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` mediumtext NOT NULL,
  `date_publication` datetime DEFAULT NULL,
  `statut` enum('BROUILLON','PUBLIE','ARCHIVE') NOT NULL DEFAULT 'BROUILLON'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `actualite`
--

INSERT INTO `actualite` (`id`, `auteur_id`, `titre`, `contenu`, `date_publication`, `statut`) VALUES
(2, 3, 'Bienvenue sur notre nouveau site', 'Nous sommes heureux de vous accueillir sur notre nouveau site web. Vous y trouverez toutes les informations concernant notre cabinet dentaire.', '2025-10-07 08:41:34', 'PUBLIE'),
(3, 3, 'TEST DE MON ACTUALITE', 'TEST DE MON ACTUALITE', '2025-10-07 09:02:05', 'PUBLIE'),
(6, 3, 'JE SUIS UN TEST', 'TEST DE MODIFICATIONNNN', '2025-10-07 09:13:17', 'PUBLIE');

-- --------------------------------------------------------

--
-- Structure de la table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(10) UNSIGNED NOT NULL,
  `utilisateur_id` int(10) UNSIGNED NOT NULL,
  `semaine_iso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cabinet`
--

CREATE TABLE `cabinet` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(150) NOT NULL,
  `adresse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creneau`
--

CREATE TABLE `creneau` (
  `id` int(10) UNSIGNED NOT NULL,
  `agenda_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL,
  `debut` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `est_reserve` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `horaire_cabinet`
--

CREATE TABLE `horaire_cabinet` (
  `id` int(10) UNSIGNED NOT NULL,
  `cabinet_id` int(10) UNSIGNED NOT NULL,
  `jour` varchar(20) NOT NULL,
  `ouverture` time NOT NULL,
  `fermeture` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(10) UNSIGNED NOT NULL,
  `rendezvous_id` int(10) UNSIGNED DEFAULT NULL,
  `destinataire_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rendezvous`
--

CREATE TABLE `rendezvous` (
  `id` int(10) UNSIGNED NOT NULL,
  `creneau_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `medecin_id` int(10) UNSIGNED NOT NULL,
  `secretaire_id` int(10) UNSIGNED DEFAULT NULL,
  `statut` enum('DEMANDE','CONFIRME','ANNULE','HONORE','ABSENT') NOT NULL DEFAULT 'DEMANDE',
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(10) UNSIGNED NOT NULL,
  `cabinet_id` int(10) UNSIGNED NOT NULL,
  `libelle` varchar(120) NOT NULL,
  `duree_minutes` smallint(5) UNSIGNED NOT NULL,
  `tarif` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(10) UNSIGNED NOT NULL,
  `role` enum('PATIENT','MEDECIN','SECRETAIRE') NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `date_naissance` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `role`, `nom`, `prenom`, `email`, `telephone`, `avatar`, `password_hash`, `date_inscription`, `date_naissance`) VALUES
(3, 'MEDECIN', 'Dupont', 'Docteur', 'admin@demo.fr', '0600000000', NULL, '$2y$10$QgebtCj.A6H2EiY0mK/wNuxUVG/8gkNxxqCzxC4hwqsc/lEAjuUhS', '2025-10-06 22:03:33', '1990-01-01');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `actualite`
--
ALTER TABLE `actualite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actu_auteur` (`auteur_id`);

--
-- Index pour la table `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_agenda_utilisateur` (`utilisateur_id`);

--
-- Index pour la table `cabinet`
--
ALTER TABLE `cabinet`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `creneau`
--
ALTER TABLE `creneau`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_creneau_agenda` (`agenda_id`),
  ADD KEY `idx_creneau_service` (`service_id`),
  ADD KEY `idx_creneau_debut` (`debut`),
  ADD KEY `idx_creneau_est_reserve` (`est_reserve`);

--
-- Index pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_horcab_cabinet` (`cabinet_id`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_dest` (`destinataire_id`),
  ADD KEY `idx_notif_rdv` (`rendezvous_id`);

--
-- Index pour la table `rendezvous`
--
ALTER TABLE `rendezvous`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_rdv_creneau` (`creneau_id`),
  ADD KEY `idx_rdv_patient` (`patient_id`),
  ADD KEY `idx_rdv_medecin` (`medecin_id`),
  ADD KEY `idx_rdv_secretaire` (`secretaire_id`),
  ADD KEY `idx_rdv_statut` (`statut`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_cabinet` (`cabinet_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_utilisateur_email` (`email`),
  ADD KEY `idx_user_role` (`role`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actualite`
--
ALTER TABLE `actualite`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cabinet`
--
ALTER TABLE `cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `creneau`
--
ALTER TABLE `creneau`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rendezvous`
--
ALTER TABLE `rendezvous`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actualite`
--
ALTER TABLE `actualite`
  ADD CONSTRAINT `fk_actu_auteur` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateur` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `fk_agenda_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `creneau`
--
ALTER TABLE `creneau`
  ADD CONSTRAINT `fk_creneau_agenda` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_creneau_service` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  ADD CONSTRAINT `fk_horcab_cabinet` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notif_dest` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notif_rdv` FOREIGN KEY (`rendezvous_id`) REFERENCES `rendezvous` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `rendezvous`
--
ALTER TABLE `rendezvous`
  ADD CONSTRAINT `fk_rdv_creneau` FOREIGN KEY (`creneau_id`) REFERENCES `creneau` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rdv_medecin` FOREIGN KEY (`medecin_id`) REFERENCES `utilisateur` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rdv_patient` FOREIGN KEY (`patient_id`) REFERENCES `utilisateur` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rdv_secretaire` FOREIGN KEY (`secretaire_id`) REFERENCES `utilisateur` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `fk_service_cabinet` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
