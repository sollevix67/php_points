-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 25 jan. 2025 à 19:17
-- Version du serveur : 11.4.4-MariaDB-log
-- Version de PHP : 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `livraison_db`
--
CREATE DATABASE IF NOT EXISTS `livraison_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `livraison_db`;

-- --------------------------------------------------------

--
-- Structure de la table `points_livraison`
--

DROP TABLE IF EXISTS `points_livraison`;
CREATE TABLE IF NOT EXISTS `points_livraison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_point` varchar(100) NOT NULL,
  `type_point` enum('Locker','Shop') NOT NULL,
  `nom_magasin` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `code_postal` varchar(20) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `photo_streetview` varchar(255) DEFAULT 'Non disponible',
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `actif` enum('Oui','Non') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `points_livraison`
--

INSERT INTO `points_livraison` (`id`, `code_point`, `type_point`, `nom_magasin`, `adresse`, `code_postal`, `ville`, `photo_streetview`, `latitude`, `longitude`, `actif`) VALUES
(6, 'FR000763', 'Shop', 'A Hair Shop', '3b Rue du Fort', '67118', 'Geispolsheim', 'https://maps.app.goo.gl/VFehuKZ5BjFjdr2r6', 48.52225410, 7.69142280, 'Oui'),
(8, '55011', 'Shop', 'Flamms\'heim', '2 Rue du Fort', '67118', 'Geispolsheim', 'https://maps.app.goo.gl/2mxwnpz8o9cPkXKH8', 48.52136860, 7.69454370, 'Oui'),
(9, 'FR001090', 'Locker', 'Auchan Hautepierre', '1 Place André Maurois', '67200', 'Strasbourg', 'https://maps.app.goo.gl/K4aPF4hzXBpwQX827', 48.59193410, 7.69527290, 'Oui'),
(10, 'FR002582', 'Locker', 'KAP Carrelage', '2 Rue Gay Lussac', '67201', 'Eckbolsheim', 'https://maps.app.goo.gl/jDfoAFVNhanG4Lhg9', 48.58836400, 7.68295800, 'Oui'),
(12, '50022', 'Locker', 'Groupe NAP - MT Laveries', '2 Rue d\'Oberhausbergen', '67202', 'Wolfisheim', 'https://maps.app.goo.gl/PjP7StjixuGM6Lio6', 48.58771970, 7.66804390, 'Oui'),
(14, '50007', 'Locker', 'Groupe NAP - Tabac J Souvenir', '47 Rue de la 1ère Division Blindée', '67114', 'Eschau', 'https://maps.app.goo.gl/e46b22PfbsPg6fua6', 48.48594240, 7.71515630, 'Oui'),
(15, '50002', 'Locker', 'SCC Place des Halles', '24 Place des Halles', '67000', 'Strasbourg', 'https://maps.app.goo.gl/k1J49i7WjYWRgvip8', 48.58670110, 7.74182250, 'Oui');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
