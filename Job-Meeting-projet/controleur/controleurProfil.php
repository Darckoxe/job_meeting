<?php
require_once __DIR__."/../vue/vueProfil.php";

/**
* Contrôleur de la vue relative à l'affichage du profil d'un compte.
*/
class ControleurProfil{

  private $vue;

  /**
  * Constructeur de la classe permettant d'initialiser la vue du profil.
  */
  public function __construct(){
    $this->vue=new VueProfil();
  }

  /**
  * Fonction qui demandera à VueMenu de générer une vue correspondant au choix du menu selon le type de connexion.
  * @param  String      $type   le type de connexion.
  * @param  Utilisateur $profil le profil à afficher.
  */
  public function afficherProfil($type,$profil) {
    if (isset($profil[0]))
    $this->vue->afficherProfil($type,$profil[0]);
    else
    header('Location:index.php');
  }

}
