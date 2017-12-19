<?php
require_once __DIR__."/../vue/vueInscriptionEtu.php";

/**
* Contrôleur de l'inscription d'un étudiant.
*/
class ControleurInscriptionEtu{

  private $vue;

  /**
  * Constructeur de la classe initialisant la vue de la page d'inscription d'un étudiant.
  */
  public function __construct(){
    $this->vue=new VueInscriptionEtu();
  }

  /**
   * Fonction permettant l'affichage du formulaire d'inscription pour les étudiants.
   */
  public function inscriptionEtu(){
    $this->vue->afficherFormulaireEtu();
  }

  public function gestionEnvoiCV(){
    // on vérifie que le fichier est bien upload
    if (isset($_FILES['cv']['error'])){
      if ($_FILES['cv']['error'] > 0) {
        echo "Une erreur lors du transfert de fichier est survenue.";
        echo $_FILES['cv']['error'];
        exit();}
    }
    // on vérifie la taille du fichier
    if (isset($_FILES['cv']['size'])){ // taille en octet
      if ($_FILES['cv']['size'] > 1048576) {
        echo "La taille du fichier est trop grande (1Mo max).";
        exit();
      }
    }
    // on vérifie que le format est en pdf
    if (isset($_FILES['cv']['name'])) {
      $extensions_valides = array("pdf");
      $extension_upload = strtolower( substr( strrchr($_FILES['cv']['name'],'.') ,1) );
      if (!in_array($extension_upload, $extensions_valides)) {
        echo "Mauvais format du fichier (pdf nécessaire)";
        exit();
      }
      else {
        if (isset($_POST['email'])) {
          $nomFichier = $_POST['email'];
          $chemin = "cv/{$nomFichier}.{$extension_upload}";
          if (isset($_FILES['cv']['tmp_name'])) {
            $resultat = move_uploaded_file($_FILES['cv']['tmp_name'], $chemin);
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
