<?php
require_once __DIR__."/../modele/dao/dao.php";

/**
 * Classe permettant l'affichage des différentes pages de l'application liées aux mails.
 */
class VueMail
{
	/**
	* Fonction permettant d'afficher le formulaire d'envoi de mail.
	* @param  String  							 $nomMail           le nom du mail à afficher
	* @param  array(String,String)  $fichier           un tableau contenant le titre et le corps du fichier html.
	* @param  array(int,fichier)    $pjDefaut          un tableau composé de couples (numéro du fichier, fichier).
	* @param  array(String)         $listesDiffusion   un tableau composé des adresses mails des listes de diffusion.
	*/
	public function afficherFormulaireMail($nomMail, $fichier, $pjDefaut, $listesDiffusion)
	{
		$util = new UtilitairePageHtml();
		echo $util->genereBandeauApresConnexion();
		?>
		<script src="vue/ckeditor/ckeditor.js"></script>

		<?php
		$titre = $fichier[0];
		$contenu_du_fichier  = $fichier[1];
		?>

		<br /><br />
		<div id = "formulaireMail">
		<h1>Envoyer un mail</h1>

		<form method = "post" action = "index.php" name = "formulaireMail" enctype="multipart/form-data">

		<input type="hidden" name= "nomMail" value = "<?=$nomMail?>"/>
		<?php
		if($listesDiffusion != 0)
		{
			$this->affichageListesDiffusion($listesDiffusion);
		}
		?>
		<label for = "destinataireMail">Votre destinataire</label><br />
		<input id = "destinataireMail" type = "text" name = "destinataireMail" value = "<?php if (isset($_SESSION['mails'])){foreach ($_SESSION['mails'] as $value) {$tab = explode("+",$value); echo($tab[0].";");}}?>"><br /><br />
		<label for = "objetMail">Objet du mail</label><br />
		<input id = "objetMail" type = "text" name = "objetMail" value = "<?=$titre?>"><br /><br />
		<textarea name = "corpsMail" rows = "20" cols = "50" class = "ckeditor">
		<?php
		echo $contenu_du_fichier."\n"; ?>
		</textarea>
		<p>
		Fichiers par défaut à envoyer en pièces jointes <a href="index.php?modifPjDefaut=<?=$nomMail?>">(cliquez-ici pour ajouter /
		supprimer les pièces jointes définies par défaut)</a>
		</p>
		<ul>
		<?php
		foreach($pjDefaut as $pjointe)
		{
			?>
			<li><?=$pjointe[1]?></li>

			<?php
		}
		echo "\n";
		?>
		</ul>

		<label for = "envoyerPlanning">Envoyer le planning au destinataire</label>
		<input type = "checkbox" name = "envoyerPlanning" id = "envoyerPlanning"><br /><br />


		<label for="mon_fichier1">Ajout de fichiers en pièces jointes du message :</label><br />
		<input type="file" name="mon_fichier1" id="mon_fichier1" onclick="value=''" />
		<input type="file" name="mon_fichier2" id="mon_fichier2" onclick="value=''" />
		<input type="file" name="mon_fichier3" id="mon_fichier3" onclick="value=''" /><br />
		<input type="submit" value="Enregistrer modèle" name = "modifierMail">
		<input type = "submit" name = "envoyerMail" value = "Envoyer le mail"
		onClick= "if (confirm('Voulez vous vraiment envoyer le mail ? ')){
		return true;
		} else {
		return false;
		} ">
		</form>
		</div>
		</body>
		</html>
		<?php
		echo $util->generePied();
	}

	/**
	 * Fonction permettant de générer le mail d'oubli de mot de passe.
	 * @param  String $new_mdp le nouveau mot de passe.
	 * @return String le message d'oubli de mot de passe.
	 */
	public function mailOubliMdp($new_mdp){
		$message = "
		<p style=\"width: 700px;margin: auto;font-family: Georgia, sans-serif;text-align: justify;\">
		Bonjour,<br />
		Voici le nouveau mot de passe que vous avez demandé à réinitialiser :<br/>
		-----------------------<br />
		$new_mdp<br/>
		-----------------------<br/>
		Ce mot de passe est temporaire, veuillez le modifier manuellement en vous connectant au site JobMeeting de l'IUT de Nantes.<br/><br/>
		Si vous n'avez pas fait cette demande, veuillez en informer l'administrateur.
		Cordialement.<br/>
		";

		return $message;
	}

	/**
	 * Fonction permettant d'afficher la page de modification des pièces jointes par défaut d'un mail spécifié.
	 * @param  String 						$nomMail  le nom du mail concerné.
	 * @param  array(int,fichier) $tabPj un tableau composé de couples (numéro du fichier, fichier).
	 */
	public function affichageModifPj($nomMail,$tabPj){
		$util = new UtilitairePageHtml();
		echo $util->genereBandeauApresConnexion();
		?>
		<br /><br /><br />
		<form method = "post" action = "index.php?nomMail=<?=$nomMail?>" name = "formulaireMail" enctype="multipart/form-data">
		<?php
		if(count ($tabPj) != 0){
		?>
		  <p>Sélectionnez les pièces jointes que vous voulez supprimer : </p>
		<?php
		}
		?>

		<ul>
		<?php
		foreach($tabPj as $pjointe)
		{
			?>
			<li>
			<label for = "<?=$pjointe[1]?>"><?=$pjointe[1]?></label>
			<input type = "checkbox" name = "piecejointesuppr<?=$pjointe[0]?>" id = "<?=$pjointe[1]?>" value = "1">
			</li>
			<?php
		}
		?>
		</ul>
		<p>Ajoutez un fichier aux pièces jointes par défaut de ce mail</p>
		<input type="file" name="ma_nouvelle_pj" id="ma_nouvelle_pj" onclick="value=''" />
		<br /><br />
		<input type = "hidden" value = "<?=$nomMail?>" name = "nomMailModif"/>
		<input type = "submit" value = "Mettre à jour les pièces jointes" name="modifPjDefaut">
		</form>
		<?php
	}
	public function affichageListesDiffusion($tab)
	{
		?>
		<script type="text/javascript">

		function CocheTout(ref)
		{
			var form = ref;

			var addToRecipientTab = [];

			while (form.parentNode && form.nodeName.toLowerCase() != 'form')
			{
				form = form.parentNode;
			}

			var elements = form.getElementsByTagName('input');

			for (var i = 0; i < elements.length; i++)
			{
				if (elements[i].type == 'checkbox' && elements[i].className == 'addrDiff')
				{
					elements[i].checked = ref.checked;
				}
			}
		</script>
		<div class="listesDiff">
			<ul style="list-style-type: none;">
				<input onclick="CocheTout(this)" id="listesDiff" type="checkbox" name="oneCheckboxToSelectThemAll"><b>Sélectionner tout</b>
			<?php
			$increment = 0;
			foreach ($tab as $value)
			{
				?><li><input type="checkbox" name="addrDiff<?=$increment?>" class="addrDiff" value="<?=$value?>"><?=$value?></li><?php
				$increment++;
			}
			?>
			</ul>
		</div>
		<?php
	}
}
?>
