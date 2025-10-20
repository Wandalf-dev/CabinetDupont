-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : sql210.infinityfree.com
-- Généré le :  lun. 20 oct. 2025 à 07:32
-- Version du serveur :  11.4.7-MariaDB
-- Version de PHP :  7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `if0_40207543_bdd_dupont`
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
(785, 1, NULL, '2025-10-17 08:00:00', '2025-10-17 08:30:00', 0, 'indisponible'),
(786, 1, NULL, '2025-10-17 08:30:00', '2025-10-17 09:00:00', 0, 'indisponible'),
(787, 1, NULL, '2025-10-17 09:00:00', '2025-10-17 09:30:00', 0, 'indisponible'),
(788, 1, NULL, '2025-10-17 09:30:00', '2025-10-17 10:00:00', 0, 'indisponible'),
(789, 1, NULL, '2025-10-17 10:00:00', '2025-10-17 10:30:00', 0, 'indisponible'),
(790, 1, NULL, '2025-10-17 10:30:00', '2025-10-17 11:00:00', 0, 'indisponible'),
(791, 1, NULL, '2025-10-17 11:00:00', '2025-10-17 11:30:00', 0, 'indisponible'),
(792, 1, NULL, '2025-10-17 11:30:00', '2025-10-17 12:00:00', 0, 'indisponible'),
(793, 1, NULL, '2025-10-17 14:00:00', '2025-10-17 14:30:00', 0, 'indisponible'),
(794, 1, NULL, '2025-10-17 14:30:00', '2025-10-17 15:00:00', 0, 'indisponible'),
(795, 1, NULL, '2025-10-17 15:00:00', '2025-10-17 15:30:00', 0, 'indisponible'),
(796, 1, NULL, '2025-10-17 15:30:00', '2025-10-17 16:00:00', 0, 'indisponible'),
(797, 1, NULL, '2025-10-17 16:00:00', '2025-10-17 16:30:00', 0, 'indisponible'),
(798, 1, NULL, '2025-10-17 16:30:00', '2025-10-17 17:00:00', 0, 'indisponible'),
(799, 1, NULL, '2025-10-17 17:00:00', '2025-10-17 17:30:00', 0, 'indisponible'),
(800, 1, NULL, '2025-10-17 17:30:00', '2025-10-17 18:00:00', 0, 'indisponible'),
(801, 1, NULL, '2025-10-17 18:00:00', '2025-10-17 18:30:00', 0, 'indisponible'),
(802, 1, NULL, '2025-10-17 18:30:00', '2025-10-17 19:00:00', 0, 'indisponible'),
(803, 1, NULL, '2025-10-17 19:00:00', '2025-10-17 19:30:00', 0, 'indisponible'),
(804, 1, NULL, '2025-10-17 19:30:00', '2025-10-17 20:00:00', 0, 'indisponible'),
(805, 1, NULL, '2025-10-18 08:00:00', '2025-10-18 08:30:00', 0, 'indisponible'),
(806, 1, NULL, '2025-10-18 08:30:00', '2025-10-18 09:00:00', 0, 'disponible'),
(807, 1, 1, '2025-10-18 09:00:00', '2025-10-18 09:30:00', 1, 'disponible'),
(808, 1, NULL, '2025-10-18 09:30:00', '2025-10-18 10:00:00', 0, 'disponible'),
(809, 1, NULL, '2025-10-18 10:00:00', '2025-10-18 10:30:00', 0, 'disponible'),
(810, 1, NULL, '2025-10-18 10:30:00', '2025-10-18 11:00:00', 0, 'disponible'),
(811, 1, 1, '2025-10-18 11:00:00', '2025-10-18 11:30:00', 1, 'disponible'),
(812, 1, NULL, '2025-10-18 11:30:00', '2025-10-18 12:00:00', 0, 'disponible'),
(813, 1, NULL, '2025-10-18 14:00:00', '2025-10-18 14:30:00', 0, 'disponible'),
(814, 1, NULL, '2025-10-18 14:30:00', '2025-10-18 15:00:00', 0, 'disponible'),
(815, 1, NULL, '2025-10-18 15:00:00', '2025-10-18 15:30:00', 0, 'disponible'),
(816, 1, NULL, '2025-10-18 15:30:00', '2025-10-18 16:00:00', 0, 'disponible'),
(817, 1, NULL, '2025-10-18 16:00:00', '2025-10-18 16:30:00', 0, 'disponible'),
(818, 1, NULL, '2025-10-18 16:30:00', '2025-10-18 17:00:00', 0, 'disponible'),
(819, 1, NULL, '2025-10-18 17:00:00', '2025-10-18 17:30:00', 0, 'disponible'),
(820, 1, NULL, '2025-10-18 17:30:00', '2025-10-18 18:00:00', 0, 'disponible'),
(821, 1, NULL, '2025-10-18 18:00:00', '2025-10-18 18:30:00', 0, 'disponible'),
(822, 1, NULL, '2025-10-18 18:30:00', '2025-10-18 19:00:00', 0, 'disponible'),
(823, 1, NULL, '2025-10-18 19:00:00', '2025-10-18 19:30:00', 0, 'disponible'),
(824, 1, NULL, '2025-10-18 19:30:00', '2025-10-18 20:00:00', 0, 'disponible'),
(845, 1, NULL, '2025-10-21 08:00:00', '2025-10-21 08:30:00', 0, 'disponible'),
(846, 1, NULL, '2025-10-21 08:30:00', '2025-10-21 09:00:00', 0, 'disponible'),
(847, 1, NULL, '2025-10-21 09:00:00', '2025-10-21 09:30:00', 0, 'disponible'),
(848, 1, NULL, '2025-10-21 09:30:00', '2025-10-21 10:00:00', 0, 'disponible'),
(849, 1, NULL, '2025-10-21 10:00:00', '2025-10-21 10:30:00', 0, 'disponible'),
(850, 1, NULL, '2025-10-21 10:30:00', '2025-10-21 11:00:00', 0, 'disponible'),
(851, 1, NULL, '2025-10-21 11:00:00', '2025-10-21 11:30:00', 0, 'disponible'),
(852, 1, NULL, '2025-10-21 11:30:00', '2025-10-21 12:00:00', 0, 'disponible'),
(853, 1, NULL, '2025-10-21 14:00:00', '2025-10-21 14:30:00', 0, 'disponible'),
(854, 1, NULL, '2025-10-21 14:30:00', '2025-10-21 15:00:00', 0, 'disponible'),
(855, 1, 3, '2025-10-21 15:00:00', '2025-10-21 15:30:00', 1, 'disponible'),
(856, 1, NULL, '2025-10-21 15:30:00', '2025-10-21 16:00:00', 0, 'disponible'),
(857, 1, NULL, '2025-10-21 16:00:00', '2025-10-21 16:30:00', 0, 'disponible'),
(858, 1, NULL, '2025-10-21 16:30:00', '2025-10-21 17:00:00', 0, 'disponible'),
(859, 1, NULL, '2025-10-21 17:00:00', '2025-10-21 17:30:00', 0, 'disponible'),
(860, 1, NULL, '2025-10-21 17:30:00', '2025-10-21 18:00:00', 0, 'disponible'),
(861, 1, NULL, '2025-10-21 18:00:00', '2025-10-21 18:30:00', 0, 'disponible'),
(862, 1, NULL, '2025-10-21 18:30:00', '2025-10-21 19:00:00', 0, 'disponible'),
(863, 1, NULL, '2025-10-21 19:00:00', '2025-10-21 19:30:00', 0, 'disponible'),
(864, 1, NULL, '2025-10-21 19:30:00', '2025-10-21 20:00:00', 0, 'disponible'),
(865, 1, NULL, '2025-10-22 08:00:00', '2025-10-22 08:30:00', 0, 'indisponible'),
(866, 1, NULL, '2025-10-22 08:30:00', '2025-10-22 09:00:00', 0, 'disponible'),
(867, 1, NULL, '2025-10-22 09:00:00', '2025-10-22 09:30:00', 0, 'disponible'),
(868, 1, NULL, '2025-10-22 09:30:00', '2025-10-22 10:00:00', 0, 'disponible'),
(869, 1, NULL, '2025-10-22 10:00:00', '2025-10-22 10:30:00', 0, 'disponible'),
(870, 1, NULL, '2025-10-22 10:30:00', '2025-10-22 11:00:00', 0, 'disponible'),
(871, 1, NULL, '2025-10-22 11:00:00', '2025-10-22 11:30:00', 0, 'disponible'),
(872, 1, NULL, '2025-10-22 11:30:00', '2025-10-22 12:00:00', 0, 'disponible'),
(873, 1, NULL, '2025-10-22 14:00:00', '2025-10-22 14:30:00', 0, 'disponible'),
(874, 1, 7, '2025-10-22 14:30:00', '2025-10-22 15:00:00', 1, 'disponible'),
(875, 1, NULL, '2025-10-22 15:00:00', '2025-10-22 15:30:00', 0, 'disponible'),
(876, 1, NULL, '2025-10-22 15:30:00', '2025-10-22 16:00:00', 0, 'disponible'),
(877, 1, NULL, '2025-10-22 16:00:00', '2025-10-22 16:30:00', 0, 'disponible'),
(878, 1, 4, '2025-10-22 16:30:00', '2025-10-22 17:00:00', 1, 'disponible'),
(879, 1, NULL, '2025-10-22 17:00:00', '2025-10-22 17:30:00', 0, 'disponible'),
(880, 1, NULL, '2025-10-22 17:30:00', '2025-10-22 18:00:00', 0, 'disponible'),
(881, 1, NULL, '2025-10-22 18:00:00', '2025-10-22 18:30:00', 0, 'disponible'),
(882, 1, NULL, '2025-10-22 18:30:00', '2025-10-22 19:00:00', 0, 'disponible'),
(883, 1, NULL, '2025-10-22 19:00:00', '2025-10-22 19:30:00', 0, 'disponible'),
(884, 1, NULL, '2025-10-22 19:30:00', '2025-10-22 20:00:00', 0, 'disponible'),
(885, 1, 8, '2025-10-23 08:00:00', '2025-10-23 08:30:00', 1, 'disponible'),
(886, 1, NULL, '2025-10-23 08:30:00', '2025-10-23 09:00:00', 0, 'disponible'),
(887, 1, NULL, '2025-10-23 09:00:00', '2025-10-23 09:30:00', 0, 'disponible'),
(888, 1, NULL, '2025-10-23 09:30:00', '2025-10-23 10:00:00', 0, 'disponible'),
(889, 1, NULL, '2025-10-23 10:00:00', '2025-10-23 10:30:00', 0, 'disponible'),
(890, 1, NULL, '2025-10-23 10:30:00', '2025-10-23 11:00:00', 0, 'disponible'),
(891, 1, NULL, '2025-10-23 11:00:00', '2025-10-23 11:30:00', 0, 'disponible'),
(892, 1, NULL, '2025-10-23 11:30:00', '2025-10-23 12:00:00', 0, 'disponible'),
(893, 1, NULL, '2025-10-23 14:00:00', '2025-10-23 14:30:00', 0, 'disponible'),
(894, 1, NULL, '2025-10-23 14:30:00', '2025-10-23 15:00:00', 0, 'disponible'),
(895, 1, NULL, '2025-10-23 15:00:00', '2025-10-23 15:30:00', 0, 'disponible'),
(896, 1, NULL, '2025-10-23 15:30:00', '2025-10-23 16:00:00', 0, 'disponible'),
(897, 1, 1, '2025-10-23 16:00:00', '2025-10-23 16:30:00', 1, 'disponible'),
(898, 1, NULL, '2025-10-23 16:30:00', '2025-10-23 17:00:00', 0, 'disponible'),
(899, 1, NULL, '2025-10-23 17:00:00', '2025-10-23 17:30:00', 0, 'disponible'),
(900, 1, NULL, '2025-10-23 17:30:00', '2025-10-23 18:00:00', 0, 'disponible'),
(901, 1, NULL, '2025-10-23 18:00:00', '2025-10-23 18:30:00', 0, 'disponible'),
(902, 1, NULL, '2025-10-23 18:30:00', '2025-10-23 19:00:00', 0, 'disponible'),
(903, 1, NULL, '2025-10-23 19:00:00', '2025-10-23 19:30:00', 0, 'disponible'),
(904, 1, NULL, '2025-10-23 19:30:00', '2025-10-23 20:00:00', 0, 'disponible'),
(905, 1, NULL, '2025-10-24 08:00:00', '2025-10-24 08:30:00', 0, 'disponible'),
(906, 1, NULL, '2025-10-24 08:30:00', '2025-10-24 09:00:00', 0, 'disponible'),
(907, 1, NULL, '2025-10-24 09:00:00', '2025-10-24 09:30:00', 0, 'disponible'),
(908, 1, NULL, '2025-10-24 09:30:00', '2025-10-24 10:00:00', 0, 'disponible'),
(909, 1, NULL, '2025-10-24 10:00:00', '2025-10-24 10:30:00', 0, 'disponible'),
(910, 1, NULL, '2025-10-24 10:30:00', '2025-10-24 11:00:00', 0, 'disponible'),
(911, 1, NULL, '2025-10-24 11:00:00', '2025-10-24 11:30:00', 0, 'disponible'),
(912, 1, NULL, '2025-10-24 11:30:00', '2025-10-24 12:00:00', 0, 'disponible'),
(913, 1, NULL, '2025-10-24 14:00:00', '2025-10-24 14:30:00', 0, 'disponible'),
(914, 1, 7, '2025-10-24 14:30:00', '2025-10-24 15:00:00', 1, 'disponible'),
(915, 1, NULL, '2025-10-24 15:00:00', '2025-10-24 15:30:00', 0, 'disponible'),
(916, 1, NULL, '2025-10-24 15:30:00', '2025-10-24 16:00:00', 0, 'disponible'),
(917, 1, NULL, '2025-10-24 16:00:00', '2025-10-24 16:30:00', 0, 'disponible'),
(918, 1, 4, '2025-10-24 16:30:00', '2025-10-24 17:00:00', 1, 'disponible'),
(919, 1, NULL, '2025-10-24 17:00:00', '2025-10-24 17:30:00', 0, 'disponible'),
(920, 1, NULL, '2025-10-24 17:30:00', '2025-10-24 18:00:00', 0, 'disponible'),
(921, 1, NULL, '2025-10-24 18:00:00', '2025-10-24 18:30:00', 0, 'disponible'),
(922, 1, NULL, '2025-10-24 18:30:00', '2025-10-24 19:00:00', 0, 'disponible'),
(923, 1, NULL, '2025-10-24 19:00:00', '2025-10-24 19:30:00', 0, 'disponible'),
(924, 1, NULL, '2025-10-24 19:30:00', '2025-10-24 20:00:00', 0, 'disponible'),
(925, 1, NULL, '2025-10-25 08:00:00', '2025-10-25 08:30:00', 0, 'disponible'),
(926, 1, 8, '2025-10-25 08:30:00', '2025-10-25 09:00:00', 1, 'disponible'),
(927, 1, NULL, '2025-10-25 09:00:00', '2025-10-25 09:30:00', 0, 'disponible'),
(928, 1, NULL, '2025-10-25 09:30:00', '2025-10-25 10:00:00', 0, 'disponible'),
(929, 1, NULL, '2025-10-25 10:00:00', '2025-10-25 10:30:00', 0, 'disponible'),
(930, 1, NULL, '2025-10-25 10:30:00', '2025-10-25 11:00:00', 0, 'disponible'),
(931, 1, NULL, '2025-10-25 11:00:00', '2025-10-25 11:30:00', 0, 'disponible'),
(932, 1, NULL, '2025-10-25 11:30:00', '2025-10-25 12:00:00', 0, 'disponible'),
(933, 1, 2, '2025-10-25 14:00:00', '2025-10-25 14:30:00', 1, 'disponible'),
(934, 1, NULL, '2025-10-25 14:30:00', '2025-10-25 15:00:00', 0, 'disponible'),
(935, 1, NULL, '2025-10-25 15:00:00', '2025-10-25 15:30:00', 0, 'disponible'),
(936, 1, NULL, '2025-10-25 15:30:00', '2025-10-25 16:00:00', 0, 'disponible'),
(937, 1, NULL, '2025-10-25 16:00:00', '2025-10-25 16:30:00', 0, 'disponible'),
(938, 1, NULL, '2025-10-25 16:30:00', '2025-10-25 17:00:00', 0, 'disponible'),
(939, 1, NULL, '2025-10-25 17:00:00', '2025-10-25 17:30:00', 0, 'disponible'),
(940, 1, 4, '2025-10-25 17:30:00', '2025-10-25 18:00:00', 1, 'disponible'),
(941, 1, NULL, '2025-10-25 18:00:00', '2025-10-25 18:30:00', 0, 'disponible'),
(942, 1, NULL, '2025-10-25 18:30:00', '2025-10-25 19:00:00', 0, 'disponible'),
(943, 1, NULL, '2025-10-25 19:00:00', '2025-10-25 19:30:00', 0, 'disponible'),
(944, 1, NULL, '2025-10-25 19:30:00', '2025-10-25 20:00:00', 0, 'disponible'),
(945, 1, NULL, '2025-10-27 08:00:00', '2025-10-27 08:30:00', 0, 'disponible'),
(946, 1, NULL, '2025-10-27 08:30:00', '2025-10-27 09:00:00', 0, 'disponible'),
(947, 1, NULL, '2025-10-27 09:00:00', '2025-10-27 09:30:00', 0, 'disponible'),
(948, 1, NULL, '2025-10-27 09:30:00', '2025-10-27 10:00:00', 0, 'disponible'),
(949, 1, NULL, '2025-10-27 10:00:00', '2025-10-27 10:30:00', 0, 'disponible'),
(950, 1, NULL, '2025-10-27 10:30:00', '2025-10-27 11:00:00', 0, 'disponible'),
(951, 1, NULL, '2025-10-27 11:00:00', '2025-10-27 11:30:00', 0, 'disponible'),
(952, 1, 4, '2025-10-27 11:30:00', '2025-10-27 12:00:00', 1, 'disponible'),
(953, 1, NULL, '2025-10-27 14:00:00', '2025-10-27 14:30:00', 0, 'disponible'),
(954, 1, NULL, '2025-10-27 14:30:00', '2025-10-27 15:00:00', 0, 'disponible'),
(955, 1, NULL, '2025-10-27 15:00:00', '2025-10-27 15:30:00', 0, 'disponible'),
(956, 1, NULL, '2025-10-27 15:30:00', '2025-10-27 16:00:00', 0, 'disponible'),
(957, 1, 2, '2025-10-27 16:00:00', '2025-10-27 16:30:00', 1, 'disponible'),
(958, 1, NULL, '2025-10-27 16:30:00', '2025-10-27 17:00:00', 0, 'disponible'),
(959, 1, NULL, '2025-10-27 17:00:00', '2025-10-27 17:30:00', 0, 'disponible'),
(960, 1, NULL, '2025-10-27 17:30:00', '2025-10-27 18:00:00', 0, 'disponible'),
(961, 1, NULL, '2025-10-27 18:00:00', '2025-10-27 18:30:00', 0, 'disponible'),
(962, 1, NULL, '2025-10-27 18:30:00', '2025-10-27 19:00:00', 0, 'disponible'),
(963, 1, NULL, '2025-10-27 19:00:00', '2025-10-27 19:30:00', 0, 'disponible'),
(964, 1, NULL, '2025-10-27 19:30:00', '2025-10-27 20:00:00', 0, 'disponible'),
(965, 1, NULL, '2025-10-28 08:00:00', '2025-10-28 08:30:00', 0, 'disponible'),
(966, 1, NULL, '2025-10-28 08:30:00', '2025-10-28 09:00:00', 0, 'disponible'),
(967, 1, NULL, '2025-10-28 09:00:00', '2025-10-28 09:30:00', 0, 'disponible'),
(968, 1, NULL, '2025-10-28 09:30:00', '2025-10-28 10:00:00', 0, 'disponible'),
(969, 1, NULL, '2025-10-28 10:00:00', '2025-10-28 10:30:00', 0, 'disponible'),
(970, 1, 1, '2025-10-28 10:30:00', '2025-10-28 11:00:00', 1, 'disponible'),
(971, 1, NULL, '2025-10-28 11:00:00', '2025-10-28 11:30:00', 0, 'disponible'),
(972, 1, NULL, '2025-10-28 11:30:00', '2025-10-28 12:00:00', 0, 'disponible'),
(973, 1, NULL, '2025-10-28 14:00:00', '2025-10-28 14:30:00', 0, 'disponible'),
(974, 1, NULL, '2025-10-28 14:30:00', '2025-10-28 15:00:00', 0, 'disponible'),
(975, 1, NULL, '2025-10-28 15:00:00', '2025-10-28 15:30:00', 0, 'disponible'),
(976, 1, 1, '2025-10-28 15:30:00', '2025-10-28 16:00:00', 1, 'disponible'),
(977, 1, NULL, '2025-10-28 16:00:00', '2025-10-28 16:30:00', 0, 'disponible'),
(978, 1, NULL, '2025-10-28 16:30:00', '2025-10-28 17:00:00', 0, 'disponible'),
(979, 1, NULL, '2025-10-28 17:00:00', '2025-10-28 17:30:00', 0, 'disponible'),
(980, 1, NULL, '2025-10-28 17:30:00', '2025-10-28 18:00:00', 0, 'disponible'),
(981, 1, NULL, '2025-10-28 18:00:00', '2025-10-28 18:30:00', 0, 'disponible'),
(982, 1, NULL, '2025-10-28 18:30:00', '2025-10-28 19:00:00', 0, 'disponible'),
(983, 1, NULL, '2025-10-28 19:00:00', '2025-10-28 19:30:00', 0, 'disponible'),
(984, 1, NULL, '2025-10-28 19:30:00', '2025-10-28 20:00:00', 0, 'disponible'),
(985, 1, NULL, '2025-10-29 08:00:00', '2025-10-29 08:30:00', 0, 'disponible'),
(986, 1, NULL, '2025-10-29 08:30:00', '2025-10-29 09:00:00', 0, 'disponible'),
(987, 1, NULL, '2025-10-29 09:00:00', '2025-10-29 09:30:00', 0, 'disponible'),
(988, 1, NULL, '2025-10-29 09:30:00', '2025-10-29 10:00:00', 0, 'disponible'),
(989, 1, 4, '2025-10-29 10:00:00', '2025-10-29 10:30:00', 1, 'disponible'),
(990, 1, NULL, '2025-10-29 10:30:00', '2025-10-29 11:00:00', 0, 'disponible'),
(991, 1, NULL, '2025-10-29 11:00:00', '2025-10-29 11:30:00', 0, 'disponible'),
(992, 1, NULL, '2025-10-29 11:30:00', '2025-10-29 12:00:00', 0, 'disponible'),
(993, 1, NULL, '2025-10-29 14:00:00', '2025-10-29 14:30:00', 0, 'disponible'),
(994, 1, NULL, '2025-10-29 14:30:00', '2025-10-29 15:00:00', 0, 'disponible'),
(995, 1, 2, '2025-10-29 15:00:00', '2025-10-29 15:30:00', 1, 'disponible'),
(996, 1, NULL, '2025-10-29 15:30:00', '2025-10-29 16:00:00', 0, 'disponible'),
(997, 1, NULL, '2025-10-29 16:00:00', '2025-10-29 16:30:00', 0, 'disponible'),
(998, 1, NULL, '2025-10-29 16:30:00', '2025-10-29 17:00:00', 0, 'disponible'),
(999, 1, NULL, '2025-10-29 17:00:00', '2025-10-29 17:30:00', 0, 'disponible'),
(1000, 1, NULL, '2025-10-29 17:30:00', '2025-10-29 18:00:00', 0, 'disponible'),
(1001, 1, NULL, '2025-10-29 18:00:00', '2025-10-29 18:30:00', 0, 'disponible'),
(1002, 1, NULL, '2025-10-29 18:30:00', '2025-10-29 19:00:00', 0, 'disponible'),
(1003, 1, NULL, '2025-10-29 19:00:00', '2025-10-29 19:30:00', 0, 'disponible'),
(1004, 1, NULL, '2025-10-29 19:30:00', '2025-10-29 20:00:00', 0, 'disponible'),
(1005, 1, NULL, '2025-10-30 08:00:00', '2025-10-30 08:30:00', 0, 'indisponible'),
(1006, 1, NULL, '2025-10-30 08:30:00', '2025-10-30 09:00:00', 0, 'disponible'),
(1007, 1, NULL, '2025-10-30 09:00:00', '2025-10-30 09:30:00', 0, 'disponible'),
(1008, 1, NULL, '2025-10-30 09:30:00', '2025-10-30 10:00:00', 0, 'disponible'),
(1009, 1, NULL, '2025-10-30 10:00:00', '2025-10-30 10:30:00', 0, 'disponible'),
(1010, 1, NULL, '2025-10-30 10:30:00', '2025-10-30 11:00:00', 0, 'disponible'),
(1011, 1, 2, '2025-10-30 11:00:00', '2025-10-30 11:30:00', 1, 'disponible'),
(1012, 1, NULL, '2025-10-30 11:30:00', '2025-10-30 12:00:00', 0, 'disponible'),
(1013, 1, NULL, '2025-10-30 14:00:00', '2025-10-30 14:30:00', 0, 'disponible'),
(1014, 1, 1, '2025-10-30 14:30:00', '2025-10-30 15:00:00', 1, 'disponible'),
(1015, 1, NULL, '2025-10-30 15:00:00', '2025-10-30 15:30:00', 0, 'disponible'),
(1016, 1, NULL, '2025-10-30 15:30:00', '2025-10-30 16:00:00', 0, 'disponible'),
(1017, 1, NULL, '2025-10-30 16:00:00', '2025-10-30 16:30:00', 0, 'disponible'),
(1018, 1, NULL, '2025-10-30 16:30:00', '2025-10-30 17:00:00', 0, 'disponible'),
(1019, 1, NULL, '2025-10-30 17:00:00', '2025-10-30 17:30:00', 0, 'disponible'),
(1020, 1, NULL, '2025-10-30 17:30:00', '2025-10-30 18:00:00', 0, 'disponible'),
(1021, 1, NULL, '2025-10-30 18:00:00', '2025-10-30 18:30:00', 0, 'disponible'),
(1022, 1, NULL, '2025-10-30 18:30:00', '2025-10-30 19:00:00', 0, 'disponible'),
(1023, 1, NULL, '2025-10-30 19:00:00', '2025-10-30 19:30:00', 0, 'disponible'),
(1024, 1, NULL, '2025-10-30 19:30:00', '2025-10-30 20:00:00', 0, 'disponible'),
(1065, 1, NULL, '2025-10-20 08:00:00', '2025-10-20 08:30:00', 0, 'disponible'),
(1066, 1, NULL, '2025-10-20 08:30:00', '2025-10-20 09:00:00', 0, 'disponible'),
(1067, 1, NULL, '2025-10-20 09:00:00', '2025-10-20 09:30:00', 0, 'disponible'),
(1068, 1, 1, '2025-10-20 09:30:00', '2025-10-20 10:00:00', 1, 'disponible'),
(1069, 1, 8, '2025-10-20 10:00:00', '2025-10-20 10:30:00', 1, 'disponible'),
(1070, 1, NULL, '2025-10-20 10:30:00', '2025-10-20 11:00:00', 0, 'disponible'),
(1071, 1, 8, '2025-10-20 11:00:00', '2025-10-20 11:30:00', 1, 'disponible'),
(1072, 1, NULL, '2025-10-20 11:30:00', '2025-10-20 12:00:00', 0, 'disponible'),
(1073, 1, 8, '2025-10-20 14:00:00', '2025-10-20 14:30:00', 1, 'disponible'),
(1074, 1, NULL, '2025-10-20 14:30:00', '2025-10-20 15:00:00', 0, 'disponible'),
(1075, 1, 2, '2025-10-20 15:00:00', '2025-10-20 15:30:00', 1, 'disponible'),
(1076, 1, NULL, '2025-10-20 15:30:00', '2025-10-20 16:00:00', 0, 'disponible'),
(1077, 1, NULL, '2025-10-20 16:00:00', '2025-10-20 16:30:00', 0, 'disponible'),
(1078, 1, NULL, '2025-10-20 16:30:00', '2025-10-20 17:00:00', 0, 'disponible'),
(1079, 1, 4, '2025-10-20 17:00:00', '2025-10-20 17:30:00', 1, 'disponible'),
(1080, 1, NULL, '2025-10-20 17:30:00', '2025-10-20 18:00:00', 0, 'disponible'),
(1081, 1, NULL, '2025-10-20 18:00:00', '2025-10-20 18:30:00', 0, 'disponible'),
(1082, 1, NULL, '2025-10-20 18:30:00', '2025-10-20 19:00:00', 0, 'disponible'),
(1083, 1, NULL, '2025-10-20 19:00:00', '2025-10-20 19:30:00', 0, 'disponible'),
(1084, 1, NULL, '2025-10-20 19:30:00', '2025-10-20 20:00:00', 0, 'disponible'),
(1085, 1, NULL, '2025-10-31 08:00:00', '2025-10-31 08:30:00', 0, 'indisponible'),
(1086, 1, NULL, '2025-10-31 08:30:00', '2025-10-31 09:00:00', 0, 'indisponible'),
(1087, 1, NULL, '2025-10-31 09:00:00', '2025-10-31 09:30:00', 0, 'disponible'),
(1088, 1, NULL, '2025-10-31 09:30:00', '2025-10-31 10:00:00', 0, 'disponible'),
(1089, 1, NULL, '2025-10-31 10:00:00', '2025-10-31 10:30:00', 0, 'disponible'),
(1090, 1, NULL, '2025-10-31 10:30:00', '2025-10-31 11:00:00', 0, 'disponible'),
(1091, 1, NULL, '2025-10-31 11:00:00', '2025-10-31 11:30:00', 0, 'disponible'),
(1092, 1, NULL, '2025-10-31 11:30:00', '2025-10-31 12:00:00', 0, 'disponible'),
(1093, 1, NULL, '2025-10-31 14:00:00', '2025-10-31 14:30:00', 0, 'disponible'),
(1094, 1, NULL, '2025-10-31 14:30:00', '2025-10-31 15:00:00', 0, 'disponible'),
(1095, 1, NULL, '2025-10-31 15:00:00', '2025-10-31 15:30:00', 0, 'disponible'),
(1096, 1, NULL, '2025-10-31 15:30:00', '2025-10-31 16:00:00', 0, 'disponible'),
(1097, 1, NULL, '2025-10-31 16:00:00', '2025-10-31 16:30:00', 0, 'disponible'),
(1098, 1, NULL, '2025-10-31 16:30:00', '2025-10-31 17:00:00', 0, 'disponible'),
(1099, 1, NULL, '2025-10-31 17:00:00', '2025-10-31 17:30:00', 0, 'disponible'),
(1100, 1, 8, '2025-10-31 17:30:00', '2025-10-31 18:00:00', 1, 'disponible'),
(1101, 1, NULL, '2025-10-31 18:00:00', '2025-10-31 18:30:00', 0, 'disponible'),
(1102, 1, 4, '2025-10-31 18:30:00', '2025-10-31 19:00:00', 1, 'disponible'),
(1103, 1, NULL, '2025-10-31 19:00:00', '2025-10-31 19:30:00', 0, 'disponible'),
(1104, 1, NULL, '2025-10-31 19:30:00', '2025-10-31 20:00:00', 0, 'disponible');

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
-- Structure de la table `rendezvous`
--

