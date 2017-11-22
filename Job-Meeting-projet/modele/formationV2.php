<?php

require_once 'dao/dao.php';

/**
 * Classe permettant de gérer les formations.
 */
class Formation {

  private $IDent;
  private $form = array();
  private $nbForm;
  private $nbSessions;
  private $periode;

  private $nbCreneauxMatin;
  private $nbCreneauxAprem;
  private $nbCreneaux;
  private $crenOrigin;


  private $ArrayForm = array();

/**
 * Constructeur de la classe.
 * @param int     $IDentreprise    l'identifiant de l'entreprise.
 * @param String  $formation       les formations recherchées.
 * @param int     $nbSessions      le nombre de stands désirés.
 * @param String  $periode         le type de créneau souhaité (matin,apres_midi,journee).
 * @param int     $nbCreneauxMatin le nombre de créneaux le matin.
 * @param int     $nbCreneauxAprem le nombre de créneaux l'après-midi.
 */
  function __construct($IDentreprise, $formation, $nbSessions, $periode, $nbCreneauxMatin, $nbCreneauxAprem)
  {
    $this -> IDent = $IDentreprise;
    $this -> form = explode(",",$formation);
    $this -> nbForm = sizeof($this -> form);
    $this -> nbSessions = $nbSessions;
    $this -> periode = $periode;
    $this -> nbCreneauxMatin = $nbCreneauxMatin;
    $this -> nbCreneauxAprem = $nbCreneauxAprem;

    # calculnbCreneau #
    $this -> crenOrigin = 1;

    switch ($this -> periode) {
      case 'matin':
      $this -> nbCreneaux = $this -> nbCreneauxMatin;
      break;
      case 'apres_midi':
      $this -> nbCreneaux = $this -> nbCreneauxMatin + $this -> nbCreneauxAprem;
      $this -> crenOrigin = $this -> nbCreneauxMatin + 1;
      break;
      default:
      $this -> nbCreneaux = $this -> nbCreneauxMatin + $this -> nbCreneauxAprem;
      break;
    }
  }

  /**
   * Méthode permettant de créer les formations d'une entreprise.
   */
  public function createForm(){
    $dao=new Dao();
    $dao->connexion();

    $dao -> supprimerFormation($this -> IDent);

    $crenAmD = $this -> crenOrigin; #debut matin
    $crenPmD = $this -> nbCreneauxMatin + 1; #debut aprem
    $crenAmF = $this -> nbCreneauxMatin; #fin matin
    $crenPmF = $this -> nbCreneaux; #fin aprem et journee

    # # # # # # # # # # # # # # # # # # # #
    #   Am -> matin et Pm -> apres_midi   #
    #   D -> debut et F -> fin            #
    #                                     #
    #   $crenAmD, $crenAmF = matin        #
    #   $crenPmD, $crenPmF = apres_midi   #
    #   $crenAmD, $crenPmF = journee      #
    # # # # # # # # # # # # # # # # # # # #

    # ajoutFormation(formation, IDentreprise, creneauDebut, creneauFin)

    #############################
    #          JOURNEE          #
    #############################
    // une personne pour une formation
    if($this -> nbForm == 1 && $this -> nbSessions == 1){
      $this -> ArrayForm[] = array($this -> form[0], $this -> IDent, "", $crenAmD, $crenPmF);
    }
    // k*nbForm = k*nbSessions
    else if($this -> nbForm == $this -> nbSessions){
      foreach ($this -> form as $value) {
        $this -> ArrayForm[] = array($value, $this -> IDent, "", $crenAmD, $crenPmF);
      }
    }
    // si nbForm = 2*nbSessions
    else if(($this -> nbForm / $this -> nbSessions) == 2){
      $this -> autreCas();
    }
    // si nbForm > nbSessions
    else if($this -> nbForm > $this -> nbSessions){
      $this -> casSpecial();
    }
    // si nbSessions > nbForm
    else if(($this -> nbSessions > $this -> nbForm)){
      $cpt = $this -> nbSessions;
      for ($i=0; $i < ceil($this -> nbSessions/$this -> nbForm); $i++) {
        foreach ($this -> form as $value) {
          if($cpt > 0){
            $this -> ArrayForm[] = array($value, $this -> IDent, "", $crenAmD, $crenPmF);
          }
          $cpt --;
        }
      }

    }else{
      $this -> autreCas();
    }

    # ajout a la bdd
    foreach ($this -> ArrayForm as $formTest) {
      $dao -> ajoutFormation($formTest[0], $formTest[1], $formTest[3], $formTest[4]);
    }
    $dao-> deconnexion();
  }

