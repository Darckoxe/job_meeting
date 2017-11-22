<?php

require 'modele/algo.php';
require 'modele/dao/dao.php';

$dao=new dao();

$dao -> supprimerCreneau();
$dao -> connexion();

$arrayNbCreneaux = $dao -> getNbCreneaux();
$creneauMatin = $arrayNbCreneaux[0];
$creneauAprem = $arrayNbCreneaux[1];
$nbCreneaux = $creneauMatin + $creneauAprem;


$Etudiants = array();
$Choix = array();
$Entreprises = array();
$Creneaux = array();
$LiensEntrCren = array();
$Formations = array();


$listeEtu=$dao->getEtudiants("Informatique");

//On s'occupe de $Etudiants
$cmp = 0;
foreach ($listeEtu as $etu){
  $Etudiants[$cmp+1] = $etu["IDEtu"];
  $Choix[] = explode ( "," , $etu["listeChoixEtu"]);
  $cmp++;
}

//On s'occupe de $Entreprises, $Creneaux et $LiensEntrCren
$listeEnt = $dao -> getEntreprises();
foreach ($listeEnt as $ent){
  $Entreprises[] = $ent["IDEnt"];
}
$listeFormation = $dao -> getFormations("Informatique");

/*for ($i = 0; $i < sizeof($Entreprises); $i++) {
  $LiensEntrCren[$i][0] = 0;
}*/

foreach ($Entreprises as $IDent) {
  $LiensEntrCren[$IDent][0] = 0;
}

$cmp = 0;
foreach ($listeFormation as $form){
  $Formations[] = $form["IDformation"];

  $LiensEntrCren[$form["entPropose"]][0]++;
  $LiensEntrCren[$form["entPropose"]][$LiensEntrCren[$form["entPropose"]][0]] = $cmp;

  $tmp = array();
  switch ($form["disponibilite"]) {
    case 'matin':
      for ($i = 0; $i < $creneauMatin; $i++) {
        $tmp[] = 1;
      }
      for ($i = 0; $i < $creneauAprem; $i++) {
        $tmp[] = 0;
      }
      break;

    case 'apres_midi':
      for ($i = 0; $i < $creneauMatin; $i++) {
        $tmp[] = 0;
      }
      for ($i = 0; $i < $creneauAprem; $i++) {
        $tmp[] = 1;
      }
      break;

    //Journee
    default:
      for ($i = 0; $i < $nbCreneaux; $i++) {
        $tmp[] = 1;
      }
      break;
  }
  $Creneaux[] = $tmp;
  $cmp++;
}


$jobMeeting = new jobMeeting($Etudiants, $Choix, $Entreprises, $Creneaux, $LiensEntrCren, $Formations,  $nbCreneaux);
$jobMeeting -> appli();

?>
