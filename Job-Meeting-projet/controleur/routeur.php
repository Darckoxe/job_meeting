<?php

require_once 'controleurAuthentification.php';
require_once 'controleurInscriptionEtu.php';
require_once 'controleurInscriptionEnt.php';
require_once 'controleurConfirmationInscription.php';
require_once 'controleurOubliMdp.php';
require_once 'controleurLost.php';
require_once 'controleurMenu.php';
require_once 'controleurProfil.php';
require_once 'controleurMail.php';
require_once __DIR__."/../modele/dao/dao.php";

/**
* Classe du contrôleur central de l'application.
*/
class Routeur {

  private $ctrlAuthentification;
  private $ctrlInscriptionEtu;
  private $ctrlInscriptionEnt;
  private $ctrlConfirmationInscription;
  private $ctrlOubliMdp;
  private $ctrlLost;
  private $ctrlProfil;
  private $ctrlMail;
  private $dao;

  /**
  * Constructeur de la classe initialisation les différents contrôleurs utilisés.
  */
  public function __construct() {
    $this->ctrlAuthentification= new ControleurAuthentification();
    $this->ctrlInscriptionEnt= new ControleurInscriptionEnt();
    $this->ctrlInscriptionEtu= new ControleurInscriptionEtu();
    $this->ctrlConfirmationInscription= new ControleurConfirmationInscription();
    $this->ctrlOubliMdp= new ControleurOubliMdp();
    $this->ctrlMenu = new ControleurMenu();
    $this->ctrlLost = new ControleurLost();
    $this->ctrlProfil = new ControleurProfil();
    $this->ctrlMail = new ControleurMail();
    $this->dao = new Dao_2016();
  }

