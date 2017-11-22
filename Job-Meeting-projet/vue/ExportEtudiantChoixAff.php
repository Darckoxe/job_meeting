<?php
require_once __DIR__."/../modele/dao/dao.php";

header('Content-Type: text/cs');
header('Content-Disposition: attachment; filename="Export EtudiantChoixAff.csv"');
$dao = new Dao();

$listes=$dao->getEtudiantChoixAff();
$att1="Formation";
$att2="Etudiant";$att3="Créneaux Choisis";
$att4=" Créneaux Affectes";
$att5 = "Ecarts";
$att6=" Tel";
$att7="Mail";

echo '"'.$att1.'"';
echo ';"'.$att2.'"';
echo ';"'.$att3.'"';
echo ';"'.$att4.'"';
echo ';"'.$att5.'"';
echo ';"'.$att6.'"';
echo ';"'.$att7.'"';



echo "\n";

if (!empty($listes))  {
  foreach ($listes as $ent){

    $typeformation=$ent["formationEtu"];
    $nomEtu=$ent["nomEtu"];
    $NbChoix=$ent["NbChoix"];
    $Nbaffecte =$ent["Nbaffecte"];
    $numtelEtu=$ent["numtelEtu"];
    $mailEtu =$ent["mailEtu"];
    $ecart = $NbChoix - $Nbaffecte;


    echo '"'.$typeformation.'"';
    echo ';"'.$nomEtu.'"';
    echo ';"'.$NbChoix.'"';
    echo ';"'.$Nbaffecte.'"';
    echo ';"'.$ecart.'"';
    echo ';"'.$numtelEtu.'"';
    echo ';"'.$mailEtu.'"';



    echo "\n";
  }

}







?>
