<?php

/**
* Classe permettant la génération de l'entête, du menu et du pied de page.
*/
class UtilitairePageHtml{

  /**
  * Fonction permettant de générer le menu en fonction de l'utilisateur connecté.
  * @return String le menu adapté à l'utilisateur connecté.
  */
  private function itemsBandeauApresConnexion(){
    //affichage du menu client
    if($_SESSION['type_connexion'] == "etudiant") {
      $menu ='<div id="menu" class="topmenu">
      <label for="afficher_menu" class="afficher_menu">Afficher le menu</label>
      <input type="checkbox" id="afficher_menu" role="button">

      <ul id = "onglets">
      <div class="onglet_etu">
      <li id = "onglet1">
      <a href="index.php?choix=ok&menu=1">Planning</a>
      </li>
      </div>
      <div class="onglet_etu">
      <li id = "onglet2">
      <a href="index.php?choix=ok&menu=2">Choix</a>
      </li>
      </div>
      <div class="onglet_etu">
      <li id = "onglet3">
      <a href="index.php?choix=ok&menu=3">Entreprises</a>
      </li>
      </div>
      <div class="onglet_etu">
      <li id = "onglet4">
      <a href="index.php?choix=ok&menu=4">Compte</a>
      </li>
      </div>
      <div class="onglet_etu">
      <li id = "ongletDeconnexion">
      <a href="index.php?deconnexion=ok">Déconnexion</a>
      </li>
      </div>
      </ul>
      </div>
      </div>';
    }
    elseif ($_SESSION['type_connexion'] == "entreprise") {
      $menu='<div id="menu" class="topmenu">
      <label for="afficher_menu" class="afficher_menu">Afficher le menu</label>
      <input type="checkbox" id="afficher_menu" role="button">


      <ul id = "onglets">
      <div class="onglet_ent">
      <li id = "onglet1">
      <a href="index.php?choix=ok&menu=1">Planning</a>
      </li>
      </div>
      <div class="onglet_ent">
      <li id = "onglet4">
      <a href="index.php?choix=ok&menu=2">Compte</a>
      </li>
      </div>
      <div class="onglet_ent">
      <li id = "ongletDeconnexion">
      <a href="index.php?deconnexion=ok">Déconnexion</a>
      </li>
      </div>
      </ul>
      </div>
      </div>';
    }
    else {
      $menu ='<div id="menu" class="topmenu">
      <label for="afficher_menu" class="afficher_menu">Afficher le menu</label>
      <input type="checkbox" id="afficher_menu" role="button">

      <ul id = "onglets">
      <div class="onglet_adm">
      <li id = "onglet1">
      <a href="index.php?choix=ok&menu=1">Planning</a>
      </li>
      </div>
      <div class="onglet_adm">
      <li id = "onglet2">
      <a href="index.php?choix=ok&menu=2">Comptes</a>
      </li>
      </div>
      <div class="onglet_adm">
      <li id = "onglet3">
      <a href = "index.php?choix=ok&menu=3">Formations</a>
      </li>
      </div>
      <div class="onglet_adm">
      <li id = "onglet4">
      <a href="index.php?choix=ok&menu=4">Mails</a>
      </li>
      </div>
      <div class="onglet_adm">
      <li id = "onglet5">
      <a href="index.php?choix=ok&menu=5">Configuration</a>
      </li>
      </div>
      <div class = "onglet_adm">
      <li id = "onglet6">
      <a href="index.php?choix=ok&menu=6">Autres</a>
      </li>
      </div>
      <div class="onglet_adm">
      <li id = "ongletDeconnexion">
      <a href="index.php?deconnexion=ok">Déconnexion</a>
      </li>
      </div>
      </ul>
      </div>
      </div>';
    }
    return $menu;
  }


  /**
  * Fonction permettant de générer l'entête HTML.
  * @return String l'entête du site.
  */
  public function genereEnteteHtml(){
    $entete="<!DOCTYPE html>\n";
    $entete.="<html>\n";
    $entete.="<head>\n\t";
    $entete.="<title>Job-Meeting</title>\n\t";
    $entete.="<meta charset=\"UTF-8\">\n\t";
    //$entete.="<script src=\"vue/js/script.js\"></script>\n\t";
    $entete.="<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js\"></script>\n\t";
    $entete.= "<link href=\"vue/css/general.css\" type=\"text/css\" rel=\"stylesheet\" /> \n";
    $entete.='<link href="http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="Stylesheet"></link>';
    $entete.='<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js" ></script>';
    $entete.="</head>\n";
    $entete.='<body>'."\n".'
    <div id="haut_page">
    <a id="img" href="index.php">
    <img src="vue/img/bandeau-RAlt.png" alt = "bandeau site"/>
    </a>
    ';
    return $entete;
  }

  /**
  * Fonction permettant de générer le bandeau du site après la connexion de l'utilisateur.
  * @return String le bandeau du site après connexion.
  */
  public function genereBandeauApresConnexion(){
    $entete=$this->genereEnteteHtml();
    $entete.$this->genereBandeauAvantConnexion();
    return $entete.$this->itemsBandeauApresConnexion();
  }

  /**
  * Fonction permettant de générer le bandeau du site avant la connexion de l'utilisateur.
  * @return String le bandeau du site avant connexion.
  */
  public function genereBandeauAvantConnexion() {
    return $entete=$this->genereEnteteHtml()."</div>\n";
  }

  /**
  * Fonction permettant de générer le pied de page du site.
  * @return String le pied de page du site.
  */
  public function generePied(){
    $dao = new Dao();
    $tableauConfig = $dao->getConfiguration();
    $siteEvenement = $tableauConfig['siteEvenement'];
    $adresseIUT = $tableauConfig['adresseIUT'];
    $numTelephone = $tableauConfig['telAdministrateur'];
    $pied= '
    <div id="bas_page">
    <table>
    <tr>
    <td>IUT de Nantes - Site '.$siteEvenement.'</td>
    <td>'.$adresseIUT.'</td>
    <td>Tel : '.$numTelephone.'</td>
    </tr>
    </table>
    </div>
    </body>
    </html>
    ';
    return $pied;
  }


}

?>
