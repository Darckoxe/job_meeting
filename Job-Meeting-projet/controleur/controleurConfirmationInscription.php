<?php
require_once __DIR__."/../vue/vueConfirmationInscription.php";

/**
* Contrôleur de la confirmation de l'inscription d'un utilisateur.
*/
class ControleurConfirmationInscription{

  private $vue;

  /**
  * Constructeur de la classe initialisant la vue de la page de confirmation d'inscription.
  */
  public function __construct(){
    $this->vue=new VueConfirmationInscription();
  }

  /**
  * Fonction permettant l'affichage de la vue de confirmation d'inscription.
  * @param  String $infoAjoutee une information propre aux étudiants ou aux entreprises dans cette vue.
  */
  public function genereVueConfirmationInscription($infoAjoutee){
    $this->vue->genereVueConfirmationInscription($infoAjoutee);
  }

}
