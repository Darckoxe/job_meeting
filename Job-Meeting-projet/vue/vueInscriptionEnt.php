<?php

require_once(__DIR__."/../modele/dao/dao.php");
require_once 'util/utilitairePageHtml.php';
require_once(__DIR__."/../modele/bean/Formation.php");

/**
 * Classe permettant de générer la vue d'inscription pour une entreprise.
 */
class VueInscriptionEnt{


	/**
	 * Fonction permettant d'afficher le formulaire d'inscription d'une entreprise.
	 */
	public function afficherFormulaireEnt(){
		$dao = new Dao();
		$tableauConfig = $dao->getConfiguration();
		$dateEvenementTmp = explode("-",$tableauConfig['dateEvenement']);
		$dateEvenement = implode("/",array_reverse($dateEvenementTmp));

		$dateFinInscriptionEntTmp = explode("-",$tableauConfig['dateFinInscriptionEnt']);
		$dateFinInscriptionEnt = implode("/",array_reverse($dateFinInscriptionEntTmp));

		$util = new UtilitairePageHtml();
		echo $util->genereBandeauAvantConnexion();
		?>
		<div id="corps">
			<div>
				<h1> Rencontres Alternance </h1>
				<span> Le <?=$dateEvenement?>, l'IUT de Nantes vous propose de rencontrer nos futurs étudiants en alternance.
					Si vous êtes intéressés, nous organiserons les entretiens sur inscription : vous avez donc l'assurance de rencontrer des candidats motivés et correspondant au profil que vous recherchez.
					<br><br/>
					<b>Attention, aucune modification ne pourra être apportée après le <?=$dateFinInscriptionEnt?>.</b>
				</span>
				<p name="obligatoire">
					Tous les champs suivis d'un * sont obligatoires
				</p>

				<form name ="inscriptionEnt" action="index.php" method="post" class="form_inscription" enctype="multipart/form-data" onSubmit="return VerifSubmit();">
					<!-- Participation-->
					<input type="checkbox" name="engagement" id = "engagement" required/><label for = "engagement">Vous souhaitez participer à cette journée de "Rencontres Alternance". <span name="obligatoire">*</span></label>
					<br/>
					<h2>Votre société</h2>

					<!-- Nom -->
					<label for="nomSociete"> Nom de la société représentée : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" id = "nomSociete" name="nomSociete" onblur="verifString(this, 'messageNom', '30')" required/>
					<p id="messageNom" style="color:red"></p>

					<!-- Champ de dépot d'offre -->
					<label for="offre_txt"> Description de votre offre emploi : (vous pouvez également déposez une offre .pdf plus bas) </label> <br />
					<textarea name="offre_txt" rows="8" cols="80"></textarea>

					<br/>

					<h2>Vous recherchez :</h2>

					<!-- Formation -->
					<label> Quelle(s) formation(s) vous intéresse(nt) ? <span name="obligatoire">*</span></label> <br/>
					<label> Vous pouvez déposer une offre .pdf (1Mo max) pour chaque formation </label> <br /> <br/>
					<label> <b> <font color=red> ATTENTION, une fois enregistré, vous ne pourrez modifier uniquement les fichiers pdf envoyés lors de cette inscription.
					Dans le cas où vous souhaiteriez mettre une nouvelle offre plus tard, veuillez envoyer un fichier pdf vide pour pouvoir le modifier ultérieurement </font> </b> </label>
					<br/><br/>
					<?php
						$compteur = 0;
						$listeFormations = $dao->getListeFormations();
						$listeDepartements = array();
						foreach ($listeFormations as $formation) {
							if(!in_array($formation->getDepartement(), $listeDepartements)) {
								array_push($listeDepartements, $formation->getDepartement());
							}
						}
						foreach ($listeDepartements as $departement) {
							echo ' <span><b>Département '.$departement.' :</b></span><br/>';
							foreach ($listeFormations as $formation) {
								if($formation->getDepartement() == $departement) {
									$name="offre_";
									$name.=$formation->getInitiales();
									echo $name;
									echo '
									<input type="checkbox" name="formation['.$compteur.']" value="'.$formation->getInitiales().'" onClick="EnableSubmit(this)">
									<a id="lienFormation" href="'. $formation->getLien() .'" target="_blank">'.
										$formation->getDescription().'
									</a>
									<br />
									<input type="file" name="'.$name.'">
									<br/> <br />';
									$compteur = $compteur + 1;
								}
							}
						}

					?>
					<br/>

					<span name="information">Pour plus d'informations sur nos formations : <a href="http://www.iutnantes.univ-nantes.fr/20796624/0/fiche___pagelibre/&RH=1183111171330&RF=1183119182323" target="_blank">www.univ-nantes.fr/iutnantes</a> </span>
					<br/><br/>
					<b>Emplacement de l'entreprise :</b>
					<br/><br/>
					<!-- Ville -->
					<label for="ville"> Ville : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" name="ville" id="ville" onblur="verifString(this, 'messageVille', '30')" required/>
					<p id="messageVille" style="color:red"></p>
					<!-- Code Postal -->
					<label for="cp"> Code Postal : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" maxlength="5" name="codePostal" id="cp" onblur="verifCodePostal(this,'messageCP')" required autocomplete="off"/>
					<p id="messageCP" style="color:red"></p>
					<!-- Adresse -->
					<label for="adresse"> Adresse : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" id="adresse" name="adresse" onblur="verifString(this, 'messageAdresse', '30')" required/>
					<p id="messageAdresse" style="color:red"></p>

					<!-- Nombre alternant -->
					<label for="NbAlternants"> Pouvez-vous indiquer le nombre d'alternants (pour chaque formation) que vous envisagez de recruter ?</label>
					<br/>
					<input type="number" name="NbAlternants" id = "NbAlternants" value="1" min="1" max="10"/>
					<br/><br/>

					<!-- Disponibilité-->
					<label for="disponibilite"> Veuillez indiquer vos disponibilités : <span name="obligatoire">*</span></label>
					<br/>
					<select name="disponibilite" id = "disponibilite" required>
						<option value="apres_midi">Après-midi</option>
					</select>
					<br/><br/>
					<!-- Nombre de recruteurs -->
					<label for="NbRecruteurs"> Combien de recruteurs serez-vous ? <span name="obligatoire">*</span></label><br/>

					<input type="number" name="NbRecruteurs" id="NbRecruteurs" value="1" min="1" max="20" onchange="activerNbSession()" required/>
					<br/><br/>
					<!-- Nombre stand -->
					<span id="nb_stand"  style="visibility:hidden; display: none;">
						<label for="NbStand"> Si vous êtes plusieurs recruteurs, combien de sessions de recrutements souhaitez-vous organiser en parallèle ? <span name="obligatoire">*</span></label><br/>
						<input type="number" name="NbStand" id="NbStand" value="1" min="1" max="10"  required/>
						<br/><br/>
					</span>


					<h2>Personne à contacter :</h2>
					<span> Pour la mise en place de cette journée. </span>
					<br/><br/>
					<!-- Nom -->
					<label for="nom"> Nom : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" id="nom" name="nom" onblur="verifString(this, 'messageNomContact', '20')" required/>
					<p id="messageNomContact" style="color:red"></p>
					<!-- Prenom-->
					<label for="prenom"> Prénom : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" id="prenom" name="prenom" onblur="verifString(this, 'messagePrenomContact', '20')" required/>
					<p id="messagePrenomContact" style="color:red"></p>
					<!-- Telephone -->
					<label for="tel"> Numéro de téléphone (sans espace. Ex : 0610203040) : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" maxlength="10" id="tel" name="tel" onblur="verifTelephone(this, 'messageTel')" required/>
					<p id="messageTel" style="color:red"></p>
					<!--  Adresse email-->
					<label for="mail"> Email (il sera utilisé pour l'authentification sur le site) : <span name="obligatoire">*</span></label>
					<br/>
					<input type="text" name="email" id="mail" onblur="verifEmail(this, 'messageEmail')" required/>
					<p id="messageEmail" style="color:red"></p>
					<!--  Mdp -->
					<label for="passw"> Mot de passe (il sera utilisé pour l'authentification sur le site) : <span name="obligatoire">*</span></label>
					<br/>
					<input type="password" name="password" id="passw" required/>
					<br/><br/>
					<!--  Mdp bis-->
					<label for="passwBis"> Veuillez réécrire le mot de passe : <span name="obligatoire">*</span></label>
					<br/>
					<input type="password" name="passwordBis" id="passwBis" onblur="verifMdp('messageMdp')" required/>
					<p id="messageMdp" style="color:red"></p>
					<br/><br/>
					<input type="hidden" name="inscription" value="entreprise"/>
					<input type="submit" name="valid_inscription_ent" id="submit"/>
				</form>
				<div id="error_fail"><?php
				if(isset($_SESSION['fail'])){
					echo $_SESSION['fail'];
					unset($_SESSION['fail']);
				}
				?>
				</div>
				<!--Les scripts pour vérifier chaque case-->
				<script>
				//On surligne les cases non valides
				function surligne(champ, erreur) {
					if(erreur)
					champ.style.backgroundColor = "#fba";
					else
					champ.style.backgroundColor = "";
				}

				function verifString(champ, txt, longMax) {
					if(champ.value.length > longMax) {
						surligne(champ, true);
						document.getElementById(txt).innerHTML = longMax + " caractères maximum autorisés";
						champ.value = "";
						return true;
					} else {
						surligne(champ, false);
						document.getElementById(txt).innerHTML = "";
						return false;
					}
				}

				function verifCodePostal(champ, txt) {
					if(champ.value.length != 5 || !/^\d+$/.test(champ.value)) {
						surligne(champ, true);
						document.getElementById(txt).innerHTML = "Le code postal doit être rentré au format XXXXX avec des chiffres";
						champ.value = "";
						return true;
					} else {
						surligne(champ, false);
						document.getElementById(txt).innerHTML = "";
						return false;
					}
				}

				function verifTelephone(champ, txt) {
					if(champ.value.length != 10 || !/^\d+$/.test(champ.value)) {
						surligne(champ, true);
						document.getElementById(txt).innerHTML = "Format invalide";
						champ.value = "";
						return true;
					} else {
						surligne(champ, false);
						document.getElementById(txt).innerHTML = "";
						return false;
					}
				}


				function verifEmail(champ, txt){
					var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
					if(!reg.test(champ.value)) {
						surligne(champ, true);
						document.getElementById(txt).innerHTML = "L'e-mail n'est pas valide.";
						champ.value = "";
						return true;
					} else {
						surligne(champ, false);
						document.getElementById(txt).innerHTML = "";
						return false;
					}
				}

				function verifMdp(txt){
					var passw = document.getElementById("passw");
					var passwBis = document.getElementById("passwBis");
					if (passw.value != passwBis.value) {
						surligne(passw, true);
						surligne(passwBis, true);
						passw.value = "";
						passwBis.value = "";
						document.getElementById(txt).innerHTML = "Les 2 valeurs sont différentes";
						return true;
					} else if (passw.value.length > 20 || passw.value.length < 6) {
						surligne(passw, true);
						surligne(passwBis, true);
						passw.value = "";
						passwBis.value = "";
						document.getElementById(txt).innerHTML = "Le mot de passe doit faire 6 à 20 caractères";
						return true;
					} else {
						surligne(passw, false);
						surligne(passwBis, false);
						document.getElementById(txt).innerHTML = "";
						return false;
					}
				}

				/*Fonction qui fait apparaitre le label et le champ correspondant pour indiquer le nombre de repas
				* initialise la valeur de départ à 1*/
				function activer(){
					check = document.getElementById("checkbox_repas").checked;
					if (check == true) {
						document.getElementById("labrepas").style.visibility = "visible";
						document.getElementById("nb_repas").style.display = "block";
						document.getElementById("nb_repas").value = 1;
					}
					else {
						document.getElementById("labrepas").style.visibility = "hidden";
						document.getElementById("nb_repas").style.display = "none";
						document.getElementById("messageNbRepas").innerHTML = "";
					}
				}

				/*Fonction qui fait apparaitre le label et le champ correspondant pour indiquer le nombre de sessions en parallèle
				* initialise la valeur de départ à 1*/
				function activerNbSession(){
					value = document.getElementById("NbRecruteurs").value;
					if (value > 1) {
						document.getElementById("nb_stand").style.visibility = "visible";
						document.getElementById("nb_stand").style.display = "block";
						document.getElementById("nb_stand").value = 1;
					}
					else {
						document.getElementById("nb_stand").style.visibility = "hidden";
						document.getElementById("nb_stand").style.display = "none";
						document.getElementById("NbStand").value = 1;
					}
				}

				/*Fonction qui vérifie que le nombre de repas et bien conforme lorsque la case est cochée
				* Si elle n'est pas cochée la valeur est à 0*/
				function verifNbRepas(champ, txt, longMax) {
					if (document.getElementById("checkbox_repas").checked == true){
						if (champ.value > 0 && champ.value < parseFloat(longMax) + 1) {
							surligne(champ, false);
							document.getElementById(txt).innerHTML = "";
						}
						else {
							surligne(champ, true);
							champ.value = "";
							document.getElementById(txt).innerHTML = "Le nombre doit être compris en 1 et 10";
						}
					}
					else {
						champ.value = 0;
						surligne(champ, false);
						document.getElementById(txt).innerHTML = "";
					}
				}


				</script>


				<script type="text/javascript">
				EnableSubmit = function(val)
				{
					var sbmt = document.getElementById("submit");

					if (val.checked == true)
					{
						sbmt.disabled = false;
					}
					else
					{
						sbmt.disabled = true;
					}
				}
				</script>
				<script>
				VerifSubmit = function()
				{
					html = html.replace(/</g, "&lt;").replace(/>/g, "&gt;");
					var nb_repas = document.getElementById("nb_repas");
					var checkboxRepas = document.getElementById("checkbox_repas");
					var passw = document.getElementById("passw");
					var passwBis = document.getElementById("passwBis");
					if (checkboxRepas.checked == true) {
						if (nb_repas.value == '' || nb_repas.value == null)
						{
							alert('Vous n\'avez pas précisé combien de repas seront à prévoir.');
							return false;
						}
					}
					if (passw.value != passwBis.value) {
						alert('Les mots de passe ne coïncident pas.');
						return false;
					}
					if (/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i.test(document.getElementById("mail").value))
					{
						return true;
					}
					else {
						alert("L\'adresse email n'est pas correcte !")  ;
						return false;
					}
				}
				</script>

				<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
				<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js" ></script>
				<script src="vue/js/script.js"></script>

			</div>
			<div style="text-align: center;">
				<a href="index.php">Retour à la page d'accueil</a>
			</div>
		</div>

		<?php

		echo $util->generePied();

	}
}
?>