  /**
   * Fonction permettant de gérer un autre cas.
   * @return Formation la formation concernée.
   */
  public function autreCas() {
    $crenOrigin = 1;
    # calculnbCreneau #
    switch ($this -> periode) {
      case 'matin':
      $nbCreneaux = $this -> nbCreneauxMatin;
      break;
      case 'apres_midi':
      $nbCreneaux = $this -> nbCreneauxAprem;
      $crenOrigin = $this -> nbCreneauxMatin + 1;
      break;
      default:
      $nbCreneaux = $this -> nbCreneauxMatin + $this -> nbCreneauxAprem;
      break;
    }

    $crenForm = floor(($nbCreneaux/$this -> nbForm)*$this -> nbSessions);

    $crenRestant = $nbCreneaux;
    $crenDebut = $crenOrigin;

    foreach ($this -> form as $value) {
      # RESET #
      if ($crenRestant < $crenForm) {
        $crenRestant = $nbCreneaux;
        $crenDebut = $crenOrigin;
      }
      # ADD #
      $crenFin = $crenDebut + $crenForm - 1;
      $this -> ArrayForm[] = array($value, $this -> IDent, "", $crenDebut, $crenFin);
      $crenDebut = $crenFin + 1;
      $crenRestant -= $crenForm;
    }
    return $this -> ArrayForm;
  }

  /**
   * Fonction permettant de gérer cas spécial.
   * @return Formation la formation concernée.
   */
  public function casSpecial() {
    $crenOrigin = 1;
    # calculnbCreneau #
    switch ($this -> periode) {
      case 'matin':
      $nbCreneaux = $this -> nbCreneauxMatin;
      break;
      case 'apres_midi':
      $nbCreneaux = $this -> nbCreneauxAprem;
      $crenOrigin = $this -> nbCreneauxMatin + 1;
      break;
      default:
      $nbCreneaux = $this -> nbCreneauxMatin + $this -> nbCreneauxAprem;
      break;
    }

    $crenForm = (floor($nbCreneaux/$this -> nbForm)*$this -> nbSessions);
    $crenParForm = floor($nbCreneaux/$this -> nbForm);

    $crenRestant = $nbCreneaux;
    $crenDebut = $crenOrigin;

    $nbFormRestant = $this -> nbForm;

    $test = "true";
    $cpt = 0;

    while($test == "true"){
      $crenFin = $crenDebut + $crenForm - 1;
      for ($i=0; $i < $this -> nbSessions; $i++) {
        if($nbFormRestant > 0){
          $this -> ArrayForm[] = array($this -> form[$cpt], $this -> IDent, "", $crenDebut, $crenFin);
          $cpt ++;
          $nbFormRestant --;
        }

      }
      $crenDebut = $crenFin + 1;

      if($nbFormRestant < 2) {
        $test = "false";
      }
    }
    if($nbFormRestant > 0){
      $crenFin = $crenDebut + $crenParForm - 1;
      for ($i=0; $i < $this -> nbSessions; $i++) {
        $this -> ArrayForm[] = array($this -> form[$cpt], $this -> IDent, "", $crenDebut, $crenFin);
      }
    }
    return $this -> ArrayForm;
  }

