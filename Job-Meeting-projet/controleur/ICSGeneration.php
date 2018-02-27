<?php
/**
* Classe permettant de générer les fichiers ICS (calendrier)
*/
require_once __DIR__."/../modele/dao/dao_2016.php";

class ICSGeneration{

  /**
  * Fonction permettant de créer le calendrier relatif à un utilisateur
  * @param  String $mailUser l'adresse mail de l'utilisateur concerné
  * @param  String $cheminDossier le chemin du dossier où enregistrer le calendrier
  * @return le chemin du fichier (cheminDossier/nomFichier)
  */
  public static function creerCalendrierUser($mailUser,$cheminDossier) {

    $dao = new Dao_2016();

    $idEntreprise = $dao->getIDEntrepriseMail($mailUser);

    if($idEntreprise != null){
      return ICSGeneration::creerCalendrierEnt($idEntreprise,$cheminDossier);
    }else{
      $idEtudiant = $dao->getIDEtudiantMail($mailUser);
      if($idEtudiant != null){
        return ICSGeneration::creerCalendrierEtu($idEtudiant,$cheminDossier);
      }
    }
    // L'adresse mail ne correspond ni à un compte etudiant, ni à un compte entreprise
    return "";
  }

  /**
  * Fonction permettant de créer le calendrier relatif à une entreprise
  * @param  String $idEntreprise  le nom de l'entreprise concernée
  * @param  String $cheminDossier le chemin du dossier de destination du fichier ics
  */
  public static function creerCalendrierEnt($idEntreprise,$cheminDossier) {

    // Création du calendrier
    $calendar = "BEGIN:VCALENDAR\n";
    $calendar.= "VERSION:2.0\n";
    $calendar.= "METHOD:PUBLISH\n";

    $dao = new Dao_2016();


    // Tableau contenant plusieurs tableaux : un sous-tableau pour chaque créneau
    // ( [0] => heureDebut, [1] => nomFormation, [2] => prenomEtu, [3] => nomEtu )
    $tableauPlanning = $dao->getEntPlanning($idEntreprise);

    // Si l'entreprise n'est concernée par aucun entretien, on ne créé par de fichier .ics pour elle
    if(sizeof($tableauPlanning) == 0){
      return "";
    }

    $tableauConfig = $dao->getConfiguration();
    $lieu = "IUT - ".$tableauConfig['adresseIUT'];
    $dateEvenementTmp = $tableauConfig['dateEvenement'];

    // Création d'un évènement par créneau qui sera rajouté au calendrier global
    foreach ($tableauPlanning as $key => $value) {
      $debut = $dateEvenementTmp." ".$value[0];
      $formation = $value[1];
      $prenomEtudiant = $value[2];
      $nomEtudiant = $value[3];

      $titreEvenement = "Job-Meeting IUT - Entretien ".$formation;
      $descriptionEvenement = "Entretien avec ".$prenomEtudiant." ".$nomEtudiant.".";
      $event = ICSGeneration::creerEvent($debut,$titreEvenement,$descriptionEvenement,$lieu);
      $calendar .= $event;
    }

    $calendar .= "END:VCALENDAR\n";
    ICSGeneration::sauvegarderICS($cheminDossier."/"."Entretiens Job-Meeting",$calendar);
    return "Entretiens Job-Meeting.ics";
  }

  /**
  * Fonction permettant de créer le calendrier relatif à un étudiant
  * @param  String $idEtudiant l'identifiant de l'étudiant concerné
  * @param  String $cheminDossier le chemin du dossier de destination du fichier ics
  */
  public static function creerCalendrierEtu($idEtudiant,$cheminDossier) {

    // Création du calendrier
    $calendar = "BEGIN:VCALENDAR\n";
    $calendar.= "VERSION:2.0\n";
    $calendar.= "METHOD:PUBLISH\n";

    $dao = new Dao_2016();


    // Tableau contenant plusieurs tableaux : un tableau pour chaque créneau
    // ( [0] => heureDebut [1] => nomEntreprise)
    $tableauPlanning = $dao->getEtuPlanning($idEtudiant);

    // Si l'étudiant n'a aucun entretien, on ne créé par de fichier .ics pour lui.
    if(sizeof($tableauPlanning) == 0){
      return "";
    }

    $tableauConfig = $dao->getConfiguration();
    $lieu = "IUT - ".$tableauConfig['adresseIUT'];
    $dateEvenementTmp = $tableauConfig['dateEvenement'];

    // Création d'un évènement par créneau qui sera rajouté au calendrier global
    foreach ($tableauPlanning as $key => $value) {
      $debut = $dateEvenementTmp." ".$value[0];
      $nomEntreprise = $value[1];
      $titreEvenement = "Job-Meeting IUT - Entretien";
      $descriptionEvenement = "Entretien avec l'entreprise ".$nomEntreprise.".";
      $event = ICSGeneration::creerEvent($debut,$titreEvenement,$descriptionEvenement,$lieu);
      $calendar .= $event;
    }

    $calendar .= "END:VCALENDAR\n";
    ICSGeneration::sauvegarderICS($cheminDossier."/"."Entretiens-Job-Meeting",$calendar);
    return "Entretiens-Job-Meeting.ics";
  }

