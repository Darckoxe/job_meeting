<?php

/**
* Classe permettant d'implémenter une liste de formations sous forme d'objet.
*/
class ListeFormation {
	private $departement;
	private $initiales;
	private $description;
	private $lien;

	public function getDepartement() {
		return $this->departement;
	}
	public function getInitiales() {
		return $this->initiales;
	}
	public function getDescription() {
		return $this->description;
	}
	public function getLien() {
		return $this->lien;
	}
}

?>