  /**
   * Fonction permettant l'affichage de la formation.
   * @param  array(String,int,int) $listeFormations un tableau (nomFormation,creneauDebut,creneauFin).
   */
  public static function afficherForm($listeFormations) { // array[nomFormation, creneauDebut, creneauFin]
    $dao = new Dao();
    $tabConfig = $dao -> getConfiguration();
    $classFormation = "Formation";
    ?>

      <div id="main">
        <br/>
        <table id="tabFormation">
          <tr>
            <td colspan=4 id="titre"><b> Formations <b></td>
          </tr>
          <tr>
            <td colspan= 1> Nom de la formation </td>
            <td colspan= 1> Debut des entretiens</td>
            <td colspan= 1> Fin des entretiens</td>
            <td colspan= 1> Nombre d'entretiens </td>
          </tr>

          <?php
          //affichage formation + horaire
        foreach ($listeFormations as $formation) {
          echo "<tr id='formation'>";
          echo "<td>";
          echo $formation[1]; //nom formation
          echo "</td>";
          echo "<td>";
          echo $formation[2]; //creneau debut
          echo "</td>";
          echo "<td>";
          echo $formation[3]; //creneau fin
          echo "</td>";
          echo "<td>";
          $nbEntretiens = $formation[3] - $formation[2] +1;
          echo $nbEntretiens; // nb Entretiens
          echo "</td>";
          echo "</tr>";
        }
      ?>
    </table>
    <?php
  }

  /**
   * Fonction permettant de calculer les horaires.
   * @param  int $creneau le créneau concerné.
   * @param  array $tabConfig le tableau de configuration de l'évènement.
   * @param  String $horaire  l'horaire concerné.
   * @return String l'horaire.
   */
  public static function calculHoraire($creneau, $tabConfig, $horaire){
    $duree = $tabConfig["dureeCreneau"];
    $start = 1;
    if($creneau <= $tabConfig["nbCreneauxMatin"]) { // si c'est le matin
      $heureString = $tabConfig["heureDebutMatin"];
    } else { //si c'est l'apres midi
      $heureString = $tabConfig["heureDebutAprem"];
      $start = $tabConfig["nbCreneauxMatin"] + 1;
    }
    $heureString = explode(':', $heureString);
    $heure = $heureString[0];
    $min = $heureString[1];

    if($horaire == "fin"){
      $start --;
    }

    for($i = $start; $i < $creneau; $i++){
      $min += $duree;
      if($min == 60) {
        $min = 0;
        $heure++;
      }
    }

    if($min == 0){
      $min = "00";
    }
    return $heure.':'.$min;
  }

  /**
   * Fonction permettant de mettre à jour les formations de l'entreprise.
   * @param  int $idEnt l'identifiant de l'entreprise.
   */
  public static function updateFormation($idEnt){
    $dao=new dao();
    $dao->connexion();
    $entr=$dao->getEnt($idEnt)[0];
    $tabConfig = $dao -> getConfiguration();
    $dao->deconnexion();
    $formation = new formation($entr-> getId(), $entr-> getFormationsRecherchees(), $entr-> getNbStands(), $entr-> getTypeCreneau(), $tabConfig["nbCreneauxMatin"], $tabConfig["nbCreneauxAprem"]);
    $formation -> createForm();
  }

  /**
   * Fonction permettant de générer une formation.
   */
  public static function generateFormation(){
    $dao=new dao();
    $dao->connexion();
    $listeEnt=$dao->getEntreprises();
    $tabConfig = $dao -> getConfiguration();
    $dao->deconnexion();

    foreach ($listeEnt as $entr) {
      $formation = new formation($entr["IDEnt"], $entr["formationsRecherchees"], $entr["nbStands"], $entr["typeCreneau"], $tabConfig["nbCreneauxMatin"], $tabConfig["nbCreneauxAprem"]);
      $formation -> createForm();
    }
  }
}
?>