  // Traite une requête entrante
  public function routerRequete() {

    if (isset($_POST['submit_login'])) {
      $this->dao->connexion();
      // Si l'identifiant correspond à un compte activé (étudiant/entreprise/administrateur)
      if ($this->dao->estValide($_POST['identifiant'])) {
        // Si le mot de passe est correct
        if($this->dao->verifieMotDePasse($_POST['identifiant'],$_POST['password'])) {
          $_SESSION['type_connexion'] = $this->dao->getTypeUtilisateur($_POST['identifiant']);
          $_SESSION['idUser'] = $this->dao->getId($_POST['identifiant'],$_SESSION['type_connexion']);
          $this->ctrlMenu->afficherMenu(1);
          return;
        }
        else {
          $_SESSION['fail'] = "La connexion au compte a échouée. Veuillez vérifier votre mot de passe et identifiant.";
        }
      }
      else {
        // Le compte existe mais n'a pas encore été activé par l'administrateur
        if($this->dao->aCompteTemporaire($_POST['identifiant'])){
          $_SESSION['fail'] = "Le compte n'a pas encore été activé par l'administrateur.";
        }else{
          // Le compte n'existe ni en "normal", ni en temporaire
          $_SESSION['fail'] = "Aucun compte correspondant, veuillez vérifier vos identifiants de connexion.";
        }
      }

    }

    // Génération du planning
    if(isset($_POST['startGeneration'])){
      $this->dao->generatePlanning();
      $this->ctrlMenu->afficherMenu(1);
      return;
    }

    // Lors d'un changement dans la liste des choix des entreprises d'un étudiant
    if(isset($_POST['changementListeEtu'])) {
      $formationEtudiant = $this->dao->getFormationEtudiant($_SESSION['idUser']);
      $string1 = "";
      $string2 = "";
      $string3 = "";
      $string4 = "";
      if ($_POST['choix1'] != "Faire un choix..." && $this->dao->getNbPlacesRestantes($_POST['choix1'],$formationEtudiant) != -1 ) {
        $string1 = $_POST['choix1'];
      }
      if ($_POST['choix2'] != "Faire un choix..." && $this->dao->getNbPlacesRestantes($_POST['choix2'],$formationEtudiant) != -1 ) {
        $string2 = ','.$_POST['choix2'];
      }
      if ($_POST['choix3'] != "Faire un choix..." && $this->dao->getNbPlacesRestantes($_POST['choix3'],$formationEtudiant) != -1 ) {
        $string3 = ','.$_POST['choix3'];
      }
      if ($_POST['choix4'] != "Faire un choix..." && $this->dao->getNbPlacesRestantes($_POST['choix4'],$formationEtudiant) != -1 ) {
        $string4 = ','.$_POST['choix4'];
      }

      $newList = $string1.$string2.$string3.$string4;
      $this->dao->editChoixEtudiant($_SESSION['idUser'],$newList);
      $this->ctrlMenu->afficherMenu(2);
      return;
    }

    // Suppression d'un ou plusieurs comptes (Etu,tmpEtu,Ent et tmpEnt)
    if(isset($_POST['supprimer'])){
      if ($_SESSION['type_connexion'] == "admin") {
        if(isset($_POST['mails'])){
          foreach($_POST['mails'] as $tmp){
            $tab = explode("+",$tmp);
            if (strcmp($tab[2],"Etu") == 0) {
              $this->dao->supprimerEtu($tab[1]);
            }
            if (strcmp($tab[2],"tmpEtu") == 0) {
              $this->dao->supprimerEtuTemp($tab[1]);
            }
            if (strcmp($tab[2],"Ent") == 0) {
              $this->dao->supprimerEnt($tab[1]);
            }
            if (strcmp($tab[2],"tmpEnt") == 0) {
              $this->dao->supprimerEntTemp($tab[1]);
            }
          }
        }
      }
      $this->ctrlMenu->afficherMenu(2);
      return;
    }

    if(isset($_SESSION['type_connexion'])){
      if ($_SESSION['type_connexion'] == "admin") {
        // Formulaire correspondant au mail vide
        if(isset($_POST['mail'])){
          if(isset($_POST['mails'])){
            $_SESSION['mails'] = $_POST['mails'];
          }
          $this->ctrlMail->afficherFormulaireMail(-1);
          return;

        }
        // Formulaire correspondant au mail choisi
        if(isset($_GET['modele'])){
          $this->ctrlMail->afficherFormulaireMail($_GET['modele']);
          unset($_SESSION['mails']);
          return;
        }

        // Modification du modele de mail
        if(isset($_POST['modifierMail'])){
          //Si tous les champs sont bien renseignés
          if(isset($_POST['corpsMail']) && isset($_POST['nomMail'])){
            if($_POST['objetMail']==null){
              $_POST['objetMail']=" ";
            }
            $this->ctrlMail->modifierMail($_POST['objetMail'], $_POST['corpsMail'], $_POST['nomMail']);
            return;
          }else{
            echo "Impossible de modifier le mail.";
            return;
          }
        }
        //Envoi du mail
        if(isset($_POST['envoyerMail'])){
          $tailleListDiff = count($this->dao->getListesDiffusion());
          $tabDestinataireListeDiff = array();
          for($i=0; $i < $tailleListDiff; $i++)
          {
            if(isset($_POST['addrDiff'."$i"]))
            {
              array_push($tabDestinataireListeDiff, $_POST['addrDiff'."$i"]);
            }
          }
          //Si tous les champs sont bien renseignés
          if(isset($_POST['destinataireMail']) && isset($_POST['objetMail'])&& isset($_POST['corpsMail']) && isset($_POST['nomMail']))
          {
            // Lors de l'envoi d'un mail à plusieurs destinataires
            if(!(strstr($_POST['destinataireMail'], ";") == false) || (count($tabDestinataireListeDiff) > 0))
            {
              $destinatairesMails = implode(";", $tabDestinataireListeDiff);
              $destinatairesMails .= ";";
              $destinatairesMails .= $_POST['destinataireMail'];
              $this->ctrlMail->envoiMailMultiple($destinatairesMails, $_POST['objetMail'],$_POST['corpsMail'], $_POST['nomMail'],false);
            }
            // Lors de l'envoi d'un mail à un destinataire unique
            else
            {
              $this->ctrlMail->envoyerMail($_POST['destinataireMail'],$_POST['objetMail'],$_POST['corpsMail'], $_POST['nomMail'],false);
            }
            $this->ctrlMenu->afficherMenu(4);
            return;
          }
        }

        // Modification des pièces jointes par défaut
        if(isset($_GET['modifPjDefaut'])){
          $this->ctrlMail->afficheModifPiecesJointes($_GET['modifPjDefaut']);
          return;
        }

        // Application des modifications de pièces jointes et retour au formulaire de mail
        if(isset($_POST['modifPjDefaut'])){
          if(isset($_POST['nomMailModif'])){
            $this->ctrlMail->modifPiecesJointes($_POST['nomMailModif'],$_FILES['ma_nouvelle_pj']);
            $this->ctrlMail->afficherFormulaireMailNom($_POST['nomMailModif']);
            return;
          }
        }

        // Modification du bandeau du site
        if(isset($_POST['modificationBandeau'])){
          $this->ctrlMenu->changeBandeauSite($_FILES['mon_nouveau_bandeau']);
          $this->ctrlMenu->afficherMenu(6);
          return;
        }

        // Affichage de la page relative à une formation
        if(isset($_GET['affichageFormation'])){
          $this->ctrlMenu->afficherUneFormation($_GET['affichageFormation']);
          return;
        }

        // Suppression d'une formation
        if(isset($_GET['suppressionFormation'])){
          $this->ctrlMenu->suppressionFormation($_GET['suppressionFormation']);
          return;
        }
      }
    }




    // Affichage de la page profil correspondant à l'utilisateur souhaité en fonction de la type de connexion
    if (isset($_GET['profil']) && isset($_GET['type']) && isset($_SESSION['type_connexion'])) {
      if ($_GET['type'] == "tmpEnt") {
        $this->ctrlProfil->afficherProfil("entreprise",$this->dao->getTempEnt($_GET['profil']));
        return;
      }
      if ($_GET['type'] == "tmpEtu") {
        $this->ctrlProfil->afficherProfil("etudiant",$this->dao->getTempEtu($_GET['profil']));
        return;
      }
      if ($_GET['type'] == "Ent") {
        $this->ctrlProfil->afficherProfil("entreprise",$this->dao->getEnt($_GET['profil']));
        return;
      }
      if ($_GET['type'] == "Etu") {
        $this->ctrlProfil->afficherProfil("etudiant",$this->dao->getEtu($_GET['profil']));
        return;
      }
    }

    // Application des modifications apportées par l'administrateur sur la configuration globale de l'évènement.
  if (isset($_POST['changementConfig'])) {
    if ($_POST['heureDebutMatin'] != "") {
      $this->dao->editHeureDebutMatin($_POST['heureDebutMatin']);
    }
    if ($_POST['heureDebutAprem']  != "") {
      $this->dao->editHeureDebutAprem($_POST['heureDebutAprem']);
    }
    if ($_POST['nbCreneauxMatin'] != "") {
      $this->dao->editNbCreneauxMatin($_POST['nbCreneauxMatin']);
    }
    if ($_POST['heureCreneauPause'] != ""){
      $this->dao->editHeureCreneauPause($_POST['heureCreneauPause']);
    }
    if ($_POST['heureCreneauPauseMatin'] != ""){
      $this->dao->editHeureCreneauPauseMatin($_POST['heureCreneauPauseMatin']);
    }
    if ($_POST['nbCreneauxAprem'] != "") {
      $this->dao->editNbCreneauxAprem($_POST['nbCreneauxAprem']);
    }
    if ($_POST['dureeCreneau'] != "") {
      $this->dao->editDureeCreneau($_POST['dureeCreneau']);
    }
    if ($_POST['dateDebutInscriptionEnt'] != "") {
      $this->dao->editDateDebutInscriptionEnt($_POST['dateDebutInscriptionEnt']);
    }
    if ($_POST['dateFinInscriptionEnt'] != "") {
      $this->dao->editDateFinInscriptionEnt($_POST['dateFinInscriptionEnt']);
    }
    if ($_POST['dateDebutInscriptionEtu'] != "") {
      $this->dao->editDateDebutInscriptionEtu($_POST['dateDebutInscriptionEtu']);
    }
    if ($_POST['dateFinInscription'] != "") {
      $this->dao->editDateFinInscription($_POST['dateFinInscription']);
    }
    if ($_POST['dateDebutVuePlanning'] != "") {
      $this->dao->editDateDebutVuePlanning($_POST['dateDebutVuePlanning']);
    }
    if ($_POST['dateEvenement'] != "") {
      $this->dao->editDateEvenement($_POST['dateEvenement']);
    }
    if ($_POST['siteEvenement'] != "") {
      $this->dao->editSiteEvenement($_POST['siteEvenement']);
    }
    if ($_POST['adresseIUT'] != "") {
      $this->dao->editAdresseIUT($_POST['adresseIUT']);
    }
    if ($_POST['mailAdministrateur'] != "") {
      $this->dao->editMailAdministrateur($_POST['mailAdministrateur']);
    }
    if ($_POST['telAdministrateur'] != "") {
      $this->dao->editTelAdministrateur($_POST['telAdministrateur']);
    }
    if ($_POST['nomAdministrateur'] != ""){
      $this->dao->editnomAdministrateur($_POST['nomAdministrateur']);
    }
    $this->ctrlMenu->afficherMenu(5);
    return;
  }


    // Les modifications de compte de l'entreprise
    if (isset($_POST['modification_entreprise_organisation'])) {
      if ($_POST['disponibiliteSociete'] != "") {
        $this->dao->editTypeCreneauEntreprise(($_SESSION['idUser']), $_POST['disponibiliteSociete']);
      }
      if ($_POST['nbStandsSociete'] != 0) {
        $this->dao->editNbStandsEntreprise(($_SESSION['idUser']), $_POST['nbStandsSociete']);
      }
      if ($_POST['nbRecruteursSociete'] >= 0) {
        $this->dao->editNbRecruteursEntreprise(($_SESSION['idUser']), $_POST['nbRecruteursSociete']);
      }
      if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
        $_SESSION['testProfil'] = $this->dao->getEnt($_SESSION['idUser']);
        $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
      }
      else{
        $this->ctrlMenu->afficherMenu(2);
      }
      return;
    }

