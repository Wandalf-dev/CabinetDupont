-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 09 oct. 2025 à 23:51
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
  `statut` enum('BROUILLON','PUBLIE','ARCHIVE') NOT NULL DEFAULT 'BROUILLON',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `actualite`
--

INSERT INTO `actualite` (`id`, `auteur_id`, `titre`, `contenu`, `date_publication`, `statut`, `image`) VALUES
(1, 3, 'Nouvel équipement de radiographie 3D', 'Le cabinet s’est doté d’un appareil de radiographie 3D dernière génération pour des diagnostics encore plus précis et confortables.', '2025-10-07 21:03:50', 'PUBLIE', NULL),
(2, 3, 'Blanchiment dentaire : offres spéciales d’automne', 'Profitez d’un sourire éclatant avec notre promotion sur le blanchiment dentaire, disponible jusqu’à la fin du mois.', '2025-10-07 21:04:01', 'PUBLIE', NULL),
(3, 3, 'Conseils pour la première visite de votre enfant', 'Découvrez nos recommandations pour préparer en douceur la première visite chez le dentiste et instaurer de bonnes habitudes dentaires.', '2025-10-07 21:04:13', 'PUBLIE', NULL),
(4, 3, 'Nouveaux horaires pour mieux vous accueillir', 'Le cabinet élargit ses horaires d’ouverture afin de s’adapter à vos disponibilités, y compris le samedi matin.', '2025-10-07 21:04:23', 'PUBLIE', NULL),
(5, 3, 'Téléconsultation dentaire : c’est désormais possible !', 'Pour vos suivis simples ou urgences mineures, prenez rendez-vous en ligne pour une consultation vidéo sécurisée.', '2025-10-07 21:05:11', 'PUBLIE', NULL),
(14, 3, 'Des soins plus respectueux de l’environnement', 'Nous adoptons des matériaux et pratiques écoresponsables pour réduire notre impact écologique sans compromettre la qualité des soins.', '2025-10-07 22:26:31', 'PUBLIE', '68e57777e23cd_13053.jpg');

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

--
-- Déchargement des données de la table `cabinet`
--

INSERT INTO `cabinet` (`id`, `nom`, `adresse`) VALUES
(1, 'Cabinet Dupont', '123 rue du sourire'),
(2, 'Cabinet Dupont', '123 rue Example');

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
  `jour` enum('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche') NOT NULL,
  `ouverture_matin` time DEFAULT NULL,
  `fermeture_matin` time DEFAULT NULL,
  `ouverture_apresmidi` time DEFAULT NULL,
  `fermeture_apresmidi` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `horaire_cabinet`
--

INSERT INTO `horaire_cabinet` (`id`, `cabinet_id`, `jour`, `ouverture_matin`, `fermeture_matin`, `ouverture_apresmidi`, `fermeture_apresmidi`) VALUES
(15, 1, 'lundi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(16, 1, 'mardi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(17, 1, 'mercredi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(18, 1, 'jeudi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(19, 1, 'vendredi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(20, 1, 'samedi', '08:00:00', '12:00:00', '00:00:00', '00:00:00'),
(21, 1, 'dimanche', '00:00:00', '00:00:00', '00:00:00', '00:00:00');

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
  `titre` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `statut` enum('BROUILLON','PUBLIE','ARCHIVE') NOT NULL DEFAULT 'BROUILLON',
  `image` varchar(255) DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `titre`, `description`, `statut`, `image`, `ordre`) VALUES
(1, 'Consultation générale', 'Examen complet de la santé bucco-dentaire, diagnostic et plan de traitement personnalisé.', 'PUBLIE', '68e6b348707a8_femme-patiente-chez-dentiste.jpg', 0),
(2, 'Détartrage', 'Nettoyage professionnel des dents pour éliminer la plaque et le tartre.', 'PUBLIE', '68e7d49939d82_19475 (1).jpg', 2),
(3, 'Implantologie', 'Remplacement des dents manquantes par des implants dentaires.', 'PUBLIE', '68e7d3d2412bf_5510224.jpg', 1),
(4, 'Orthodontie', 'Correction de l\'alignement des dents et des problèmes d\'occlusion.', 'PUBLIE', '68e7d541958fd_17722.jpg', 3),
(5, 'Blanchiment dentaire', 'Procédure esthétique pour éclaircir la couleur des dents.', 'PUBLIE', '68e7d4c8844fc_2598.jpg', 4);

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
(3, 'MEDECIN', 'ADMIN', 'Dupont', 'admin@demo.fr', '+33-6-00-00-00-00', NULL, '$2y$10$QgebtCj.A6H2EiY0mK/wNuxUVG/8gkNxxqCzxC4hwqsc/lEAjuUhS', '2025-10-06 22:03:33', '1990-01-01');

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
  ADD UNIQUE KEY `unique_jour` (`cabinet_id`,`jour`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cabinet`
--
ALTER TABLE `cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `creneau`
--
ALTER TABLE `creneau`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `horaire_cabinet_ibfk_1` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
