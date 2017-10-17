<?php


require_once 'util/utilitairePageHtml.php';

/**
 * Classe permettant l'affichage de la vue d'oubli de mot de passe.
 */
class VueOubliMdp{


	/**
	 * Fonction permettant de générer la vue d'oubli de mot de passe.
	 */
	public function genereVueOubliMdp(){

		$util = new UtilitairePageHtml();
		echo $util->genereBandeauAvantConnexion();
		$util->genereEnteteHtml();
		?>

		<div id="login">
			<div id="mail_new_mdp">

				<script>
				function checkMail(element) {
					if (/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i.test(element).value)
					{
						element.style.borderColor = "none";
					}
					else {
						element.style.borderColor = "grey";
					}
				}
				VerifSubmit = function()
				{
					html = html.replace(/</g, "&lt;").replace(/>/g, "&gt;");
					if (/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i.test(document.getElementById("mail").value))
					{
						alert("L\'adresse email n\'est pas correcte !");
						return true;
					}
					else {
						alert("L\'adresse email n\'est pas correcte !");
						return false;
					}
				}
				</script>


				<h1>Réinitialisation du mot de passe</h1>
				<br />


				<form method="POST" action="index.php" onsubmit="return VerifSubmit()">

					<label for = "mail">E-mail utilisé : </label><br />
					<input type="email" name="mail_new_mdp" id="mail" onchange="checkMail(this);" required/><br/><br />

					<label>Profil de compte : </label><br /><br />

					<input type="radio" name="profil_new_mdp" id = "profil_new_mdp_ent" value="entreprise">
					<label for = "profil_new_mdp_ent" class = "labelProfilOubliMdp" >Entreprise</label>

					<input type="radio" name="profil_new_mdp" id = "profil_new_mdp_etu" value="etudiant">
					<label for = "profil_new_mdp_etu" class = "labelProfilOubliMdp" >Etudiant</label>
					<br /><br />
					<p>Un nouveau mot de passe vous sera envoyé à cette adresse.</p>
					<!-- erreur d'email-->
					<div id="error_fail"><?php
					if(isset($_SESSION['fail'])){
						echo $_SESSION['fail'];
						unset($_SESSION['fail']);
					}
					?></div>

					<input type="submit" name="submit_new_mdp" value="Demander un nouveau MDP">

				</form>
				<table style="width: 70%; margin: auto; text-align: center;">
					<tr>
						<td><a href="index.php">Retour à la page de connexion</a></td>
					</tr>
				</table>
				<br>
			</div>
		</div>
		<br>
		<?php

		echo $util->generePied();

	}
}
?>
