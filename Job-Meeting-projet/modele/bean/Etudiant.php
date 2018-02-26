<?php

/**
* Classe permettant d'implémenter un étudiant sous forme d'objet.
*/
class Etudiant {
	private $IDEtu;
	private $nomEtu;
	private $prenomEtu;
	private $mailEtu;
	private $mdpEtu;
	private $numtelEtu;
	private $formationEtu;
	private $listechoixEtu;

	public function getId() {
		return $this->IDEtu;
	}
	public function getNomEtu() {
		return $this->nomEtu;
	}
	public function getPrenomEtu() {
		return $this->prenomEtu;
	}
	public function getMailEtu() {
		return $this->mailEtu;
	}
	public function getMdpEtu() {
		return $this->mdpEtu;
	}
	public function getNumTelEtu() {
		return $this->numtelEtu;
	}
	public function getFormationEtu() {
		return $this->formationEtu;
	}
	public function getListeChoixEtu() {
		return $this->listechoixEtu;
	}
}

?>
