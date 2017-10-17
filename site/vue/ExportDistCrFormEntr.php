<?php
require_once __DIR__."/../modele/dao/dao.php";

header('Content-Type: text/cs');
header('Content-Disposition: attachment; filename="Export DistributionFormEnt.csv"');
$dao = new Dao();

$listes=$dao->getExportDistCrFormEntr();
$att1="typeFormation";
$att2="Entreprise";$att3="Debut Creneau";$att4=" Fin Creneau";

echo '"'.$att1.'"';
echo ';"'.$att2.'"';
echo ';"'.$att3.'"';
echo ';"'.$att4.'"';

echo "\n";

if (!empty($listes))  {
  foreach ($listes as $ent){

    $typeformation=$ent["typeFormation"];
    $nomEnt=$ent["nomEnt"];
    $DebutCreneau=$ent["creneauDebut"];
    $FinCreneau=$ent["creneauFin"];


    echo '"'.$typeformation.'"';
    echo ';"'.$nomEnt.'"';
    echo ';"'.$DebutCreneau.'"';
    echo ';"'.$FinCreneau.'"';

    echo "\n";
  }

}







?>
