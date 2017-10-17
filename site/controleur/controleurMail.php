<?php
require_once __DIR__."/../vue/VueMail.php";
require_once __DIR__."/../vue/vueMenu.php";
require_once __DIR__."/../modele/dao/dao_2016.php";
require_once __DIR__.'/ICSGeneration.php';

/**
* Classe de mailing qui permet l'envoi et la création de mails.
*/
class ControleurMail
{
  private $vue;
  private $dao;

  /**
  * Constructeur de la classe initialisant les vues nécessaires ainsi que le DAO.
  */
  public function __construct(){
    $this->vue=new VueMail();
    $this->vueMenu = new VueMenu();
    $this->dao = new Dao_2016();
  }


  /**
  * Fonction permettant de générer le mail du mot de passe oublié.
  * @return String le contenu du mail en HTML de mot de passe oublié.
  */
  public function genererMailOublie(){
    $new_mdp = chr(rand(65,90)).chr(rand(65,90)).chr(rand(65,90)).chr(rand(65,90))
    .chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) .
    chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) ;

    $message = $this->vue->mailOubliMdp($new_mdp);

    $mailOublie = array($message,$new_mdp);

    return $mailOublie;

  }


  /**
  * Fonction permettant de générer une vue correspondant au numéro du mail choisi.
  * @param  int $mail le numéro du mail correspondant.
  */
  public function afficherFormulaireMail($numMail) {
    $nomMail = "";
    switch ($numMail) {
      case '0':
      $nomMail = 'confirmInscriptionEnt';
      break;
      case '1':
      $nomMail = 'infosPratiquesEnt';                         //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      break;                                                  //                                                                                                                  //
      case '2':                                               // Il est préférable d'utiliser la fonction suivante (afficherFormulaireNom()), mais elles remplissent le meme role //
      $nomMail = 'invitationEnt';                             //                                                                                                                  //
      break;                                                  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      case '3':
      $nomMail = 'invitationEtu';
      break;
      case '4':
      $nomMail = 'preValidationEtu';
      break;
      default:
      $nomMail = "nouveau";
      break;
    }
    // Affichage des mails des listes de diffusion pour le nouveau mail seulement.
    $listesDiffusion = 0;
    if ($nomMail == "nouveau")
    {
      $listesDiffusion = $this->getListesDiffusion();
    }
    $fichier  = $this->lireFichierHtml($nomMail);
    $pjDefaut = $this->tabPiecesJointesDefaut($nomMail);
    $this->vue->afficherFormulaireMail($nomMail,$fichier,$pjDefaut, $listesDiffusion);
  }

  /**
  * Fonction permettant d'afficher le formulaire de mail correspondant au mail choisi.
  * @param  String $nomMail le nom du mail que l'on souhaite affiché.
  */
  public function afficherFormulaireMailNom($nomMail){
    $fichier  = $this->lireFichierHtml($nomMail);
    $pjDefaut = $this->tabPiecesJointesDefaut($nomMail);
    // Affichage des mails des listes de diffusion pour le nouveau mail seulement.
    $listesDiffusion = 0;
    if ($nomMail == "nouveau")
    {
      $listesDiffusion = $this->getListesDiffusion();
    }
    $this->vue->afficherFormulaireMail($nomMail,$fichier,$pjDefaut, $listesDiffusion);
  }

  /**
  * Fonction permettant de retourner un code intendé pour les principales balises html utilisées dans le mail.
  * @param  String $corpsMail le contenu d'un mail non indenté.
  * @return String            le contenu du mail correctement indénté.
  */
  private function mettreEnFormeHtml($corpsMail){
    // Decode caracteres html et apostrophe
    $corpsMail = html_entity_decode($corpsMail);
    $corpsMail = str_replace('&#39;','\'',$corpsMail);

    // Met en forme le code source
    $indent = "\t\t\t";

    $corpsMail = str_replace('<p>',"\n$indent<p>\n".$indent."\t",$corpsMail);
    $corpsMail = str_replace("</p>","\n$indent</p>\n",$corpsMail);
    $corpsMail = str_replace('<li>',"$indent\t<li>",$corpsMail);
    $corpsMail = str_replace('</li>',"</li>\n",$corpsMail);
    $corpsMail = str_replace('</a>',"</a>\n",$corpsMail);
    $corpsMail = str_replace('<ul>',"\n$indent<ul>\n",$corpsMail);
    $corpsMail = str_replace('</ul>',$indent."</ul>\n",$corpsMail);

    return $corpsMail;
  }


  /**
  * Fonction permettant de modifier un mail et de l'enregistrer dans un fichier au format HTML.
  * @param  String $objetMail l'objet du mail modifié.
  * @param  String $corpsMail le corps du mail modifié.
  * @param  String $nomMail   le nom du mail envoyé (plus précisément le nom du dossier le contenant).
  */
  public function modifierMail($objetMail, $corpsMail, $nomMail){

    $filename = "vue/mail/$nomMail/mail.html";
    //chmod("vue/mail/$nomMail/mail.html",0666); // droits d'écriture et de lecture

    // $corpsMail = nl2br($corpsMail);
    // $corpsMail = stripslashes($corpsMail);

    $corpsMail = $this->mettreEnFormeHtml($corpsMail);


    $contenu = "<!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv=\"content-type\" content=\"text/html\" charset=\"utf-8\">
    <title>$objetMail</title>
    </head>
    <body>".
    $corpsMail.
    "
    </body>
    </html>";


    //file_put_contents($filename, utf8_encode($contenu));
    // Enregistrement du contenu du mail dans le fichier correspondant.
    if (is_writable($filename))
    {
      if (!$handle = fopen($filename, 'w+'))
      {
        echo "Impossible d'ouvrir le fichier ($filename)";
        exit;
      }

      if (fwrite($handle, $contenu."\n") === FALSE)
      {
        echo "Le fichier $filename n'est pas modifiable.";
        exit;
      }

      //L'écriture dans le fichier a réussi";
      fclose($handle);
    }
    else
    {
      echo "Le fichier $filename n'est pas accessible en écriture.";
    }

    // Après enregistrement du fichier, on affiche de nouveau le formulaire de mail à partir du nouveau fichier modifié.

    $fichier  = $this->lireFichierHtml($nomMail);
    $pjDefaut = $this->tabPiecesJointesDefaut($nomMail);
    //chmod("vue/mail/$nomMail/mail.html",0444); // droits de lecture
    // Affichage des listes de diffusion pour le nouveau mail seulement.
    $listesDiffusion = 0;
    if ($nomMail == -1)
    {
      $listesDiffusion = $this->getListesDiffusion();
    }
    $this->vue->afficherFormulaireMail($nomMail,$fichier,$pjDefaut, $listesDiffusion);

  }


  /**
  * Fonction permettant de lire le mail dans le fichier contenu dans le dossier correspondant.
  * @param  String $nomDossier     le nom du dossier.
  * @return array(String,String)   un tableau contenant le titre et le corps du fichier html.
  */
  public function lireFichierHtml($nomDossier) {

    $cheminFichier = "vue/mail/$nomDossier/mail.html";
    //chmod("vue/mail/$nomDossier/mail.html",0444); // droits de lecture


    if ($fp=file_get_contents($cheminFichier)){

      // Avoir le titre de la page s'il est présent.
      if(preg_match("#<title>(.*)</title>#Ui", $fp , $titre) === 1){
        $titre = substr($titre[1],0,255);
      }else{
        $titre = "";
      }

      // Avoir le corps de la page.
      $s1 = strpos($fp, '<body>') + strlen('<body>');
      $f1 = '</body>';
      $corps =  trim(substr($fp, $s1, strpos($fp, $f1) - $s1));

      return array($titre,$corps);

    }else{
      return array("","");
    }
  }

  /**
  * Fonction permettant de retourner le contenu de tous les mails.
  * @return array(array(String,String)) un tableau composé des couples (objet,contenu) de chaque mail.
  */
  public function getAllMails(){

    // contenuFichiers va contenir une liste de tuples(objet,contenu)
    $contenuFichiers = array();
    $cheminDossier = "vue/mail/";
    //chmod($cheminDossier,0444); // droits de lecture
    $sousDossiers = scandir($cheminDossier);
    foreach($sousDossiers as $dossier){
      if($dossier != '.' && $dossier != '..'){
        if(is_dir($cheminDossier.$dossier) && $dossier != 'nouveau'){
          $contenuFichiers[] = $this->lireFichierHtml($dossier);
        }
      }
    }
    $contenuFichiers[] = array("Créer un nouveau message","<br /><br />");
    return $contenuFichiers;
  }

  /**
  * Fonction retournant un tableau contenant les pièces jointes par défaut d'un mail (dans dossier pièces_jointes du mail).
  * @param  String $nomMail    le nom du mail dont on veut récupérer les pièces jointes (nom du dossier le contenant).
  * @return array(array(int,fichier)) un tableau composé de couples (numéro du fichier, fichier).
  */
  private function tabPiecesJointesDefaut($nomMail){

    $piecesJointesDefaut = array();
    $cpt = 0;

    $chemin = "vue/mail/$nomMail/pieces_jointes";

    if(file_exists($chemin)){
      //chmod($chemin,0444); // droits de lecture
      if($dossier = opendir($chemin)){

        while(false !== ($fichier = readdir($dossier))){

          $cpt++;

          if($fichier != '.' && $fichier != '..'){

            $pos = strpos($fichier, '.');
            $fichier = substr($fichier, 0,$pos);
            $piecesJointesDefaut[]= array($cpt,$fichier);

          }
        }
      }
    }
    return $piecesJointesDefaut;
  }


  /**
  * Fonction permettant d'envoyer un mail.
  * @param  String  $destinataire un tableau contenant des adresses emails.
  * @param  String  $objet        l'objet du mail.
  * @param  String  $corps        le corps du mail.
  * @param  String  $nomMail      le nom du mail à envoyer (plus précisément le nom du dossier le contenant).
  * @param  boolean $automatique  true si le message est lu directement dans le fichier, false sinon.
  */
  public function envoyerMail($destinataire, $objet, $corps, $nomMail, $automatique)
  {
    // Déclaration du mail
    $mail = '';

    // Filtrage des serveurs qui rencontrent des bogues.
    if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire))
    {
      $passage_ligne = "\r\n";
    }
    else
    {
      $passage_ligne = "\n";
    }

    // Si le message est automatisé, lire directement le mail dans le fichier correspondant
    if($automatique == true)
    {
      $corps = $this->lireFichierHtml($nomMail)[1];
    }

    // Déclaration du message au format texte
    $message_txt  = $corps;
    // Suppression des balises dans le corps du message.
    $message_txt = preg_replace ( '<br />', '\n' , $message_txt);
    $message_txt = strip_tags($message_txt);
    $message_txt = html_entity_decode($message_txt, ENT_QUOTES, 'iso-8859-1');

    // Déclaration du message au format html

    $message_html = "
    <!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv=\"content-type\" content=\"text/html\"; charset=\"utf-8\">
    <title>$objet</title>
    </head>
    <body>".
    $corps.
    "</body>
    </html>";

    // Création de la boundary
    $boundary = "------------".md5(rand());
    $boundary_message = "------------".md5(rand());

    // Récupération dans la BD du nom et de l'adresse de l'expéditeur du mail
    $tableauConfig = $this->dao->getConfiguration();
    $nomAdministrateur = $tableauConfig['nomAdministrateur'];
    $mailAdministrateur = $tableauConfig['mailAdministrateur'];


    // Création du header de l'e-mail
    $header = "MIME-Version: 1.0".$passage_ligne;


    $header.= "From: $nomAdministrateur <$mailAdministrateur>".$passage_ligne;
    $header.= "X-Mailer: PHP ".phpversion().$passage_ligne;
    $header.= "Reply-To: $mailAdministrateur".$passage_ligne;
    $header.= "Return-Path: $mailAdministrateur".$passage_ligne;

    // Ajout du message au format texte
    $message = $passage_ligne."--".$boundary_message.$passage_ligne;
    $message.= "Content-Type: text/plain; charset=iso-8859-1; format=flowed".$passage_ligne;
    $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
    $message.= $passage_ligne.$message_txt.$passage_ligne;

    // Ajout du message au format HTML
    $message.= $passage_ligne."--".$boundary_message.$passage_ligne;
    $message.= "Content-Type: text/html; charset=UTF-8 ".$passage_ligne;
    $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
    $message.= $passage_ligne.$message_html.$passage_ligne;

    // Ajout du séparateur
    $message.= $passage_ligne."--".$boundary_message."--".$passage_ligne;


    $nbPiecesJointes = 0;
    // Si le mail a été envoyé depuis le formulaire
    if(isset($_POST['envoyerMail']))
    {
      // Pour rajouter les pièces jointes ajoutées par l'utilisateur via le formulaire
      for($i = 1; $i <= 3; $i++)
      {
        if ($_FILES['mon_fichier'.$i]['size'] != 0)
        {
          // Informations sur chaque fichier
          $fichier_nom_tmp    = $_FILES['mon_fichier'.$i]['tmp_name'];
          $fichier_nom        = $_FILES['mon_fichier'.$i]['name'];
          $fichier_taille     = $_FILES['mon_fichier'.$i]['size'];
          $fichier_type       = $_FILES['mon_fichier'.$i]['type'];
          $fichier_erreur     = $_FILES['mon_fichier'.$i]['error'];

          if($fichier_erreur > 0)
          {
            exit('Impossible de charger le fichier.');
          }
          // Lecture du fichier et encodage pour le mail
          $handle = fopen($fichier_nom_tmp, "r") or exit('Le fichier '.$fichier_nom.' ne peut pas s\'ouvrir.');
          $content = fread($handle, $fichier_taille);
          fclose($handle);
          $encoded_content = chunk_split(base64_encode($content));

          $nbPiecesJointes++;
          //On rajoute le fichier au message
          $message.= $passage_ligne."--".$boundary.$passage_ligne;
          $message.= "Content-Type: $fichier_type; name=\"$fichier_nom\"".$passage_ligne;
          $message.= "Content-Disposition: attachment; filename=\"$fichier_nom\"".$passage_ligne;
          $message.= "Content-Transfer-Encoding: base64".$passage_ligne;
          $message.= "X-Attachment-Id: ".rand(1000,99999).$passage_ligne.$passage_ligne;
          $message.=  $encoded_content.$passage_ligne;
        }
      }
    }
    if(isset($_POST['envoyerMail']) || $automatique == true){

      if(isset($_POST['envoyerPlanning'])){
        if(! file_exists("vue/mail/$nomMail/pieces_jointes")){
          mkdir("vue/mail/$nomMail/pieces_jointes");
        }
        // Ajout du planning et insertion dans le dossier des pièces jointes.
        $nomFichierICS = ICSGeneration::creerCalendrierUser($destinataire,"vue/mail/$nomMail/pieces_jointes");
      }

      // Pour rajouter les pièces jointes par défaut du message contenu dans le dossier pieces_jointes
      if(file_exists("vue/mail/$nomMail/pieces_jointes")){


        if($dossier = opendir("vue/mail/$nomMail/pieces_jointes")){
          //chmod("vue/mail/$nomMail/pieces_jointes",0444);
          // On va parcourir le dossier
          while(false !== ($fichier = readdir($dossier)))
          {
            $extension = pathinfo($fichier, PATHINFO_EXTENSION);
            $fichier = substr($fichier, 0, strpos($fichier, '.'));

            // Si le fichier n'est pas un lien vers un dossier et qu'il a été sélectionné comme piece jointe
            if($fichier != '.' && $fichier != '..' && $fichier != ''){

              $fichier_nom = "vue/mail/$nomMail/pieces_jointes/".$fichier.'.'.$extension;
              $fichier_type = filetype($fichier_nom);
              $fichier_taille = filesize($fichier_nom);
              $handle = fopen($fichier_nom, 'r');// or exit('Le fichier '.$fichier_nom.' ne peut pas s\'ouvrir'.);
              $content = fread($handle, $fichier_taille);
              $encoded_content = chunk_split(base64_encode($content));
              $f = fclose($handle);
              //$fichier_nom = substr($fichier_nom,strlen($nomDossier+3))
              $fichier_nom = $fichier.".".$extension;

              $nbPiecesJointes++;
              //On rajoute le fichier au message
              $message.= $passage_ligne."--".$boundary.$passage_ligne;
              $message.= "Content-Type: $fichier_type; name=\"$fichier_nom\"".$passage_ligne;
              $message.= "Content-Disposition: attachment; filename=\"$fichier_nom\"".$passage_ligne;
              $message.= "Content-Transfer-Encoding: base64".$passage_ligne;
              $message.= "X-Attachment-Id: ".rand(1000,99999).$passage_ligne.$passage_ligne;
              $message.=  $encoded_content.$passage_ligne;
            }
          }
        }
        if(isset($_POST['envoyerPlanning'])){
          if(file_exists("vue/mail/$nomMail/pieces_jointes/".$nomFichierICS) && $nomFichierICS != ""){
            unlink("vue/mail/$nomMail/pieces_jointes/".$nomFichierICS);
          }
        }
      }
    }
    // Différenciation du format du mail en fonction de l'envoi ou non de pièces jointes
    // Si des fichiers sont joints au mail -> multipart/mixed
    if($nbPiecesJointes != 0){

      // Fin de l'entête
      $header.= "Content-Type: multipart/mixed;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;

      // Création du message
      $debutMessage = $passage_ligne."--$boundary".$passage_ligne;
      $debutMessage.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary_message\"".$passage_ligne;
      $message = $debutMessage.$message;

      // Fin du message
      $message.= $passage_ligne."--".$boundary."--".$passage_ligne;

      // Si aucun fichier n'est joint -> multipart/alternative
    }else{

      // Fin de l'entête
      $header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary_message\"".$passage_ligne;

    }

    // Encodage de l'objet du mail
    $objet = utf8_decode($objet);
    $objet = mb_encode_mimeheader($objet,"UTF-8");
    // Envoi de l'e-mail
    $envoiMail = mail($destinataire,$objet,$message,$header);



  }

  /**
  * Fonction permettant d'envoyer un mail à plusieurs destinataires.
  * @param  String $destinataires composée des courriels des destinaires sous la forme (adresseMail1;adresseMail2;).
  * @param  String $objet         l'objet du mail.
  * @param  String $corps         le corps du mail.
  * @param  String $nomMail       le nom du mail (plus précisément le nom du dossier le contenant)
  * @param  boolean $automatique, true si le message est lu directement dans le fichier, false sinon.
  */
  public function envoiMailMultiple($destinataires, $objet, $corps, $nomMail, $automatique)
  {
    $tabDest = explode(";",$destinataires);
    foreach($tabDest as $dest){
      if(!(strstr($dest, "@") == false)){
        $this->envoyerMail($dest, $objet, $corps, $nomMail, $automatique) ;
      }
    }
  }

  /**
  * Fonction permettant d'afficher la vue de modification des pièces jointes d'un mail.
  * @param  String $nomMail le nom du mail (plus précisément le nom du dossier le contenant).
  */
  public function afficheModifPiecesJointes($nomMail){
    $pjDefaut = $this->tabPiecesJointesDefaut($nomMail);
    $this->vue->affichageModifPj($nomMail,$pjDefaut);
  }

  /**
  * Fonction permettant de mettre à jour les pièces jointes par défaut (ajout / suppression).
  * @param  String $nomMail       le nom du mail (plus précisément le nom du dossier le contenant).
  * @param  File   $fichierAjoute le fichier à ajouter au dossier des pièces jointes du mail.
  */
  public function modifPiecesJointes($nomMail,$fichierAjoute){

    $dossierDestination = "vue/mail/$nomMail/pieces_jointes";

    // si le dossier de destination n'existe pas, on le créé
    if( ! file_exists($dossierDestination)){
      mkdir($dossierDestination);
    }
    //chmod("vue/mail/$nomMail/pieces_jointes",0666); // droits de lecture et d'écriture

    // Ajout du fichier dans le dossier correspondant
    $this->uploadFichier($fichierAjoute,$dossierDestination);

    // Suppression des fichiers que l'on ne souhaite pas conserver
    $this->supprimerFichier($dossierDestination);

    //chmod("vue/mail/$nomMail/pieces_jointes",0444); // droit de lecture
  }

  /**
  * Fonction permettant de supprimer les fichiers sélectionnés dans un dossier.
  * @param  String $cheminDossier le chemin du dossier
  */
  public function supprimerFichier($cheminDossier){
    if(file_exists($cheminDossier)){
      //chmod($cheminDossier,0666); // droits de lecture et d'écriture

      if($dossier = opendir($cheminDossier)){

        //chmod("vue/mail/$nomMail/pieces_jointes",500);

        // On va parcourir le dossier
        $cpt = 0; // Compteur permettant d'indiquer le numéro de fichier choisi
        while(false !== ($fichier = readdir($dossier)))
        {
          $cpt++;
          $extension = pathinfo($fichier, PATHINFO_EXTENSION);
          $fichier = substr($fichier, 0, strpos($fichier, '.'));

          // Si le fichier n'est pas un lien vers un dossier et qu'il a été sélectionné comme piece jointe à supprimer
          if($fichier != '.' && $fichier != '..' && $fichier != '' && isset($_POST["piecejointesuppr".$cpt])){
            $fichier_nom = $cheminDossier.'/'.$fichier.'.'.$extension;
            //chmod($fichier_nom,0666); // droits de lecture et d'écriture
            unlink($fichier_nom);
          }
        }
      }
    }
  }


  /**
  * Fonction permettant l'upload d'un fichier vers un dossier spécifié.
  * @param  File   $fichier        le fichier à ajouter.
  * @param  String $nomDossierDest le chemin du dossier cible.
  */
  private function uploadFichier($fichier,$nomDossierDest){

    if( !empty($fichier) && ($fichier['error'] == 0 )) {

      $nomFichier = $fichier['name'];
      //chmod($nomDossierDest,0666); // droits de lecture et d'écriture

      // On vérifie qu'aucun fichier ne porte le même nom
      if (!file_exists($nomDossierDest.'/'.$nomFichier)) {

        //Attempt to move the uploaded file to it's new place
        if ( ! (move_uploaded_file($fichier['tmp_name'],$nomDossierDest.'/'.$nomFichier))) {
          echo "Erreur durant l'upload du fichier";
        }
      } else {
        echo "Le fichier existe déjà.";
      }
    }
    //chmod($nomDossierDest,444); // droits de lecture
  }

  /**
  * Fonction permettant d'accéder aux mails des listes de diffusion de l'IUT.
  * @return array un tableau composé des adresses mails des listes de diffusion.
  */
  public function getListesDiffusion()
  {
    return $this->dao->getListesDiffusion() ;
  }
}
?>
