<?php

require_once __DIR__."/../modele/dao/dao_2016.php";
require_once __DIR__."/../modele/bean/Etudiant.php";
require_once __DIR__."/../modele/bean/Entreprise.php";
require_once __DIR__."/../modele/formationV2.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Export planning.csv"');

$dao = new Dao_2016();
$listeDepartement = $dao->getListeInitialesFormations();
$heures = $dao->getListeCreneaux();

$tabConfig = $dao->getConfiguration();
$pause_apres_midi = (new DateTime($tabConfig['heureCreneauPause']))->format("H:i");
$pause_matin = (new DateTime($tabConfig['heureCreneauPauseMatin']))->format("H:i");


$heureCreneauPause = new DateTime($tabConfig['heureCreneauPause']);
$heureCreneauPauseMatin = new DateTime($tabConfig['heureCreneauPauseMatin']);

$numCreneauPauseAprem = -1;
$numCreneauPauseMatin = -1;
$dureeCreneau = $tabConfig["dureeCreneau"];


$pauseMidi = $tabConfig["nbCreneauxMatin"];
if($pauseMidi == 0){
  $pauseMidi--;
}

$heureCreneauApresPause = $heureCreneauPause;
$heureCreneauApresPause->add(new DateInterval('PT'.$dureeCreneau.'M'));
$heureCreneauApresPauseMatin = $heureCreneauPauseMatin;
$heureCreneauApresPauseMatin->add(new DateInterval('PT'.$dureeCreneau.'M'));

echo ' ; ';
for($i = 0; $i < sizeof($heures); $i++)
{
  if ($i == $pauseMidi) {
    echo ';Pause-midi';
  }
  if ($heures[$i] == $pause_matin) {
    echo ';';
    $pause_matin = $i; # on stocke le numéro du créneau de pause du matin
  }
  if ($heures[$i] == $pause_apres_midi){
    echo ';';
    $pause_apres_midi = $i; # on stocke le numéro du créneau de pause de l'aprem
  }else{
    if($heures[$i] == $heureCreneauApresPause->format('H:i')){
      $numCreneauPauseAprem = $i;
    }
    if($heures[$i] == $heureCreneauApresPauseMatin->format('H:i')){
      $numCreneauPauseMatin = $i;
    }
    echo ';"'.$heures[$i].'"';
  }
}
echo "\n";


$tabEnt = $dao->getAllEntreprises();
$nbCreneaux = $tabConfig["nbCreneauxAprem"] + $tabConfig["nbCreneauxMatin"];

foreach ($tabEnt as $ent) {
  $tabForm = $dao -> getFormationsEntreprise($ent -> getID());
  foreach ($tabForm as $form) {
    echo '"'.$ent->getNomEnt().'"'.';"'.$form['typeFormation'].'"';

    for($i = 0; $i < $nbCreneaux; $i++) {
      if ($i == $pauseMidi) {
        echo ';';
      }
      if ($i == $pause_apres_midi){
        echo ';';
      }
      if ($i == $pause_matin){
        echo ';';
      }
      if ($numCreneauPauseAprem != -1 && $i >= $numCreneauPauseAprem) {
        echo ';"'.utf8_decode($dao -> getNomEtudiant($dao->getCreneau($i+1, $form['IDformation']))).'"';
      }
      if ($numCreneauPauseMatin != -1 && $i >= $numCreneauPauseMatin) {
        echo ';"'.utf8_decode($dao -> getNomEtudiant($dao->getCreneau($i+1, $form['IDformation']))).'"';
      }
      else {
        echo ';"'.utf8_decode($dao -> getNomEtudiant($dao->getCreneau($i, $form['IDformation']))).'"';
      }

    }
    echo "\n";
  }

}
?>
