<?php


require_once 'util/utilitairePageHtml.php';

/**
* Classe permettant de générer la vue de confirmation d'inscription.
*/
class VueConfirmationInscription{

	/**
	* Fonction qui permet de générer la vue de confirmation de l'inscription.
	* @param  String  $infoAjoutee l'information personnalisée selon que l'utilisateur soit un étudiant ou une entreprise.
	*/
	public function genereVueConfirmationInscription($infoAjoutee){
		if (isset($_SESSION['type_connexion'])) {
			$util = new UtilitairePageHtml();
			echo $util->genereBandeauApresConnexion();
		}
		else {
			$util = new UtilitairePageHtml();
			echo $util->genereBandeauAvantConnexion();
		}


		if (isset($_SESSION['type_connexion'])) {
			echo '<div id="main">';
		}
		else {
			echo '<div id="login">';
		}
		?>
		<br/><br/>
		<div id="Lost">

			Votre inscription a été enregistrée.<br/>
			Elle sera prochainement traitée par l'administrateur<br/>
			qui vous enverra un e-mail de confirmation.
			<?=$infoAjoutee?>

			<br/><br/><a href="index.php">Retour à l'accueil</a>
		</div>
	</div>
	<?php

	echo $util->generePied();

	?>
	<?php
	}
}
?>
