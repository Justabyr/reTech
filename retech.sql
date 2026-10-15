-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 15 oct. 2026 à 12:33
-- Version du serveur : 8.0.31
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `retech`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_cat` int NOT NULL AUTO_INCREMENT,
  `nom_cat` varchar(50) NOT NULL,
  PRIMARY KEY (`id_cat`),
  UNIQUE KEY `nom_cat` (`nom_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_cat`, `nom_cat`) VALUES
(1, 'PC Portables'),
(2, 'Smartphones');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_com` int NOT NULL AUTO_INCREMENT,
  `date_com` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_com` decimal(10,2) UNSIGNED NOT NULL,
  `id_util` int NOT NULL,
  `statut_com` enum('en cours de préparation','en cours de livraison','livrée') NOT NULL DEFAULT 'en cours de préparation',
  `adresse_livraison_com` text NOT NULL,
  PRIMARY KEY (`id_com`),
  KEY `id_util` (`id_util`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commande`
--

DROP TABLE IF EXISTS `ligne_commande`;
CREATE TABLE IF NOT EXISTS `ligne_commande` (
  `id_com` int NOT NULL,
  `id_prod` int NOT NULL,
  `prix_unitaire` decimal(6,2) UNSIGNED NOT NULL,
  `quantite` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_com`,`id_prod`),
  KEY `id_prod` (`id_prod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_prod` int NOT NULL AUTO_INCREMENT,
  `nom_prod` varchar(50) NOT NULL,
  `description_prod` text NOT NULL,
  `prix_prod` decimal(6,2) UNSIGNED NOT NULL,
  `stock_prod` int UNSIGNED NOT NULL,
  `etat_prod` enum('Bon état','Très bon état','Correct') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `image` varchar(255) NOT NULL,
  `id_cat` int NOT NULL,
  PRIMARY KEY (`id_prod`),
  KEY `id_cat` (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_prod`, `nom_prod`, `description_prod`, `prix_prod`, `stock_prod`, `etat_prod`, `image`, `id_cat`) VALUES
(1, 'iPhone 13 128 Go', 'Smartphone Apple reconditionné, écran 6,1 pouces, batterie testée à plus de 85 %. Livré avec câble de charge.', 449.99, 5, 'Très bon état', 'images/iphone13.jpg', 2),
(2, 'Samsung Galaxy S21', 'Smartphone Samsung reconditionné, 128 Go, triple capteur photo. Quelques micro-rayures invisibles à l\'usage', 319.00, 3, 'Correct', 'images/galaxys21.jpg', 2),
(3, 'MacBook Air M1', 'Ordinateur portable Apple reconditionné, 8 Go de RAM, SSD 256 Go. Clavier AZERTY.', 749.00, 0, 'Bon état', 'images/macbook-air-m1.jpg', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_util` int NOT NULL AUTO_INCREMENT,
  `nom_util` varchar(50) NOT NULL,
  `prenom_util` varchar(50) NOT NULL,
  `email_util` varchar(250) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('Client','Admin') NOT NULL DEFAULT 'Client',
  PRIMARY KEY (`id_util`),
  UNIQUE KEY `email_util` (`email_util`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_util`, `nom_util`, `prenom_util`, `email_util`, `mdp`, `role`) VALUES
(1, 'BELAOUISSI', 'Abyr', 'bel.abyr@gmail.com', '$2y$10$fhkK5SAn/1OMgK46w9i/.exOn1Xr0EgNdTj8EnpO9EluX/Z5lv922', 'Admin');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_util`) REFERENCES `utilisateur` (`id_util`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
  ADD CONSTRAINT `ligne_commande_ibfk_1` FOREIGN KEY (`id_com`) REFERENCES `commande` (`id_com`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_commande_ibfk_2` FOREIGN KEY (`id_prod`) REFERENCES `produit` (`id_prod`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_cat`) REFERENCES `categorie` (`id_cat`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
