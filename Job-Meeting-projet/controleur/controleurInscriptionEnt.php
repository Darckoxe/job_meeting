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
    /* Récupérer le nom de l'entreprise */
    echo ("fichier".$_FILES[$name]['name']);
    $_POST['nomSociete'] = "TACO";
    echo ("Nom société".$_POST['nomSociete']);

    $listeFormations = $this->dao->getListeFormations();
    foreach ($listeFormations as $formation){
      $name="offre_";
      $name.=$formation->getInitiales();
      if (isset($_FILES[$name]['error'])) {
        if(($_FILES[$name]['error'] == 0) || ($_FILES[$name]['error'] == 4)){
          if ($_FILES[$name]['size'] > 10485760) {
            echo "La taille du fichier est trop grande (1Mo max).";
            exit();
          }
          $extensions_valides = array("pdf");
          $extension_upload = strtolower( substr( strrchr($_FILES[$name]['name'],'.') ,1) );

          if(($_FILES[$name]['error'] == 0)){
            if (!in_array($extension_upload, $extensions_valides)) {
              echo "Mauvais format du fichier (pdf necessaire)";
              exit();
            }
          }

          if ((isset($_POST['nomSociete'])) && ($_FILES[$name]['error'] == 0)) {
              $nomFichier = $_POST['nomSociete'].'_'.$name;
              $chemin = "offre/{$nomFichier}.{$extension_upload}";
              echo $nomFichier;
              if (isset($_FILES[$name]['tmp_name'])) {
                $resultat = move_uploaded_file($_FILES[$name]['tmp_name'], $chemin);
                  if (!$resultat) {
                    echo "Echec de transfert";
                    exit();
                  }
              }
          }
        }
        else{
          echo "Une erreur lors du transfert de fichier est survenue. ";
          echo "Code erreur ".$_FILES[$name]['error'];
          exit();
        }
      }
    }
  }


}