    if (isset($_POST['modification_entreprise_formations'])) {
      if(isset($_POST['formation'])) {
        $stringFormations = "";
        $forms = $_POST['formation'];
        foreach ($forms as $form){
          $stringFormations = $stringFormations . $form . ",";
        }
        $this->dao->editFormationsRechercheesEntreprise(($_SESSION['idUser']), $stringFormations);
        $_POST['nomSociete'] = $this->dao->getNomEntreprise($_SESSION['idUser']);
        echo ($_POST['nomSociete']);
        $listeFormations = $this->dao->getListeFormations();
        foreach ($listeFormations as $formation){
          $name="offre_";
          $name.=$formation->getInitiales();
          if (isset($_FILES[$name]['error'])) {
            if(($_FILES[$name]['error'] == 0) || ($_FILES[$name]['error'] == 4)){
              if ($_FILES[$name]['size'] > 10485760) {
                echo "La taille du fichier est trop grande (1Mo max).";
                exit();
              }
              $extensions_valides = array("pdf");
              $extension_upload = strtolower( substr( strrchr($_FILES[$name]['name'],'.') ,1) );

              if(($_FILES[$name]['error'] == 0)){
                if (!in_array($extension_upload, $extensions_valides)) {
                  echo "Mauvais format du fichier (pdf necessaire)";
                  exit();
                }
              }

              if ((isset($_POST['nomSociete'])) && ($_FILES[$name]['error'] == 0)) {
                  $nomFichier = $_POST['nomSociete'].'_'.$name;
                  $chemin = "offre/{$nomFichier}.{$extension_upload}";
                  echo $nomFichier;
                  if (isset($_FILES[$name]['tmp_name'])) {
                    $resultat = move_uploaded_file($_FILES[$name]['tmp_name'], $chemin);
                      if (!$resultat) {
                        echo "Echec de transfert";
                        exit();
                      }
                  }
              }
            }
            else{
              echo "Une erreur lors du transfert de fichier est survenue. ";
              echo "Code erreur ".$_FILES[$name]['error'];
              exit();
            }
          }
        }

      }
      if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
        $_SESSION['testProfil'] = $this->dao->getEnt($_SESSION['idUser']);
        $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
      }
      else{
        $this->ctrlMenu->afficherMenu(2);
      }
      return;
    }


    if (isset($_POST['modification_entreprise_informations'])) {
      if ($_POST['nomSociete'] != "") {
        $this->dao->editNomEntreprise(($_SESSION['idUser']), $_POST['nomSociete']);
      }
      if ($_POST['villeSociete'] != "") {
        $this->dao->editVilleEntreprise(($_SESSION['idUser']), $_POST['villeSociete']);
      }
      if ($_POST['codePostalSociete'] != 0) {
        $this->dao->editCPEntreprise(($_SESSION['idUser']), $_POST['codePostalSociete']);
      }
      if ($_POST['adresseSociete'] != "") {
        $this->dao->editAdresseEntreprise(($_SESSION['idUser']), $_POST['adresseSociete']);
      }
      if ($_POST['offre_txt'] != ""){
        $caractere_a_remplacer = array("'");
        $caractere_remplacant = array("\'");
        $_POST['offre_txt'] = str_replace($caractere_a_remplacer,$caractere_remplacant,$_POST['offre_txt']);
        $this->dao->editOffreEntreprise(($_SESSION['idUser']), $_POST['offre_txt']);
      }
      if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
        $_SESSION['testProfil'] = $this->dao->getEnt($_SESSION['idUser']);
        $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
      }
      else{
        $this->ctrlMenu->afficherMenu(2);
      }

      return;
    }

    if (isset($_POST['modification_entreprise_contact'])) {
      if ($_POST['nomContactSociete'] != "") {
        $this->dao->editNomContactEntreprise(($_SESSION['idUser']), $_POST['nomContactSociete']);
      }
      if ($_POST['prenomContactSociete'] != "") {
        $this->dao->editPrenomContactEntreprise(($_SESSION['idUser']), $_POST['prenomContactSociete']);
      }
      if ($_POST['emailSociete'] != "") {
        $this->dao->editMailEntreprise(($_SESSION['idUser']), $_POST['emailSociete']);
      }
      if ($_POST['numTelSociete'] != 0) {
        $this->dao->editTelephoneEntreprise(($_SESSION['idUser']), $_POST['numTelSociete']);
      }
      if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
        $_SESSION['testProfil'] = $this->dao->getEnt($_SESSION['idUser']);
        $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
      }
      else{
        $this->ctrlMenu->afficherMenu(2);
      }
      return;
    }
    if (isset($_POST['modification_entreprise_motdepasse'])) {
      if ($_SESSION['type_connexion'] == "admin") {
        if ($_POST['mdpNouveau1'] != "" && $_POST['mdpNouveau2'] != "" && $_POST['mdpNouveau1'] == $_POST['mdpNouveau2']) {
          $this->dao->editMdpEntreprise(($_SESSION['idUser']), $_POST['mdpNouveau1'], ""); // L'admin n'a pas besoin de renseigner l'ancien mot de passe
        }
        if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
          $_SESSION['testProfil'] = $this->dao->getEnt($_SESSION['idUser']);
          $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
        }
        else{
          $this->ctrlMenu->afficherMenu(2);
        }
        return;
      }
      elseif (($_POST['mdpActuel'] != "" && $_POST['mdpNouveau1'] != "" && $_POST['mdpNouveau2'] != ""
      && $_POST['mdpNouveau1'] == $_POST['mdpNouveau2'])) {
        $this->dao->editMdpEntreprise(($_SESSION['idUser']), $_POST['mdpNouveau1'], $_POST['mdpActuel']);
      }
      $this->ctrlMenu->afficherMenu(2);
      return;
    }
    if (isset($_POST['modCreneauxFormationEntreprise'])) {
      if ($_SESSION['type_connexion'] == "admin") {
        $tabFormation = $this->dao->getAllFormations();
        foreach ($tabFormation as $key => $value) {
          $idFormation = $value[0];
          // gestion des posts avec partie fixe et partie variable de modification des crénaux de formation des entreprise
          if (isset($_POST['debEntreModif_'.$idFormation])) {
            $idEntre = $_SESSION['idUser'];
            $numDebut = $_POST["debEntreModif_$idFormation"];
            $numFin = $_POST["finEntreModif_$idFormation"];
            $this->dao->editCreneauDebutEtFinFormationEntreprise($idEntre, $numDebut , $numFin , $idFormation);
          }
        }
      }
      if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
        $_SESSION['testProfil'] = $this->dao->getEnt($_SESSION['idUser']);
        $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
      }
      else{
        $this->ctrlMenu->afficherMenu(2);
      }
      return;
    }

    //Les modifications de compte de l'étudiant
    if (isset($_POST['modification_etudiant_identite'])) {
      if (isset($_POST['nomEtu'])){
        if ($_POST['nomEtu'] != "") {
          $this->dao->editNomEtudiant(($_SESSION['idUser']), $_POST['nomEtu']);
        }
      }
      if (isset($_POST['prenomEtu'])){
        if ($_POST['prenomEtu'] != "") {
          $this->dao->editPrenomEtudiant(($_SESSION['idUser']), $_POST['prenomEtu']);
        }
      }
      if (isset($_POST['email'])){
        if ($_POST['email'] != "") {
        $ancien_mail = $this->dao->getMailEtu($_SESSION['idUser']);
        if ($ancien_mail != $_POST['email'] ) {
          copy("cv/".$ancien_mail.".pdf", "cv/".$_POST['email'].".pdf");
          unlink("cv/".$ancien_mail.".pdf");
        }
        $this->dao->editMailEtudiant(($_SESSION['idUser']), $_POST['email']);
        }
      }
      if (isset($_POST['numTelEtu'])){
        if ($_POST['numTelEtu'] > 0) {
          $this->dao->editTelephoneEtudiant(($_SESSION['idUser']), $_POST['numTelEtu']);
        }
      }
      if (isset($_POST['nomFormEtu'])){
        if ($_POST['nomFormEtu'] != "") {
          $this->dao->editFormationEtudiant($_SESSION['idUser'],$_POST['nomFormEtu']);
        }
      }
      if ($_SESSION['type_connexion'] == "admin"){
        if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
          $_SESSION['testType']="etudiant";
          $_SESSION['testProfil'] = $this->dao->getEtu($_SESSION['idUser']);
          $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
        }
      }
      else{
        $this->ctrlMenu->afficherMenu(4);
      }
      return;
    }

    if (isset($_POST['modification_cv'])) {
      /*1. Récupérer email de l'etudiant actuel
        2. Supprimer le fichier du même nom que le mail
        3. Upload du fichier
        4. Changement du nom du fichier par l'adresse mail
      */
      $mail = $this->dao->getMailEtu($_SESSION['idUser']);
      $_POST['email'] = $mail;
      $nomfichier = "$mail".".pdf";
      // on vérifie que le fichier est bien upload
      if (isset($_FILES['cv']['error'])){
        if ($_FILES['cv']['error'] > 0) {
          echo "Une erreur lors du transfert de fichier est survenue.";
          exit();}
      }
      // on vérifie la taille du fichier
      if (isset($_FILES['cv']['size'])){ // taille en octet
        if ($_FILES['cv']['size'] > 1048576) {
          echo "La taille du fichier est trop grande (1Mo max).";
          exit();
        }
      }
      // on vérifie que le format est en pdf
      if (isset($_FILES['cv']['name'])) {
        $extensions_valides = array("pdf");
        $extension_upload = strtolower( substr( strrchr($_FILES['cv']['name'],'.') ,1) );
        if (!in_array($extension_upload, $extensions_valides)) {
          echo "Mauvais format du fichier (pdf nécessaire)";
          exit();
        }
        else {
          if (isset($_POST['email'])) {
            $nomFichier = $_POST['email'];
            $chemin = "cv/{$nomFichier}.{$extension_upload}";
            if (isset($_FILES['cv']['tmp_name'])) {
              unlink("cv/".$nomfichier);
              $resultat = move_uploaded_file($_FILES['cv']['tmp_name'], $chemin);
              if (!$resultat) {
                echo "Echec de transfert";
                exit();
              }
            }
          }
        }
      }
    }

    if (isset($_POST['modification_etudiant_motdepasse'])) {
      if ($_SESSION['type_connexion'] == "admin") {
        if ($_POST['mdpNouveau1'] != "" && $_POST['mdpNouveau2'] != "" && $_POST['mdpNouveau1'] == $_POST['mdpNouveau2']) {
          $this->dao->editMdpEtudiant(($_SESSION['idUser']), $_POST['mdpNouveau1'],"");
        }
        if(isset($_SESSION['testProfil']) && isset($_SESSION['testType'])){
          $_SESSION['testType'] = "etudiant";
          $_SESSION['testProfil'] = $this->dao->getEtu($_SESSION['idUser']);
          $this->ctrlProfil->afficherProfil($_SESSION['testType'],$_SESSION['testProfil']);
        }
        else{
          $this->ctrlMenu->afficherMenu(2);
        }
        return;
      }
      elseif ($_POST['mdpActuel'] != "" && $_POST['mdpNouveau1'] != "" && $_POST['mdpNouveau2'] != ""
      && $_POST['mdpNouveau1'] == $_POST['mdpNouveau2']) {
        $this->dao->editMdpEtudiant(($_SESSION['idUser']), $_POST['mdpNouveau1'], $_POST['mdpActuel']);
      }
      $this->ctrlMenu->afficherMenu(4);
      return;
    }

    // Affichage d'une page d'erreur en cas de problème rencontré lors de la navigation sur le site (page non trouvée)
    if (isset($_GET['error'])) {
      $_SESSION['fail'] = "Êtes-vous perdu(e) ? Il semblerait qu'un imprévu<br/>soit arrivé. Refaites donc votre choix pour retrouver<br/>vos marques.";
      $this->ctrlLost->genererLost();
      unset($_SESSION['fail']);
      return;
    }

    // Gestion de l'inscription d'un utilisateur
    if (isset($_POST['inscription'])) {
      $dateNow = new DateTime("now");
      $tabConfig = $this->dao->getConfiguration();
      $dateDebutEnt = new DateTime($tabConfig['dateDebutInscriptionEnt']);
      $dateLimitEnt = new DateTime($tabConfig['dateFinInscriptionEnt']);
      $dateDebutEtu = new DateTime($tabConfig['dateDebutInscriptionEtu']);
      $dateLimitEtu = new DateTime($tabConfig['dateFinInscription']);

      //Correction du décalage d'une journée
      $dateLimitEnt->setTime(23,59,59);
      $dateLimitEtu->setTime(23,59,59);

      if ((($_POST['inscription'] == "etudiant")) && ($dateNow >= $dateDebutEtu && $dateNow <= $dateLimitEtu)) {
          if ($this->dao->ajoutEtudiant()) {
            $this->ctrlInscriptionEtu->gestionEnvoiCV();
            $this->ctrlConfirmationInscription->genereVueConfirmationInscription("<br>Après cette étape,  vous pourrez choisir les entreprises");
            return;
          }
          else {
            $_SESSION['fail'] = "Une autre personne du même nom ou utilisant cette adresse email semble déjà inscrite. Veuillez réessayer avec une autre adresse ou vérifiez que vous n'êtes pas déjà inscrit.";
            $this->ctrlInscriptionEtu->inscriptionEtu();
            unset($_SESSION['fail']);
            return;
          }
      }

      if (($_POST['inscription'] == "entreprise") && ($dateNow <= $dateLimitEnt && $dateNow >= $dateDebutEnt)) {
        if($this->dao->ajoutEntreprise()) {
          $listeFormations = $this->dao->getListeFormations();
          foreach ($listeFormations as $formation){
            $name="offre_";
            $name.=$formation->getInitiales();
            if (isset($_FILES[$name]['error'])) {
              if(($_FILES[$name]['error'] == 0) || ($_FILES[$name]['error'] == 4)){
                if ($_FILES[$name]['size'] > 10485760) {
                  echo "La taille du fichier est trop grande (1Mo max).";
                  exit();
                }
                $extensions_valides = array("pdf");
                $extension_upload = strtolower( substr( strrchr($_FILES[$name]['name'],'.') ,1) );

                if(($_FILES[$name]['error'] == 0)){
                  if (!in_array($extension_upload, $extensions_valides)) {
                    echo "Mauvais format du fichier (pdf necessaire)";
                    exit();
                  }
                }

                if ((isset($_POST['nomSociete'])) && ($_FILES[$name]['error'] == 0)) {
                    $nomFichier = $_POST['nomSociete'].'_'.$name;
                    $chemin = "offre/{$nomFichier}.{$extension_upload}";
                    echo $nomFichier;
                    if (isset($_FILES[$name]['tmp_name'])) {
                      $resultat = move_uploaded_file($_FILES[$name]['tmp_name'], $chemin);
                        if (!$resultat) {
                          echo "Echec de transfert";
                          exit();
                        }
                    }
                }
              }
              else{
                echo "Une erreur lors du transfert de fichier est survenue. ";
                echo "Code erreur ".$_FILES[$name]['error'];
                exit();
              }
            }
          }
          $this->ctrlConfirmationInscription->genereVueConfirmationInscription("");
          return;
        }
        else {
          $_SESSION['fail'] = "Cette adresse email a déjà été utilisée ou cette entreprise est déjà inscrite à l'événement. Veuillez vérifier que vous n'êtes pas déjà inscrit ou réessayez avec une autre adresse email.";
          $this->ctrlInscriptionEnt->inscriptionEnt();
          unset($_SESSION['fail']);
          return;
        }
      }
    }

    // Validation de l'inscription d'un utilisateur
    if (isset($_GET['validation']) && isset($_GET['id']) && isset($_GET['type']) && isset($_SESSION['type_connexion'])) {
      if ($_SESSION['type_connexion'] == "admin") {
        // Inscription de l'étudiant
        if ($_GET['type'] == "tmpEtu") {
          $user = $this->dao->getTempEtu($_GET['id']);
          $destinataire = $user[0]->getMailEtu();
          $objet = "Inscription validée et A FINALISER - Rencontres Alternance IUT de Nantes";
          $nomMail = 'preValidationEtu';
          $automatique = true;
          $this->ctrlMail->envoyerMail($destinataire,$objet," ",$nomMail,$automatique);

          $this->dao->validerEtudiant($_GET['id']);
        }
        // Inscription de l'entreprise
        if ($_GET['type'] == "tmpEnt") {

          $user = $this->dao->getTempEnt($_GET['id']);
          $destinataire = $user[0]->getMailEnt();
          $objet = "Confirmation inscription Rencontres Alternance - IUT de Nantes";
          $nomMail = 'confirmInscriptionEnt';
          $automatique = true;
          $this->ctrlMail->envoyerMail($destinataire,$objet," ",$nomMail,$automatique);

          $this->dao->validerEntreprise($_GET['id']);
        }
        $this->ctrlMenu->afficherMenu(2);
        return;
      }
    }

    // Gelage d'un compte utilisateur par l'administrateur
    if (isset($_GET['geler']) && isset($_GET['id']) && isset($_GET['type']) && isset($_SESSION['type_connexion'])) {
      if ($_SESSION['type_connexion'] == "admin") {
        if ($_GET['type'] == "Etu") {
          $user = $this->dao->getEtu($_GET['id']);
          $this->dao->gelerEtudiant($_GET['id']);
        }
        if ($_GET['type'] == "Ent") {
          $user = $this->dao->getEnt($_GET['id']);
          $this->dao->gelerEntreprise($_GET['id']);
        }
        $this->ctrlMenu->afficherMenu(2);
        return;
      }
    }

    // Affichage de la page d'oubli de mot passe.
    if (isset($_GET['oubliMdp'])) {
      $this->ctrlOubliMdp->aideOubliMdp();
      return;
    }

    // Affichage de la page d'inscription d'un étudiant
    if (isset($_GET['inscriptionEtu'])) {
      $dateNow = new DateTime("now");
      $tabConfig = $this->dao->getConfiguration();
      $dateDebutEtu = new DateTime((string)$tabConfig['dateDebutInscriptionEtu']);
      $dateLimitEtu = new DateTime((string)$tabConfig['dateFinInscription']);

      //Correction du décalage d'une journée
      $dateLimitEtu->setTime(23,59,59);

      if ($dateNow <= $dateLimitEtu && $dateNow >= $dateDebutEtu) {
        $this->ctrlInscriptionEtu->inscriptionEtu();
        return;
      }
    }

    // Affichage de la page d'inscription d'une entreprise
    if (isset($_GET['inscriptionEnt'])) {
      $dateNow = new DateTime("now");
      $tabConfig = $this->dao->getConfiguration();
      $dateDebutEnt = new DateTime((string)$tabConfig['dateDebutInscriptionEnt']);
      $dateLimitEnt = new DateTime($tabConfig['dateFinInscriptionEnt']);

      //Correction du décalage d'une journée
      $dateLimitEnt->setTime(23,59,59);

      if ($dateNow <= $dateLimitEnt && $dateNow >= $dateDebutEnt) {
        $this->ctrlInscriptionEnt->inscriptionEnt();
        return;
      }
    }

    // Redirection vers la page souhaitée ou affichage de la page d'erreur.
    if (isset($_GET['choix']) && isset($_SESSION['type_connexion']) && isset($_GET['menu'])) {
      if ($_SESSION['type_connexion'] == "entreprise" && ($_GET['menu'] > 2 || $_GET['menu'] < 1)) {
        $_SESSION['fail'] = "Êtes-vous perdu(e) ? Il semblerait qu'un imprévu<br/>soit arrivé. Refaites donc votre choix pour retrouver<br/>vos marques.";
        $this->ctrlLost->genererLost();
        unset($_SESSION['fail']);
        return;
      }
      if ($_SESSION['type_connexion'] == "admin" && ($_GET['menu'] > 6 || $_GET['menu'] < 1)) {
        $_SESSION['fail'] = "Êtes-vous perdu(e) ? Il semblerait qu'un imprévu<br/>soit arrivé. Refaites donc votre choix pour retrouver<br/>vos marques.";
        $this->ctrlLost->genererLost();
        unset($_SESSION['fail']);
        return;
      }
      if ($_SESSION['type_connexion'] == "etudiant" && ($_GET['menu'] > 4 || $_GET['menu'] < 1)) {
        $_SESSION['fail'] = "Êtes-vous perdu(e) ? Il semblerait qu'un imprévu<br/>soit arrivé. Refaites donc votre choix pour retrouver<br/>vos marques.";
        $this->ctrlLost->genererLost();
        unset($_SESSION['fail']);
        return;
      }
      $this->ctrlMenu->afficherMenu($_GET['menu']);
      return;
    }

    // Déconnexion d'un utililisateur
    if (isset($_GET['deconnexion'])) {

      session_destroy();
      $this->ctrlAuthentification->authentification();
      return;
    }

    // Ajout d'une nouvelle formation
    if (isset($_POST['addFormation'])) {
      $this->dao->addFormation($_POST['departement'], $_POST['initiales'], $_POST['description'], $_POST['lien']);
      $this->ctrlMenu->afficherMenu(3);
      return;
    }

    // Affichage de la page d'accueil de l'utilisateur
    if (isset($_SESSION['type_connexion'])) {
      $this->ctrlMenu->afficherMenu(1);
      return;
    }

    // Demande d'un nouveau mot de passe
    if (isset($_POST['submit_new_mdp'])) {

      // Vérification de la présence de l'adresse mail dans la base de données
      if (($this->dao->estInscrit($_POST['mail_new_mdp']))) {

        $objet = "Réinitialisation du mot de passe Rencontres Alternance - IUT de Nantes";
        $mail = $this->ctrlMail->genererMailOublie();

        $contenu = $mail[0];
        $new_mdp = $mail[1];

        // On vérifie que les champs du formulaire ont bien été remplis
        if(isset($_POST['profil_new_mdp']) && isset($_POST['mail_new_mdp'])){
          $tmp = new Dao_2016;

          // On vérifie que la modification de mot de passe a bien été effectuée
          if($this->ctrlOubliMdp->editNewMdp($_POST['mail_new_mdp'],$_POST['profil_new_mdp'],$new_mdp)){

            $this->ctrlMail->envoyerMail($_POST['mail_new_mdp'],$objet,$contenu,"",false);

            $_SESSION['fail'] = "Un email a été envoyé à l'adresse indiquée.";

            $this->ctrlLost->genererLost();
            unset($_SESSION['fail']);
            return;
          }else{
            $_SESSION['fail'] = "L'adresse mail ne correspond pas au profil";
            $this->ctrlOubliMdp->aideOubliMdp();
            unset($_SESSION['fail']);
            return;
          }
        }else{
          $_SESSION['fail'] = "Veuillez remplir tous les champs.";
          $this->ctrlOubliMdp->aideOubliMdp();
          unset($_SESSION['fail']);
          return;

        }

      }
      else {
        $_SESSION['fail'] = "Un email vient de vous être envoyé. Si vous n'avez pas reçu d'email, Veuillez vérifier que vous avez bien entré l'email correspondant à votre compte.";
        $this->ctrlOubliMdp->aideOubliMdp();
        unset($_SESSION['fail']);
        return;
      }
    }



    if (isset($_POST['submit_new_choix'])) {
      /*
      TODO
      Accès à la BDD
      Modifier valeurs des choix de l'étudiant
      $this->ctrlMenu->afficherMenu(2);
      */
      $_SESSION['fail'] = "Cette fonctionnalité n'est pas encore<br/>implémentée. Nous nous excusons pour cela<br/>et vous demanderons de bien vouloir faire<br/>preuve de patience.";
      $this->ctrlLost->genererLost();
      unset($_SESSION['fail']);
      return;
    }

      if (isset($_POST['edition_compte'])) {
      /*
      TODO
      Si mdp correct
      Etablir modifications
      */
      $_SESSION['fail'] = "Cette fonctionnalité n'est pas encore<br/>implémentée. Nous nous excusons pour cela<br/>et vous demanderons de bien vouloir faire<br/>preuve de patience.";
      $this->ctrlLost->genererLost();
      unset($_SESSION['fail']);
      return;
    }

    $this->ctrlAuthentification->authentification();

    return;
  }

  /**
  * Méthode de débug lors de la mise à on de debug dans URL
  */
  // public function debug()
  // {
  //   $value = null;
  //   if (isset($_GET['debug'])) {
  //     if ($_GET['debug'] == 'on') {
  //       $value = $this->debug->fileStat(__DIR__);
  //       echo $value;
  //       var_dump($_SERVER);
  //     }
  //   }
  // }
}


?>
