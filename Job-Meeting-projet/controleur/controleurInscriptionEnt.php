<?php
require_once __DIR__."/../vue/vueInscriptionEnt.php";
require_once __DIR__."/../modele/dao/dao.php";

/**
 * Contrôleur de l'inscription d'une entreprise.
*/
class ControleurInscriptionEnt {

  private $vue;
  private $dao;

  /**
  * Constructeur de la classe initialisant la vue de la page d'inscription d'une entreprise et le dao.
  */
  public function __construct(){
    $this->vue=new VueInscriptionEnt();
    $this->dao=new Dao();
  }

  /**
  * Fonction permettant l'affichage du formulaire d'inscription pour les entreprises.
  */
  public function inscriptionEnt(){
    $this->vue->afficherFormulaireEnt();
  }

  public function gestionEnvoiOffreModif(){
    $nomSociete = ($this->dao->getNomEntreprise($_SESSION['idUser']));
    $_POST['nomSociete'] = $nomSociete;
    $listeFormations = $this->dao->getListeFormations();
    foreach ($listeFormations as $formation){
      echo "Nom du fichier = ".$_FILES['offre_modif'.$formation->getInitiales()]['name'];
      if ((isset($_FILES['offre_modif'.$formation->getInitiales()]['error'])) && ($_FILES['offre_modif'.$formation->getInitiales()]['error'] != 0)) {
          if ($_FILES['offre_modif'.$formation->getInitiales()]['error'] == 4) {
            return;
          }
            echo "Une erreur lors du transfert de fichier est survenue. ";
            echo "Code erreur ".$_FILES['offre_'.$formation->getInitiales()]['error'];
            exit();
      }

      // on vérifie la taille du fichier
      if (isset($_FILES['offre_modif'.$formation->getInitiales()]['size'])){  // taille en octet
        if ($_FILES['offre_modif'.$formation->getInitiales()]['size'] > 10485760) {
          echo "La taille du fichier est trop grande (1Mo max).";
          exit();
      }
    }

    // on vérifie que le format est en pdf
  if (isset($_FILES['offre_modif'.$formation->getInitiales()]['name'])) {
    $extensions_valides = array("pdf");
    $extension_upload = strtolower( substr( strrchr($_FILES['offre_modif'.$formation->getInitiales()]['name'],'.') ,1) );
    if (!in_array($extension_upload, $extensions_valides)) {
      echo "Mauvais format du fichier (pdf necessaire)";
      exit();
    }
    else {
      if (isset($_POST['nomSociete'])) {
        $nomFichier = $_POST['nomSociete'].'_offre_'.$formation->getInitiales();
        echo $nomFichier;
        $chemin = "offre/{$nomFichier}.{$extension_upload}";
        if (isset($_FILES['offre_modif'.$formation->getInitiales()]['tmp_name'])) {
          $resultat = move_uploaded_file($_FILES['offre_modif'.$formation->getInitiales()]['tmp_name'], $chemin);
            if (!$resultat) {
              echo "Echec de transfert";
              exit();
              }
            }
          }
        }
      }
    }
  }


}
