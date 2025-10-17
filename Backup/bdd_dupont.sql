-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 17 oct. 2025 à 20:46
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
(1, 'Cabinet Dupont', '123 rue du sourire');

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
  `est_reserve` tinyint(1) NOT NULL DEFAULT 0,
  `statut` enum('disponible','reserve','indisponible') NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `creneau`
--

INSERT INTO `creneau` (`id`, `agenda_id`, `service_id`, `debut`, `fin`, `est_reserve`, `statut`) VALUES
(485, 1, NULL, '2025-10-16 08:00:00', '2025-10-16 08:30:00', 0, 'disponible'),
(486, 1, NULL, '2025-10-16 08:30:00', '2025-10-16 09:00:00', 0, 'disponible'),
(487, 1, NULL, '2025-10-16 09:00:00', '2025-10-16 09:30:00', 0, 'disponible'),
(488, 1, NULL, '2025-10-16 09:30:00', '2025-10-16 10:00:00', 0, 'disponible'),
(489, 1, NULL, '2025-10-16 10:00:00', '2025-10-16 10:30:00', 0, 'disponible'),
(490, 1, NULL, '2025-10-16 10:30:00', '2025-10-16 11:00:00', 0, 'disponible'),
(491, 1, NULL, '2025-10-16 11:00:00', '2025-10-16 11:30:00', 0, 'disponible'),
(492, 1, NULL, '2025-10-16 11:30:00', '2025-10-16 12:00:00', 0, 'disponible'),
(493, 1, NULL, '2025-10-16 14:00:00', '2025-10-16 14:30:00', 0, 'disponible'),
(494, 1, NULL, '2025-10-16 14:30:00', '2025-10-16 15:00:00', 0, 'disponible'),
(495, 1, NULL, '2025-10-16 15:00:00', '2025-10-16 15:30:00', 0, 'disponible'),
(496, 1, NULL, '2025-10-16 15:30:00', '2025-10-16 16:00:00', 0, 'disponible'),
(497, 1, NULL, '2025-10-16 16:00:00', '2025-10-16 16:30:00', 0, 'disponible'),
(498, 1, NULL, '2025-10-16 16:30:00', '2025-10-16 17:00:00', 0, 'disponible'),
(499, 1, NULL, '2025-10-16 17:00:00', '2025-10-16 17:30:00', 0, 'disponible'),
(500, 1, NULL, '2025-10-16 17:30:00', '2025-10-16 18:00:00', 0, 'disponible'),
(501, 1, NULL, '2025-10-16 18:00:00', '2025-10-16 18:30:00', 0, 'disponible'),
(502, 1, NULL, '2025-10-16 18:30:00', '2025-10-16 19:00:00', 0, 'disponible'),
(503, 1, NULL, '2025-10-16 19:00:00', '2025-10-16 19:30:00', 0, 'disponible'),
(504, 1, NULL, '2025-10-16 19:30:00', '2025-10-16 20:00:00', 0, 'disponible'),
(505, 1, 2, '2025-10-17 08:00:00', '2025-10-17 08:30:00', 1, 'disponible'),
(506, 1, NULL, '2025-10-17 08:30:00', '2025-10-17 09:00:00', 0, 'disponible'),
(507, 1, NULL, '2025-10-17 09:00:00', '2025-10-17 09:30:00', 0, 'indisponible'),
(508, 1, NULL, '2025-10-17 09:30:00', '2025-10-17 10:00:00', 0, 'indisponible'),
(509, 1, 7, '2025-10-17 10:00:00', '2025-10-17 10:30:00', 1, 'disponible'),
(510, 1, NULL, '2025-10-17 10:30:00', '2025-10-17 11:00:00', 0, 'disponible'),
(511, 1, NULL, '2025-10-17 11:00:00', '2025-10-17 11:30:00', 0, 'disponible'),
(512, 1, 7, '2025-10-17 11:30:00', '2025-10-17 12:00:00', 1, 'disponible'),
(513, 1, NULL, '2025-10-17 14:30:00', '2025-10-17 15:00:00', 0, 'disponible'),
(514, 1, NULL, '2025-10-17 14:30:00', '2025-10-17 15:00:00', 0, 'disponible'),
(515, 1, NULL, '2025-10-17 15:00:00', '2025-10-17 15:30:00', 0, 'disponible'),
(516, 1, NULL, '2025-10-17 15:30:00', '2025-10-17 16:00:00', 0, 'disponible'),
(517, 1, NULL, '2025-10-17 16:00:00', '2025-10-17 16:30:00', 0, 'disponible'),
(518, 1, NULL, '2025-10-17 16:30:00', '2025-10-17 17:00:00', 0, 'disponible'),
(519, 1, NULL, '2025-10-17 17:00:00', '2025-10-17 17:30:00', 0, 'disponible'),
(520, 1, NULL, '2025-10-17 17:30:00', '2025-10-17 18:00:00', 0, 'disponible'),
(521, 1, NULL, '2025-10-17 18:00:00', '2025-10-17 18:30:00', 0, 'disponible'),
(522, 1, NULL, '2025-10-17 18:30:00', '2025-10-17 19:00:00', 0, 'disponible'),
(523, 1, NULL, '2025-10-17 19:00:00', '2025-10-17 19:30:00', 0, 'disponible'),
(525, 1, NULL, '2025-10-18 08:00:00', '2025-10-18 08:30:00', 0, 'disponible'),
(526, 1, NULL, '2025-10-18 08:30:00', '2025-10-18 09:00:00', 0, 'disponible'),
(527, 1, 8, '2025-10-18 09:00:00', '2025-10-18 09:30:00', 1, 'disponible'),
(528, 1, 8, '2025-10-18 09:30:00', '2025-10-18 10:00:00', 1, 'disponible'),
(529, 1, NULL, '2025-10-18 10:00:00', '2025-10-18 10:30:00', 0, 'disponible'),
(530, 1, NULL, '2025-10-18 10:30:00', '2025-10-18 11:00:00', 0, 'disponible'),
(531, 1, NULL, '2025-10-18 11:00:00', '2025-10-18 11:30:00', 0, 'disponible'),
(532, 1, NULL, '2025-10-18 11:30:00', '2025-10-18 12:00:00', 0, 'disponible'),
(533, 1, NULL, '2025-10-18 14:00:00', '2025-10-18 14:30:00', 0, 'disponible'),
(534, 1, NULL, '2025-10-18 14:30:00', '2025-10-18 15:00:00', 0, 'disponible'),
(535, 1, NULL, '2025-10-18 15:00:00', '2025-10-18 15:30:00', 0, 'disponible'),
(536, 1, NULL, '2025-10-18 15:30:00', '2025-10-18 16:00:00', 0, 'disponible'),
(537, 1, NULL, '2025-10-18 16:00:00', '2025-10-18 16:30:00', 0, 'disponible'),
(538, 1, NULL, '2025-10-18 16:30:00', '2025-10-18 17:00:00', 0, 'disponible'),
(539, 1, NULL, '2025-10-18 17:00:00', '2025-10-18 17:30:00', 0, 'disponible'),
(540, 1, NULL, '2025-10-18 17:30:00', '2025-10-18 18:00:00', 0, 'disponible'),
(541, 1, NULL, '2025-10-18 18:00:00', '2025-10-18 18:30:00', 0, 'disponible'),
(542, 1, NULL, '2025-10-18 18:30:00', '2025-10-18 19:00:00', 0, 'disponible'),
(543, 1, NULL, '2025-10-18 19:00:00', '2025-10-18 19:30:00', 0, 'disponible'),
(544, 1, NULL, '2025-10-18 19:30:00', '2025-10-18 20:00:00', 0, 'disponible'),
(545, 1, NULL, '2025-10-20 08:00:00', '2025-10-20 08:30:00', 0, 'disponible'),
(546, 1, NULL, '2025-10-20 08:30:00', '2025-10-20 09:00:00', 0, 'disponible'),
(547, 1, NULL, '2025-10-20 09:00:00', '2025-10-20 09:30:00', 0, 'disponible'),
(548, 1, NULL, '2025-10-20 09:30:00', '2025-10-20 10:00:00', 0, 'disponible'),
(549, 1, NULL, '2025-10-20 10:00:00', '2025-10-20 10:30:00', 0, 'disponible'),
(550, 1, NULL, '2025-10-20 10:30:00', '2025-10-20 11:00:00', 0, 'disponible'),
(551, 1, NULL, '2025-10-20 11:00:00', '2025-10-20 11:30:00', 0, 'disponible'),
(552, 1, NULL, '2025-10-20 11:30:00', '2025-10-20 12:00:00', 0, 'disponible'),
(553, 1, NULL, '2025-10-20 14:00:00', '2025-10-20 14:30:00', 0, 'disponible'),
(554, 1, NULL, '2025-10-20 14:30:00', '2025-10-20 15:00:00', 0, 'disponible'),
(555, 1, NULL, '2025-10-20 15:00:00', '2025-10-20 15:30:00', 0, 'disponible'),
(556, 1, NULL, '2025-10-20 15:30:00', '2025-10-20 16:00:00', 0, 'disponible'),
(557, 1, NULL, '2025-10-20 16:00:00', '2025-10-20 16:30:00', 0, 'disponible'),
(558, 1, NULL, '2025-10-20 16:30:00', '2025-10-20 17:00:00', 0, 'disponible'),
(559, 1, NULL, '2025-10-20 17:00:00', '2025-10-20 17:30:00', 0, 'disponible'),
(560, 1, NULL, '2025-10-20 17:30:00', '2025-10-20 18:00:00', 0, 'disponible'),
(561, 1, NULL, '2025-10-20 18:00:00', '2025-10-20 18:30:00', 0, 'disponible'),
(562, 1, NULL, '2025-10-20 18:30:00', '2025-10-20 19:00:00', 0, 'disponible'),
(563, 1, NULL, '2025-10-20 19:00:00', '2025-10-20 19:30:00', 0, 'disponible'),
(564, 1, NULL, '2025-10-20 19:30:00', '2025-10-20 20:00:00', 0, 'disponible'),
(565, 1, NULL, '2025-10-21 08:00:00', '2025-10-21 08:30:00', 0, 'disponible'),
(566, 1, NULL, '2025-10-21 08:30:00', '2025-10-21 09:00:00', 0, 'disponible'),
(567, 1, NULL, '2025-10-21 09:00:00', '2025-10-21 09:30:00', 0, 'disponible'),
(568, 1, NULL, '2025-10-21 09:30:00', '2025-10-21 10:00:00', 0, 'disponible'),
(569, 1, NULL, '2025-10-21 10:00:00', '2025-10-21 10:30:00', 0, 'disponible'),
(570, 1, NULL, '2025-10-21 10:30:00', '2025-10-21 11:00:00', 0, 'disponible'),
(571, 1, NULL, '2025-10-21 11:00:00', '2025-10-21 11:30:00', 0, 'disponible'),
(572, 1, NULL, '2025-10-21 11:30:00', '2025-10-21 12:00:00', 0, 'disponible'),
(573, 1, NULL, '2025-10-21 14:00:00', '2025-10-21 14:30:00', 0, 'disponible'),
(574, 1, NULL, '2025-10-21 14:30:00', '2025-10-21 15:00:00', 0, 'disponible'),
(575, 1, NULL, '2025-10-21 15:00:00', '2025-10-21 15:30:00', 0, 'disponible'),
(576, 1, NULL, '2025-10-21 15:30:00', '2025-10-21 16:00:00', 0, 'disponible'),
(577, 1, NULL, '2025-10-21 16:00:00', '2025-10-21 16:30:00', 0, 'disponible'),
(578, 1, NULL, '2025-10-21 16:30:00', '2025-10-21 17:00:00', 0, 'disponible'),
(579, 1, NULL, '2025-10-21 17:00:00', '2025-10-21 17:30:00', 0, 'disponible'),
(580, 1, NULL, '2025-10-21 17:30:00', '2025-10-21 18:00:00', 0, 'disponible'),
(581, 1, NULL, '2025-10-21 18:00:00', '2025-10-21 18:30:00', 0, 'disponible'),
(582, 1, NULL, '2025-10-21 18:30:00', '2025-10-21 19:00:00', 0, 'disponible'),
(583, 1, NULL, '2025-10-21 19:00:00', '2025-10-21 19:30:00', 0, 'disponible'),
(584, 1, NULL, '2025-10-21 19:30:00', '2025-10-21 20:00:00', 0, 'disponible'),
(605, 1, NULL, '2025-10-23 08:00:00', '2025-10-23 08:30:00', 0, 'disponible'),
(606, 1, NULL, '2025-10-23 08:30:00', '2025-10-23 09:00:00', 0, 'disponible'),
(607, 1, NULL, '2025-10-23 09:00:00', '2025-10-23 09:30:00', 0, 'disponible'),
(608, 1, NULL, '2025-10-23 09:30:00', '2025-10-23 10:00:00', 0, 'disponible'),
(609, 1, NULL, '2025-10-23 10:00:00', '2025-10-23 10:30:00', 0, 'disponible'),
(610, 1, NULL, '2025-10-23 10:30:00', '2025-10-23 11:00:00', 0, 'disponible'),
(611, 1, NULL, '2025-10-23 11:00:00', '2025-10-23 11:30:00', 0, 'disponible'),
(612, 1, NULL, '2025-10-23 11:30:00', '2025-10-23 12:00:00', 0, 'disponible'),
(613, 1, NULL, '2025-10-23 14:00:00', '2025-10-23 14:30:00', 0, 'disponible'),
(614, 1, NULL, '2025-10-23 14:30:00', '2025-10-23 15:00:00', 0, 'disponible'),
(615, 1, NULL, '2025-10-23 15:00:00', '2025-10-23 15:30:00', 0, 'disponible'),
(616, 1, NULL, '2025-10-23 15:30:00', '2025-10-23 16:00:00', 0, 'disponible'),
(617, 1, NULL, '2025-10-23 16:00:00', '2025-10-23 16:30:00', 0, 'disponible'),
(618, 1, NULL, '2025-10-23 16:30:00', '2025-10-23 17:00:00', 0, 'disponible'),
(619, 1, NULL, '2025-10-23 17:00:00', '2025-10-23 17:30:00', 0, 'disponible'),
(620, 1, NULL, '2025-10-23 17:30:00', '2025-10-23 18:00:00', 0, 'disponible'),
(621, 1, NULL, '2025-10-23 18:00:00', '2025-10-23 18:30:00', 0, 'disponible'),
(622, 1, NULL, '2025-10-23 18:30:00', '2025-10-23 19:00:00', 0, 'disponible'),
(623, 1, NULL, '2025-10-23 19:00:00', '2025-10-23 19:30:00', 0, 'disponible'),
(624, 1, NULL, '2025-10-23 19:30:00', '2025-10-23 20:00:00', 0, 'disponible'),
(625, 1, NULL, '2025-10-24 08:00:00', '2025-10-24 08:30:00', 0, 'indisponible'),
(626, 1, NULL, '2025-10-24 08:30:00', '2025-10-24 09:00:00', 0, 'disponible'),
(627, 1, NULL, '2025-10-24 09:00:00', '2025-10-24 09:30:00', 0, 'disponible'),
(628, 1, NULL, '2025-10-24 09:30:00', '2025-10-24 10:00:00', 0, 'disponible'),
(629, 1, NULL, '2025-10-24 10:00:00', '2025-10-24 10:30:00', 0, 'disponible'),
(630, 1, NULL, '2025-10-24 10:30:00', '2025-10-24 11:00:00', 0, 'disponible'),
(631, 1, NULL, '2025-10-24 11:00:00', '2025-10-24 11:30:00', 0, 'disponible'),
(632, 1, NULL, '2025-10-24 11:30:00', '2025-10-24 12:00:00', 0, 'disponible'),
(633, 1, NULL, '2025-10-24 14:00:00', '2025-10-24 14:30:00', 0, 'disponible'),
(634, 1, NULL, '2025-10-24 14:30:00', '2025-10-24 15:00:00', 0, 'disponible'),
(635, 1, NULL, '2025-10-24 15:00:00', '2025-10-24 15:30:00', 0, 'disponible'),
(636, 1, NULL, '2025-10-24 15:30:00', '2025-10-24 16:00:00', 0, 'disponible'),
(637, 1, NULL, '2025-10-24 16:00:00', '2025-10-24 16:30:00', 0, 'disponible'),
(638, 1, NULL, '2025-10-24 16:30:00', '2025-10-24 17:00:00', 0, 'disponible'),
(639, 1, NULL, '2025-10-24 17:00:00', '2025-10-24 17:30:00', 0, 'disponible'),
(640, 1, NULL, '2025-10-24 17:30:00', '2025-10-24 18:00:00', 0, 'disponible'),
(641, 1, NULL, '2025-10-24 18:00:00', '2025-10-24 18:30:00', 0, 'disponible'),
(642, 1, NULL, '2025-10-24 18:30:00', '2025-10-24 19:00:00', 0, 'disponible'),
(643, 1, NULL, '2025-10-24 19:00:00', '2025-10-24 19:30:00', 0, 'disponible'),
(644, 1, NULL, '2025-10-24 19:30:00', '2025-10-24 20:00:00', 0, 'disponible'),
(645, 1, NULL, '2025-10-25 08:00:00', '2025-10-25 08:30:00', 0, 'disponible'),
(646, 1, NULL, '2025-10-25 08:30:00', '2025-10-25 09:00:00', 0, 'disponible'),
(647, 1, NULL, '2025-10-25 09:00:00', '2025-10-25 09:30:00', 0, 'disponible'),
(648, 1, NULL, '2025-10-25 09:30:00', '2025-10-25 10:00:00', 0, 'disponible'),
(649, 1, NULL, '2025-10-25 10:00:00', '2025-10-25 10:30:00', 0, 'disponible'),
(650, 1, NULL, '2025-10-25 10:30:00', '2025-10-25 11:00:00', 0, 'disponible'),
(651, 1, NULL, '2025-10-25 11:00:00', '2025-10-25 11:30:00', 0, 'disponible'),
(652, 1, NULL, '2025-10-25 11:30:00', '2025-10-25 12:00:00', 0, 'disponible'),
(653, 1, NULL, '2025-10-25 14:00:00', '2025-10-25 14:30:00', 0, 'disponible'),
(654, 1, NULL, '2025-10-25 14:30:00', '2025-10-25 15:00:00', 0, 'disponible'),
(655, 1, NULL, '2025-10-25 15:00:00', '2025-10-25 15:30:00', 0, 'disponible'),
(656, 1, NULL, '2025-10-25 15:30:00', '2025-10-25 16:00:00', 0, 'disponible'),
(657, 1, NULL, '2025-10-25 16:00:00', '2025-10-25 16:30:00', 0, 'disponible'),
(658, 1, NULL, '2025-10-25 16:30:00', '2025-10-25 17:00:00', 0, 'disponible'),
(659, 1, NULL, '2025-10-25 17:00:00', '2025-10-25 17:30:00', 0, 'disponible'),
(660, 1, NULL, '2025-10-25 17:30:00', '2025-10-25 18:00:00', 0, 'disponible'),
(661, 1, NULL, '2025-10-25 18:00:00', '2025-10-25 18:30:00', 0, 'disponible'),
(662, 1, NULL, '2025-10-25 18:30:00', '2025-10-25 19:00:00', 0, 'disponible'),
(663, 1, NULL, '2025-10-25 19:00:00', '2025-10-25 19:30:00', 0, 'disponible'),
(664, 1, NULL, '2025-10-25 19:30:00', '2025-10-25 20:00:00', 0, 'disponible'),
(665, 1, NULL, '2025-10-27 08:00:00', '2025-10-27 08:30:00', 0, 'disponible'),
(666, 1, NULL, '2025-10-27 08:30:00', '2025-10-27 09:00:00', 0, 'disponible'),
(667, 1, NULL, '2025-10-27 09:00:00', '2025-10-27 09:30:00', 0, 'disponible'),
(668, 1, NULL, '2025-10-27 09:30:00', '2025-10-27 10:00:00', 0, 'disponible'),
(669, 1, NULL, '2025-10-27 10:00:00', '2025-10-27 10:30:00', 0, 'disponible'),
(670, 1, NULL, '2025-10-27 10:30:00', '2025-10-27 11:00:00', 0, 'disponible'),
(671, 1, NULL, '2025-10-27 11:00:00', '2025-10-27 11:30:00', 0, 'disponible'),
(672, 1, NULL, '2025-10-27 11:30:00', '2025-10-27 12:00:00', 0, 'disponible'),
(673, 1, NULL, '2025-10-27 14:00:00', '2025-10-27 14:30:00', 0, 'disponible'),
(674, 1, NULL, '2025-10-27 14:30:00', '2025-10-27 15:00:00', 0, 'disponible'),
(675, 1, NULL, '2025-10-27 15:00:00', '2025-10-27 15:30:00', 0, 'disponible'),
(676, 1, NULL, '2025-10-27 15:30:00', '2025-10-27 16:00:00', 0, 'disponible'),
(677, 1, NULL, '2025-10-27 16:00:00', '2025-10-27 16:30:00', 0, 'disponible'),
(678, 1, NULL, '2025-10-27 16:30:00', '2025-10-27 17:00:00', 0, 'disponible'),
(679, 1, NULL, '2025-10-27 17:00:00', '2025-10-27 17:30:00', 0, 'disponible'),
(680, 1, NULL, '2025-10-27 17:30:00', '2025-10-27 18:00:00', 0, 'disponible'),
(681, 1, NULL, '2025-10-27 18:00:00', '2025-10-27 18:30:00', 0, 'disponible'),
(682, 1, NULL, '2025-10-27 18:30:00', '2025-10-27 19:00:00', 0, 'disponible'),
(683, 1, NULL, '2025-10-27 19:00:00', '2025-10-27 19:30:00', 0, 'disponible'),
(684, 1, NULL, '2025-10-27 19:30:00', '2025-10-27 20:00:00', 0, 'disponible'),
(685, 1, NULL, '2025-10-28 08:00:00', '2025-10-28 08:30:00', 0, 'disponible'),
(686, 1, NULL, '2025-10-28 08:30:00', '2025-10-28 09:00:00', 0, 'disponible'),
(687, 1, NULL, '2025-10-28 09:00:00', '2025-10-28 09:30:00', 0, 'disponible'),
(688, 1, NULL, '2025-10-28 09:30:00', '2025-10-28 10:00:00', 0, 'disponible'),
(689, 1, NULL, '2025-10-28 10:00:00', '2025-10-28 10:30:00', 0, 'disponible'),
(690, 1, NULL, '2025-10-28 10:30:00', '2025-10-28 11:00:00', 0, 'disponible'),
(691, 1, NULL, '2025-10-28 11:00:00', '2025-10-28 11:30:00', 0, 'disponible'),
(692, 1, NULL, '2025-10-28 11:30:00', '2025-10-28 12:00:00', 0, 'disponible'),
(693, 1, NULL, '2025-10-28 14:00:00', '2025-10-28 14:30:00', 0, 'disponible'),
(694, 1, NULL, '2025-10-28 14:30:00', '2025-10-28 15:00:00', 0, 'disponible'),
(695, 1, NULL, '2025-10-28 15:00:00', '2025-10-28 15:30:00', 0, 'disponible'),
(696, 1, NULL, '2025-10-28 15:30:00', '2025-10-28 16:00:00', 0, 'disponible'),
(697, 1, NULL, '2025-10-28 16:00:00', '2025-10-28 16:30:00', 0, 'disponible'),
(698, 1, NULL, '2025-10-28 16:30:00', '2025-10-28 17:00:00', 0, 'disponible'),
(699, 1, NULL, '2025-10-28 17:00:00', '2025-10-28 17:30:00', 0, 'disponible'),
(700, 1, NULL, '2025-10-28 17:30:00', '2025-10-28 18:00:00', 0, 'disponible'),
(701, 1, NULL, '2025-10-28 18:00:00', '2025-10-28 18:30:00', 0, 'disponible'),
(702, 1, NULL, '2025-10-28 18:30:00', '2025-10-28 19:00:00', 0, 'disponible'),
(703, 1, NULL, '2025-10-28 19:00:00', '2025-10-28 19:30:00', 0, 'disponible'),
(704, 1, NULL, '2025-10-28 19:30:00', '2025-10-28 20:00:00', 0, 'disponible'),
(705, 1, NULL, '2025-10-29 08:00:00', '2025-10-29 08:30:00', 0, 'disponible'),
(706, 1, NULL, '2025-10-29 08:30:00', '2025-10-29 09:00:00', 0, 'disponible'),
(707, 1, NULL, '2025-10-29 09:00:00', '2025-10-29 09:30:00', 0, 'disponible'),
(708, 1, NULL, '2025-10-29 09:30:00', '2025-10-29 10:00:00', 0, 'disponible'),
(709, 1, NULL, '2025-10-29 10:00:00', '2025-10-29 10:30:00', 0, 'disponible'),
(710, 1, NULL, '2025-10-29 10:30:00', '2025-10-29 11:00:00', 0, 'disponible'),
(711, 1, NULL, '2025-10-29 11:00:00', '2025-10-29 11:30:00', 0, 'disponible'),
(712, 1, NULL, '2025-10-29 11:30:00', '2025-10-29 12:00:00', 0, 'disponible'),
(713, 1, NULL, '2025-10-29 14:00:00', '2025-10-29 14:30:00', 0, 'disponible'),
(714, 1, NULL, '2025-10-29 14:30:00', '2025-10-29 15:00:00', 0, 'disponible'),
(715, 1, NULL, '2025-10-29 15:00:00', '2025-10-29 15:30:00', 0, 'disponible'),
(716, 1, NULL, '2025-10-29 15:30:00', '2025-10-29 16:00:00', 0, 'disponible'),
(717, 1, NULL, '2025-10-29 16:00:00', '2025-10-29 16:30:00', 0, 'disponible'),
(718, 1, NULL, '2025-10-29 16:30:00', '2025-10-29 17:00:00', 0, 'disponible'),
(719, 1, NULL, '2025-10-29 17:00:00', '2025-10-29 17:30:00', 0, 'disponible'),
(720, 1, NULL, '2025-10-29 17:30:00', '2025-10-29 18:00:00', 0, 'disponible'),
(721, 1, NULL, '2025-10-29 18:00:00', '2025-10-29 18:30:00', 0, 'disponible'),
(722, 1, NULL, '2025-10-29 18:30:00', '2025-10-29 19:00:00', 0, 'disponible'),
(723, 1, NULL, '2025-10-29 19:00:00', '2025-10-29 19:30:00', 0, 'disponible'),
(724, 1, NULL, '2025-10-29 19:30:00', '2025-10-29 20:00:00', 0, 'disponible'),
(725, 1, NULL, '2025-10-30 08:00:00', '2025-10-30 08:30:00', 0, 'disponible'),
(726, 1, NULL, '2025-10-30 08:30:00', '2025-10-30 09:00:00', 0, 'disponible'),
(727, 1, NULL, '2025-10-30 09:00:00', '2025-10-30 09:30:00', 0, 'disponible'),
(728, 1, NULL, '2025-10-30 09:30:00', '2025-10-30 10:00:00', 0, 'disponible'),
(729, 1, NULL, '2025-10-30 10:00:00', '2025-10-30 10:30:00', 0, 'disponible'),
(730, 1, NULL, '2025-10-30 10:30:00', '2025-10-30 11:00:00', 0, 'disponible'),
(731, 1, NULL, '2025-10-30 11:00:00', '2025-10-30 11:30:00', 0, 'disponible'),
(732, 1, NULL, '2025-10-30 11:30:00', '2025-10-30 12:00:00', 0, 'disponible'),
(733, 1, NULL, '2025-10-30 14:00:00', '2025-10-30 14:30:00', 0, 'disponible'),
(734, 1, NULL, '2025-10-30 14:30:00', '2025-10-30 15:00:00', 0, 'disponible'),
(735, 1, NULL, '2025-10-30 15:00:00', '2025-10-30 15:30:00', 0, 'disponible'),
(736, 1, NULL, '2025-10-30 15:30:00', '2025-10-30 16:00:00', 0, 'disponible'),
(737, 1, NULL, '2025-10-30 16:00:00', '2025-10-30 16:30:00', 0, 'disponible'),
(738, 1, NULL, '2025-10-30 16:30:00', '2025-10-30 17:00:00', 0, 'disponible'),
(739, 1, NULL, '2025-10-30 17:00:00', '2025-10-30 17:30:00', 0, 'disponible'),
(740, 1, NULL, '2025-10-30 17:30:00', '2025-10-30 18:00:00', 0, 'disponible'),
(741, 1, NULL, '2025-10-30 18:00:00', '2025-10-30 18:30:00', 0, 'disponible'),
(742, 1, NULL, '2025-10-30 18:30:00', '2025-10-30 19:00:00', 0, 'disponible'),
(743, 1, NULL, '2025-10-30 19:00:00', '2025-10-30 19:30:00', 0, 'disponible'),
(744, 1, NULL, '2025-10-30 19:30:00', '2025-10-30 20:00:00', 0, 'disponible'),
(745, 1, NULL, '2025-10-31 08:00:00', '2025-10-31 08:30:00', 0, 'disponible'),
(746, 1, NULL, '2025-10-31 08:30:00', '2025-10-31 09:00:00', 0, 'disponible'),
(747, 1, NULL, '2025-10-31 09:00:00', '2025-10-31 09:30:00', 0, 'disponible'),
(748, 1, NULL, '2025-10-31 09:30:00', '2025-10-31 10:00:00', 0, 'disponible'),
(749, 1, NULL, '2025-10-31 10:00:00', '2025-10-31 10:30:00', 0, 'disponible'),
(750, 1, NULL, '2025-10-31 10:30:00', '2025-10-31 11:00:00', 0, 'disponible'),
(751, 1, NULL, '2025-10-31 11:00:00', '2025-10-31 11:30:00', 0, 'disponible'),
(752, 1, NULL, '2025-10-31 11:30:00', '2025-10-31 12:00:00', 0, 'disponible'),
(753, 1, NULL, '2025-10-31 14:00:00', '2025-10-31 14:30:00', 0, 'disponible'),
(754, 1, NULL, '2025-10-31 14:30:00', '2025-10-31 15:00:00', 0, 'disponible'),
(755, 1, NULL, '2025-10-31 15:00:00', '2025-10-31 15:30:00', 0, 'disponible'),
(756, 1, NULL, '2025-10-31 15:30:00', '2025-10-31 16:00:00', 0, 'disponible'),
(757, 1, NULL, '2025-10-31 16:00:00', '2025-10-31 16:30:00', 0, 'disponible'),
(758, 1, NULL, '2025-10-31 16:30:00', '2025-10-31 17:00:00', 0, 'disponible'),
(759, 1, NULL, '2025-10-31 17:00:00', '2025-10-31 17:30:00', 0, 'disponible'),
(760, 1, NULL, '2025-10-31 17:30:00', '2025-10-31 18:00:00', 0, 'disponible'),
(761, 1, NULL, '2025-10-31 18:00:00', '2025-10-31 18:30:00', 0, 'disponible'),
(762, 1, NULL, '2025-10-31 18:30:00', '2025-10-31 19:00:00', 0, 'disponible'),
(763, 1, NULL, '2025-10-31 19:00:00', '2025-10-31 19:30:00', 0, 'disponible'),
(765, 1, NULL, '2025-10-22 08:00:00', '2025-10-22 08:30:00', 0, 'indisponible'),
(766, 1, NULL, '2025-10-22 08:30:00', '2025-10-22 09:00:00', 0, 'indisponible'),
(767, 1, NULL, '2025-10-22 09:00:00', '2025-10-22 09:30:00', 0, 'indisponible'),
(768, 1, NULL, '2025-10-22 09:30:00', '2025-10-22 10:00:00', 0, 'indisponible'),
(769, 1, NULL, '2025-10-22 10:00:00', '2025-10-22 10:30:00', 0, 'indisponible'),
(770, 1, NULL, '2025-10-22 10:30:00', '2025-10-22 11:00:00', 0, 'indisponible'),
(771, 1, NULL, '2025-10-22 11:00:00', '2025-10-22 11:30:00', 0, 'indisponible'),
(772, 1, NULL, '2025-10-22 11:30:00', '2025-10-22 12:00:00', 0, 'indisponible'),
(773, 1, NULL, '2025-10-22 14:00:00', '2025-10-22 14:30:00', 0, 'disponible'),
(774, 1, NULL, '2025-10-22 14:30:00', '2025-10-22 15:00:00', 0, 'disponible'),
(775, 1, NULL, '2025-10-22 15:00:00', '2025-10-22 15:30:00', 0, 'disponible'),
(776, 1, NULL, '2025-10-22 15:30:00', '2025-10-22 16:00:00', 0, 'disponible'),
(777, 1, NULL, '2025-10-22 16:00:00', '2025-10-22 16:30:00', 0, 'disponible'),
(778, 1, NULL, '2025-10-22 16:30:00', '2025-10-22 17:00:00', 0, 'disponible'),
(779, 1, NULL, '2025-10-22 17:00:00', '2025-10-22 17:30:00', 0, 'disponible'),
(780, 1, NULL, '2025-10-22 17:30:00', '2025-10-22 18:00:00', 0, 'disponible'),
(781, 1, NULL, '2025-10-22 18:00:00', '2025-10-22 18:30:00', 0, 'disponible'),
(782, 1, NULL, '2025-10-22 18:30:00', '2025-10-22 19:00:00', 0, 'disponible'),
(783, 1, NULL, '2025-10-22 19:00:00', '2025-10-22 19:30:00', 0, 'disponible'),
(784, 1, NULL, '2025-10-22 19:30:00', '2025-10-22 20:00:00', 0, 'disponible');

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
(120, 1, 'lundi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(121, 1, 'mardi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(122, 1, 'mercredi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(123, 1, 'jeudi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(124, 1, 'vendredi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(125, 1, 'samedi', '08:00:00', '12:00:00', '14:00:00', '20:00:00'),
(126, 1, 'dimanche', '00:00:00', '00:00:00', '00:00:00', '00:00:00');

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

--
-- Déchargement des données de la table `rendezvous`
--

INSERT INTO `rendezvous` (`id`, `creneau_id`, `patient_id`, `medecin_id`, `secretaire_id`, `statut`, `commentaire`) VALUES
(31, 505, 6, 3, NULL, 'DEMANDE', NULL),
(32, 506, 6, 3, NULL, 'ANNULE', NULL),
(33, 507, 6, 3, NULL, 'ANNULE', NULL),
(34, 508, 6, 3, NULL, 'ANNULE', NULL),
(35, 509, 6, 3, NULL, 'DEMANDE', NULL),
(36, 510, 6, 3, NULL, 'ANNULE', NULL),
(37, 511, 6, 3, NULL, 'ANNULE', NULL),
(38, 512, 6, 3, NULL, 'ANNULE', NULL),
(39, 513, 6, 3, NULL, 'ANNULE', NULL),
(40, 514, 6, 3, NULL, 'ANNULE', NULL),
(41, 515, 6, 3, NULL, 'ANNULE', NULL),
(42, 516, 6, 3, NULL, 'ANNULE', NULL),
(43, 517, 6, 3, NULL, 'ANNULE', NULL),
(44, 518, 6, 3, NULL, 'ANNULE', NULL),
(45, 519, 6, 3, NULL, 'ANNULE', NULL),
(46, 520, 6, 3, NULL, 'ANNULE', NULL),
(66, 527, 6, 3, NULL, 'DEMANDE', NULL);

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
  `duree` int(11) NOT NULL DEFAULT 30,
  `couleur` varchar(7) DEFAULT '#4CAF50'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `titre`, `description`, `statut`, `image`, `ordre`, `duree`, `couleur`) VALUES
(1, 'Consultation générale', 'Examen complet, diagnostic et plan de traitement personnalisé.', 'PUBLIE', '68e6b348707a8_femme-patiente-chez-dentiste.jpg', 3, 30, '#4caf50'),
(2, 'Détartrage', 'Nettoyage professionnel des dents pour éliminer la plaque et le tartre.', 'PUBLIE', '68e7d49939d82_19475 (1).jpg', 2, 30, '#2196f3'),
(3, 'Implantologie', 'Pose d’implants pour remplacer les dents manquantes.', 'PUBLIE', '68e7d3d2412bf_5510224.jpg', 5, 90, '#9c27b0'),
(4, 'Orthodontie', 'Alignement des dents : appareils fixes ou aligneurs transparents.', 'PUBLIE', '68e7d541958fd_17722.jpg', 1, 30, '#00c9cc'),
(7, 'Parodontologie', 'Soins des gencives et traitement des maladies parodontales.', 'PUBLIE', '68ee48ae76743_5536487.jpg', 4, 120, '#e0ed2c'),
(8, 'Urgences dentaires', 'Prise en charge rapide des douleurs et traumatismes dentaires.', 'PUBLIE', '68ee4d73cf020_ChatGPT Image 14 oct. 2025, 15_13_39.png', 0, 60, '#ed0202');

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
(3, 'MEDECIN', 'ADMIN', 'Dupont', 'admin@demo.fr', '+33-7-00-00-00-25', NULL, '$2y$10$QgebtCj.A6H2EiY0mK/wNuxUVG/8gkNxxqCzxC4hwqsc/lEAjuUhS', '2025-10-06 22:03:33', '1990-01-04'),
(4, 'SECRETAIRE', 'DUPONT', 'Secretaire', 'secretaire@demo.fr', NULL, NULL, '$2y$10$leMn12WIQu9fTzcW.zGS4ePJsiEQC8DQVWXiXw4gGyxKop7ILSEnC', '2025-10-10 11:44:31', '1995-10-03'),
(5, 'PATIENT', 'PATIENT', 'Jean', 'patient@demo.fr', '+33-6-85-99-66-33', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 13:26:16', '1988-10-13'),
(6, 'PATIENT', 'PATIENT', 'Hugues', 'patient1@demo.fr', '+33-4-55-88-99-99', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:21:11', '2001-12-04'),
(7, 'PATIENT', 'Martin', 'Sophie', 'sophie.martin@email.com', '6-12-34-56-78', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1985-03-12'),
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=785;

--
-- AUTO_INCREMENT pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rendezvous`
--
ALTER TABLE `rendezvous`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
