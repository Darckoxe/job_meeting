<?php
require_once __DIR__."/../modele/dao/dao.php";

header('Content-Type: text/cs');
header('Content-Disposition: attachment; filename="Export CreneauxEntreprise.csv"');
$dao = new Dao();

$listes=$dao->getEntreprisesTotalCreneaux();
$att1="Entreprise";
$att2="NbTotalCreneaux";
echo '"'.$att1.'"';
echo ';"'.$att2.'"';

echo "\n";

if (!empty($listes))  {
  foreach ($listes as $ent){
    $nomEnt=$ent["nomEnt"];
    $NbCreneaux=$ent["nbCreneauxTotal"];


    echo '"'.$nomEnt.'"';
    echo ';"'.$NbCreneaux.'"';

    echo "\n";
  }
}
?>
