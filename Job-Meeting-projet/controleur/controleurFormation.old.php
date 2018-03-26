<?php
include_once __DIR__.'/../modele/dao/dao.php';

class controleurFormation{

  private $dao;

  function __construct(){

    $this->dao = new Dao();
  }

  function liste_etudiant($formation){
    $etudiants = $this->dao->getEtudiants($formation);
    echo json_encode($etudiants);
    //return $etudiants;
  }
}
?>