  /**
  * Fonction permettant de créer un évènement en respectant le format ICS
  * @param  String $debut       la date correspondant au début de l'évènement
  * @param  String $nom         le nom de l'évènement
  * @param  String $description la description de l'évènement
  * @param  String $lieu        le lieu de l'évènement
  * @return String l'évènement au format ICS
  */
  public static function creerEvent($debut,$nom,$description,$lieu) {
    // Création de l'évènement et de ses caractéristiques
    $donnees= "BEGIN:VEVENT\n";

    // Deux solutions pour la gestion des heures :

    // 1ère solution : définir la zone horaire mais bug sur les applications android
    // Début de l'évènement
    // $heureDebut = new DateTime($debut,new DateTimeZone('Europe/Paris'));
    // $donnees.= "DTSTART:".$heureDebut->format('Ymd\THis')."\n";
    // Fin de l'évènement (20 minutes plus tard que le début)
    // $heureFin = new DateTime($debut,new DateTimeZone('Europe/Paris'));
    // date_add($heureFin, date_interval_create_from_date_string('20 minutes'));
    // $donnees.= "DTEND:".$heureFin->format('Ymd\THis')."\n";

    // 2ème solution : soustraire de deux heures (décalage par rapport à UTC à l'heure d'été) ou une heure (décalage par rapport à l'heure d'hiver).
    // Début de l'évènement
    $decalageHeure = 2; // Décalage de deux heures à l'heure d'été.
    if(! ICSGeneration::dateHeureEte($debut)){
      $decalageHeure = 1; // Décalage d'une heure selement à l'heure d'hiver.
    }
    $heureDebut = new DateTime($debut);

    $dao = new Dao_2016();

    $dureeCreneau = $dao->getConfiguration()['dureeCreneau'];


    date_sub($heureDebut, date_interval_create_from_date_string($decalageHeure.' hours'));
    $donnees.= "DTSTART:".$heureDebut->format('Ymd\THis\Z')."\n"; // Le Z désigne le fuseau horaire UTC
    // Fin de l'évènement (20 minutes plus tard que le début)
    $heureFin = new DateTime($debut);
    date_sub($heureFin, date_interval_create_from_date_string($decalageHeure.' hours'));
    date_add($heureFin, date_interval_create_from_date_string($dureeCreneau.' minutes'));
    $donnees.= "DTEND:".$heureFin->format('Ymd\THis\Z')."\n";

    // Titre de l'évènement
    $donnees.= "SUMMARY:".$nom."\n";
    // Lieu de l'évènement
    $donnees.= "LOCATION:".$lieu."\n";
    // Description de l'évènement
    $donnees.= "DESCRIPTION:".$description."\n";
    // ID lié à l'évènement
    $donnees.="UID:".uniqid()."\n";
    // Indique que la personne concernée par l'évènement est rendue indisponible par cet évènement
    $donnees.= "TRANSP: OPAQUE\n";
    // Fin de la création de l'évènement
    $donnees.= "END:VEVENT\n";

    return $donnees;
  }

  /**
  * Fonction permettant de sauvegarder le calendrier dans un fichier ics
  * @param  String $nomFichier le nom du fichier où l'on va stocker le calendrier
  * @param  String $calendrier le calendrier
  */
  public static function sauvegarderICS($nomFichier,$calendrier) {
    file_put_contents($nomFichier.".ics",$calendrier);
  }

  /**
  * Fonction permettant de connaître les dates liées aux changements d'heure d'une année
  * @param  String $year l'année concernée
  * @return un tableau contenant la date du changement d'été et celle du changement d'hiver
  */
  public static function getChangementsHeure($year){
    // Le changement d'heure d'été a lieu le dernier dimanche de mars
    $chgtEte = date('Y-m-d',strtotime("last Sunday of March ".$year));
    // Le changement d'heure d'hiver a lieu le dernier dimanche d'octobre
    $chgtHiver = date('Y-m-d',strtotime("last Sunday of October ".$year));
    return array($chgtEte,$chgtHiver);
  }

  /**
  * Fonction qui permet de savoir si une date est à l'heure d'été ou non
  * @param  String $dateTmp la date recherchée
  * @return true si la date est à l'heure d'été, false si la date est à l'heure d'hiver
  */
  public static function dateHeureEte($dateTmp){
    $date = date('Y-m-d',strtotime($dateTmp));
    $annee = date('Y',strtotime($dateTmp));

    $dateChgtHeureAnneeCourante   = ICSGeneration::getChangementsHeure($annee);

    // Entre mars année N et octobre année N -> à l'heure d'été
    if( $date >= $dateChgtHeureAnneeCourante[0] && $date < $dateChgtHeureAnneeCourante[1]){
      return true;
    }
    // Entre janvier et fin mars de l'année N ou
    // entre octobre et décembre de l'année N -> à l'heure d'hiver
    else{
      return false;
    }
  }

}
