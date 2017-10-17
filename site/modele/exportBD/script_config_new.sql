-- à remplacer dans le script de BD (export config -> BD)
--
-- Structure de la table `scriptconfig`
--

CREATE TABLE IF NOT EXISTS `scriptconfig` (
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
  `heureCreneauPause` time NOT NULL

  
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `scriptconfig`
--

INSERT INTO `scriptconfig` (`heureDebutMatin`, `heureDebutAprem`, `nbCreneauxMatin`, `nbCreneauxAprem`, `dureeCreneau`, `dateDebutInscriptionEtu`, `dateDebutInscriptionEnt`, `dateFinInscriptionEnt`, `dateFinInscription`, `dateDebutVuePlanning`, `dateFinVuePlanning`, `dateEvenement`,`siteEvenement`, `adresseIUT`, `mailAdministrateur`, `telAdministrateur`, `nomAdministrateur`) VALUES
('10:00:00', '13:40:00', 0, 12, 20, '2017-03-15', '2017-02-15', '2017-12-31', '2017-12-16', '2016-03-30', '0000-00-00', '0000-00-00', 'de la Fleuriaye', '2 avenue du Prof Jean Rouxel - Carquefou', 'mailAdmin@univ-nantes.aModifier', '0202020202', 'Prenom Nom','15:40:00');