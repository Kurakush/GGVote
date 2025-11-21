-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 21 nov. 2025 à 09:34
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
(1, 'DEGRELLE', 'Kurakush', 'password', 'thomas.degrelle88@orange.fr', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `type` varchar(70) NOT NULL,
  `idadmin` int NOT NULL,
  PRIMARY KEY (`idelecteur`),
  KEY `fk_electeur_admin` (`idadmin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`idjoueur`),
  KEY `fk_joueur_admin` (`idadmin`),
  KEY `fk_joueur_compet` (`idcompetition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scrutin`
--

DROP TABLE IF EXISTS `scrutin`;
CREATE TABLE IF NOT EXISTS `scrutin` (
  `idscrutin` int NOT NULL AUTO_INCREMENT,
  `nom_scrutin` varchar(45) NOT NULL,
  `date_ouverture` date NOT NULL,
  `date_cloture` date NOT NULL,
  `etat_scrutin` varchar(45) NOT NULL,
  `idadmin` int NOT NULL,
  `idcompetition` int NOT NULL,
  PRIMARY KEY (`idscrutin`),
  KEY `fk_scrutin_admin` (`idadmin`),
  KEY `fk_scrutin_compet` (`idcompetition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE IF NOT EXISTS `token` (
  `idtoken` int NOT NULL AUTO_INCREMENT,
  `etat` varchar(45) NOT NULL,
  `date_generation` date NOT NULL,
  `idadmin` int NOT NULL,
  `idelecteur` int NOT NULL,
  PRIMARY KEY (`idtoken`),
  KEY `fk_token_admin` (`idadmin`),
  KEY `fk_token_electeur` (`idelecteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  ADD CONSTRAINT `fk_token_admin` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_token_electeur` FOREIGN KEY (`idelecteur`) REFERENCES `electeur` (`idelecteur`) ON DELETE RESTRICT ON UPDATE CASCADE;

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
