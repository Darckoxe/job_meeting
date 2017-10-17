<?php
require_once __DIR__."/../vue/vueLost.php";

/**
* Contrôleur de l'affichage de la vue générée en cas de page non trouvée.
*/
class ControleurLost{

  private $vue;

  /**
  * Constructeur de la classe initialisant la vue de page non trouvée.
  */
  public function __construct(){
    $this->vue=new VueLost();
  }

  /**
   * Fonction permettant d'afficher la vue de page non trouvée.
   */
  public function genererLost(){
    $this->vue->genereVueLost2();
  }

}
