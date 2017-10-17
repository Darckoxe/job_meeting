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

}
