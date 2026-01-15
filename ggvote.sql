-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 15 jan. 2026 à 14:51
-- Version du serveur : 8.0.43
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ggvote`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `idadmin` int NOT NULL AUTO_INCREMENT,
  `nom_admin` varchar(50) NOT NULL,
  `login_admin` varchar(100) NOT NULL,
  `mot_de_passe` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` tinyint(1) NOT NULL,
  PRIMARY KEY (`idadmin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`idadmin`, `nom_admin`, `login_admin`, `mot_de_passe`, `email`, `contact`) VALUES
(1, 'DEGRELLE', 'Kurakush', '$2y$10$Ki0Y2Yd2f/LWEk2ZhrdRD.WRupreVnRrvMQJ.B5g2Zld1VXJvam6O', 'thomas.degrelle88@orange.fr', 1);

-- --------------------------------------------------------

--
-- Structure de la table `competition`
--

DROP TABLE IF EXISTS `competition`;
CREATE TABLE IF NOT EXISTS `competition` (
  `idcompetition` int NOT NULL AUTO_INCREMENT,
  `nom_compet` varchar(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` varchar(45) NOT NULL,
  `idadmin` int NOT NULL,
  `idjeu` int NOT NULL,
  PRIMARY KEY (`idcompetition`),
  KEY `fk_compet_admin` (`idadmin`),
  KEY `fk_compet_jeu` (`idjeu`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `competition`
--

INSERT INTO `competition` (`idcompetition`, `nom_compet`, `date_debut`, `date_fin`, `statut`, `idadmin`, `idjeu`) VALUES
(1, 'Champions', '2025-09-12', '2025-09-29', 'Terminé', 2, 1),
(2, 'VCT EMEA', '2025-01-15', '2025-08-31', 'Terminé', 2, 1),
(3, 'VCT PACIFIC', '2025-01-18', '2025-08-31', 'Terminé', 2, 1),
(4, 'Worlds', '2025-10-14', '2025-11-09', 'Terminé', 2, 2),
(5, 'MSI', '2025-06-27', '2025-07-12', 'Terminé', 2, 2),
(6, 'First Stand', '2025-03-10', '2025-03-16', 'Terminé', 2, 2),
(7, 'Rocket League Championship Series', '2025-01-03', '2025-09-14', 'Terminé', 2, 3),
(8, 'Majors', '2025-09-12', '2025-09-14', 'Terminé', 2, 3),
(9, 'Esports World Cup', '2025-08-14', '2025-08-17', 'Terminé', 2, 3),
(10, 'FNCS Major – Solo Victory Cup', '2025-09-06', '2025-09-07', 'Terminé', 2, 4),
(11, 'Elite Cup Solo (Epic - Série Elite)', '2025-12-05', '2025-12-21', 'En cours', 2, 4),
(12, 'Cash Cups Solo - Majors Seasonnelles', '2024-12-15', '2025-02-18', 'Terminé', 2, 4),
(13, 'PGL Major Copenhagen 2024', '2024-10-16', '2024-10-29', 'Terminé', 2, 5),
(14, 'BLAST.tv Austin Major 2025', '2025-06-03', '2025-06-22', 'Terminé', 2, 5),
(15, 'Perfect World Shanghai Major 2024', '2024-11-30', '2024-12-15', 'Terminé', 2, 5);

-- --------------------------------------------------------

--
-- Structure de la table `concerne`
--

DROP TABLE IF EXISTS `concerne`;
CREATE TABLE IF NOT EXISTS `concerne` (
  `idscrutin` int NOT NULL,
  `idjoueur` int NOT NULL,
  `classement` int DEFAULT NULL,
  PRIMARY KEY (`idscrutin`,`idjoueur`),
  KEY `fk_concerne_joueur` (`idjoueur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `electeur`
--

DROP TABLE IF EXISTS `electeur`;
CREATE TABLE IF NOT EXISTS `electeur` (
  `idelecteur` int NOT NULL AUTO_INCREMENT,
  `email` varchar(70) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `type` varchar(70) NOT NULL,
  `idadmin` int DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idelecteur`),
  KEY `fk_electeur_admin` (`idadmin`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `electeur`
--

INSERT INTO `electeur` (`idelecteur`, `email`, `mot_de_passe`, `type`, `idadmin`, `actif`) VALUES
(1, 'test@ggvote.fr', '$2y$10$lJpSMpfMa4HD2c.vKA7yf.tVtf3v4UWTu7vNqLqR07Djpmv7XsFOS', 'Staff', 1, 1),
(3, 'test2@ggvote.fr', '$2y$10$hnMn5z05Knw909EnL6fJWOM.afm25YdWtwbkiZwlaTB/TkNXjIQiC', 'Joueur', 1, 1),
(9, 'thomas.degrelle88@orange.fr', '$2y$10$1nWtnbVR3lUzN9oAHKPNiO6vWY4hc2cE.BGMDbEw6i4wShQMO2uw6', 'Staff', NULL, 1),
(11, 'test3@ggvote.fr', '$2y$10$gO7l9sooPYBXKVVG69a9MOS7mqXhSiECbf4lpogFXPSS5YsnVclQC', 'Public', NULL, 1),
(12, 'test4@ggvote.fr', '$2y$10$K8PJlCu89ESZ76imOdsM8.0uzsWv6gybXAIlU5MYgb8xaNVn9gpDe', 'Staff', NULL, 1),
(13, 'test5@ggvote.fr', '$2y$10$UJPuneHzngs2Iusu06xlmuagZ2brbxAEUkw6VTIBJCBsGsOpfektK', 'Staff', NULL, 1),
(14, 'test6@ggvote.fr', '$2y$10$d8SrNzYC2A9Ss3Q9xgkzPuafBnUyWaIcCZHajiha3EeNIhzAYNOBq', 'Joueur', NULL, 1),
(15, 'test7@ggvote.fr', '$2y$10$cFXOs7Ki9w/A4rWDEuobhOOzdX6xhBqHHU1wYfgdAsn3nAvpPoR4W', 'Joueur', NULL, 1),
(16, 'test8@ggvote.fr', '$2y$10$bFsUuJVcOmfWyfiIWplgg.AzPE4hWotOGTOHuTZG8bvACPnn73qLy', 'Staff', NULL, 1),
(17, 'test9@ggvote.fr', '$2y$10$YrXSWd0kDLIFWMkvla6H6ua7pLLhyFT.4KyjG4.u8JMucWaLTA2Cm', 'Staff', NULL, 1),
(18, 'test10@ggvote.fr', '$2y$10$Aqa/VoAtyNTYneHe05TUeuGTg107wjefkDlH4O6/.0pxdkaK9mLke', 'Staff', NULL, 1),
(19, 'test11@ggvote.fr', '$2y$10$pCXKlRrtyMxU5FjhsoyXJ.aJtFfCEPBxSUEyurG4v5IbTTj.CbzeS', 'Public', NULL, 1),
(20, 'test12@ggvote.fr', '$2y$10$.ePQaWI3iZ2S6ms4CN67aemtteLA48v5cxrXGeVTeE91sm7aw8Z3a', 'Public', NULL, 1),
(21, 'test13@ggvote.fr', '$2y$10$d1r.1kYv7MW5vAyKtkww5ecdMBR2EQuu6UN/acmxAYgO7KgYlW2DG', 'Public', NULL, 1),
(22, 'test14@ggvote.fr', '$2y$10$F7MyD7sOCxLZYMosI540web4SBGUgQlFeKNhHLMcdniz75SUgM/0S', 'Staff', NULL, 1),
(23, 'test15@ggvote.fr', '$2y$10$xPtUNHAyoz6Jee39X5wgwObemfnWLYInTSGzHu/T1Na3AEa1uDm1W', 'Staff', NULL, 1),
(24, 'test16@ggvote.fr', '$2y$10$i8Dfo82lE4juqpFnvQcTkOdfOsKqN3xWe5srJYeE7FXmsBlQzRgku', 'Staff', NULL, 1),
(25, 'test17@ggvote.fr', '$2y$10$Mb6WK77nTXawsjGfi1R2aOQkv2qKsKqcu5UL4fcui98JA3GNnLmde', 'Staff', NULL, 1),
(26, 'test18@ggvote.fr', '$2y$10$wcxp0sSaXQE0yle4TntjPO3kr7XqVHNuoHUkrOgzxxRpmM8ffkb0a', 'Joueur', NULL, 1),
(27, 'test19@ggvote.fr', '$2y$10$CzYfjk/MZNmH2pbPzzgY6.42bQV9VbqtEyYUTYxLVI2.aSFYSrU4G', 'Joueur', NULL, 1),
(28, 'test20@ggvote.fr', '$2y$10$MTZEqQ3UThZuqShiUfpQrelLjwKpVpW0Qx3LiCPvRqXXOghAxtDIS', 'Joueur', NULL, 1),
(29, 'test21@ggvote.fr', '$2y$10$8VJk1d85CBlDg7OUUEzvR.IwuvPd1BEWc5POKYnrJxvN3B9U0hEGq', 'Joueur', NULL, 1),
(30, 'test22@ggvote.fr', '$2y$10$Ju7ukJfxhOy2Lar/bmJuXuo4VA2zAIAaeoaTEVlVTo5L.8yUKBalS', 'Joueur', NULL, 1),
(31, 'test23@ggvote.fr', '$2y$10$QfuULgwPEiIr89W7ObwGEeqjt4BhW67RMfOIYH01CdUi9odDbmw.2', 'Public', NULL, 1),
(33, 'demo@ggvote.fr', '$2y$10$iTgYTI/MpohBbzJXY5sJa./IeC2Y5j4odjVzyrevENeoZtSR5vQhK', 'Staff', NULL, 1),
(34, 'demo2@ggvote.fr', '$2y$10$T5ShMS1APmE6FDrrQ4SoDOoZf87GhDvlKIPpVMkbm71NPl.LjmVRK', 'Staff', 1, 1),
(35, 'demoFinal@ggvote.fr', '$2y$10$9AuRjM/jJgdS25P2KPS6vu1fMChfoMhght.R8E3RrLTT6NRrXZBsO', 'Public', NULL, 1),
(36, 'thomas@ggvote.fr', '$2y$10$x5mAHQyFqRS5lxSjNQYdveWN.ahhCqu7I5OuIwbXAvwGKveC8L9DO', 'Staff', NULL, 1),
(37, 'thomas2@ggvote.fr', '$2y$10$kSTq2iHHdPjVlA0VXQTTle8NpS0zp2g6JLE1aooFw8mCExSANUx/2', 'Spectateur', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `emargement`
--

DROP TABLE IF EXISTS `emargement`;
CREATE TABLE IF NOT EXISTS `emargement` (
  `idemargement` int NOT NULL AUTO_INCREMENT,
  `idelecteur` int NOT NULL,
  `idscrutin` int NOT NULL,
  `date_vote` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idemargement`),
  UNIQUE KEY `uniq_emargement` (`idelecteur`,`idscrutin`),
  KEY `fk_emargement_scrutin` (`idscrutin`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `emargement`
--

INSERT INTO `emargement` (`idemargement`, `idelecteur`, `idscrutin`, `date_vote`) VALUES
(1, 1, 6, '2026-01-14 15:02:03'),
(2, 3, 6, '2026-01-14 15:03:57'),
(3, 11, 7, '2026-01-15 14:28:02'),
(4, 12, 7, '2026-01-15 14:29:26'),
(5, 13, 7, '2026-01-15 14:30:14'),
(6, 14, 7, '2026-01-15 14:31:26'),
(7, 15, 7, '2026-01-15 14:32:07'),
(8, 16, 7, '2026-01-15 14:32:51'),
(9, 17, 7, '2026-01-15 14:33:39'),
(10, 18, 7, '2026-01-15 14:34:13'),
(11, 19, 7, '2026-01-15 14:35:08'),
(12, 20, 7, '2026-01-15 14:35:43'),
(13, 21, 7, '2026-01-15 14:36:38'),
(14, 22, 7, '2026-01-15 14:37:11'),
(15, 23, 7, '2026-01-15 14:37:39'),
(16, 24, 7, '2026-01-15 14:38:15'),
(17, 25, 7, '2026-01-15 14:38:52'),
(18, 26, 7, '2026-01-15 14:39:41'),
(19, 27, 7, '2026-01-15 14:40:04'),
(20, 28, 7, '2026-01-15 14:40:31'),
(21, 29, 7, '2026-01-15 14:41:04'),
(22, 30, 7, '2026-01-15 14:41:43'),
(23, 31, 7, '2026-01-15 14:42:09'),
(24, 33, 7, '2026-01-15 14:47:26'),
(25, 35, 7, '2026-01-15 15:13:48'),
(26, 36, 7, '2026-01-15 15:23:21');

-- --------------------------------------------------------

--
-- Structure de la table `jeu`
--

DROP TABLE IF EXISTS `jeu`;
CREATE TABLE IF NOT EXISTS `jeu` (
  `idjeu` int NOT NULL AUTO_INCREMENT,
  `nom_jeu` varchar(100) NOT NULL,
  `description_jeu` mediumtext,
  `date_ajout` date DEFAULT NULL,
  `idadmin` int NOT NULL,
  PRIMARY KEY (`idjeu`),
  KEY `fk_jeu_admin` (`idadmin`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `jeu`
--

INSERT INTO `jeu` (`idjeu`, `nom_jeu`, `description_jeu`, `date_ajout`, `idadmin`) VALUES
(1, 'Valorant', 'FPS développé par Riot Games', '2025-10-10', 2),
(2, 'League of Legends', 'MOBA développé par Riot Games', '2025-10-10', 2),
(3, 'Rocket League', 'Deux équipes, composées de un à quatre joueurs conduisant des véhicules, s\'affrontent au cours d\'un match de cinq minutes afin de frapper un ballon et de marquer dans le but adverse. Jeu développé par Epic Games', '2025-10-10', 2),
(4, 'Fortnite', 'Fortnite est un Battle Royale en ligne développé par Epic Games sous la forme de différents modes de jeu qui partagent le même gameplay général et le même moteur de jeu.', '2025-10-10', 2),
(5, 'CSGO', 'Counter-Strike: Global Offensive (abrégé CS:GO) est un jeu de tir à la première personne multijoueur en ligne de jeu d\'équipe, développé par Valve.', '2025-10-10', 2);

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

DROP TABLE IF EXISTS `joueur`;
CREATE TABLE IF NOT EXISTS `joueur` (
  `idjoueur` int NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(45) NOT NULL,
  `equipe` varchar(70) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `nationalite` varchar(45) DEFAULT NULL,
  `poste` varchar(45) DEFAULT NULL,
  `idadmin` int NOT NULL,
  `idcompetition` int NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email_candidat` varchar(255) DEFAULT NULL,
  `mdp_candidat` varchar(255) DEFAULT NULL,
  `bio_candidat` text,
  `lien_media` varchar(255) DEFAULT NULL,
  `candidature_complete` tinyint(1) NOT NULL DEFAULT '1',
  `candidature_validee` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idjoueur`),
  KEY `fk_joueur_admin` (`idadmin`),
  KEY `fk_joueur_compet` (`idcompetition`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `joueur`
--

INSERT INTO `joueur` (`idjoueur`, `pseudo`, `equipe`, `age`, `nationalite`, `poste`, `idadmin`, `idcompetition`, `photo`, `email_candidat`, `mdp_candidat`, `bio_candidat`, `lien_media`, `candidature_complete`, `candidature_validee`) VALUES
(1, 'Aspas', 'MIBR', 22, 'Brésilien', '', 1, 1, 'aspas.webp', 'aspas@orange.fr', '$2y$10$uwmbpTyj3i0xSuMXAshZxeWjYN5rdcnOIxvFG47.OKoHkYApHcp2W', 'Salut, je suis plus brésilien mais norvégien.', NULL, 1, 1),
(4, 'Brawk', 'NRG', 25, 'Américain', '', 2, 1, 'brawk.webp', NULL, NULL, NULL, NULL, 1, 1),
(5, 'Kaajak', 'FNATIC', 21, 'Polonais', '', 2, 1, 'kaajak.jpg', NULL, NULL, NULL, NULL, 1, 1),
(6, 'Cortezia', 'MIBR', 20, 'Brésilien', '', 2, 1, 'cortezia.jpg', NULL, NULL, NULL, NULL, 1, 1),
(7, 'NoMan', 'XLG', 21, 'Hong Kong', '', 2, 1, 'noman.jpg', NULL, NULL, NULL, NULL, 1, 1),
(8, 'RieNs', 'Team Heretics', 20, 'Turque', '', 2, 1, 'riens.jpg', NULL, NULL, NULL, NULL, 1, 1),
(9, 'HYUNMIN', 'DRX', 19, 'Coréen', '', 2, 1, 'alfager.jpg', NULL, NULL, NULL, NULL, 1, 1),
(10, 'Jawgemo', 'G2', 26, 'Américain', '', 2, 1, 'jawgemo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(11, 'Derke', 'VITALITY', 22, 'Russe', '', 2, 2, 'derke.jpg', NULL, NULL, NULL, NULL, 1, 1),
(12, 'Miniboo', 'Team Heretics', 20, 'Lituanien', '', 2, 2, 'miniboo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(13, 'Koalanoob', 'NAVI', 22, 'Canadien', '', 2, 2, 'koalanoob.jpg', NULL, NULL, NULL, NULL, 1, 1),
(14, 'Xeus', 'FUT ESPORTS', 21, 'Turque', '', 2, 2, 'xeus.jpg', NULL, NULL, NULL, NULL, 1, 1),
(15, 'Kaajak', 'FNATIC', 21, 'Polonais', '', 2, 2, 'kaajak.jpg', NULL, NULL, NULL, NULL, 1, 1),
(16, 'RieNs', 'Team Heretics', 20, 'Turque', '', 2, 2, 'riens.jpg', NULL, NULL, NULL, NULL, 1, 1),
(17, 'Alfajer', 'FNATIC', 20, 'Turque', '', 2, 2, 'alfager.jpg', NULL, NULL, NULL, NULL, 1, 1),
(18, 'Boo', 'Team Heretics', 28, 'Lituanien', '', 2, 2, 'boo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(19, 'Meiy', 'DFM', 22, 'Japonais', '', 2, 3, 'meiy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(20, 'hyunmin', 'DRX', 19, 'Sud Coréen', '', 2, 3, '', NULL, NULL, NULL, NULL, 1, 1),
(21, 'jemkin', 'RRQ', 21, 'Russe', '', 2, 3, 'jemkin.jpg', NULL, NULL, NULL, NULL, 1, 1),
(22, 'karon', 'GEN.G', 23, 'Sud Coréen', '', 2, 3, 'karon.jpg', NULL, NULL, NULL, NULL, 1, 1),
(23, 'something', 'PAPER REX', 23, 'Russe', '', 2, 3, 'something.jpg', NULL, NULL, NULL, NULL, 1, 1),
(24, 'D4V41', 'PAPER REX', 27, 'Malaisien', '', 2, 3, 'd4v41.jpg', NULL, NULL, NULL, NULL, 1, 1),
(25, 'Meteor', 'T1', 25, 'Sud Coréen', '', 2, 3, 'meteor.jpg', NULL, NULL, NULL, NULL, 1, 1),
(26, 'mako', 'DRX', 23, 'Sud Coréen', '', 2, 3, 'mako.jpg', NULL, NULL, NULL, NULL, 1, 1),
(27, 'chovy', 'GEN.G', 24, 'Sud Coréen', 'Mid-laner', 2, 4, 'chovy.webp', NULL, NULL, NULL, NULL, 1, 1),
(28, 'ruler', 'GEN.G', 26, 'Sud Coréen', 'ADC', 2, 4, 'ruler.png', NULL, NULL, NULL, NULL, 1, 1),
(29, 'kiin', 'GEN.G', 26, 'Sud Coréen', 'Top-laner', 2, 4, 'kiin.jpg', NULL, NULL, NULL, NULL, 1, 1),
(30, 'faker', 'T1', 29, 'Sud Coréen', 'Mid-laner', 2, 4, 'faker.webp', NULL, NULL, NULL, NULL, 1, 1),
(31, 'gumayusi', 'T1', 23, 'Sud Coréen', 'ADC', 2, 4, 'gumayusi.webp', NULL, NULL, NULL, NULL, 1, 1),
(32, 'knight', 'BLG', 25, 'Chinois', 'Mid-laner', 2, 4, 'knight.jpg', NULL, NULL, NULL, NULL, 1, 1),
(33, 'BDD', 'KT Rolster', 26, 'Sud Coréen', 'Mid-laner', 2, 4, 'bdd.jpg', NULL, NULL, NULL, NULL, 1, 1),
(34, 'cuzz', 'KT Rolster', 26, 'Sud Coréen', 'Jungler', 2, 4, 'cuzz.jpg', NULL, NULL, NULL, NULL, 1, 1),
(35, 'gumayusi', 'T1', 23, 'Sud Coréen', 'ADC', 2, 5, 'gumayusi.webp', NULL, NULL, NULL, NULL, 1, 1),
(36, 'doggo', 'CFO', 22, 'Taiwanais', 'ADC', 2, 5, 'doggo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(37, 'ruler', 'GEN.G', 26, 'Sud Coréen', 'ADC', 2, 5, 'ruler.png', NULL, NULL, NULL, NULL, 1, 1),
(38, 'knight', 'BLG', 25, 'Chinois', 'Mid-laner', 2, 5, 'knight.jpg', NULL, NULL, NULL, NULL, 1, 1),
(39, 'supa', 'MKOI', 25, 'Espagnol', 'ADC', 2, 5, 'supa.jpg', NULL, NULL, NULL, NULL, 1, 1),
(40, 'keria', 'T1', 23, 'Sud Coréen', 'Support', 2, 5, 'keria.jpg', NULL, NULL, NULL, NULL, 1, 1),
(41, 'elk', 'BLG', 24, 'Chinois', 'ADC', 2, 5, 'elk.jpg', NULL, NULL, NULL, NULL, 1, 1),
(42, 'hope', 'AL', 25, 'Chinois', 'ADC', 2, 5, 'hope.jpg', NULL, NULL, NULL, NULL, 1, 1),
(43, 'zeka', 'HLE', 23, 'Sud Coréen', 'Mid-laner', 1, 6, 'zeka.jpg', NULL, NULL, NULL, NULL, 1, 1),
(44, 'zeus', 'HLE', 21, 'Sud Coréen', 'Top-laner', 2, 6, 'zeus.jpg', NULL, NULL, NULL, NULL, 1, 1),
(45, 'viper', 'HLE', 25, 'Sud Coréen', 'ADC', 1, 6, 'viper.jpg', NULL, NULL, NULL, NULL, 1, 1),
(46, 'delight', 'HLE', 23, 'Sud Coréen', 'ADC', 2, 6, 'delight.jpg', NULL, NULL, NULL, NULL, 1, 1),
(47, 'peanut', 'HLE', 27, 'Sud Coréen', 'Jungler', 2, 6, 'peanut.jpg', NULL, NULL, NULL, NULL, 1, 1),
(48, 'kanavi', 'TES', 25, 'Sud Coréen', 'Jungler', 2, 6, 'kanavi.jpg', NULL, NULL, NULL, NULL, 1, 1),
(49, 'canna', 'TES', 25, 'Sud Coréen', 'Top-laner', 2, 6, 'canna.jpg', NULL, NULL, NULL, NULL, 1, 1),
(50, 'jackeylove', 'TES', 25, 'Chinois', 'ADC', 2, 6, 'jackeylove.jpg', NULL, NULL, NULL, NULL, 1, 1),
(51, 'zen', 'VITALITY', 18, 'Français', '', 2, 7, 'zen.jpg', NULL, NULL, NULL, NULL, 1, 1),
(52, 'firstkiller', 'COMPLEXITY', 20, 'Américain', '', 2, 7, 'firstkiller.jpg', NULL, NULL, NULL, NULL, 1, 1),
(53, 'jknaps', 'G2', 26, 'Canadien', '', 2, 7, 'jknaps.jpg', NULL, NULL, NULL, NULL, 1, 1),
(54, 'monkey moon', 'BDS', 23, 'Français', '', 2, 7, 'monkeymoon.jpg', NULL, NULL, NULL, NULL, 1, 1),
(55, 'rise', 'OXYGEN', 21, 'Anglais', '', 2, 7, 'rise.jpg', NULL, NULL, NULL, NULL, 1, 1),
(56, 'vatira', 'KC', 19, 'Français', '', 2, 7, 'vatira.jpg', NULL, NULL, NULL, NULL, 1, 1),
(57, 'atomic', 'G2', 22, 'Américain', '', 2, 7, 'atomic.jpg', NULL, NULL, NULL, NULL, 1, 1),
(58, 'seikoo', 'BDS', 21, 'Français', '', 2, 7, 'seikoo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(59, 'zen', 'VITALITY', 18, 'Français', '', 2, 8, 'zen.jpg', NULL, NULL, NULL, NULL, 1, 1),
(60, 'Vatira', 'KC', 19, 'Français', '', 2, 8, 'vatira.jpg', NULL, NULL, NULL, NULL, 1, 1),
(61, 'rise', 'OXYGEN', 21, 'Anglais', '', 2, 8, 'rise.jpg', NULL, NULL, NULL, NULL, 1, 1),
(62, 'joyo', 'MOIST', 19, 'Anglais', '', 2, 8, 'joyo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(63, 'beastmode', 'COMPLEXITY', 20, 'Américain', '', 2, 8, 'beastmode.jpg', NULL, NULL, NULL, NULL, 1, 1),
(64, 'evoh', 'FAZE', 18, 'Américain', '', 2, 8, 'evoh.jpg', NULL, NULL, NULL, NULL, 1, 1),
(65, 'monkey moon', 'BDS', 23, 'Français', '', 2, 8, 'monkeymoon.jpg', NULL, NULL, NULL, NULL, 1, 1),
(66, 'kaydop', 'VITALITY', 27, 'Français', '', 2, 8, 'kaydop.jpg', NULL, NULL, NULL, NULL, 1, 1),
(67, 'zen', 'VITALITY', 18, 'Français', '', 2, 9, 'zen.jpg', NULL, NULL, NULL, NULL, 1, 1),
(68, 'alpha54', 'VITALITY', 22, 'Français', '', 2, 9, 'alpha.jpg', NULL, NULL, NULL, NULL, 1, 1),
(69, 'radosin', 'VITALITY', 21, 'Français', '', 2, 9, 'radosin.jpg', NULL, NULL, NULL, NULL, 1, 1),
(70, 'ahmad', 'FALCONS', 23, 'Saoudien', '', 2, 9, 'ahmad.jpg', NULL, NULL, NULL, NULL, 1, 1),
(71, 'trk511', 'FALCONS', 20, 'Saoudien', '', 2, 9, 'trk.jpg', NULL, NULL, NULL, NULL, 1, 1),
(72, 'oski', 'OXYGEN', 18, 'Polonais', '', 2, 9, 'oski.jpg', NULL, NULL, NULL, NULL, 1, 1),
(73, 'joyo', 'MOIST', 19, 'Anglais', '', 2, 9, 'joyo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(74, 'itachi', 'KC', 22, 'Marocain', '', 2, 9, 'itachi.jpg', NULL, NULL, NULL, NULL, 1, 1),
(75, 'pinq', 'HERETICS', 21, 'Anglais', '', 2, 10, 'pink.jpg', NULL, NULL, NULL, NULL, 1, 1),
(76, 'setty', 'BL', 22, 'Polonais', '', 2, 10, 'setty.jpg', NULL, NULL, NULL, NULL, 1, 1),
(77, 'malibuca', 'FALCONS', 20, 'Russe', '', 2, 10, 'malibuca.jpg', NULL, NULL, NULL, NULL, 1, 1),
(78, 'thomashd', 'ASTRALIS', 23, 'Danois', '', 2, 10, 'thomas.jpg', NULL, NULL, NULL, NULL, 1, 1),
(79, 'merstach', 'FALCONS', 18, 'Letton', '', 2, 10, 'merstach.jpg', NULL, NULL, NULL, NULL, 1, 1),
(80, 'queasy', 'GENTLEMATES', 23, 'Serbe', '', 2, 10, 'queasy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(81, 'alex', 'HEROIC', 17, 'Russe', '', 2, 10, 'alex.jpg', NULL, NULL, NULL, NULL, 1, 1),
(82, 'kami', 'AIQ', 20, 'Polonais', '', 2, 10, 'kami.jpg', NULL, NULL, NULL, NULL, 1, 1),
(83, 'malibuca', 'FALCONS', 20, 'Russe', '', 2, 11, 'malibuca.jpg', NULL, NULL, NULL, NULL, 1, 1),
(84, 'merstach', 'FALCONS', 18, 'Letton', '', 2, 11, 'merstach.jpg', NULL, NULL, NULL, NULL, 1, 1),
(85, 'setty', 'BL', 22, 'Polonais', '', 2, 11, 'setty.jpg', NULL, NULL, NULL, NULL, 1, 1),
(86, 'queasy', 'GENTLEMATES', 23, 'Serbe', '', 2, 11, 'queasy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(87, 'kami', 'AIQ', 20, 'Polonais', '', 2, 11, 'kami.jpg', NULL, NULL, NULL, NULL, 1, 1),
(88, 'pinq', 'HERETICS', 21, 'Anglais', '', 2, 11, 'pink.jpg', NULL, NULL, NULL, NULL, 1, 1),
(89, 'thomashd', 'ASTRALIS', 23, 'Danois', '', 2, 11, 'thomas.jpg', NULL, NULL, NULL, NULL, 1, 1),
(90, 'veno', 'XSET', 21, 'Anglais', '', 2, 11, 'veno.jpg', NULL, NULL, NULL, NULL, 1, 1),
(91, 'malibuca', 'FALCONS', 20, 'Russe', '', 2, 12, 'malibuca.jpg', NULL, NULL, NULL, NULL, 1, 1),
(92, 'merstach', 'FALCONS', 18, 'Lituanien', '', 2, 12, 'merstach.jpg', NULL, NULL, NULL, NULL, 1, 1),
(93, 'setty', 'BL', 22, 'Polonais', '', 2, 12, 'setty.jpg', NULL, NULL, NULL, NULL, 1, 1),
(94, 'queasy', 'GENTLEMATES', 23, 'Serbe', '', 2, 12, 'queasy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(95, 'kami', 'AIQ', 20, 'Polonais', '', 2, 12, 'kami.jpg', NULL, NULL, NULL, NULL, 1, 1),
(96, 'pinq', 'HERETICS', 21, 'Anglais', '', 2, 12, 'pink.jpg', NULL, NULL, NULL, NULL, 1, 1),
(97, 'thomashd', 'ASTRALIS', 23, 'Danois', '', 2, 12, 'thomas.jpg', NULL, NULL, NULL, NULL, 1, 1),
(98, 'veno', 'XSET', 21, 'Anglais', '', 2, 12, 'veno.jpg', NULL, NULL, NULL, NULL, 1, 1),
(99, 'JL', 'NAVI', 26, 'Lituanien', '', 2, 13, 'jl.jpg', NULL, NULL, NULL, NULL, 1, 1),
(100, 'monesy', 'FALCONS', 20, 'Russe', '', 1, 13, 'monesy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(101, 'donk', 'TEAM SPIRIT', 18, 'Russe', '', 2, 13, 'donk.jpg', NULL, NULL, NULL, NULL, 1, 1),
(102, 'niko', 'G2', 28, 'Bosnien', '', 1, 13, 'niko.avif', NULL, NULL, NULL, NULL, 1, 1),
(103, 'zywoo', 'VITALITY', 25, 'Français', '', 2, 13, 'zywoo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(104, 'broky', 'FAZE CLAN', 20, 'Letton', '', 2, 13, 'broky.jpg', NULL, NULL, NULL, NULL, 1, 1),
(105, 'b1t', 'NAVI', 22, 'Ukrainien', '', 2, 13, 'bit.jpg', NULL, NULL, NULL, NULL, 1, 1),
(106, 'frozen', 'FAZE CLAN', 23, 'Slovaque', '', 2, 13, 'frozen.jpg', NULL, NULL, NULL, NULL, 1, 1),
(107, 'zywoo', 'VITALITY', 25, 'Français', '', 2, 14, 'zywoo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(108, 'donk', 'TEAM SPIRIT', 18, 'Russe', '', 2, 14, 'donk.jpg', NULL, NULL, NULL, NULL, 1, 1),
(109, 'senzu', 'THE MONGOLZ', 19, 'Mongole', '', 1, 14, 'senzu.avif', NULL, NULL, NULL, NULL, 1, 1),
(110, 'ropz', 'VITALITY', 25, 'Estonien', '', 2, 14, 'ropz.jpg', NULL, NULL, NULL, NULL, 1, 1),
(111, 'mezii', 'VITALITY', 27, 'Anglais', '', 2, 14, 'mezi.jpg', NULL, NULL, NULL, NULL, 1, 1),
(112, '910', 'THE MONGOLZ', 23, 'Mongole', '', 2, 14, '910.jpg', NULL, NULL, NULL, NULL, 1, 1),
(113, 'nqz', 'PAIN GAMING', 20, 'Brésilien', '', 2, 14, 'nqz.jpg', NULL, NULL, NULL, NULL, 1, 1),
(114, 'simple', 'FAZE CLAN', 28, 'Ukrainien', '', 2, 14, 'simple.jpg', NULL, NULL, NULL, NULL, 1, 1),
(115, 'donk', 'TEAM SPIRIT', 18, 'Russe', '', 2, 15, 'donk.jpg', NULL, NULL, NULL, NULL, 1, 1),
(116, 'zywoo', 'VITALITY', 25, 'Français', '', 2, 15, 'zywoo.jpg', NULL, NULL, NULL, NULL, 1, 1),
(117, 'niko', 'G2', 28, 'Bosnien', '', 1, 15, 'niko.avif', NULL, NULL, NULL, NULL, 1, 1),
(118, 'monesy', 'G2', 20, 'Russe', '', 2, 15, 'monesy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(119, 'icy', 'CLOUD9', 20, ' Kazakhstanais', '', 2, 15, 'icy.jpg', NULL, NULL, NULL, NULL, 1, 1),
(120, 'fame', 'VIRTUS.PRO', 22, 'Russe', '', 2, 15, 'fame.jpg', NULL, NULL, NULL, NULL, 1, 1),
(121, 'flamez', 'VITALITY', 22, 'Israélien ', '', 2, 15, 'flamez.jpg', NULL, NULL, NULL, NULL, 1, 1),
(122, 'spinx', 'VITALITY', 25, 'Israélien ', '', 2, 15, 'spinx.jpg', NULL, NULL, NULL, NULL, 1, 1),
(128, 'Caps', 'G2 Esport', 27, 'Danois', 'Mid-laner', 1, 5, 'caps.jpg', 'caps@orange.fr', '$2y$10$Jtsnu.HtCd9TgI39sk0VQ.PaEJFgpjGfyy72suMOyWeth5TymReOO', 'Salut, c\'est pour la démo !', NULL, 1, 1),
(130, 'Caliste', 'KC', 24, 'Français', 'ADC', 1, 5, 'caliste.webp', 'caliste@orange.fr', '$2y$10$ZdHnhWbkqeO86tA94AcAY.Rs2dO6B7VwTp34qrNvFtRdkmAa9Vw.K', NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `resultat`
--

DROP TABLE IF EXISTS `resultat`;
CREATE TABLE IF NOT EXISTS `resultat` (
  `idresultat` int NOT NULL AUTO_INCREMENT,
  `nb_votes` int NOT NULL,
  `date_calcul` date NOT NULL,
  `rang` int NOT NULL,
  `idadmin` int NOT NULL,
  `idscrutin` int NOT NULL,
  `idjoueur` int NOT NULL,
  PRIMARY KEY (`idresultat`),
  KEY `fk_resultat_admin` (`idadmin`),
  KEY `fk_resultat_scrutin` (`idscrutin`),
  KEY `fk_resultat_joueur` (`idjoueur`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `resultat`
--

INSERT INTO `resultat` (`idresultat`, `nb_votes`, `date_calcul`, `rang`, `idadmin`, `idscrutin`, `idjoueur`) VALUES
(161, 1, '2026-01-15', 1, 1, 1, 9),
(162, 1, '2026-01-15', 1, 1, 6, 34),
(163, 1, '2026-01-15', 2, 1, 6, 30),
(164, 18, '2026-01-15', 1, 1, 7, 35),
(165, 5, '2026-01-15', 2, 1, 7, 40),
(166, 1, '2026-01-15', 3, 1, 7, 38);

-- --------------------------------------------------------

--
-- Structure de la table `scrutin`
--

DROP TABLE IF EXISTS `scrutin`;
CREATE TABLE IF NOT EXISTS `scrutin` (
  `idscrutin` int NOT NULL AUTO_INCREMENT,
  `nom_scrutin` varchar(45) NOT NULL,
  `date_ouverture` datetime NOT NULL,
  `date_cloture` datetime NOT NULL,
  `etat_scrutin` varchar(45) NOT NULL,
  `idadmin` int NOT NULL,
  `idcompetition` int NOT NULL,
  PRIMARY KEY (`idscrutin`),
  KEY `fk_scrutin_admin` (`idadmin`),
  KEY `fk_scrutin_compet` (`idcompetition`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `scrutin`
--

INSERT INTO `scrutin` (`idscrutin`, `nom_scrutin`, `date_ouverture`, `date_cloture`, `etat_scrutin`, `idadmin`, `idcompetition`) VALUES
(1, 'MVP Champions Valorant', '2025-12-01 00:00:00', '2025-12-14 00:00:00', 'cloture', 1, 1),
(2, 'MVP First Stand LoL', '2025-12-10 00:00:00', '2025-12-31 00:00:00', 'cloture', 1, 6),
(3, 'MVP Major RL', '2025-12-11 00:00:00', '2025-12-31 00:00:00', 'cloture', 1, 8),
(4, 'MVP Major Solo Fortnite', '2025-12-11 00:00:00', '2025-12-31 00:00:00', 'cloture', 1, 10),
(5, 'MVP Major CSGO:2', '2025-12-11 00:00:00', '2025-12-31 00:00:00', 'cloture', 1, 13),
(6, 'MVP Worlds LoL', '2026-01-07 00:00:00', '2026-01-19 00:00:00', 'cloture', 1, 4),
(7, 'MVP MSI', '2026-01-15 00:00:00', '2026-01-15 23:59:00', 'cloture', 1, 5),
(10, 'MVP RLCS', '2026-01-15 14:00:00', '2026-01-23 14:00:00', 'ouvert', 1, 7);

-- --------------------------------------------------------

--
-- Structure de la table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE IF NOT EXISTS `token` (
  `idtoken` int NOT NULL AUTO_INCREMENT,
  `etat` varchar(45) NOT NULL,
  `date_generation` date NOT NULL,
  `idadmin` int DEFAULT NULL,
  `token_hash` varchar(255) NOT NULL,
  `idcompetition` int NOT NULL,
  PRIMARY KEY (`idtoken`),
  KEY `fk_token_admin` (`idadmin`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `token`
--

INSERT INTO `token` (`idtoken`, `etat`, `date_generation`, `idadmin`, `token_hash`, `idcompetition`) VALUES
(1, '1', '2025-12-10', NULL, '$2y$10$c6sMkmi80vXezDWfNl4SRej3dJ1MZONWuxtJ6yhJp/qAq2kaWe/Ga', 1),
(2, '0', '2025-12-11', NULL, '$2y$10$8/NH06v5GlPiWezVQ0e.8OKeUdc83BK2D/s6PWwEIMJTYMfI1VyYG', 1),
(3, '0', '2025-12-11', NULL, '$2y$10$183iUTjVwMQY4V8itB3MRunXUbbr7kJ3xP1GUv1tczvgK3p9qmIU.', 6),
(4, '0', '2025-12-11', NULL, '$2y$10$jDoAXAYkPtKl8au5//jzGOVyISXRXHO15tZ3o1fPpPcjJ2kbfHL2.', 8),
(5, '0', '2025-12-11', NULL, '$2y$10$CSkRlmXsOnZaqI.5dFrbLOupjkTaImIADqNcwtI77amqfJ0ziq1t2', 10),
(6, '0', '2025-12-11', NULL, '$2y$10$q9uVifITNfQnzcFr2SFwA.jNsML.XEqGevREzZcaF2WelOVZ8gWLO', 13),
(7, '0', '2025-12-12', NULL, '$2y$10$tsnhuy3vyIz7TuYhtmthYu1ca7p7dgfUB5K92ayFK6L6QzkyLwkXe', 6),
(8, '0', '2025-12-12', NULL, '$2y$10$tEY9Ojtm9wvWMrMPFyme0eiGwJLxO8s3G.NfH18lEiQgbW9oDiicu', 8),
(9, '0', '2025-12-12', NULL, '$2y$10$bRekjrBCoomrE0yKqnnmcengXo4AyJfFjrcmHoky22s27Bs.QXnK.', 10),
(10, '0', '2025-12-12', NULL, '$2y$10$oRJwDThPNLfGKT0SA6OWDOdHJGBGNdUDoPODr4MQ9RYoZzuq/lb9m', 13),
(19, '1', '2026-01-14', NULL, '$2y$10$/p86cWwsqpAU7UOEKe8iZuFExwF09qh3PUhrhJgQkrJiGLmmpNflm', 4),
(20, '1', '2026-01-14', NULL, '$2y$10$0yXRpIPVXjnmk1flB5Zy0eo8KW2HC6c16z7r4EaVSyrwsfSajlglW', 4),
(21, '1', '2026-01-15', NULL, '$2y$10$z7.4hqU5e0/GOhmdPuvc8u1pYtV8wQNf9S8dS5dXpGu8Qw6WV2oeq', 5),
(22, '1', '2026-01-15', NULL, '$2y$10$E2BmyL3n.NFkk15oLb1KQesVkHUHPXKrVHyALrz3uC9garhmKAm7m', 5),
(23, '1', '2026-01-15', NULL, '$2y$10$OTaIph3sf2bktgYNTnPxru1LdyE1XkT29rbtTTUrma9yPi7tvxHQG', 5),
(24, '1', '2026-01-15', NULL, '$2y$10$QSls.FeLxKIGftup1mYPX.LBGodWAA2.xMLD/z6jOYkbKOyNtofZO', 5),
(25, '1', '2026-01-15', NULL, '$2y$10$k0N/aeQnP1ouQVBTD9OARONvpTvIek43.n5OypJTvH2c8Su35rc/.', 5),
(26, '1', '2026-01-15', NULL, '$2y$10$04ZkrcR4Lc8tqmkT1BUkDu5GO.SLxfjgGuaqFlHkNhztPbNpQtIby', 5),
(27, '1', '2026-01-15', NULL, '$2y$10$cN4y0lHlhH/Uk4dMm.oW2uvT30/pe6GheE3sjp/yGQxwZXUYS1/wy', 5),
(28, '1', '2026-01-15', NULL, '$2y$10$8qN1vRu3PFT5ZTkhvYJR5uuMfJveonUj7436.TnT6IuSI0igcvf7O', 5),
(29, '1', '2026-01-15', NULL, '$2y$10$2PUQiVGha7AUye73NG9auuVROj9tNyEYPK./92AOk3Bb9nsifwNc.', 5),
(30, '1', '2026-01-15', NULL, '$2y$10$yCW.0vO/8fv3Kwl0czfGlOfFLTCs.QYdYqKMGa9NI6YDxOUo6EXxy', 5),
(31, '1', '2026-01-15', NULL, '$2y$10$EZhiVKE44doIyzdaqW7mieEg1xYCxLIUkN/Wo1AkAg4fGUv9KVNQG', 5),
(32, '1', '2026-01-15', NULL, '$2y$10$0SwIvsrZ9jVoRxRR6HPvyem4ruSJ9XldnOS5hiQt/c17qiPP.jEcW', 5),
(33, '1', '2026-01-15', NULL, '$2y$10$T0dYJJGxOfZ9dbj83aS24uXPKbqZbmuJl7rWTOFpdDQuXwAIXVjn2', 5),
(34, '1', '2026-01-15', NULL, '$2y$10$plEm6hmzmePGtPpXn5LEzeaBRq.GBFRhWKF9IyHE91gz7hiA4W0ji', 5),
(35, '1', '2026-01-15', NULL, '$2y$10$Ya8yJfpBiq5BLdVEDFVpsOINz7BDRpbLhSd4ZxrW9EGML43rdl4x6', 5),
(36, '1', '2026-01-15', NULL, '$2y$10$FTfyaNtccObcGzMw.vkGnevGkW418w22s.cE5Ip04k14t5/oxmZ6q', 5),
(37, '1', '2026-01-15', NULL, '$2y$10$kJdQaCF3hc/F0plpJl52iu5rzpTQy7JGLO3Cid7ggxZBTUqN2VYb6', 5),
(38, '1', '2026-01-15', NULL, '$2y$10$Hlj83ReHYm6k4NQXPQVGxuIxle24SKAf5sB6fyYCiHukp6seo/AY6', 5),
(39, '1', '2026-01-15', NULL, '$2y$10$raFrHywjG0gC52xB6S.9R.htFK09IuE52Jfv8oCkaI4c8XJiJZuXi', 5),
(40, '1', '2026-01-15', NULL, '$2y$10$4Q7YpMJkNFbLJHQcyrTRTeLItQcbIqVhFZjWbZJDnyiSEVi0TedHW', 5),
(41, '1', '2026-01-15', NULL, '$2y$10$Si23v7VV/ySCZNBJoJ1Gzub0ytMKN9e.dVALiY8y47R/dRr8iXeU2', 5),
(42, '1', '2026-01-15', NULL, '$2y$10$LGYyyKcPrsu3y7ykFpSUXed/UO1IxahUTanTqADKT8Namc4GdoyJ2', 5),
(43, '1', '2026-01-15', NULL, '$2y$10$F6rXFl6nGX58/GmgXrzoxeKBLMADgOTM3M7Xn9MrDTFEhLTq7f6K2', 5),
(44, '1', '2026-01-15', NULL, '$2y$10$ghEyya6D3RpnlySiN1tlf.JAZ2PM06CkVqX6hcNy8MC5yr8UETj0u', 5);

-- --------------------------------------------------------

--
-- Structure de la table `vote`
--

DROP TABLE IF EXISTS `vote`;
CREATE TABLE IF NOT EXISTS `vote` (
  `idvote` int NOT NULL AUTO_INCREMENT,
  `date_vote` date NOT NULL,
  `heure_vote` datetime NOT NULL,
  `idscrutin` int NOT NULL,
  `idjoueur` int NOT NULL,
  `idtoken` int NOT NULL,
  PRIMARY KEY (`idvote`),
  KEY `fk_vote_scrutin` (`idscrutin`),
  KEY `fk_vote_joueur` (`idjoueur`),
  KEY `fk_vote_token` (`idtoken`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vote`
--

INSERT INTO `vote` (`idvote`, `date_vote`, `heure_vote`, `idscrutin`, `idjoueur`, `idtoken`) VALUES
(1, '2025-12-11', '2025-12-11 11:40:15', 1, 9, 1),
(6, '2026-01-14', '2026-01-14 15:02:03', 6, 34, 19),
(7, '2026-01-14', '2026-01-14 15:03:57', 6, 30, 20),
(8, '2026-01-15', '2026-01-15 14:28:02', 7, 35, 21),
(9, '2026-01-15', '2026-01-15 14:29:26', 7, 35, 22),
(10, '2026-01-15', '2026-01-15 14:30:14', 7, 35, 23),
(11, '2026-01-15', '2026-01-15 14:31:26', 7, 40, 24),
(12, '2026-01-15', '2026-01-15 14:32:07', 7, 35, 25),
(13, '2026-01-15', '2026-01-15 14:32:51', 7, 40, 26),
(14, '2026-01-15', '2026-01-15 14:33:39', 7, 35, 27),
(15, '2026-01-15', '2026-01-15 14:34:13', 7, 35, 28),
(16, '2026-01-15', '2026-01-15 14:35:08', 7, 35, 29),
(17, '2026-01-15', '2026-01-15 14:35:43', 7, 35, 30),
(18, '2026-01-15', '2026-01-15 14:36:38', 7, 35, 31),
(19, '2026-01-15', '2026-01-15 14:37:11', 7, 40, 32),
(20, '2026-01-15', '2026-01-15 14:37:39', 7, 40, 33),
(21, '2026-01-15', '2026-01-15 14:38:15', 7, 35, 34),
(22, '2026-01-15', '2026-01-15 14:38:52', 7, 35, 35),
(23, '2026-01-15', '2026-01-15 14:39:41', 7, 35, 36),
(24, '2026-01-15', '2026-01-15 14:40:04', 7, 40, 37),
(25, '2026-01-15', '2026-01-15 14:40:31', 7, 35, 38),
(26, '2026-01-15', '2026-01-15 14:41:04', 7, 35, 39),
(27, '2026-01-15', '2026-01-15 14:41:43', 7, 35, 40),
(28, '2026-01-15', '2026-01-15 14:42:09', 7, 35, 41),
(29, '2026-01-15', '2026-01-15 14:47:26', 7, 35, 42),
(30, '2026-01-15', '2026-01-15 15:13:48', 7, 38, 43),
(31, '2026-01-15', '2026-01-15 15:23:21', 7, 35, 44);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `competition`
--
ALTER TABLE `competition`
  ADD CONSTRAINT `fk_compet_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compet_jeu` FOREIGN KEY (`idjeu`) REFERENCES `jeu` (`idjeu`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `concerne`
--
ALTER TABLE `concerne`
  ADD CONSTRAINT `fk_concerne_joueur` FOREIGN KEY (`idjoueur`) REFERENCES `joueur` (`idjoueur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_concerne_scrutin` FOREIGN KEY (`idscrutin`) REFERENCES `scrutin` (`idscrutin`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `electeur`
--
ALTER TABLE `electeur`
  ADD CONSTRAINT `fk_electeur_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `emargement`
--
ALTER TABLE `emargement`
  ADD CONSTRAINT `fk_emargement_electeur` FOREIGN KEY (`idelecteur`) REFERENCES `electeur` (`idelecteur`),
  ADD CONSTRAINT `fk_emargement_scrutin` FOREIGN KEY (`idscrutin`) REFERENCES `scrutin` (`idscrutin`);

--
-- Contraintes pour la table `jeu`
--
ALTER TABLE `jeu`
  ADD CONSTRAINT `fk_jeu_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD CONSTRAINT `fk_joueur_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_joueur_compet` FOREIGN KEY (`idcompetition`) REFERENCES `competition` (`idcompetition`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `resultat`
--
ALTER TABLE `resultat`
  ADD CONSTRAINT `fk_resultat_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resultat_joueur` FOREIGN KEY (`idjoueur`) REFERENCES `joueur` (`idjoueur`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resultat_scrutin` FOREIGN KEY (`idscrutin`) REFERENCES `scrutin` (`idscrutin`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `scrutin`
--
ALTER TABLE `scrutin`
  ADD CONSTRAINT `fk_scrutin_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_scrutin_compet` FOREIGN KEY (`idcompetition`) REFERENCES `competition` (`idcompetition`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_token_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `fk_vote_joueur` FOREIGN KEY (`idjoueur`) REFERENCES `joueur` (`idjoueur`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vote_scrutin` FOREIGN KEY (`idscrutin`) REFERENCES `scrutin` (`idscrutin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vote_token` FOREIGN KEY (`idtoken`) REFERENCES `token` (`idtoken`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
