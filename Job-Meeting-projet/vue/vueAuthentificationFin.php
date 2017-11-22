<?php

require_once __DIR__."/../modele/dao/dao.php";
require_once 'util/utilitairePageHtml.php';

/**
 * Classe permettant de gérer la vue d'authenfication du site.
 */
class VueAuthentification{



/**
 * Fonction permettant de générer la vue d'authenfication du site.
 * @param  String  $erreur le message à afficher en cas d'erreur.
 */
public function genereVueAuthentification(){
	$util = new UtilitairePageHtml();
	echo $util->genereBandeauAvantConnexion();
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="vue/css/general.css">
	<title></title>
	<meta charset="UTF-8">
</head>
<body>
	<div id="login">
     <br/><br/><br/><br/>

			<p> <span style="font-size:16px; color: #8A2908; font-weight: bold;">Entreprises, étudiants, nous vous proposons d'échanger à l'occasion de
cette journée "Rencontres Alternance".</span> <!-- police sup, couleur #8A2908, gras-->
			<br/><br/>
					<?php
					$dao = new Dao();
					$dateNow = new DateTime("now");
		      $tabConfig = $dao->getConfiguration();
					$textDateDebutEtu = explode("-",$tabConfig['dateDebutInscriptionEtu']);
					$textDateDebutEnt = explode("-",$tabConfig['dateDebutInscriptionEnt']);
		      $dateDebutEnt = new DateTime((string)$tabConfig['dateDebutInscriptionEnt']);
		      $dateLimitEnt = new DateTime($tabConfig['dateFinInscriptionEnt']);
		      $dateDebutEtu = new DateTime((string)$tabConfig['dateDebutInscriptionEtu']);
		      $dateLimitEtu = new DateTime((string)$tabConfig['dateFinInscription']);

						if ($dateNow >= $dateLimitEtu){
							echo '<span style="opacity: 0.5">Inscription étudiant terminée</span>';
						}
						elseif ($dateNow < $dateDebutEtu) {
							echo '<span style="opacity: 0.5">Inscription étudiant à partir de '.$textDateDebutEtu[2].'/'.$textDateDebutEtu[1].'</span>';
						}
						else {
							echo '<b><span style="color: #8A2908; font-size: 12px;"> Venez échanger avec vos futurs employeurs !</span></b><br/><a href="index.php?inscriptionEtu=1">Inscription étudiant</a> '; // gras
						}
					?>
					<br/><br/>
					<?php
						if ($dateNow >= $dateLimitEnt) {
							echo '<span style="opacity: 0.5">Inscription entreprise terminée</span>';
						}
						elseif ($dateNow < $dateDebutEnt) {
							echo '<span style="opacity: 0.5">Inscription entreprise à partir de '.$textDateDebutEnt[2].'/'.$textDateDebutEnt[1].'</span>';
						}
						else {
							echo '<b><span style="color: #8A2908; font-size: 12px;"> Venez à la rencontre de vos futurs collaborateurs en alternance !</span></b><br/><a href="index.php?inscriptionEnt=1">Inscription entreprise</a> '; // gras
						}
					?>
			<span style="font-size: 14px;"><br/><br/><br/><!-- couleur #8A2908 -->
			Nous restons à votre disposition pour toutes informations
			complémentaires :
			<br/><br/>
			- Tifenn Corbel - Relations entreprises : 02 72 64 22 14 - 02 40 30 60
87 - 06 86 11 02 85
			<br/><br/>
			- Sylvie Gaborit -  Formation continue et apprentissage : 02 40 30 60 62
			- 02 28 09 22 09</span>
	</div>
	<br/><br/>

	<br/><br/>
	<?php

	echo $util->generePied();

	?>
</body>
</html>

<?php
}
}
?>
