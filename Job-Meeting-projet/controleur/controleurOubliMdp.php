<?php
require_once __DIR__."/../vue/vueOubliMdp.php";

/**
* Contrôleur de l'affichage de la vue relative à l'oubli de mot de passe.
*/
class ControleurOubliMdp{

  private $vue;
  private $dao;

  /**
  * Contrôleur de la classe qui permet d'initialiser la vue d'oubli de mot de passe et le DAO.
  */
  public function __construct(){
    $this->vue=new VueOubliMdp();
    $this->dao = new Dao();
  }

  /**
  * Fonction qui permettra d'afficher la page d'oubli de mot de passe.
  */
  public function aideOubliMdp(){
    $this->vue->genereVueOubliMdp();
  }

  /**
  * Fonction permettant de changer le mot de passe.
  * @param  String $mail désigne le mail du compte concerné par la modifications.
  * @param  String $profil désigne le statut de l'utilisateur : Entreprise ou Etudiant.
  * @param  String $new_mdp désigne le nouveau mot de passe.
  * @return boolean Retourne true si le changement s'est bien passé, false sinon.
  */
  public function editNewMdp($mail, $profil, $new_mdp){
    if(! $this->dao->PasswdEdit($mail,$profil,$new_mdp)){
      return false;
    }
    return true;
  }

}
