<?php
include_once __DIR__.'/../modele/dao/dao.php';

class controleurFormation{

  private $dao;

  function __construct(){

    $this->dao = new Dao();
  }

  function liste_etudiant_formation($formation){
    $etudiants = $this->dao->getEtudiants($formation);
    echo json_encode($etudiants);
  }

  function liste_entreprise_formation($formation){
    $entreprises = $this->dao->getEntreprisesParFormation($formation);
    $liste_entreprises = array();
    foreach($entreprises as $entreprise){
      array_push($liste_entreprises, array(
        "Identreprise" => $entreprise[0],
        "NomEntreprise" => $this->dao->getNomEntreprise($entreprise[0])
      ));
    }
    echo json_encode($liste_entreprises);
  }

  function liste_etudiant_entreprise($entreprise, $formation){
    $etudiants = $this->dao->getEtudiantpourEntreprise($formation, $entreprise);
    $liste_etudiants = array();
    foreach ($etudiants as $etudiant) {
      array_push($liste_etudiants, array(
        "Idetudiant" => $etudiant[1],
        "NomEtudiant" => $etudiant[0]
      ));
    }
    echo json_encode($liste_etudiants);
  }

  function liste_etudiant_creneau($numcreneau){
    $etudiants = $this->dao->getEtudiantCreneau($numcreneau);
    $liste_etudiants = array();
    foreach ($etudiants as $etudiant) {
      array_push($liste_etudiants, array(
        "Idetudiant" => $etudiant[1],
        "NomEtudiant" => $etudiant[0]
      ));
    }
    echo json_encode($liste_etudiants);
  }

  function supprimerEtu($numcreneau, $idetudiant){
	  $this->dao->supprimerEtuCreneau($numcreneau, $idetudiant);
  }

}
?>
