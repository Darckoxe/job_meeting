<?php
header('Content-type: application/json');
// pour autoriser les requêtes cross-domain
header("Access-Control-Allow-Origin: *");
class AutoCompletionCPVille {
	public $CodePostal;
	public $Ville;
}


//Initialisation de la liste
$list = array();

//Connexion MySQL
try
{
    $db = new PDO('mysql:host=localhost;dbname=info2-2015-jobdating', 'info2-2015-jobda', 'jobdating');
    $db->exec("SET CHARACTER SET utf8");
}
catch (Exception $ex)
{
    echo $ex->getMessage();
}
//Construction de la requete
$strQuery = "SELECT CP CodePostal, VILLE Ville FROM cp_autocomplete WHERE ";
if (isset($_GET["codePostal"]))
{
    $strQuery .= "CP LIKE :codePostal ";
}
else
{
    $strQuery .= "VILLE LIKE :ville ";
}
$strQuery .= "AND CODEPAYS = 'FR' ";
//Limite
if (isset($_GET["maxRows"]))
{
    $strQuery .= "LIMIT 0, :maxRows";
}
$query = $db->prepare($strQuery);
if (isset($_POST["codePostal"]))
{
    $value = $_GET["codePostal"]."%";
    $query->bindParam(":codePostal", $value, PDO::PARAM_STR);
}
else
{
    $value = $_GET["commune"]."%";
    $query->bindParam(":ville", $value, PDO::PARAM_STR);
}
//Limite
if (isset($_GET["maxRows"]))
{
    $valueRows = intval($_GET["maxRows"]);
    $query->bindParam(":maxRows", $valueRows, PDO::PARAM_INT);
}

$query->execute();

$list = $query->fetchAll(PDO::FETCH_CLASS, "AutoCompletionCPVille");;

echo json_encode($list);
?>
