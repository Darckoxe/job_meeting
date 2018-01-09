-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- H�te : 127.0.0.1
-- G�n�r� le :  mar. 19 d�c. 2017 � 14:18
-- Version du serveur :  10.1.26-MariaDB
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de donn�es :  `job-meeting`
--

-- --------------------------------------------------------

--
-- Structure de la table `scriptconfig`
--

CREATE TABLE `scriptconfig` (
  `heureDebutMatin` time NOT NULL,
  `heureDebutAprem` time NOT NULL,
  `nbCreneauxMatin` int(11) NOT NULL,
  `nbCreneauxAprem` int(11) NOT NULL,
  `dureeCreneau` int(11) NOT NULL,
  `dateDebutInscriptionEtu` date NOT NULL,
  `dateDebutInscriptionEnt` date NOT NULL,
  `dateFinInscriptionEnt` date NOT NULL,
  `dateFinInscription` date NOT NULL,
  `dateDebutVuePlanning` date NOT NULL,
  `dateFinVuePlanning` date NOT NULL,
  `dateEvenement` date NOT NULL,
  `siteEvenement` varchar(255) NOT NULL,
  `adresseIUT` varchar(255) NOT NULL,
  `mailAdministrateur` varchar(255) NOT NULL,
  `telAdministrateur` varchar(10) NOT NULL,
  `nomAdministrateur` varchar(255) NOT NULL,
  `heureCreneauPause` time NOT NULL,
  `heureCreneauPauseMatin` time NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- D�chargement des donn�es de la table `scriptconfig`
--

INSERT INTO `scriptconfig` (`heureDebutMatin`, `heureDebutAprem`, `nbCreneauxMatin`, `nbCreneauxAprem`, `dureeCreneau`, `dateDebutInscriptionEtu`, `dateDebutInscriptionEnt`, `dateFinInscriptionEnt`, `dateFinInscription`, `dateDebutVuePlanning`, `dateFinVuePlanning`, `dateEvenement`, `siteEvenement`, `adresseIUT`, `mailAdministrateur`, `telAdministrateur`, `nomAdministrateur`, `heureCreneauPause`, `heureCreneauPauseMatin`) VALUES
('10:00:00', '13:40:00', 6, 12, 20, '2017-01-01', '2017-02-15', '2017-12-31', '2018-01-01', '2016-05-05', '0000-00-00', '2017-03-30', 'de la Fleuriaye', '2 avenue du Prof Jean Rouxel - Carquefou', 'yohann.rialet@etu.univ-nantes.fr', '0202020202', 'Y Rialet');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
