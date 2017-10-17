<?php


require_once 'util/utilitairePageHtml.php';

/**
* Classe permettant de générer une page d'erreur en cas d'accès à une page introuvable.
*/
class VueLost{


  /**
  * Fonction générant la page d'erreur.
  */
  public function genereVueLost2(){
    if (isset($_SESSION['type_connexion'])) {
      $util = new UtilitairePageHtml();
      echo $util->genereBandeauApresConnexion();
    }
    else {
      $util = new UtilitairePageHtml();
      echo $util->genereBandeauAvantConnexion();
    }

    ?>

    <?php
    if (isset($_SESSION['type_connexion'])) {
      echo '<div id="main">';
    }
    else {
      echo '<div id="login">';
    }
    ?>
    <br/><br/>
    <div id="Lost">

      <?php
      echo $_SESSION['fail'];
      ?>

      <br/><br/><a href="index.php">Retour à l'accueil</a>
    </div>
  </div>
  <?php
  echo $util->generePied();

  }

  /**
  * Méthode qui permet de générer le message d'erreur
  */
  public function genereVueLost()
  {
    echo $_SESSION['fail'];
  }
}
?>
