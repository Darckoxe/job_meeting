<?php
require_once __DIR__."/../modele/dao/dao.php";

header('Content-Type: text/cs');
header('Content-Disposition: attachment; filename="Export EntResAffIns.csv"');
$dao = new Dao();
$listes=$dao->getEntResAffIns();
$att1="typeFormation";
$att2="Entreprise";$att3="Creneaux Reserves";
$att4=" Creneaux Affectes";
$att5=" Creneaux Inscrits";
$att6 = "Difference inscrits & attendus";


echo '"'.$att1.'"';
echo ';"'.$att2.'"';
echo ';"'.$att3.'"';
echo ';"'.$att4.'"';
echo ';"'.$att5.'"';
echo ';"'.$att6.'"';


echo "\n";

if (!empty($listes))  {
  foreach ($listes as $ent){

    $typeformation=$ent["typeFormation"];
    $nomEnt=$ent["nomEnt"];
    $CreneauRes=$ent["nbcreneauxReserves"];
    $CreneauAff=$ent["NBCreneauxAffectes"];
    $CreneauInscrit=$ent["nbEtudinantsInscrits"];
    $differenceInscrits_Reserves = $CreneauInscrit - $CreneauRes;

    echo '"'.$typeformation.'"';
    echo ';"'.$nomEnt.'"';
    echo ';"'.$CreneauRes.'"';
    echo ';"'.$CreneauAff.'"';
    echo ';"'.$CreneauInscrit.'"';
    echo ';"'.$differenceInscrits_Reserves.'"';


    echo "\n";
  }

}







?>