CREATE TABLE `rendezvous` (
  `id` int(10) UNSIGNED NOT NULL,
  `creneau_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `medecin_id` int(10) UNSIGNED NOT NULL,
  `secretaire_id` int(10) UNSIGNED DEFAULT NULL,
  `statut` enum('DEMANDE','CONFIRME','ANNULE','HONORE','ABSENT') NOT NULL DEFAULT 'DEMANDE',
  `duree` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rendezvous`
--

INSERT INTO `rendezvous` (`id`, `creneau_id`, `patient_id`, `medecin_id`, `secretaire_id`, `statut`, `duree`, `commentaire`) VALUES
(69, 1083, 6, 3, NULL, 'ANNULE', 30, NULL),
(70, 807, 5, 3, NULL, 'HONORE', 30, NULL),
(71, 933, 5, 3, NULL, 'CONFIRME', 30, NULL),
(72, 1079, 6, 3, NULL, 'CONFIRME', 30, NULL),
(73, 976, 6, 3, NULL, 'CONFIRME', 30, NULL),
(74, 957, 7, 3, NULL, 'CONFIRME', 30, NULL),
(75, 1068, 8, 3, NULL, 'CONFIRME', 30, NULL),
(76, 989, 8, 3, NULL, 'CONFIRME', 30, NULL),
(77, 874, 9, 3, NULL, 'CONFIRME', 120, NULL),
(78, 1011, 9, 3, NULL, 'ABSENT', 30, NULL),
(79, 811, 10, 3, NULL, 'ABSENT', 30, NULL),
(80, 918, 10, 3, NULL, 'CONFIRME', 30, NULL),
(81, 855, 11, 3, NULL, 'CONFIRME', 90, NULL),
(82, 885, 12, 3, NULL, 'CONFIRME', 60, NULL),
(83, 1014, 12, 3, NULL, 'HONORE', 30, NULL),
(84, 952, 13, 3, NULL, 'CONFIRME', 30, NULL),
(85, 1075, 14, 3, NULL, 'CONFIRME', 30, NULL),
(86, 970, 14, 3, NULL, 'CONFIRME', 30, NULL),
(87, 878, 15, 3, NULL, 'CONFIRME', 30, NULL),
(88, 995, 15, 3, NULL, 'CONFIRME', 30, NULL),
(89, 897, 16, 3, NULL, 'CONFIRME', 30, NULL),
(90, 926, 16, 3, NULL, 'CONFIRME', 60, NULL),
(91, 1071, 6, 3, NULL, 'CONFIRME', 60, NULL),
(92, 1102, 6, 3, NULL, 'HONORE', 30, NULL),
(93, 940, 6, 3, NULL, 'CONFIRME', 30, NULL),
(94, 1100, 6, 3, NULL, 'HONORE', 60, NULL),
(95, 914, 6, 3, NULL, 'CONFIRME', 120, NULL),
(96, 1073, 6, 3, NULL, 'CONFIRME', 60, NULL),
(97, 1069, 6, 3, NULL, 'CONFIRME', 60, NULL),
(98, 1081, 6, 3, NULL, 'ANNULE', 30, NULL);

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
(1, 'Consultation générale', 'Examen complet, diagnostic et plan de traitement personnalisé.', 'PUBLIE', '68e6b348707a8_femme-patiente-chez-dentiste.jpg', 2, 30, '#4caf50'),
(2, 'Détartrage', 'Nettoyage professionnel des dents pour éliminer la plaque et le tartre.', 'PUBLIE', '68e7d49939d82_19475 (1).jpg', 1, 30, '#2196f3'),
(3, 'Implantologie', 'Pose d’implants pour remplacer les dents manquantes.', 'PUBLIE', '68e7d3d2412bf_5510224.jpg', 4, 90, '#9c27b0'),
(4, 'Orthodontie', 'Alignement des dents : appareils fixes ou aligneurs transparents.', 'PUBLIE', '68e7d541958fd_17722.jpg', 0, 30, '#00c9cc'),
(7, 'Parodontologie', 'Soins des gencives et traitement des maladies parodontales.', 'PUBLIE', '68ee48ae76743_5536487.jpg', 3, 120, '#a35f00'),
(8, 'Urgences dentaires', 'Prise en charge rapide des douleurs et traumatismes dentaires.', 'PUBLIE', '68ee4d73cf020_ChatGPT Image 14 oct. 2025, 15_13_39.png', 5, 60, '#a9be09');

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
(16, 'PATIENT', 'Faure', 'Antoine', 'antoine.faure@email.com', '+33-6-11-12-13-14', NULL, '$2y$10$mo8f4e7HCiOxYerUVK1WB.8zvi8rT3JvBrEFofyLRwfVZcOd8nDYi', '2025-10-10 22:27:14', '1983-05-08'),
(17, 'PATIENT', 'WALK', 'James', 'walkjames@orange.fr', '6-66-66-66-66', NULL, '$2y$10$8.n08wayraozB/eITblyGO/pm15yTsJ0rNlrp25mktkSrKpq9e.Ma', '2025-10-19 18:10:28', '1995-10-19'),
(18, 'PATIENT', 'BECHE', 'Jules', 'bechejules@orange.fr', '+33-7-78-88-88-78', NULL, '$2y$10$aG0M4EXt7Az/4Irk64jdA.MNQ2kZFkHnA48Pip7GaDOOBtDeOia6K', '2025-10-19 18:13:18', '1990-10-19'),
(19, 'PATIENT', 'SAINT-LAURENT', 'Yves', 'ysl@gmail.com', '6-55-55-55-55', NULL, '$2y$10$Y5JEt7kBxE/Dst78jWNyNuepSqalNMpIhLqGH0i8qPQ2gy24chjna', '2025-10-19 18:39:28', '1991-11-11');

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
-- Index pour la table `rendezvous`
--
ALTER TABLE `rendezvous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rdv_patient` (`patient_id`),
  ADD KEY `idx_rdv_medecin` (`medecin_id`),
  ADD KEY `idx_rdv_secretaire` (`secretaire_id`),
  ADD KEY `idx_rdv_statut` (`statut`),
  ADD KEY `idx_creneau` (`creneau_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1105;

--
-- AUTO_INCREMENT pour la table `horaire_cabinet`
--
ALTER TABLE `horaire_cabinet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT pour la table `rendezvous`
--
ALTER TABLE `rendezvous`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
