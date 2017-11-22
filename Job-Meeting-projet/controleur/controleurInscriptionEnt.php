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



}
