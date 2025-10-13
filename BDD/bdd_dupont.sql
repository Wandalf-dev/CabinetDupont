-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 13 oct. 2025 à 19:43
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
(2, 3, 'Blanchiment dentaire : offres spéciales d’automne', 'Profitez d’un sourire éclatant avec notre promotion sur le blanchiment dentaire, disponible jusqu’à la fin du mois.', '2025-10-07 21:04:01', 'PUBLIE', '68e8d4c50c8ed_11664328_20944858.jpg'),
(3, 3, 'Conseils pour la première visite de votre enfant', 'Découvrez nos recommandations pour préparer en douceur la première visite chez le dentiste et instaurer de bonnes habitudes dentaires.', '2025-10-07 21:04:13', 'PUBLIE', '68e8d3d7a7781_26921713_Family brushing huge tooth flat vector illustration.jpg'),
(4, 3, 'Nouveaux horaires pour mieux vous accueillir', 'Le cabinet élargit ses horaires d’ouverture afin de s’adapter à vos disponibilités, y compris le samedi matin.', '2025-10-07 21:04:23', 'PUBLIE', '68e8d37e8d841_2149241137.jpg'),
(5, 3, 'Téléconsultation dentaire : c’est désormais possible !', 'Pour vos suivis simples ou urgences mineures, prenez rendez-vous en ligne pour une consultation vidéo sécurisée.', '2025-10-07 21:05:11', 'PUBLIE', '68e8d318e6931_2149329013.jpg'),
(14, 3, 'Des soins plus respectueux de l’environnement', 'Nous adoptons des matériaux et pratiques écoresponsables pour réduire notre impact écologique sans compromettre la qualité des soins.', '2025-10-07 22:26:31', 'PUBLIE', '68e57777e23cd_13053.jpg'),
(16, 3, 'Nouvel équipement pour des soins encore plus précis', 'Notre cabinet s’est récemment doté d’un scanner intra-oral dernière génération. Cet outil permet de réaliser des empreintes numériques plus confortables et plus précises, sans pâte ni inconfort.', '2025-10-13 09:23:59', 'PUBLIE', '68eca90f39665_7681.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(10) UNSIGNED NOT NULL,
  `utilisateur_id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agenda`
--

INSERT INTO `agenda` (`id`, `utilisateur_id`, `titre`) VALUES
(1, 3, 'Agenda Dr DUPONT');

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

--
-- Déchargement des données de la table `creneau`
--

INSERT INTO `creneau` (`id`, `agenda_id`, `service_id`, `debut`, `fin`, `est_reserve`) VALUES
(425, 1, NULL, '2025-10-13 08:00:00', '2025-10-13 08:30:00', 0),
(426, 1, NULL, '2025-10-13 08:30:00', '2025-10-13 08:30:00', 0),
(427, 1, NULL, '2025-10-13 09:00:00', '2025-10-13 09:30:00', 0),
(428, 1, NULL, '2025-10-13 09:30:00', '2025-10-13 09:30:00', 0),
(429, 1, NULL, '2025-10-13 10:00:00', '2025-10-13 10:30:00', 0),
(430, 1, NULL, '2025-10-13 10:30:00', '2025-10-13 10:30:00', 0),
(431, 1, NULL, '2025-10-13 11:00:00', '2025-10-13 11:30:00', 0),
(432, 1, NULL, '2025-10-13 11:30:00', '2025-10-13 11:30:00', 0),
(433, 1, NULL, '2025-10-13 14:00:00', '2025-10-13 14:30:00', 0),
(434, 1, NULL, '2025-10-13 14:30:00', '2025-10-13 14:30:00', 0),
(435, 1, NULL, '2025-10-13 15:00:00', '2025-10-13 15:30:00', 0),
(436, 1, NULL, '2025-10-13 15:30:00', '2025-10-13 15:30:00', 0),
(437, 1, NULL, '2025-10-13 16:00:00', '2025-10-13 16:30:00', 0),
(438, 1, NULL, '2025-10-13 16:30:00', '2025-10-13 16:30:00', 0),
(439, 1, NULL, '2025-10-13 17:00:00', '2025-10-13 17:30:00', 0),
(440, 1, NULL, '2025-10-13 17:30:00', '2025-10-13 17:30:00', 0),
(441, 1, NULL, '2025-10-13 18:00:00', '2025-10-13 18:30:00', 0),
(442, 1, NULL, '2025-10-13 18:30:00', '2025-10-13 18:30:00', 0),
(443, 1, NULL, '2025-10-13 19:00:00', '2025-10-13 19:30:00', 0),
(444, 1, NULL, '2025-10-13 19:30:00', '2025-10-13 19:30:00', 0),
(445, 1, NULL, '2025-10-14 08:00:00', '2025-10-14 08:30:00', 0),
(446, 1, NULL, '2025-10-14 08:30:00', '2025-10-14 08:30:00', 0),
(447, 1, NULL, '2025-10-14 09:00:00', '2025-10-14 09:30:00', 0),
(448, 1, NULL, '2025-10-14 09:30:00', '2025-10-14 09:30:00', 0),
(449, 1, NULL, '2025-10-14 10:00:00', '2025-10-14 10:30:00', 0),
(450, 1, NULL, '2025-10-14 10:30:00', '2025-10-14 10:30:00', 0),
(451, 1, NULL, '2025-10-14 11:00:00', '2025-10-14 11:30:00', 0),
(452, 1, NULL, '2025-10-14 11:30:00', '2025-10-14 11:30:00', 0),
(453, 1, NULL, '2025-10-14 14:00:00', '2025-10-14 14:30:00', 0),
(454, 1, NULL, '2025-10-14 14:30:00', '2025-10-14 14:30:00', 0),
(455, 1, NULL, '2025-10-14 15:00:00', '2025-10-14 15:30:00', 0),
(456, 1, NULL, '2025-10-14 15:30:00', '2025-10-14 15:30:00', 0),
(457, 1, NULL, '2025-10-14 16:00:00', '2025-10-14 16:30:00', 0),
(458, 1, NULL, '2025-10-14 16:30:00', '2025-10-14 16:30:00', 0),
(459, 1, NULL, '2025-10-14 17:00:00', '2025-10-14 17:30:00', 0),
(460, 1, NULL, '2025-10-14 17:30:00', '2025-10-14 17:30:00', 0),
(461, 1, NULL, '2025-10-14 18:00:00', '2025-10-14 18:30:00', 0),
(462, 1, NULL, '2025-10-14 18:30:00', '2025-10-14 18:30:00', 0),
(463, 1, NULL, '2025-10-14 19:00:00', '2025-10-14 19:30:00', 0),
(464, 1, NULL, '2025-10-14 19:30:00', '2025-10-14 19:30:00', 0),
(465, 1, NULL, '2025-10-15 08:00:00', '2025-10-15 08:30:00', 0),
(466, 1, NULL, '2025-10-15 08:30:00', '2025-10-15 08:30:00', 0),
(467, 1, NULL, '2025-10-15 09:00:00', '2025-10-15 09:30:00', 0),
(468, 1, NULL, '2025-10-15 09:30:00', '2025-10-15 09:30:00', 0),
(469, 1, NULL, '2025-10-15 10:00:00', '2025-10-15 10:30:00', 0),
(470, 1, NULL, '2025-10-15 10:30:00', '2025-10-15 10:30:00', 0),
(471, 1, NULL, '2025-10-15 11:00:00', '2025-10-15 11:30:00', 0),
(472, 1, NULL, '2025-10-15 11:30:00', '2025-10-15 11:30:00', 0),
(473, 1, NULL, '2025-10-15 14:00:00', '2025-10-15 14:30:00', 0),
(474, 1, NULL, '2025-10-15 14:30:00', '2025-10-15 14:30:00', 0),
(475, 1, NULL, '2025-10-15 15:00:00', '2025-10-15 15:30:00', 0),
(476, 1, NULL, '2025-10-15 15:30:00', '2025-10-15 15:30:00', 0),
(477, 1, NULL, '2025-10-15 16:00:00', '2025-10-15 16:30:00', 0),
(478, 1, NULL, '2025-10-15 16:30:00', '2025-10-15 16:30:00', 0),
(479, 1, NULL, '2025-10-15 17:00:00', '2025-10-15 17:30:00', 0),
(480, 1, NULL, '2025-10-15 17:30:00', '2025-10-15 17:30:00', 0),
(481, 1, NULL, '2025-10-15 18:00:00', '2025-10-15 18:30:00', 0),
(482, 1, NULL, '2025-10-15 18:30:00', '2025-10-15 18:30:00', 0),
(483, 1, NULL, '2025-10-15 19:00:00', '2025-10-15 19:30:00', 0),
(484, 1, NULL, '2025-10-15 19:30:00', '2025-10-15 19:30:00', 0),
(485, 1, NULL, '2025-10-16 08:00:00', '2025-10-16 08:30:00', 0),
(486, 1, NULL, '2025-10-16 08:30:00', '2025-10-16 08:30:00', 0),
(487, 1, NULL, '2025-10-16 09:00:00', '2025-10-16 09:30:00', 0),
(488, 1, NULL, '2025-10-16 09:30:00', '2025-10-16 09:30:00', 0),
(489, 1, NULL, '2025-10-16 10:00:00', '2025-10-16 10:30:00', 0),
(490, 1, NULL, '2025-10-16 10:30:00', '2025-10-16 10:30:00', 0),
(491, 1, NULL, '2025-10-16 11:00:00', '2025-10-16 11:30:00', 0),
(492, 1, NULL, '2025-10-16 11:30:00', '2025-10-16 11:30:00', 0),
(493, 1, NULL, '2025-10-16 14:00:00', '2025-10-16 14:30:00', 0),
(494, 1, NULL, '2025-10-16 14:30:00', '2025-10-16 14:30:00', 0),
(495, 1, NULL, '2025-10-16 15:00:00', '2025-10-16 15:30:00', 0),
(496, 1, NULL, '2025-10-16 15:30:00', '2025-10-16 15:30:00', 0),
(497, 1, NULL, '2025-10-16 16:00:00', '2025-10-16 16:30:00', 0),
(498, 1, NULL, '2025-10-16 16:30:00', '2025-10-16 16:30:00', 0),
(499, 1, NULL, '2025-10-16 17:00:00', '2025-10-16 17:30:00', 0),
(500, 1, NULL, '2025-10-16 17:30:00', '2025-10-16 17:30:00', 0),
(501, 1, NULL, '2025-10-16 18:00:00', '2025-10-16 18:30:00', 0),
(502, 1, NULL, '2025-10-16 18:30:00', '2025-10-16 18:30:00', 0),
(503, 1, NULL, '2025-10-16 19:00:00', '2025-10-16 19:30:00', 0),
(504, 1, NULL, '2025-10-16 19:30:00', '2025-10-16 19:30:00', 0),
(505, 1, NULL, '2025-10-17 08:00:00', '2025-10-17 08:30:00', 0),
(506, 1, NULL, '2025-10-17 08:30:00', '2025-10-17 08:30:00', 0),
(507, 1, NULL, '2025-10-17 09:00:00', '2025-10-17 09:30:00', 0),
(508, 1, NULL, '2025-10-17 09:30:00', '2025-10-17 09:30:00', 0),
(509, 1, NULL, '2025-10-17 10:00:00', '2025-10-17 10:30:00', 0),
(510, 1, NULL, '2025-10-17 10:30:00', '2025-10-17 10:30:00', 0),
(511, 1, NULL, '2025-10-17 11:00:00', '2025-10-17 11:30:00', 0),
(512, 1, NULL, '2025-10-17 11:30:00', '2025-10-17 11:30:00', 0),
(513, 1, NULL, '2025-10-17 14:00:00', '2025-10-17 14:30:00', 0),
(514, 1, NULL, '2025-10-17 14:30:00', '2025-10-17 14:30:00', 0),
(515, 1, NULL, '2025-10-17 15:00:00', '2025-10-17 15:30:00', 0),
(516, 1, NULL, '2025-10-17 15:30:00', '2025-10-17 15:30:00', 0),
(517, 1, NULL, '2025-10-17 16:00:00', '2025-10-17 16:30:00', 0),
(518, 1, NULL, '2025-10-17 16:30:00', '2025-10-17 16:30:00', 0),
(519, 1, NULL, '2025-10-17 17:00:00', '2025-10-17 17:30:00', 0),
(520, 1, NULL, '2025-10-17 17:30:00', '2025-10-17 17:30:00', 0),
(521, 1, NULL, '2025-10-17 18:00:00', '2025-10-17 18:30:00', 0),
(522, 1, NULL, '2025-10-17 18:30:00', '2025-10-17 18:30:00', 0),
(523, 1, NULL, '2025-10-17 19:00:00', '2025-10-17 19:30:00', 0),
(524, 1, NULL, '2025-10-17 19:30:00', '2025-10-17 19:30:00', 0),
(525, 1, NULL, '2025-10-18 08:00:00', '2025-10-18 08:30:00', 0),
(526, 1, NULL, '2025-10-18 08:30:00', '2025-10-18 08:30:00', 0),
(527, 1, NULL, '2025-10-18 09:00:00', '2025-10-18 09:30:00', 0),
(528, 1, NULL, '2025-10-18 09:30:00', '2025-10-18 09:30:00', 0),
(529, 1, NULL, '2025-10-18 10:00:00', '2025-10-18 10:30:00', 0),
(530, 1, NULL, '2025-10-18 10:30:00', '2025-10-18 10:30:00', 0),
(531, 1, NULL, '2025-10-18 11:00:00', '2025-10-18 11:30:00', 0),
(532, 1, NULL, '2025-10-18 11:30:00', '2025-10-18 11:30:00', 0);

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
(50, 1, 'lundi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(51, 1, 'mardi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(52, 1, 'mercredi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(53, 1, 'jeudi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(54, 1, 'vendredi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(55, 1, 'samedi', '08:00:00', '12:00:00', '00:00:00', '00:00:00'),
(56, 1, 'dimanche', '00:00:00', '00:00:00', '00:00:00', '00:00:00');

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
  `ordre` int(11) NOT NULL DEFAULT 0,
  `duree` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `titre`, `description`, `statut`, `image`, `ordre`, `duree`) VALUES
(1, 'Consultation générale', 'Examen complet de la santé bucco-dentaire, diagnostic et plan de traitement personnalisé.', 'PUBLIE', '68e6b348707a8_femme-patiente-chez-dentiste.jpg', 3, 30),
(2, 'Détartrage', 'Nettoyage professionnel des dents pour éliminer la plaque et le tartre.', 'PUBLIE', '68e7d49939d82_19475 (1).jpg', 1, 45),
(3, 'Implantologie', 'Remplacement des dents manquantes par des implants dentaires.', 'PUBLIE', '68e7d3d2412bf_5510224.jpg', 0, 90),
(4, 'Orthodontie', 'Correction de l\'alignement des dents et des problèmes d\'occlusion.', 'PUBLIE', '68e7d541958fd_17722.jpg', 2, 60);

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
(3, 'MEDECIN', 'ADMIN', 'Dupont', 'admin@demo.fr', '+33-6-00-00-00-00', NULL, '$2y$10$QgebtCj.A6H2EiY0mK/wNuxUVG/8gkNxxqCzxC4hwqsc/lEAjuUhS', '2025-10-06 22:03:33', '1990-01-01'),
(4, 'SECRETAIRE', 'DUPONT', 'Secretaire', 'secretaire@demo.fr', NULL, NULL, '$2y$10$leMn12WIQu9fTzcW.zGS4ePJsiEQC8DQVWXiXw4gGyxKop7ILSEnC', '2025-10-10 11:44:31', '1995-10-03'),
(5, 'PATIENT', 'PATIENT', 'Jean', 'patient@demo.fr', '+33-6-85-99-66-33', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 13:26:16', '1988-10-13'),
(6, 'PATIENT', 'PATIENT', 'Hugues', 'patient1@demo.fr', '4-55-88-99-99', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:21:11', '2001-12-04'),
(7, 'PATIENT', 'Martin', 'Sophie', 'sophie.martin@email.com', '+33-6-12-34-56-78', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1985-03-12'),
(8, 'PATIENT', 'Dupont', 'Jean', 'jean.dupont@email.com', '+33-6-23-45-67-89', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1978-07-22'),
(9, 'PATIENT', 'Durand', 'Claire', 'claire.durand@email.com', '+33-6-34-56-78-90', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1990-11-05'),
(10, 'PATIENT', 'Petit', 'Luc', 'luc.petit@email.com', '+33-6-45-67-89-01', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1982-01-17'),
(11, 'PATIENT', 'Lefevre', 'Emma', 'emma.lefevre@email.com', '+33-6-56-78-90-12', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1995-06-30'),
(12, 'PATIENT', 'Moreau', 'Paul', 'paul.moreau@email.com', '+33-6-67-89-01-23', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1988-09-14'),
(13, 'PATIENT', 'Girard', 'Julie', 'julie.girard@email.com', '+33-6-78-90-12-34', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1992-12-03'),
(14, 'PATIENT', 'Roux', 'Pierre', 'pierre.roux@email.com', '+33-6-89-01-23-45', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1980-04-25'),
(15, 'PATIENT', 'Blanc', 'Laura', 'laura.blanc@email.com', '+33-6-90-12-34-56', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1997-08-19'),
(16, 'PATIENT', 'Faure', 'Antoine', 'antoine.faure@email.com', '+33-6-11-12-13-14', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1983-05-08');

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
  ADD UNIQUE KEY `uk_agenda_utilisateur` (`utilisateur_id`),
  ADD UNIQUE KEY `utilisateur_agenda_unique` (`utilisateur_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `cabinet`
--
ALTER TABLE `cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `creneau`
--
ALTER TABLE `creneau`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=533;

--
-- AUTO_INCREMENT pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_agenda_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `creneau`
--
ALTER TABLE `creneau`
  ADD CONSTRAINT `fk_creneau_agenda` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE,
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
