<?php

require 'modele/formation.php';
require_once 'modele/dao/daoAlgo.php';

$dao=new daoAlgo();
$dao->connexion();
$listeEnt=$dao->getEntreprises();

$dao->deconnexion();
$array = array();

foreach ($listeEnt as $entr) {
  $formation = new formation($entr["IDEnt"], $entr["formationsRecherchees"], $entr["nbPlaces"], $entr["typeCreneau"]);
  $array[] = $formation -> createForm();

}
print_r($array);

?>
