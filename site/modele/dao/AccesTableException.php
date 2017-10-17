<?php


/**
* Classe permettant de gérer les exceptions du DAO relatives à un mauvais accès à une table.
*/
class AccesTableException extends Exception
{
	private $chaine;

	/**
	* Constructeur de la classe permettant d'initialiser le message d'erreur.
	* @param String $chaine le message à afficher en cas d'erreur.
	*/
	public function __construct($chaine)
	{
		$this->chaine=$chaine;
	}

	/**
	* Fonction permettant d'afficher le message.
	* @return le message d'erreur
	*/
	public function afficherMessage()
	{
		return $this->chaine;
	}


}

?>
