<?php
require_once __DIR__."/../vue/vueAuthentification.php";

/**
* Contrôleur de l'authentification d'un utilisateur.
*/
class ControleurAuthentification{

  private $vue;

  /**
  * Constructeur de la classe initialisant la vue de la page d'authentification.
  */
  public function __construct(){
    $this->vue=new VueAuthentification();
  }

  /**
  * Fonction permettant l'affichage de la vue d'authentification.
  */
  public function authentification(){
    $arg = null;
    if (isset($_SESSION['fail'])) {
      $arg = $_SESSION['fail'];
      unset($_SESSION['fail']);
    }
    $this->vue->genereVueAuthentification($arg);
  }

}
