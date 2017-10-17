<?php
require_once __DIR__."/../vue/vueMenu.php";
require_once __DIR__."/../controleur/controleurMail.php";

/**
* Contrôleur de la majorité des pages affichées lors de la connexion d'un utilisateur.
*/
class ControleurMenu{

  private $vue;
  private $dao;
  private $ctrlMail;

  /**
  * Constructeur de la classe permettant d'initialiser la vue, le dao et le contrôleur de mail.
  */
  public function __construct(){
    $this->vue=new VueMenu();
    $this->dao = new Dao_2016();
    $this->ctrlMail = new ControleurMail();
  }

  /**
  * Fonction qui demandera à VueMenu de générer une vue correspondant au choix du menu selon le type de connexion.
  * @param  int $pos l'entier correspondant à la page souhaitée.
  */
  public function afficherMenu($pos) {
    if ($pos == 1) {
      if ($_SESSION['type_connexion'] == "entreprise") {
        $this->vue->afficherPlanningEnt();
      }
      if ($_SESSION['type_connexion'] == "etudiant") {
        $this->vue->afficherPlanningEtu();
      }
      if ($_SESSION['type_connexion'] == "admin") {
        $this->vue->afficherPlanningAdmin();
      }
    }
    if ($pos == 2) {
      if ($_SESSION['type_connexion'] == "entreprise") {
        $this->vue->afficherCompteEnt();
      }
      if ($_SESSION['type_connexion'] == "etudiant") {
        $this->vue->afficherChoix();
      }
      if ($_SESSION['type_connexion'] == "admin") {
        $this->vue->afficherComptes();
      }
    }
    if ($pos == 3) {
      if ($_SESSION['type_connexion'] == "etudiant") {
        $this->vue->afficherEntreprises();
      }
      if($_SESSION['type_connexion'] == "admin"){
        $tabFormations = $this->dao->getNbCreneauxParFormation();
        $tabFormationsNonChoisies = $this->dao->getAllFormationsNonChoisies();
        $listeDepartements = $this->dao->getListeDepartements();
        $this->vue->afficherListeFormations($tabFormations,$tabFormationsNonChoisies, $listeDepartements);
      }

    }
    if ($pos == 4) {
      if ($_SESSION['type_connexion'] == "etudiant") {
        $this->vue->afficherCompteEtu();
      }
      if ($_SESSION['type_connexion'] == "admin") {
        $listeMails = $this->ctrlMail->getAllMails();
        $this->vue->afficherMails($listeMails);
      }
    }
    if ($pos == 5){
      if ($_SESSION['type_connexion'] == "admin") {
        $this->vue->afficherConfig();
      }

    }
    if ($pos == 6){
      if ($_SESSION['type_connexion'] == "admin") {
        $this->vue->afficherAutres();
      }
    }

  }

  /**
  * Fonction qui permet l'affichage d'une formation
  * @param  String $nomFormation nom de la formation (version courte, initiale) ex : GEII au lieu de Genie emectrique et informatique industrielle
  */
  public function afficherUneFormation($nomFormation){
    $tabFormation = $this->dao->getNbCreneauxFormation($nomFormation);
    $tabTmp = array();

    // Récupération des ID des entreprises
    foreach ($tabFormation as $elt) {
      $id = $this->dao->getIdEntreprise($elt['nomEnt']);
      $elt['idEnt'] = $id;
      $tabTmp[] = $elt;
    }
    //récupération du lien correspondant à la formation dans la table listeformation
    $tmp = $this->dao->getListeFormations();
    foreach ($tmp as $formation) {
      if ($nomFormation == $formation->getInitiales()) {
        //lien de la formation donnée en paramètre
        $url = $formation->getLien();
      }
    }
    $this->vue->afficherUneFormation($tabTmp, $url);
  }

  /**
  * Fonction permettant de supprimer une formation et de réafficher la liste des formations
  * @param  String $initialesFormation les initiales de la formation
  */
  public function suppressionFormation($initialesFormation){
    $this->dao->supprimerFormationListe($initialesFormation);

    $tabFormations = $this->dao->getNbCreneauxParFormation();
    $tabFormationsNonChoisies = $this->dao->getAllFormationsNonChoisies();
    $tabFormationsNonChoisies = $this->dao->getAllFormationsNonChoisies();
    $listeDepartements = $this->dao->getListeDepartements();
    $this->vue->afficherListeFormations($tabFormations,$tabFormationsNonChoisies, $listeDepartements);
  }

  /**
  * Fonction permettant de modifier le bandeau du site.
  * @param  String $fichier la nouvelle image représentant le bandeau du site.
  */
  public function changeBandeauSite($fichier){

    if( empty($fichier) || ($fichier['error'] != 0 )) {
      echo "Le fichier est incorrect, veuillez réessayer.";
      return;
    }

    $extension = pathinfo($fichier['name'], PATHINFO_EXTENSION);

    if($extension != 'png'){
      echo "L'image doit être au format png";
      return;
    }

    $nomDossierDest = "vue/img/";
    //  chmod($nomDossierDest,0666); // droits de lecture et d'écriture
    $nomFichier = "bandeau-RAlt.png";
    if(file_exists($nomDossierDest.$nomFichier)){
      unlink($nomDossierDest.$nomFichier);
    }

    if( !empty($fichier) && ($fichier['error'] == 0 )) {

      if ( ! (move_uploaded_file($fichier['tmp_name'],$nomDossierDest.'/'.$nomFichier))) {
        echo "Erreur durant l'upload du fichier";
      }
    }
    //  chmod($nomDossierDest,0444); // droits de lecture

  }

}
