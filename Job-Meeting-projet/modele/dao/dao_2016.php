<?php

require_once("ConnexionException.php");
require_once("AccesTableException.php");
require_once(__DIR__."/../formationV2.php");
require_once("dao.php");

/* Dans un path, utiliser '\..'' remonte d'un dossier. Sous windows
*/
class Dao_2016 extends DAO
{
	/**
	* Méthode qui permet de récuppérer les mails de tous les étudiants inscrits dont le compte a été validé
	*
	* @return    array liste des mails des étudiants
	*/
	public function getAllStudentsMail()
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT mailEtu FROM etudiant ;') ;
			$statement->execute() ;
			$tabAllEtuMails = $statement->fetchAll() ;
			$result = array() ;
			foreach ($tabAllEtuMails as $value)
			{
				array_push($result, $value['mailEtu']) ;
			}
			return $result ;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	public function getListesDiffusion()
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT mailDiff FROM listesDiffusion') ;
			$statement->execute() ;
			$tabListesDiffusion = $statement->fetchAll() ;
			$result = array() ;
			foreach ($tabListesDiffusion as $value)
			{
				array_push($result, $value['mailDiff']) ;
			}
			return $result ;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	/**
	* Méthode qui permet de réccupérer les mails des étudiants par formations
	*
	* @param $formation String formation concerné
	*/
	public function getEducationStudentsMail($formation)
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT mailEtu FROM etudiant WHERE formationEtu=?;') ;
			$statement->bindParam(1, $formation) ;
			$statement->execute() ;
			$tabAllMails = $statement->fetchAll() ;
			$result = array();
			foreach ($tabAllMails as $value) {
				array_push($result, $value['mailEtu']);
			}
			return $result;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	/**
	* Méthode qui permet de récupérer les mails de toutes les entreprises
	*
	* @return array liste des mails des entreprises
	*/
	public function getAllCompaniesMail()
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT mailEnt FROM entreprise ;') ;
			$statement->execute() ;
			$tabAllCompMail = $statement->fetchAll();
			$result = array();
			foreach ($tabAllCompMail as $value) {
				array_push($result, $value['mailEnt']);
			}
			return $result;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	/**
	* Méthode qui permet de récupérer les mail des étudiants n'ayant pas réalisé de choix
	*
	* @return array liste des étudiant n'ayant pas réalisé de choix
	*/
	public function getUndecidedStudents()
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT mailEtu FROM etudiant WHERE listeChoixEtu=?;') ;
			$param1="";
			$statement->bindParam(1, $param1) ;
			$statement->execute() ;
			$tabUndecidedStudents = $statement->fetchAll() ;
			$result = array();
			foreach ($tabUndecidedStudents as $value) {
				array_push($result, $value['mailEtu']);
			}
			return $result;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	/**
	* Méthode qui retourne la liste des numéros de créneux possible (global)
	*
	* @return    array tableau des horraire en humain (cad indéxé de 1 à taille du tableau et non de 0 à taille + 1)
	*/
	public function getNumCreneau() {
		try {
			$this->connexion();
			$statement = $this->connexion->prepare('SELECT `Num` FROM `heurecreneau`;');
			$statement->execute();
			$tabHeure = $statement->fetchAll();
			$result = array();
			foreach ($tabHeure as $value) {
				array_push($result, $value['Num']+1);
			}
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		return $result;
		$this->deconnexion();

	}

	/**
	* Méthode qui permet de modifier le créneau de début d'une entreprise
	*
	* @param $idEntre int numéros d'identifiant de l'entreprise
	* @param $numDeb int numéros du créneau de départ
	* @param $idFormation int id de la formation concérné
	* @return    void
	*/
	public function editCreneauDebutFormationEntreprise($idEntre, $numDebut, $idFormation) {
		try {

			$this->connexion();
			$requete='UPDATE `formation` SET `creneauDebut` = ?
			WHERE `formation`.`entPropose` = ?
			AND `formation`.`IDformation` = ? ;';

			$statement = $this->connexion->prepare($requete);
			$statement->bindParam(1, $numDebut);
			$statement->bindParam(2, $idEntre);
			$statement->bindParam(3, $idFormation);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}
	/**
	* Méthode qui permet de modifier le créneau de fin d'une entreprise
	*
	* @param $idEntre int numéros d'identifiant de l'entreprise
	* @param $numFin int numéros du créneau de fin
	* @param $idFormation string formation concérné
	* @return    void
	*/
	public function editCreneauFinFormationEntreprise($idEntre, $numFin, $idFormation) {
		try {
			$this->connexion();
			$requete='UPDATE `formation` SET `creneauFin` = ?
			WHERE `formation`.`entPropose` = ?
			AND `formation`.`IDformation` = ? ;';

			$statement = $this->connexion->prepare($requete);
			$statement->bindParam(1, $numFin);
			$statement->bindParam(2, $idEntre);
			$statement->bindParam(3, $idFormation);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Méthode qui permet de modifier le créneau de début et de fin d'une entreprise
	*
	* @param $idEntre int numéros d'identifiant de l'entreprise
	* @param $numDebut int numéros du créneau de début
	* @param $numFin int numéros du créneau de fin
	* @param $idFormation string formation concérné
	* @return    void
	*/
	public function editCreneauDebutEtFinFormationEntreprise($idEntre, $numDebut, $numFin, $idFormation)
	{
		$this->editCreneauDebutFormationEntreprise($idEntre, $numDebut, $idFormation);
		$this->editCreneauFinFormationEntreprise($idEntre, $numFin, $idFormation);
	}

	/**
	* Méthode qui permet de récupérer l'heure correspondant au numéros du créneau
	*
	* @param $num string numéros du créneau
	* @return string heure du créneau
	*/
	public function getHeureNum($num) {
		try {
			$this->connexion();
			$requete = 'SELECT `heure` from `heurecreneau` WHERE `Num` = ? ;';
			$statement = $this->connexion->prepare($requete);
			$statement->bindParam(1, $num);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		return $statement->fetch();
		$this->deconnexion();
	}

	/**
	* Méthode qui permet de savoir si le compte de l'identifiant entré est enregistré en temporaire ou s'il n'est pas enregistré
	*
	* @param $identifiant l'identifiant de l'utilisateur concerné
	* @return true si le compte est enregistré en temporaire, false sinon
	*/
	public function aCompteTemporaire($identifiant){

		$nbComptesTemp = 0;
		$this->connexion();
		$statementEtu = $this->connexion->prepare('SELECT count(*) as nbEtu FROM temp_etudiant WHERE mailEtu=?;');
		$statementEtu->bindParam(1,$identifiant);
		$statementEtu->execute();
		$nbComptesTemp = $statementEtu->fetch()['nbEtu'];
		if( $nbComptesTemp != 0){
			$this->deconnexion();
			return true;
		}else{
			$statementEnt = $this->connexion->prepare('SELECT count(*) as nbEnt FROM temp_entreprise WHERE mailEnt=?;');
			$statementEnt->bindParam(1,$identifiant);
			$statementEnt->execute();
			$nbComptesTemp = $statementEnt->fetch()['nbEnt'];
			if( $nbComptesTemp != 0){
				$this->deconnexion();
				return true;
			}else{
				$this->deconnexion();
				return false;
			}
		}
	}

	/**
	* Fonction permettant d'éditer la date de l'évènement
	* @param $new la nouvelle date de l'évènement
	*/
	public function editDateEvenement($new) {
		try{
			$this->connexion();
			$statement = $this->connexion->prepare("UPDATE scriptconfig SET dateEvenement = ? ;");
			$statement->bindParam(1,$new);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Fonction permettant d'éditer le site de l'IUT concerné par l'évènement
	* @param $new le site concerné
	*/
	public function editSiteEvenement($new) {
		try{
			$this->connexion();
			$statement = $this->connexion->prepare("UPDATE scriptconfig SET siteEvenement = ? ;");
			$statement->bindParam(1,$new);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Fonction permettant d'éditer l'adresse de l'IUT où se déroule l'évènement
	* @param $new l'adresse de l'IUT
	*/
	public function editAdresseIUT($new) {
		try{
			$this->connexion();
			$statement = $this->connexion->prepare("UPDATE scriptconfig SET adresseIUT = ? ;");
			$statement->bindParam(1,$new);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Fonction permettant d'éditer l'adresse mail utilisée pour envoyer des messages depuis le site
	* @param $new la nouvelle adresse mail
	*/
	public function editMailAdministrateur($new) {
		try{
			$this->connexion();
			$statement = $this->connexion->prepare("UPDATE scriptconfig SET mailAdministrateur = ? ;");
			$statement->bindParam(1,$new);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Fonction permettant d'éditer le numéro de téléphone présent dans le pied de page du site
	* @param $new le nouveau numéro attribué
	*/
	public function editTelAdministrateur($new) {
		try{
			$this->connexion();
			$statement = $this->connexion->prepare("UPDATE scriptconfig SET telAdministrateur = ? ;");
			$statement->bindParam(1,$new);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		} catch (PDOException $e){
			print("Le numéro ne doit être composé que de 10 chiffres.");
		}
		$this->deconnexion();
	}

	/**
	* Fonction permettant d'éditer le nom de l'administrateur qui signera les mails envoyés depuis le site
	* @param $new le nom attribué à l'administrateur
	*/
	public function editnomAdministrateur($new) {
		try{
			$this->connexion();
			$statement = $this->connexion->prepare("UPDATE scriptconfig SET nomAdministrateur = ? ;");
			$statement->bindParam(1,$new);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* récuppérer les info afin de communiquer avec l'api google de recaptcha
	* @return array info de la captcha : clé du site / clé sercrète et url
	*/
	public function getRecaptchaInfo() {
		try {
			$this->connexion();
			$statement = $this->connexion->prepare("SELECT * FROM recaptcha");
			$statement->execute();
			$result = $statement->fetchAll();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
		return $result[0];
	}

	public function getProxySetting() {
		try {
			$this->connexion();
			$statement = $this->connexion->prepare("SELECT * FROM proxy");
			$statement->execute();
			$result = $statement->fetchAll();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
		return $result[0];

	}
	/**
	* ajouter une formation dans la table listeFormation
	* @param String $departement Nom du département de la formation
	* @param String $initiales   Nom court de la formation
	* @param String $description Nom long de la formation
	* @param String $lien        lien vers une page de description de la formation (univ-nantes.fr)
	*/
	public function addFormation($departement, $initiales, $description, $lien)
	{
		try {
			$this->connexion();
			$query = "INSERT INTO listeFormations (departement, initiales, description, lien) VALUES  (?, ?, ?, ?)";
			$statement = $this->connexion->prepare($query);
			$statement->bindParam(1, $departement);
			$statement->bindParam(2, $initiales);
			$statement->bindParam(3, $description);
			$statement->bindParam(4, $lien);
			$statement->execute();
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}
	/**
	* Méthode permettant de récupérer les formations non choisies par des entreprises
	* @return array des listes de formations non choisies
	*/
	public function getAllFormationsNonChoisies(){
		try{
			$this->connexion();
			$query = "SELECT departement, initiales, description, lien FROM listeFormations where initiales not in (select typeFormation from formation) ;";
			$statement = $this->connexion->prepare($query);
			$statement->execute();
			$tabListFormations = $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
		return $tabListFormations;
	}

	/**
	* Méthode qui pemet de récupérer la liste des formations
	*
	* @return    array liste des formation sous forme de tableau
	*/
	public function getListeDepartements() {
		try {
			$this->connexion();
			$query = "SELECT DISTINCT departement FROM listeFormations";
			$statement = $this->connexion->prepare($query);
			$statement->execute();
			$tabListDep = $statement->fetchAll();
			$result = array();
			foreach ($tabListDep as $value) {
				array_push($result, $value['departement']);
			}
			return $result;
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Méthode qui pemet de récupérer la liste des initales des formations
	*
	* @return    array liste des initiales des formations sous forme de tableau
	*/
	public function getListeInitialesFormations() {
		try {
			$this->connexion();
			$query = "SELECT initiales FROM listeFormations";
			$statement = $this->connexion->prepare($query);
			$statement->execute();
			$tabListDep = $statement->fetchAll();
			$result = array();
			foreach ($tabListDep as $value) {
				array_push($result, $value['initiales']);
			}
			return $result;
		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
	}

	/**
	* Fonction qui permet de supprimer une formation.
	* @param  String $initiales les initiales de la formation à supprimer.
	*/
	public function supprimerFormationListe($initiales){
		$this->connexion();
		$statement = $this->connexion->prepare('DELETE FROM listeFormations WHERE initiales = ? and initiales not in (select typeFormation from formation);');
		$statement->bindParam(1, $initiales);
		$statement->execute();
		$this->deconnexion();
		return;
	}

	/**
	* Méthode qui permet de récupérer l'identifiant d'un étudiant suivant son adresse mail
	*
	* @return l'identifiant de l'étudiant
	*/
	public function getIDEtudiantMail($mail)
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT IDEtu FROM etudiant WHERE mailEtu=?;') ;
			$statement->bindParam(1, $mail);
			$statement->execute() ;
			$idEtudiant = $statement->fetch()["IDEtu"] ;

			return $idEtudiant;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	/**
	* Méthode qui permet de récupérer l'identifiant d'une entreprise suivant son adresse mail
	*
	* @return l'identifiant de l'entreprise
	*/
	public function getIDEntrepriseMail($mail)
	{
		try
		{
			$this->connexion() ;
			$statement = $this->connexion->prepare('SELECT IDEnt FROM entreprise WHERE mailEnt=?;') ;
			$statement->bindParam(1, $mail);
			$statement->execute() ;
			$idEntreprise = $statement->fetch()["IDEnt"] ;

			return $idEntreprise;
		}
		catch(AccesTableException $e)
		{
			print($e->getMessage()) ;
		}
		$this->deconnexion() ;
	}

	/**
	 * Fonction permettant de mettre à jour la table heurecreneau.
	 */
	public function updateHeureCreneau(){
		try {

			// Récupération des caractéristiques des créneaux de l'événement
			$configurationEvenement = $this->getConfiguration();

			$heureDebutMatin = new DateTime($configurationEvenement['heureDebutMatin']);
			$heureDebutAprem = new DateTime($configurationEvenement['heureDebutAprem']);
			$nbCreneauxMatin = $configurationEvenement['nbCreneauxMatin'];
			$nbCreneauxAprem = $configurationEvenement['nbCreneauxAprem'];
			$dureeCreneau    = $configurationEvenement['dureeCreneau'];
			$heureCreneauPause = (new DateTime($configurationEvenement['heureCreneauPause']))->format("H:i");
			$heureCreneauPauseMatin = (new DateTime($configurationEvenement['heureCreneauPauseMatin']))->format("H:i");

			$this->connexion();

			// On vide la table contenant les anciennes valeurs
			$this->connexion->query("DELETE from heurecreneau;");

			// On remplit la table avec la nouvelle configuration
			$queryInsert = "INSERT INTO heurecreneau values(?,?);";
			$cpt = 0;


			// Remplissage avec les créneaux du matin
			if($nbCreneauxMatin != 0){
				for($i = 0; $i < $nbCreneauxMatin; $i++){
					if ($heureCreneauPauseMatin == ($heureDebutMatin->format("H:i"))) {
						$heureDebutMatin->add(new DateInterval('PT'.$dureeCreneau.'M'));
					}
					$heureString = $heureDebutMatin->format("H:i");
					$statement = $this->connexion->prepare($queryInsert);
					$statement ->bindParam(1,$i);
					$statement ->bindParam(2,$heureString);
					$statement->execute();
					$heureDebutMatin->add(new DateInterval('PT'.$dureeCreneau.'M'));
				}

			$cpt = $i; // On garde le numéro du dernier créneau pour continuer la numérotation des créneaux
			}

			// Remplissage avec les créneaux de l'après midi
			if($nbCreneauxAprem != 0){
				for($i = $cpt; $i < $nbCreneauxAprem + $cpt; $i++){
					if ($heureCreneauPause == ($heureDebutAprem->format("H:i"))) {
						$heureDebutAprem->add(new DateInterval('PT'.$dureeCreneau.'M'));
					}
					$heureString = $heureDebutAprem->format("H:i");
						$statement = $this->connexion->prepare($queryInsert);
						$statement ->bindParam(1,$i);
						$statement ->bindParam(2,$heureString);
						$statement->execute();
						$heureDebutAprem->add(new DateInterval('PT'.$dureeCreneau.'M'));

				}
			}


		} catch (AccesTableException $e) {
			print($e->getMessage());
		}
		$this->deconnexion();
		return;
	}
}
